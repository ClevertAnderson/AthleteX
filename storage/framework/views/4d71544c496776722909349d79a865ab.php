<?php $__env->startSection('title', 'Settings'); ?>

<?php $__env->startSection('content'); ?>
<?php
    // Small helpers: read a saved setting, and read a toggle (hidden "0" + checkbox "1")
    $s  = fn ($key, $default = null) => \App\Models\Setting::get($key, $default);
    $on = fn ($key, $default = '0') => (string) old($key, $s($key, $default)) === '1';
?>

<div class="container-fluid p-0">
    <div class="row g-0">
        
        <div class="col-md-3 col-lg-2 p-3 md:min-h-screen" style="background-color: #dbead5; border-right: 1px solid #c4d79b;">
            <?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <div class="col-md-9 col-lg-10 p-4" style="background-color: #EBF1DE;">
            
            <div class="card-header fw-bold fst-italic border-bottom border-secondary mb-4 p-2" style="background-color: #bfbfbf; font-family: 'Courier New';">
                ADMIN > SETTINGS
            </div>

            <!-- Success message after saving -->
            <?php if(session('success')): ?>
                <div class="alert alert-success shadow-sm">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <!-- Global Error Alert -->
            <?php if($errors->any()): ?>
                <div class="alert alert-danger shadow-sm">
                    <strong>⚠️ Please fix the errors below before saving.</strong>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('admin.saveSettings')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                
                
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header fw-bold text-white py-2" style="background-color: #2e4e1f;">
                        <i class="bi bi-building"></i> School Profile
                    </div>
                    <div class="card-body" style="background-color: #EBF1DE;">
                        <div class="row">
                            <div class="col-md-9">

                                <div class="mb-3 row">
                                    <label class="col-sm-3 col-form-label form-label-sm fw-bold text-start md:text-end">School / Company</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="school_name" class="form-control <?php $__errorArgs = ['school_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('school_name', $s('school_name'))); ?>" placeholder="Enter School Name">
                                        <?php $__errorArgs = ['school_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-sm-3 col-form-label form-label-sm fw-bold text-start md:text-end">Contact Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="contact_name" class="form-control <?php $__errorArgs = ['contact_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('contact_name', $s('contact_name'))); ?>">
                                        <?php $__errorArgs = ['contact_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-sm-3 col-form-label form-label-sm fw-bold text-start md:text-end">Email</label>
                                    <div class="col-sm-4 mb-2 mb-sm-0">
                                        <input type="email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('email', $s('email'))); ?>">
                                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <label class="col-sm-2 col-form-label form-label-sm fw-bold text-start md:text-end mt-2 mt-sm-0">Phone 1</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="phone_1" class="form-control <?php $__errorArgs = ['phone_1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('phone_1', $s('phone_1'))); ?>">
                                        <?php $__errorArgs = ['phone_1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-sm-3 col-form-label form-label-sm fw-bold text-start md:text-end">Address</label>
                                    <div class="col-sm-4 mb-2 mb-sm-0">
                                        <input type="text" name="address" class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('address', $s('address'))); ?>">
                                        <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <label class="col-sm-2 col-form-label form-label-sm fw-bold text-start md:text-end mt-2 mt-sm-0">Phone 2</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="phone_2" class="form-control <?php $__errorArgs = ['phone_2'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('phone_2', $s('phone_2'))); ?>">
                                        <?php $__errorArgs = ['phone_2'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-3 mt-4 mt-md-0">
                                <div class="card">
                                    <div class="card-header text-center bg-secondary text-white py-1 small fw-bold">Logo</div>
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center p-2 bg-white">
                                        <?php $logo = $s('logo'); ?>
                                        <?php if($logo): ?>
                                            <img src="<?php echo e(asset('storage/' . $logo)); ?>" class="img-fluid mb-2" style="max-height: 100px; border: 1px solid #ddd;">
                                        <?php else: ?>
                                            <div class="border d-flex align-items-center justify-content-center mb-2 bg-light" style="height: 100px; width: 100%;"> 
                                                <i class="fas fa-image fa-3x text-secondary"></i>
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" name="logo" class="form-control form-control-sm <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <small class="text-muted d-block mt-1">Rec: 200x200px</small>
                                        <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text fw-bold bg-light">City / Municipality</span>
                                    <input type="text" name="city" class="form-control <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('city', $s('city'))); ?>">
                                </div>
                                <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold px-2"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text fw-bold bg-light">Province</span>
                                    <input type="text" name="province" class="form-control <?php $__errorArgs = ['province'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('province', $s('province'))); ?>">
                                </div>
                                <?php $__errorArgs = ['province'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold px-2"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text fw-bold bg-light">Zip Code</span>
                                    <input type="text" name="zip" class="form-control <?php $__errorArgs = ['zip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('zip', $s('zip'))); ?>">
                                </div>
                                <?php $__errorArgs = ['zip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold px-2"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                </div>

                
                
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header fw-bold text-white py-2" style="background-color: #2e4e1f;">
                        <i class="bi bi-calendar-event"></i> Academic Year &amp; Semester
                    </div>
                    <div class="card-body" style="background-color: #EBF1DE;">

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label form-label-sm fw-bold text-start md:text-end">Academic Year</label>
                            <div class="col-sm-3">
                                <input type="text" name="academic_year" class="form-control <?php $__errorArgs = ['academic_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('academic_year', $s('academic_year'))); ?>" placeholder="e.g. 2026-2027" maxlength="9">
                                <?php $__errorArgs = ['academic_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <label class="col-sm-3 col-form-label form-label-sm fw-bold text-start md:text-end mt-2 mt-sm-0">Current Semester</label>
                            <div class="col-sm-3">
                                <?php $sem = old('current_semester', $s('current_semester')); ?>
                                <select name="current_semester" class="form-select <?php $__errorArgs = ['current_semester'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">-- Select --</option>
                                    <?php $__currentLoopData = ['1st Semester', '2nd Semester', 'Summer']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($opt); ?>" <?php echo e($sem === $opt ? 'selected' : ''); ?>><?php echo e($opt); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['current_semester'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="mb-0 row">
                            <label class="col-sm-3 col-form-label form-label-sm fw-bold text-start md:text-end">Semester Start</label>
                            <div class="col-sm-3 mb-2 mb-sm-0">
                                <input type="date" name="semester_start" class="form-control <?php $__errorArgs = ['semester_start'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('semester_start', $s('semester_start'))); ?>">
                                <?php $__errorArgs = ['semester_start'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <label class="col-sm-3 col-form-label form-label-sm fw-bold text-start md:text-end mt-2 mt-sm-0">Semester End</label>
                            <div class="col-sm-3">
                                <input type="date" name="semester_end" class="form-control <?php $__errorArgs = ['semester_end'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('semester_end', $s('semester_end'))); ?>">
                                <?php $__errorArgs = ['semester_end'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 
                
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header fw-bold text-white py-2" style="background-color: #2e4e1f;">
                        <i class="bi bi-bell"></i> Email &amp; Notifications
                    </div>
                    <div class="card-body" style="background-color: #EBF1DE;">

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label form-label-sm fw-bold text-start md:text-end">Notification Email</label>
                            <div class="col-sm-6">
                                <input type="email" name="notification_email" class="form-control <?php $__errorArgs = ['notification_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('notification_email', $s('notification_email'))); ?>" placeholder="Where system alerts are sent">
                                <?php $__errorArgs = ['notification_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="mb-2 row">
                            <div class="col-sm-9 offset-sm-3">
                                <div class="form-check form-switch mb-2">
                                    <input type="hidden" name="notify_new_registration" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="notifyNewReg" name="notify_new_registration" value="1" <?php echo e($on('notify_new_registration', '1') ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="notifyNewReg">Email me when a new athlete registration needs approval</label>
                                </div>

                                <div class="form-check form-switch mb-2">
                                    <input type="hidden" name="notify_approval_decision" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="notifyDecision" name="notify_approval_decision" value="1" <?php echo e($on('notify_approval_decision', '1') ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="notifyDecision">Email the applicant when their registration is approved or declined</label>
                                </div>

                                <div class="form-check form-switch">
                                    <input type="hidden" name="send_coach_credentials" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="sendCoachCreds" name="send_coach_credentials" value="1" <?php echo e($on('send_coach_credentials', '1') ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="sendCoachCreds">Email login details to new coaches when their account is created</label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div> -->

                
                
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header fw-bold text-white py-2" style="background-color: #2e4e1f;">
                        <i class="bi bi-shield-lock"></i> Security
                    </div>
                    <div class="card-body" style="background-color: #EBF1DE;">

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label form-label-sm fw-bold text-start md:text-end">Auto-Logout (minutes)</label>
                            <div class="col-sm-3">
                                <input type="number" name="session_timeout" min="0" max="480" class="form-control <?php $__errorArgs = ['session_timeout'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('session_timeout', $s('session_timeout', 0))); ?>">
                                <?php $__errorArgs = ['session_timeout'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-sm-6 d-flex align-items-center">
                                <small class="text-muted">Logs users out after this many minutes of inactivity. Use 0 to turn off.</small>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label form-label-sm fw-bold text-start md:text-end">Minimum Password Length</label>
                            <div class="col-sm-3">
                                <input type="number" name="password_min_length" min="6" max="32" class="form-control <?php $__errorArgs = ['password_min_length'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('password_min_length', $s('password_min_length', 8))); ?>">
                                <?php $__errorArgs = ['password_min_length'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-sm-6 d-flex align-items-center">
                                <small class="text-muted">Applies when creating coach accounts.</small>
                            </div>
                        </div>

                        <div class="mb-0 row">
                            <div class="col-sm-9 offset-sm-3">
                                <div class="form-check form-switch mb-2">
                                    <input type="hidden" name="password_require_number" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="pwNumber" name="password_require_number" value="1" <?php echo e($on('password_require_number') ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="pwNumber">Passwords must contain at least one number</label>
                                </div>

                                <div class="form-check form-switch">
                                    <input type="hidden" name="password_require_symbol" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="pwSymbol" name="password_require_symbol" value="1" <?php echo e($on('password_require_symbol') ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="pwSymbol">Passwords must contain at least one symbol (! @ # $ ...)</label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 
                
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header fw-bold text-white py-2" style="background-color: #2e4e1f;">
                        <i class="bi bi-sliders"></i> System Preferences
                    </div>
                    <div class="card-body" style="background-color: #EBF1DE;">

                        <div class="mb-0 row">
                            <label class="col-sm-3 col-form-label form-label-sm fw-bold text-start md:text-end">Records Per Page</label>
                            <div class="col-sm-3">
                                <?php $perPage = (string) old('records_per_page', $s('records_per_page', 15)); ?>
                                <select name="records_per_page" class="form-select <?php $__errorArgs = ['records_per_page'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <?php $__currentLoopData = [10, 15, 25, 50]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($n); ?>" <?php echo e($perPage === (string) $n ? 'selected' : ''); ?>><?php echo e($n); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['records_per_page'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger fw-bold"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-sm-6 d-flex align-items-center">
                                <small class="text-muted">How many rows lists show per page (used by the Achievements list).</small>
                            </div>
                        </div>

                    </div>
                </div> -->

                <div class="mb-4 text-end">
                    <button type="submit" class="btn btn-success fw-bold px-4"><i class="fas fa-save"></i> Save Settings</button>
                </div>
            </form>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\AthleteX\resources\views/admin/settings.blade.php ENDPATH**/ ?>