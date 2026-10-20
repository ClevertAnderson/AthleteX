<?php $__env->startSection('title', 'Attendance'); ?>

<?php $__env->startSection('content'); ?>
<div id="tab-content" class="bg-[#c5e0b4] p-4 md:p-6 rounded w-full min-h-screen">
    <div class="bg-white border-[8px] md:border-[12px] border-[#d1e9f0] p-1 shadow-sm rounded-lg">

        <!-- Page Header -->
        <div class="bg-[#5bc0de] p-4 flex flex-col md:flex-row items-center justify-between gap-4 mb-6 rounded shadow-sm">
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-0">Attendance</h1>
                <?php if(isset($today)): ?>
                    <p class="text-sm text-gray-700 font-medium">Today: <?php echo e(\Carbon\Carbon::parse($today)->format('l, F j, Y')); ?></p>
                <?php endif; ?>
            </div>
            <div class="w-full md:w-auto">
                <a href="<?php echo e(route('attendance.history')); ?>" 
                class="bg-green-700 text-white px-4 py-2 rounded shadow hover:bg-green-800 transition block text-center font-semibold">
                    📊 Attendance History
                </a>
            </div>
        </div>

        <!-- Admin Filter -->
        <?php if(auth()->user()->role === 'admin'): ?>
        <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 mb-4 w-full px-2">
            
            <select name="sport" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-2 w-full sm:w-auto flex-1 focus:ring-2 focus:ring-green-500 outline-none">
                <option value="">All Sports</option>
                <?php $__currentLoopData = $sports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($sport->name); ?>" <?php echo e(request('sport') == $sport->name ? 'selected' : ''); ?>>
                        <?php echo e($sport->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <select id="monthFilter" name="month" onchange="document.getElementById('dateFilter').value=''; this.form.submit()" class="border border-gray-300 rounded px-3 py-2 w-full sm:w-auto flex-1 focus:ring-2 focus:ring-green-500 outline-none">
                <option value="">All Months</option>
                <?php
                    $monthsList = [
                        '01' => 'January', '02' => 'February', '03' => 'March',
                        '04' => 'April', '05' => 'May', '06' => 'June',
                        '07' => 'July', '08' => 'August', '09' => 'September',
                        '10' => 'October', '11' => 'November', '12' => 'December'
                    ];
                ?>
                <?php $__currentLoopData = $monthsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($num); ?>" <?php echo e(request('month') == $num ? 'selected' : ''); ?>>
                        <?php echo e($name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <input id="dateFilter" type="date" name="date" value="<?php echo e(request('date')); ?>" onchange="document.getElementById('monthFilter').value=''; this.form.submit()"
                class="border border-gray-300 rounded px-3 py-2 w-full sm:w-auto flex-1 focus:ring-2 focus:ring-green-500 outline-none" />

            <?php if(request('sport') || request('month') || request('date')): ?>
                <a href="<?php echo e(route('admin.attendance')); ?>" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition text-center font-semibold w-full sm:w-auto">
                    Clear Filters
                </a>
            <?php endif; ?>
        </form>
        <?php endif; ?>

        <!-- Coach Attendance Checking Button -->
        <?php if(auth()->user()->role === 'coach'): ?>
        <div class="flex justify-center md:justify-start px-2 mb-4">
            <button class="btn btn-success w-full md:w-auto font-semibold" data-bs-toggle="modal" data-bs-target="#attendanceModal">
                <i class="bi bi-clipboard-check me-1"></i> Check Attendance
            </button>
        </div>
        <?php endif; ?>

        <!-- Attendance Table -->
        <div class="bg-white rounded shadow border border-gray-300 overflow-hidden mx-2 md:mx-0">
            <div class="overflow-x-auto w-full">
                <table class="min-w-full divide-y divide-gray-300 whitespace-nowrap">
                    <thead class="bg-[#d1e9f0] border-b border-gray-300">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase border-r border-gray-200">#</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase border-r border-gray-200">Athlete</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase border-r border-gray-200">Sport</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase border-r border-gray-200">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase border-r border-gray-200">Remarks</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $athletesWithStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $athlete): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-blue-50 transition-colors <?php echo e($index % 2 == 0 ? 'bg-white' : 'bg-gray-50'); ?>">
                                <td class="px-6 py-3 text-sm border-r border-gray-200">
                                    <?php echo e(method_exists($athletesWithStatus, 'firstItem') && $athletesWithStatus->firstItem() ? $athletesWithStatus->firstItem() + $index : $index + 1); ?>

                                </td>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900 border-r border-gray-200"><?php echo e($athlete['last_name']); ?>, <?php echo e($athlete['first_name']); ?></td>
                                <td class="px-6 py-3 text-sm text-gray-600 border-r border-gray-200"><?php echo e($athlete['sport_event']); ?></td>
                                <td class="px-6 py-3 border-r border-gray-200">
                                    <?php if(strtolower($athlete['status']) === 'present'): ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 border border-green-200">Present</span>
                                    <?php elseif(strtolower($athlete['status']) === 'absent'): ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 border border-red-200">Absent</span>
                                    <?php elseif(strtolower($athlete['status']) === 'late'): ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">Late</span>
                                    <?php elseif(strtolower($athlete['status']) === 'excused'): ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">Excused</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-500 border border-gray-200">Not Marked</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500 border-r border-gray-200"><?php echo e($athlete['remarks'] ?? '—'); ?></td>
                                <td class="px-6 py-3 text-sm text-gray-600">
                                    <?php echo e(\Carbon\Carbon::parse($athlete['attendance_date'])->format('Y-m-d')); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 font-medium">No athletes found for this filter.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- 🚀 Pagination Links Footer -->
            <?php if(method_exists($athletesWithStatus, 'links')): ?>
                <div class="p-4 border-t bg-gray-50">
                    <?php echo e($athletesWithStatus->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Attendance Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" data-bs-backdrop="false" style="background-color: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?php echo e(route('coach.attendance.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label font-bold">Attendance Date</label>
                        <input type="date" name="attendance_date" class="form-control" value="<?php echo e(isset($today) ? $today : now()->toDateString()); ?>" max="<?php echo e(now()->toDateString()); ?>">
                        <small class="text-muted">Select today or a past date to back-log historical attendance.</small>
                    </div>

                    <div class="alert alert-info mb-3 text-sm" role="alert">
                        <strong>📋 How it works:</strong> Select the date you want to record, then click the buttons to cycle through the status.
                    </div>

                    <div class="table-responsive mb-3" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>#</th>
                                    <th>Athlete</th>
                                    <th>Sports</th>
                                    <th>Status</th>
                                    <th>Remarks (Optional)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($athletes)): ?>
                                    <?php $__currentLoopData = $athletes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $athlete): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td><?php echo e($athlete->last_name); ?>, <?php echo e($athlete->first_name); ?></td>
                                        <td><?php echo e($athlete->sport_event); ?></td>
                                        <td>
                                            <input type="hidden" name="attendance[<?php echo e($athlete->id); ?>][status]" value="present" class="attendance-hidden">
                                            <button type="button" class="btn btn-sm btn-outline-success attendance-toggle" title="Click to cycle through statuses">
                                                Present
                                            </button>
                                        </td>
                                        <td>
                                            <input type="text" name="attendance[<?php echo e($athlete->id); ?>][remarks]" class="form-control form-control-sm" placeholder="e.g., Injured">
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Attendance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.appendChild(document.getElementById('attendanceModal'));

    const statuses = ['present', 'absent', 'late', 'excused'];
    const statusLabels = { 'present': 'Present', 'absent': 'Absent', 'late': 'Late', 'excused': 'Excused' };
    const statusClasses = { 'present': 'btn-outline-success', 'absent': 'btn-outline-danger', 'late': 'btn-outline-warning', 'excused': 'btn-outline-info' };

    document.querySelectorAll('.attendance-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const hiddenInput = this.previousElementSibling;
            let currentStatus = hiddenInput.value;
            let currentIndex = statuses.indexOf(currentStatus);
            let nextIndex = (currentIndex + 1) % statuses.length;
            let nextStatus = statuses[nextIndex];

            hiddenInput.value = nextStatus;
            this.textContent = statusLabels[nextStatus];
            this.className = 'btn btn-sm ' + statusClasses[nextStatus] + ' attendance-toggle';
        });
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\AthleteX\resources\views/features/attendance.blade.php ENDPATH**/ ?>