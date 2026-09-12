<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athlete Registration | Sports Office</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#c5e0b4] min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded shadow-xl p-6 md:p-8 w-full max-w-4xl border-t-[8px] border-[#1b5e20]">
        
        <div class="text-center mb-8 border-b pb-4">
            <h1 class="text-3xl font-bold text-gray-800">Student-Athlete Registration</h1>
            <p class="text-gray-600 mt-1">University of the Cordilleras Sports Office</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-center font-bold">
                ✅ {{ session('success') }}
            </div>
        @else
            @if ($errors->any())
                <div class="bg-red-50 text-red-700 p-4 rounded mb-4 border border-red-200 text-sm">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('public.athlete.store') }}" method="POST" class="space-y-6 text-sm">
                @csrf
                
                <!-- General Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Last Name *</label>
                        <input type="text" name="last_name" required class="w-full border-gray-300 rounded shadow-sm px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">First Name *</label>
                        <input type="text" name="first_name" required class="w-full border-gray-300 rounded shadow-sm px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">M.I.</label>
                        <input type="text" name="middle_initial" class="w-full border-gray-300 rounded shadow-sm px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-600" placeholder="Optional">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Student ID *</label>
                        <input type="text" name="student_id" required class="w-full border-gray-300 rounded shadow-sm px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-600" placeholder="XX-XXXX-XXX">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Sports Event *</label>
                        <select name="sport_event" required class="w-full border-gray-300 rounded shadow-sm px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-600">
                            <option value="">Select Sport...</option>
                            @foreach($sports as $sport)
                                <option value="{{ $sport->name }}">{{ $sport->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Gender *</label>
                        <select name="gender" required class="w-full border-gray-300 rounded shadow-sm px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-600">
                            <option value="">Select...</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Birthdate *</label>
                        <input type="date" name="birthdate" required class="w-full border-gray-300 rounded shadow-sm px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Age *</label>
                        <input type="number" name="age" required class="w-full border-gray-300 rounded shadow-sm px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Birthplace *</label>
                        <input type="text" name="place_of_birth" required class="w-full border-gray-300 rounded shadow-sm px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-600" placeholder="City/Province">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Blood Type</label>
                        <input type="text" name="blood_type" class="w-full border-gray-300 rounded shadow-sm px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-600" placeholder="e.g., O+">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Course *</label>
                        <input type="text" name="course" required class="w-full border-gray-300 rounded shadow-sm px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-600" placeholder="e.g., BSIT">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Year/Level *</label>
                        <input type="text" name="year_level" required class="w-full border-gray-300 rounded shadow-sm px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-600" placeholder="e.g., 4th Year">
                    </div>
                </div>

                <hr>

                <!-- Contact & Emergency -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-1">
                        <label class="block text-gray-700 font-bold mb-1">Contact No. *</label>
                        <input type="text" name="contact_number" required class="w-full border-gray-300 rounded shadow-sm px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-600" placeholder="09xxxxxxxxx">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-bold mb-1">Address *</label>
                        <input type="text" name="address" required class="w-full border-gray-300 rounded shadow-sm px-3 py-2 focus:ring focus:ring-green-200 focus:border-green-600">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-bold mb-1">Emergency Contact Person *</label>
                        <input type="text" name="emergency_person" required class="w-full border-gray-300 rounded shadow-sm px-3 py-2 border-red-300 bg-red-50 focus:ring focus:ring-red-200 focus:border-red-600">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-gray-700 font-bold mb-1">Emergency Contact No. *</label>
                        <input type="text" name="emergency_contact" required class="w-full border-gray-300 rounded shadow-sm px-3 py-2 border-red-300 bg-red-50 focus:ring focus:ring-red-200 focus:border-red-600">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white font-bold py-3 px-4 rounded shadow transition duration-200">
                        Submit Registration
                    </button>
                </div>
            </form>
        @endif
    </div>

</body>
</html>