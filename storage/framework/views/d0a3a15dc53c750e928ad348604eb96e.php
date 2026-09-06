 

<?php $__env->startSection('content'); ?>

<?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
                               value="<?php echo e(route('tryout.register.show')); ?>" 
                               id="regLink" readonly>
                        <button class="btn btn-success fw-bold px-3" type="button" onclick="copyToClipboard('regLink')">
                            Copy
                        </button>
                        <a href="<?php echo e(route('tryout.register.show')); ?>" target="_blank" class="btn btn-outline-success fw-bold px-3">
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
                               value="<?php echo e(route('alumni.form.show')); ?>" 
                               id="alumniLink" readonly>
                        <button class="btn btn-success fw-bold px-3" type="button" onclick="copyToClipboard('alumniLink')">
                            Copy
                        </button>
                        <a href="<?php echo e(route('alumni.form.show')); ?>" target="_blank" class="btn btn-outline-success fw-bold px-3">
                            Open
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message Alert -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

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
                <?php if(isset($tryoutPendings) && $tryoutPendings->isNotEmpty()): ?>
                    <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.75rem;"><?php echo e($tryoutPendings->count()); ?> Pending</span>
                <?php endif; ?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-3 text-secondary d-flex align-items-center gap-2" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests-pane" type="button" role="tab" aria-controls="requests-pane" aria-selected="false">
                <span><i class="fas fa-file-signature me-2"></i> Student Requests</span>
                <?php if(isset($studentRequests) && $studentRequests->isNotEmpty()): ?>
                    <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.75rem;"><?php echo e($studentRequests->count()); ?> Pending</span>
                <?php endif; ?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-3 text-secondary d-flex align-items-center gap-2" id="alumni-tab" data-bs-toggle="tab" data-bs-target="#alumni-pane" type="button" role="tab" aria-controls="alumni-pane" aria-selected="false">
                <span><i class="fas fa-graduation-cap me-2"></i> Alumni Submissions</span>
                <?php if(isset($alumniPendings) && $alumniPendings->isNotEmpty()): ?>
                    <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.75rem;"><?php echo e($alumniPendings->count()); ?> Pending</span>
                <?php endif; ?>
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
                            <?php $__currentLoopData = \App\Models\Sport::orderBy('name', 'asc')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e(strtoupper(str_replace('_', ' ', $sport->name))); ?>"><?php echo e($sport->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="card-body p-0">
                    <?php if(!isset($tryoutPendings) || $tryoutPendings->isEmpty()): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted fs-5">No tryout applicants pending.</p>
                        </div>
                    <?php else: ?>
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
                                    <?php $__currentLoopData = $tryoutPendings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="athlete-row">
                                        <td class="ps-4 fw-bold text-dark"><?php echo e($p->first_name); ?> <?php echo e($p->last_name); ?></td>
                                        <td>
                                            <span class="text-dark small"><i class="fas fa-envelope me-1"></i> <?php echo e($p->email); ?></span><br>
                                            <span class="text-muted small"><i class="fas fa-phone me-1"></i> <?php echo e($p->contact_number ?? 'N/A'); ?></span>
                                        </td>
                                        <td><span class="badge bg-success px-3 py-2 sport-cell"><?php echo e(str_replace('_', ' ', $p->sport_event)); ?></span></td>
                                        <td class="text-secondary small"><?php echo e($p->created_at->format('M d, Y')); ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" onclick="viewProfile(<?php echo e($p->id); ?>)" class="btn btn-info btn-sm px-3 text-white" title="View Profile">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </button>
                                                <form action="<?php echo e(route('admin.approve.athlete', $p->id)); ?>" method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-success btn-sm px-3" onclick="return confirm('Did <?php echo e($p->first_name); ?> pass the tryouts? This will make them an Active athlete.')">
                                                        <i class="fas fa-trophy me-1"></i> Passed
                                                    </button>
                                                </form>
                                                <form action="<?php echo e(route('admin.reject.athlete', $p->id)); ?>" method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-outline-danger btn-sm px-3" onclick="return confirm('Did they fail/not show up? This will remove their record.')">
                                                        <i class="fas fa-times me-1"></i> Failed
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
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
                            <?php $__currentLoopData = \App\Models\Sport::orderBy('name', 'asc')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e(strtoupper(str_replace('_', ' ', $sport->name))); ?>"><?php echo e($sport->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="card-body p-0">
                    <?php if(!isset($studentRequests) || $studentRequests->isEmpty()): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-check text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted fs-5">No pending student requests from coaches.</p>
                        </div>
                    <?php else: ?>
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
                                    <?php $__currentLoopData = $studentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="athlete-row">
                                        <td class="ps-4 fw-bold text-dark"><?php echo e($req->first_name); ?> <?php echo e($req->last_name); ?></td>
                                        <td class="text-secondary"><?php echo e($req->student_id); ?></td>
                                        <td class="sport-cell-container">
                                            <span class="badge bg-success px-2 py-1 mb-1 sport-cell"><?php echo e(str_replace('_', ' ', $req->sport_event)); ?></span><br>
                                            <span class="badge bg-secondary px-2 py-1"><?php echo e(str_replace('_', ' ', $req->classification)); ?></span>
                                        </td>
                                        <td class="text-secondary small">
                                            <?php echo e($req->coach ? $req->coach->coach_first_name . ' ' . $req->coach->coach_last_name : 'N/A'); ?>

                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" onclick="viewProfile(<?php echo e($req->id); ?>)" class="btn btn-info btn-sm px-3 text-white" title="View Profile">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </button>
                                                <form action="<?php echo e(route('admin.approve.athlete', $req->id)); ?>" method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-success btn-sm px-3" onclick="return confirm('Approve this athlete profile to the active roster?')">
                                                        <i class="fas fa-check me-1"></i> Approve
                                                    </button>
                                                </form>
                                                <form action="<?php echo e(route('admin.reject.athlete', $req->id)); ?>" method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-outline-danger btn-sm px-3" onclick="return confirm('Reject and delete this entry?')">
                                                        <i class="fas fa-trash me-1"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
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
                            <?php $__currentLoopData = \App\Models\Sport::orderBy('name', 'asc')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e(strtoupper(str_replace('_', ' ', $sport->name))); ?>"><?php echo e($sport->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="card-body p-0">
                    <?php if(!isset($alumniPendings) || $alumniPendings->isEmpty()): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted fs-5">No pending alumni forms to approve.</p>
                        </div>
                    <?php else: ?>
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
                                    <?php $__currentLoopData = $alumniPendings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alumni): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="athlete-row">
                                        <td class="ps-4 fw-bold text-dark"><?php echo e($alumni->first_name); ?> <?php echo e($alumni->last_name); ?></td>
                                        <td>
                                            <span class="badge bg-success px-2 py-1 mb-1 sport-cell"><?php echo e(str_replace('_', ' ', $alumni->sport_event)); ?></span><br>
                                            <span class="text-muted small">Batch <?php echo e($alumni->year_graduated ?? 'N/A'); ?></span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark"><?php echo e($alumni->current_work ?? 'Not Specified'); ?></span><br>
                                            <span class="text-muted small"><?php echo e($alumni->current_company ?? 'N/A'); ?></span>
                                        </td>
                                        <td class="text-secondary small"><?php echo e($alumni->created_at->format('M d, Y')); ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <form action="<?php echo e(route('admin.approve.alumni', $alumni->id)); ?>" method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-success btn-sm px-3" onclick="return confirm('Approve this alumni and update their master record?')">
                                                        <i class="fas fa-check me-1"></i> Approve
                                                    </button>
                                                </form>
                                                <form action="<?php echo e(route('admin.reject.alumni', $alumni->id)); ?>" method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-outline-danger btn-sm px-3" onclick="return confirm('Delete this alumni submission?')">
                                                        <i class="fas fa-trash me-1"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
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
            document.getElementById('modalName').innerText = `${data.first_name} ${data.last_name}`;
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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\AthleteX\resources\views/features/approvals.blade.php ENDPATH**/ ?>