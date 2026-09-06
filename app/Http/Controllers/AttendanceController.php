<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Sport;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function coachIndex()
    {
        if (auth()->user()->role !== 'coach') {
            abort(403);
        }

        // 1. Get the coach profile and their specific sport
        $coach = auth()->user()->coach;
        $coachSport = $coach->coach_sport_event ?? null;

        if (!$coach || !$coachSport) {
            $attendances = collect();
            $athletes = collect();
            $athletesWithStatus = collect();
            $today = now()->toDateString();
            return view('features.attendance', compact('attendances', 'athletes', 'athletesWithStatus', 'today'));
        }

        // 2. Strict Sport Filter
        $attendances = Attendance::whereHas('athlete', function ($query) use ($coachSport) {
            $query->where('sport_event', $coachSport);
        })->get();

        // 3. ONLY ACTIVE ATHLETES
        $athletes = \App\Models\Athlete::where('sport_event', $coachSport)
            ->where('status', 'Active')
            ->where('classification', '!=', 'Tryout')
            ->get();

        // 4. Enrich athletes with today's attendance status
        $today = now()->toDateString();
        $athletesWithStatus = $athletes->map(function ($athlete) use ($today) {
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
        });

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
        // SMART VIEW ROUTING
        // ==========================================
        if (!empty($month)) {
            // MONTH VIEW: Show only actual recorded data for the month
            $query = \App\Models\Attendance::with('athlete');

            if ($sportName) {
                $query->whereHas('athlete', function($q) use ($sportName) {
                    $q->where('sport_event', $sportName);
                });
            }
            $query->whereMonth('date', $month);
            $attendances = $query->orderBy('date', 'desc')->get();

            $athletesWithStatus = $attendances->filter(function($att) {
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
            })->values();
        } else {
            // DATE VIEW (Today OR Past Date): Show the FULL active roster
            $athletes = \App\Models\Athlete::where('approval_status', 'approved')
                ->where('status', 'Active')
                ->where('classification', '!=', 'Tryout')
                ->when($sportName, function($q) use ($sportName) {
                    $q->where('sport_event', $sportName);
                })->get();

            $athletesWithStatus = $athletes->map(function ($athlete) use ($targetDate) {
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
            });
        }

        return view('features.attendance', compact('sports', 'athletesWithStatus', 'targetDate', 'today'));
    }

    public function store(Request $request)
    {
        $attendanceDate = $request->input('attendance_date');
        $today = now()->toDateString();

        // Allow backlogging past dates, but PREVENT future dates
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

        if(auth()->user()->role === 'admin') {
            $sportId = $request->query('sport_id');
            $attendances = \App\Models\Attendance::with('athlete')
                ->whereBetween('date', [$start, $end])
                ->when($sportId, function($q) use ($sportId) {
                    $q->whereHas('athlete', function($q2) use ($sportId) {
                        $q2->where('sport_event', $sportId);
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

        $athletes = $attendances->pluck('athlete')
            ->filter()
            ->unique('id')
            ->values(); 

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