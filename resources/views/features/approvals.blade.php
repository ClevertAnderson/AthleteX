@extends('layouts.app') 

@section('content')

@include('partials.sidebar')

<div class="bg-light min-vh-100 p-4">

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
                @if(isset($tryoutPendings) && $tryoutPendings->isNotEmpty())
                    <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.75rem;">{{ $tryoutPendings->count() }} Pending</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-3 text-secondary d-flex align-items-center gap-2" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests-pane" type="button" role="tab" aria-controls="requests-pane" aria-selected="false">
                <span><i class="fas fa-file-signature me-2"></i> Student Requests</span>
                @if(isset($studentRequests) && $studentRequests->isNotEmpty())
                    <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.75rem;">{{ $studentRequests->count() }} Pending</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-3 text-secondary d-flex align-items-center gap-2" id="alumni-tab" data-bs-toggle="tab" data-bs-target="#alumni-pane" type="button" role="tab" aria-controls="alumni-pane" aria-selected="false">
                <span><i class="fas fa-graduation-cap me-2"></i> Alumni Submissions</span>
                @if(isset($alumniPendings) && $alumniPendings->isNotEmpty())
                    <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.75rem;">{{ $alumniPendings->count() }} Pending</span>
                @endif
            </button>
        </li>
    </ul>

    <!-- ========================================== -->
    <!-- TABS CONTENT -->
    <!-- ========================================== -->
    <div class="tab-content" id="approvalTabsContent">
        
        <!-- 1. TRYOUT APPLICANTS PANE -->
        <div class="tab-pane fade show active" id="tryouts-pane" role="tabpanel" aria-labelledby="tryouts-tab" tabindex="0">
            <div class="card shadow-sm border-0 mb-5 border-top border-success border-3">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-secondary">Pending Applicants</h5>
                    <div class="d-flex align-items-center gap-2">
                        <label for="tryoutFilter" class="fw-bold text-muted mb-0" style="white-space: nowrap;">Filter Sport:</label>
                        <select id="tryoutFilter" class="form-select form-select-sm" style="width: 200px;" onchange="filterTable('tryoutsTable', this.value)">
                            <option value="ALL">All Sports</option>
                            @foreach(\App\Models\Sport::orderBy('name', 'asc')->get() as $sport)
                                <option value="{{ strtoupper(str_replace('_', ' ', $sport->name)) }}">{{ $sport->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if(!isset($tryoutPendings) || $tryoutPendings->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted fs-5">No tryout applicants pending.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 w-100" id="tryoutsTable">
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
                                                <button type="button" onclick="viewProfile({{ $p->id }})" class="btn btn-info btn-sm px-3 text-white" title="View Profile">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </button>
                                                <form action="{{ route('admin.approve.athlete', $p->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm px-3" onclick="return confirm('Did {{ $p->first_name }} pass the tryouts? This will make them an Active athlete.')">
                                                        <i class="fas fa-trophy me-1"></i> Passed
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.reject.athlete', $p->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger btn-sm px-3" onclick="return confirm('Did they fail/not show up? This will remove their record.')">
                                                        <i class="fas fa-times me-1"></i> Failed
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. STUDENT REQUESTS PANE -->
        <div class="tab-pane fade" id="requests-pane" role="tabpanel" aria-labelledby="requests-tab" tabindex="0">
            <div class="card shadow-sm border-0 mb-5 border-top border-success border-3">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-secondary">Coach Requests</h5>
                    <div class="d-flex align-items-center gap-2">
                        <label for="requestFilter" class="fw-bold text-muted mb-0" style="white-space: nowrap;">Filter Sport:</label>
                        <select id="requestFilter" class="form-select form-select-sm" style="width: 200px;" onchange="filterTable('requestsTable', this.value)">
                            <option value="ALL">All Sports</option>
                            @foreach(\App\Models\Sport::orderBy('name', 'asc')->get() as $sport)
                                <option value="{{ strtoupper(str_replace('_', ' ', $sport->name)) }}">{{ $sport->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if(!isset($studentRequests) || $studentRequests->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-check text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted fs-5">No pending student requests from coaches.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 w-100" id="requestsTable">
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
                                                <button type="button" onclick="viewProfile({{ $req->id }})" class="btn btn-info btn-sm px-3 text-white" title="View Profile">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </button>
                                                <form action="{{ route('admin.approve.athlete', $req->id) }}" method="POST">
                                                    @csrf
                                                    <button type="button" class="btn btn-success btn-sm px-3" 
                                                            onclick="openApprovalModal({{ $req->id }}, '{{ addslashes($req->first_name . ' ' . $req->last_name) }}', '{{ $req->classification }}')">
                                                        <i class="fas fa-check me-1"></i> Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.reject.athlete', $req->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger btn-sm px-3" onclick="return confirm('Reject and delete this entry?')">
                                                        <i class="fas fa-trash me-1"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. ALUMNI SUBMISSIONS PANE -->
        <div class="tab-pane fade" id="alumni-pane" role="tabpanel" aria-labelledby="alumni-tab" tabindex="0">
            <div class="card shadow-sm border-0 mb-5 border-top border-success border-3">
                
                <!-- 🚀 NEW ALUMNI SPORT FILTER -->
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-secondary">Pending Alumni Submissions</h5>
                    <div class="d-flex align-items-center gap-2">
                        <label for="alumniFilter" class="fw-bold text-muted mb-0" style="white-space: nowrap;">Filter Sport:</label>
                        <select id="alumniFilter" class="form-select form-select-sm" style="width: 200px;" onchange="filterTable('alumniTable', this.value)">
                            <option value="ALL">All Sports</option>
                            @foreach(\App\Models\Sport::orderBy('name', 'asc')->get() as $sport)
                                <option value="{{ strtoupper(str_replace('_', ' ', $sport->name)) }}">{{ $sport->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if(!isset($alumniPendings) || $alumniPendings->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted fs-5">No pending alumni forms to approve.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 w-100" id="alumniTable">
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
                                                <form action="{{ route('admin.approve.alumni', $alumni->id) }}" method="POST">
                                                    @csrf
                                                    <button type="button" class="btn btn-success btn-sm px-3" 
                                                            onclick="openApprovalModal({{ $req->id }}, '{{ addslashes($req->first_name . ' ' . $req->last_name) }}', '{{ $req->classification }}')">
                                                        <i class="fas fa-check me-1"></i> Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.reject.alumni', $alumni->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger btn-sm px-3" onclick="return confirm('Delete this alumni submission?')">
                                                        <i class="fas fa-trash me-1"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
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

<script>
    function filterTable(tableId, filterValue) {
        const table = document.getElementById(tableId);
        if (!table) return;
        const rows = table.getElementsByClassName('athlete-row');
        const filter = filterValue.toUpperCase();
        for (let i = 0; i < rows.length; i++) {
            const sportBadge = rows[i].querySelector('.sport-cell');
            if (sportBadge) {
                const sportText = sportBadge.textContent || sportBadge.innerText;
                if (filter === 'ALL' || sportText.toUpperCase().includes(filter)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
    }

    function copyToClipboard(elementId) {
        var copyText = document.getElementById(elementId);
        copyText.select();
        copyText.setSelectionRange(0, 99999); 
        navigator.clipboard.writeText(copyText.value);
        let linkType = elementId === 'regLink' ? 'Tryout Registration' : 'Alumni Form';
        alert(linkType + " link copied! You can now paste it in Messenger or Email.");
    }

    // Toggle styling on tabs
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('button[data-bs-toggle="tab"]');
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
    });

    let athleteModal;
    document.addEventListener("DOMContentLoaded", function() {
        athleteModal = new bootstrap.Modal(document.getElementById('athleteProfileModal'));
    });

    function viewProfile(athleteId) {
        athleteModal.show();
        document.getElementById('modalLoading').classList.remove('d-none');
        document.getElementById('modalContent').classList.add('d-none');

        fetch(`/admin/approvals/${athleteId}/view`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            // Updated dynamically format name in the modal popup
            let mi = data.middle_initial ? ' ' + data.middle_initial : '';
            document.getElementById('modalName').innerText = `${data.last_name}, ${data.first_name}${mi}`;
            
            document.getElementById('modalSport').innerText = data.sport_event ? data.sport_event.replace('_', ' ') : 'N/A';
            document.getElementById('modalClass').innerText = data.classification ? data.classification.replace('_', ' ') : 'N/A';
            document.getElementById('modalStudentId').innerText = data.student_id || 'N/A';
            document.getElementById('modalEmail').innerText = data.email || 'N/A';
            document.getElementById('modalContact').innerText = data.contact_number || 'N/A';
            
            let courseYr = data.course || '';
            if (data.year_level) courseYr += ` - Year ${data.year_level}`;
            document.getElementById('modalCourse').innerText = courseYr || 'N/A';
            document.getElementById('modalBirthdate').innerText = data.birthdate || 'N/A';
            
            let addr = data.address || '';
            if (data.city_municipality) addr += `, ${data.city_municipality}`;
            if (data.province_state) addr += `, ${data.province_state}`;
            document.getElementById('modalAddress').innerText = addr || 'N/A';

            if (data.picture_path) {
                document.getElementById('modalPicture').src = `/storage/${data.picture_path}`;
            } else {
                document.getElementById('modalPicture').src = `https://ui-avatars.com/api/?name=${data.first_name}+${data.last_name}&background=e8f5e9&color=2e4e1f&size=150`;
            }

            document.getElementById('modalLoading').classList.add('d-none');
            document.getElementById('modalContent').classList.remove('d-none');
        })
        .catch(error => {
            console.error('Error fetching profile:', error);
            alert('Failed to load profile data.');
            athleteModal.hide();
        });
    }
</script>

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

<script>
    function openApprovalModal(athleteId, athleteName, classification) {
        let form = document.getElementById('approveAthleteForm');
        form.action = `/admin/approvals/${athleteId}/approve`; 
        
        document.getElementById('calcAthleteName').innerText = athleteName;
        
        // Auto-select dropdown using the exact "Class_X" values
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

        var myModal = new bootstrap.Modal(document.getElementById('scholarshipModal'));
        myModal.show();
    }

    function calculateFees() {
        let tuition = parseFloat(document.getElementById('calcTuition').value) || 0;
        let misc = parseFloat(document.getElementById('calcMisc').value) || 0;
        let other = parseFloat(document.getElementById('calcOther').value) || 0;
        let classification = document.getElementById('calcClassification').value;

        let totalAssessment = tuition + misc + other;
        let totalDiscount = 0;

        // Apply rules using exact "Class_X" values
        if (classification === 'Class_A') {
            totalDiscount = tuition + misc; 
        } else if (classification === 'Class_B') {
            totalDiscount = tuition; 
        } else if (classification === 'Class_C') {
            totalDiscount = tuition * 0.75; 
        } else {
            totalDiscount = 0;
        }

        let netPayable = totalAssessment - totalDiscount;

        document.getElementById('calcTotalAssess').value = totalAssessment.toFixed(2);
        document.getElementById('calcTotalDiscount').value = totalDiscount.toFixed(2);
        document.getElementById('calcNetPayable').value = netPayable.toFixed(2);
    }

    document.querySelectorAll('.calc-trigger').forEach(item => {
        item.addEventListener('input', calculateFees);
        item.addEventListener('change', calculateFees);
    });
</script>

@endsection