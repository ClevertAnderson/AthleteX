@extends('layouts.app')

@section('title', 'Student Athletes')

@section('content')
    <div class="space-y-6 w-full">
    <div class="bg-white p-6 flex items-center justify-between">
        <div class="flex-1 text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-0">Student Athlete</h1>
        </div>
        <div>
            <a href="{{ route('student.athletes') }}" 
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                Student Athlete List
            </a>
        </div>
    </div>

    <!-- Search & Action Buttons Spacing -->
    <div class="flex flex-wrap items-center justify-start w-full p-4 gap-4">
        
        <!-- Search Section -->
        <div class="flex items-center space-x-2">
            <label class="text-gray-700 font-medium whitespace-nowrap" for="search">Search</label>
            <div class="relative">
                <input type="text" id="search" name="search" placeholder="Enter full name"
                    class="w-64 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600"
                    autocomplete="off">
                <!-- Dropdown -->
                <div id="searchResults" class="absolute top-full left-0 z-50 w-full mt-1 bg-white border border-gray-200 rounded shadow-xl hidden max-h-60 overflow-y-auto"></div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2">
            <button id="addNewBtn" type="button" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer shadow-sm">
                + Add New Athlete
            </button>

            <button id="saveBtn" type="button" class="hidden px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 transition cursor-pointer shadow-sm">
                Save Athlete
            </button>

            <button id="editBtn" type="button" class="hidden px-4 py-2 rounded bg-yellow-500 text-white hover:bg-yellow-600 transition cursor-pointer shadow-sm">
                Edit Athlete
            </button>

            <button id="updateBtn" type="button" class="hidden px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer shadow-sm">
                Update Athlete
            </button>

            <button id="cancelBtn" type="button" onclick="clearAthleteData()" class="hidden px-5 py-2 rounded bg-gray-200 text-gray-800 hover:bg-gray-300 transition cursor-pointer font-medium shadow-sm border border-gray-300">
                Cancel
            </button>
        </div>
    </div>

    <!-- TABS NAVIGATION -->
    <nav class="mb-6 w-full overflow-x-auto h-8">
            <ul class="flex space-x-2 min-w-max">
                <li id="tab-general-info">
                    <a href="#general-info" 
                    class="tab-link whitespace-nowrap px-4 py-2 text-gray-700 font-medium border-b-2 border-transparent hover:border-green-600 hover:text-green-600 transition">
                        General Information
                    </a>
                </li>
                <li id="tab-achievements">
                    <a href="#achievements" 
                    class="tab-link whitespace-nowrap px-4 py-2 text-gray-700 font-medium border-b-2 border-transparent hover:border-green-600 hover:text-green-600 transition">
                        Achievements
                    </a>
                </li>
                <li id="tab-academic-evaluation">
                    <a href="#academic-evolution" 
                    class="tab-link whitespace-nowrap px-4 py-2 text-gray-700 font-medium border-b-2 border-transparent hover:border-green-600 hover:text-green-600 transition">
                        Academic Evaluation
                    </a>
                </li>
                <li id="tab-fees-discounts">
                    <a href="#fees-discounts" 
                    class="tab-link whitespace-nowrap px-4 py-2 text-gray-700 font-medium border-b-2 border-transparent hover:border-green-600 hover:text-green-600 transition">
                        Fees and Discounts
                    </a>
                </li>
                <li id="tab-work-history">
                    <a href="#work-history" 
                    class="tab-link whitespace-nowrap px-4 py-2 text-gray-700 font-medium border-b-2 border-transparent hover:border-green-600 hover:text-green-600 transition">
                        Work History
                    </a>
                </li>
            </ul>
        </nav>

    <hr class="border-t-2 border-gray-400 my-2 w-[100%]">

    <div id="tab-content" class="bg-white p-6 rounded shadow w-full relative">

        <div id="general-info" class="tab-pane">
            <div class="flex items-stretch gap-6">
                <form id="athleteForm" method="POST" action="{{ route('athletes.store') }}" class="student-form flex items-stretch gap-6 w-full" autocomplete="off" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="_method" id="_method" value="POST">
                    <input type="hidden" name="selected_athlete_id" id="selected_athlete_id" value="">

                    <!-- LEFT SIDE: Main Form -->
                    <div class="flex-1 gap-4 mb-6">

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="flex items-center">
                                <label for="last_name" class="w-1/3 text-gray-700 font-medium">Last Name</label>
                                <input type="text" id="last_name" name="last_name" placeholder="Enter last name"
                                    oninput="this.value = this.value.replace(/[^a-zA-Z\s\-\.]/g, '')"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label for="first_name" class="w-1/3 text-gray-700 font-medium">First Name & MI</label>
                                <input type="text" id="first_name" name="first_name" placeholder="Enter first name"
                                    oninput="this.value = this.value.replace(/[^a-zA-Z\s\-\.]/g, '')"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>
                            <div class="flex flex-col w-full">
                                <div class="flex items-center">
                                    <label for="student_id" class="w-1/3 text-gray-700 font-medium">Student ID</label>
                                    <input type="text" id="student_id" name="student_id" placeholder="XX-XXXX-XXX"
                                        oninput="formatStudentID(this)" maxlength="11"
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600 font-mono tracking-wider">
                                </div>
                                <div class="flex justify-end">
                                    <span id="studentIdError" class="text-red-500 text-xs hidden w-2/3 text-left mt-1 font-bold">
                                        ⚠️ Format must be XX-XXXX-XXX (11 characters)
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="flex items-center">
                            <label class="w-1/3 text-gray-700 font-medium">Gender</label>
                            <select id="gender" name="gender"
                                class="w-2/3 border border-gray-300 rounded px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-green-600">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                            <div class="flex items-center">
                                <label class="w-1/3 text-gray-700 font-medium">Birthdate</label>
                                <input type="date" id="birthdate" name="birthdate"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label class="w-1/3 text-gray-700 font-medium">Age</label>
                                <input type="number" id="age" name="age" placeholder="Enter Age"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                    min="15" max="35" title="Age must be a realistic number between 15 and 35."
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="flex items-center">
                                <label class="w-1/3 text-gray-700 font-medium">Blood Type</label>
                                <input type="text" id="blood_type" name="blood_type" placeholder="Enter Blood Type"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label class="w-1/3 text-gray-700 font-medium">Course</label>
                                <input type="text" id="course" name="course" placeholder="Enter Course"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label class="w-1/3 text-gray-700 font-medium">Year/Level</label>
                                <input type="text" id="year_level" name="year_level" placeholder="Enter Year/Level"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="flex items-center">
                                <label for="email" class="w-1/3 text-gray-700 font-medium">Email Address</label>
                                <input type="text" id="email" name="email" placeholder="Enter Email Address"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label for="facebook" class="w-1/3 text-gray-700 font-medium">Facebook Link</label>
                                <input type="text" id="facebook" name="facebook" placeholder="Enter Facebook Account Link"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label for="marital_status" class="w-1/3 text-gray-700 font-medium">Marital Status</label>
                                <select id="marital_status" name="marital_status"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-green-600">
                                    <option value="Select Marital Status">Select Marital Status</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Divorced">Divorced</option>
                                    <option value="Separated">Separated</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="flex items-center">
                                <label for="contact_number" class="w-1/3 text-gray-700 font-medium">Contact No.</label>
                                <input type="text" id="contact_number" name="contact_number" placeholder="e.g., 09123456789"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                    maxlength="11" minlength="11" pattern="^09[0-9]{9}$" 
                                    title="Contact number must start with 09 and be exactly 11 digits long."
                                    class="contact-number w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="col-span-2 flex items-center">
                                <label for="address" class="w-1/6 text-gray-700 font-medium">Address</label>
                                <input type="text" id="address" name="address" placeholder="Enter Address"
                                    class="flex-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="flex items-center">
                                <label for="city_municipality" class="w-1/3 text-gray-700 font-medium">City / Municipality</label>
                                <input type="text" id="city_municipality" name="city_municipality" placeholder="Enter City/Municipality"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label for="province_state" class="w-1/3 text-gray-700 font-medium">Province / State</label>
                                <input type="text" id="province_state" name="province_state" placeholder="Enter Province/State"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label for="zip_code" class="w-1/3 text-gray-700 font-medium">Zip Code</label>
                                <input type="text" id="zip_code" name="zip_code" placeholder="Enter Zip Code"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                    maxlength="4" minlength="4" pattern="^[0-9]{4}$" 
                                    title="Philippine ZIP codes must be exactly 4 digits."
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>
                        </div>

                        <hr class="border-t-2 border-gray-400 my-2 w-[100%]">

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="flex items-center gap-2">
                                <label for="emergency_person" class="w-24 text-gray-700 font-medium">
                                    Emergency Contact Person
                                </label>
                                <input type="text" id="emergency_person" name="emergency_person"
                                    placeholder="Enter Contact Person"
                                    oninput="this.value = this.value.replace(/[^a-zA-Z\s\-\.]/g, '')"
                                    class="flex-1 border border-gray-300 rounded px-3 py-2 
                                        focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center gap-2">
                                <label for="emergency_contact" class="w-24 text-gray-700 font-medium">
                                    Contact No.
                                </label>
                                <input type="text" id="emergency_contact" name="emergency_contact"
                                    placeholder="e.g., 09123456789"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                    maxlength="11" minlength="11" pattern="^09[0-9]{9}$" 
                                    title="Emergency contact number must start with 09 and be exactly 11 digits long."
                                    class="contact-number flex-1 border border-gray-300 rounded px-3 py-2 
                                        focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>
                        </div>

                        <hr class="border-t-2 border-gray-400 my-2 w-[100%]">

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="mb-4">
                                <label class="block text-gray-700 font-bold mb-2">Coach</label>
                                <div id="coachDisplay" class="p-2 border rounded bg-gray-100 text-gray-700">
                                    @php
                                        $currentCoach = $selectedAthlete->coach ?? optional(Auth::user())->coach;
                                    @endphp
                                    {{ $currentCoach ? $currentCoach->coach_first_name . ' ' . $currentCoach->coach_last_name : 'No coach assigned' }}
                                </div>
                            </div>
                            
                            <input type="hidden" name="coach_id" id="coach_id_input" value="{{ $currentCoach ? $currentCoach->id : '' }}">
                            
                            <div class="flex items-center">
                                <label for="date_joined" class="w-1/3 text-gray-700 font-medium">Date Joined (Varsity)</label>
                                <input type="date" id="date_joined" name="date_joined"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label for="term_graduated" class="w-1/3 text-gray-700 font-medium">Term Graduated</label>
                                <input type="text" id="term_graduated" name="term_graduated" placeholder="Enter Term Graduated"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="flex items-center">
                                <label for="asst_coach" class="w-1/3 text-gray-700 font-medium">Asst. Coach</label>
                                <input type="text" id="asst_coach" name="asst_coach" placeholder="Enter Asst. Coach’s Name"
                                    oninput="this.value = this.value.replace(/[^a-zA-Z\s\-\.]/g, '')"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label for="total_unit" class="w-1/3 text-gray-700 font-medium">Total Units</label>
                                <input type="text" id="total_unit" name="total_unit" placeholder="Enter Units Enrolled"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label for="year_graduated" class="w-1/3 text-gray-700 font-medium">Year Graduated</label>
                                <input type="date" id="year_graduated" name="year_graduated" placeholder="Enter Year Graduated"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>
                        </div>

                        <hr class="border-t-2 border-gray-400 my-2 w-[100%]">

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="flex items-center">
                                <label for="tuition_fee" class="w-1/3 text-gray-700 font-medium">Tuition Fee</label>
                                <input type="text" id="tuition_fee" name="tuition_fee" placeholder="Enter Tuition Fee"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label for="misc_fee" class="w-1/3 text-gray-700 font-medium">Misc. Fee</label>
                                <input type="text" id="misc_fee" name="misc_fee" placeholder="Enter Miscellaneous Fee"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label for="other_charges" class="w-1/3 text-gray-700 font-medium">Other Charges</label>
                                <input type="text" id="other_charges" name="other_charges" placeholder="Enter Other Charges"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="flex items-center">
                                <label for="total_assessment" class="w-1/3 text-gray-700 font-medium">Total Assessment</label>
                                <input type="text" id="total_assessment" name="total_assessment" placeholder="Enter Total Assessment"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label for="total_discount" class="w-1/3 text-gray-700 font-medium">Total Discount</label>
                                <input type="text" id="total_discount" name="total_discount" placeholder="Enter Total Discount"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label for="balance" class="w-1/3 text-gray-700 font-medium">Balance</label>
                                <input type="text" id="balance" name="balance" placeholder="Enter Balance"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>
                        </div>

                        <hr class="border-t-2 border-gray-400 my-2 w-[100%]">

                        <!-- Working Student Section -->
                        <div class="grid grid-cols-2 gap-4 mb-4 bg-gray-50 p-4 rounded border border-gray-200 shadow-sm">
                            <div class="col-span-2 mb-1 border-b pb-2">
                                <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">
                                    <i class="bi bi-briefcase-fill me-1 text-green-700"></i> Working Student / Job Info (Trabaho Dati)
                                </h3>
                            </div>
                            <div class="flex items-center">
                                <label for="current_work" class="w-1/3 text-gray-700 font-medium text-sm">Job Position</label>
                                <input type="text" id="current_work" name="current_work" placeholder="e.g., Service Crew (Leave blank if none)"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>

                            <div class="flex items-center">
                                <label for="current_company" class="w-1/3 text-gray-700 font-medium text-sm">Company Name</label>
                                <input type="text" id="current_company" name="current_company" placeholder="e.g., McDonald's"
                                    class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                            </div>
                        </div>

                        <hr class="border-t-2 border-gray-400 my-2 w-[100%]">
                    </div>

                    <!-- RIGHT SIDE WRAPPER -->
                    <div class="w-[350px] flex-shrink-0 flex flex-col">
                        <div class="bg-gray-100 rounded-lg p-4 shadow-inner flex flex-col h-full">
                            <div class="flex flex-col items-center border border-dashed border-gray-400 rounded-lg p-3 bg-white mb-4">  
                                <span id="selected_name" class="mt-2 text-gray-800 font-semibold text-center">
                                    {{ $selectedAthlete->full_name ?? 'No athlete selected' }}
                                </span>
                            </div>
                            
                            <div class="flex flex-col items-center border border-dashed border-gray-400 rounded-lg p-3 bg-white mb-4">
                                <div class="w-80 h-96 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 text-sm relative overflow-hidden">
                                    <img id="picturePreview" class="absolute inset-0 w-full h-full object-cover hidden" />
                                    <span id="noPictureText" class="text-gray-500 text-sm">No Picture</span>
                                </div>
                            </div>

                            <div class="space-y-3 flex-1 w-full">
                                <div class="flex items-center">
                                    <label class="w-1/3 text-sm font-medium text-gray-700">Sports Event</label>
                                    <select name="sport_event" class="w-2/3 bg-white border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-green-600" required>
                                        <option value="">Select Sport...</option>
                                        @foreach(\App\Models\Sport::orderBy('name', 'asc')->get() as $sport)
                                            <option value="{{ str_replace(' ', '_', $sport->name) }}">
                                                {{ $sport->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex items-center mt-2">
                                    <label class="w-1/3 text-sm font-medium text-gray-700">Status</label>
                                    <select name="status" class="w-2/3 bg-white border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-green-600">
                                        <option value="">-- Select Status --</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Transfered">Transfered</option>
                                        <option value="Graduated">Graduated</option>
                                    </select>
                                </div>

                                <div class="flex items-center mt-2">
                                    <label class="w-1/3 text-sm font-medium text-gray-700">Classification</label>
                                    <select name="classification" class="w-2/3 bg-white border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-green-600">
                                        <option value="">-- Select Classification --</option>
                                        <option value="Class_A">Class A</option>
                                        <option value="Class_B">Class B</option>
                                        <option value="Class_C">Class C</option>
                                    </select>
                                </div>

                                <div class="flex justify-between mt-6">
                                    <input type="file" id="pictureInput" name="athlete_picture" accept="image/*" class="hidden">
                                    <button type="button" id="addPictureBtn"
                                        class="bg-green-700 text-white font-semibold rounded px-3 py-1 flex items-center gap-1 hover:bg-green-800 transition">
                                        <i class="bi bi-person-plus"></i> Add Picture
                                    </button>
                                    <button type="button" id="clearPictureBtn"
                                        class="bg-green-700 text-white font-semibold rounded px-3 py-1 flex items-center gap-1 hover:bg-green-800 transition">
                                        <i class="bi bi-person-x"></i> Clear Picture
                                    </button>
                                </div>

                                <div class="flex items-center mt-4">
                                    <label class="w-1/3 text-sm font-medium text-gray-700">Date Inactive</label>
                                    <input type="date" name="inactive_date" class="w-2/3 bg-white border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-green-600" autocomplete="off">
                                </div>

                                <!-- PRINT BUTTON -->
                                <button type="button" id="printBtn" onclick="printAthlete()" 
                                    class="hidden w-full mt-6 bg-green-700 text-white font-bold rounded px-3 py-2 shadow hover:bg-green-800 transition flex justify-center items-center gap-2">
                                    🖨️ Print Athlete Record
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Achievements Tab -->
        <div id="achievements" class="tab-pane hidden">
            <div class="space-y-6">
                <div class="bg-white p-6 shadow flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800">🏅 Achievements</h1>
                    <button onclick="toggleAchievementModal(true)"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 shadow">
                        + Add Achievement
                    </button>
                </div>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table id="achievementsTable" class="min-w-full text-sm text-left">
                        <thead class="bg-green-600 text-white text-center">
                            <tr>
                                <th class="px-6 py-3 font-medium">Year</th>
                                <th class="px-6 py-3 font-medium">Month-Day</th>
                                <th class="px-6 py-3 font-medium">Sports Event</th>
                                <th class="px-6 py-3 font-medium">Venue</th>
                                <th class="px-6 py-3 font-medium">Award</th>
                                <th class="px-6 py-3 font-medium">Category</th>
                                <th class="px-6 py-3 font-medium">Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="achievementsTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Academic Evaluation Tab -->
        <div id="academic-evolution" class="tab-pane hidden">
            <div class="space-y-6">
                <div class="bg-white p-6 shadow flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800">Academic Evaluation</h1>
                    <button onclick="toggleAcademicModal(true)"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 shadow">
                        + Add Record
                    </button>
                </div>
                <div>
                    <table class="w-full text-left">
                        <thead class="bg-green-600 text-white text-center">
                            <tr>
                                <th class="py-3">Units Passed</th>
                                <th class="py-3">Units Enrolled</th>
                                <th class="py-3">Percentage</th>
                                <th class="py-3">Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="gradesTable" class="text-gray-700"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Fees and Discounts Tab -->
        <div id="fees-discounts" class="tab-pane hidden">
            <div class="bg-white rounded-lg shadow p-4">
               <div class="bg-white p-6 shadow flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800">Fees and Discounts</h1>
                    <button onclick="toggleFeeModal(true)"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 shadow">
                        + Add Fee / Discount
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300 text-sm text-gray-700">
                        <thead class="bg-green-600 text-white text-center">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">Academic Term and Year</th>
                                <th class="border border-gray-300 px-4 py-2">Total Units Enrolled</th>
                                <th class="border border-gray-300 px-4 py-2">Tuition Fee</th>
                                <th class="border border-gray-300 px-4 py-2">Miscellaneous Fee</th>
                                <th class="border border-gray-300 px-4 py-2">Other Charges</th>
                                <th class="border border-gray-300 px-4 py-2">Total Assessment</th>
                                <th class="border border-gray-300 px-4 py-2">Total Discount</th>
                                <th class="border border-gray-300 px-4 py-2">Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="fees-discounts-table-body"></tbody>
                    </table>
                </div>
            </div>

            <!-- FEE MODAL -->
            <div id="feeModal" class="hidden fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
                <div class="bg-[#2e4e1f] rounded-xl shadow-2xl w-full max-w-lg p-6 relative border-2 border-green-600">
                    <button type="button" onclick="toggleFeeModal(false)" class="absolute top-3 right-4 text-white hover:text-gray-300 text-2xl font-bold">✕</button>
                    <h2 class="text-2xl font-bold mb-6 text-center text-white border-b border-green-700 pb-2">Add Fee / Discount</h2>

                    <form id="feeForm" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-white font-medium mb-1 text-sm">Academic Term/Year</label>
                                <input type="text" name="academic_year" placeholder="Ex: 2025-2026" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>
                            <div>
                                <label class="block text-white font-medium mb-1 text-sm">Total Units Enrolled</label>
                                <input type="number" name="total_units" placeholder="0" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>
                            <div>
                                <label class="block text-white font-medium mb-1 text-sm">Tuition Fee</label>
                                <input type="number" name="tuition_fee" placeholder="0.00" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>
                            <div>
                                <label class="block text-white font-medium mb-1 text-sm">Miscellaneous Fee</label>
                                <input type="number" name="miscellaneous_fee" placeholder="0.00" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>
                            <div>
                                <label class="block text-white font-medium mb-1 text-sm">Other Charges</label>
                                <input type="number" name="other_charges" placeholder="0.00" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>
                            <div>
                                <label class="block text-white font-medium mb-1 text-sm">Total Assessment</label>
                                <input type="number" name="total_assessment" placeholder="0.00" class="w-full bg-gray-200 text-gray-900 border border-gray-400 rounded px-3 py-2 text-sm focus:outline-none font-bold" readonly>
                            </div>
                            <div>
                                <label class="block text-white font-medium mb-1 text-sm">Total Discount</label>
                                <input type="number" name="total_discount" placeholder="0.00" class="w-full bg-gray-200 text-gray-900 border border-gray-400 rounded px-3 py-2 text-sm focus:outline-none font-bold" readonly>
                            </div>
                            <div>
                                <label class="block text-white font-medium mb-1 text-sm">Classification</label>
                                <select name="classification" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                    <option value="">-- Select Classification --</option>
                                    <option value="Class_A">Class A (100% Discount)</option>
                                    <option value="Class_B">Class B (75% Discount)</option>
                                    <option value="Class_C">Class C (50% Discount)</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-white font-medium mb-1 text-sm">Remarks</label>
                                <select name="remarks" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                    <option value="">Select Status</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Waived">Waived</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="w-full mt-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition shadow-md">
                            Save Record
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Work History Tab -->
        <div id="work-history" class="tab-pane hidden">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="bg-white p-6 shadow flex items-center justify-between border-b-4 border-green-600">
                    <h1 class="text-2xl font-bold text-gray-800"><i class="bi bi-person-workspace text-green-700 me-2"></i> Work History</h1>
                    <button onclick="toggleWorkModal(true)"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 shadow font-semibold">
                        + Add Work Record
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300 text-sm text-gray-700 mt-4">
                        <thead class="bg-gray-100 text-gray-700 text-center uppercase tracking-wider text-xs">
                            <tr>
                                <th class="border px-4 py-3">Year / Term</th>
                                <th class="border px-4 py-3">Date Hired</th>
                                <th class="border px-4 py-3">Job Position</th>
                                <th class="border px-4 py-3">Company / Employer</th>
                                <th class="border px-4 py-3">Status / Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="workTable" class="text-gray-700 text-center"></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- PRINT PREVIEW MODAL -->
    <div id="printModal" class="hidden items-center justify-center p-4" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.85); z-index: 999999 !important;">
        <div class="bg-gray-300 rounded-xl shadow-2xl flex flex-col relative overflow-hidden border-4 border-green-700" style="width: 100%; max-width: 1000px; height: 90vh;">
            <div class="bg-green-700 text-white px-6 py-4 flex justify-between items-center z-10 shadow-md">
                <h2 class="text-xl font-bold tracking-wide">🖨️ Document Print Preview</h2>
                <div class="space-x-3">
                    <button onclick="triggerIframePrint()" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded font-bold shadow transition border border-blue-400">
                        Print Document
                    </button>
                    <button onclick="togglePrintModal(false)" class="bg-gray-600 hover:bg-gray-500 text-white px-5 py-2 rounded font-bold shadow transition border border-gray-400">
                        Close
                    </button>
                </div>
            </div>
            <div class="flex-1 w-full bg-gray-500 p-6 flex justify-center relative shadow-inner" style="overflow-y: auto;">
                <iframe id="printIframe" class="bg-white shadow-2xl border border-gray-300 rounded-sm" style="width: 100%; max-width: 8.5in; height: 100%; min-height: 11in;" src=""></iframe>
            </div>
        </div>
    </div>
    
<script>
document.addEventListener('DOMContentLoaded', () => {
    
    const byId = id => document.getElementById(id);
    const q = sel => document.querySelector(sel);

    window.newAthleteData = { generalInfo: {}, achievements: [], academicRecords: [], fees: [], workHistory: [] };

    const getAchievementsTbody = () => byId('achievementsTableBody') || q('#achievements table tbody');
    const getGradesTbody = () => byId('gradesTable') || q('#academic-evolution table tbody');
    const getFeesTbody = () => byId('fees-discounts-table-body') || q('#fees-discounts table tbody');
    const getWorkTbody = () => byId('workTable') || q('#work-history table tbody');

    // TAB SWITCHING
    const tabs = document.querySelectorAll('.tab-link');
    const contents = document.querySelectorAll('.tab-pane');    

    const defaultTab = document.querySelector('.tab-link[href="#general-info"]');
    const defaultContent = document.getElementById('general-info');

    if (defaultTab && defaultContent) {
        tabs.forEach(t => t.classList.remove('border-b-2', 'border-green-600', 'text-green-600'));
        contents.forEach(c => c.classList.add('hidden'));
        defaultTab.classList.add('border-b-2', 'border-green-600', 'text-green-600');
        defaultContent.classList.remove('hidden');
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', e => {
            e.preventDefault();
            tabs.forEach(t => t.classList.remove('border-b-2', 'border-green-600', 'text-green-600'));
            contents.forEach(c => c.classList.add('hidden'));
            tab.classList.add('border-b-2', 'border-green-600', 'text-green-600');
            const target = document.querySelector(tab.getAttribute('href'));
            if (target) target.classList.remove('hidden');
        });
    });

    // 🚀 NEW LOGIC: Dynamic Profile Hiding
    function setTabsForActive() {
        if(byId('tab-achievements')) byId('tab-achievements').style.display = 'block';
        if(byId('tab-academic-evaluation')) byId('tab-academic-evaluation').style.display = 'block';
        if(byId('tab-fees-discounts')) byId('tab-fees-discounts').style.display = 'block';
        if(byId('tab-work-history')) byId('tab-work-history').style.display = 'none'; // Hide Work History

        // Force user to general info tab if they were stuck on a hidden tab
        const workHistoryPane = byId('work-history');
        if(workHistoryPane && !workHistoryPane.classList.contains('hidden')) {
            const generalTab = document.querySelector('.tab-link[href="#general-info"]');
            if(generalTab) generalTab.click();
        }
    }

    function setTabsForInactive() {
        if(byId('tab-achievements')) byId('tab-achievements').style.display = 'block';
        if(byId('tab-academic-evaluation')) byId('tab-academic-evaluation').style.display = 'none';
        if(byId('tab-fees-discounts')) byId('tab-fees-discounts').style.display = 'none';
        if(byId('tab-work-history')) byId('tab-work-history').style.display = 'block'; // Show Work History

        // Force user to general info tab if they were stuck on a hidden tab
        const acadPane = byId('academic-evolution');
        const feesPane = byId('fees-discounts');
        if((acadPane && !acadPane.classList.contains('hidden')) || (feesPane && !feesPane.classList.contains('hidden'))) {
            const generalTab = document.querySelector('.tab-link[href="#general-info"]');
            if(generalTab) generalTab.click();
        }
    }

    window.formatStudentID = function(input) {
        let val = input.value.replace(/\D/g, ''); 
        if (val.length > 2) val = val.slice(0, 2) + '-' + val.slice(2);
        if (val.length > 7) val = val.slice(0, 7) + '-' + val.slice(7);
        input.value = val;
        const err = document.getElementById('studentIdError');
        if (err) {
            if (val.length > 0 && val.length < 11) {
                err.classList.remove('hidden');
                input.classList.add('border-red-500', 'ring-red-500');
            } else {
                err.classList.add('hidden');
                input.classList.remove('border-red-500', 'ring-red-500');
            }
        }
    };

    function lockFormInitial() {
        const form = byId('athleteForm');
        if (form) {
            form.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.type !== 'hidden' && el.id !== 'search') el.disabled = true;
            });
        }
        byId('addNewBtn')?.classList.remove('hidden');
        byId('saveBtn')?.classList.add('hidden');
        byId('editBtn')?.classList.add('hidden');
        byId('updateBtn')?.classList.add('hidden');
        byId('cancelBtn')?.classList.add('hidden');
        
        setTabsForActive(); // Show default tabs when empty
    }

    function unlockFormForNew() {
        const form = byId('athleteForm');
        if (form) {
            form.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.type !== 'hidden' && el.id !== 'search') el.disabled = false;
            });
        }
        byId('addNewBtn')?.classList.add('hidden');
        byId('saveBtn')?.classList.remove('hidden');
        byId('editBtn')?.classList.add('hidden');
        byId('updateBtn')?.classList.add('hidden');
        byId('cancelBtn')?.classList.remove('hidden');

        setTabsForActive(); // Only Work History is hidden for new athletes
    }

    function lockFormForViewing() {
        const form = byId('athleteForm');
        if (form) {
            form.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.type !== 'hidden' && el.id !== 'search') el.disabled = true;
            });
        }
        byId('addNewBtn')?.classList.add('hidden');
        byId('saveBtn')?.classList.add('hidden');
        byId('updateBtn')?.classList.add('hidden');
        byId('editBtn')?.classList.remove('hidden');
        byId('cancelBtn')?.classList.remove('hidden');
    }

    function unlockFormForEditing() {
        const form = byId('athleteForm');
        if (form) {
            form.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.type !== 'hidden' && el.id !== 'search') el.disabled = false;
            });
        }
        byId('addNewBtn')?.classList.add('hidden');
        byId('editBtn')?.classList.add('hidden');
        byId('updateBtn')?.classList.remove('hidden');
        byId('cancelBtn')?.classList.remove('hidden');
    }

    window.clearAthleteData = function() {
        const form = byId('athleteForm');
        if (form) {
            form.reset();
            form.setAttribute('action', '{{ route('athletes.store') }}');
            if(byId('_method')) byId('_method').value = 'POST';
            if(byId('selected_athlete_id')) byId('selected_athlete_id').value = '';
        }
        if(byId('search')) byId('search').value = '';
        if(byId('selected_name')) byId('selected_name').textContent = 'No athlete selected';
        if(byId('coachDisplay')) byId('coachDisplay').textContent = 'No coach assigned';
        if(byId('picturePreview')) byId('picturePreview').classList.add('hidden');
        if(byId('noPictureText')) byId('noPictureText').classList.remove('hidden');
        
        if(getAchievementsTbody()) getAchievementsTbody().innerHTML = '';
        if(getGradesTbody()) getGradesTbody().innerHTML = '';
        if(getFeesTbody()) getFeesTbody().innerHTML = '';
        if(getWorkTbody()) getWorkTbody().innerHTML = '';

        window.newAthleteData = { generalInfo: {}, achievements: [], academicRecords: [], fees: [], workHistory: [] };
        byId('printBtn')?.classList.add('hidden');
        lockFormInitial(); // Will also reset the tabs
    };

    lockFormInitial();

    byId('addNewBtn')?.addEventListener('click', unlockFormForNew);
    byId('editBtn')?.addEventListener('click', unlockFormForEditing);
    
    byId('addPictureBtn')?.addEventListener('click', () => byId('pictureInput')?.click());
    byId('clearPictureBtn')?.addEventListener('click', () => {
        const fileInput = byId('pictureInput');
        const preview = byId('picturePreview');
        const noPic = byId('noPictureText');
        if (fileInput) fileInput.value = '';
        if (preview) { preview.src = ''; preview.classList.add('hidden'); }
        if (noPic) noPic.classList.remove('hidden');
    });

    byId('pictureInput')?.addEventListener('change', (e) => {
        if(e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = (ev) => {
                const preview = byId('picturePreview');
                const noPic = byId('noPictureText');
                if (preview) { preview.src = ev.target.result; preview.classList.remove('hidden'); }
                if (noPic) noPic.classList.add('hidden');
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    const updateBase = '{{ url('/athletes') }}';
    const generalForm = byId('athleteForm');

    window.loadAthleteData = function(id) {
        if (!generalForm) return;

        generalForm.setAttribute('action', updateBase + '/' + id);
        if (byId('_method')) byId('_method').value = 'PUT';
        if (byId('selected_athlete_id')) byId('selected_athlete_id').value = id;
        byId('printBtn')?.classList.remove('hidden');

        fetch(updateBase + '/' + id, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(full => {
                lockFormForViewing();

                const status = full.status ? full.status.toLowerCase() : '';
                const classification = full.classification ? full.classification.toLowerCase() : '';
                const isInactiveOrAlumni = status !== 'active' || classification === 'alumni' || classification === 'class_alumni';

                // 🚀 Toggle Tabs dynamically based on data
                if (isInactiveOrAlumni) {
                    setTabsForInactive();
                } else {
                    setTabsForActive();
                }

                for (const key in full) {
                    try {
                        const el = generalForm.querySelector(`[name="${key}"]`);
                        if (!el) continue;
                        
                        if (el.tagName === 'SELECT') {
                            let val = String(full[key]).trim().toLowerCase();
                            let found = false;
                            for(let i=0; i<el.options.length; i++) {
                                if(el.options[i].value.toLowerCase() === val) { el.selectedIndex = i; found = true; break; }
                            }
                            if(!found) {
                                for(let i=0; i<el.options.length; i++) {
                                    if(el.options[i].value.toLowerCase().includes(val)) { el.selectedIndex = i; break; }
                                }
                            }
                        } else if (el.type !== 'file') {
                            el.value = full[key] ?? '';
                        }
                    } catch (err) {}
                }

                if (full.birthdate) {
                    const el = generalForm.querySelector('[name="birthdate"]');
                    if(el) el.value = full.birthdate.split('T')[0];
                }
                if (full.picture_url) {
                    const preview = byId('picturePreview');
                    const noPic = byId('noPictureText');
                    if (preview) { preview.src = full.picture_url; preview.classList.remove('hidden'); }
                    if (noPic) noPic.classList.add('hidden');
                }
                const selectedName = byId('selected_name');
                if (selectedName) selectedName.textContent = full.full_name || full.first_name + ' ' + full.last_name;

                const coachDisplayEl = byId('coachDisplay');
                if (coachDisplayEl) {
                    let coachName = full.coach_name || (full.coach ? `${full.coach.coach_first_name || ''} ${full.coach.coach_last_name || ''}`.trim() : '');
                    coachDisplayEl.textContent = coachName || 'No coach assigned';
                }

                newAthleteData.achievements = full.achievements || [];
                const achTbody = getAchievementsTbody();
                if(achTbody) {
                    achTbody.innerHTML = '';
                    newAthleteData.achievements.forEach((a, idx) => {
                        achTbody.innerHTML += `<tr class="${idx % 2 === 0 ? 'bg-gray-50' : 'bg-white'}"><td class="px-6 py-3">${a.year || ''}</td><td class="px-6 py-3">${a.month_day || ''}</td><td class="px-6 py-3">${a.event || ''}</td><td class="px-6 py-3">${a.venue || ''}</td><td class="px-6 py-3 text-green-700 font-bold">${a.award || ''}</td><td class="px-6 py-3">${a.category || ''}</td><td class="px-6 py-3">${a.remarks || ''}</td></tr>`;
                    });
                }

                newAthleteData.academicRecords = full.academic_evaluations || [];
                const gradesTbody = getGradesTbody();
                if(gradesTbody) {
                    gradesTbody.innerHTML = '';
                    newAthleteData.academicRecords.forEach((r, idx) => {
                        gradesTbody.innerHTML += `<tr class="${idx % 2 === 0 ? 'bg-gray-50' : 'bg-white'}"><td class="px-6 py-3 text-center">${r.passed || ''}</td><td class="px-6 py-3 text-center">${r.enrolled || ''}</td><td class="px-6 py-3 text-center">${r.percentage || ''}</td><td class="px-6 py-3 text-center">${r.remark || ''}</td></tr>`;
                    });
                }

                newAthleteData.fees = full.fees_discounts || [];
                const feesTbody = getFeesTbody();
                if(feesTbody) {
                    feesTbody.innerHTML = '';
                    newAthleteData.fees.forEach((f, idx) => {
                        feesTbody.innerHTML += `<tr class="text-center ${idx % 2 === 0 ? 'bg-gray-50' : 'bg-white'}"><td class="border px-4 py-2">${f.academic_year || ''}</td><td class="border px-4 py-2">${f.total_units || ''}</td><td class="border px-4 py-2">₱${f.tuition_fee || '0'}</td><td class="border px-4 py-2">₱${f.miscellaneous_fee || '0'}</td><td class="border px-4 py-2">₱${f.other_charges || '0'}</td><td class="border px-4 py-2 font-bold text-gray-900">₱${f.total_assessment || '0'}</td><td class="border px-4 py-2 font-bold text-green-700">₱${f.total_discount || '0'}</td><td class="border px-4 py-2">${f.remarks || ''}</td></tr>`;
                    });
                }

                newAthleteData.workHistory = full.work_histories || [];
                const workTbody = getWorkTbody();
                if(workTbody) {
                    workTbody.innerHTML = '';
                    newAthleteData.workHistory.forEach(w => {
                        workTbody.innerHTML += `<tr class="bg-white"><td class="border px-4 py-2">${w.year || ''}</td><td class="border px-4 py-2">${w.date || ''}</td><td class="border px-4 py-2">${w.position || ''}</td><td class="border px-4 py-2">${w.company || ''}</td><td class="border px-4 py-2">${w.remarks || ''}</td></tr>`;
                    });
                }
            })
            .catch(err => console.error("Error loading athlete:", err));
    };

    // SEARCH LOGIC
    const searchInput = byId('search');
    const resultsBox = byId('searchResults');
    if (searchInput && resultsBox) {
        let timer = null;
        const searchUrl = '{{ route('athletes.search') }}';

        searchInput.addEventListener('input', (e) => {
            const v = e.target.value.trim();
            if (timer) clearTimeout(timer);
            
            if (!v) { 
                resultsBox.innerHTML = ''; 
                resultsBox.classList.add('hidden'); 
                window.clearAthleteData(); 
                return; 
            }

            timer = setTimeout(() => {
                fetch(searchUrl + '?q=' + encodeURIComponent(v), { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(items => {
                        resultsBox.innerHTML = '';
                        if(items.length === 0) { resultsBox.classList.add('hidden'); return; }
                        
                        items.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm';
                            div.textContent = (item.full_name || item.first_name + ' ' + item.last_name) + ' (' + item.student_id + ')';
                            div.onclick = () => {
                                window.loadAthleteData(item.id);
                                resultsBox.innerHTML = '';
                                resultsBox.classList.add('hidden');
                                searchInput.value = item.first_name + ' ' + item.last_name;
                            };
                            resultsBox.appendChild(div);
                        });
                        resultsBox.classList.remove('hidden');
                    });
            }, 300);
        });
    }

    const urlParams = new URLSearchParams(window.location.search);
    const editId = urlParams.get('id');
    if (editId) window.loadAthleteData(editId);

    window.toggleModal = (id, show) => { const m = byId(id); if(m) m.classList.toggle('hidden', !show); };
    window.toggleAchievementModal = (s) => toggleModal('AchievementModal', s);
    window.toggleAcademicModal = (s) => toggleModal('academicModal', s);
    window.toggleFeeModal = (s) => toggleModal('feeModal', s);
    window.toggleWorkModal = (s) => toggleModal('workModal', s);

    // FEE MODAL & CALCULATOR LOGIC
    const feeForm = document.getElementById('feeForm');
    if (feeForm) {
        feeForm.addEventListener('input', (e) => {
            if (['tuition_fee', 'miscellaneous_fee', 'other_charges', 'classification'].includes(e.target.name)) {
                const tuition = parseFloat(feeForm.querySelector('[name="tuition_fee"]').value) || 0;
                const misc = parseFloat(feeForm.querySelector('[name="miscellaneous_fee"]').value) || 0;
                const other = parseFloat(feeForm.querySelector('[name="other_charges"]').value) || 0;
                
                const assessment = tuition + misc + other;
                feeForm.querySelector('[name="total_assessment"]').value = assessment.toFixed(2);
                
                const classification = feeForm.querySelector('[name="classification"]').value;
                let rate = 0;
                if (classification === 'Class_A') rate = 1.00;
                if (classification === 'Class_B') rate = 0.75;
                if (classification === 'Class_C') rate = 0.50;
                
                feeForm.querySelector('[name="total_discount"]').value = (tuition * rate).toFixed(2);
            }
        });

        feeForm.onsubmit = async (e) => { 
            e.preventDefault(); 
            const d = Object.fromEntries(new FormData(feeForm).entries()); 
            const selectedId = document.getElementById('selected_athlete_id').value;

            if (selectedId) {
                d.athlete_id = selectedId;
                try {
                    const res = await fetch('{{ url("/fees-discounts") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify(d)
                    });
                    
                    const result = await res.json();
                    if (result.success) {
                        feeForm.reset(); 
                        window.toggleFeeModal(false);
                        
                        if (typeof window.loadAthleteData === 'function') window.loadAthleteData(selectedId);
                        
                        const generalTab = document.querySelector('.tab-link[href="#general-info"]');
                        if(generalTab) generalTab.click();
                    } else {
                        alert('Validation failed. Please ensure inputs are correct.');
                    }
                } catch (err) {
                    alert('Error securely saving fee to the database.');
                }
            } else {
                alert('Please search for and select an athlete first before adding a fee!');
            }
        };
    }

    const achForm = byId('achievementForm');
    if(achForm) achForm.addEventListener('submit', e => { e.preventDefault(); const d = {year: byId('year').value, monthDay: byId('month_day').value, event: byId('event').value, venue: byId('venue').value, award: byId('award').value, category: byId('category').value, remarks: byId('remarks').value}; newAthleteData.achievements.push(d); getAchievementsTbody().innerHTML += `<tr><td class="px-6 py-3">${d.year}</td><td class="px-6 py-3">${d.monthDay}</td><td class="px-6 py-3">${d.event}</td><td class="px-6 py-3">${d.venue}</td><td class="px-6 py-3 text-green-700">${d.award}</td><td class="px-6 py-3">${d.category}</td><td class="px-6 py-3">${d.remarks}</td></tr>`; achForm.reset(); toggleAchievementModal(false); });

    const acForm = byId('academicForm');
    if(acForm) acForm.addEventListener('submit', e => { e.preventDefault(); const d = {passed: acForm.querySelector('[name="Passed"]').value, enrolled: acForm.querySelector('[name="enrolled"]').value, percentage: acForm.querySelector('[name="percentage"]').value, remark: acForm.querySelector('[name="remark"]').value}; newAthleteData.academicRecords.push(d); getGradesTbody().innerHTML += `<tr><td class="px-6 py-3 text-center">${d.passed}</td><td class="px-6 py-3 text-center">${d.enrolled}</td><td class="px-6 py-3 text-center">${d.percentage}</td><td class="px-6 py-3 text-center">${d.remark}</td></tr>`; acForm.reset(); toggleAcademicModal(false); });

    const workForm = byId('workForm');
    if(workForm) workForm.addEventListener('submit', e => { e.preventDefault(); const d = Object.fromEntries(new FormData(workForm).entries()); newAthleteData.workHistory.push(d); getWorkTbody().innerHTML += `<tr class="bg-white"><td class="border px-4 py-2">${d.year}</td><td class="border px-4 py-2">${d.date}</td><td class="border px-4 py-2">${d.position}</td><td class="border px-4 py-2">${d.company}</td><td class="border px-4 py-2">${d.remarks}</td></tr>`; workForm.reset(); toggleWorkModal(false); });

    function performFinalSave(e) {
        e.preventDefault();
        if (generalForm) {
            generalForm.querySelectorAll('input[name], select[name], textarea[name]').forEach(input => {
                newAthleteData.generalInfo[input.name] = input.value;
            });
        }
        
        const selectedId = byId('selected_athlete_id') ? byId('selected_athlete_id').value : null;
        const endpoint = selectedId ? (updateBase + '/' + selectedId) : updateBase;
        const method = selectedId ? 'PUT' : 'POST';

        fetch(endpoint, {
            method: method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify(newAthleteData)
        })
        .then(async r => { const data = await r.json(); if(!r.ok) throw data; alert('Athlete saved successfully!'); window.location.href = "{{ route('athletes.index') }}"; })
        .catch(err => {
            if (err.errors) {
                let msg = "Please fix the following errors:\n\n";
                for (const f in err.errors) msg += `❌ ${err.errors[f][0]}\n`;
                alert(msg);
            } else if (err.message) alert('Error: ' + err.message);
            else alert('Unexpected error.');
        });
    }

    byId('saveBtn')?.addEventListener('click', performFinalSave);
    byId('updateBtn')?.addEventListener('click', performFinalSave);

    const printModalEl = document.getElementById('printModal');
    if (printModalEl) document.body.appendChild(printModalEl);
});

window.printAthlete = function() {
    const athleteId = document.getElementById('selected_athlete_id').value;
    if (athleteId) { document.getElementById('printIframe').src = '/athlete/' + athleteId + '/print'; togglePrintModal(true); } 
    else alert('Please select an athlete to print first!');
};
window.togglePrintModal = function(show) {
    const m = document.getElementById('printModal');
    if(m) { m.classList.toggle('hidden', !show); m.classList.toggle('flex', show); }
};
window.triggerIframePrint = function() {
    const iframe = document.getElementById('printIframe');
    iframe.contentWindow.focus(); iframe.contentWindow.print();
};
</script>
@endsection