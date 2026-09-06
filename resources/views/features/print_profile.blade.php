<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($athlete) ? 'Athlete' : 'Coach' }} Profile</title>
    <!-- Pull in Tailwind CSS specifically for printing -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* PRINT SETTINGS - Force Single Page & High Quality */
        @media print {
            @page { margin: 0.5in; size: letter portrait; }
            body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            html, body { height: 100%; background: white; }
            .no-print { display: none !important; }
        }
        
        body { font-family: 'Arial', sans-serif; color: #000; background: #fff; }
    </style>
</head>
<body class="p-8 max-w-4xl mx-auto">

    <!-- OFFICIAL SHARED LETTERHEAD -->
    <div class="flex justify-between items-end pb-2 border-b-2 border-black mb-6">
        <div class="flex items-center">
            <!-- Official UC Logo (.png) -->
            <img src="{{ asset('images/UC_Official_Logo.png') }}" alt="University of the Cordilleras" class="h-14 object-contain" style="max-width: 300px;">
        </div>
        <div class="text-sm font-semibold italic text-black pb-1">
            Sports Development Office
        </div>
    </div>


    <!-- ========================================== -->
    <!-- ATHLETE PROFILE RENDER LOGIC               -->
    <!-- ========================================== -->
    @if(isset($athlete))
        
        <!-- Document Title -->
        <div class="text-center mb-6">
            <h2 class="text-lg font-black text-black uppercase tracking-widest m-0">General Athlete Profile</h2>
        </div>

        <!-- PROFILE HEADER (Photo & Core Info) -->
        <div class="flex gap-6 mb-6">
            <!-- Photo Box -->
            <div class="w-32 h-32 border-2 border-black bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                @if($athlete->picture_path)
                    <img src="{{ asset('storage/' . $athlete->picture_path) }}" alt="Photo" class="w-full h-full object-cover">
                @else
                    <span class="text-xs font-bold text-gray-400">NO PHOTO</span>
                @endif
            </div>
            
            <!-- Core Info -->
            <div class="flex flex-col justify-center w-full">
                <h1 class="text-2xl font-black uppercase tracking-wide m-0 leading-none mb-2">{{ $athlete->last_name }}, {{ $athlete->first_name }}</h1>
                <table class="w-full text-sm">
                    <tr>
                        <td class="font-bold w-32 py-1">ID Number:</td>
                        <td class="font-mono font-bold text-lg">{{ $athlete->student_id }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold py-1">Sport Event:</td>
                        <td class="font-bold text-gray-800 uppercase">{{ str_replace('_', ' ', $athlete->sport_event) }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold py-1">Classification:</td>
                        <td>{{ str_replace('_', ' ', $athlete->classification) }} <span class="text-gray-500 font-bold">({{ $athlete->status }})</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- DATA SECTIONS -->
        <table class="w-full border-collapse border border-black text-sm text-black mb-4">
            <tr>
                <td class="border border-black p-2 font-bold uppercase tracking-widest bg-gray-100" colspan="4">Personal Information</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold w-1/4">Course & Year:</td>
                <td class="border border-black p-2 w-1/4">{{ $athlete->course ?? 'N/A' }} {{ $athlete->year_level ? '- ' . $athlete->year_level : '' }}</td>
                <td class="border border-black p-2 font-bold w-1/4">Date of Birth:</td>
                <td class="border border-black p-2 w-1/4">{{ $athlete->birthdate ? \Carbon\Carbon::parse($athlete->birthdate)->format('M d, Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold">Age:</td>
                <td class="border border-black p-2">{{ $athlete->age ?? 'N/A' }}</td>
                <td class="border border-black p-2 font-bold">Gender:</td>
                <td class="border border-black p-2">{{ $athlete->gender ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold">Blood Type:</td>
                <td class="border border-black p-2">{{ $athlete->blood_type ?? 'N/A' }}</td>
                <td class="border border-black p-2 font-bold">Civil Status:</td>
                <td class="border border-black p-2">{{ $athlete->marital_status ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold">Email Address:</td>
                <td class="border border-black p-2">{{ $athlete->email ?? 'N/A' }}</td>
                <td class="border border-black p-2 font-bold">Contact Number:</td>
                <td class="border border-black p-2 font-mono">{{ $athlete->contact_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold">Home Address:</td>
                <td class="border border-black p-2" colspan="3">{{ trim($athlete->address . ', ' . $athlete->city_municipality . ', ' . $athlete->province_state . ' ' . $athlete->zip_code, ', ') ?: 'N/A' }}</td>
            </tr>
        </table>

        <table class="w-full border-collapse border border-black text-sm text-black mb-4">
            <tr>
                <td class="border border-black p-2 font-bold uppercase tracking-widest bg-gray-100" colspan="4">Emergency Contact</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold w-1/4">Contact Person:</td>
                <td class="border border-black p-2 w-1/4">{{ $athlete->emergency_person ?? 'N/A' }}</td>
                <td class="border border-black p-2 font-bold w-1/4">Emergency No:</td>
                <td class="border border-black p-2 w-1/4 font-mono">{{ $athlete->emergency_contact ?? 'N/A' }}</td>
            </tr>
        </table>

        <table class="w-full border-collapse border border-black text-sm text-black mb-4">
            <tr>
                <td class="border border-black p-2 font-bold uppercase tracking-widest bg-gray-100" colspan="4">Varsity & Academic Details</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold w-1/4">Head Coach:</td>
                <td class="border border-black p-2 w-1/4">{{ $athlete->coach ? $athlete->coach->coach_first_name . ' ' . $athlete->coach->coach_last_name : 'N/A' }}</td>
                <td class="border border-black p-2 font-bold w-1/4">Asst. Coach:</td>
                <td class="border border-black p-2 w-1/4">{{ $athlete->asst_coach ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold">Date Joined:</td>
                <td class="border border-black p-2">{{ $athlete->date_joined ? \Carbon\Carbon::parse($athlete->date_joined)->format('M d, Y') : 'N/A' }}</td>
                <td class="border border-black p-2 font-bold">Total Units:</td>
                <td class="border border-black p-2">{{ $athlete->total_unit ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold">Term Graduated:</td>
                <td class="border border-black p-2">{{ $athlete->term_graduated ?? 'N/A' }}</td>
                <td class="border border-black p-2 font-bold">Year Graduated:</td>
                <td class="border border-black p-2">{{ $athlete->year_graduated ? \Carbon\Carbon::parse($athlete->year_graduated)->format('Y') : 'N/A' }}</td>
            </tr>
        </table>

        <table class="w-full border-collapse border border-black text-sm text-black mb-4">
            <tr>
                <td class="border border-black p-2 font-bold uppercase tracking-widest bg-gray-100" colspan="4">Financial Assessment</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold w-1/4">Tuition Fee:</td>
                <td class="border border-black p-2 w-1/4">{{ $athlete->tuition_fee ? 'Php ' . number_format($athlete->tuition_fee, 2) : 'N/A' }}</td>
                <td class="border border-black p-2 font-bold w-1/4">Miscellaneous Fee:</td>
                <td class="border border-black p-2 w-1/4">{{ $athlete->misc_fee ? 'Php ' . number_format($athlete->misc_fee, 2) : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold">Other Charges:</td>
                <td class="border border-black p-2">{{ $athlete->other_charges ? 'Php ' . number_format($athlete->other_charges, 2) : 'N/A' }}</td>
                <td class="border border-black p-2 font-bold">Total Assessment:</td>
                <td class="border border-black p-2 font-bold">{{ $athlete->total_assessment ? 'Php ' . number_format($athlete->total_assessment, 2) : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold">Total Discount:</td>
                <td class="border border-black p-2">{{ $athlete->total_discount ? 'Php ' . number_format($athlete->total_discount, 2) : 'N/A' }}</td>
                <td class="border border-black p-2 font-bold">Final Balance:</td>
                <td class="border border-black p-2 font-bold text-red-700">{{ $athlete->balance ? 'Php ' . number_format($athlete->balance, 2) : 'N/A' }}</td>
            </tr>
        </table>

        @if($athlete->current_work || $athlete->current_company)
        <table class="w-full border-collapse border border-black text-sm text-black mb-6">
            <tr>
                <td class="border border-black p-2 font-bold uppercase tracking-widest bg-gray-100" colspan="2">Employment Information (Alumni)</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold w-1/4">Current Work/Position:</td>
                <td class="border border-black p-2">{{ $athlete->current_work ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold">Company/Employer:</td>
                <td class="border border-black p-2">{{ $athlete->current_company ?? 'N/A' }}</td>
            </tr>
        </table>
        @endif


    <!-- ========================================== -->
    <!-- COACH PROFILE RENDER LOGIC                 -->
    <!-- ========================================== -->
    @elseif(isset($coach))

        <!-- Document Title -->
        <div class="text-center mb-6">
            <h2 class="text-lg font-black text-black uppercase tracking-widest m-0">General Coach Profile</h2>
        </div>

        <!-- PROFILE HEADER (Photo & Core Info) -->
        <div class="flex gap-6 mb-6">
            <!-- Photo Box -->
            <div class="w-32 h-32 border-2 border-black bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                @if(isset($coach->picture_path) && $coach->picture_path)
                    <img src="{{ asset('storage/' . $coach->picture_path) }}" alt="Photo" class="w-full h-full object-cover">
                @else
                    <span class="text-xs font-bold text-gray-400">NO PHOTO</span>
                @endif
            </div>
            
            <!-- Core Info -->
            <div class="flex flex-col justify-center w-full">
                <h1 class="text-2xl font-black uppercase tracking-wide m-0 leading-none mb-2">
                    {{ $coach->coach_last_name ?? 'N/A' }}, {{ $coach->coach_first_name ?? 'N/A' }}
                </h1>
                <table class="w-full text-sm">
                    <tr>
                        <td class="font-bold w-32 py-1">Position:</td>
                        <td class="font-bold text-gray-800 uppercase">{{ $coach->position ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold py-1">Sport Event:</td>
                        <td class="font-bold text-gray-800 uppercase">{{ str_replace('_', ' ', $coach->coach_sport_event ?? 'N/A') }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold py-1">Status:</td>
                        <td class="font-bold {{ strtolower($coach->status ?? '') === 'active' ? 'text-green-700' : 'text-red-700' }}">
                            {{ strtoupper($coach->status ?? 'N/A') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- DATA SECTIONS -->
        <table class="w-full border-collapse border border-black text-sm text-black mb-4">
            <tr>
                <td class="border border-black p-2 font-bold uppercase tracking-widest bg-gray-100" colspan="4">Personal & Contact Information</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold w-1/4">Email Address:</td>
                <td class="border border-black p-2 w-1/4">{{ $coach->email ?? 'N/A' }}</td>
                <td class="border border-black p-2 font-bold w-1/4">Contact Number:</td>
                <td class="border border-black p-2 w-1/4 font-mono">{{ $coach->contact_no ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold">Home Address:</td>
                <td class="border border-black p-2" colspan="3">
                    {{ trim(($coach->address ?? '') . ', ' . ($coach->city_municipality ?? ''), ', ') ?: 'N/A' }}
                </td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold">Emergency Contact:</td>
                <td class="border border-black p-2" colspan="3">{{ $coach->emergency_contact_person ?? 'N/A' }}</td>
            </tr>
        </table>

        <table class="w-full border-collapse border border-black text-sm text-black mb-4">
            <tr>
                <td class="border border-black p-2 font-bold uppercase tracking-widest bg-gray-100" colspan="2">Educational Background</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold w-1/3">High School:</td>
                <td class="border border-black p-2 w-2/3">{{ $coach->name_of_highschool ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold">College / University:</td>
                <td class="border border-black p-2">{{ $coach->name_of_college_school ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold">Post Graduate School:</td>
                <td class="border border-black p-2">{{ $coach->name_of_post_graduate_school ?? 'N/A' }}</td>
            </tr>
        </table>

        <table class="w-full border-collapse border border-black text-sm text-black mb-4">
            <tr>
                <td class="border border-black p-2 font-bold uppercase tracking-widest bg-gray-100" colspan="4">Employment Details</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold w-1/4">Occupation (Outside UC):</td>
                <td class="border border-black p-2" colspan="3">{{ $coach->occupation ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold w-1/4">Date Hired (SDO):</td>
                <td class="border border-black p-2 w-1/4">{{ isset($coach->date_hired) && $coach->date_hired ? \Carbon\Carbon::parse($coach->date_hired)->format('M d, Y') : 'N/A' }}</td>
                <td class="border border-black p-2 font-bold w-1/4">Date Inactive:</td>
                <td class="border border-black p-2 w-1/4">{{ isset($coach->date_inactive) && $coach->date_inactive ? \Carbon\Carbon::parse($coach->date_inactive)->format('M d, Y') : 'N/A' }}</td>
            </tr>
        </table>

    @endif

    <!-- ========================================== -->
    <!-- SHARED SIGNATURES & FOOTER                 -->
    <!-- ========================================== -->
    <table class="w-full border-collapse text-sm text-black mt-16">
        <tr>
            <td class="w-1/2 text-center pb-2 px-8">
                <div class="border-b border-black w-full mb-1"></div>
                <span class="font-bold text-black block text-sm uppercase">Ms. Daphnie S. Pelaez</span>
                <span class="text-xs text-gray-700 italic">Administrative Staff, SDO</span>
            </td>
            <td class="w-1/2 text-center pb-2 px-8">
                <div class="border-b border-black w-full mb-1"></div>
                <span class="font-bold text-black block text-sm uppercase">Dr. Danilo L. Cong-o</span>
                <span class="text-xs text-gray-700 italic">Director, Sports Development Office</span>
            </td>
        </tr>
    </table>

    <div class="flex justify-between border-t border-black mt-12 pt-2 text-[10px] text-gray-600 font-sans">
        <div>
            UC-ADM-SDO-FORM-{{ isset($athlete) ? 'AP' : 'CP' }}<br>
            August 17, 2023 Rev. 00
        </div>
        <div class="text-right">
            Printed on: {{ now()->format('F d, Y') }}<br>
            Page 1 of 1
        </div>
    </div>

</body>
</html>