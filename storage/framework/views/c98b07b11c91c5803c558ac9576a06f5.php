<?php $__env->startSection('title', 'Coaches'); ?>

<?php $__env->startSection('content'); ?>

    <!-- Force clean tab styles & hover effects, overriding any global CSS conflicts -->
    <style>
        a.coach-tab-link {
            color: #4b5563 !important; /* Default gray text */
            text-decoration: none !important; /* Remove any underline */
            border-bottom: 2px solid transparent !important; /* Invisible border for spacing */
            transition: all 0.3s ease !important; /* Smooth effect */
        }
        
        /* The exact hover effect from Student Athlete tabs */
        a.coach-tab-link:hover {
            color: #16a34a !important; /* Turn text green */
            border-bottom: 2px solid #16a34a !important; /* Show green border */
        }

        /* The style when a tab is currently active */
        a.coach-tab-link.active-tab {
            color: #16a34a !important;
            border-bottom: 2px solid #16a34a !important;
        }
    </style>

    <div class="space-y-6 w-full pt-20 md:pt-6 p-4">

        <!-- Page Header -->
        <div class="bg-white p-6 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm rounded-lg">
            <div class="flex-1 text-center md:text-left flex justify-center md:justify-start items-center gap-2 w-full">
                <i class="bi bi-person text-3xl text-gray-800"></i>
                <?php if(auth()->check() && auth()->user()->role === 'coach'): ?>
                    <h1 class="text-3xl font-bold text-gray-800 mb-0">My Profile</h1>
                <?php else: ?>
                    <h1 class="text-3xl font-bold text-gray-800 mb-0">Coaches</h1>
                <?php endif; ?>
            </div>

            <!-- Right Button - Only show for admins -->
            <?php if(!auth()->check() || auth()->user()->role !== 'coach'): ?>
                <div>
                    <a href="<?php echo e(route('coaches.index')); ?>" 
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition inline-block text-sm md:text-base font-semibold">
                        List of Coaches
                    </a>
                </div>
            <?php else: ?>
                <!-- Invisible spacer so the title stays centered for coaches on desktop -->
                <div class="hidden md:block w-[140px]"></div>
            <?php endif; ?>
        </div>

        <!-- Search & Action Buttons Spacing -->
        <div class="flex flex-wrap items-center justify-start w-full p-4 gap-4">
            
            <!-- Search Section - Only for admins -->
            <?php if(!auth()->check() || auth()->user()->role !== 'coach'): ?>
                <div class="flex items-center space-x-2">
                    <label class="text-gray-700 font-medium whitespace-nowrap" for="coach_search">Search</label>
                    <div class="relative">
                        <input type="text" id="coach_search" name="coach_search" placeholder="Enter full name"
                            class="w-64 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600"
                            autocomplete="off" value="">
                        <!-- Dropdown -->
                        <div id="coach_searchResults" class="absolute top-full left-0 z-50 w-full mt-1 bg-white border border-gray-200 rounded shadow-xl hidden max-h-60 overflow-y-auto"></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Action Buttons (Locked Side-by-Side) -->
            <div class="inline-flex flex-row items-center gap-2">
                <?php if(auth()->check() && auth()->user()->role === 'coach'): ?>
                    <!-- COACH USERS -->
                    <?php if(!isset($coach)): ?>
                        <button id="coach_saveBtn" type="button" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 transition cursor-pointer shadow-sm font-semibold whitespace-nowrap">
                            Save My Profile
                        </button>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- ADMIN USERS -->
                    <button id="coach_addNewBtn" type="button" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer shadow-sm font-semibold whitespace-nowrap">
                        + Add New Coach
                    </button>
                    <button id="coach_saveBtn" type="button" class="hidden px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 transition cursor-pointer shadow-sm font-semibold whitespace-nowrap">
                        Save Coach
                    </button>
                <?php endif; ?>
                
                <button id="coach_editBtn" type="button" class="<?php echo e((auth()->check() && auth()->user()->role === 'coach' && isset($coach)) ? '' : 'hidden'); ?> px-4 py-2 rounded bg-yellow-500 text-white hover:bg-yellow-600 transition cursor-pointer shadow-sm font-semibold whitespace-nowrap">
                    Edit Profile
                </button>
                
                <button id="coach_updateBtn" type="button" class="hidden px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer shadow-sm font-semibold whitespace-nowrap">
                    Update Profile
                </button>

                <?php if(!auth()->check() || auth()->user()->role !== 'coach'): ?>
                    <button id="coach_cancelBtn" type="button" class="hidden px-5 py-2 rounded bg-gray-200 text-gray-800 hover:bg-gray-300 transition cursor-pointer font-medium shadow-sm border border-gray-300 whitespace-nowrap">
                        Cancel
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <nav class="mb-6 w-full overflow-x-auto h-12">
            <ul class="flex space-x-2 min-w-max pb-2">
                <li id="tab-coach-general-info">
                    <a href="#coach-general-info" class="coach-tab-link whitespace-nowrap px-4 py-2 font-medium cursor-pointer block">
                        General Information
                    </a>
                </li>
                <li id="tab-coach-achievements">
                    <a href="#coach-achievements" class="coach-tab-link whitespace-nowrap px-4 py-2 font-medium cursor-pointer block">
                        Achievements
                    </a>
                </li>
                <li id="tab-assigned-schedule-athletes">
                    <a href="#assigned-schedule-athletes" class="coach-tab-link whitespace-nowrap px-4 py-2 font-medium cursor-pointer block">
                        Assigned Schedule / Athletes
                    </a>
                </li>
                <li id="tab-expenses-payments">
                    <a href="#expenses-payments" class="coach-tab-link whitespace-nowrap px-4 py-2 font-medium cursor-pointer block">
                        Expenses and Payments
                    </a>
                </li>
                <li id="tab-membership">
                    <a href="#membership" class="coach-tab-link whitespace-nowrap px-4 py-2 font-medium cursor-pointer block">
                        Membership
                    </a>
                </li>
                <li id="tab-seminars">
                    <a href="#seminars" class="coach-tab-link whitespace-nowrap px-4 py-2 font-medium cursor-pointer block">
                        Seminars
                    </a>
                </li>
                <li id="tab-coach-work-history">
                    <a href="#coach-work-history" class="coach-tab-link whitespace-nowrap px-4 py-2 font-medium cursor-pointer block">
                        Work History
                    </a>
                </li>
                <li id="tab-coach-attachments">
                    <a href="#coach-attachments" class="coach-tab-link whitespace-nowrap px-4 py-2 font-medium cursor-pointer block">
                        Attachments
                    </a>
                </li>
            </ul>
        </nav>

        <hr class="border-t-2 border-gray-400 my-2 w-[100%]">

        <!-- Tab Content -->
        <div id="tab-content" class="bg-white p-4 md:p-6 rounded shadow w-full relative">

            <!-- Coach General Info Content -->
            <div id="coach-general-info" class="coach-tab-pane hidden">
                <div class="flex items-stretch gap-6 w-full">
                    <form id="coachForm" method="POST" action="
                        <?php if(auth()->check() && auth()->user()->role === 'coach' && isset($coach)): ?>
                            <?php echo e(route('coaches.update', $coach->id)); ?>

                        <?php else: ?>
                            <?php echo e(route('coaches.store')); ?>

                        <?php endif; ?>
                    " class="coach-form flex flex-col lg:flex-row items-stretch gap-6 w-full" autocomplete="off" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>

                        <?php if(auth()->check() && auth()->user()->role === 'coach' && isset($coach)): ?>
                            <?php echo method_field('PUT'); ?>
                        <?php endif; ?>

                        <input type="hidden" name="_method" id="coach_method" value="POST">
                        <input type="hidden" name="selected_coach_id" id="selected_coach_id" value="<?php echo e(isset($coach) ? $coach->id : ''); ?>">

                        <!-- LEFT SIDE: Main Form -->
                        <div class="flex-1 gap-4 mb-6">

                            <!-- 1st Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="flex items-center">
                                    <label for="coach_last_name" class="w-1/3 text-gray-700 font-medium">Last Name</label>
                                    <input type="text" id="coach_last_name" name="coach_last_name" placeholder="Enter last name" required
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="coach_first_name" class="w-1/3 text-gray-700 font-medium">First Name</label>
                                    <input type="text" id="coach_first_name" name="coach_first_name" placeholder="Enter first name" required
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="coach_middle_initial" class="w-1/3 text-gray-700 font-medium">Middle Initial</label>
                                    <input type="text" id="coach_middle_initial" name="coach_middle_initial" placeholder="Enter middle initial" required
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>
                            </div>

                            <!-- 2nd Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="flex items-center">
                                    <label class="w-1/3 text-gray-700 font-medium">Gender</label>
                                    <select id="coach_gender" name="coach_gender" required
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-green-600">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>

                                <div class="flex items-center">
                                    <label class="w-1/3 text-gray-700 font-medium">Birthdate</label>
                                    <input type="date" id="coach_birthdate" name="coach_birthdate" required
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label class="w-1/3 text-gray-700 font-medium">Age</label>
                                    <input type="number" id="coach_age" name="coach_age" placeholder="Enter Age" required
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>
                            </div>

                            <!-- 3rd Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="flex items-center">
                                    <label class="w-1/3 text-gray-700 font-medium">Blood Type</label>
                                    <input type="text" id="coach_blood_type" name="coach_blood_type" placeholder="Enter Blood Type" required
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label class="w-1/3 text-gray-700 font-medium">Place of Birth</label>
                                    <input type="text" id="coach_place_of_birth" name="coach_place_of_birth" placeholder="Enter Place of Birth" required
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="coach_marital_status" class="w-1/3 text-gray-700 font-medium">Marital Status</label>
                                    <select id="coach_marital_status" name="coach_marital_status" required
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-green-600">
                                        <option value="">Select Marital Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Divorced">Divorced</option>
                                        <option value="Separated">Separated</option>
                                    </select>
                                </div>
                            </div>

                            <!-- 4th Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="flex items-center">
                                    <label for="coach_email" class="w-1/3 text-gray-700 font-medium">Email Address</label>
                                    <input type="text" id="coach_email" name="coach_email" placeholder="Enter Email Address" required
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="coach_facebook" class="w-1/3 text-gray-700 font-medium">Facebook Link</label>
                                    <input type="text" id="coach_facebook" name="coach_facebook" placeholder="Enter Facebook Account Link"
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="coach_tba" class="w-1/3 text-gray-700 font-medium">TBA</label>
                                    <input type="text" id="coach_tba" name="coach_tba" placeholder="TO BE ANNOUNCE" 
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>
                            </div>

                            <!-- 5th Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="flex items-center">
                                    <label for="coach_contact_number" class="w-1/3 text-gray-700 font-medium">Contact No.</label>
                                    <input type="text" id="coach_contact_number" name="coach_contact_number" placeholder="Enter Contact Number" required
                                        class="contact-number w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="col-span-1 md:col-span-2 flex items-center">
                                    <label for="coach_address" class="w-1/6 text-gray-700 font-medium">Address</label>
                                    <input type="text" id="coach_address" name="coach_address" placeholder="Enter Address" required
                                        class="flex-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>
                            </div>

                            <!-- 6th Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="flex items-center">
                                    <label for="coach_city_municipality" class="w-1/3 text-gray-700 font-medium">City / Municipality</label>
                                    <input type="text" id="coach_city_municipality" name="coach_city_municipality" placeholder="Enter City/Municipality" required
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="coach_province_state" class="w-1/3 text-gray-700 font-medium">Province / State</label>
                                    <input type="text" id="coach_province_state" name="coach_province_state" placeholder="Enter Province/State" required
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="coach_zip_code" class="w-1/3 text-gray-700 font-medium">Zip Code</label>
                                    <input type="text" id="coach_zip_code" name="coach_zip_code" placeholder="Enter Zip Code" required
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>
                            </div>

                            <hr class="border-t-2 border-gray-400 my-2 w-[100%]">

                            <!-- 7th Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="flex items-center gap-2">
                                    <label for="coach_emergency_person" class="w-24 text-gray-700 font-medium">
                                        Emergency Contact Person
                                    </label>
                                    <input type="text" id="coach_emergency_person" name="coach_emergency_person" required
                                        placeholder="Enter Contact Person"
                                        class="flex-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center gap-2">
                                    <label for="coach_emergency_contact" class="w-24 text-gray-700 font-medium">
                                        Contact No.
                                    </label>
                                    <input type="text" id="coach_emergency_contact" name="coach_emergency_contact" required
                                        placeholder="Enter Contact Number"
                                        class="contact-number flex-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>
                            </div>

                            <hr class="border-t-2 border-gray-400 my-2 w-[100%]">

                            <!-- 8th Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="flex items-center">
                                    <label for="post_graduate" class="w-1/3 text-gray-700 font-medium">Name of Post Graduate School</label>
                                    <input type="text" id="post_graduate" name="post_graduate" placeholder="Enter Name of Post Graduate School"
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="course_graduated" class="w-1/3 text-gray-700 font-medium">Course Graduated</label>
                                    <input type="text" id="course_graduated" name="course_graduated" placeholder="Enter Course Graduated"
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="coach_year_graduated" class="w-1/3 text-gray-700 font-medium">Year Graduated</label>
                                    <input type="date" id="coach_year_graduated" name="coach_year_graduated" placeholder="Enter Year Graduated"
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>
                            </div>

                            <!-- 9th Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="flex items-center">
                                    <label for="name_collage" class="w-1/3 text-gray-700 font-medium">Name of Collage School</label>
                                    <input type="text" id="name_collage" name="name_collage" placeholder="Enter Name of Collage School"
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="coach_course_graduated" class="w-1/3 text-gray-700 font-medium">Course Graduated</label>
                                    <input type="text" id="coach_course_graduated" name="coach_course_graduated" placeholder="Enter Course Graduated"
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="coach_graduated" class="w-1/3 text-gray-700 font-medium">Year Graduated</label>
                                    <input type="date" id="coach_graduated" name="coach_graduated" placeholder="Enter Year Graduated"
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>
                            </div>

                            <hr class="border-t-2 border-gray-400 my-2 w-[100%]">

                            <!-- 10th Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="flex items-center">
                                    <label for="coach_highschool" class="w-1/3 text-gray-700 font-medium">Name of Highschool</label>
                                    <input type="text" id="coach_highschool" name="coach_highschool" placeholder="Enter Name of Highschool"
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="strand_graduated" class="w-1/3 text-gray-700 font-medium">Strand Graduated</label>
                                    <input type="text" id="strand_graduated" name="strand_graduated" placeholder="Enter Strand Graduated"
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="highschool_graduated" class="w-1/3 text-gray-700 font-medium">Year Graduated</label>
                                    <input type="date" id="highschool_graduated" name="highschool_graduated" placeholder="Enter Year Graduated"
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>
                            </div>

                            <!-- 11th Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="flex items-center">
                                    <label for="date_hired" class="w-1/3 text-gray-700 font-medium">Date Hired</label>
                                    <input type="date" id="date_hired" name="date_hired" placeholder="Enter Date Hired"
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="honorarium_payment" class="w-1/3 text-gray-700 font-medium">Honorarium Payment</label>
                                    <input type="text" id="honorarium_payment" name="honorarium_payment" placeholder="Enter Honorarium Payment"
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="date_resigned" class="w-1/3 text-gray-700 font-medium">Date Resigned</label>
                                    <input type="date" id="date_resigned" name="date_resigned" 
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>
                            </div>

                            <hr class="border-t-2 border-gray-400 my-2 w-[100%]">

                            <!-- 12th Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="flex items-center">
                                    <label for="occupation" class="w-1/3 text-gray-700 font-medium">Occupation</label>
                                    <input type="text" id="occupation" name="occupation" placeholder="Enter Occupation"
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex items-center">
                                    <label for="coach_current_company" class="w-1/3 text-gray-700 font-medium">Current Company</label>
                                    <input type="text" id="coach_current_company" name="coach_current_company" placeholder="Enter Current Company"
                                        class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>
                            </div>

                            <hr class="border-t-2 border-gray-400 my-2 w-[100%]">
                        </div>

                        <!-- RIGHT SIDE WRAPPER -->
                        <div class="w-full lg:w-[350px] flex-shrink-0 flex flex-col">
                            <div class="bg-gray-100 rounded-lg p-4 shadow-inner flex flex-col h-full">

                                <div class="flex flex-col items-center border border-dashed border-gray-400 rounded-lg p-3 bg-white mb-4">  
                                    <span id="coach_selected_name" class="mt-2 text-gray-800 font-semibold text-center">
                                        No Coach Selected
                                    </span>
                                </div>
                                
                                <div class="flex flex-col items-center border border-dashed border-gray-400 rounded-lg p-3 bg-white mb-4">
                                    <div class="w-full h-80 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 text-sm relative overflow-hidden">
                                        <img id="coach_picturePreview" class="absolute inset-0 w-full h-full object-cover hidden" />
                                        <span id="coach_noPictureText" class="text-gray-500 text-sm">No Picture</span>
                                    </div>
                                </div>

                                <div class="space-y-3 flex-1 w-full">
                                    <div class="flex items-center">
                                        <label class="w-1/3 text-sm font-medium text-gray-700">Sports Event</label>
                                        <?php if(auth()->check() && auth()->user()->role === 'coach' && auth()->user()->coach_sport): ?>
                                            <input type="text" class="w-2/3 bg-gray-100 border border-gray-300 rounded px-2 py-1 text-gray-600 focus:outline-none" 
                                                value="<?php echo e(auth()->user()->coach_sport); ?>" disabled>
                                            <input type="hidden" name="coach_sport_event" value="<?php echo e(auth()->user()->coach_sport); ?>">
                                        <?php else: ?>
                                            <select id="coach_sport_event" name="coach_sport_event" class="w-2/3 bg-white border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-green-600" required>
                                                <option value="">Select Sport...</option>
                                                <?php $__currentLoopData = \App\Models\Sport::orderBy('name', 'asc')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($sport->name); ?>">
                                                        <?php echo e($sport->name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex items-center mt-2">
                                        <label class="w-1/3 text-sm font-medium text-gray-700">Position</label>
                                        <select name="position" class="w-2/3 bg-white border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-green-600" required>
                                            <option value="">-- Select Position --</option>
                                            <option value="Head Coach">Head Coach</option>
                                            <option value="Assistant Coach">Assistant Coach</option>
                                            <option value="Trainer">Trainer</option>
                                            <option value="Physiotherapist">Physiotherapist</option>
                                            <option value="Manager">Manager</option>
                                            <option value="Scout">Scout</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                    <div class="flex items-center mt-2">
                                        <label class="w-1/3 text-sm font-medium text-gray-700">Status</label>
                                        <select name="coach_status" class="w-2/3 bg-white border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-green-600" required>
                                            <option value="">-- Select Status --</option>
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                            <option value="Transfered">Transfered</option>
                                            <option value="Graduated">Graduated</option>
                                        </select>
                                    </div>

                                    <div class="flex flex-col sm:flex-row justify-between gap-4 mt-6">
                                        <input type="file" id="coach_pictureInput" name="coach_picture" accept="image/*" class="hidden">

                                        <button type="button" id="coach_addPictureBtn"
                                            class="w-full bg-green-700 text-white font-semibold rounded px-3 py-2 flex items-center justify-center gap-1 hover:bg-green-800 transition">
                                            <i class="bi bi-person-plus"></i> Add Picture
                                        </button>

                                        <button type="button" id="coach_clearPictureBtn"
                                            class="w-full bg-red-600 text-white font-semibold rounded px-3 py-2 flex items-center justify-center gap-1 hover:bg-red-700 transition">
                                            <i class="bi bi-person-x"></i> Clear Picture
                                        </button>
                                    </div>

                                    <div class="flex items-center mt-4">
                                        <label class="w-1/3 text-sm font-medium text-gray-700">Date Inactive</label>
                                        <input type="date" name="coach_inactive_date" class="w-2/3 bg-white border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-green-600" autocomplete="off">
                                    </div>

                                    <!-- PRINT BUTTON -->
                                    <button type="button" id="coach_printBtn" onclick="printCoach()" 
                                        class="hidden w-full mt-6 bg-blue-600 text-white font-bold rounded px-3 py-2 shadow hover:bg-blue-700 transition flex justify-center items-center gap-2">
                                        🖨️ Print Coach Record
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Note Section -->
                <div id="coach_tab-content" class="mt-6">
                    <div class="bg-white p-6 shadow-sm rounded border">
                        <div class="flex-1">
                            <p class="text-xl md:text-2xl font-bold text-gray-800 mb-0">Notes</p>
                            <hr class="border-t-2 border-gray-400 my-2 w-[100%]">
                            <textarea type="text" name="coach_notes" class="w-full border border-gray-300 rounded px-3 py-2 h-52 focus:outline-none focus:ring-2 focus:ring-green-600" autocomplete="off"><?php echo e(isset($coach) ? $coach->coach_notes : ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================================= -->
            <!-- Coach Achievements Content -->
            <div id="coach-achievements" class="coach-tab-pane hidden">
                <div class="space-y-4 border rounded p-4 mt-4 bg-white shadow-sm">
                    <!-- PAGE HEADER -->
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b pb-4">
                        <h1 class="text-xl md:text-2xl font-bold text-gray-800">🏅 Achievements</h1>
                        <button onclick="toggleCoachAchievementModal(true)"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 shadow w-full md:w-auto font-semibold">
                            + Add Achievement
                        </button>
                    </div>

                    <!-- Achievements Table -->
                    <div class="overflow-x-auto w-full rounded-lg border border-gray-200">
                        <table class="min-w-full text-sm text-left whitespace-nowrap">
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
                            <tbody id="coach-achievements-tbody" class="bg-white text-gray-700">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal -->
                <div id="coach-AchievementModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div class="bg-[#2e4e1f] rounded-xl shadow-xl w-full max-w-lg p-6 relative border-2 border-green-600 max-h-[90vh] overflow-y-auto">
                        <button onclick="toggleCoachAchievementModal(false)" 
                                class="absolute top-3 right-4 text-white hover:text-gray-300 text-2xl font-bold">
                            ✕
                        </button>
                        <h2 class="text-2xl font-semibold text-white text-center mb-6 border-b border-green-700 pb-2">Add Achievement</h2>
                        
                        <form id="coach-achievementForm" onsubmit="return false;">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="coach_id" id="coach_id" value="">
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-white mb-1">Year</label>
                                    <input type="text" name="year" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="2024">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white mb-1">Month-Day</label>
                                    <input type="text" name="month_day" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Mar-15">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white mb-1">Sports Event</label>
                                    <input type="text" name="sports_event" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Basketball Tournament">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white mb-1">Venue</label>
                                    <input type="text" name="venue" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="City Gym">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white mb-1">Award</label>
                                    <select name="award" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                        <option value="">Select Award</option>
                                        <option value="Coach of the Year">Coach of the Year</option>
                                        <option value="Best Performance">Best Performance</option>
                                        <option value="Team Excellence">Team Excellence</option>
                                        <option value="Achievement in Sports">Achievement in Sports</option>
                                        <option value="Lifetime Achievement">Lifetime Achievement</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white mb-1">Category</label>
                                    <input type="text" name="category" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Regional">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-white mb-1">Remarks</label>
                                    <textarea name="remarks" rows="2" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Additional notes..."></textarea>
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full mt-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition shadow-md">
                                Save Achievement
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ========================================================================================= -->
            <!-- Coach Assigned Schedule / Athletes Content -->
            <div id="assigned-schedule-athletes" class="coach-tab-pane hidden">
                <div class="space-y-4 border rounded p-4 mt-4 bg-white shadow-sm">
                    <!-- PAGE HEADER -->
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b pb-4">
                        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Assigned Schedule / Athletes</h1>
                        <button onclick="toggleScheduleModal(true)"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 shadow w-full md:w-auto font-semibold">
                            + Add Record
                        </button>
                    </div>

                    <!-- SUBJECT TABLE -->
                    <div class="overflow-x-auto w-full rounded-lg border border-gray-200">
                        <table class="min-w-full text-left whitespace-nowrap">
                            <thead class="bg-green-600 text-white text-center">
                                <tr>
                                    <th class="py-3 px-4 border border-gray-300" rowspan="2">Term</th>
                                    <th class="py-3 px-4 border border-gray-300" rowspan="2">Academic Year</th>
                                    <th class="py-3 px-4 border border-gray-300" colspan="3">Total Number of Athletes</th>
                                    <th class="py-3 px-4 border border-gray-300" rowspan="2">Remarks</th>
                                </tr>
                                <tr>
                                    <th class="py-2 px-4 border border-gray-300">A</th>
                                    <th class="py-2 px-4 border border-gray-300">B</th>
                                    <th class="py-2 px-4 border border-gray-300">C</th>
                                </tr>
                            </thead>
                            <tbody id="scheduleTable" class="text-gray-700 bg-white"></tbody>
                        </table>
                    </div>
                </div>

                <!-- ADD SUBJECT MODAL -->
                <div id="scheduleModal" class="hidden fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 p-4">
                    <div class="bg-[#2e4e1f] rounded-xl shadow-2xl w-full max-w-lg p-6 relative border-2 border-green-600 max-h-[90vh] overflow-y-auto">
                        <button onclick="toggleScheduleModal(false)" class="absolute top-3 right-4 text-white hover:text-gray-300 text-2xl font-bold">✕</button>
                        <h2 class="text-2xl font-bold mb-6 text-center text-white border-b border-green-700 pb-2">Add Schedule / Athlete</h2>

                        <form id="ScheduleForm" class="space-y-4" onsubmit="return false;">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Term</label>
                                    <input type="text" name="term" placeholder="e.g., 1st Term" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Academic Year</label>
                                    <input type="text" name="academic_year" placeholder="e.g., 2024 - 2025" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                            </div>

                            <label class="block text-white font-medium mt-4 mb-2 text-sm text-center">Total Number of Athletes</label>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-white text-xs mb-1 text-center">Class A</label>
                                    <input type="number" name="count_a" placeholder="0" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div>
                                    <label class="block text-white text-xs mb-1 text-center">Class B</label>
                                    <input type="number" name="count_b" placeholder="0" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div>
                                    <label class="block text-white text-xs mb-1 text-center">Class C</label>
                                    <input type="number" name="count_c" placeholder="0" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="block text-white font-medium mb-1 text-sm">Remarks</label>
                                <input type="text" name="remarks" placeholder="Any notes or remark" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>

                            <button type="submit" class="w-full mt-6 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition shadow-md">
                                Save Record
                            </button>
                        </form> 
                    </div> 
                </div> 
            </div>

            <!-- ========================================================================================= -->
            <!-- Coach Expenses and Payments Content -->
            <div id="expenses-payments" class="coach-tab-pane hidden">
                <div class="space-y-4 border rounded p-4 mt-4 bg-white shadow-sm">
                    <!-- PAGE HEADER -->
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b pb-4">
                        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Expenses and Payments</h1>
                        <button onclick="toggleExpensesModal(true)"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 shadow w-full md:w-auto font-semibold">
                            + Add Record
                        </button>
                    </div>

                    <!-- SUBJECT TABLE -->
                    <div class="overflow-x-auto w-full rounded-lg border border-gray-200">
                        <table class="min-w-full text-left whitespace-nowrap">
                            <thead class="bg-green-600 text-white text-center">
                                <tr>
                                    <th class="py-3 px-4 border border-gray-300">Year</th>
                                    <th class="py-3 px-4 border border-gray-300">Date</th>
                                    <th class="py-3 px-4 border border-gray-300">Title of Activity</th>
                                    <th class="py-3 px-4 border border-gray-300">Estimate Budget</th>
                                    <th class="py-3 px-4 border border-gray-300">Actual Budget</th>
                                    <th class="py-3 px-4 border border-gray-300">Variance</th>
                                    <th class="py-3 px-4 border border-gray-300">Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="expensesTable" class="text-gray-700 bg-white text-center"></tbody>
                        </table>
                    </div>
                </div>

                <!-- ADD EXPENSES MODAL -->
                <div id="expensesModal" class="hidden fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 p-4">
                    <div class="bg-[#2e4e1f] rounded-xl shadow-2xl w-full max-w-lg p-6 relative border-2 border-green-600 max-h-[90vh] overflow-y-auto">
                        <button onclick="toggleExpensesModal(false)" class="absolute top-3 right-4 text-white hover:text-gray-300 text-2xl font-bold">✕</button>
                        <h2 class="text-2xl font-bold mb-6 text-center text-white border-b border-green-700 pb-2">Add Expenses</h2>

                        <form id="ExpensesForm" class="space-y-4" onsubmit="return false;">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Year</label>
                                    <input type="text" name="year" placeholder="Year" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Date</label>
                                    <input type="date" name="date" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-white font-medium mb-1 text-sm">Title of Activity</label>
                                    <input type="text" name="title" placeholder="Title of Activity" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Estimate Budget</label>
                                    <input type="number" name="estimate" placeholder="0.00" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Actual Budget</label>
                                    <input type="text" name="actual" placeholder="0.00" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Variance</label>
                                    <input type="text" name="variance" placeholder="0.00" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Remarks</label>
                                    <input type="text" name="remark" placeholder="Input Remark" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                            </div>
                            <button type="submit" class="w-full mt-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition shadow-md">
                                Save Record
                            </button>
                        </form> 
                    </div> 
                </div> 
            </div>

            <!-- ========================================================================================= -->
            <!-- Coach Membership Content -->
            <div id="membership" class="coach-tab-pane hidden">
                <div class="space-y-4 border rounded p-4 mt-4 bg-white shadow-sm">
                    <!-- PAGE HEADER -->
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b pb-4">
                        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Membership</h1>
                        <button onclick="toggleMembershipModal(true)"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 shadow w-full md:w-auto font-semibold">
                            + Add Record
                        </button>
                    </div>

                    <!-- SUBJECT TABLE -->
                    <div class="overflow-x-auto w-full rounded-lg border border-gray-200">
                        <table class="min-w-full text-left whitespace-nowrap">
                            <thead class="bg-green-600 text-white text-center">
                                <tr>
                                    <th class="py-3 px-4 border border-gray-300">Year</th>
                                    <th class="py-3 px-4 border border-gray-300">Date</th>
                                    <th class="py-3 px-4 border border-gray-300">Venue</th>
                                    <th class="py-3 px-4 border border-gray-300">Name of organization</th>
                                    <th class="py-3 px-4 border border-gray-300">Level</th>
                                    <th class="py-3 px-4 border border-gray-300">Position</th>
                                    <th class="py-3 px-4 border border-gray-300">Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="membershipTable" class="text-gray-700 bg-white text-center"></tbody>
                        </table>
                    </div>
                </div>

                <!-- ADD Membership MODAL -->
                <div id="membershipModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div class="bg-[#2e4e1f] rounded-xl shadow-2xl w-full max-w-lg p-6 relative border-2 border-green-600 max-h-[90vh] overflow-y-auto">
                        <button onclick="toggleMembershipModal(false)" class="absolute top-3 right-4 text-white hover:text-gray-300 text-2xl font-bold">✕</button>
                        <h2 class="text-2xl font-bold mb-6 text-center text-white border-b border-green-700 pb-2">Add Membership</h2>

                        <form id="MembershipForm" class="space-y-4" onsubmit="return false;">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Year</label>
                                    <input type="text" name="year" placeholder="Year" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Date</label>
                                    <input type="date" name="date" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-white font-medium mb-1 text-sm">Venue</label>
                                    <input type="text" name="venue" placeholder="Venue" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-white font-medium mb-1 text-sm">Name of Organization</label>
                                    <input type="text" name="organization" placeholder="Name of Organization" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Level</label>
                                    <input type="text" name="level" placeholder="Level" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Position</label>
                                    <input type="text" name="position" placeholder="Position" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-white font-medium mb-1 text-sm">Remarks</label>
                                    <input type="text" name="remark" placeholder="Remarks" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                            </div>
                            <button type="submit" class="w-full mt-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition shadow-md">
                                Save Record
                            </button>
                        </form> 
                    </div> 
                </div> 
            </div>

            <!-- ========================================================================================= -->
            <!-- Coach Seminars Content -->
            <div id="seminars" class="coach-tab-pane hidden">
                <div class="space-y-4 border rounded p-4 mt-4 bg-white shadow-sm">
                    <!-- PAGE HEADER -->
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b pb-4">
                        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Seminars</h1>
                        <button onclick="toggleSeminarsModal(true)"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 shadow w-full md:w-auto font-semibold">
                            + Add Record
                        </button>
                    </div>

                    <!-- SUBJECT TABLE -->
                    <div class="overflow-x-auto w-full rounded-lg border border-gray-200">
                        <table class="min-w-full text-left whitespace-nowrap">
                            <thead class="bg-green-600 text-white text-center">
                                <tr>
                                    <th class="py-3 px-4 border border-gray-300">Year</th>
                                    <th class="py-3 px-4 border border-gray-300">Date</th>
                                    <th class="py-3 px-4 border border-gray-300">Venue</th>
                                    <th class="py-3 px-4 border border-gray-300">Title of Seminar / Workshop</th>
                                    <th class="py-3 px-4 border border-gray-300">Level</th>
                                    <th class="py-3 px-4 border border-gray-300">Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="seminarsTable" class="text-gray-700 bg-white text-center"></tbody>
                        </table>
                    </div>
                </div>

                <!-- ADD Seminars MODAL -->
                <div id="seminarsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div class="bg-[#2e4e1f] rounded-xl shadow-2xl w-full max-w-lg p-6 relative border-2 border-green-600 max-h-[90vh] overflow-y-auto">
                        <button onclick="toggleSeminarsModal(false)" class="absolute top-3 right-4 text-white hover:text-gray-300 text-2xl font-bold">✕</button>
                        <h2 class="text-2xl font-bold mb-6 text-center text-white border-b border-green-700 pb-2">Add Seminars</h2>

                        <form id="SeminarsForm" class="space-y-4" onsubmit="return false;">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Year</label>
                                    <input type="text" name="year" placeholder="Seminar Year" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Date</label>
                                    <input type="date" name="date" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-white font-medium mb-1 text-sm">Venue</label>
                                    <input type="text" name="venue" placeholder="Venue" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-white font-medium mb-1 text-sm">Title of Seminar / Workshop</label>
                                    <input type="text" name="seminar" placeholder="Title of Seminar / Workshop" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Level</label>
                                    <input type="text" name="level" placeholder="Level" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Remarks</label>
                                    <input type="text" name="remark" placeholder="Remarks" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                            </div>
                            <button type="submit" class="w-full mt-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition shadow-md">
                                Save Record
                            </button>
                        </form> 
                    </div> 
                </div> 
            </div>

            <!-- ========================================================================================= -->
            <!-- Coach Work History Content -->
            <div id="coach-work-history" class="coach-tab-pane hidden">
                <div class="space-y-4 border rounded p-4 mt-4 bg-white shadow-sm">
                    <!-- PAGE HEADER -->
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b pb-4">
                        <h1 class="text-xl md:text-2xl font-bold text-gray-800"><i class="bi bi-person-workspace text-green-700 me-2"></i> Work History</h1>
                        <button onclick="toggleWorkHistoryModal(true)"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 shadow w-full md:w-auto font-semibold">
                            + Add Work Record
                        </button>
                    </div>

                    <!-- SUBJECT TABLE -->
                    <div class="overflow-x-auto w-full rounded-lg border border-gray-200">
                        <table class="min-w-full border border-gray-300 text-sm text-gray-700 whitespace-nowrap">
                            <thead class="bg-gray-100 text-gray-700 text-center uppercase tracking-wider text-xs">
                                <tr>
                                    <th class="border px-4 py-3">Year / Term</th>
                                    <th class="border px-4 py-3">Date Hired</th>
                                    <th class="border px-4 py-3">Job Position</th>
                                    <th class="border px-4 py-3">Company / Employer</th>
                                    <th class="border px-4 py-3">Status / Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="workTable" class="text-gray-700 text-center bg-white"></tbody>
                        </table>
                    </div>
                </div>

                <!-- ADD Work History MODAL -->
                <div id="workModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div class="bg-[#2e4e1f] rounded-xl shadow-2xl w-full max-w-lg p-6 relative border-2 border-green-600 max-h-[90vh] overflow-y-auto">
                        <button onclick="toggleWorkHistoryModal(false)" class="absolute top-3 right-4 text-white hover:text-gray-300 text-2xl font-bold">✕</button>
                        <h2 class="text-2xl font-bold mb-6 text-center text-white border-b border-green-700 pb-2">Add Work History</h2>

                        <form id="WorkHistoryForm" class="space-y-4" onsubmit="return false;">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Year</label>
                                    <input type="text" name="year" placeholder="Year" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div>
                                    <label class="block text-white font-medium mb-1 text-sm">Date</label>
                                    <input type="date" name="date" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-white font-medium mb-1 text-sm">Work Position</label>
                                    <input type="text" name="work_position" placeholder="Work Position" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-white font-medium mb-1 text-sm">Name of Company</label>
                                    <input type="text" name="company" placeholder="Name of Company" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-white font-medium mb-1 text-sm">Remarks</label>
                                    <input type="text" name="remark" placeholder="Remarks" class="w-full bg-white text-gray-900 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                </div>
                            </div>
                            <button type="submit" class="w-full mt-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition shadow-md">
                                Save Record
                            </button>
                        </form> 
                    </div> 
                </div> 
            </div>

            <!-- ========================================================================================= -->
            <!-- Coach Attachments Content -->
            <div id="coach-attachments" class="coach-tab-pane hidden">
                <div class="space-y-4 border rounded p-4 mt-4 bg-white shadow-sm">
                    <p>This is the attachments.</p>
                </div>
            </div>

        </div>
    </div>  

    <!-- PRINT PREVIEW MODAL (TELEPORTED) -->
    <div id="coach_printModal" class="hidden items-center justify-center p-4" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.85); z-index: 999999 !important;">
        <div class="bg-gray-300 rounded-xl shadow-2xl flex flex-col relative overflow-hidden border-4 border-green-700" style="width: 100%; max-width: 1000px; height: 90vh;">
            <div class="bg-green-700 text-white px-6 py-4 flex flex-col md:flex-row justify-between gap-4 items-center z-10 shadow-md">
                <h2 class="text-xl font-bold tracking-wide">🖨️ Document Print Preview</h2>
                <div class="space-x-3 w-full md:w-auto flex justify-end">
                    <button onclick="triggerCoachIframePrint()" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded font-bold shadow transition border border-blue-400">
                        Print Document
                    </button>
                    <button onclick="toggleCoachPrintModal(false)" class="bg-gray-600 hover:bg-gray-500 text-white px-5 py-2 rounded font-bold shadow transition border border-gray-400">
                        Close
                    </button>
                </div>
            </div>
            <div class="flex-1 w-full bg-gray-500 p-6 flex justify-center relative shadow-inner" style="overflow-y: auto;">
                <iframe id="coach_printIframe" class="bg-white shadow-2xl border border-gray-300 rounded-sm" style="width: 100%; max-width: 8.5in; height: 100%; min-height: 11in;" src=""></iframe>
            </div>
        </div>
    </div>

<script>
window.currentUserRole = '<?php echo e(auth()->check() ? auth()->user()->role : ''); ?>';
window.currentCoachId = '<?php echo e(auth()->check() ? auth()->user()->coach_id : ''); ?>';
window.hasCoachProfile = <?php echo e((auth()->check() && auth()->user()->role === 'coach' && isset($coach) && $coach) ? 'true' : 'false'); ?>;

document.addEventListener('DOMContentLoaded', () => {
    
    // --- UPDATED TAB LOGIC USING THE NEW CSS CLASSES ---
    const tabs = document.querySelectorAll('.coach-tab-link');
    const contents = document.querySelectorAll('.coach-tab-pane');

    const defaultTab = document.querySelector('.coach-tab-link[href="#coach-general-info"]');
    if (defaultTab) {
        tabs.forEach(t => t.classList.remove('active-tab'));
        contents.forEach(c => c.classList.add('hidden'));
        defaultTab.classList.add('active-tab');
        document.querySelector('#coach-general-info')?.classList.remove('hidden');
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', e => {
            e.preventDefault();
            tabs.forEach(t => t.classList.remove('active-tab'));
            contents.forEach(c => c.classList.add('hidden'));
            tab.classList.add('active-tab');
            const targetId = tab.getAttribute('href');
            if(targetId) {
                document.querySelector(targetId)?.classList.remove('hidden');
            }
        });
    });

    // --- AUTO CALCULATE AGE ---
    const birthdateInput = document.getElementById('coach_birthdate');
    const ageInput = document.getElementById('coach_age');

    if (birthdateInput && ageInput) {
        birthdateInput.addEventListener('change', function() {
            if (this.value) {
                const today = new Date();
                const birthDate = new Date(this.value);
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                ageInput.value = age >= 0 ? age : 0;
            } else {
                ageInput.value = '';
            }
        });
    }

    const byId = id => document.getElementById(id);
    window.newCoachData = {
        generalInfo: {}, achievements: [], schedule: [], expenses: [], memberships: [], seminars: [], workHistory: []   
    };

    function lockCoachFormInitial() {
        const form = byId('coachForm');
        if (form) {
            form.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.type !== 'hidden' && el.id !== 'coach_search') el.disabled = true;
            });
        }
        byId('coach_addNewBtn')?.classList.remove('hidden');
        byId('coach_saveBtn')?.classList.add('hidden');
        byId('coach_editBtn')?.classList.add('hidden');
        byId('coach_updateBtn')?.classList.add('hidden');
        byId('coach_cancelBtn')?.classList.add('hidden');
    }

    function unlockCoachFormForNew() {
        const form = byId('coachForm');
        if (form) {
            form.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.type !== 'hidden' && el.id !== 'coach_search') el.disabled = false;
            });
        }
        byId('coach_addNewBtn')?.classList.add('hidden');
        byId('coach_saveBtn')?.classList.remove('hidden');
        byId('coach_editBtn')?.classList.add('hidden');
        byId('coach_updateBtn')?.classList.add('hidden');
        byId('coach_cancelBtn')?.classList.remove('hidden');
    }

    function lockCoachFormForViewing() {
        const form = byId('coachForm');
        if (form) {
            form.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.type !== 'hidden' && el.id !== 'coach_search') el.disabled = true;
            });
        }
        byId('coach_addNewBtn')?.classList.add('hidden');
        byId('coach_saveBtn')?.classList.add('hidden');
        byId('coach_updateBtn')?.classList.add('hidden');
        byId('coach_editBtn')?.classList.remove('hidden');
        byId('coach_cancelBtn')?.classList.remove('hidden');
    }

    function unlockCoachFormForEditing() {
        const form = byId('coachForm');
        if (form) {
            form.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.type !== 'hidden' && el.id !== 'coach_search') el.disabled = false;
            });
        }
        byId('coach_addNewBtn')?.classList.add('hidden');
        byId('coach_editBtn')?.classList.add('hidden');
        byId('coach_updateBtn')?.classList.remove('hidden');
        byId('coach_cancelBtn')?.classList.remove('hidden');
        if (byId('coach_method')) byId('coach_method').value = 'PUT';
    }

    // --- AUTO-UNLOCK FORM IF ON /create ROUTE ---
    if (window.currentUserRole !== 'coach') {
        if (window.location.pathname.includes('/create')) {
            unlockCoachFormForNew();
        } else {
            lockCoachFormInitial();
        }
    }

    byId('coach_addNewBtn')?.addEventListener('click', unlockCoachFormForNew);
    byId('coach_editBtn')?.addEventListener('click', unlockCoachFormForEditing);

    byId('coach_addPictureBtn')?.addEventListener('click', () => byId('coach_pictureInput')?.click());
    
    byId('coach_clearPictureBtn')?.addEventListener('click', () => {
        const fileInput = byId('coach_pictureInput');
        const preview = byId('coach_picturePreview');
        const noPic = byId('coach_noPictureText');
        if (fileInput) fileInput.value = '';
        if (preview) { preview.src = ''; preview.classList.add('hidden'); }
        if (noPic) noPic.classList.remove('hidden');
    });

    byId('coach_pictureInput')?.addEventListener('change', (e) => {
        if(e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = byId('coach_picturePreview');
                const noPic = byId('coach_noPictureText');
                if (preview) { preview.src = e.target.result; preview.classList.remove('hidden'); }
                if (noPic) noPic.classList.add('hidden');
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    byId('coach_cancelBtn')?.addEventListener('click', () => {
        const form = byId('coachForm');
        if (form) {
            form.querySelectorAll('input[name], select[name], textarea[name]').forEach(el => {
                if (['_method', '_token', 'selected_coach_id'].includes(el.name)) return;
                if (el.type === 'file') {
                    el.value = ''; const preview = byId('coach_picturePreview');
                    if (preview) { preview.src = ''; preview.classList.add('hidden'); }
                    byId('coach_noPictureText')?.classList.remove('hidden');
                } else if (el.tagName === 'SELECT') el.selectedIndex = 0;
                else el.value = '';
                
                el.classList.remove('border-red-500', 'ring-2', 'ring-red-500');
            });
        }

        if (byId('coach_search')) byId('coach_search').value = '';
        if (byId('coach_selected_name')) byId('coach_selected_name').textContent = 'No Coach Selected';
        
        if (form) form.setAttribute('action', '<?php echo e(route('coaches.store')); ?>');
        if (byId('coach_method')) byId('coach_method').value = 'POST';
        if (byId('selected_coach_id')) byId('selected_coach_id').value = '';
        
        byId('coach_clearPictureBtn')?.click();
        byId('coach_printBtn')?.classList.add('hidden');

        ['coach-achievements-tbody', 'scheduleTable', 'expensesTable', 'membershipTable', 'seminarsTable', 'workTable'].forEach(id => {
            const tb = byId(id); if (tb) tb.innerHTML = `<tr><td colspan="100%" class="text-center py-4 text-gray-500">No data</td></tr>`;
        });
        window.newCoachData = { generalInfo: {}, achievements: [], schedule: [], expenses: [], memberships: [], seminars: [], workHistory: [] };

        lockCoachFormInitial();
    });

    const printModalEl = document.getElementById('coach_printModal');
    if (printModalEl) document.body.appendChild(printModalEl);

    async function loadAvailableSports() {
        try {
            const response = await fetch('<?php echo e(route('coaches.available-sports')); ?>');
            const data = await response.json();
            const sportSelect = byId('coach_sport_event');
            
            // Only apply the filter if the backend actually returns a valid array of available sports
            if (sportSelect && !sportSelect.disabled && data && Array.isArray(data.available)) {
                const allOptions = Array.from(sportSelect.querySelectorAll('option')).slice(1);
                allOptions.forEach(option => {
                    option.style.display = data.available.includes(option.value) ? '' : 'none';
                });
            }
        } catch (error) { 
            console.error('Error fetching available sports:', error); 
        }
    }
    
    // Comment this out completely if you just want to show ALL sports in the database
    // loadAvailableSports();

    const updateBase = '<?php echo e(url('/coaches')); ?>';
    function populateCoachData(full) {
        const generalForm = byId('coachForm');
        
        lockCoachFormForViewing();
        byId('coach_printBtn')?.classList.remove('hidden');

        const fieldMap = {
            'coach_last_name': 'coach_last_name', 'coach_first_name': 'coach_first_name', 'coach_middle_initial': 'coach_middle_initial',
            'coach_age': 'coach_age', 'coach_blood_type': 'coach_blood_type', 'coach_place_of_birth': 'coach_place_of_birth',
            'coach_email': 'coach_email', 'coach_facebook': 'coach_facebook', 'coach_tba': 'coach_tba',
            'coach_contact_number': 'coach_contact_number', 'coach_address': 'coach_address', 'coach_city_municipality': 'coach_city_municipality',
            'coach_province_state': 'coach_province_state', 'coach_zip_code': 'coach_zip_code', 'coach_emergency_person': 'coach_emergency_person',
            'coach_emergency_contact': 'coach_emergency_contact', 'post_graduate': 'post_graduate', 'course_graduated': 'course_graduated',
            'name_collage': 'name_collage', 'coach_course_graduated': 'coach_course_graduated', 'coach_highschool': 'coach_highschool',
            'strand_graduated': 'strand_graduated', 'date_hired': 'date_hired', 'honorarium_payment': 'honorarium_payment',
            'occupation': 'occupation', 'coach_current_company': 'coach_current_company', 'coach_notes': 'coach_notes'
        };

        for (const [dbField, formField] of Object.entries(fieldMap)) {
            const el = generalForm?.querySelector(`[name="${formField}"]`);
            if (el && el.type !== 'file' && el.tagName !== 'SELECT') { el.value = full[dbField] ?? ''; }
        }

        const selectMappings = [
            { name: 'coach_gender', value: full.coach_gender || full.gender },
            { name: 'coach_marital_status', value: full.coach_marital_status || full.marital_status },
            { name: 'coach_sport_event', value: full.coach_sport_event || full.sport_event },
            { name: 'position', value: full.position || full.coach_position },
            { name: 'coach_status', value: full.coach_status || full.status }
        ];

        selectMappings.forEach(mapping => {
            const el = generalForm?.querySelector(`[name="${mapping.name}"]`);
            if (el && mapping.value) {
                // If it's a dropdown menu, find the matching option
                if (el.tagName === 'SELECT') {
                    let valToMatch = String(mapping.value).trim().toLowerCase();
                    let matched = false;
                    
                    for(let i = 0; i < el.options.length; i++) {
                        if(el.options[i].value.toLowerCase() === valToMatch) {
                            el.selectedIndex = i;
                            matched = true;
                            break;
                        }
                    }
                    
                    if(!matched) {
                        for(let i = 0; i < el.options.length; i++) {
                            if(el.options[i].value.toLowerCase().includes(valToMatch) && el.options[i].value !== "") {
                                el.selectedIndex = i;
                                break;
                            }
                        }
                    }
                    el.dispatchEvent(new Event('change'));
                } else {
                    // If it's a hidden input, just assign the value directly
                    el.value = mapping.value;
                }
            }
        });

        ['coach_birthdate', 'coach_year_graduated', 'coach_graduated', 'highschool_graduated', 'date_hired', 'date_resigned', 'coach_inactive_date'].forEach(field => {
            const el = generalForm?.querySelector(`[name="${field}"]`);
            if (el && full[field]) {
                const date = new Date(full[field]);
                el.value = !isNaN(date.getTime()) ? date.toISOString().split('T')[0] : full[field];
            }
        });

        if (full.picture_url) {
            const preview = byId('coach_picturePreview');
            const noPic = byId('coach_noPictureText');
            if (preview) { preview.src = full.picture_url; preview.classList.remove('hidden'); }
            if (noPic) noPic.classList.add('hidden');
        }

        const selectedName = byId('coach_selected_name');
        if (selectedName) selectedName.textContent = full.full_name?.trim() || `${full.coach_first_name || ''} ${full.coach_last_name || ''}`.trim() || 'No Coach Selected';

        if (byId('coach_method')) byId('coach_method').value = 'PUT';
        if (byId('selected_coach_id')) byId('selected_coach_id').value = full.id;

        window.newCoachData = {
            generalInfo: {}, achievements: full.achievements || [], schedule: full.schedule || [],
            expenses: full.expenses || [], memberships: full.memberships || [], seminars: full.seminars || [], workHistory: full.workHistories || []
        };

        populateTable('coach-achievements-tbody', full.achievements, { year: 'Year', month_day: 'Month-Day', sports_event: 'Sports Event', venue: 'Venue', award: 'Award', category: 'Category', remarks: 'Remarks' });
        populateTable('scheduleTable', full.schedule, { term: 'Term', academic_year: 'Academic Year', count_a: 'A',count_b: 'B',count_c: 'C', coachRemark: 'Remarks' });
        populateTable('expensesTable', full.expenses, { year: 'Year', date: 'Date', title: 'Title of Activity', estimate: 'Estimate Budget', actual: 'Actual Budget', variance: 'Variance', remark: 'Remarks' });
        populateTable('membershipTable', full.memberships, { year: 'Year', date: 'Date', venue: 'Venue', organization: 'Name of Organization', level: 'Level', position: 'Position', remark: 'Remarks' });
        populateTable('seminarsTable', full.seminars, { year: 'Year', date: 'Date', venue: 'venue', seminar: 'Title of Seminar / Workshop', level: 'Level', remark: 'Remarks' });
        populateTable('workTable', full.workHistories, { year: 'Year', date: 'Date', work_position: 'Position', company: 'Company', remark: 'Remarks' });
    }

    if (window.currentUserRole === 'coach' && window.currentCoachId) {
        fetch(`${updateBase}/${window.currentCoachId}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(full => { if (full) populateCoachData(full); })
        .catch(err => console.error('Error auto-loading coach:', err));
    }

    const urlParams = new URLSearchParams(window.location.search);
    const coachEditId = urlParams.get('coach_id') || urlParams.get('id');
    if (coachEditId && window.currentUserRole !== 'coach') {
        const generalForm = byId('coachForm');
        generalForm?.setAttribute('action', `${updateBase}/${coachEditId}`);
        fetch(`${updateBase}/${coachEditId}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(full => { if(full) populateCoachData(full); })
        .catch(err => console.error('Error fetching coach URL:', err));
    }

    const searchInput = byId('coach_search');
    const resultsBox = byId('coach_searchResults');
    if (searchInput && resultsBox) {
        const searchUrl = '<?php echo e(route('coaches.search')); ?>';
        let timer = null;

        searchInput.addEventListener('input', (e) => {
            const value = e.target.value;
            if (timer) clearTimeout(timer);
            if (!value?.trim()) { 
                resultsBox.innerHTML = ''; resultsBox.classList.add('hidden'); 
                byId('coach_cancelBtn')?.click(); return; 
            }

            timer = setTimeout(() => {
                fetch(`${searchUrl}?q=${encodeURIComponent(value.trim())}`, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(items => {
                    if (!items?.length) { resultsBox.innerHTML = ''; resultsBox.classList.add('hidden'); return; }
                    resultsBox.innerHTML = '';
                    items.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm';
                        const name = item.full_name?.trim() || `${item.coach_first_name || ''} ${item.coach_last_name || ''}`.trim();
                        div.textContent = name + (item.id ? ` — ID: ${item.id}` : '');
                        
                        div.addEventListener('click', () => {
                            if (byId('selected_coach_id')) byId('selected_coach_id').value = item.id;
                            const generalForm = byId('coachForm');
                            generalForm?.setAttribute('action', `${updateBase}/${item.id}`);

                            fetch(`${updateBase}/${item.id}`, { headers: { 'Accept': 'application/json' } })
                            .then(r => r.json())
                            .then(full => { if(full) populateCoachData(full); })
                            .catch(err => console.error('Error load:', err));

                            resultsBox.innerHTML = '';
                            resultsBox.classList.add('hidden');
                        });
                        resultsBox.appendChild(div);
                    });
                    resultsBox.classList.remove('hidden');
                });
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.innerHTML = ''; resultsBox.classList.add('hidden');
            }
        });
    }

    window.toggleCoachAchievementModal = (s) => byId('coach-AchievementModal')?.classList.toggle('hidden', !s);
    window.toggleScheduleModal = (s) => byId('scheduleModal')?.classList.toggle('hidden', !s);
    window.toggleExpensesModal = (s) => byId('expensesModal')?.classList.toggle('hidden', !s);
    window.toggleMembershipModal = (s) => byId('membershipModal')?.classList.toggle('hidden', !s);
    window.toggleSeminarsModal = (s) => byId('seminarsModal')?.classList.toggle('hidden', !s);
    window.toggleWorkHistoryModal = (s) => byId('workModal')?.classList.toggle('hidden', !s);

    function populateTable(tableId, dataArray, fieldMap) {
        const tbody = byId(tableId);
        if (!tbody) return;
        tbody.innerHTML = '';
        if (!Array.isArray(dataArray) || dataArray.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${Object.keys(fieldMap).length}" class="text-center py-4 text-gray-500">No records found</td></tr>`;
            return;
        }
        dataArray.forEach((item) => {
            const row = document.createElement('tr'); row.className = 'border-b hover:bg-gray-50 text-center';
            row.innerHTML = Object.entries(fieldMap).map(([key, _]) => {
                let val = item[key] ?? item[key.toLowerCase()] ?? item[key.replace(/([A-Z])/g, '_$1').toLowerCase()] ?? '-';
                return `<td class="border px-4 py-2 text-sm">${val}</td>`;
            }).join('');
            tbody.appendChild(row);
        });
    }

    function handleModuleForm(formId, tbodyId, dataKey, fieldMap) {
        const form = byId(formId); const tbody = byId(tbodyId);
        if (!form || !tbody) return;
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(e.target).entries());
            if (!data[Object.keys(fieldMap)[0]]?.trim()) { alert('Fill required fields.'); return; }
            window.newCoachData[dataKey].push(data);
            const r = document.createElement('tr'); r.className = 'border-b hover:bg-gray-50';
            r.innerHTML = Object.keys(fieldMap).map(k => `<td class="border px-4 py-2 text-sm">${data[k] || '-'}</td>`).join('');
            tbody.appendChild(r); e.target.reset();
            const modalMap = { achievements: 'coach-AchievementModal', schedule: 'scheduleModal', expenses: 'expensesModal', memberships: 'membershipModal', seminars: 'seminarsModal', workHistory: 'workModal'};
            if (modalMap[dataKey]) byId(modalMap[dataKey])?.classList.add('hidden');
        });
    }

    handleModuleForm('coach-achievementForm', 'coach-achievements-tbody', 'achievements', { year: 'Year', month_day: 'Month-Day', sports_event: 'Sports Event', venue: 'Venue', award: 'Award', category: 'Category', remarks: 'Remarks' });
    handleModuleForm('ScheduleForm', 'scheduleTable', 'schedule', { term: 'Term', academic_year: 'Academic Year', count_a: 'A',count_b: 'B',count_c: 'C', remarks: 'Remarks' });
    handleModuleForm('ExpensesForm', 'expensesTable', 'expenses', { year: 'Year', date: 'Date', title: 'Title of Activity', estimate: 'Estimate Budget', actual: 'Actual Budget', variance: 'Variance', remark: 'Remarks' });
    handleModuleForm('MembershipForm', 'membershipTable', 'memberships', { year: 'Year', date: 'Date', venue: 'Venue', organization: 'Name of Organization', level: 'Level', position: 'Position', remark: 'Remarks' });
    handleModuleForm('SeminarsForm', 'seminarsTable', 'seminars', { year: 'Year', date: 'Date', venue: 'venue', seminar: 'Title of Seminar / Workshop', level: 'Level', remark: 'Remarks' });
    handleModuleForm('WorkHistoryForm', 'workTable', 'workHistory', { year: 'Year', date: 'Date', work_position: 'Position', company: 'Company', remark: 'Remarks' });

    // --- MANDATORY FIELD VALIDATION HIGHLIGHTS ---
    const generalForm = byId('coachForm');
    function validateRequiredFields() {
        if (!generalForm) return true;
        let isValid = true;
        let firstInvalid = null;

        generalForm.querySelectorAll('[required]').forEach(el => {
            if (!el.value || !el.value.trim()) {
                isValid = false;
                el.classList.add('border-red-500', 'ring-2', 'ring-red-500');
                if (!firstInvalid) firstInvalid = el;
            } else {
                el.classList.remove('border-red-500', 'ring-2', 'ring-red-500');
            }
        });

        if (!isValid) {
            // Smoothly scroll to the first missing field.
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        return isValid;
    }

    // Clear highlights on input change
    if (generalForm) {
        generalForm.querySelectorAll('[required]').forEach(el => {
            el.addEventListener('input', () => {
                if (el.value && el.value.trim()) {
                    el.classList.remove('border-red-500', 'ring-2', 'ring-red-500');
                }
            });
            el.addEventListener('change', () => {
                if (el.value && el.value.trim()) {
                    el.classList.remove('border-red-500', 'ring-2', 'ring-red-500');
                }
            });
        });
    }

    function performFinalSave(e) {
        if (e) e.preventDefault();
        if (!validateRequiredFields()) return;

        const form = byId('coachForm');
        if (form) {
            form.querySelectorAll('input[name], select[name], textarea[name]').forEach(input => {
                if (input.name && !['_method', '_token'].includes(input.name)) { window.newCoachData.generalInfo[input.name] = input.value; }
            });
        }

        const selectedId = byId('selected_coach_id')?.value;
        const endpoint = selectedId ? `<?php echo e(url('/coaches')); ?>/${selectedId}` : '<?php echo e(url('/coaches')); ?>';
        const method = selectedId ? 'PUT' : 'POST';
        
        const btn = e?.target; if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }

        fetch(endpoint, {
            method: method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
            body: JSON.stringify(window.newCoachData)
        })
        .then(async r => {
            const txt = await r.text(); let d = null; try{ d = JSON.parse(txt); }catch(err){}
            if (!r.ok) throw new Error(r.status === 422 && d?.errors ? 'Validation: ' + JSON.stringify(d.errors) : `Server ${r.status}`);
            return d;
        })
        .then(data => {
            alert('✅ Profile saved successfully!' + (data && data.onboarding ? '\n\n' + data.onboarding : ''));
            if (window.currentUserRole !== 'coach' && data?.id) { window.location.href = `<?php echo e(url('/coach')); ?>?coach_id=${data.id}`; } 
            else { location.reload(); }
        })
        .catch(err => { alert(`Save failed: ${err.message}`); })
        .finally(() => { if (btn) { btn.disabled = false; btn.textContent = selectedId ? 'Update Coach' : 'Save Coach'; } });
    }

    byId('coach_saveBtn')?.addEventListener('click', performFinalSave);
    byId('coach_updateBtn')?.addEventListener('click', performFinalSave);
});

window.printCoach = function() {
    const coachId = document.getElementById('selected_coach_id').value;
    if (coachId) { document.getElementById('coach_printIframe').src = '/coach/' + coachId + '/print'; toggleCoachPrintModal(true); } 
    else alert('Please select a coach to print first!');
};
window.toggleCoachPrintModal = function(show) {
    const m = document.getElementById('coach_printModal');
    if(m) { m.classList.toggle('hidden', !show); m.classList.toggle('flex', show); }
};
window.triggerCoachIframePrint = function() {
    const iframe = document.getElementById('coach_printIframe');
    iframe.contentWindow.focus(); iframe.contentWindow.print();
};
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\AthleteX\resources\views/features/coach.blade.php ENDPATH**/ ?>