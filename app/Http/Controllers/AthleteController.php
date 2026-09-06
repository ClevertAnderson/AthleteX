<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
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

class AthleteController extends Controller
{
    public function index(Request $request)
    {
        // 🚀 Fetch ALL sports from the DB for the dropdown filter
        $sports = \App\Models\Sport::all();

        if (auth()->user()->role === 'admin') {
            
            $athleteQuery = Athlete::where('approval_status', 'approved')
                                   ->where('classification', '!=', 'Tryout');

            // CATCH DASHBOARD FILTER CLICKS
            if ($request->filled('status')) {
                if ($request->status === 'Alumni') {
                    $athleteQuery->where('classification', 'Alumni');
                } else {
                    $athleteQuery->where('status', $request->status)
                                 ->where('classification', '!=', 'Alumni');
                }
            }

            $athletes = $athleteQuery->get();

            $recruits = Athlete::where('status', 'Active')
                               ->where('classification', 'Tryout') 
                               ->get();

        } elseif (auth()->user()->role === 'coach') {
            
            $coachSport = auth()->user()->coach->coach_sport_event ?? null;

            if ($coachSport) {
                $athletes = Athlete::where('sport_event', $coachSport)
                    ->where('approval_status', 'approved')
                    ->where('classification', '!=', 'Tryout')
                    ->get();

                $recruits = Athlete::where('sport_event', $coachSport)
                    ->where('status', 'Active')
                    ->where('classification', 'Tryout')
                    ->get();
            } else {
                $athletes = collect(); 
                $recruits = collect(); 
            }

        } else {
            $athletes = collect(); 
            $recruits = collect(); 
        }
        
        // Pass $sports to the view!
        return view('features.athlete_lists', compact('athletes', 'recruits', 'sports'));
    }

    // ==========================================
    // APPROVALS PAGE LOGIC
    // ==========================================

    public function showApprovals()
    {
        // 1. Public Tryout Applicants (Strictly Tryout classification)
        $tryoutPendings = Athlete::where('approval_status', 'pending')
                            ->where('classification', 'Tryout')
                            ->latest()->get();

        // 2. Coach-Submitted Athletes (Catches non-tryouts AND non-alumni)
        $studentRequests = Athlete::where('approval_status', 'pending')
                            ->where(function($q) {
                                $q->whereNotIn('classification', ['Tryout', 'Alumni'])
                                  ->orWhereNull('classification');
                            })
                            ->latest()->get();

        // 3. Alumni Submissions
        $alumniPendings = Athlete::where('approval_status', 'pending')
                            ->where('classification', 'Alumni')
                            ->latest()->get();

        $approvedAthletes = Athlete::where('status', 'Active')
                                   ->where('approval_status', 'approved')
                                   ->orderBy('updated_at', 'desc')
                                   ->take(5)
                                   ->get();

        $declinedAthletes = Athlete::where('status', 'Declined')
                                   ->orderBy('updated_at', 'desc')
                                   ->take(5)
                                   ->get();

        return view('features.approvals', compact(
            'tryoutPendings', 
            'studentRequests',
            'alumniPendings',
            'approvedAthletes', 
            'declinedAthletes'
        ));
    }

    public function approve(Request $request, $id)
    {
        $athlete = Athlete::findOrFail($id);
        
        $athlete->update([
            'status' => 'Active',
            'approval_status' => 'approved',
        ]);

        // 🚀 BLOCKCHAIN LOGGING: Track Approvals
        BlockchainService::logAction('Approved Athlete Application: ' . $athlete->first_name . ' ' . $athlete->last_name, $athlete->toArray());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Approved!']);
        }

