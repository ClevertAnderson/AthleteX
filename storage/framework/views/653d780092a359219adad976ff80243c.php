<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athlete Registration - SDO</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
    <!-- 🚀 NEW: SweetAlert2 Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-to-br from-green-900 to-green-800 min-h-screen flex items-center justify-center p-4">

    <!-- Main Card -->
    <div class="w-full max-w-xl bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden relative z-10">

        <!-- Header -->
        <div class="bg-green-800 text-white p-6 text-center">
            <h1 class="text-2xl font-bold mb-1 flex items-center justify-center gap-2">
                <span>📇</span> Athlete Registration
            </h1>
            <p class="text-green-100 opacity-80 text-sm">Sports Development Office • Enrollment & Profiling</p>
        </div>

        <!-- Form Content -->
        <div class="p-6 space-y-6">

            <!-- Alerts (For general errors) -->
            <?php if(session('error')): ?>
                <div class="bg-red-100 text-red-900 p-3 rounded-md text-sm border border-red-300">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="bg-red-100 text-red-900 p-3 rounded-md text-sm border border-red-300">
                    <ul class="list-disc list-inside mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="bg-blue-50 border-l-4 border-blue-600 p-3 rounded-md text-blue-800 text-sm">
                <strong>Tryout Applicant:</strong> Please fill out your details below. Your tryout schedule will be shown after submission.
            </div>

            <!-- Form -->
            <form action="<?php echo e(route('tryout.register.submit')); ?>" method="POST" class="space-y-4" id="registrationForm">
                <?php echo csrf_field(); ?>
                
                <!-- Automatically set as Tryout -->
                <input type="hidden" name="classification" value="Tryout">

                <!-- Basic Info -->
                <div class="space-y-3">
                    <h2 class="text-gray-800 font-semibold border-b border-gray-300 pb-1">Basic Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="text-gray-700 text-sm">Student ID <span class="text-red-600">*</span></label>
                            <input type="text" id="student_id" name="student_id" value="<?php echo e(old('student_id')); ?>" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600 font-mono"
                                placeholder="XX-XXXX-XXX" required oninput="formatStudentID(this)" maxlength="11">
                            <span id="studentIdError" class="text-red-500 text-xs hidden font-bold mt-1">Must be 11 chars</span>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-gray-700 text-sm">Email <span class="text-red-600">*</span></label>
                            <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" class="w-full p-2 rounded-md border <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600" required oninput="validateEmail(this)">
                            <span id="emailError" class="text-red-500 text-xs hidden font-bold mt-1">Enter a valid email</span>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-red-500 text-xs font-bold mt-1 block"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-gray-700 text-sm">Contact Number <span class="text-red-600">*</span></label>
                            <input type="text" id="contact_number" name="contact_number" value="<?php echo e(old('contact_number')); ?>" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600 font-mono" placeholder="09XXXXXXXXX" required oninput="formatContactNumber(this)" maxlength="11">
                            <span id="contactError" class="text-red-500 text-xs hidden font-bold mt-1">Must start with 09 and be 11 digits</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                        <div>
                            <label class="text-gray-700 text-sm">First Name <span class="text-red-600">*</span></label>
                            <input type="text" name="first_name" value="<?php echo e(old('first_name')); ?>" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600" required>
                        </div>
                        <div>
                            <label class="text-gray-700 text-sm">M.I.</label>
                            <input type="text" name="middle_initial" value="<?php echo e(old('middle_initial')); ?>" maxlength="3" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600">
                        </div>
                        <div>
                            <label class="text-gray-700 text-sm">Last Name <span class="text-red-600">*</span></label>
                            <input type="text" name="last_name" value="<?php echo e(old('last_name')); ?>" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="text-gray-700 text-sm">Sport Event <span class="text-red-600">*</span></label>
                        <select name="sport_event" class="form-select w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600" required>
                            <option value="">Select Sport...</option>
                            <?php $__currentLoopData = \App\Models\Sport::orderBy('name', 'asc')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e(str_replace(' ', '_', $sport->name)); ?>" <?php echo e(old('sport_event') == str_replace(' ', '_', $sport->name) ? 'selected' : ''); ?>>
                                    <?php echo e($sport->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <!-- Contact & Address -->
                <div class="space-y-3 mt-6">
                    <h2 class="text-gray-800 font-semibold border-b border-gray-300 pb-1">Contact & Address</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-gray-700 text-sm">Facebook/Social Link</label>
                            <input type="url" name="facebook_link" value="<?php echo e(old('facebook_link')); ?>" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="text-gray-700 text-sm">Home Address <span class="text-red-600">*</span></label>
                        <input type="text" name="address" value="<?php echo e(old('address')); ?>" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                        <div>
                            <label class="text-gray-700 text-sm">City / Municipality <span class="text-red-600">*</span></label>
                            <input type="text" name="city_municipality" value="<?php echo e(old('city_municipality')); ?>" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600" required>
                        </div>
                        <div>
                            <label class="text-gray-700 text-sm">Province</label>
                            <input type="text" name="province_state" value="<?php echo e(old('province_state')); ?>" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600">
                        </div>
                        <div>
                            <label class="text-gray-700 text-sm">Zip Code</label>
                            <input type="text" name="zip_code" value="<?php echo e(old('zip_code')); ?>" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600">
                        </div>
                    </div>

                    <h2 class="text-gray-800 font-semibold border-b border-gray-300 pb-1 mt-6">Academic Info</h2>
                    <div class="mt-3">
                        <label class="text-gray-700 text-sm">Course / Program <span class="text-red-600">*</span></label>
                        <input type="text" name="course" value="<?php echo e(old('course')); ?>" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600" placeholder="e.g. BSIT" required>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex flex-col md:flex-row gap-3 mt-6">
                    <button type="submit" id="submitBtn" class="w-full md:w-auto bg-green-700 hover:bg-green-800 text-white py-2.5 px-6 rounded-md font-semibold transition opacity-50 cursor-not-allowed" disabled>Submit Registration</button>
                    <a href="<?php echo e(url('/')); ?>" class="w-full md:w-auto text-green-800 hover:text-green-600 text-center py-2.5">Cancel</a>
                </div>

            </form>
        </div>
    </div>

<!-- 🚀 NEW: SWEETALERT TRIGGER FOR SUCCESS -->
<?php if(session('success') || session('tryout_success')): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Registration Confirmed!',
            html: `
                <div class="text-left bg-green-50 p-4 rounded-md border border-green-200 mb-3 font-medium text-gray-800">
                    <?php echo session('tryout_success') ?? session('success'); ?>

                </div>
                <p class="text-sm text-gray-500">Your application has been successfully submitted to the Sports Development Office. Please take note of your schedule.</p>
            `,
            icon: 'success',
            confirmButtonColor: '#15803d', // Tailwind green-700
            confirmButtonText: 'I Understand, Close',
            allowOutsideClick: false, // Forces them to click the button
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Refresh the page to clear the form fields safely
                window.location.href = window.location.pathname; 
            }
        });
    });
