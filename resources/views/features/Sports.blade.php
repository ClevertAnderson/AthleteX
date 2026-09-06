@extends('layouts.app')

@section('title', 'Sports')

@section('content')
<div class="bg-gray-100 min-h-screen p-6">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-3xl font-bold text-gray-800">Sports Programs</h1>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                {{ session('error') }}
            </div>
        @endif
        @error('name')
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                {{ $message }}
            </div>
        @enderror

        <!-- ========================================== -->
        <!-- ADMIN ONLY: DYNAMIC SPORTS MANAGER -->
        <!-- ========================================== -->
        @if(auth()->user()->role === 'admin')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <!-- ADD NEW SPORT -->
            <div class="md:col-span-1 bg-white p-6 rounded-xl shadow border">
                <h2 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="bi bi-plus-circle-fill text-green-600"></i> Add New Sport
                </h2>
                <form action="{{ route('admin.sports.manage.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Sport Name</label>
                        <input type="text" name="name" placeholder="e.g., E-Sports" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition shadow">
                        Save Sport
                    </button>
                </form>
            </div>

            <!-- LIST OF SPORTS / DELETE -->
            <div class="md:col-span-2 bg-white rounded-xl shadow border overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-700"><i class="bi bi-list-ul text-blue-600"></i> Active Sport Categories</h2>
                </div>
                <div class="max-h-64 overflow-y-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-white sticky top-0 shadow-sm">
                            <tr>
                                <th class="py-3 px-6 text-sm font-medium text-gray-500">Sport Name</th>
                                <th class="py-3 px-6 text-sm font-medium text-gray-500 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($sports as $sport)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3 px-6 font-semibold text-gray-800">{{ $sport->name }}</td>
                                    <td class="py-3 px-6 text-right">
                                        <form action="{{ route('admin.sports.manage.destroy', $sport->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete {{ $sport->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition text-sm font-bold">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-6 text-gray-400">No sports found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- ========================================== -->
        <!-- ACTIVE ROSTER HEADCOUNT (Visible to Admin & Coach) -->
        <!-- ========================================== -->
        <div class="bg-white rounded-xl shadow border overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h2 class="text-xl font-bold text-gray-800">Active Roster Headcount</h2>
                
                @if(auth()->user()->role === 'admin')
                <div class="flex flex-col w-full sm:w-64">
                    <label class="text-xs text-gray-500 font-medium mb-1 uppercase tracking-wider">Filter by Sport</label>
                    <select id="sportFilter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-600 bg-gray-50 font-medium text-gray-700">
                        <option value="All">All Sports</option>
                        @foreach($sports as $sport)
                            @php
                                $sportValue = str_replace(' ', '_', $sport->name);
                            @endphp
                            <option value="{{ $sportValue }}">{{ $sport->name }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                    <!-- Hidden field for Coaches so the script still runs perfectly -->
                    <input type="hidden" id="sportFilter" value="All">
                @endif
            </div>

            <div class="overflow-x-auto p-6 pt-2">
                <table class="min-w-full divide-y divide-gray-200" id="scheduleTable">
                    <thead class="bg-gray-100 rounded-t-lg">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider rounded-tl-lg">Coach</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Assistant Coach</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Class A</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Class B</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Class C</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider rounded-tr-lg">Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleBody" class="divide-y divide-gray-200 bg-white">
                        <!-- JavaScript will inject rows here -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sportSelect = document.getElementById('sportFilter');
    const scheduleBody = document.getElementById('scheduleBody');

    function loadSportsData(sport) {
        scheduleBody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-400"><i class="bi bi-arrow-repeat animate-spin text-2xl inline-block mb-2"></i><br>Loading roster data...</td></tr>';

        fetch(`/sports/filter/${sport}`)
            .then(res => res.json())
            .then(coaches => {
                scheduleBody.innerHTML = ''; 

                if(coaches.length === 0) {
                    scheduleBody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500 font-medium">No active rosters found for this sport.</td></tr>';
                    return;
                }

                coaches.forEach(coach => {
                    scheduleBody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-800">${coach.name}</td>
                            <td class="px-6 py-4 text-gray-500">${coach.assistant_coach}</td>
                            <td class="px-6 py-4 text-center font-extrabold text-green-600 text-lg">${coach.class_a}</td>
                            <td class="px-6 py-4 text-center font-extrabold text-blue-600 text-lg">${coach.class_b}</td>
                            <td class="px-6 py-4 text-center font-extrabold text-purple-600 text-lg">${coach.class_c}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 font-medium">
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">${coach.remarks}</span>
                            </td>
                        </tr>
                    `;
                });
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                scheduleBody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-red-500">Failed to load data. Please refresh.</td></tr>';
            });
    }

    // Load data instantly on page open
    loadSportsData('All');

    // Only attach the change event if the element is actually a dropdown (Admin view)
    if (sportSelect.tagName === 'SELECT') {
        sportSelect.addEventListener('change', function() {
            loadSportsData(this.value);
        });
    }
});
</script>
@endsection