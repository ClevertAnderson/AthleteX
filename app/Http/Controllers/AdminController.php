<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// All necessary models for the Admin module must be imported here:
use App\Models\Setting;
use App\Models\Course;
use App\Models\User;
use App\Models\Coach;
use App\Models\TransactionItem;
use App\Models\Holiday; // Added for scheduling
use App\Models\Certificate; // Added for certificates
use App\Models\GradeScale; 
use App\Models\ScholarshipCriteria;
use App\Models\Sport;
use Illuminate\Support\Facades\Mail;
use App\Mail\CoachCredentialsMail;

class AdminController extends Controller
{
    // --- VIEW LOADERS (Fetch data for the specific tab) ---

    // "General Information" is now the Settings page. Old links/bookmarks to /admin/general
    // still work because they redirect here.
    public function general() { 
        return redirect()->route('admin.settings'); 
    }
    
    public function settings() { 
        return view('admin.settings'); 
    }
    
    public function users() { 
        $users = User::paginate(10);
        $sports = Sport::orderBy('name')->get(); 
        return view('admin.users', compact('users', 'sports')); 
    }
    
    public function classes() { 
        $courses = Course::all(); 
        return view('admin.classes', compact('courses')); 
    }
    
    public function scheduling() { 
        // Loads data for the Holidays table
        $holidays = Holiday::all(); 
        return view('admin.scheduling', compact('holidays')); 
    }
    
    public function certificates() { 
        // Loads data for the Certificates table
        $certs = Certificate::all(); 
        return view('admin.certificates', compact('certs'));
    }
    
    public function grades() { 
        return view('admin.grades'); 
    }
    
    public function transactions() { 
        $items = TransactionItem::all(); 
        return view('admin.transactions', compact('items')); 
    }