</script>
<?php endif; ?>

<script>
    let isStudentIdValid = false;
    let isEmailValid = false;
    let isContactValid = false;

    function checkFormValidity() {
        const submitBtn = document.getElementById('submitBtn');
        if (isStudentIdValid && isEmailValid && isContactValid) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    function formatStudentID(input) {
        let val = input.value.replace(/\D/g, ''); 
        if (val.length > 2) val = val.slice(0, 2) + '-' + val.slice(2);
        if (val.length > 7) val = val.slice(0, 7) + '-' + val.slice(7);
        input.value = val;
        
        const err = document.getElementById('studentIdError');
        if (val.length === 11) {
            err.classList.add('hidden');
            input.classList.remove('border-red-500', 'ring-red-500');
            input.classList.add('border-green-600', 'ring-green-600');
            isStudentIdValid = true;
        } else {
            if (val.length > 0) err.classList.remove('hidden');
            else err.classList.add('hidden');
            input.classList.add('border-red-500', 'ring-red-500');
            input.classList.remove('border-green-600', 'ring-green-600');
            isStudentIdValid = false;
        }
        checkFormValidity();
    }

    function validateEmail(input) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const err = document.getElementById('emailError');
        
        if (emailRegex.test(input.value)) {
            err.classList.add('hidden');
            input.classList.remove('border-red-500', 'ring-red-500');
            input.classList.add('border-green-600', 'ring-green-600');
            isEmailValid = true;
        } else {
            if (input.value.length > 0) err.classList.remove('hidden');
            else err.classList.add('hidden');
            input.classList.add('border-red-500', 'ring-red-500');
            input.classList.remove('border-green-600', 'ring-green-600');
            isEmailValid = false;
        }
        checkFormValidity();
    }

    function formatContactNumber(input) {
        let val = input.value.replace(/\D/g, '');
        if (val.length > 11) val = val.slice(0, 11);
        input.value = val;

        const err = document.getElementById('contactError');
        if (val.length === 11 && val.startsWith('09')) {
            err.classList.add('hidden');
            input.classList.remove('border-red-500', 'ring-red-500');
            input.classList.add('border-green-600', 'ring-green-600');
            isContactValid = true;
        } else {
            if (val.length > 0) err.classList.remove('hidden');
            else err.classList.add('hidden');
            input.classList.add('border-red-500', 'ring-red-500');
            input.classList.remove('border-green-600', 'ring-green-600');
            isContactValid = false;
        }
        checkFormValidity();
    }

    // Run validations on page load if old data is present
    window.addEventListener('DOMContentLoaded', () => {
        const studentIdInput = document.getElementById('student_id');
        const emailInput = document.getElementById('email');
        const contactInput = document.getElementById('contact_number');

        if (studentIdInput && studentIdInput.value) formatStudentID(studentIdInput);
        if (emailInput && emailInput.value) validateEmail(emailInput);
        if (contactInput && contactInput.value) formatContactNumber(contactInput);
    });
</script>

</body>
</html><?php /**PATH D:\xampp\htdocs\AthleteX\resources\views/features/tryout_registration.blade.php ENDPATH**/ ?>