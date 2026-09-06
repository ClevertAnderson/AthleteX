<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\CoachAchievement;
use App\Models\CoachWorkHistory;
use App\Models\CoachMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CoachController extends Controller
{
    public function index(Request $request)
    {
        // If user is a coach, show only their own profile
        if (auth()->check() && auth()->user()->role === 'coach') {
            $coach = auth()->user()->coach;
            if (!$coach) {
                // Coach user doesn't have a coach profile yet, show creation form
                return view('features.coach', compact('coach'));
            }
            // Load coach with related data - include ALL relationships so all tabs display data
            $coach->load('achievements', 'workHistories', 'memberships', 'schedule', 'expenses', 'seminars');
            return view('features.coach', compact('coach'));
        }
        
        // Admins see all coaches (🚀 CATCH DASHBOARD FILTER CLICKS HERE)
        $query = Coach::query();

        if ($request->filled('status')) {
            // Your database column is called 'coach_status'
            $query->where('coach_status', $request->status);
        }

        $coaches = $query->get();
        
        return view('c_lists.coach_lists', compact('coaches'));
    }

    public function create()
    {
        // If coach user, redirect to index (their profile page)
        if (auth()->check() && auth()->user()->role === 'coach') {
            return redirect()->route('coaches.index');
        }
        return view('features.coach');
    }

    public function store(Request $request)
    {
        // Normalize payload (in case frontend sends nested structure)
        $payload = $request->all();
        $general = is_array($payload) && array_key_exists('generalInfo', $payload) && is_array($payload['generalInfo'])
            ? $payload['generalInfo']
            : $payload;

        // Validate required fields
        $rules = [
            'coach_first_name' => 'required|string|max:255',
            'coach_last_name' => 'required|string|max:255',
        ];

        $validator = Validator::make($general, $rules);

        if ($validator->fails()) {
            Log::warning('Coach store validation failed', ['errors' => $validator->errors()->toArray()]);

            if ($request->expectsJson() || $request->wantsJson() || $request->isJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        // If coach user is updating their own profile
        $isCoachUser = auth()->check() && auth()->user()->role === 'coach';
        $coach = $isCoachUser ? auth()->user()->coach : null;

        // Mass assign coach data from general info
        $fields = [
            'coach_last_name', 'coach_first_name', 'coach_middle_initial',
            'coach_gender', 'coach_birthdate', 'coach_age', 'coach_blood_type',
            'coach_place_of_birth', 'coach_marital_status',
            'coach_email', 'coach_facebook', 'coach_tba', 'coach_contact_number',
            'coach_address', 'coach_city_municipality', 'coach_province_state', 'coach_zip_code',
            'coach_emergency_person', 'coach_emergency_contact', 'post_graduate',
            'course_graduated', 'coach_year_graduated', 'name_collage',
            'coach_course_graduated', 'coach_graduated', 'coach_highschool',
            'strand_graduated', 'highschool_graduated', 'date_hired',
            'honorarium_payment', 'date_resigned', 'occupation', 'coach_current_company',
            'coach_sport_event', 'position', 'coach_status', 'coach_inactive_date', 'coach_notes'
        ];

        $data = [];
        foreach ($fields as $f) {
            if (array_key_exists($f, $general)) {
                $data[$f] = $general[$f];
            }
        }

        try {
            // If coach user updating their own profile, update instead of create
            if ($isCoachUser && $coach) {
                $coach->update($data);
                
                if ($request->expectsJson() || $request->wantsJson() || $request->isJson()) {
                    return response()->json(['success' => true, 'coach' => $coach], 200);
                }
                return redirect()->route('coaches.index')->with('success', 'Your profile has been updated successfully!');
            }

            // Otherwise, create new coach (for admins or first-time coach setup)
            $coachData = DB::transaction(function () use ($data, $payload) {
                $c = Coach::create($data);

                // ACHIEVEMENTS
                if (!empty($payload['achievements']) && is_array($payload['achievements'])) {
                    foreach ($payload['achievements'] as $ach) {
                        CoachAchievement::create([
                            'coach_id' => $c->id,
                            'year' => $ach['year'] ?? null,
                            'month_day' => $ach['month_day'] ?? null,
                            'sports_event' => $ach['sports_event'] ?? null,
                            'venue' => $ach['venue'] ?? null,
                            'award' => $ach['award'] ?? null,
                            'category' => $ach['category'] ?? null,
                            'remarks' => $ach['remarks'] ?? null,
                        ]);
                    }
                }

                // SCHEDULES
                if (!empty($payload['schedule']) && is_array($payload['schedule'])) {
                    foreach ($payload['schedule'] as $sch) {
                        $c->schedule()->create([
                            'term' => $sch['term'] ?? null,
                            'academic_year' => $sch['academic_year'] ?? null,
                            'count_a' => $sch['count_a'] ?? 0,
                            'count_b' => $sch['count_b'] ?? 0,
                            'count_c' => $sch['count_c'] ?? 0, 
                            'remarks' => $sch['remarks'] ?? null,   
                        ]);
                    }
                }

                // EXPENSES
                if (!empty($payload['expenses']) && is_array($payload['expenses'])) {
                    foreach ($payload['expenses'] as $exp) {
                        $c->expenses()->create([
                            'year' => $exp['year'] ?? null,          
                            'date' => $exp['date'] ?? null,
                            'title' => $exp['title'] ?? null,
                            'estimate_budget' => $exp['estimate'] ?? null,
                            'notes' => $exp['notes'] ?? null,
                            'actual_budget' => $exp['actual'] ?? null,
                            'variance' => $exp['variance'] ?? null,     
                            'remark' => $exp['remark'] ?? null,             
                        ]);
                    }
                }

                // MEMBERSHIPS
                if (!empty($payload['memberships']) && is_array($payload['memberships'])) {
                    foreach ($payload['memberships'] as $mem) {
                        $c->memberships()->create([
                            'year' => $mem['year'] ?? null,     
                            'date' => $mem['date'] ?? null,
                            'venue' => $mem['venue'] ?? null,          
                            'name_of_organization' => $mem['organization'] ?? null,         
                            'level' => $mem['level'] ?? null,    
                            'position' => $mem['position'] ?? null,
                            'remarks' => $mem['remark'] ?? null,                   
                        ]);
                    }
                }

                // SEMINARS
                if (!empty($payload['seminars']) && is_array($payload['seminars'])) {
                    foreach ($payload['seminars'] as $sem) {
                        $c->seminars()->create([
                            'year' => $sem['year'] ?? null,
                            'date' => $sem['date'] ?? null,
                            'venue' => $sem['venue'] ?? null,  
                            'title_of_seminar_workshop' => $sem['seminar'] ?? null,
                            'level' => $sem['level'] ?? null,     
                            'remarks' => $sem['remark'] ?? null,         
                        ]);
                    }
                }

                // WORK HISTORY
                if (!empty($payload['workHistory']) && is_array($payload['workHistory'])) {
                    foreach ($payload['workHistory'] as $w) {
                        CoachWorkHistory::create([
                            'coach_id' => $c->id,
                            'year' => $w['year'] ?? null,
                            'date' => $w['date'] ?? null,
                            'work_position' => $w['work_position'] ?? null,    
                            'company_name' => $w['company'] ?? null,     
                            'remarks' => $w['remark'] ?? null,       
                        ]);
                    }
                }

                // If the current authenticated user is a coach user, link the newly
                // created coach profile to their user record so subsequent loads
                // (auth()->user()->coach) return the correct model.
                if (auth()->check()) {
                    $user = auth()->user();
                    if ($user->role === 'coach') {
                        $user->coach_id = $c->id;
                        // Preserve existing coach_sport on user if coach data doesn't include one
                        if (empty($user->coach_sport) && !empty($c->coach_sport_event)) {
                            $user->coach_sport = $c->coach_sport_event;
                        }
                        $user->save();
                    }
                }

                return $c;
            });

            // Handle picture upload if present
            if ($request->hasFile('coach_picture')) {
                $path = $request->file('coach_picture')->store('coaches', 'public');
                $coachData->update(['coach_picture' => $path]);
            }

            if ($request->expectsJson() || $request->wantsJson() || $request->isJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Coach created successfully',
                    'coach' => $coachData
                ], 201);
            }

            return redirect()->route('coaches.show', $coachData->id)->with('success', 'Coach created successfully');
        } catch (\Exception $e) {
            Log::error('Coach creation failed', ['exception' => $e->getMessage()]);

            if ($request->expectsJson() || $request->wantsJson() || $request->isJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to create coach'], 500);
            }

            return back()->with('error', 'Failed to create coach');
        }
    }

    public function update(Request $request, Coach $coach)
    {
        // Normalize payload
        $payload = $request->all();
        $general = is_array($payload) && array_key_exists('generalInfo', $payload) && is_array($payload['generalInfo'])
            ? $payload['generalInfo']
            : $payload;

        // Validate required fields
        $rules = [
            'coach_first_name' => 'required|string|max:255',
            'coach_last_name' => 'required|string|max:255',
        ];

        $validator = Validator::make($general, $rules);

        if ($validator->fails()) {
            Log::warning('Coach update validation failed', ['errors' => $validator->errors()->toArray()]);

            if ($request->expectsJson() || $request->wantsJson() || $request->isJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        // Update coach fields
        $fields = [
            'coach_last_name', 'coach_first_name', 'coach_middle_initial',
            'coach_gender', 'coach_birthdate', 'coach_age', 'coach_blood_type',
            'coach_place_of_birth', 'coach_marital_status',
            'coach_email', 'coach_facebook', 'coach_tba', 'coach_contact_number',
            'coach_address', 'coach_city_municipality', 'coach_province_state', 'coach_zip_code',
            'coach_emergency_person', 'coach_emergency_contact', 'post_graduate',
            'course_graduated', 'coach_year_graduated', 'name_collage',
            'coach_course_graduated', 'coach_graduated', 'coach_highschool',
            'strand_graduated', 'highschool_graduated', 'date_hired',
            'honorarium_payment', 'date_resigned', 'occupation', 'coach_current_company',
            'coach_sport_event', 'position', 'coach_status', 'coach_inactive_date', 'coach_notes'
        ];

        $data = [];
        foreach ($fields as $f) {
            if (array_key_exists($f, $general)) {
                $data[$f] = $general[$f];
            }
        }

        try {
            DB::transaction(function () use ($coach, $data, $payload) {
                $coach->update($data);

                // DELETE AND REPLACE ALL MODULES
                $coach->achievements()->delete();
                $coach->schedule()->delete();
                $coach->expenses()->delete();
                $coach->memberships()->delete();
                $coach->seminars()->delete();
                $coach->workHistories()->delete();

                // ACHIEVEMENTS
                if (!empty($payload['achievements']) && is_array($payload['achievements'])) {
                    foreach ($payload['achievements'] as $ach) {
                        CoachAchievement::create([
                            'coach_id' => $coach->id,
                            'year' => $ach['year'] ?? null,
                            'month_day' => $ach['month_day'] ?? null,
                            'sports_event' => $ach['sports_event'] ?? null,
                            'venue' => $ach['venue'] ?? null,
                            'award' => $ach['award'] ?? null,
                            'category' => $ach['category'] ?? null,
                            'remarks' => $ach['remarks'] ?? null,
                        ]);
                    }
                }

                // SCHEDULES
                if (!empty($payload['schedule']) && is_array($payload['schedule'])) {
                    foreach ($payload['schedule'] as $sch) {
                        $coach->schedule()->create([
                            'term' => $sch['term'] ?? null,
                            'academic_year' => $sch['academic_year'] ?? null,
                            'count_a' => $sch['count_a'] ?? 0,
                            'count_b' => $sch['count_b'] ?? 0,
                            'count_c' => $sch['count_c'] ?? 0,   
                            'remarks' => $sch['remarks'] ?? null,   
                        ]);
                    }
                }

                // EXPENSES
                if (!empty($payload['expenses']) && is_array($payload['expenses'])) {
                    foreach ($payload['expenses'] as $exp) {
                        $coach->expenses()->create([
                            'year' => $exp['year'] ?? null,          
                            'date' => $exp['date'] ?? null,
                            'title' => $exp['title'] ?? null,
                            'estimate_budget' => $exp['estimate'] ?? null,
                            'actual_budget' => $exp['actual'] ?? null,
                            'variance' => $exp['variance'] ?? null,     
                            'remark' => $exp['remark'] ?? null,              
                        ]);
                    }
                }

                // MEMBERSHIPS
                if (!empty($payload['memberships']) && is_array($payload['memberships'])) {
                    foreach ($payload['memberships'] as $mem) {
                        $coach->memberships()->create([
                            'year' => $mem['year'] ?? null,     
                            'date' => $mem['date'] ?? null,
                            'venue' => $mem['venue'] ?? null,          
                            'name_of_organization' => $mem['organization'] ?? null,         
                            'level' => $mem['level'] ?? null,    
                            'position' => $mem['position'] ?? null,
                            'remarks' => $mem['remark'] ?? null,                 
                        ]);
                    }
                }

                // SEMINARS
                if (!empty($payload['seminars']) && is_array($payload['seminars'])) {
                    foreach ($payload['seminars'] as $sem) {
                        $coach->seminars()->create([
                            'year' => $sem['year'] ?? null,
                            'date' => $sem['date'] ?? null,
                            'venue' => $sem['venue'] ?? null,  
                            'title_of_seminar_workshop' => $sem['seminar'] ?? null,
                            'level' => $sem['level'] ?? null,     
                            'remarks' => $sem['remark'] ?? null,         
                        ]);
                    }
                }

                // WORK HISTORY
                if (!empty($payload['workHistory']) && is_array($payload['workHistory'])) {
                    foreach ($payload['workHistory'] as $w) {
                        $coach->workHistories()->create([
                            'year' => $w['year'] ?? null,
                            'date' => $w['date'] ?? null,
                            'work_position' => $w['work_position'] ?? null,    
                            'company_name' => $w['company'] ?? null,     
                            'remarks' => $w['remark'] ?? null,       
                        ]);
                    }
                }
            });

            // Handle picture upload if present
            if ($request->hasFile('coach_picture')) {
                $path = $request->file('coach_picture')->store('coaches', 'public');
                $coach->update(['coach_picture' => $path]);
            }

            if ($request->expectsJson() || $request->wantsJson() || $request->isJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Coach updated successfully',
                    'coach' => $coach
                ]);
            }

            return redirect()->route('coaches.show', $coach->id)->with('success', 'Coach updated successfully');
        } catch (\Exception $e) {
            Log::error('Coach update failed', ['exception' => $e->getMessage()]);

            if ($request->expectsJson() || $request->wantsJson() || $request->isJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to update coach'], 500);
            }

            return back()->with('error', 'Failed to update coach');
        }
    }

    /**
     * Search coaches (AJAX endpoint).
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        return Coach::where('coach_first_name', 'like', "%{$query}%")
            ->orWhere('coach_last_name', 'like', "%{$query}%")
            ->orWhereRaw("CONCAT(coach_first_name, ' ', coach_last_name) LIKE ?", ["%{$query}%"])
            ->limit(10)
            ->get(['id', 'coach_first_name', 'coach_last_name', 'coach_sport_event']);
    }

    public function show(Request $request, Coach $coach)
    {
        // Load relationships - use exact method names from Coach model
        $coach->load([
            'achievements', 
            'schedule',       // This will be 'schedule' in JSON
            'expenses', 
            'memberships', 
            'seminars', 
            'workHistories'   // This will be 'workHistories' in JSON (plural)
        ]);

        $coach->picture_url = $coach->coach_picture ? asset('storage/' . $coach->coach_picture) : null;

        // Return the model - Laravel auto-includes all loaded relationships
        return response()->json($coach);
    }

    // Get available sports (exclude already assigned sports)
    public function getAvailableSports()
    {
        // All available sports
        $allSports = [
            'Basketball',
            'Volleyball',
            'Athletics',
            'Swimming',
            'Taekwondo',
            'Chess',
            'Football',
            'Boxing',
        ];

        // Get sports already assigned to coaches
        $assignedSports = Coach::whereNotNull('coach_sport_event')
            ->pluck('coach_sport_event')
            ->unique()
            ->toArray();

        // Filter out assigned sports
        $availableSports = array_diff($allSports, $assignedSports);

        return response()->json([
            'available' => array_values($availableSports),
            'assigned' => $assignedSports,
        ]);
    }

    // --- 🖨️ ADDED PRINT PROFILE METHOD FOR COACHES ---
    public function printProfile($id)
    {
        $coach = \App\Models\Coach::findOrFail($id);
        // Uses the exact same view as the athletes!
        return view('features.print_profile', compact('coach'));
    }
}