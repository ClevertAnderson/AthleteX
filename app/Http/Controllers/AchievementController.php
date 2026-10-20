<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\CoachAchievement;
use App\Support\AchievementDate;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AchievementController extends Controller
{
    /**
     * Display all achievements of a specific athlete.
     */
    public function index($athlete_id)
    {
        $achievements = Achievement::where('athlete_id', $athlete_id)->get();
        return response()->json($achievements);
    }

    /**
     * Store a new achievement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'year' => 'required|string',
            'month_day' => 'required|string',
            'event' => 'required|string',
            'venue' => 'required|string', // was 'date' by mistake - a venue is a place name
            'award' => 'required|string',
            'category' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $achievement = Achievement::create([
            'athlete_id' => $request->athlete_id,
            'year' => $request->year,
            'month_day' => $request->month_day,
            'event' => $request->event,
            'venue' => $request->venue,
            'award' => $request->award,
            'category' => $request->category,
            'remarks' => $request->remarks,
        ]);

        return response()->json(['message' => 'Achievement added successfully.', 'data' => $achievement]);
    }

    /**
     * Update an achievement.
     */
    public function update(Request $request, $id)
    {
        $achievement = Achievement::findOrFail($id);

        $request->validate([
            'year' => 'required|string',
            'month_day' => 'required|string',
            'event' => 'required|string',
            'venue' => 'required|string',
            'award' => 'required|string',
            'category' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $achievement->update($request->all());

        return response()->json(['message' => 'Achievement updated successfully.', 'data' => $achievement]);
    }

    /**
     * Delete an achievement.
     */
    public function destroy($id)
    {
        $achievement = Achievement::findOrFail($id);
        $achievement->delete();
        return response()->json(['message' => 'Achievement deleted successfully.']);
    }

    /**
     * Display a global view of all achievements (Master List).
     * Always sorted newest -> oldest by the DATE ACHIEVED (year + month_day),
     * and filterable by Month and Year of that date.
     */
    public function globalIndex(Request $request)
    {
        $user   = auth()->user();
        $type   = $request->query('type', 'all');
        $month  = $request->query('month');
        $year   = $request->query('year');
        $search = $request->query('search');

        $items = collect();

        // =========================================
        // 1. STUDENT-ATHLETE ACHIEVEMENTS
        // =========================================
        if ($type !== 'coach') {
            $athleteQuery = Achievement::with('athlete');

            if ($user->role === 'coach') {
                $coachSport = $user->coach->coach_sport_event ?? null;
                if ($coachSport) {
                    $athleteQuery->whereHas('athlete', function ($q) use ($coachSport) {
                        $q->where('sport_event', $coachSport);
                    });
                } else {
                    $athleteQuery->whereNull('id');
                }
            }

            if ($search) {
                $athleteQuery->where(function ($q) use ($search) {
                    $q->where('event', 'LIKE', "%{$search}%")->orWhere('award', 'LIKE', "%{$search}%");
                });
            }

            foreach ($athleteQuery->get() as $item) {
                $item->model_type = 'athlete';
                $items->push($item);
            }
        }

        // =========================================
        // 2. COACH ACHIEVEMENTS
        // =========================================
        if ($type !== 'athlete') {
            $coachQuery = CoachAchievement::with('coach');

            if ($user->role === 'coach') {
                $coachId = $user->coach->id ?? null;
                if ($coachId) {
                    $coachQuery->where('coach_id', $coachId);
                } else {
                    $coachQuery->whereNull('id');
                }
            }

            if ($search) {
                $coachQuery->where(function ($q) use ($search) {
                    $q->where('sports_event', 'LIKE', "%{$search}%")->orWhere('award', 'LIKE', "%{$search}%");
                });
            }

            foreach ($coachQuery->get() as $item) {
                $item->model_type = 'coach';
                $items->push($item);
            }
        }

        // =========================================
        // 3. DATE ACHIEVED -> YEAR LIST -> FILTER -> SORT
        // =========================================
        foreach ($items as $item) {
            $item->achieved_at = AchievementDate::from($item);
        }

        // Year dropdown options (built before the month/year filter is applied)
        $years = $items->map(fn ($i) => $i->achieved_at->year)->unique()->values();
        if ($year && ! $years->contains((int) $year)) {
            $years->push((int) $year);
        }
        $years = $years->sortDesc()->values();

        $items = $items->filter(function ($i) use ($month, $year) {
            if ($month && $i->achieved_at->month !== (int) $month) return false;
            if ($year && $i->achieved_at->year !== (int) $year) return false;
            return true;
        });

        // Strict newest -> oldest (ties broken by newest recorded first)
        $items = $items->sort(function ($a, $b) {
            return [$b->achieved_at->timestamp, optional($b->created_at)->timestamp ?? 0]
               <=> [$a->achieved_at->timestamp, optional($a->created_at)->timestamp ?? 0];
        })->values();

        // =========================================
        // 4. PAGINATE
        // =========================================
        $perPage = 15;
        $page = LengthAwarePaginator::resolveCurrentPage() ?: 1;

        $achievements = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('features.achievements', compact('achievements', 'type', 'years'));
    }
}