    // --- ACTIONS (Save Logic) ---
    public function saveSettings(Request $request) 
    {
        // Validate the known fields (all optional). Any other posted keys are still saved below,
        // exactly like before, so other forms that post to this route keep working.
        $request->validate([
            // School profile
            'school_name'  => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'phone_1'      => 'nullable|string|max:50',
            'phone_2'      => 'nullable|string|max:50',
            'address'      => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:255',
            'province'     => 'nullable|string|max:255',
            'zip'          => 'nullable|string|max:20',
            'logo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            // Academic year & semester
            'academic_year'    => ['nullable', 'regex:/^\d{4}-\d{4}$/'],
            'current_semester' => 'nullable|in:1st Semester,2nd Semester,Summer',
            'semester_start'   => 'nullable|date',
            'semester_end'     => 'nullable|date|after_or_equal:semester_start',

            // Email & notifications
            'notification_email'       => 'nullable|email|max:255',
            'notify_new_registration'  => 'nullable|in:0,1',
            'notify_approval_decision' => 'nullable|in:0,1',
            'send_coach_credentials'   => 'nullable|in:0,1',

            // Security
            'session_timeout'         => 'nullable|integer|min:0|max:480',
            'password_min_length'     => 'nullable|integer|min:6|max:32',
            'password_require_number' => 'nullable|in:0,1',
            'password_require_symbol' => 'nullable|in:0,1',

            // System preferences
            'records_per_page' => 'nullable|integer|min:5|max:100',
        ], [
            'academic_year.regex'           => 'Use the format 2026-2027.',
            'semester_end.after_or_equal'   => 'The semester end date must be on or after the start date.',
        ]);

        $data = $request->except(['_token', 'logo']);
        
        if ($request->hasFile('logo')) {
             $path = $request->file('logo')->store('uploads', 'public');
             Setting::set('logo', $path);
        }
        
        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }
        return back()->with('success', 'Settings Saved!');
    }

    public function updateUserPermissions(Request $request) {
    // 1. Get the array of permissions from the form
    $permissionsData = $request->input('permissions'); 

        if ($permissionsData) {
            foreach ($permissionsData as $userId => $perms) {
                // 2. Find the user
                $user = User::find($userId);
                
                if ($user) {
                    // 3. Save the array (Laravel automatically converts this to JSON because of the Model Cast)
                    $user->permissions = $perms; 
                    $user->save();
                }
            }
            return back()->with('success', 'Security Rights Updated!');
        }
        
        return back()->with('success', 'No changes detected.'); 
    }

    public function createCoachUser(Request $request)
    {
        // Password rules come from Admin > Settings > Security
        $minLength = max(6, (int) Setting::get('password_min_length', 8));
        $passwordRules = ['required', 'string', "min:{$minLength}", 'confirmed'];

        if (Setting::get('password_require_number', '0') === '1') {
            $passwordRules[] = function ($attribute, $value, $fail) {
                if (! preg_match('/[0-9]/', (string) $value)) {
                    $fail('The password must contain at least one number.');
                }
            };
        }

        if (Setting::get('password_require_symbol', '0') === '1') {
            $passwordRules[] = function ($attribute, $value, $fail) {
                if (! preg_match('/[^A-Za-z0-9]/', (string) $value)) {
                    $fail('The password must contain at least one symbol (e.g. ! @ # $).');
                }
            };
        }

        // 1. Validate the input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => $passwordRules,
            'coach_sport' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!Sport::where('name', $value)->exists()) {
                        $fail('The selected sport is invalid.');
                    }
                },
            ],
        ]);

        // 2. Create the user account
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'coach',
            'coach_sport' => $validated['coach_sport'],
        ]);

        // 3. Attempt to find or create a minimal Coach profile and link it to the user
        // First try to find an existing coach by email
        $coach = Coach::where('coach_email', $user->email)->first();
        if (!$coach) {
            // Split name into first and last if possible
            $nameParts = preg_split('/\s+/', trim($user->name));
            $first = $nameParts[0] ?? $user->name;
            $last = count($nameParts) > 1 ? array_pop($nameParts) : '';

            $coach = Coach::create([
                'coach_first_name' => $first,
                'coach_last_name' => $last ?: $first,
                'coach_email' => $user->email,
                'coach_sport_event' => $validated['coach_sport'],
            ]);
        }

        // Link the user to the coach profile
        $user->coach_id = $coach->id;
        if (empty($user->coach_sport)) {
            $user->coach_sport = $validated['coach_sport'];
        }
        $user->save();

        // ✉️ NEW: ACTUALLY SEND THE EMAIL
        try {
            $mailData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'] // The password the admin typed
            ];
            Mail::to($validated['email'])->send(new CoachCredentialsMail($mailData));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => "Coach account created, but email failed to send: " . $e->getMessage()]);
        }

        return back()->with('success', "Coach user account '{$user->email}' created and linked to coach profile (ID: {$coach->id}) for {$validated['coach_sport']}.");
    }

    // --- NEW: Delete User / Remove Coach Method ---
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting your own currently logged-in account
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own active administrator account.');
        }

        // If the user is a coach, also delete their linked coach profile
        if ($user->role === 'coach' && $user->coach) {
            $user->coach->delete();
        }

        $user->delete();

        return redirect()->back()->with('success', 'User account successfully removed.');
    }
    
    public function addClass(Request $request) {
        // Simplified creation for demonstration
        Course::forceCreate($request->except('_token'));
        return back()->with('success', 'Class Added');
    }

    public function saveGrades(Request $request) {
        // 1. Save Grades
        if ($request->has('grades')) {
            \App\Models\GradeScale::truncate(); // Deletes old rows
            
            foreach ($request->grades as $grade) {
                // Only save if the Grade letter is typed in
                if (!empty($grade['grade'])) {
                    \App\Models\GradeScale::create($grade);
                }
            }
        }

        // 2. Save Scholarships
        if ($request->has('scholarships')) {
            \App\Models\ScholarshipCriteria::truncate(); // Deletes old rows
            
            foreach ($request->scholarships as $scholarship) {
                // Only save if the Level is typed in
                if (!empty($scholarship['level'])) {
                    \App\Models\ScholarshipCriteria::create($scholarship);
                }
            }
        }

        return back()->with('success', 'Grades & Scoring Updated!');
    }

    public function addCertificate(Request $request) {
        // 1. Validate input
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'file' => 'required|file|max:10240' // Max 10MB
        ]);

        // 2. Upload the file
        // Saves to storage/app/public/certificates/
        $path = $request->file('file')->store('certificates', 'public');

        // 3. Save to Database
        \App\Models\Certificate::create([
            'name' => $request->name,
            'type' => $request->type,
            'filename' => $path
        ]);

        return back()->with('success', 'Certificate Added Successfully!');
    }
    
    public function addTransaction(Request $request) {
        // Simplified creation for demonstration
        TransactionItem::forceCreate($request->except('_token'));
        return back()->with('success', 'Item Added');
    }

    public function addHoliday(Request $request) {
        \App\Models\Holiday::forceCreate($request->except('_token'));
        return back()->with('success', 'Holiday Added Successfully!');
    }

    // Approve the pending registration
    public function approveAthlete($id)
    {
        $athlete = \App\Models\Athlete::findOrFail($id);
        
        // Change status to 'approved' so they appear in the main list
        $athlete->approval_status = 'approved';
        $athlete->save();

        return back()->with('success', 'Athlete has been successfully verified and added to the official list.');
    }

    // Reject and delete the registration
    public function rejectAthlete($id)
    {
        $athlete = \App\Models\Athlete::findOrFail($id);
        $athlete->delete();

        return back()->with('success', 'Registration request rejected and removed.');
    }

    public function approvals() {
        // This grabs EVERY athlete whose status is 'Pending' (Regulars, Alumni, and Tryouts!)
        $pendingAthletes = \App\Models\Athlete::where('status', 'Pending')->get();
        
        return view('features.admin_approvals', compact('pendingAthletes'));
    }
}