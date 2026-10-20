<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\TryoutSchedule;
use App\Services\BlockchainService;
use App\Models\Achievement;
use App\Models\AcademicEvaluation;
use App\Models\FeesDiscount;
use App\Models\WorkHistory;
use App\Mail\TryoutScheduleMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Sport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AthleteController extends Controller
{
    public function index(Request $request)
    {
        $sports = \App\Models\Sport::all();

        if (auth()->user()->role === 'admin') {

            $athleteQuery = Athlete::where('approval_status', 'approved')
                                   ->where('classification', '!=', 'Tryout');

            if ($request->filled('status')) {
                if ($request->status === 'Alumni') {
                    $athleteQuery->where('classification', 'Alumni');
                } else {
                    $athleteQuery->where('status', $request->status)
                                 ->where('classification', '!=', 'Alumni');
                }
            }

            // Paginated Admin Athletes
            $athletes = $athleteQuery->orderBy('last_name', 'asc')
                                     ->orderBy('first_name', 'asc')
                                     ->paginate(10, ['*'], 'athletes_page')
                                     ->withQueryString();

            // Paginated Admin Recruits
            $recruits = Athlete::where('status', 'Active')
                               ->where('classification', 'Tryout')
                               ->orderBy('last_name', 'asc')
                               ->orderBy('first_name', 'asc')
                               ->paginate(10, ['*'], 'recruits_page')
                               ->withQueryString();

        } elseif (auth()->user()->role === 'coach') {

            $coachSport = auth()->user()->coach->coach_sport_event ?? null;

            if ($coachSport) {
                $athleteQuery = Athlete::where('sport_event', $coachSport)
                    ->where('approval_status', 'approved')
                    ->where('classification', '!=', 'Tryout');

                if ($request->filled('status')) {
                    if ($request->status === 'Alumni') {
                        $athleteQuery->where('classification', 'Alumni');
                    } else {
                        $athleteQuery->where('status', $request->status)
                                     ->where('classification', '!=', 'Alumni');
                    }
                }

                // Paginated Coach Athletes
                $athletes = $athleteQuery->orderBy('last_name', 'asc')
                                         ->orderBy('first_name', 'asc')
                                         ->paginate(10, ['*'], 'athletes_page')
                                         ->withQueryString();

                // Paginated Coach Recruits
                $recruits = Athlete::where('sport_event', $coachSport)
                    ->where('status', 'Active')
                    ->where('classification', 'Tryout')
                    ->orderBy('last_name', 'asc')
                    ->orderBy('first_name', 'asc')
                    ->paginate(10, ['*'], 'recruits_page')
                    ->withQueryString();
            } else {
                $athletes = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
                $recruits = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            }

        } else {
            $athletes = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $recruits = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        return view('features.athlete_lists', compact('athletes', 'recruits', 'sports'));
    }

    public function showApprovals(Request $request)
    {
        $tryoutQuery = Athlete::where('approval_status', 'pending')
                            ->where('classification', 'Tryout');

        if ($request->filled('tryout_search')) {
            $term = trim($request->tryout_search);
            $tryoutQuery->where(function ($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('student_id', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            });
        }
        if ($request->filled('tryout_sport') && $request->tryout_sport !== 'ALL') {
            $tryoutQuery->where('sport_event', $request->tryout_sport);
        }
        $tryoutPendings = $tryoutQuery->latest()
                                     ->paginate(15, ['*'], 'tryout_page')
                                     ->appends($request->all());

        $schedules = TryoutSchedule::orderBy('tryout_date', 'desc')
                                   ->paginate(10, ['*'], 'schedules_page');

        $recruits = Athlete::where('classification', 'Tryout')
                           ->where('approval_status', 'approved')
                           ->latest('updated_at')
                           ->paginate(10, ['*'], 'recruits_page');

        $studentQuery = Athlete::where('approval_status', 'pending')
                               ->where(function($q) {
                                   $q->whereNotIn('classification', ['Tryout', 'Alumni'])
                                     ->orWhereNull('classification');
                               });

        if ($request->filled('request_search')) {
            $term = trim($request->request_search);
            $studentQuery->where(function ($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('student_id', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            });
        }
        if ($request->filled('request_sport') && $request->request_sport !== 'ALL') {
            $studentQuery->where('sport_event', $request->request_sport);
        }
        $studentRequests = $studentQuery->latest()
                                        ->paginate(15, ['*'], 'request_page')
                                        ->appends($request->all());

        $alumniQuery = Athlete::where('approval_status', 'pending')
                            ->where('classification', 'Alumni');

        if ($request->filled('alumni_search')) {
            $term = trim($request->alumni_search);
            $alumniQuery->where(function ($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('student_id', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            });
        }
        if ($request->filled('alumni_sport') && $request->alumni_sport !== 'ALL') {
            $alumniQuery->where('sport_event', $request->alumni_sport);
        }
        $alumniPendings = $alumniQuery->latest()
                                    ->paginate(15, ['*'], 'alumni_page')
                                    ->appends($request->all());

        $approvedAthletes = Athlete::where('status', 'Active')
                                   ->where('approval_status', 'approved')
                                   ->orderBy('updated_at', 'desc')
                                   ->take(5)
                                   ->get();

        // Recently rejected applications (kept on file together with the typed reason)
        $declinedAthletes = Athlete::where('approval_status', 'rejected')
                                   ->orderByDesc('updated_at')
                                   ->take(10)
                                   ->get();

        return view('features.approvals', compact(
            'tryoutPendings',
            'schedules',
            'recruits',
            'studentRequests',
            'alumniPendings',
            'approvedAthletes',
            'declinedAthletes'
        ));
    }

    public function approve(Request $request, $id)
    {
        $athlete = Athlete::findOrFail($id);

        $totalAssessment = $request->input('total_assessment', 0);
        $totalDiscount = $request->input('total_discount', 0);
        $balance = $totalAssessment - $totalDiscount;

        $cleanSport = str_replace('_', ' ', $athlete->sport_event);
        $baseSport = trim(str_replace([' Men', ' Women', " Men's", " Women's"], '', $cleanSport));

        $assignedCoach = \App\Models\Coach::where('coach_sport_event', 'LIKE', '%' . $baseSport . '%')->first();
        $coachId = $assignedCoach ? $assignedCoach->id : $athlete->coach_id;

        $athlete->update([
            'status' => 'Active',
            'approval_status' => 'approved',
            'classification' => $request->input('classification', $athlete->classification),
            'coach_id' => $coachId,
            'tuition_fee' => $request->input('tuition_fee'),
            'misc_fee' => $request->input('miscellaneous_fee'),
            'other_charges' => $request->input('other_charges'),
            'total_assessment' => $totalAssessment,
            'total_discount' => $totalDiscount,
            'balance' => $balance
        ]);

        if ($request->filled('academic_year')) {
            \App\Models\FeesDiscount::create([
                'athlete_id' => $athlete->id,
                'academic_year' => $request->input('academic_year'),
                'total_units' => $request->input('total_units'),
                'tuition_fee' => $request->input('tuition_fee'),
                'miscellaneous_fee' => $request->input('miscellaneous_fee'),
                'other_charges' => $request->input('other_charges'),
                'total_assessment' => $totalAssessment,
                'total_discount' => $totalDiscount,
                'remarks' => 'Initial Scholarship Approval'
            ]);
        }

        BlockchainService::logAction('Approved Athlete Application: ' . $athlete->first_name . ' ' . $athlete->last_name, $athlete->toArray());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Approved and fees calculated!']);
        }

        return redirect()->back()->with('success', 'Athlete approved and scholarship fees successfully saved.');
    }

    /**
     * Decline an athlete / student request / tryout applicant.
     * A typed reason is REQUIRED. The record is kept (status "Declined",
     * approval_status "rejected") so the reason is not lost.
     */
    public function decline(Request $request, $id)
    {
        return $this->rejectApplication($request, $id, 'Declined Athlete Application');
    }

    public function approveAlumni(Request $request, $id)
    {
        $athlete = Athlete::findOrFail($id);

        $athlete->update([
            'status' => 'Graduated',
            'approval_status' => 'approved',
        ]);

        BlockchainService::logAction('Verified Alumni Record: ' . $athlete->first_name . ' ' . $athlete->last_name, $athlete->toArray());

        return redirect()->back()->with('success', 'Alumni record successfully verified and added to the master roster!');
    }

    /**
     * Reject an alumni submission. A typed reason is REQUIRED (same flow as decline()).
     */
    public function rejectAlumni(Request $request, $id)
    {
        return $this->rejectApplication($request, $id, 'Rejected Alumni Submission');
    }

    /**
     * Shared rejection logic: validate the reason, mark the record as rejected,
     * write it to the Security Ledger and answer (JSON for the modal, redirect otherwise).
     */
    private function rejectApplication(Request $request, $id, string $ledgerLabel)
    {
        $validated = $request->validate(
            ['rejection_reason' => ['required', 'string', 'min:5', 'max:1000']],
            [
                'rejection_reason.required' => 'Please type the reason for rejecting this application.',
                'rejection_reason.min'      => 'The reason must be at least 5 characters.',
                'rejection_reason.max'      => 'The reason may not be longer than 1000 characters.',
            ]
        );

        $athlete = Athlete::findOrFail($id);
        $reason  = trim($validated['rejection_reason']);

        $athlete->update([
            'approval_status'  => 'rejected',
            'status'           => 'Declined',
            'rejection_reason' => $reason,
            'rejected_by'      => auth()->id(),
        ]);

        $fullName = trim($athlete->first_name . ' ' . $athlete->last_name);

        BlockchainService::logAction(
            $ledgerLabel . ': ' . $fullName . ' | Reason: ' . $reason,
            $athlete->fresh()->toArray()
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Application rejected.',
                'rejected' => [
                    'name'   => $athlete->last_name . ', ' . $athlete->first_name,
                    'type'   => $athlete->classification === 'Tryout' ? 'Tryout'
                                : ($athlete->classification === 'Alumni' ? 'Alumni' : 'Student'),
                    'sport'  => str_replace('_', ' ', (string) $athlete->sport_event),
                    'reason' => $reason,
                    'date'   => now()->format('M d, Y'),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Application rejected. The reason was saved.');
    }

    public function showPending()
    {
        return $this->showApprovals();
    }

    public function create()
    {
        $sports = Sport::all();
        
        // ✅ FIX: Explicitly passing null to prevent Blade errors on initial load
        return view('features.student_athlete', [
            'sports' => $sports,
            'selectedAthlete' => null
        ]);
    }

    /**
     * Student ID must be unique, but REJECTED applications don't count
     * (so a corrected application can be re-submitted).
     */
    private function studentIdUniqueRule($ignoreId = null)
    {
        $rule = Rule::unique('athletes', 'student_id')->where(function ($q) {
            $q->whereNull('approval_status')->orWhere('approval_status', '!=', 'rejected');
        });

        return $ignoreId ? $rule->ignore($ignoreId) : $rule;
    }

    public function store(Request $request)
    {
        $payload = $request->all();
        $general = is_array($payload) && array_key_exists('generalInfo', $payload) && is_array($payload['generalInfo'])
            ? $payload['generalInfo']
            : $payload;

        $rules = [
            'student_id' => [
                'required',
                'string',
                'max:255',
                'regex:/^[0-9]{2}-[0-9]{4}-[0-9]{3}$/',
                $this->studentIdUniqueRule()
            ],
            'first_name'        => 'required|string|max:255|regex:/^[a-zA-Z\s\-\.]+$/',
            'last_name'         => 'required|string|max:255|regex:/^[a-zA-Z\s\-\.]+$/',
            'birthdate'         => 'nullable|date|before:today',
            'email'             => 'nullable|email|max:255',
            'contact_number'    => 'nullable|regex:/^09[0-9]{9}$/',
            'emergency_contact' => 'nullable|regex:/^09[0-9]{9}$/',
            'zip_code'          => 'nullable|digits:4',
            'age'               => 'nullable|integer|between:15,35',
            'sport_event'       => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!Sport::where('name', $value)->exists()) {
                        $fail('The selected sport event is invalid.');
                    }
                },
            ],
        ];

        $messages = [
            'student_id.unique'        => 'This Student ID is already registered.',
            'student_id.regex' => 'The Student ID must follow the exact format XX-XXXX-XXX (e.g., 20-3847-666).',
            'first_name.regex'        => 'First Name cannot contain numbers or special characters.',
            'last_name.regex'          => 'Last Name cannot contain numbers or special characters.',
            'birthdate.before'         => 'The birthdate must be a valid date in the past.',
            'email.email'              => 'Please provide a valid email format.',
            'contact_number.regex'     => 'Contact Number must start with 09 and be exactly 11 digits.',
            'emergency_contact.regex' => 'Emergency Contact must start with 09 and be exactly 11 digits.',
            'zip_code.digits'          => 'ZIP code must be exactly 4 digits.',
            'age.between'              => 'Age must be a realistic number between 15 and 35.',
        ];

        $validator = Validator::make($general, $rules, $messages);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $data = [];
        $fields = [
            'student_id', 'first_name', 'last_name', 'course', 'year_level', 'sport',
            'full_name', 'athlete_id', 'middle_initial', 'sport_event', 'status', 'classification',
            'gender', 'birthdate', 'age', 'blood_type', 'email', 'facebook', 'marital_status',
            'contact_number', 'address', 'city_municipality', 'province_state', 'zip_code',
            'emergency_person', 'emergency_contact', 'coach_id', 'date_joined', 'term_graduated',
            'asst_coach', 'total_unit', 'year_graduated', 'tuition_fee', 'misc_fee', 'other_charges',
            'total_assessment', 'total_discount', 'balance', 'current_work', 'current_company',
            'picture_path', 'notes', 'inactive_date'
        ];

        foreach ($fields as $f) {
            if (array_key_exists($f, $general)) {
                $data[$f] = $general[$f];
            }
        }

        if (isset($general['province_state'])) {
            $data['province_state'] = $general['province_state'];
            $data['province'] = $general['province_state'];
        } elseif (isset($general['province'])) {
            $data['province_state'] = $general['province'];
            $data['province'] = $general['province'];
        }

        if (auth()->check()) {
            if (auth()->user()->role === 'coach') {
                $data['coach_id'] = auth()->user()->coach->id ?? auth()->user()->coach_id ?? null;
                $data['status'] = 'Pending';
                $data['approval_status'] = 'pending';
            } elseif (auth()->user()->role === 'admin') {
                $data['status'] = isset($general['status']) && $general['status'] !== '' ? $general['status'] : 'Active';
                $data['approval_status'] = 'approved';
            }
        }

        try {
            $athlete = DB::transaction(function () use ($data, $payload) {
                $a = Athlete::create($data);

                if (!empty($payload['achievements']) && is_array($payload['achievements'])) {
                    foreach ($payload['achievements'] as $ach) {
                        Achievement::create([
                            'athlete_id' => $a->id,
                            'year' => $ach['year'] ?? null,
                            'month_day' => $ach['monthDay'] ?? ($ach['month_day'] ?? null),
                            'event' => $ach['event'] ?? null,
                            'venue' => $ach['venue'] ?? null,
                            'award' => $ach['award'] ?? null,
                            'category' => $ach['category'] ?? null,
                            'remarks' => $ach['remarks'] ?? null,
                        ]);
                    }
                }

                if (!empty($payload['academicRecords']) && is_array($payload['academicRecords'])) {
                    foreach ($payload['academicRecords'] as $rec) {
                        AcademicEvaluation::create([
                            'athlete_id' => $a->id,
                            'passed' => $rec['passed'] ?? null,
                            'enrolled' => $rec['enrolled'] ?? null,
                            'percentage' => $rec['percentage'] ?? null,
                            'remark' => $rec['remark'] ?? null,
                        ]);
                    }
                }

                if (!empty($payload['fees']) && is_array($payload['fees'])) {
                    foreach ($payload['fees'] as $f) {
                        FeesDiscount::create([
                            'athlete_id' => $a->id,
                            'academic_year' => $f['academic_year'] ?? null,
                            'total_units' => $f['total_units'] ?? null,
                            'tuition_fee' => $f['tuition_fee'] ?? null,
                            'miscellaneous_fee' => $f['miscellaneous_fee'] ?? ($f['misc_fee'] ?? null),
                            'other_charges' => $f['other_charges'] ?? null,
                            'total_assessment' => $f['total_assessment'] ?? null,
                            'total_discount' => $f['total_discount'] ?? null,
                            'remarks' => $f['remarks'] ?? null,
                        ]);
                    }
                }

                if (!empty($payload['workHistory']) && is_array($payload['workHistory'])) {
                    foreach ($payload['workHistory'] as $w) {
                        WorkHistory::create([
                            'athlete_id' => $a->id,
                            'year' => $w['year'] ?? null,
                            'date' => $w['date'] ?? null,
                            'position' => $w['position'] ?? null,
                            'company' => $w['company'] ?? null,
                            'remarks' => $w['remarks'] ?? null,
                        ]);
                    }
                }

                return $a;
            });

            BlockchainService::logAction('Created New Athlete: ' . $athlete->first_name . ' ' . $athlete->last_name, $athlete->toArray());

            if ($request->expectsJson() || $request->wantsJson() || $request->isJson()) {
                return response()->json(['success' => true, 'athlete' => $athlete], 201);
            }

            return redirect()->route('athletes.index')->with('success', 'Athlete added successfully!');

        } catch (\Throwable $e) {
            Log::error('Athlete store error: ' . $e->getMessage(), ['exception' => $e]);

            if ($request->expectsJson() || $request->wantsJson() || $request->isJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to create athlete', 'error' => $e->getMessage()], 500);
            }
            return back()->withErrors('Failed to create athlete.');
        }
    }

    public function search(Request $request)
    {
        $term = trim($request->get('q', ''));
        $query = Athlete::query();

        if (auth()->check() && auth()->user()->role === 'coach') {
            $coachSport = auth()->user()->coach->coach_sport_event ?? null;
            if ($coachSport) {
                $query->where('sport_event', $coachSport);
            } else {
                $query->whereNull('id');
            }
        }

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('full_name', 'like', "%{$term}%")
                  ->orWhere('student_id', 'like', "%{$term}%");
            });
        }

        $results = $query->limit(15)->get()->map(function ($a) {
            return [
                'id' => $a->id,
                'student_id' => $a->student_id,
                'first_name' => $a->first_name,
                'last_name' => $a->last_name,
                'full_name' => $a->full_name,
                'age' => $a->age,
                'gender' => $a->gender,
                'birthdate' => $a->birthdate,
                'blood_type' => $a->blood_type,
                'course' => $a->course,
                'year_level' => $a->year_level,
                'email' => $a->email,
                'facebook' => $a->facebook,
                'marital_status' => $a->marital_status,
                'contact_number' => $a->contact_number,
                'address' => $a->address,
                'city_municipality' => $a->city_municipality,
                'province_state' => $a->province_state,
                'zip_code' => $a->zip_code,
                'emergency_person' => $a->emergency_person,
                'emergency_contact' => $a->emergency_contact,
                'coach_id' => $a->coach_id,
                'coach_name' => $a->coach
                ? $a->coach->coach_first_name . ' ' . $a->coach->coach_last_name
                : null,
                'date_joined' => $a->date_joined,
                'term_graduated' => $a->term_graduated,
                'asst_coach' => $a->asst_coach,
                'total_unit' => $a->total_unit,
                'year_graduated' => $a->year_graduated,
                'tuition_fee' => $a->tuition_fee,
                'misc_fee' => $a->misc_fee,
                'other_charges' => $a->other_charges,
                'total_assessment' => $a->total_assessment,
                'total_discount' => $a->total_discount,
                'balance' => $a->balance,
                'current_work' => $a->current_work,
                'current_company' => $a->current_company,
                'sport_event' => $a->sport_event,
                'status' => $a->status,
                'picture_url' => $a->picture_path ? asset('storage/' . $a->picture_path) : null,
            ];
        });

        return response()->json($results);
    }

    public function show(\Illuminate\Http\Request $request, $id)
    {
        $athlete = \App\Models\Athlete::with(['coach', 'achievements', 'academicEvaluations', 'feesDiscounts', 'workHistories'])->findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json($athlete);
        }
        return view('features.athlete_profile', compact('athlete'));
    }

    public function update(Request $request, Athlete $athlete)
    {
        $payload = $request->all();
        $general = is_array($payload) && array_key_exists('generalInfo', $payload) && is_array($payload['generalInfo'])
            ? $payload['generalInfo']
            : $payload;

        $rules = [
          'student_id' => [
                'required',
                'string',
                'max:255',
                'regex:/^[0-9]{2}-[0-9]{4}-[0-9]{3}$/',
                $this->studentIdUniqueRule($athlete->id)
            ],
            'first_name'        => 'required|string|max:255|regex:/^[a-zA-Z\s\-\.]+$/',
            'last_name'         => 'required|string|max:255|regex:/^[a-zA-Z\s\-\.]+$/',
            'birthdate'         => 'nullable|date|before:today',
            'email'             => 'nullable|email|max:255',
            'contact_number'    => 'nullable|regex:/^09[0-9]{9}$/',
            'emergency_contact' => 'nullable|regex:/^09[0-9]{9}$/',
            'zip_code'          => 'nullable|digits:4',
            'age'               => 'nullable|integer|between:15,35',
        ];

        $messages = [
            'student_id.unique'        => 'This Student ID is already registered.',
            'student_id.regex' => 'The Student ID must follow the exact format XX-XXXX-XXX (e.g., 20-3847-666).',
            'first_name.regex'        => 'First Name cannot contain numbers or special characters.',
            'last_name.regex'          => 'Last Name cannot contain numbers or special characters.',
            'birthdate.before'         => 'The birthdate must be a valid date in the past.',
            'email.email'              => 'Please provide a valid email format.',
            'contact_number.regex'     => 'Contact Number must start with 09 and be exactly 11 digits.',
            'emergency_contact.regex' => 'Emergency Contact must start with 09 and be exactly 11 digits.',
            'zip_code.digits'          => 'ZIP code must be exactly 4 digits.',
            'age.between'              => 'Age must be a realistic number between 15 and 35.',
        ];

        $validator = Validator::make($general, $rules, $messages);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $data = [];
        $fields = [
            'student_id', 'first_name', 'last_name', 'course', 'year_level', 'sport',
            'full_name', 'athlete_id', 'middle_initial', 'sport_event', 'status', 'classification',
            'gender', 'birthdate', 'age', 'blood_type', 'email', 'facebook', 'marital_status',
            'contact_number', 'address', 'city_municipality', 'province_state', 'zip_code',
            'emergency_person', 'emergency_contact', 'coach_id', 'date_joined', 'term_graduated',
            'asst_coach', 'total_unit', 'year_graduated', 'tuition_fee', 'misc_fee', 'other_charges',
            'total_assessment', 'total_discount', 'balance', 'current_work', 'current_company',
            'picture_path', 'notes', 'inactive_date'
        ];

        foreach ($fields as $f) {
            if (array_key_exists($f, $general)) {
                $data[$f] = $general[$f];
            }
        }

        if (isset($general['province_state'])) {
            $data['province_state'] = $general['province_state'];
            $data['province'] = $general['province_state'];
        }

        if (auth()->check()) {
            if (auth()->user()->role === 'coach') {
                $data['coach_id'] = auth()->user()->coach->id ?? auth()->user()->coach_id ?? null;
                $data['status'] = 'Pending';
                $data['approval_status'] = 'pending';
            } elseif (auth()->user()->role === 'admin') {
                $data['status'] = isset($general['status']) && $general['status'] !== '' ? $general['status'] : 'Active';
                $data['approval_status'] = 'approved';
            }
        }

        try {
            DB::transaction(function () use ($athlete, $data, $payload) {
                $athlete->update($data);
                $athlete->achievements()->delete();
                if (!empty($payload['achievements'])) {
                    foreach ($payload['achievements'] as $ach) {
                        Achievement::create(array_merge(['athlete_id' => $athlete->id], $ach));
                    }
                }
            });

            $athlete->refresh();
            BlockchainService::logAction('Updated Athlete Profile: ' . $athlete->first_name . ' ' . $athlete->last_name, $athlete->toArray());

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'athlete' => $athlete]);
            }
            return redirect()->route('athletes.index')->with('success', 'Athlete updated successfully!');
        } catch (\Throwable $e) {
            return back()->withErrors('Failed to update athlete.');
        }
    }

    public function showPublicRegistrationForm()
    {
        return view('features.tryout_registration');
    }

    public function storePublicRegistration(Request $request)
    {
        $rules = [
            'classification' => 'required|in:Alumni,Tryout',
            'student_id' => [
                'required_unless:classification,Tryout',
                'nullable',
                'string',
                'regex:/^[0-9]{2}-[0-9]{4}-[0-9]{3}$/'
            ],
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => ['required', 'string', 'email', 'max:255', 'regex:/^[\w\-\.]+@([\w\-]+\.)+[a-zA-Z]{2,7}$/'],
            'contact_number' => ['required', 'regex:/^09[0-9]{9}$/'],
            'sport_event'    => 'required|string',
            'course'         => 'nullable|string|max:255',
            'year_graduated'  => 'nullable|integer',
            'current_work'    => 'nullable|string|max:255',
            'current_company' => 'nullable|string|max:255',
        ];

        $messages = [
            'email.regex'          => 'Please provide a valid and complete email address (e.g., name@gmail.com).',
            'contact_number.regex' => 'Contact Number must start with 09 and be exactly 11 digits.',
        ];

        $validated = $request->validate($rules, $messages);

        // 🔒 ALUMNI DUPLICATION PREVENTION:
        // cross-reference the submission against the existing records
        // (inactive / graduated / alumni / pending) BEFORE anything is saved.
        if ($validated['classification'] === 'Alumni') {
            [$existing, $matchedById] = $this->findExistingAlumniProfile($validated);

            if ($existing) {
                return back()
                    ->withInput()
                    ->withErrors(['student_id' => $this->duplicateAlumniMessage($existing, $matchedById)]);
            }
        }

        try {
            $athlete = \App\Models\Athlete::create([
                'student_id' => $request->input('student_id'),
                'first_name' => $validated['first_name'],
                'middle_initial' => $request->input('middle_initial'),
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'sport_event' => $validated['sport_event'],
                'status' => 'Pending',
                'approval_status' => 'pending',
                'classification' => $validated['classification'],
                'picture_path' => null,
                'course' => $request->input('course'),
                'year_level' => $request->input('year_level'),
                'contact_number' => $request->input('contact_number'),
                'address' => $request->input('address'),
                'city_municipality' => $request->input('city_municipality'),
                'year_graduated' => $request->input('year_graduated'),
                'current_work' => $request->input('current_work'),
                'current_company' => $request->input('current_company'),
            ]);

            BlockchainService::logAction('Public Registration (' . $validated['classification'] . '): ' . $athlete->first_name . ' ' . $athlete->last_name, $athlete->toArray());

            if ($validated['classification'] === 'Tryout') {
                $schedule = \App\Models\TryoutSchedule::where('sport_event', $validated['sport_event'])->first();

                if ($schedule) {
                    $date = \Carbon\Carbon::parse($schedule->tryout_date)->format('F d, Y');
                    $time = \Carbon\Carbon::parse($schedule->tryout_time)->format('h:i A');
                    $message = "Your tryout is scheduled on <strong>{$date}</strong> at <strong>{$time}</strong>. Venue: <strong>{$schedule->venue}</strong>. Notes: {$schedule->notes}";
                    return redirect()->back()->with('tryout_success', $message);
                } else {
                    return redirect()->back()->with('success', 'Registration Successful! The SDO has not posted a schedule for your sport yet. Keep an eye out for announcements.');
                }
            }

            return redirect()->back()->with('success', 'Registration submitted successfully! Please wait for SDO verification.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error saving registration: ' . $e->getMessage());
        }
    }

    /**
     * Looks for an existing profile that the incoming alumni submission would duplicate.
     * Rejected applications never count. Returns [Athlete|null, matchedByStudentId(bool)].
     *
     *  1. Same Student ID (ignoring old tryout-only records)
     *  2. Same first + last name AND same sport, among inactive / graduated / alumni records
     */
    private function findExistingAlumniProfile(array $v): array
    {
        $notRejected = function ($q) {
            $q->whereNull('approval_status')->orWhere('approval_status', '!=', 'rejected');
        };

        // 1. Student ID match
        if (!empty($v['student_id'])) {
            $byId = Athlete::where($notRejected)
                ->where('student_id', $v['student_id'])
                ->where(function ($q) {
                    $q->whereNull('classification')->orWhere('classification', '!=', 'Tryout');
                })
                ->first();

            if ($byId) {
                return [$byId, true];
            }
        }

        // 2. Name + sport match against the inactive / alumni database
        $first = mb_strtolower(trim($v['first_name']));
        $last  = mb_strtolower(trim($v['last_name']));
        $sport = str_replace('_', ' ', $v['sport_event']);

        $byName = Athlete::where($notRejected)
            ->whereRaw('LOWER(TRIM(first_name)) = ?', [$first])
            ->whereRaw('LOWER(TRIM(last_name)) = ?', [$last])
            ->whereRaw("REPLACE(sport_event, '_', ' ') = ?", [$sport])
            ->where(function ($q) {
                $q->whereIn('status', ['Inactive', 'Graduated'])
                  ->orWhere('classification', 'Alumni');
            })
            ->first();

        return [$byName, false];
    }

    private function duplicateAlumniMessage(Athlete $existing, bool $matchedById): string
    {
        $subject = $matchedById ? 'This Student ID' : 'A profile with the same name and sport';

        if ($existing->classification === 'Alumni' && $existing->approval_status === 'approved') {
            $detail = 'is already in the alumni records';
        } elseif ($existing->classification === 'Alumni') {
            $detail = 'has already been submitted and is waiting for SDO verification';
        } else {
            $detail = 'already has a profile in the SDO records (status: ' . ($existing->status ?: 'on file') . ')';
        }

        return "{$subject} {$detail}. To avoid duplicate profiles, please contact the Sports Development Office if your details need updating.";
    }

    public function printProfile($id)
    {
        $athlete = \App\Models\Athlete::findOrFail($id);
        return view('features.print_profile', compact('athlete'));
    }
}