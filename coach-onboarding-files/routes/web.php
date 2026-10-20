<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\AdminMiddleware;

// Controllers
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AthleteController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AcademicEvaluationController;
use App\Http\Controllers\FeesDiscountController;
use App\Http\Controllers\WorkHistoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\SportsController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\BlockchainController;
use App\Http\Controllers\SetPasswordController;

// New Global Achievements
use App\Http\Controllers\AchievementController;

// Coach Sub-Controllers
use App\Http\Controllers\CoachAchievementController;
use App\Http\Controllers\CoachScheduleController;
use App\Http\Controllers\CoachExpenseController;
use App\Http\Controllers\CoachMembershipController;
use App\Http\Controllers\CoachSeminarController;
use App\Http\Controllers\CoachWorkHistoryController;

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TryoutScheduleController;

// ==============================================================
// PUBLIC ROUTES (No Login Required)
// ==============================================================

// Login Page (Home)
Route::get('/', function () {
    return view('log.login');
})->name('log.login');

// Login Page
Route::get('/login', function () {
    return view('log.login');
})->name('login');

// Handle Login
Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }
    return back()->withErrors(['email' => 'Invalid credentials.']);
})->name('login.submit');

// Handle Real Logout (Invalidates session and flushes cookies)
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

Route::get('/register', [\App\Http\Controllers\AthleteRegistrationController::class, 'create'])->name('public.athlete.register');
Route::post('/register', [\App\Http\Controllers\AthleteRegistrationController::class, 'store'])->name('public.athlete.store');

// ==============================================================
// COACH ONBOARDING: set-your-password link (signed + expiring, sent by email)
// ==============================================================
Route::get('/set-password/{user}', [SetPasswordController::class, 'show'])
    ->middleware('signed')
    ->name('password.setup');
Route::post('/set-password/{user}', [SetPasswordController::class, 'update'])
    ->middleware('signed')
    ->name('password.setup.update');

// ==============================================================
// PUBLIC TRYOUT & ALUMNI REGISTRATION
// ==============================================================
Route::get('/tryout-registration', [AthleteController::class, 'showPublicRegistrationForm'])->name('tryout.register.show');
Route::post('/tryout-registration', [AthleteController::class, 'storePublicRegistration'])->name('tryout.register.submit');

// Public Alumni Form Link
Route::get('/alumni/form', function () {
    return view('alumni.form');
})->name('alumni.form.show');

// Public Alumni Form Submission
Route::post('/alumni/form', [AthleteController::class, 'storePublicRegistration'])
    ->name('alumni.form.store');

