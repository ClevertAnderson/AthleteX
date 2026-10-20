<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Achievement;
use App\Models\TryoutSchedule;
use App\Models\Sport;
use App\Support\AchievementDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. BASE QUERY: Only count athletes who are fully APPROVED and NOT Tryouts
        $baseAthleteQuery = Athlete::where('approval_status', 'approved')
                                   ->where('classification', '!=', 'Tryout');

        // 2. 🔒 STRICT RBAC CHECK: Lock the query down to ONLY their sport!
        if ($user->role === 'coach') {
            $coachSport = $user->coach->coach_sport_event ?? ($user->coach_sport ?? null);

            if ($coachSport) {
                $baseAthleteQuery->where('sport_event', $coachSport);
            } else {
                $baseAthleteQuery->whereNull('id');
            }
        }

        // 3. Exact matching counts (Now guaranteed to match the list view!)

        // ACTIVE
        $activeAthletesCount = (clone $baseAthleteQuery)
            ->where('status', 'Active')
            ->where('classification', '!=', 'Alumni')
            ->count();

        // ALUMNI
        $alumniCount = (clone $baseAthleteQuery)
            ->where('classification', 'Alumni')
            ->count();

        // INACTIVE
        $inactive = (clone $baseAthleteQuery)
            ->where('status', 'Inactive')
            ->where('classification', '!=', 'Alumni')
            ->count();

        // PENDING: For the Admin (Uses a separate query because they are NOT approved)
        $pendingApprovals = Athlete::where('approval_status', 'pending')->count();

        // 4. Global Stats
        $coachesCount = Coach::count();
        $activeSports = Sport::count();
        $sports = Sport::orderBy('name', 'asc')->get();

        // ACHIEVEMENTS (scoped to the coach's sport if they are a coach)
        $totalAchievements = $this->achievementQueryFor($user)->count();

        // Chart: optional date range from the From / To pickers (?from=YYYY-MM-DD&to=YYYY-MM-DD)
        $from  = $request->query('from');
        $to    = $request->query('to');
        $chart = $this->chartData($this->achievedDatesFor($user), $from, $to);

        return view('features.dashboard', compact(
            'activeAthletesCount', 'alumniCount', 'coachesCount',
            'inactive', 'pendingApprovals', 'activeSports',
            'totalAchievements', 'sports',
            'chart', 'from', 'to'
        ));
    }

    /**
     * JSON endpoint: refreshes ONLY the Achievements chart (no page reload).
     */
    public function achievementsChart(Request $request)
    {
        return response()->json(
            $this->chartData(
                $this->achievedDatesFor(auth()->user()),
                $request->query('from'),
                $request->query('to')
            )
        );
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /** Achievement query, locked to the coach's sport when the user is a coach. */
    private function achievementQueryFor($user)
    {
        $query = Achievement::query();

        if ($user->role === 'coach') {
            $coachSport = $user->coach->coach_sport_event ?? ($user->coach_sport ?? null);

            // All athletes for this sport (even pending) so coaches see all their earned achievements
            $athleteIds = Athlete::where('sport_event', $coachSport)->pluck('id');
            $query->whereIn('athlete_id', $athleteIds);
        }

        return $query;
    }

    /** Collection of Carbon "date achieved" values for the user's achievements. */
    private function achievedDatesFor($user)
    {
        return $this->achievementQueryFor($user)
            ->get(['id', 'year', 'month_day', 'created_at'])
            ->map(fn ($a) => AchievementDate::from($a));
    }

    /** Safely read a YYYY-MM-DD value from the date picker. */
    private function parseDay($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Builds the chart payload.
     *  - No dates picked  -> Jan..Dec, all years combined (the classic view)
     *  - From and/or To   -> one bar per month inside that range
     * "meta" carries month/year for each bar so clicking a bar can open the matching list.
     */
    private function chartData($dates, $from, $to): array
    {
        $start = $this->parseDay($from);
        $end   = $this->parseDay($to);

        // ---------- Classic view: no range ----------
        if (! $start && ! $end) {
            $counts = $dates->groupBy(fn ($d) => $d->month)->map(fn ($g) => $g->count());

            $labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            $data = [];
            $meta = [];
            foreach (range(1, 12) as $m) {
                $data[] = $counts[$m] ?? 0;
                $meta[] = ['month' => $m, 'year' => null];
            }

            return ['labels' => $labels, 'data' => $data, 'meta' => $meta, 'ranged' => false];
        }

        // ---------- Range view ----------
        // If only one side is picked, the other side defaults to the data's latest/earliest date
        if ($start && ! $end) {
            $latest = $dates->max();
            $end = ($latest && $latest->gt($start)) ? $latest->copy() : $start->copy();
        } elseif (! $start && $end) {
            $earliest = $dates->min();
            $start = ($earliest && $earliest->lt($end)) ? $earliest->copy() : $end->copy();
        }

        if ($start->gt($end)) {
            [$start, $end] = [$end, $start];
        }

        // Safety cap: at most 10 years of monthly bars
        if ($start->diffInMonths($end) > 119) {
            $end = $start->copy()->addMonths(119)->endOfMonth();
        }

        $inRange = $dates->filter(fn ($d) => $d->betweenIncluded($start, $end));
        $counts  = $inRange->groupBy(fn ($d) => $d->format('Y-m'))->map(fn ($g) => $g->count());

        $multiYear = $start->year !== $end->year;

        $labels = [];
        $data   = [];
        $meta   = [];

        $cursor = $start->copy()->startOfMonth();
        $last   = $end->copy()->startOfMonth();

        while ($cursor->lte($last)) {
            $labels[] = $cursor->format($multiYear ? 'M Y' : 'M');
            $data[]   = $counts[$cursor->format('Y-m')] ?? 0;
            $meta[]   = ['month' => $cursor->month, 'year' => $cursor->year];
            $cursor->addMonth();
        }

        return ['labels' => $labels, 'data' => $data, 'meta' => $meta, 'ranged' => true];
    }
}