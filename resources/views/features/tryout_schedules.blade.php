@extends('layouts.app') <!-- Adjust this to your main layout name, e.g., 'layouts.admin' -->

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-success mb-0" style="color: #2e4e1f !important;">
            <i class="fas fa-calendar-alt me-2"></i>Tryout Schedules & Recruits
        </h2>
    </div>

    <!-- Alert Messages (Success/Error) -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs border-bottom-0 mb-3" id="tryoutTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold text-success border-success border-bottom-0" style="color: #2e4e1f;" id="schedules-tab" data-bs-toggle="tab" data-bs-target="#schedules" type="button" role="tab" aria-controls="schedules" aria-selected="true">
                <i class="fas fa-calendar-day me-2"></i>Schedules
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-secondary border-0" id="recruits-tab" data-bs-toggle="tab" data-bs-target="#recruits" type="button" role="tab" aria-controls="recruits" aria-selected="false">
                <i class="fas fa-users me-2"></i>Passed Recruits
            </button>
        </li>
    </ul>

    <div class="tab-content" id="tryoutTabsContent">
        <!-- ========================================== -->
        <!-- 1. SCHEDULES PANE -->
        <!-- ========================================== -->
        <div class="tab-pane fade show active" id="schedules" role="tabpanel" aria-labelledby="schedules-tab">
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
                                    <label for="sport_event" class="form-label fw-bold text-muted small">Sport Event</label>
                                    <select class="form-select" id="sport_event" name="sport_event" required>
                                        <option value="" disabled selected>Select a Sport</option>
                                        <option value="Basketball">Basketball</option>
                                        <option value="Volleyball">Volleyball</option>
                                        <option value="Football">Football</option>
                                        <option value="Badminton">Badminton</option>
                                        <option value="Table_Tennis">Table Tennis</option>
                                        <option value="Athletics">Athletics</option>
                                        <option value="Chess">Chess</option>
                                        <option value="E_Sports">E-Sports</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="tryout_date" class="form-label fw-bold text-muted small">Date</label>
                                    <input type="date" class="form-control" id="tryout_date" name="tryout_date" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tryout_time" class="form-label fw-bold text-muted small">Time</label>
                                    <input type="time" class="form-control" id="tryout_time" name="tryout_time" required>
                                </div>
                                <div class="mb-3">
                                    <label for="venue" class="form-label fw-bold text-muted small">Venue</label>
                                    <input type="text" class="form-control" id="venue" name="venue" placeholder="e.g. University Gym" required>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label fw-bold text-muted small">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="Active" selected>Active</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="notes" class="form-label fw-bold text-muted small">Notes (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any specific requirements..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success w-100 fw-bold" style="background-color: #2e4e1f; border-color: #2e4e1f;">
                                    <i class="fas fa-paper-plane me-1"></i> Post Schedule
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Schedules List Table -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm border-0 border-top border-success border-4">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-secondary">Tryout Schedules List</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive w-100 overflow-x-auto">
                                <table class="table table-hover align-middle mb-0 w-100 text-nowrap">
                                    <thead class="bg-light text-secondary">
                                        <tr>
                                            <th class="ps-4">Sport / Status</th>
                                            <th>Date & Time</th>
                                            <th>Venue & Notes</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($schedules ?? [] as $schedule)
                                        <tr>
                                            <td class="ps-4">
                                                <span class="badge bg-success px-2 py-1 mb-1">{{ str_replace('_', ' ', $schedule->sport_event) }}</span><br>
                                                @if($schedule->status == 'Active')
                                                    <span class="badge bg-primary px-2 py-1 small">Active</span>
                                                @else
                                                    <span class="badge bg-secondary px-2 py-1 small">Completed</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($schedule->tryout_date)->format('M d, Y') }}</span><br>
                                                <span class="text-muted small"><i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($schedule->tryout_time)->format('h:i A') }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-dark">{{ $schedule->venue }}</span>
                                                @if($schedule->notes)
                                                    <br><span class="text-muted small text-wrap" style="max-width: 250px; display: inline-block;">{{ Str::limit($schedule->notes, 40) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <form action="{{ route('admin.tryouts.destroy', $schedule->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm px-3" title="Delete Schedule">
                                                            <i class="fas fa-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="fas fa-calendar-times mb-3" style="font-size: 2.5rem;"></i><br>
                                                <span class="fs-6">No tryout schedules have been posted yet.</span>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination for Schedules -->
                            @if(isset($schedules) &&$schedules->hasPages())
                            <div class="p-3 border-top bg-white">
                                {{ $schedules->links('pagination::bootstrap-5') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 2. PASSED RECRUITS PANE -->
        <!-- ========================================== -->
        <div class="tab-pane fade" id="recruits" role="tabpanel" aria-labelledby="recruits-tab">
            <div class="card shadow-sm border-0 border-top border-success border-4 mb-5">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-secondary">Passed Recruits Overview</h5>
                </div>
                <div class="card-body p-0">
                    @if(!isset($recruits) \vert{}\vert{}$recruits->isEmpty())
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
                                    @foreach($recruits as$recruit)
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark">{{ $recruit->last_name }}, {{ $recruit->first_name }} {{$recruit->middle_initial }}</td>
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

                        <!-- Pagination for Recruits -->
                        @if($recruits->hasPages())
                        <div class="p-3 d-flex flex-column flex-md-row justify-content-between align-items-center border-top bg-white gap-2">
                            <small class="text-muted">
                                Showing {{ $recruits->firstItem() ?? 0 }} to {{ $recruits->lastItem() ?? 0 }} of {{$recruits->total() }} recruits
                            </small>
                            <div>
                                {{ $recruits->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                        @endif
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
        <div class="modal-content shadow-lg border-0">
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
                        <!-- Left Side: Picture & Badges -->
                        <div class="col-md-4 text-center border-end">
                            <img id="modalPicture" src="" alt="Profile Picture" class="img-fluid rounded-circle mb-3 border shadow-sm" style="width: 150px; height: 150px; object-fit: cover; background-color: #f8f9fa;">
                            <h4 id="modalName" class="fw-bold text-dark mb-1"></h4>
                            <div class="mt-3">
                                <span id="modalSport" class="badge bg-success px-3 py-2 mb-2 w-100 fs-6 shadow-sm"></span><br>
                                <span id="modalClass" class="badge bg-secondary px-3 py-2 w-100"></span>
                            </div>
                        </div>
                        <!-- Right Side: Details -->
                        <div class="col-md-8 ps-md-4 mt-4 mt-md-0">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-success" style="color: #2e4e1f !important;">Personal Information</h6>
                            <table class="table table-sm table-borderless align-middle">
                                <tbody>
                                    <tr><th class="text-muted w-25"><i class="fas fa-id-card me-2"></i>ID Number</th><td id="modalStudentId" class="fw-bold text-dark"></td></tr>
                                    <tr><th class="text-muted"><i class="fas fa-envelope me-2"></i>Email</th><td id="modalEmail" class="text-dark"></td></tr>
                                    <tr><th class="text-muted"><i class="fas fa-phone me-2"></i>Contact</th><td id="modalContact" class="text-dark"></td></tr>
                                    <tr><th class="text-muted"><i class="fas fa-graduation-cap me-2"></i>Course & Yr</th><td id="modalCourse" class="text-dark"></td></tr>
                                    <tr><th class="text-muted"><i class="fas fa-birthday-cake me-2"></i>Birthdate</th><td id="modalBirthdate" class="text-dark"></td></tr>
                                    <tr><th class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>Address</th><td id="modalAddress" class="text-dark"></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SCRIPTS -->
<!-- ========================================== -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // 1. Tab Styling Toggle Script (Highlights active tab in green)
        const tabs = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabs.forEach(tab => {
            tab.addEventListener('shown.bs.tab', event => {
                // Reset all tabs to default secondary color
                tabs.forEach(t => { 
                    t.style.color = ''; 
                    t.classList.remove('text-success'); 
                    t.classList.add('text-secondary'); 
                });
                // Highlight the active tab
                event.target.classList.remove('text-secondary');
                event.target.style.color = '#2e4e1f'; 
            });
        });

        // 2. Fetch Logic for Athlete Profile Modal
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

                // Adjust this route to match your actual route for fetching user details.
                fetch(`/admin/approvals/${athleteId}/view`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Backend returned ' + response.status);
                    return response.json();
                })
                .then(data => {
                    // Populate Name
                    let mi = data.middle_initial ? ' ' + String(data.middle_initial) : '';
                    document.getElementById('modalName').innerText = `${data.last_name || ''}, ${data.first_name || ''}${mi}`;
                    
                    // Populate Badges
                    document.getElementById('modalSport').innerText = data.sport_event ? String(data.sport_event).replace(/_/g, ' ') : 'N/A';
                    document.getElementById('modalClass').innerText = data.classification ? String(data.classification).replace(/_/g, ' ') : 'N/A';
                    
                    // Populate Details
                    document.getElementById('modalStudentId').innerText = data.student_id || 'N/A';
                    document.getElementById('modalEmail').innerText = data.email || 'N/A';
                    document.getElementById('modalContact').innerText = data.contact_number || 'N/A';
                    
                    // Format Course & Year
                    let courseText = data.course ? String(data.course).trim() : '';
                    let yearText = data.year_level ? String(data.year_level).trim() : '';
                    let courseYr = courseText;
                    if (yearText) {
                        courseYr += (/^year/i.test(yearText)) ? ` — ${yearText}` : ` — Year ${yearText}`;
                    }
                    document.getElementById('modalCourse').innerText = courseYr || 'N/A';
                    
                    // Format Birthdate
                    let formattedDate = 'N/A';
                    if (data.birthdate) {
                        let datePart = String(data.birthdate).split('T')[0];
                        let d = new Date(datePart);
                        formattedDate = (!isNaN(d.getTime())) ? d.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : datePart;
                    }
                    document.getElementById('modalBirthdate').innerText = formattedDate;
                    
                    // Format Address
                    let addr = data.address || '';
                    if (data.city_municipality) addr += (addr ? ', ' : '') + data.city_municipality;
                    if (data.province_state) addr += (addr ? ', ' : '') + data.province_state;
                    document.getElementById('modalAddress').innerText = addr || 'N/A';

                    // Populate Image
                    if (data.picture_path) {
                        document.getElementById('modalPicture').src = `/storage/${data.picture_path}`;
                    } else {
                        // Generate a placeholder avatar using ui-avatars API
                        document.getElementById('modalPicture').src = `https://ui-avatars.com/api/?name=${data.first_name || 'User'}+${data.last_name || ''}&background=e8f5e9&color=2e4e1f&size=150`;
                    }

                    // Show content
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
    });
</script>
@endsection