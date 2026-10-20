@extends('layouts.app')

@section('title', 'Global Achievements')

@section('content')
<div class="flex bg-gray-100 h-full">

    <div class="flex-1 flex flex-col h-full">
        <main class="p-6 flex-1 overflow-y-auto h-full">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="bi bi-trophy-fill text-yellow-400 mr-2"></i> Master Achievements List
                </h1>
                <p class="text-sm text-gray-500">
                    {{ $achievements->total() }} {{ \Illuminate\Support\Str::plural('record', $achievements->total()) }} · newest first
                </p>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-xl shadow mb-6">
                <form method="GET" action="{{ route('achievements.index') }}" class="flex flex-wrap gap-4 items-end">

                    <!-- Search by Event or Award -->
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search event or award..." class="w-full text-gray-900 bg-white border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 focus:outline-none p-2 border" onblur="this.form.submit()">
                    </div>

                    <!-- Filter by Type (All, Athlete, Coach) -->
                    <div class="w-56">
                        <label class="block text-sm font-medium text-gray-700 mb-1">View Type</label>
                        <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border font-semibold text-[#2e4e1f]" onchange="this.form.submit()">
                            <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All (Students & Coaches)</option>
                            <option value="athlete" {{ $type === 'athlete' ? 'selected' : '' }}>Student-Athletes Only</option>
                            <option value="coach" {{ $type === 'coach' ? 'selected' : '' }}>Coaches Only</option>
                        </select>
                    </div>

                    <!-- Filter by Month (of the date achieved) -->
                    <div class="w-44">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Month Achieved</label>
                        <select name="month" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border" onchange="this.form.submit()">
                            <option value="">All Months</option>
                            @php
                                $months = ['1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
                            @endphp
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter by Year (of the date achieved) -->
                    <div class="w-36">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Year Achieved</label>
                        <select name="year" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border" onchange="this.form.submit()">
                            <option value="">All Years</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ (string) request('year') === (string) $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Clear Button -->
                    <div class="flex gap-2">
                        @if(request()->filled('search') || request()->filled('month') || request()->filled('year') || (request()->filled('type') && request('type') !== 'all'))
                        <a href="{{ route('achievements.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 transition">
                            Clear Filters
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#2e4e1f] text-white">
                                <th class="p-4 border-b font-semibold text-sm">#</th>
                                <th class="p-4 border-b font-semibold text-sm">Name (Role)</th>
                                <th class="p-4 border-b font-semibold text-sm">Sport / Category</th>
                                <th class="p-4 border-b font-semibold text-sm">Event & Venue</th>
                                <th class="p-4 border-b font-semibold text-sm">Award / Rank</th>
                                <th class="p-4 border-b font-semibold text-sm">Date Achieved <i class="bi bi-arrow-down-short"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($achievements as $index => $achievement)
                                <tr class="hover:bg-gray-50 border-b transition">
                                    <td class="p-4 text-sm text-gray-600">{{ $achievements->firstItem() + $index }}</td>

                                    <!-- Dynamic Name with Role Badge -->
                                    <td class="p-4 text-sm font-bold text-gray-800">
                                        @if($achievement->model_type === 'coach')
                                            {{ $achievement->coach->coach_first_name ?? 'Unknown' }} {{ $achievement->coach->coach_last_name ?? '' }}
                                            <span class="ml-2 text-xs text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full font-medium">Coach</span>
                                        @else
                                            {{ $achievement->athlete->first_name ?? 'Unknown' }} {{ $achievement->athlete->last_name ?? '' }}
                                            <span class="ml-2 text-xs text-green-700 bg-green-100 px-2 py-0.5 rounded-full font-medium">Student</span>
                                        @endif
                                    </td>

                                    <!-- Dynamic Sport/Category -->
                                    <td class="p-4 text-sm text-gray-600">
                                        <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-gray-300">
                                            @if($achievement->model_type === 'coach')
                                                {{ $achievement->category ?? $achievement->coach->coach_sport_event ?? 'N/A' }}
                                            @else
                                                {{ $achievement->category ?? $achievement->athlete->sport_event ?? 'N/A' }}
                                            @endif
                                        </span>
                                    </td>

                                    <!-- Dynamic Event & Venue -->
                                    <td class="p-4 text-sm text-gray-600">
                                        <p class="font-bold text-gray-700">{{ $achievement->model_type === 'coach' ? $achievement->sports_event : $achievement->event }}</p>
                                        <p class="text-xs text-gray-500"><i class="bi bi-geo-alt"></i> {{ $achievement->venue }}</p>
                                    </td>

                                    <td class="p-4 text-sm text-gray-600">
                                        <span class="text-yellow-600 font-bold"><i class="bi bi-award"></i> {{ $achievement->award }}</span>
                                    </td>

                                    <td class="p-4 text-sm text-gray-600">
                                        {{ $achievement->month_day }} {{ $achievement->year }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-500">
                                        <i class="bi bi-folder-x text-4xl mb-2 block"></i>
                                        No achievements found for this filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="p-4 border-t bg-gray-50">
                    {{ $achievements->links() }}
                </div>
            </div>

        </main>
    </div>
</div>
@endsection