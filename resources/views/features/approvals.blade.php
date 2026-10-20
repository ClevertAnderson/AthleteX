@extends('layouts.app') 

@section('content')

<!-- Custom styles for nested pill navigation -->
<style>
    .nav-pills.custom-pills .nav-link {
        color: #2e4e1f;
        background-color: transparent;
        border: 1px solid transparent;
        transition: all 0.2s ease-in-out;
    }
    .nav-pills.custom-pills .nav-link:hover {
        background-color: #e8f5e9;
    }
    .nav-pills.custom-pills .nav-link.active {
        background-color: #2e4e1f !important;
        color: #ffffff !important;
        border-color: #2e4e1f !important;
    }
</style>

<!-- Main Wrapper with proper spacing for the global sidebar -->
<div class="bg-light min-vh-100 pt-20 md:pt-6 p-4 md:pl-64">

    <!-- ========================================== -->
    <!-- PUBLIC LINKS CARDS (UNIFIED GREEN) -->
    <!-- ========================================== -->
    <div class="row mb-4">
        <!-- Tryout Registration Link Card -->
        <div class="col-md-6 mb-3 mb-md-0">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3" style="background-color: #e8f5e9; border-left: 4px solid #2e4e1f;">
                    <h6 class="fw-bold text-success mb-2">
                        <i class="fas fa-user-plus me-2"></i>Tryout Registration Link
                    </h6>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control bg-white" 
                               value="{{ route('tryout.register.show') }}" 
                               id="regLink" readonly>
                        <button class="btn btn-success fw-bold px-3" type="button" onclick="copyToClipboard('regLink')">
                            Copy
                        </button>
                        <a href="{{ route('tryout.register.show') }}" target="_blank" class="btn btn-outline-success fw-bold px-3">
                            Open
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alumni Form Link Card -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3" style="background-color: #e8f5e9; border-left: 4px solid #2e4e1f;">
                    <h6 class="fw-bold text-success mb-2">
                        <i class="fas fa-graduation-cap me-2"></i>Alumni Tracing Form Link
                    </h6>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control bg-white" 
                               value="{{ route('alumni.form.show') }}" 
                               id="alumniLink" readonly>
                        <button class="btn btn-success fw-bold px-3" type="button" onclick="copyToClipboard('alumniLink')">
                            Copy
                        </button>
                        <a href="{{ route('alumni.form.show') }}" target="_blank" class="btn btn-outline-success fw-bold px-3">
                            Open
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
        <h2 class="fw-bold text-dark fs-3">
            <i class="fas fa-tasks me-2"></i> Approval Queues
        </h2>
    </div>

    <!-- ========================================== -->
    <!-- TABS NAVIGATION -->
    <!-- ========================================== -->
    <ul class="nav nav-tabs mb-4 border-bottom-0" id="approvalTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4 py-3 d-flex align-items-center gap-2" id="tryouts-tab" data-bs-toggle="tab" data-bs-target="#tryouts-pane" type="button" role="tab" aria-controls="tryouts-pane" aria-selected="true" style="color: #2e4e1f;">
                <span><i class="fas fa-user-check me-2"></i> Tryout Verifications</span>
                @if(isset($tryoutPendings) && $tryoutPendings->total() > 0)
                    <span class="badge bg-danger rounded-pill px-2 py-1" id="tryouts-badge" style="font-size: 0.75rem;">{{ $tryoutPendings->total() }} Pending</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-3 text-secondary d-flex align-items-center gap-2" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests-pane" type="button" role="tab" aria-controls="requests-pane" aria-selected="false">
                <span><i class="fas fa-file-signature me-2"></i> Student Requests</span>
                @if(isset($studentRequests) && $studentRequests->total() > 0)
                    <span class="badge bg-danger rounded-pill px-2 py-1" id="requests-badge" style="font-size: 0.75rem;">{{ $studentRequests->total() }} Pending</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-3 text-secondary d-flex align-items-center gap-2" id="alumni-tab" data-bs-toggle="tab" data-bs-target="#alumni-pane" type="button" role="tab" aria-controls="alumni-pane" aria-selected="false">
                <span><i class="fas fa-graduation-cap me-2"></i> Alumni Submissions</span>
                @if(isset($alumniPendings) && $alumniPendings->total() > 0)
                    <span class="badge bg-danger rounded-pill px-2 py-1" id="alumni-badge" style="font-size: 0.75rem;">{{ $alumniPendings->total() }} Pending</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-3 text-secondary d-flex align-items-center gap-2" id="declined-tab" data-bs-toggle="tab" data-bs-target="#declined-pane" type="button" role="tab" aria-controls="declined-pane" aria-selected="false">
                <span><i class="fas fa-ban me-2"></i> Declined</span>
                <span class="badge bg-secondary rounded-pill px-2 py-1" id="declined-badge" style="font-size: 0.75rem; {{ isset($declinedAthletes) && $declinedAthletes->count() > 0 ? '' : 'display:none;' }}">{{ isset($declinedAthletes) ? $declinedAthletes->count() : 0 }}</span>
            </button>
        </li>
    </ul>

    <!-- ========================================== -->
    <!-- TABS CONTENT -->
    <!-- ========================================== -->
    <div class="tab-content" id="approvalTabsContent">
        
        <!-- 1. TRYOUT APPLICANTS PANE (NOW WITH SUB-TABS) -->
        <div class="tab-pane fade show active" id="tryouts-pane" role="tabpanel" aria-labelledby="tryouts-tab" tabindex="0">
            
            <!-- Tryout Sub-Navigation -->
            <ul class="nav nav-pills custom-pills mb-3" id="tryoutSubTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold border border-success px-4" id="sub-pending-tab" data-bs-toggle="pill" data-bs-target="#sub-pending" type="button" role="tab">Pending Applicants</button>
                </li>
                <li class="nav-item ms-2" role="presentation">
                    <button class="nav-link fw-bold border border-success px-4" id="sub-schedules-tab" data-bs-toggle="pill" data-bs-target="#sub-schedules" type="button" role="tab">Manage Schedules</button>
                </li>
                <li class="nav-item ms-2" role="presentation">
                    <button class="nav-link fw-bold border border-success px-4" id="sub-recruits-tab" data-bs-toggle="pill" data-bs-target="#sub-recruits" type="button" role="tab">Passed Recruits</button>
                </li>
            </ul>

            <div class="tab-content" id="tryoutSubTabsContent">
                
                <!-- Sub Tab 1A: Pending Applicants (Original view) -->
                <div class="tab-pane fade show active" id="sub-pending" role="tabpanel">
                    <div class="card shadow-sm border-0 mb-5 border-top border-success border-3">
                        <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                            <h5 class="mb-0 fw-bold text-secondary">Pending Applicants</h5>
                            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
                                <!-- Tryout Search Bar -->
                                <div class="input-group input-group-sm" style="min-width: 220px;">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-search"></i></span>
                                    <input type="text" id="tryoutSearchInput" class="form-control border-start-0" placeholder="Type name or ID to search..." value="{{ request('tryout_search') }}">
                                </div>
                                <!-- Tryout Sport Filter -->
                                <div class="d-flex align-items-center gap-2">
                                    <label for="tryoutSportSelect" class="fw-bold text-muted mb-0 text-nowrap small">Sport:</label>
                                    <select id="tryoutSportSelect" class="form-select form-select-sm">
                                        <option value="ALL">All Sports</option>
                                        @foreach(\App\Models\Sport::orderBy('name', 'asc')->get() as $sport)
                                            <option value="{{ $sport->name }}" {{ request('tryout_sport') == $sport->name ? 'selected' : '' }}>
                                                {{ $sport->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-0" id="tryouts-content">
                            @if(!isset($tryoutPendings) || $tryoutPendings->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                                    <p class="text-muted fs-5">No tryout applicants found.</p>
                                </div>
                            @else
                                <div class="table-responsive w-100 overflow-x-auto">
                                    <table class="table table-hover align-middle mb-0 w-100 text-nowrap" id="tryoutsTable">
                                        <thead class="bg-light text-secondary">
                                            <tr>
                                                <th class="ps-4">Applicant Name</th>
                                                <th>Contact Info</th>
                                                <th>Sport Applying For</th>
                                                <th>Date Applied</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tryoutPendings as $p)
                                            <tr class="athlete-row">
                                                <td class="ps-4 fw-bold text-dark">{{ $p->last_name }}, {{ $p->first_name }} {{ $p->middle_initial }}</td>
                                                <td>
                                                    <span class="text-dark small"><i class="fas fa-envelope me-1"></i> {{ $p->email }}</span><br>
                                                    <span class="text-muted small"><i class="fas fa-phone me-1"></i> {{ $p->contact_number ?? 'N/A' }}</span>
                                                </td>
                                                <td><span class="badge bg-success px-3 py-2 sport-cell">{{ str_replace('_', ' ', $p->sport_event) }}</span></td>
                                                <td class="text-secondary small">{{ $p->created_at->format('M d, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button type="button" class="btn btn-info btn-sm px-3 text-white" data-bs-toggle="modal" data-bs-target="#athleteProfileModal" data-id="{{ $p->id }}" title="View Profile">
                                                            <i class="fas fa-eye me-1"></i> View
                                                        </button>
                                                        <form action="{{ route('admin.approve.athlete', $p->id) }}" method="POST" class="m-0 ajax-action-form">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm px-3" onclick="return confirm('Did {{ $p->first_name }} pass the tryouts? This will make them an Active athlete.')">
                                                                <i class="fas fa-trophy me-1"></i> Passed
                                                            </button>
                                                        </form>
                                                        <!-- Opens the Decline modal (a typed reason is required) -->
                                                        <button type="button" class="btn btn-outline-danger btn-sm px-3"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#declineModal"
                                                                data-action="{{ route('admin.reject.athlete', $p->id) }}"
                                                                data-name="{{ $p->first_name }} {{ $p->last_name }}"
                                                                data-kind="Tryout applicant">
                                                            <i class="fas fa-times me-1"></i> Failed
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Tryout Pagination Links -->
                                <div class="p-3 d-flex flex-column flex-md-row justify-content-between align-items-center border-top bg-white gap-2">
                                    <small class="text-muted">
                                        Showing {{ $tryoutPendings->firstItem() ?? 0 }} to {{ $tryoutPendings->lastItem() ?? 0 }} of {{ $tryoutPendings->total() }} applicants
                                    </small>
                                    <div>
                                        {{ $tryoutPendings->appends(request()->except('tryout_page'))->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sub Tab 1B: Manage Schedules -->
                <div class="tab-pane fade" id="sub-schedules" role="tabpanel">
                    <div class="row">
                        <!-- Add Schedule Form -->
                        <div class="col-lg-4 mb-4">
                            <div class="card shadow-sm border-0 border-top border-success border-4">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h5 class="mb-0 fw-bold text-secondary">Post New Schedule</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.tryouts.store') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-muted small">Sport Event</label>
                                            <select class="form-select" name="sport_event" required>
                                                <option value="" disabled selected>Select a Sport</option>
                                                @foreach(\App\Models\Sport::orderBy('name', 'asc')->get() as $sport)
                                                    <option value="{{ $sport->name }}">{{ $sport->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-muted small">Date</label>
                                            <input type="date" class="form-control" name="tryout_date" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-muted small">Time</label>
                                            <input type="time" class="form-control" name="tryout_time" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-muted small">Venue</label>
                                            <input type="text" class="form-control" name="venue" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-muted small">Status</label>
                                            <select class="form-select" name="status" required>
                                                <option value="Active" selected>Active</option>
                                                <option value="Completed">Completed</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100 fw-bold" style="background-color: #2e4e1f;">Post Schedule</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Schedules List Table -->
                        <div class="col-lg-8 mb-4">
                            <div class="card shadow-sm border-0 border-top border-success border-4">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h5 class="mb-0 fw-bold text-secondary">Tryout Schedules List</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0 w-100 text-nowrap">
                                            <thead class="bg-light text-secondary">
                                                <tr>
                                                    <th class="ps-4">Sport / Status</th>
                                                    <th>Date & Time</th>
                                                    <th>Venue</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($schedules ?? [] as $schedule)
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="badge bg-success mb-1">{{ str_replace('_', ' ', $schedule->sport_event) }}</span><br>
                                                        <span class="badge {{ $schedule->status == 'Active' ? 'bg-primary' : 'bg-secondary' }}">{{ $schedule->status }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($schedule->tryout_date)->format('M d, Y') }}</span><br>
                                                        <span class="text-muted small">{{ \Carbon\Carbon::parse($schedule->tryout_time)->format('h:i A') }}</span>
                                                    </td>
                                                    <td><span class="fw-semibold text-dark">{{ $schedule->venue }}</span></td>
                                                    <td class="text-center">
                                                        <form action="{{ route('admin.tryouts.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Delete this schedule?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-4 text-muted">No schedules posted yet.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    @if(isset($schedules) && $schedules->hasPages())
                                        <div class="p-3 border-top">{{ $schedules->appends(request()->except('schedules_page'))->links('pagination::bootstrap-5') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub Tab 1C: Passed Recruits -->
                <div class="tab-pane fade" id="sub-recruits" role="tabpanel">
                    <div class="card shadow-sm border-0 border-top border-success border-4 mb-5">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold text-secondary">Passed Recruits Overview</h5>
                        </div>
                        <div class="card-body p-0">
                            @if(!isset($recruits) || $recruits->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fas fa-users-slash text-muted mb-3" style="font-size: 3rem;"></i>
                                    <p class="text-muted fs-5">No recruits have passed tryouts recently.</p>
                                </div>
                            @else
                                <div class="table-responsive w-100 overflow-x-auto">
                                    <table class="table table-hover align-middle mb-0 w-100 text-nowrap">
                                        <thead class="bg-light text-secondary">
                                            <tr>
                                                <th class="ps-4">Athlete Name</th>
                                                <th>Contact Info</th>
                                                <th>Sport Event</th>
                                                <th>Date Passed</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recruits as $recruit)
                                            <tr>
                                                <td class="ps-4 fw-bold text-dark">{{ $recruit->last_name }}, {{ $recruit->first_name }} {{ $recruit->middle_initial }}</td>
                                                <td>
                                                    <span class="text-dark small"><i class="fas fa-envelope me-1"></i> {{ $recruit->email }}</span><br>
                                                    <span class="text-muted small"><i class="fas fa-phone me-1"></i> {{ $recruit->contact_number ?? 'N/A' }}</span>
                                                </td>
                                                <td><span class="badge bg-success px-3 py-2">{{ str_replace('_', ' ', $recruit->sport_event) }}</span></td>
                                                <td class="text-secondary small">{{ $recruit->updated_at->format('M d, Y') }}</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-info btn-sm px-3 text-white" data-bs-toggle="modal" data-bs-target="#athleteProfileModal" data-id="{{ $recruit->id }}" title="View Profile">
                                                        <i class="fas fa-eye me-1"></i> View
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3 d-flex flex-column flex-md-row justify-content-between align-items-center border-top bg-white gap-2">
                                    <small class="text-muted">
                                        Showing {{ $recruits->firstItem() ?? 0 }} to {{ $recruits->lastItem() ?? 0 }} of {{ $recruits->total() }} recruits
                                    </small>
                                    <div>
                                        {{ $recruits->appends(request()->except('recruits_page'))->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div> <!-- End Sub Tabs Content -->
        </div>

        <!-- 2. STUDENT REQUESTS PANE -->
        <div class="tab-pane fade" id="requests-pane" role="tabpanel" aria-labelledby="requests-tab" tabindex="0">
            <div class="card shadow-sm border-0 mb-5 border-top border-success border-3">
                
                <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <h5 class="mb-0 fw-bold text-secondary">Coach Requests</h5>
                    
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
                        <!-- Requests Search Bar -->
                        <div class="input-group input-group-sm" style="min-width: 220px;">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" id="requestSearchInput" class="form-control border-start-0" placeholder="Type name or ID to search..." value="{{ request('request_search') }}">
                        </div>

                        <!-- Requests Sport Filter -->
                        <div class="d-flex align-items-center gap-2">
                            <label for="requestSportSelect" class="fw-bold text-muted mb-0 text-nowrap small">Sport:</label>
                            <select id="requestSportSelect" class="form-select form-select-sm">
                                <option value="ALL">All Sports</option>
                                @foreach(\App\Models\Sport::orderBy('name', 'asc')->get() as $sport)
                                    <option value="{{ $sport->name }}" {{ request('request_sport') == $sport->name ? 'selected' : '' }}>
                                        {{ $sport->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0" id="requests-content">
                    @if(!isset($studentRequests) || $studentRequests->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-check text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted fs-5">No pending student requests found.</p>
                        </div>
                    @else
                        <div class="table-responsive w-100 overflow-x-auto">
                            <table class="table table-hover align-middle mb-0 w-100 text-nowrap" id="requestsTable">
                                <thead class="bg-light text-secondary">
                                    <tr>
                                        <th class="ps-4">Athlete Name</th>
                                        <th>Student ID</th>
                                        <th>Sport & Class</th>
                                        <th>Submitted By</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($studentRequests as $req)
                                    <tr class="athlete-row">
                                        <td class="ps-4 fw-bold text-dark">{{ $req->last_name }}, {{ $req->first_name }} {{ $req->middle_initial }}</td>
                                        <td class="text-secondary">{{ $req->student_id }}</td>
                                        <td class="sport-cell-container">
                                            <span class="badge bg-success px-2 py-1 mb-1 sport-cell">{{ str_replace('_', ' ', $req->sport_event) }}</span><br>
                                            <span class="badge bg-secondary px-2 py-1">{{ str_replace('_', ' ', $req->classification) }}</span>
                                        </td>
                                        <td class="text-secondary small">
                                            {{ $req->coach ? $req->coach->coach_first_name . ' ' . $req->coach->coach_last_name : 'N/A' }}
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-info btn-sm px-3 text-white" data-bs-toggle="modal" data-bs-target="#athleteProfileModal" data-id="{{ $req->id }}" title="View Profile">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </button>
                                                <!-- NOTE: This button opens the Scholarship modal. It is NOT a form. -->
                                                <button type="button" class="btn btn-success btn-sm px-3" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#scholarshipModal"
                                                        data-id="{{ $req->id }}" 
                                                        data-name="{{ $req->first_name }} {{ $req->last_name }}" 
                                                        data-classification="{{ $req->classification }}">
                                                    <i class="fas fa-check me-1"></i> Approve
                                                </button>
                                                <!-- Opens the Decline modal (a typed reason is required) -->
                                                <button type="button" class="btn btn-outline-danger btn-sm px-3"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#declineModal"
                                                        data-action="{{ route('admin.reject.athlete', $req->id) }}"
                                                        data-name="{{ $req->first_name }} {{ $req->last_name }}"
                                                        data-kind="Student request">
                                                    <i class="fas fa-times me-1"></i> Reject
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Student Requests Pagination Links -->
                        <div class="p-3 d-flex flex-column flex-md-row justify-content-between align-items-center border-top bg-white gap-2">
                            <small class="text-muted">
                                Showing {{ $studentRequests->firstItem() ?? 0 }} to {{ $studentRequests->lastItem() ?? 0 }} of {{ $studentRequests->total() }} requests
                            </small>
                            <div>
                                {{ $studentRequests->appends(request()->except('request_page'))->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. ALUMNI SUBMISSIONS PANE -->
        <div class="tab-pane fade" id="alumni-pane" role="tabpanel" aria-labelledby="alumni-tab" tabindex="0">
            <div class="card shadow-sm border-0 mb-5 border-top border-success border-3">
                
                <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <h5 class="mb-0 fw-bold text-secondary">Pending Alumni Submissions</h5>
                    
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
                        <!-- Alumni Search Bar -->
                        <div class="input-group input-group-sm" style="min-width: 220px;">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" id="alumniSearchInput" class="form-control border-start-0" placeholder="Type name or ID to search..." value="{{ request('alumni_search') }}">
                        </div>

                        <!-- Alumni Sport Filter -->
                        <div class="d-flex align-items-center gap-2">
                            <label for="alumniSportSelect" class="fw-bold text-muted mb-0 text-nowrap small">Sport:</label>
                            <select id="alumniSportSelect" class="form-select form-select-sm">
                                <option value="ALL">All Sports</option>
                                @foreach(\App\Models\Sport::orderBy('name', 'asc')->get() as $sport)
                                    <option value="{{ $sport->name }}" {{ request('alumni_sport') == $sport->name ? 'selected' : '' }}>
                                        {{ $sport->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0" id="alumni-content">
                    @if(!isset($alumniPendings) || $alumniPendings->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted fs-5">No pending alumni records found.</p>
                        </div>
                    @else
                        <div class="table-responsive w-100 overflow-x-auto">
                            <table class="table table-hover align-middle mb-0 w-100 text-nowrap" id="alumniTable">
                                <thead class="bg-light text-secondary">
                                    <tr>
                                        <th class="ps-4">Alumni Name</th>
                                        <th>Sport & Year Graduated</th>
                                        <th>Current Work</th>
                                        <th>Date Submitted</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alumniPendings as $alumni)
                                    <tr class="athlete-row">
                                        <td class="ps-4 fw-bold text-dark">{{ $alumni->last_name }}, {{ $alumni->first_name }} {{ $alumni->middle_initial }}</td>
                                        <td>
                                            <span class="badge bg-success px-2 py-1 mb-1 sport-cell">{{ str_replace('_', ' ', $alumni->sport_event) }}</span><br>
                                            <span class="text-muted small">Batch {{ $alumni->year_graduated ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $alumni->current_work ?? 'Not Specified' }}</span><br>
                                            <span class="text-muted small">{{ $alumni->current_company ?? 'N/A' }}</span>
                                        </td>
                                        <td class="text-secondary small">{{ $alumni->created_at->format('M d, Y') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <form action="{{ route('admin.approve.alumni', $alumni->id) }}" method="POST" class="m-0 ajax-action-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm px-3" onclick="return confirm('Approve this alumni record?')">
                                                        <i class="fas fa-check me-1"></i> Approve
                                                    </button>
                                                </form>
                                                <!-- Opens the Decline modal (a typed reason is required) -->
                                                <button type="button" class="btn btn-outline-danger btn-sm px-3"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#declineModal"
                                                        data-action="{{ route('admin.reject.alumni', $alumni->id) }}"
                                                        data-name="{{ $alumni->first_name }} {{ $alumni->last_name }}"
                                                        data-kind="Alumni submission">
                                                    <i class="fas fa-times me-1"></i> Reject
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Alumni Pagination Links -->
                        <div class="p-3 d-flex flex-column flex-md-row justify-content-between align-items-center border-top bg-white gap-2">
                            <small class="text-muted">
                                Showing {{ $alumniPendings->firstItem() ?? 0 }} to {{ $alumniPendings->lastItem() ?? 0 }} of {{ $alumniPendings->total() }} submissions
                            </small>
                            <div>
                                {{ $alumniPendings->appends(request()->except('alumni_page'))->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4. DECLINED PANE (rejected applications are kept on file with their reason) -->
        <div class="tab-pane fade" id="declined-pane" role="tabpanel" aria-labelledby="declined-tab" tabindex="0">
            <div class="card shadow-sm border-0 mb-5 border-top border-danger border-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-secondary">Recently Declined Applications</h5>
                    <small class="text-muted">Latest 10. Each record is kept on file with the reason it was declined.</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive w-100 overflow-x-auto">
                        <table class="table table-hover align-middle mb-0 w-100" id="declinedTable">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th class="ps-4">Applicant</th>
                                    <th>Type</th>
                                    <th>Sport</th>
                                    <th style="min-width: 260px;">Reason</th>
                                    <th>Date</th>
                                    <th>By</th>
                                </tr>
                            </thead>
                            <tbody id="declinedBody">
                                @forelse($declinedAthletes ?? [] as $d)
                                    @php
                                        $dType = $d->classification === 'Tryout' ? 'Tryout' : ($d->classification === 'Alumni' ? 'Alumni' : 'Student');
                                    @endphp
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark text-nowrap">{{ $d->last_name }}, {{ $d->first_name }} {{ $d->middle_initial }}</td>
                                        <td><span class="badge bg-secondary px-2 py-1">{{ $dType }}</span></td>
                                        <td><span class="badge bg-success px-2 py-1">{{ str_replace('_', ' ', $d->sport_event) }}</span></td>
                                        <td class="text-secondary" style="white-space: normal;">{{ $d->rejection_reason ?? 'No reason recorded' }}</td>
                                        <td class="text-secondary small text-nowrap">{{ $d->rejected_at ? $d->rejected_at->format('M d, Y') : 'N/A' }}</td>
                                        <td class="text-secondary small text-nowrap">{{ optional($d->rejecter)->name ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr id="declinedEmpty">
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-ban mb-2 d-block" style="font-size: 2rem;"></i>
                                            No declined applications yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- ATHLETE PROFILE MODAL -->
<!-- ========================================== -->
<div class="modal fade" id="athleteProfileModal" tabindex="-1" aria-labelledby="athleteProfileModalLabel" aria-hidden="true" data-bs-backdrop="false" style="background-color: rgba(0, 0, 0, 0.6); z-index: 105000;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header text-white" style="background-color: #2e4e1f;">
                <h5 class="modal-title fw-bold" id="athleteProfileModalLabel">
                    <i class="fas fa-user-circle me-2"></i> Athlete Profile Summary
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center py-5" id="modalLoading">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Retrieving profile...</p>
                </div>
                <div id="modalContent" class="d-none">
                    <div class="row">
                        <div class="col-md-4 text-center border-end">
                            <img id="modalPicture" src="" alt="Profile Picture" class="img-fluid rounded-circle mb-3 border shadow-sm" style="width: 150px; height: 150px; object-fit: cover; background-color: #f8f9fa;">
                            <h4 id="modalName" class="fw-bold text-dark mb-1"></h4>
                            <div class="mt-2">
                                <span id="modalSport" class="badge bg-success px-3 py-2 mb-2 w-100"></span><br>
                                <span id="modalClass" class="badge bg-secondary px-3 py-2 w-100"></span>
                            </div>
                        </div>
                        <div class="col-md-8 ps-md-4">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-success">Personal Information</h6>
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr><th class="text-muted w-25"><i class="fas fa-id-card me-2"></i>ID</th><td id="modalStudentId" class="fw-bold"></td></tr>
                                    <tr><th class="text-muted"><i class="fas fa-envelope me-2"></i>Email</th><td id="modalEmail"></td></tr>
                                    <tr><th class="text-muted"><i class="fas fa-phone me-2"></i>Contact</th><td id="modalContact"></td></tr>
                                    <tr><th class="text-muted"><i class="fas fa-graduation-cap me-2"></i>Course</th><td id="modalCourse"></td></tr>
                                    <tr><th class="text-muted"><i class="fas fa-birthday-cake me-2"></i>Birthdate</th><td id="modalBirthdate"></td></tr>
                                    <tr><th class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>Address</th><td id="modalAddress"></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SCHOLARSHIP APPROVAL CALCULATOR MODAL -->
<!-- ========================================== -->
<div class="modal fade" id="scholarshipModal" tabindex="-1" aria-labelledby="scholarshipModalLabel" aria-hidden="true" data-bs-backdrop="false" style="background-color: rgba(0, 0, 0, 0.6); z-index: 105000;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header text-white" style="background-color: #2e4e1f;">
                <h5 class="modal-title fw-bold" id="scholarshipModalLabel">
                    <i class="fas fa-calculator me-2"></i> Scholarship Assessment & Approval
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="approveAthleteForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <!-- Name Banner -->
                    <div class="alert alert-info border-info border-start border-4 bg-white shadow-sm mb-4">
                        <h5 class="fw-bold mb-0 text-dark" id="calcAthleteName">Athlete Name</h5>
                    </div>

                    <!-- Classification Dropdown -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-success"><i class="fas fa-star me-1"></i> Set Scholarship Classification <span class="text-danger">*</span></label>
                            <select name="classification" id="calcClassification" class="form-select form-select-lg fw-bold shadow-sm calc-trigger" style="border-color: #4F6228;" required>
                                <option value="Regular">Regular (No Discount)</option>
                                <option value="Class_A">Class A (100% Tuition + 100% Misc)</option>
                                <option value="Class_B">Class B (100% Tuition Only)</option>
                                <option value="Class_C">Class C (75% Tuition Only)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary">Academic Year <span class="text-danger">*</span></label>
                            <input type="text" name="academic_year" class="form-control" placeholder="e.g. 2026-2027" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary">Total Units</label>
                            <input type="number" name="total_units" class="form-control" placeholder="0">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-secondary">Tuition Fee (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="tuition_fee" id="calcTuition" class="form-control calc-trigger" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-secondary">Misc Fee (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="miscellaneous_fee" id="calcMisc" class="form-control calc-trigger" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-secondary">Other Charges (₱)</label>
                            <input type="number" step="0.01" name="other_charges" id="calcOther" class="form-control calc-trigger" placeholder="0.00">
                        </div>
                    </div>

                    <hr class="text-muted">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark">Total Assessment</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">₱</span>
                                <input type="text" name="total_assessment" id="calcTotalAssess" class="form-control bg-white text-dark fw-bold" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-success">Total Discount</label>
                            <div class="input-group">
                                <span class="input-group-text bg-success text-white">₱</span>
                                <input type="text" name="total_discount" id="calcTotalDiscount" class="form-control bg-white text-success fw-bold" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-danger">Net Payable</label>
                            <div class="input-group">
                                <span class="input-group-text bg-danger text-white">₱</span>
                                <input type="text" id="calcNetPayable" class="form-control bg-white text-danger fw-bold" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0">
                    <button type="button" class="btn btn-outline-secondary fw-bold px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4"><i class="fas fa-check-circle me-1"></i> Approve & Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- DECLINE / REJECT MODAL (typed reason required) -->
<!-- ========================================== -->
<div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true" data-bs-backdrop="false" style="background-color: rgba(0, 0, 0, 0.6); z-index: 105000;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header text-white" style="background-color: #b02a37;">
                <h5 class="modal-title fw-bold" id="declineModalLabel">
                    <i class="fas fa-ban me-2"></i> Decline Application
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="declineForm" method="POST" action="" novalidate>
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="alert alert-light border-start border-danger border-4 shadow-sm mb-3 py-2">
                        <div class="small text-muted" id="declineKind">Application</div>
                        <div class="fw-bold text-dark fs-5" id="declineName">Applicant Name</div>
                    </div>

                    <label for="declineReason" class="form-label fw-bold text-secondary">
                        Reason for declining <span class="text-danger">*</span>
                    </label>
                    <textarea id="declineReason" name="rejection_reason" class="form-control" rows="4" minlength="5" maxlength="1000" placeholder="Type the reason (e.g. Incomplete requirements, did not meet the tryout standards, duplicate record...)" required></textarea>
                    <div class="d-flex justify-content-between mt-1">
                        <div class="text-danger small fw-bold d-none" id="declineError"></div>
                        <small class="text-muted ms-auto" id="declineCounter">0 / 1000</small>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle me-1"></i> This reason is saved on the record and written to the Security Ledger.
                    </small>
                </div>
                <div class="modal-footer bg-white border-top-0">
                    <button type="button" class="btn btn-outline-secondary fw-bold px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="declineSubmitBtn" class="btn btn-danger fw-bold px-4">
                        <i class="fas fa-ban me-1"></i> Confirm Decline
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(elementId) {
        var copyText = document.getElementById(elementId);
        copyText.select();
        copyText.setSelectionRange(0, 99999); 
        navigator.clipboard.writeText(copyText.value);
        let linkType = elementId === 'regLink' ? 'Tryout Registration' : 'Alumni Form';
        alert(linkType + " link copied!");
    }

    function escapeHtml(value) {
        return String(value === null || value === undefined ? '' : value).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    const currentAdminName = @json(auth()->user()->name ?? '');

    let searchTimeouts = {};

    function fetchTabContent(targetContainerId, params) {
        const targetContainer = document.getElementById(targetContainerId);
        if (!targetContainer) return;

        targetContainer.style.opacity = '0.4';

        const url = new URL(window.location.origin + window.location.pathname);
        Object.keys(params).forEach(key => {
            if (params[key] && params[key] !== 'ALL') {
                url.searchParams.set(key, params[key]);
            }
        });

        fetch(url.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.getElementById(targetContainerId);

            if (newContent) {
                targetContainer.innerHTML = newContent.innerHTML;
            }
            targetContainer.style.opacity = '1';
        })
        .catch(error => {
            console.error('AJAX Fetch Error:', error);
            targetContainer.style.opacity = '1';
        });
    }

    function triggerLiveSearch(containerId, paramsKey, searchTerm, sportTerm) {
        clearTimeout(searchTimeouts[containerId]);
        searchTimeouts[containerId] = setTimeout(() => {
            const params = {};
            if (containerId === 'tryouts-content') {
                params['tryout_search'] = searchTerm;
                params['tryout_sport'] = sportTerm;
                params['tryout_page'] = 1;
            } else if (containerId === 'requests-content') {
                params['request_search'] = searchTerm;
                params['request_sport'] = sportTerm;
                params['request_page'] = 1;
            } else if (containerId === 'alumni-content') {
                params['alumni_search'] = searchTerm;
                params['alumni_sport'] = sportTerm;
                params['alumni_page'] = 1;
            }
            fetchTabContent(containerId, params);
        }, 300);
    }

    /**
     * Fades a table row out, removes it, and lowers the "N Pending" badge of the
     * main tab it belongs to. Shows an "all caught up" message when the queue is empty.
     */
    function removeRowAndUpdateBadge(tableRow) {
        if (!tableRow) return;

        const mainPane  = tableRow.closest('#approvalTabsContent > .tab-pane');
        const tbody     = tableRow.parentElement;
        const tableWrap = tableRow.closest('.table-responsive');

        tableRow.style.transition = 'all 0.4s ease';
        tableRow.style.opacity = '0';
        tableRow.style.transform = 'translateX(30px)';

        setTimeout(() => {
            tableRow.remove();
            if (!mainPane) return;

            const badge = document.getElementById(mainPane.id.replace('-pane', '-badge'));
            if (!badge) return;

            const match = badge.innerText.match(/\d+/);
            if (!match) return;

            const newCount = parseInt(match[0], 10) - 1;
            if (newCount > 0) {
                badge.innerText = newCount + ' Pending';
                return;
            }

            badge.style.display = 'none';
            if (tbody && tbody.children.length === 0 && tableWrap) {
                tableWrap.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted fs-5">All caught up! No more pending records.</p>
                    </div>
                `;
            }
        }, 400);
    }

    /** Adds a freshly declined application to the top of the Declined tab. */
    function addDeclinedRow(info) {
        const body = document.getElementById('declinedBody');
        if (!body || !info) return;

        const empty = document.getElementById('declinedEmpty');
        if (empty) empty.remove();

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="ps-4 fw-bold text-dark text-nowrap">${escapeHtml(info.name)}</td>
            <td><span class="badge bg-secondary px-2 py-1">${escapeHtml(info.type)}</span></td>
            <td><span class="badge bg-success px-2 py-1">${escapeHtml(info.sport)}</span></td>
            <td class="text-secondary" style="white-space: normal;">${escapeHtml(info.reason)}</td>
            <td class="text-secondary small text-nowrap">${escapeHtml(info.date)}</td>
            <td class="text-secondary small text-nowrap">${escapeHtml(currentAdminName || 'N/A')}</td>
        `;
        body.prepend(tr);

        const badge = document.getElementById('declined-badge');
        if (badge) {
            const current = parseInt(badge.innerText, 10) || 0;
            badge.innerText = current + 1;
            badge.style.display = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        
        // 1. Setup Live Search Listeners
        const tryoutSearch = document.getElementById('tryoutSearchInput');
        const tryoutSport = document.getElementById('tryoutSportSelect');
        if (tryoutSearch && tryoutSport) {
            tryoutSearch.addEventListener('input', () => triggerLiveSearch('tryouts-content', 'tryout', tryoutSearch.value.trim(), tryoutSport.value));
            tryoutSport.addEventListener('change', () => triggerLiveSearch('tryouts-content', 'tryout', tryoutSearch.value.trim(), tryoutSport.value));
        }

        const reqSearch = document.getElementById('requestSearchInput');
        const reqSport = document.getElementById('requestSportSelect');
        if (reqSearch && reqSport) {
            reqSearch.addEventListener('input', () => triggerLiveSearch('requests-content', 'request', reqSearch.value.trim(), reqSport.value));
            reqSport.addEventListener('change', () => triggerLiveSearch('requests-content', 'request', reqSearch.value.trim(), reqSport.value));
        }

        const alumniSearch = document.getElementById('alumniSearchInput');
        const alumniSport = document.getElementById('alumniSportSelect');
        if (alumniSearch && alumniSport) {
            alumniSearch.addEventListener('input', () => triggerLiveSearch('alumni-content', 'alumni', alumniSearch.value.trim(), alumniSport.value));
            alumniSport.addEventListener('change', () => triggerLiveSearch('alumni-content', 'alumni', alumniSearch.value.trim(), alumniSport.value));
        }

        // 2. AJAX Pagination Link Click Interceptor
        document.addEventListener('click', function (e) {
            const pageLink = e.target.closest('.pagination a');
            if (!pageLink) return;

            e.preventDefault();
            const url = pageLink.getAttribute('href');
            if (!url || url === '#') return;

            const activePane = pageLink.closest('.tab-pane.show.active') || pageLink.closest('.tab-pane');
            if (!activePane) return;

            const paneId = activePane.id;
            let contentContainerId = paneId.replace('-pane', '-content');
            
            // Handle new sub-tab pagination mappings
            if(paneId === 'sub-pending') contentContainerId = 'tryouts-content';
            if(paneId === 'sub-schedules') contentContainerId = 'sub-schedules';
            if(paneId === 'sub-recruits') contentContainerId = 'sub-recruits';

            const targetContainer = document.getElementById(contentContainerId);
            if (!targetContainer) {
                window.location.href = url;
                return;
            }

            targetContainer.style.opacity = '0.4';

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById(contentContainerId);

                if (newContent) {
                    targetContainer.innerHTML = newContent.innerHTML;
                }
                targetContainer.style.opacity = '1';
                activePane.scrollIntoView({ behavior: 'smooth', block: 'start' });
            })
            .catch(error => {
                console.error('Pagination load error:', error);
                targetContainer.style.opacity = '1';
                window.location.href = url;
            });
        });

        // 3. Tab Styling Toggle (Main Tabs)
        const tabs = document.querySelectorAll('#approvalTabs button[data-bs-toggle="tab"]');
        tabs.forEach(tab => {
            tab.addEventListener('shown.bs.tab', event => {
                tabs.forEach(t => { 
                    t.style.color = ''; 
                    t.classList.remove('text-dark'); 
                    t.classList.add('text-secondary'); 
                });
                event.target.classList.remove('text-secondary');
                event.target.style.color = '#2e4e1f'; 
            });
        });

        // 4. Profile Summary Modal Loader
        const profileModalEl = document.getElementById('athleteProfileModal');
        if (profileModalEl) {
            profileModalEl.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const athleteId = button.getAttribute('data-id');
                if (!athleteId) return;

                const loadingEl = document.getElementById('modalLoading');
                const contentEl = document.getElementById('modalContent');
                
                loadingEl.classList.remove('d-none');
                contentEl.classList.add('d-none');
                
                loadingEl.innerHTML = `
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Retrieving profile...</p>
                `;

                fetch(`/admin/approvals/${athleteId}/view`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Backend returned ' + response.status);
                    return response.json();
                })
                .then(data => {
                    let mi = data.middle_initial ? ' ' + String(data.middle_initial) : '';
                    document.getElementById('modalName').innerText = `${data.last_name || ''}, ${data.first_name || ''}${mi}`;
                    
                    document.getElementById('modalSport').innerText = data.sport_event ? String(data.sport_event).replace(/_/g, ' ') : 'N/A';
                    document.getElementById('modalClass').innerText = data.classification ? String(data.classification).replace(/_/g, ' ') : 'N/A';
                    document.getElementById('modalStudentId').innerText = data.student_id || 'N/A';
                    document.getElementById('modalEmail').innerText = data.email || 'N/A';
                    document.getElementById('modalContact').innerText = data.contact_number || 'N/A';
                    
                    let courseText = data.course ? String(data.course).trim() : '';
                    let yearText = data.year_level ? String(data.year_level).trim() : '';
                    let courseYr = courseText;
                    if (yearText) {
                        courseYr += (/^year/i.test(yearText)) ? ` — ${yearText}` : ` — Year ${yearText}`;
                    }
                    document.getElementById('modalCourse').innerText = courseYr || 'N/A';
                    
                    let formattedDate = 'N/A';
                    if (data.birthdate) {
                        let datePart = String(data.birthdate).split('T')[0];
                        let d = new Date(datePart);
                        formattedDate = (!isNaN(d.getTime())) ? d.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : datePart;
                    }
                    document.getElementById('modalBirthdate').innerText = formattedDate;
                    
                    let addr = data.address || '';
                    if (data.city_municipality) addr += `, ${data.city_municipality}`;
                    if (data.province_state) addr += `, ${data.province_state}`;
                    document.getElementById('modalAddress').innerText = addr || 'N/A';

                    if (data.picture_path) {
                        document.getElementById('modalPicture').src = `/storage/${data.picture_path}`;
                    } else {
                        document.getElementById('modalPicture').src = `https://ui-avatars.com/api/?name=${data.first_name || 'User'}+${data.last_name || ''}&background=e8f5e9&color=2e4e1f&size=150`;
                    }

                    loadingEl.classList.add('d-none');
                    contentEl.classList.remove('d-none');
                })
                .catch(error => {
                    console.error('Error fetching profile:', error);
                    loadingEl.innerHTML = `
                        <i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 3rem;"></i>
                        <p class="text-danger fs-5 fw-bold">Failed to load profile.</p>
                        <p class="text-muted small">The database record might be incomplete or missing.</p>
                    `;
                });
            });
        }

        // 5. Scholarship Approval Calculator Modal Loader
        function calculateFees() {
            let tuition = parseFloat(document.getElementById('calcTuition').value) || 0;
            let misc = parseFloat(document.getElementById('calcMisc').value) || 0;
            let other = parseFloat(document.getElementById('calcOther').value) || 0;
            let classification = document.getElementById('calcClassification').value;

            let totalAssessment = tuition + misc + other;
            let totalDiscount = 0;

            if (classification === 'Class_A') {
                totalDiscount = tuition + misc; 
            } else if (classification === 'Class_B') {
                totalDiscount = tuition; 
            } else if (classification === 'Class_C') {
                totalDiscount = tuition * 0.75; 
            }

            let netPayable = totalAssessment - totalDiscount;

            document.getElementById('calcTotalAssess').value = totalAssessment.toFixed(2);
            document.getElementById('calcTotalDiscount').value = totalDiscount.toFixed(2);
            document.getElementById('calcNetPayable').value = netPayable.toFixed(2);
        }

        const calcTriggers = document.querySelectorAll('.calc-trigger');
        if (calcTriggers.length > 0) {
            calcTriggers.forEach(item => {
                item.addEventListener('input', calculateFees);
                item.addEventListener('change', calculateFees);
            });
        }

        const scholarshipModalEl = document.getElementById('scholarshipModal');
        if (scholarshipModalEl) {
            scholarshipModalEl.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const athleteId = button.getAttribute('data-id');
                const athleteName = button.getAttribute('data-name');
                const classification = button.getAttribute('data-classification');

                let form = document.getElementById('approveAthleteForm');
                form.action = `/admin/approvals/${athleteId}/approve`; 
                
                document.getElementById('calcAthleteName').innerText = athleteName;
                
                let cleanClass = classification ? classification.trim().toUpperCase() : '';
                let selectElement = document.getElementById('calcClassification');
                
                if (cleanClass.includes('A')) selectElement.value = 'Class_A';
                else if (cleanClass.includes('B')) selectElement.value = 'Class_B';
                else if (cleanClass.includes('C')) selectElement.value = 'Class_C';
                else selectElement.value = 'Regular'; 

                document.getElementById('calcTuition').value = '';
                document.getElementById('calcMisc').value = '';
                document.getElementById('calcOther').value = '';
                
                calculateFees(); 
            });
        }

        // 6. AJAX Form Submission for Single-Click Buttons (Passed / Approve Alumni)
        document.addEventListener('submit', function(e) {
            if (e.target && e.target.classList.contains('ajax-action-form')) {
                e.preventDefault(); 

                let formElement = e.target;
                let tableRow = formElement.closest('tr');
                if (!tableRow) return;

                let submitBtn = formElement.querySelector('button[type="submit"]');
                let originalHtml = submitBtn.innerHTML;

                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                submitBtn.disabled = true;

                fetch(formElement.action, {
                    method: formElement.method,
                    body: new FormData(formElement),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => {
                    if (response.ok) {
                        removeRowAndUpdateBadge(tableRow);
                    } else {
                        alert('Something went wrong on the server. Please try again.');
                        submitBtn.innerHTML = originalHtml;
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('AJAX Action Error:', error);
                    alert('Network error occurred.');
                    submitBtn.innerHTML = originalHtml;
                    submitBtn.disabled = false;
                });
            }
        });

        // 7. Decline / Reject modal (a typed reason is required)
        const declineModalEl = document.getElementById('declineModal');
        const declineForm    = document.getElementById('declineForm');
        const declineReason  = document.getElementById('declineReason');
        const declineError   = document.getElementById('declineError');
        const declineCounter = document.getElementById('declineCounter');
        const declineSubmit  = document.getElementById('declineSubmitBtn');
        let declineRow = null;

        function showDeclineError(message) {
            declineError.textContent = message;
            declineError.classList.remove('d-none');
            declineReason.classList.add('is-invalid');
        }

        function clearDeclineError() {
            declineError.textContent = '';
            declineError.classList.add('d-none');
            declineReason.classList.remove('is-invalid');
        }

        function updateDeclineCounter() {
            declineCounter.textContent = declineReason.value.length + ' / 1000';
        }

        if (declineModalEl && declineForm) {
            declineModalEl.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return;

                declineForm.action = button.getAttribute('data-action');
                document.getElementById('declineName').innerText = button.getAttribute('data-name') || 'Applicant';
                document.getElementById('declineKind').innerText = button.getAttribute('data-kind') || 'Application';

                declineRow = button.closest('tr');
                declineReason.value = '';
                clearDeclineError();
                updateDeclineCounter();

                declineSubmit.disabled = false;
                declineSubmit.innerHTML = '<i class="fas fa-ban me-1"></i> Confirm Decline';
            });

            declineModalEl.addEventListener('shown.bs.modal', function () {
                declineReason.focus();
            });

            declineReason.addEventListener('input', function () {
                updateDeclineCounter();
                if (declineReason.value.trim().length >= 5) clearDeclineError();
            });

            declineForm.addEventListener('submit', function (e) {
                e.preventDefault();

                if (declineReason.value.trim().length < 5) {
                    showDeclineError('Please type a reason (at least 5 characters).');
                    declineReason.focus();
                    return;
                }

                clearDeclineError();
                declineSubmit.disabled = true;
                declineSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

                fetch(declineForm.action, {
                    method: 'POST',
                    body: new FormData(declineForm),
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));

                    if (response.ok && data.success) {
                        // Close the modal, remove the row, add it to the Declined tab
                        declineModalEl.querySelector('[data-bs-dismiss="modal"]').click();
                        removeRowAndUpdateBadge(declineRow);
                        addDeclinedRow(data.rejected);
                        return;
                    }

                    if (response.status === 422 && data.errors && data.errors.rejection_reason) {
                        showDeclineError(data.errors.rejection_reason[0]);
                    } else {
                        showDeclineError(data.message || 'Something went wrong on the server. Please try again.');
                    }
                    declineSubmit.disabled = false;
                    declineSubmit.innerHTML = '<i class="fas fa-ban me-1"></i> Confirm Decline';
                })
                .catch(error => {
                    console.error('Decline error:', error);
                    showDeclineError('Network error occurred. Please try again.');
                    declineSubmit.disabled = false;
                    declineSubmit.innerHTML = '<i class="fas fa-ban me-1"></i> Confirm Decline';
                });
            });
        }
    });
</script>

@endsection