<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Sport;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function coachIndex(Request $request)
    {
        if (auth()->user()->role !== 'coach') {
            abort(403);
        }

        // 1. Get the coach profile and their specific sport
        $coach = auth()->user()->coach;
        $coachSport = $coach->coach_sport_event ?? null;

        $today = now()->toDateString();

        if (!$coach || !$coachSport) {
            $attendances = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $athletes = collect();
            $athletesWithStatus = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            return view('features.attendance', compact('attendances', 'athletes', 'athletesWithStatus', 'today'));
        }

        // 2. Strict Sport Filter for Attendance Records
        $attendances = Attendance::whereHas('athlete', function ($query) use ($coachSport) {
            $query->where('sport_event', $coachSport);
        })->get();

        // 3. ONLY ACTIVE ATHLETES (Paginated & Ordered Alphabetically)
        $athletesQuery = \App\Models\Athlete::where('sport_event', $coachSport)
            ->where('status', 'Active')
            ->where('classification', '!=', 'Tryout')
            ->orderBy('last_name')
            ->orderBy('first_name');

        $paginatedAthletes = $athletesQuery->paginate(15)->withQueryString();

        // Raw collection for the Check Attendance Modal (unpaginated so modal shows everyone)
        $athletes = \App\Models\Athlete::where('sport_event', $coachSport)
            ->where('status', 'Active')
            ->where('classification', '!=', 'Tryout')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // 4. Enrich paginated athletes with today's attendance status
        $athletesWithStatus = new \Illuminate\Pagination\LengthAwarePaginator(
            collect($paginatedAthletes->items())->map(function ($athlete) use ($today) {
                $todayAttendance = $athlete->attendances()
                    ->whereDate('date', $today)
                    ->first();
                return [
                    'id' => $athlete->id,
                    'first_name' => $athlete->first_name,
                    'last_name' => $athlete->last_name,
                    'sport_event' => $athlete->sport_event,
                    'status' => $todayAttendance?->status ?? 'Not Marked',
                    'remarks' => $todayAttendance?->remarks ?? '—',
                    'attendance_date' => $todayAttendance?->date ?? $today,
                    'isEditable' => true,
                ];
            }),
            $paginatedAthletes->total(),
            $paginatedAthletes->perPage(),
            $paginatedAthletes->currentPage(),
            ['path' => $paginatedAthletes->path(), 'query' => $request->query()]
        );

        return view('features.attendance', compact('attendances', 'athletes', 'athletesWithStatus', 'today'));
    }

    public function adminIndex(Request $request)
    {
        $sportId = $request->query('sport');
        $month = $request->query('month'); 
        $date = $request->query('date');    

        $sportName = null;
        if (!empty($sportId)) {
            if (is_numeric($sportId)) {
                $sport = Sport::find($sportId);
                $sportName = $sport ? $sport->name : null;
            } else {
                $sportName = $sportId; 
            }
        }

        $today = now()->toDateString();
        $targetDate = $date ?: $today;
        $sports = Sport::all();

        // ==========================================
        // SMART VIEW ROUTING WITH PAGINATION
        // ==========================================
        if (!empty($month)) {
            // MONTH VIEW: Show only actual recorded data for the month
            $query = \App\Models\Attendance::select('attendances.*')
                ->join('athletes', 'attendances.athlete_id', '=', 'athletes.id')
                ->with('athlete');

            if ($sportName) {
                $query->where('athletes.sport_event', $sportName);
            }
            $query->whereMonth('attendances.date', $month);
            
            $paginatedAttendances = $query->orderBy('athletes.sport_event', 'asc')
                ->orderBy('athletes.last_name', 'asc')
                ->orderBy('athletes.first_name', 'asc')
                ->orderBy('attendances.date', 'desc')
                ->paginate(15)
                ->withQueryString();

            $athletesWithStatus = new \Illuminate\Pagination\LengthAwarePaginator(
                collect($paginatedAttendances->items())->filter(function($att) {
                    return $att->athlete != null;
                })->map(function ($att) {
                    return [
                        'id' => $att->athlete->id,
                        'first_name' => $att->athlete->first_name,
                        'last_name' => $att->athlete->last_name,
                        'sport_event' => $att->athlete->sport_event,
                        'status' => $att->status,
                        'remarks' => $att->remarks ?? '—',
                        'attendance_date' => $att->date,
                    ];
                })->values(),
                $paginatedAttendances->total(),
                $paginatedAttendances->perPage(),
                $paginatedAttendances->currentPage(),
                ['path' => $paginatedAttendances->path(), 'query' => $request->query()]
            );
        } else {
            // DATE VIEW (Today OR Past Date): Show paginated active roster
            $athleteQuery = \App\Models\Athlete::where('approval_status', 'approved')
                ->where('status', 'Active')
                ->where('classification', '!=', 'Tryout')
                ->when($sportName, function($q) use ($sportName) {
                    $q->where('sport_event', $sportName);
                })
                ->orderBy('sport_event')
                ->orderBy('last_name')
                ->orderBy('first_name');

            $paginatedAthletes = $athleteQuery->paginate(15)->withQueryString();

            $athletesWithStatus = new \Illuminate\Pagination\LengthAwarePaginator(
                collect($paginatedAthletes->items())->map(function ($athlete) use ($targetDate) {
                    $attendance = $athlete->attendances()
                        ->whereDate('date', $targetDate)
                        ->first();

                    return [
                        'id' => $athlete->id,
                        'first_name' => $athlete->first_name,
                        'last_name' => $athlete->last_name,
                        'sport_event' => $athlete->sport_event,
                        'status' => $attendance?->status ?? 'Not Marked',
                        'remarks' => $attendance?->remarks ?? '—',
                        'attendance_date' => $attendance?->date ?? $targetDate,
                    ];
                }),
                $paginatedAthletes->total(),
                $paginatedAthletes->perPage(),
                $paginatedAthletes->currentPage(),
                ['path' => $paginatedAthletes->path(), 'query' => $request->query()]
            );
        }

        return view('features.attendance', compact('sports', 'athletesWithStatus', 'targetDate', 'today'));
    }

    public function store(Request $request)
    {
        $attendanceDate = $request->input('attendance_date');
        $today = now()->toDateString();

        if ($attendanceDate > $today) {
            return back()->withErrors(['attendance_date' => 'You cannot record attendance for future dates.']);
        }

        $attendanceData = $request->input('attendance', []);
        $coachId = null;

        if (auth()->user()->role === 'coach' && auth()->user()->coach) {
            $coachId = auth()->user()->coach->id;
        }

        foreach ($attendanceData as $athleteId => $data) {
            Attendance::updateOrCreate(
                [
                    'athlete_id' => $athleteId,
                    'date' => $attendanceDate,
                ],
                [
                    'status' => $data['status'] ?? 'present',
                    'remarks' => $data['remarks'] ?? null,
                    'coach_id' => $coachId,
                ]
            );
        }

        $formattedDate = \Carbon\Carbon::parse($attendanceDate)->format('F j, Y');
        return back()->with('success', "Attendance saved successfully for {$formattedDate}.");
    }

    public function history(Request $request)
    {
        $backRoute = auth()->user()->role === 'admin' 
            ? route('admin.attendance') 
            : route('coach.attendance.index');

        $selectedMonth = $request->query('month') ?? date('F');
        $selectedYear  = $request->query('year') ?? date('Y');

        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $monthNumber = date('m', strtotime($selectedMonth));
        $start = \Carbon\Carbon::create($selectedYear, $monthNumber, 1)->startOfMonth();
        $end   = \Carbon\Carbon::create($selectedYear, $monthNumber, 1)->endOfMonth();
        $daysInMonth = $start->daysInMonth;

        $sports = collect();
        $sportId = null;
        $sportName = null;

        if(auth()->user()->role === 'admin') {
            $sportId = $request->query('sport_id');
            if ($sportId && is_numeric($sportId)) {
                $sportModel = \App\Models\Sport::find($sportId);
                $sportName = $sportModel ? $sportModel->name : null;
            } else {
                $sportName = $sportId;
            }

            $attendances = \App\Models\Attendance::with('athlete')
                ->whereBetween('date', [$start, $end])
                ->when($sportName, function($q) use ($sportName) {
                    $q->whereHas('athlete', function($q2) use ($sportName) {
                        $q2->where('sport_event', $sportName);
                    });
                })->get();
            $sports = \App\Models\Sport::all(); 
        } else {
            $coachSport = auth()->user()->coach->coach_sport_event ?? null;
            $attendances = \App\Models\Attendance::with('athlete')
                ->whereHas('athlete', function($q) use ($coachSport){
                    $q->where('sport_event', $coachSport);
                })
                ->whereBetween('date', [$start, $end])
                ->get();
        }

        $athleteQuery = \App\Models\Athlete::where('approval_status', 'approved')
            ->where('classification', '!=', 'Tryout')
            ->orderBy('sport_event')
            ->orderBy('last_name')
            ->orderBy('first_name');

        if (auth()->user()->role === 'admin' && $sportName) {
            $athleteQuery->where('sport_event', $sportName);
        } elseif (auth()->user()->role === 'coach') {
            $coachSport = auth()->user()->coach->coach_sport_event ?? null;
            $athleteQuery->where('sport_event', $coachSport);
        }

        // Paginate the historical matrix rows
        $athletes = $athleteQuery->paginate(15)->withQueryString();

        $attendanceMap = [];
        foreach($attendances as $attendance){
            $key = $attendance->athlete_id . '_' . \Carbon\Carbon::parse($attendance->date)->format('Y-m-d');
            $attendanceMap[$key] = $attendance;
        }

        return view('features.attendance_history', compact(
            'athletes', 'attendanceMap', 'daysInMonth', 'selectedMonth',
            'selectedYear', 'months', 'backRoute', 'sports', 'sportId'
        ));
    }
}