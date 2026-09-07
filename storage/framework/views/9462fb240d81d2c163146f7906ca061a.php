 

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    <div class="row g-0">
        
        <div class="col-md-3 col-lg-2 p-3" style="background-color: #dbead5; min-height: 100vh; border-right: 1px solid #c4d79b;">
            <?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <div class="col-md-9 col-lg-10 p-4" style="background-color: #EBF1DE;">
            
            <div class="card-header fw-bold fst-italic border-bottom border-secondary mb-4 p-2" style="background-color: #bfbfbf; font-family: 'Courier New';">
                ADMIN > USERS & SECURITY
            </div>

            <!-- Success Message -->
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Create New Coach User Section -->
            <div class="card shadow-sm border border-secondary rounded-0 mb-4">
                <div class="card-header bg-info text-white fw-bold p-3">
                    <i class="fas fa-user-plus"></i> Create New Coach User Account
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('admin.createCoachUser')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">Coach Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    placeholder="e.g., John Doe" value="<?php echo e(old('name')); ?>" required>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    placeholder="coach@example.com" value="<?php echo e(old('email')); ?>" required>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                                <input type="password" id="password" name="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    placeholder="Min. 8 characters" required>
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label fw-bold">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" 
                                    placeholder="Confirm password" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="coach_sport" class="form-label fw-bold">Sport Event <span class="text-danger">*</span></label>
                                <select id="coach_sport" name="coach_sport" class="form-select <?php $__errorArgs = ['coach_sport'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">-- Select a Sport --</option>
                                    <?php $__currentLoopData = $sports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($sport->name); ?>" <?php echo e(old('coach_sport') == $sport->name ? 'selected' : ''); ?>>
                                            <?php echo e($sport->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['coach_sport'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-success fw-bold">
                                <i class="fas fa-plus"></i> Create Coach User Account
                            </button>
                        </div>
                    </form>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger mt-3" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <strong>Error creating user:</strong>
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Existing Users Table -->
            <div class="card shadow-sm border border-secondary rounded-0">
                <div class="card-header bg-secondary text-white fw-bold p-3 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-users"></i> Manage User Security Rights & Accounts</span>
                </div>
                <div class="card-body p-0">
                    <form action="<?php echo e(route('admin.updateUserPermissions')); ?>" method="POST" id="permissionsForm">
                        <?php echo csrf_field(); ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center mb-0 align-middle" style="font-size: 0.85rem;">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="bg-light" style="width: 15%;">USER DETAILS</th>
                                        <th colspan="10" class="text-white" style="background-color: #4F6228;">SECURITY RIGHTS</th>
                                        <th rowspan="2" class="bg-light" style="width: 10%;">ACTION</th>
                                    </tr>
                                    <tr style="background-color: #C4D79B;">
                                        <th>Admin</th><th>Athletes</th><th>Coaches</th><th>Sched</th><th>Achieve</th><th>Classes</th><th>Exams</th><th>Trans</th><th>Notifs</th><th>Dash</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="text-start bg-white">
                                            <div class="fw-bold text-dark"><?php echo e($user->email); ?></div>
                                            <div class="text-muted small"><?php echo e($user->name); ?></div>
                                            <?php if($user->role === 'coach'): ?>
                                                <span class="badge bg-info">Coach</span>
                                            <?php elseif($user->role === 'admin'): ?>
                                                <span class="badge bg-danger">Admin</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php $modules = ['admin', 'athletes', 'coaches', 'scheduling', 'achievements', 'classes', 'exams', 'transactions', 'notifications', 'dashboard']; ?>
                                        <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="p-1">
                                            <select name="permissions[<?php echo e($user->id); ?>][<?php echo e($module); ?>]" class="form-select form-select-sm border-0 text-center" style="cursor: pointer; <?php echo e(($user->permissions[$module] ?? '') == 'Edit' ? 'color:green; font-weight:bold;' : ''); ?>">
                                                <option value="Hidden" <?php echo e(($user->permissions[$module] ?? '') == 'Hidden' ? 'selected' : ''); ?>>Hidden</option>
                                                <option value="View" <?php echo e(($user->permissions[$module] ?? '') == 'View' ? 'selected' : ''); ?>>View</option>
                                                <option value="Edit" <?php echo e(($user->permissions[$module] ?? '') == 'Edit' ? 'selected' : ''); ?>>Edit</option>
                                            </select>
                                        </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        
                                        <!-- DELETE / REMOVE USER ACTION -->
                                        <td class="bg-white">
                                            <?php if($user->id !== auth()->id()): ?>
                                                <button type="button" class="btn btn-danger btn-sm px-2 py-1 text-white fw-bold" onclick="confirmDelete('<?php echo e(route('admin.deleteUser', $user->id)); ?>', '<?php echo e($user->name); ?>')">
                                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted small fst-italic">Current</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="12" class="text-center text-muted p-3">No users found</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 bg-light border-top text-end">
                            <button type="submit" class="btn btn-success fw-bold"><i class="fas fa-save"></i> Update Security Rights</button>
                        </div>
                    </form>

                    <!-- Hidden Delete Form -->
                    <form id="deleteUserForm" method="POST" style="display: none;">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                    </form>
                </div>
            </div>
            <div class="mt-3 text-muted small"><i class="fas fa-info-circle"></i> <b>Edit:</b> Can add/modify data. <b>View:</b> Read-only access. <b>Hidden:</b> Module disappears from menu.</div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(deleteUrl, userName) {
        if (confirm('Are you sure you want to remove user "' + userName + '"? This action cannot be undone.')) {
            var form = document.getElementById('deleteUserForm');
            form.action = deleteUrl;
            form.submit();
        }
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\AthleteX\resources\views/admin/users.blade.php ENDPATH**/ ?>