        return redirect()->back()->with('success', 'Athlete approved successfully.');
    }

    public function decline(Request $request, $id)
    {
        $athlete = Athlete::findOrFail($id);
        
        // Grab the data BEFORE we delete it so the blockchain remembers who it was!
        $athleteData = $athlete->toArray(); 
        $athlete->delete();

        // 🚀 BLOCKCHAIN LOGGING: Track Deletions/Declines
        BlockchainService::logAction('Declined & Deleted Athlete Application: ' . $athleteData['first_name'] . ' ' . $athleteData['last_name'], $athleteData);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Rejected and deleted!']);
        }

        return redirect()->back()->with('success', 'Athlete application has been rejected and deleted.');
    }

    // --- NEW ALUMNI APPROVAL LOGIC ---
    public function approveAlumni(Request $request, $id)
    {
        $athlete = Athlete::findOrFail($id);
        
        $athlete->update([
            'status' => 'Graduated', // Marking as an official Alumni
            'approval_status' => 'approved',
        ]);

        BlockchainService::logAction('Verified Alumni Record: ' . $athlete->first_name . ' ' . $athlete->last_name, $athlete->toArray());

        return redirect()->back()->with('success', 'Alumni record successfully verified and added to the master roster!');
    }

    public function rejectAlumni(Request $request, $id)
    {
        $athlete = Athlete::findOrFail($id);
        $athleteData = $athlete->toArray(); 
        $athlete->delete();

        BlockchainService::logAction('Rejected Alumni Submission: ' . $athleteData['first_name'] . ' ' . $athleteData['last_name'], $athleteData);

        return redirect()->back()->with('success', 'Alumni submission was rejected and deleted.');
    }

    public function showPending()
    {
        return $this->showApprovals();
    }

    public function create()
    {
        $sports = Sport::all();
        return view('features.student_athlete', compact('sports'));
    }

    public function store(Request $request)
    {
        $payload = $request->all();
        $general = is_array($payload) && array_key_exists('generalInfo', $payload) && is_array($payload['generalInfo'])
            ? $payload['generalInfo']
            : $payload;

        // STRICT VALIDATION RULES
        $rules = [
            'student_id' => [
                'required',
                'string',
                'max:255',
                'regex:/^[0-9]{2}-[0-9]{4}-[0-9]{3}$/',
                'unique:athletes,student_id'
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

        // STRICT ERROR MESSAGES
        $messages = [
            'student_id.unique'       => 'This Student ID is already registered.',
            'student_id.regex' => 'The Student ID must follow the exact format XX-XXXX-XXX (e.g., 20-3847-666).',
            'first_name.regex'        => 'First Name cannot contain numbers or special characters.',
            'last_name.regex'         => 'Last Name cannot contain numbers or special characters.',
            'birthdate.before'        => 'The birthdate must be a valid date in the past.',
            'email.email'             => 'Please provide a valid email format.',
            'contact_number.regex'    => 'Contact Number must start with 09 and be exactly 11 digits.',
            'emergency_contact.regex' => 'Emergency Contact must start with 09 and be exactly 11 digits.',
            'zip_code.digits'         => 'ZIP code must be exactly 4 digits.',
            'age.between'             => 'Age must be a realistic number between 15 and 35.',
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

        // SMART ROLE-BASED APPROVAL LOGIC
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

            // 🚀 BLOCKCHAIN LOGGING: Track Creation
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

        // 🔒 STRICT RBAC CHECK FOR SEARCH: Prevent coaches from searching other sports
        if (auth()->check() && auth()->user()->role === 'coach') {
            $coachSport = auth()->user()->coach->coach_sport_event ?? null;
            if ($coachSport) {
                $query->where('sport_event', $coachSport);
            } else {
                $query->whereNull('id'); // Return empty if no sport assigned
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
            
        // STRICT VALIDATION RULES
        $rules = [
          'student_id' => [
                'required',
                'string',
                'max:255',
                'regex:/^[0-9]{2}-[0-9]{4}-[0-9]{3}$/',
                'unique:athletes,student_id,' . $athlete->id
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

        // STRICT ERROR MESSAGES
        $messages = [
            'student_id.unique'       => 'This Student ID is already registered.',
            'student_id.regex' => 'The Student ID must follow the exact format XX-XXXX-XXX (e.g., 20-3847-666).',
            'first_name.regex'        => 'First Name cannot contain numbers or special characters.',
            'last_name.regex'         => 'Last Name cannot contain numbers or special characters.',
            'birthdate.before'        => 'The birthdate must be a valid date in the past.',
            'email.email'             => 'Please provide a valid email format.',
            'contact_number.regex'    => 'Contact Number must start with 09 and be exactly 11 digits.',
            'emergency_contact.regex' => 'Emergency Contact must start with 09 and be exactly 11 digits.',
            'zip_code.digits'         => 'ZIP code must be exactly 4 digits.',
            'age.between'             => 'Age must be a realistic number between 15 and 35.',
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

        // ====================================================
        // SMART ROLE-BASED APPROVAL LOGIC FOR UPDATING
        // ====================================================
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

            // 🚀 BLOCKCHAIN LOGGING: Track Profile Updates
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
        // 🔒 STRICT VALIDATION RULES FOR PUBLIC TRYOUT/ALUMNI REGISTRATION
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
            
            // Allow new Alumni employment fields
            'year_graduated'  => 'nullable|integer',
            'current_work'    => 'nullable|string|max:255',
            'current_company' => 'nullable|string|max:255',
        ];

        $messages = [
            'email.regex'          => 'Please provide a valid and complete email address (e.g., name@gmail.com).',
            'contact_number.regex' => 'Contact Number must start with 09 and be exactly 11 digits.',
        ];

        $validated = $request->validate($rules, $messages);

        try {
            $athlete = \App\Models\Athlete::create([
                'student_id' => $request->input('student_id'), 
                'first_name' => $validated['first_name'],
                'middle_initial' => $request->input('middle_initial'),
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'sport_event' => $validated['sport_event'],
                'status' => 'Pending', 
                'approval_status' => 'pending', // VERY IMPORTANT: Required to show up in the approval queues!
                'classification' => $validated['classification'],
                'picture_path' => null, 
                'course' => $request->input('course'),
                'year_level' => $request->input('year_level'),
                'contact_number' => $request->input('contact_number'),
                'address' => $request->input('address'),
                'city_municipality' => $request->input('city_municipality'),
                
                // Add new Alumni data
                'year_graduated' => $request->input('year_graduated'),
                'current_work' => $request->input('current_work'),
                'current_company' => $request->input('current_company'),
            ]);

            // 🚀 BLOCKCHAIN LOGGING: Track Public Registrations
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

    public function printProfile($id)
    {
        $athlete = \App\Models\Athlete::findOrFail($id);
        return view('features.print_profile', compact('athlete'));
    }
}