// ==============================================================
// AUTHENTICATED ROUTES (General - Login Required)
// ==============================================================
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Dashboard chart refresh (JSON, used by the Year filter - works for admin AND coach)
    Route::get('/dashboard/achievements-chart', [DashboardController::class, 'achievementsChart'])->name('dashboard.achievements-chart');

    // ✅ FIX: Sports Programs roster headcount (JSON) - works for admin AND coach.
    // SportsController@filter already restricts coaches to their own roster.
    Route::get('/sports/filter/{sport}', [SportsController::class, 'filter'])->name('sports.filter.global');

    // Global Achievements Route
    Route::get('/achievements', [AchievementController::class, 'globalIndex'])->name('achievements.index');

    // Report Routes (accessible by both coach and admin)
    Route::get('/reports/{report}/download', [\App\Http\Controllers\ReportController::class, 'download'])
        ->name('reports.download');
    Route::patch('/reports/{report}/mark-received', [\App\Http\Controllers\ReportController::class, 'markReceived'])
        ->name('reports.mark-received');
    Route::patch('/reports/{report}/mark-rejected', [\App\Http\Controllers\ReportController::class, 'markRejected'])
        ->name('reports.mark-rejected');

    Route::get('/coach', function () { return view('features.coach'); })->name('coach');
    Route::get('/schedule', function () { return view('features.schedule'); })->name('schedule');
    Route::get('/student-athlete', [AthleteController::class, 'create'])->name('student.athlete');

    // Student Athlete Logic
    Route::get('/student-athletes', [AthleteController::class, 'index'])->name('student.athletes');

    // Athlete CRUD
    Route::get('/athletes', [AthleteController::class, 'index'])->name('athletes.index');
    Route::get('/athletes/create', [AthleteController::class, 'create'])->name('athletes.create');
    Route::post('/athletes', [AthleteController::class, 'store'])->name('athletes.store');
    Route::get('/athletes/search', [AthleteController::class, 'search'])->name('athletes.search');
    Route::get('/athletes/{athlete}', [AthleteController::class, 'show'])->name('athletes.show');
    Route::put('/athletes/{athlete}', [AthleteController::class, 'update'])->name('athletes.update');

    // PRINT ROUTES FOR ATHLETES AND COACHES
    Route::get('/athlete/{id}/print', [App\Http\Controllers\AthleteController::class, 'printProfile'])->name('athlete.print');
    Route::get('/coach/{id}/print', [App\Http\Controllers\CoachController::class, 'printProfile'])->name('coach.print');

    // Related Athlete Tables (Achievements, Grades, Fees, Work)
    Route::post('/academic-evaluation', [AcademicEvaluationController::class, 'store']);
    Route::get('/academic-evaluation/{athlete_id}', [AcademicEvaluationController::class, 'show']);

    Route::post('/fees-discounts', [FeesDiscountController::class, 'store']);
    Route::get('/fees-discounts/{athlete_id}', [FeesDiscountController::class, 'show']);

    Route::post('/work-history', [WorkHistoryController::class, 'store']);
    Route::get('/work-history/{athlete_id}', [WorkHistoryController::class, 'show']);

    // Shared Attendance History for both Admin and Coach
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');

    // ==============================================================
    // COACH ROUTES
    // ==============================================================
    Route::get('/coaches', [CoachController::class, 'index'])->name('coaches.index');
    Route::get('/coaches/create', [CoachController::class, 'create'])->name('coaches.create');
    Route::post('/coaches', [CoachController::class, 'store'])->name('coaches.store');
    Route::get('/coaches/search', [CoachController::class, 'search'])->name('coaches.search');
    Route::get('/coaches-api/available-sports', [CoachController::class, 'getAvailableSports'])->name('coaches.available-sports');
    Route::get('/coaches/{coach}', [CoachController::class, 'show'])->name('coaches.show');
    Route::put('/coaches/{coach}', [CoachController::class, 'update'])->name('coaches.update');

    // Coach Sub-features
    Route::get('/coaches/{id}/achievements', [CoachAchievementController::class, 'show'])->name('coach.achievements.show');
    Route::post('/coach-achievements', [CoachAchievementController::class, 'store'])->name('coach.achievements.store');
    Route::put('/coach-achievements/{id}', [CoachAchievementController::class, 'update'])->name('coach.achievements.update');
    Route::delete('/coach-achievements/{id}', [CoachAchievementController::class, 'destroy'])->name('coach.achievements.destroy');

    Route::get('/coach-schedules/{coach_id}', [CoachScheduleController::class, 'show']);
    Route::post('/coach-schedules', [CoachScheduleController::class, 'store']);
    Route::put('/coach-schedules/{id}', [CoachScheduleController::class, 'update']);
    Route::delete('/coach-schedules/{id}', [CoachScheduleController::class, 'destroy']);

    Route::get('/coach-expenses/{coach_id}', [CoachExpenseController::class, 'show']);
    Route::post('/coach-expenses', [CoachExpenseController::class, 'store']);
    Route::put('/coach-expenses/{id}', [CoachExpenseController::class, 'update']);
    Route::delete('/coach-expenses/{id}', [CoachExpenseController::class, 'destroy']);

    Route::get('/coach-memberships/{coach_id}', [CoachMembershipController::class, 'show']);
    Route::post('/coach-memberships', [CoachMembershipController::class, 'store']);
    Route::put('/coach-memberships/{id}', [CoachMembershipController::class, 'update']);
    Route::delete('/coach-memberships/{id}', [CoachMembershipController::class, 'destroy']);

    Route::get('/coach-seminars/{coach_id}', [CoachSeminarController::class, 'show']);
    Route::post('/coach-seminars', [CoachSeminarController::class, 'store']);
    Route::put('/coach-seminars/{id}', [CoachSeminarController::class, 'update']);
    Route::delete('/coach-seminars/{id}', [CoachSeminarController::class, 'destroy']);

    Route::get('/coach-work-history/{coach_id}', [CoachWorkHistoryController::class, 'show']);
    Route::post('/coach-work-history', [CoachWorkHistoryController::class, 'store']);
    Route::put('/coach-work-history/{id}', [CoachWorkHistoryController::class, 'update']);
    Route::delete('/coach-work-history/{id}', [CoachWorkHistoryController::class, 'destroy']);

    // Incident Router Endpoints
    Route::post('/incidents/report', [IncidentController::class, 'createReport'])->name('incidents.report');
    Route::put('/incidents/approve/{report_id}', [IncidentController::class, 'approveTicket'])->name('incidents.approve');

    // Coach Attendance Routes
    Route::prefix('coach')
        ->name('coach.')
        ->group(function () {
            Route::get('/attendance', [AttendanceController::class, 'coachIndex'])
                ->name('attendance.index');

            Route::post('/attendance', [AttendanceController::class, 'store'])
                ->name('attendance.store');

            // Coach Report Routes
            Route::get('/reports', [ReportController::class, 'coachIndex'])
                ->name('reports.index');

            Route::post('/reports', [ReportController::class, 'coachStore'])
                ->name('reports.store');
        });
});

