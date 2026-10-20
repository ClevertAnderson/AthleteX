@extends('layouts.app')

@section('title', 'Athlete Profile')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <a href="{{ route('student.athlete') }}" class="inline-flex items-center text-gray-600 hover:text-green-600 mb-6 transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to List
    </a>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 border border-gray-100">
        <div class="bg-gradient-to-r from-green-700 to-green-600 px-8 py-8">
            <div class="flex flex-col md:flex-row items-center gap-6">
                
                {{-- Profile Picture Logic --}}
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center text-3xl font-bold text-green-700 shadow-lg border-4 border-green-100 overflow-hidden">
                    @if($athlete->picture_path)
                        <img src="{{ asset($athlete->picture_path) }}" alt="Profile" class="w-full h-full object-cover">
                    @else
                        {{ substr($athlete->first_name, 0, 1) }}
                    @endif
                </div>
                
                <div class="text-center md:text-left text-white flex-1">
                    <h1 class="text-3xl font-bold">
                        {{ $athlete->first_name }} {{ $athlete->middle_name ? $athlete->middle_name.' ' : '' }}{{ $athlete->last_name }} {{ $athlete->suffix }}
                    </h1>
                    <div class="flex flex-wrap gap-2 mt-2 justify-center md:justify-start">
                        <span class="bg-green-800 bg-opacity-50 px-3 py-1 rounded-full text-sm font-medium border border-green-400">
                            ID: {{ $athlete->student_id }}
                        </span>
                        <span class="bg-green-800 bg-opacity-50 px-3 py-1 rounded-full text-sm font-medium border border-green-400">
                            {{ str_replace('_', ' ', $athlete->sport_event) }}
                        </span>
                        <span class="bg-white text-green-700 px-3 py-1 rounded-full text-sm font-bold shadow-sm">
                            {{ $athlete->classification ?? $athlete->status }}
                        </span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('student.athlete') }}?id={{ $athlete->id }}" 
                    class="bg-white bg-opacity-20 hover:bg-opacity-30 text-black px-4 py-2 rounded-lg font-medium transition backdrop-blur-sm border border-white border-opacity-30">
                        Edit Profile
                    </a>

                    <a href="{{ route('athlete.print', $athlete->id) }}" target="_blank" 
                    class="bg-white text-green-700 hover:bg-gray-100 px-4 py-2 rounded-lg font-bold transition shadow-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Generate Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="md:col-span-1 space-y-6">
            {{-- Personal Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Personal Details
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex flex-col md:flex-row justify-between gap-4">
                        <span class="text-gray-500">Birthdate</span>
                        <span class="font-medium text-gray-900">{{ $athlete->birthdate ? $athlete->birthdate->format('M d, Y') : '-' }}</span>
                    </div>
                    <div class="flex flex-col md:flex-row justify-between gap-4">
                        <span class="text-gray-500">Age</span>
                        <span class="font-medium text-gray-900">{{ $athlete->age ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col md:flex-row justify-between gap-4">
                        <span class="text-gray-500">Gender</span>
                        <span class="font-medium text-gray-900">{{ $athlete->gender ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col md:flex-row justify-between gap-4">
                        <span class="text-gray-500">Civil Status</span>
                        <span class="font-medium text-gray-900">{{ $athlete->marital_status ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col md:flex-row justify-between gap-4">
                        <span class="text-gray-500">Nationality</span>
                        <span class="font-medium text-gray-900">{{ $athlete->nationality ?? 'Filipino' }}</span>
                    </div>
                </div>
            </div>

            {{-- Physical Attributes --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                    Physical Attributes
                </h3>
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="p-2 bg-gray-50 rounded-lg">
                        <div class="text-xs text-gray-500 uppercase tracking-wider">Height</div>
                        <div class="font-bold text-gray-800">{{ $athlete->height ?? '-' }} <span class="text-xs font-normal">cm</span></div>
                    </div>
                    <div class="p-2 bg-gray-50 rounded-lg">
                        <div class="text-xs text-gray-500 uppercase tracking-wider">Weight</div>
                        <div class="font-bold text-gray-800">{{ $athlete->weight ?? '-' }} <span class="text-xs font-normal">kg</span></div>
                    </div>
                    <div class="p-2 bg-gray-50 rounded-lg">
                        <div class="text-xs text-gray-500 uppercase tracking-wider">Blood</div>
                        <div class="font-bold text-gray-800">{{ $athlete->blood_type ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">
            {{-- Contact Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Contact Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Email Address</label>
                        <p class="text-gray-900 font-medium break-all">{{ $athlete->email }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Mobile Number</label>
                        <p class="text-gray-900 font-medium">{{ $athlete->contact_number ?? 'N/A' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-500 uppercase font-semibold">Home Address</label>
                        <p class="text-gray-900">
                            {{ $athlete->address }}, {{ $athlete->city_municipality }}, {{ $athlete->province_state }} {{ $athlete->zip_code }}
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-500 uppercase font-semibold">Facebook Profile</label>
                        @if($athlete->facebook)
                            <a href="{{ str_contains($athlete->facebook, 'http') ? $athlete->facebook : 'https://'.$athlete->facebook }}" target="_blank" class="text-blue-600 hover:underline block truncate">
                                {{ $athlete->facebook }}
                            </a>
                        @else
                            <p class="text-gray-400">N/A</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Academic Record --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Academic Record
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Course/Program</label>
                        <p class="font-medium text-gray-900">{{ $athlete->course ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Year Level</label>
                        <p class="font-medium text-gray-900">{{ $athlete->year_level ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">College</label>
                        <p class="font-medium text-gray-900">{{ $athlete->college ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Emergency Contact --}}
            <div class="bg-red-50 rounded-xl shadow-sm border border-red-100 p-6">
                <h3 class="text-lg font-bold text-red-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    In Case of Emergency
                </h3>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <p class="text-red-900 font-bold text-lg">{{ $athlete->emergency_person ?? 'N/A' }}</p>
                        <p class="text-red-600 text-sm">{{ $athlete->emergency_relationship ?? 'Guardian' }}</p>
                    </div>
                    <div class="bg-white px-4 py-2 rounded-lg border border-red-200 flex items-center text-red-700 font-bold">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        {{ $athlete->emergency_contact ?? 'N/A' }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection