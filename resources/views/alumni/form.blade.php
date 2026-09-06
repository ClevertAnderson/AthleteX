<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Tracing Form - SDO</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gradient-to-br from-green-900 to-green-800 min-h-screen flex items-center justify-center p-4">

    <!-- Modal-style Card -->
    <div class="w-full max-w-xl bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden">

        <!-- Header -->
        <div class="bg-green-800 text-white p-6 text-center">
            <h1 class="text-2xl font-bold mb-1 flex items-center justify-center gap-2">
                <span>🎓</span> Alumni Tracing Form
            </h1>
            <p class="text-green-100 opacity-80 text-sm">Sports Development Office • Athlete Employment Update</p>
        </div>

        <!-- Form Content -->
        <div class="p-6 space-y-6">

            <!-- Alerts -->
            @if(session('success'))
                <div class="bg-green-100 text-green-900 p-3 rounded-md text-sm border border-green-300">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 text-red-900 p-3 rounded-md text-sm border border-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-blue-50 border-l-4 border-blue-600 p-3 rounded-md text-blue-800 text-sm">
                <strong>Hello, UC Alumni!</strong> Please update your employment details below to keep your varsity records up to date.
            </div>

            <!-- Form -->
            <form action="{{ route('alumni.form.store') }}" method="POST" class="space-y-4" id="alumniForm">
                @csrf

                <input type="hidden" name="classification" value="Alumni">
                
                <div class="space-y-3">
                    <h2 class="text-gray-800 font-semibold border-b border-gray-300 pb-1">Personal Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="text-gray-700 text-sm">Student ID <span class="text-red-600">*</span></label>
                            <input type="text" id="student_id" name="student_id" value="{{ old('student_id') }}" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600 font-mono"
                                placeholder="XX-XXXX-XXX" required oninput="formatStudentID(this)" maxlength="11">
                            <span id="studentIdError" class="text-red-500 text-xs hidden font-bold mt-1">Must be 11 chars</span>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-gray-700 text-sm">Email <span class="text-red-600">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600" required oninput="validateEmail(this)">
                            <span id="emailError" class="text-red-500 text-xs hidden font-bold mt-1">Enter a valid email</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                        <div>
                            <label class="text-gray-700 text-sm">First Name <span class="text-red-600">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600" required>
                        </div>
                        <div>
                            <label class="text-gray-700 text-sm">Last Name <span class="text-red-600">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                        <div>
                            <label class="text-gray-700 text-sm">Contact Number <span class="text-red-600">*</span></label>
                            <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600 font-mono" placeholder="09XXXXXXXXX" required oninput="formatContactNumber(this)" maxlength="11">
                            <span id="contactError" class="text-red-500 text-xs hidden font-bold mt-1">Must start with 09 and be 11 digits</span>
                        </div>
                        <div>
                            <label class="text-gray-700 text-sm">Year Graduated <span class="text-red-600">*</span></label>
                            <input type="number" name="year_graduated" value="{{ old('year_graduated') }}" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600" placeholder="e.g. 2023" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="text-gray-700 text-sm">Sport Event Played <span class="text-red-600">*</span></label>
                        <select name="sport_event" class="form-select w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600" required>
                            <option value="">Select Sport...</option>
                            @foreach(\App\Models\Sport::orderBy('name', 'asc')->get() as $sport)
                                <option value="{{ str_replace(' ', '_', $sport->name) }}" {{ old('sport_event') == str_replace(' ', '_', $sport->name) ? 'selected' : '' }}>
                                    {{ $sport->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Employment Status -->
                <div class="space-y-3 mt-6">
                    <h2 class="text-gray-800 font-semibold border-b border-gray-300 pb-1">Current Employment Status</h2>
                    <div class="mt-3">
                        <label class="text-gray-700 text-sm">Current Job Title / Position <span class="text-red-600">*</span></label>
                        <input type="text" name="current_work" value="{{ old('current_work') }}" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600" placeholder="e.g. Service Crew, IT Associate" required>
                    </div>
                    <div class="mt-3">
                        <label class="text-gray-700 text-sm">Current Company / Employer <span class="text-red-600">*</span></label>
                        <input type="text" name="current_company" value="{{ old('current_company') }}" class="w-full p-2 rounded-md border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-600 focus:border-green-600" placeholder="e.g. McDonald's, Accenture" required>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-3 mt-6">
                    <button type="submit" id="alumniSubmitBtn" class="w-full md:w-auto bg-green-700 hover:bg-green-800 text-white py-2.5 px-6 rounded-md font-semibold transition opacity-50 cursor-not-allowed" disabled>Submit Alumni Record</button>
                    <a href="{{ url('/') }}" class="w-full md:w-auto text-green-800 hover:text-green-600 text-center py-2.5">Cancel</a>
                </div>
            </form>
        </div>
    </div>

<script>
    let isStudentIdValid = false;
    let isEmailValid = false;
    let isContactValid = false;

    function checkFormValidity() {
        const submitBtn = document.getElementById('alumniSubmitBtn');
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
</html>