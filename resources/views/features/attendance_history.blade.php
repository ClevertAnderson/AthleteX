@extends('layouts.app')

@section('title', 'Attendance History')

@section('content')
<div id="tab-content" class="bg-[#c5e0b4] p-8 rounded-lg w-full min-h-screen">
    <div class="bg-white border-[12px] border-[#d1e9f0] p-1 shadow-sm">

        <!-- Header -->
        <div class="bg-[#5bc0de] text-white px-4 py-2 flex flex-col md:flex-row justify-between gap-4 items-center">
            <a href="{{ $backRoute }}" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition flex items-center gap-1">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <h2 class="text-lg font-bold flex-1 text-center">
                📜 Attendance History - {{ $selectedMonth }} {{ $selectedYear }}
            </h2>
            <div></div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('attendance.history') }}" class="p-4 flex flex-wrap gap-4 items-center border-b bg-gray-50 rounded-t-lg">
            <div class="flex items-center gap-2">
                <label class="font-bold text-sm text-gray-700">Month:</label>
                <select name="month" onchange="this.form.submit()" class="border-gray-300 rounded px-3 py-1.5 shadow-sm focus:border-green-500 focus:ring-green-500 border">
                    @foreach($months as $month)
                        <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                            {{ $month }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label class="font-bold text-sm text-gray-700">Year:</label>
                <select name="year" onchange="this.form.submit()" class="border-gray-300 rounded px-3 py-1.5 shadow-sm focus:border-green-500 focus:ring-green-500 border">
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            @if(auth()->user()->role === 'admin')
            <div class="flex items-center gap-2">
                <label class="font-bold text-sm text-gray-700">Sport:</label>
                <select name="sport_id" onchange="this.form.submit()" class="border-gray-300 rounded px-3 py-1.5 shadow-sm focus:border-green-500 focus:ring-green-500 border">
                    <option value="">All Sports</option>
                    @foreach($sports as $sport)
                        <option value="{{ $sport->name }}" {{ ($sportId ?? '') == $sport->name ? 'selected' : '' }}>
                            {{ $sport->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            @if(request()->has('sport_id') || request()->has('month') || request()->has('year'))
            <a href="{{ route('attendance.history') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1.5 rounded text-sm transition font-semibold shadow-sm">
                Clear Filters
            </a>
            @endif
        </form>

        <!-- Color Key -->
        <div class="flex gap-4 px-4 pb-2 text-[10px] font-bold uppercase mt-2">
            <span class="text-gray-600">Color Key:</span>
            <div class="flex items-center gap-1"><span class="w-4 h-4 bg-green-500"></span> Present</div>
            <div class="flex items-center gap-1"><span class="w-4 h-4 bg-yellow-400"></span> Late</div>
            <div class="flex items-center gap-1"><span class="w-4 h-4 bg-blue-400"></span> Excused</div>
            <div class="flex items-center gap-1"><span class="w-4 h-4 bg-red-500"></span> Absent</div>
            <div class="flex items-center gap-1"><span class="w-4 h-4 bg-gray-200 border"></span> No Record</div>
        </div>

        <!-- Monthly Attendance Matrix -->
        <div class="overflow-x-auto mt-4">
            <table class="border-collapse text-xs w-full">
                <thead>
                    <tr class="bg-[#d1e9f0] text-[#333]">
                        <th class="border p-2 sticky left-0 bg-[#d1e9f0] z-10 text-left w-36 min-w-[140px]">
                            Athlete
                        </th>
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            <th class="border p-1 text-center w-8">
                                {{ $day }}
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @forelse($athletes as $athlete)
                        <tr class="hover:bg-gray-50">
                            <td class="border p-2 sticky left-0 bg-white font-semibold w-36 min-w-[140px]">
                                {{ $athlete->first_name }} {{ $athlete->last_name }}
                            </td>
                            @for($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $date = \Carbon\Carbon::create($selectedYear, date('m', strtotime($selectedMonth)), $day)->format('Y-m-d');
                                    $key = $athlete->id . '_' . $date;
                                    $status = strtolower($attendanceMap[$key]->status ?? '');
                                    $bgColor = match($status){
                                        'present' => 'bg-green-500',
                                        'late' => 'bg-yellow-400',
                                        'excused' => 'bg-blue-400',
                                        'absent' => 'bg-red-500',
                                        default => 'bg-gray-200'
                                    };
                                @endphp
                                <td class="border w-8 h-8 {{ $bgColor }}"></td>
                            @endfor
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 1 }}" class="text-center text-gray-500 p-4">
                                No attendance records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 🚀 Pagination Links Footer -->
        @if(method_exists($athletes, 'links'))
            <div class="p-4 border-t bg-gray-50">
                {{ $athletes->links() }}
            </div>
        @endif

    </div>
</div>
@endsection