// ==============================================================
// ADMIN PANEL ROUTES (Protected strictly by AdminMiddleware)
// ==============================================================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', AdminMiddleware::class])
    ->group(function () {

    // 1. Dashboard Views
    // "General Info" now lives on the dedicated Settings page - old links still work.
    Route::redirect('/general', '/admin/settings')->name('general');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/classes', [AdminController::class, 'classes'])->name('classes');
    Route::get('/scheduling', [AdminController::class, 'scheduling'])->name('scheduling');
    Route::get('/certificates', [AdminController::class, 'certificates'])->name('certificates');
    Route::get('/grades', [AdminController::class, 'grades'])->name('grades');
    Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');

    // Security Ledger (Blockchain)
    Route::get('/security-ledger', [BlockchainController::class, 'index'])->name('blockchain');

    // 2. APPROVALS (Using AthleteController to handle approvals)
    Route::get('/approvals', [AthleteController::class, 'showApprovals'])->name('approvals');
    Route::get('/approvals/{id}/view', [AthleteController::class, 'show'])->name('approvals.show');
    Route::get('/athletes/print/{id}', [App\Http\Controllers\AthleteController::class, 'printProfile'])->name('athletes.print');

    // These are the Actions for Athletes (Approve/Reject)
    Route::post('/approvals/{id}/approve', [AthleteController::class, 'approve'])->name('approve.athlete');
    Route::post('/approvals/{id}/decline', [AthleteController::class, 'decline'])->name('reject.athlete');

    // These are the Actions for Alumni (Approve/Reject)
    Route::post('/alumni/{id}/approve', [AthleteController::class, 'approveAlumni'])->name('approve.alumni');
    Route::post('/alumni/{id}/reject', [AthleteController::class, 'rejectAlumni'])->name('reject.alumni');

    // 3. Other Admin Actions
    Route::post('/save-settings', [AdminController::class, 'saveSettings'])->name('saveSettings');
    Route::post('/add-class', [AdminController::class, 'addClass'])->name('addClass');
    Route::post('/add-transaction', [AdminController::class, 'addTransaction'])->name('addTransaction');
    Route::post('/save-grades', [AdminController::class, 'saveGrades'])->name('saveGrades');
    Route::post('/users/update', [AdminController::class, 'updateUserPermissions'])->name('updateUserPermissions');
    Route::post('/users/create-coach', [AdminController::class, 'createCoachUser'])->name('createCoachUser');
    Route::post('/add-holiday', [AdminController::class, 'addHoliday'])->name('addHoliday');
    Route::post('/add-certificate', [AdminController::class, 'addCertificate'])->name('addCertificate');
    Route::delete('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('deleteUser');

    // Sports Management inside Admin
    Route::get('/sports-management', [SportsController::class, 'manageIndex'])->name('sports.manage');
    Route::post('/sports-management/store', [SportsController::class, 'manageStore'])->name('sports.manage.store');
    Route::delete('/sports-management/{id}/delete', [SportsController::class, 'manageDestroy'])->name('sports.manage.destroy');

    // Admin Attendance & Reports (Aliased to match sidebar names)
    Route::get('/attendance', [AttendanceController::class, 'adminIndex'])->name('attendance');
    Route::get('/reports', [ReportController::class, 'adminIndex'])->name('reports');

    // Tryouts Management
    Route::get('/tryouts', [TryoutScheduleController::class, 'index'])->name('tryouts.index');
    Route::post('/tryouts', [TryoutScheduleController::class, 'store'])->name('tryouts.store');
    Route::post('/tryouts/{id}/update', [TryoutScheduleController::class, 'update'])->name('tryouts.update');
    Route::post('/tryouts/{id}/delete', [TryoutScheduleController::class, 'destroy'])->name('tryouts.destroy');

    Route::get('/sports/filter/{sport}', [SportsController::class, 'filter'])->name('sports.filter');
});

// Sports view (Guarded by AdminMiddleware so coaches cannot access it)
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/sports', [SportsController::class, 'index'])->name('sports');
    // NOTE: '/sports/filter/{sport}' was moved to the general auth group above so coaches can use it.
});
