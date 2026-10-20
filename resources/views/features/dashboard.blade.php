@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
{{-- Grid placement (plain CSS so it works without rebuilding Tailwind) --}}
<style>
    @media (min-width: 1024px) {
        /* Admin: Sports box on the right, spanning the KPI row 2 + chart row; chart sits under Coaches/Pending */
        .dash-sports-admin { grid-column: 3 / span 2; grid-row: 2 / span 2; height: auto !important; }
        .dash-chart-admin  { grid-column: 1 / span 2; grid-row: 3; }
    }

    /* Achievements chart header: compact so the date pickers sit on the same row as the title */
    .ach-title { font-size: 1.25rem !important; margin: 0 !important; line-height: 1.2; white-space: nowrap; }
    .ach-controls input[type="date"] { width: 8.5rem; }

    /* Achievements card: header on top, chart fills the rest and can never spill out of the card */
    .ach-card { display: flex; flex-direction: column; overflow: hidden; }
    .ach-chart-wrap { flex: 1 1 0; min-height: 0; position: relative; }

    /* "Clear Filter" button (shown only while a date filter is active; the 'hidden' class toggles it) */
    .ach-clear { font-size: 0.75rem; font-weight: 600; color: #b91c1c; background: #fee2e2; border-radius: 0.5rem; padding: 0.3rem 0.65rem; white-space: nowrap; transition: background 0.15s; }
    .ach-clear:hover { background: #fecaca; }
</style>
@php
    $isAdmin = auth()->user()->role === 'admin';
    // After adding/deleting a sport, reopen the "Manage Sports" tab so the flash message is seen in context
    $startManage = $isAdmin && (session('success') || session('error') || $errors->has('name'));
@endphp

<div class="flex bg-gray-100 h-full">

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-full">

        <!-- Dashboard Content -->
        <main class="p-6 flex-1 overflow-y-auto h-full space-y-6">

            {{-- Flash messages --}}
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

            <!-- DASHBOARD GRID: KPI cards + chart + sports box -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- 1. CLICKABLE: Active Athletes -->
                <a href="{{ route('athletes.index', ['status' => 'Active']) }}" class="block bg-white p-5 rounded-xl shadow hover:shadow-lg hover:scale-105 border border-transparent hover:border-green-600 transition-all duration-200 cursor-pointer no-underline group">
                    <p class="text-gray-500 group-hover:text-green-600 transition-colors">
                        {{ auth()->user()->role === 'coach' ? 'My Active Athletes' : 'Total Active Athletes' }}
                    </p>
                    <h2 class="text-3xl font-bold text-green-600">
                        {{ $activeAthletesCount ?? 0 }}
                    </h2>
                </a>

                <!-- 2. CLICKABLE: Alumni -->
                <a href="{{ route('athletes.index', ['status' => 'Alumni']) }}" class="block bg-white p-5 rounded-xl shadow hover:shadow-lg hover:scale-105 border border-transparent hover:border-blue-600 transition-all duration-200 cursor-pointer no-underline group">
                    <p class="text-gray-500 group-hover:text-blue-600 transition-colors">
                        {{ auth()->user()->role === 'coach' ? 'My Alumni' : 'Total Alumni' }}
                    </p>
                    <h2 class="text-3xl font-bold text-blue-600">
                        {{ $alumniCount ?? 0 }}
                    </h2>
                </a>

                <!-- 3. CLICKABLE: Inactive -->
                <a href="{{ route('athletes.index', ['status' => 'Inactive']) }}" class="block bg-white p-5 rounded-xl shadow hover:shadow-lg hover:scale-105 border border-transparent hover:border-orange-600 transition-all duration-200 cursor-pointer no-underline group">
                    <p class="text-gray-500 group-hover:text-orange-600 transition-colors">
                        {{ auth()->user()->role === 'coach' ? 'My Inactive Athletes' : 'Total Inactive Athletes' }}
                    </p>
                    <h2 class="text-3xl font-bold text-orange-600">
                        {{ $inactive ?? 0 }}
                    </h2>
                </a>

                <!-- 4. CLICKABLE: Achievements -->
                <a href="{{ route('achievements.index') }}" class="block bg-white p-5 rounded-xl shadow hover:shadow-lg hover:scale-105 border border-transparent hover:border-purple-600 transition-all duration-200 cursor-pointer no-underline group">
                    <p class="text-gray-500 group-hover:text-purple-600 transition">Achievements Recorded</p>
                    <h2 class="text-3xl font-bold text-purple-600">
                        {{ $totalAchievements ?? 0 }}
                    </h2>
                </a>

                {{-- ================= ADMIN EXCLUSIVE CARDS ================= --}}
                @if($isAdmin)

                    <!-- 5. CLICKABLE: Total Coaches -->
                    <a href="{{ route('coaches.index', ['status' => 'Active']) }}" class="block bg-white p-5 rounded-xl shadow hover:shadow-lg hover:scale-105 border border-transparent hover:border-blue-500 transition-all duration-200 cursor-pointer no-underline group">
                        <p class="text-gray-500 group-hover:text-blue-500 transition-colors">Total Coaches</p>
                        <h2 class="text-3xl font-bold text-blue-600">
                            {{ $coachesCount ?? 0 }}
                        </h2>
                    </a>

                    <!-- 6. CLICKABLE Pending Approvals -->
                    <a href="{{ route('admin.approvals.pending') }}" class="block bg-white p-5 rounded-xl shadow hover:shadow-lg hover:scale-105 border-b-4 border-red-500 transition cursor-pointer text-decoration-none">
                        <p class="text-gray-500 group-hover:text-red-600 transition">Pending Approvals</p>
                        <h2 class="text-3xl font-bold text-red-600">
                            {{ $pendingApprovals ?? 0 }}
                        </h2>
                    </a>
                @endif

                <!-- Monthly Achievements Chart -->
                <div class="sm:col-span-2 lg:col-span-2 {{ $isAdmin ? 'dash-chart-admin' : '' }} bg-white p-6 rounded-xl shadow h-96 border border-transparent hover:border-green-600 transition duration-300 ach-card">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                        <h3 class="ach-title font-semibold text-gray-700">Achievements Per Month</h3>
                        <div class="ach-controls flex items-center gap-2">
                            <label for="achFrom" class="text-xs text-gray-500 font-medium uppercase tracking-wider">From</label>
                            <input type="date" id="achFrom" value="{{ $from ?? '' }}" class="border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 bg-gray-50 text-gray-700 disabled:opacity-50">
                            <label for="achTo" class="text-xs text-gray-500 font-medium uppercase tracking-wider">To</label>
                            <input type="date" id="achTo" value="{{ $to ?? '' }}" class="border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 bg-gray-50 text-gray-700 disabled:opacity-50">
                            <button type="button" id="achClear" class="ach-clear {{ ($from ?? '') || ($to ?? '') ? '' : 'hidden' }}" title="Clear the date filter and show all achievements">
                                <i class="bi bi-x-circle"></i> Clear Filter
                            </button>
                        </div>
                    </div>
                    <div class="ach-chart-wrap cursor-pointer" title="Click a bar to view achievements">
                        <canvas id="achievementChart" class="w-full h-full"></canvas>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- SPORTS PROGRAMS: Roster Headcount + Manage  -->
                <!-- Admin: fills the blank space above the chart (spans 2 rows) -->
                <!-- ========================================== -->
                <div class="relative sm:col-span-2 lg:col-span-2 {{ $isAdmin ? 'h-96 dash-sports-admin' : 'h-96' }}">
                    <div class="absolute inset-0 bg-white rounded-xl shadow border border-transparent hover:border-green-600 transition duration-300 flex flex-col overflow-hidden">

                        <!-- Header -->
                        <div class="px-4 pt-4 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-lg text-gray-700">Sports Programs</h3>
                                @if($isAdmin)
                                    <a href="{{ route('sports') }}" class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-2.5 py-0.5 rounded-full text-xs font-bold no-underline transition" title="View all sports">
                                        {{ $activeSports ?? 0 }} Sports
                                    </a>
                                @endif
                            </div>

                            @if($isAdmin)
                                <div id="rosterFilterWrap">
                                    <select id="sportFilter" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 bg-gray-50 font-medium text-gray-700">
                                        <option value="All">All Sports</option>
                                        @foreach($sports as $sport)
                                            <option value="{{ str_replace(' ', '_', $sport->name) }}">{{ $sport->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <input type="hidden" id="sportFilter" value="All">
                            @endif
                        </div>

                        <!-- Tabs (admin only) -->
                        @if($isAdmin)
                            <div class="px-4 mt-3 border-b border-gray-200 flex gap-6">
                                <button type="button" data-tab="roster" class="pb-2 text-sm font-semibold border-b-2 transition border-green-600 text-green-700">
                                    <i class="bi bi-people-fill"></i> Roster Headcount
                                </button>
                                <button type="button" data-tab="manage" class="pb-2 text-sm font-semibold border-b-2 transition border-transparent text-gray-500 hover:text-gray-700">
                                    <i class="bi bi-gear-fill"></i> Manage Sports
                                </button>
                            </div>
                        @else
                            <div class="border-b border-gray-100 mt-3"></div>
                        @endif

                        <!-- PANEL 1: Active Roster Headcount -->
                        <div data-panel="roster" class="flex-1 min-h-0 overflow-auto {{ $startManage ? 'hidden' : '' }}">
                            <table class="min-w-full divide-y divide-gray-200" id="scheduleTable">
                                <thead class="bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Coach</th>
                                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Asst. Coach</th>
                                        <th class="px-3 py-2 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">A</th>
                                        <th class="px-3 py-2 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">B</th>
                                        <th class="px-3 py-2 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">C</th>
                                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody id="scheduleBody" class="divide-y divide-gray-200 bg-white">
                                    <!-- JavaScript will inject rows here -->
                                </tbody>
                            </table>
                        </div>

                        <!-- PANEL 2: Manage Sports (admin only) -->
                        @if($isAdmin)
                        <div data-panel="manage" class="flex-1 min-h-0 overflow-auto {{ $startManage ? '' : 'hidden' }}">

                            <!-- Add new sport -->
                            <form action="{{ route('admin.sports.manage.store') }}" method="POST" class="p-4 border-b border-gray-100 flex items-end gap-2">
                                @csrf
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Add New Sport</label>
                                    <input type="text" name="name" placeholder="e.g., E-Sports" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                                </div>
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold text-sm py-2 px-4 rounded-lg transition shadow whitespace-nowrap">
                                    <i class="bi bi-plus-circle-fill"></i> Save Sport
                                </button>
                            </form>

                            <!-- Sports list / delete -->
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="py-2 px-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Sport Name</th>
                                        <th class="py-2 px-4 text-xs font-bold text-gray-600 uppercase tracking-wider text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @if(isset($sports) && count($sports) > 0)
                                        @foreach($sports as $sport)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="py-2 px-4 font-semibold text-gray-800">{{ $sport->name }}</td>
                                                <td class="py-2 px-4 text-right">
                                                    <form action="{{ route('admin.sports.manage.destroy', $sport->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete {{ addslashes($sport->name) }}?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition text-sm font-bold">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2" class="text-center py-6 text-gray-400">No sports found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @endif

                    </div>
                </div>

            </div>

        </main>
    </div>
</div>

{{-- Chart.js & Roster Script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ---------- Tabs (Roster Headcount / Manage Sports) ----------
    const tabButtons = document.querySelectorAll('[data-tab]');
    const panels = document.querySelectorAll('[data-panel]');
    const filterWrap = document.getElementById('rosterFilterWrap');

    function showTab(name) {
        panels.forEach(p => p.classList.toggle('hidden', p.dataset.panel !== name));
        tabButtons.forEach(b => {
            const active = b.dataset.tab === name;
            b.classList.toggle('border-green-600', active);
            b.classList.toggle('text-green-700', active);
            b.classList.toggle('border-transparent', !active);
            b.classList.toggle('text-gray-500', !active);
        });
        if (filterWrap) filterWrap.classList.toggle('hidden', name !== 'roster');
    }

    tabButtons.forEach(b => b.addEventListener('click', () => showTab(b.dataset.tab)));
    if (tabButtons.length) showTab('{{ $startManage ? 'manage' : 'roster' }}');

    // ---------- Achievements chart ----------
    const chartInit = @json($chart);
    let achievementChart = null;
    let chartMeta = chartInit.meta;                  // [{month, year}] for each bar (used when a bar is clicked)
    let monthlyAchievements = chartInit.data;
    const monthlyLabels = chartInit.labels;
    const ctx = document.getElementById('achievementChart');

    if (ctx) {
        achievementChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Achievements',
                    data: monthlyAchievements,
                    backgroundColor: 'rgba(35, 233, 17, 0.6)',
                    borderColor: 'rgb(33, 177, 52)',
                    borderWidth: 2,
                    borderRadius: 6,
                    hoverBackgroundColor: 'rgba(35, 233, 17, 0.9)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let count = context.parsed.y;
                                return count === 1 ? count + ' Record (Click to view details)' : count + ' Records (Click to view details)';
                            }
                        }
                    }
                },
                onHover: (event, chartElement) => {
                    event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const bar = chartMeta[elements[0].index];
                        const yearPart = bar.year ? `&year=${bar.year}` : '';
                        window.location.href = `/achievements?month=${bar.month}${yearPart}`;
                    }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Months', font: { size: 14, weight: 'bold' } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        title: { display: true, text: 'Number of Achievements', font: { size: 14, weight: 'bold' } }
                    }
                }
            }
        });
    }

    // ---------- Date range filter: refresh ONLY the chart (no page reload) ----------
    const fromInput = document.getElementById('achFrom');
    const toInput   = document.getElementById('achTo');
    const clearBtn  = document.getElementById('achClear');

    function refreshChart() {
        const from = fromInput.value;
        const to   = toInput.value;

        // Keep the two pickers consistent: From can't be after To
        if (from && to && from > to) {
            toInput.value = from;
        }

        const params = new URLSearchParams();
        if (fromInput.value) params.set('from', fromInput.value);
        if (toInput.value)   params.set('to', toInput.value);

        // Show "Clear Filter" as soon as a date is picked; hide it when both are empty
        clearBtn.classList.toggle('hidden', !(fromInput.value || toInput.value));

        fromInput.disabled = toInput.disabled = true;

        fetch(`{{ route('dashboard.achievements-chart') }}?${params.toString()}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(res => {
                if (!res.ok) throw new Error('Request failed');
                return res.json();
            })
            .then(data => {
                chartMeta = data.meta;
                if (achievementChart) {
                    achievementChart.data.labels = data.labels;
                    achievementChart.data.datasets[0].data = data.data;
                    achievementChart.update();
                }
                clearBtn.classList.toggle('hidden', !(fromInput.value || toInput.value));

                // Keep the URL in sync so a manual refresh shows the same range
                const qs = params.toString();
                history.replaceState(null, '', qs ? `?${qs}` : window.location.pathname);
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
                alert('Could not load the chart data. Please try again.');
            })
            .finally(() => { fromInput.disabled = toInput.disabled = false; });
    }

    if (fromInput && toInput) {
        fromInput.addEventListener('change', refreshChart);
        toInput.addEventListener('change', refreshChart);
        clearBtn.addEventListener('click', function() {
            fromInput.value = '';
            toInput.value = '';
            refreshChart();
        });
    }

    // ---------- Sport Roster Table Loader ----------
    const sportSelect = document.getElementById('sportFilter');
    const scheduleBody = document.getElementById('scheduleBody');

    function loadSportsData(sport) {
        if (!scheduleBody) return;
        scheduleBody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-400"><i class="bi bi-arrow-repeat animate-spin text-2xl inline-block mb-2"></i><br>Loading roster data...</td></tr>';

        fetch(`/sports/filter/${sport}`, { headers: { 'Accept': 'application/json' } })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(coaches => {
                scheduleBody.innerHTML = '';

                if (coaches.length === 0) {
                    scheduleBody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500 font-medium">No active rosters found for this sport.</td></tr>';
                    return;
                }

                coaches.forEach(coach => {
                    scheduleBody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-3 py-2 font-bold text-gray-800">${coach.name}</td>
                            <td class="px-3 py-2 text-gray-500">${coach.assistant_coach}</td>
                            <td class="px-3 py-2 text-center font-extrabold text-green-600">${coach.class_a}</td>
                            <td class="px-3 py-2 text-center font-extrabold text-blue-600">${coach.class_b}</td>
                            <td class="px-3 py-2 text-center font-extrabold text-purple-600">${coach.class_c}</td>
                            <td class="px-3 py-2 text-sm text-gray-500 font-medium">
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

    if (scheduleBody) {
        loadSportsData('All');
    }

    if (sportSelect && sportSelect.tagName === 'SELECT') {
        sportSelect.addEventListener('change', function() {
            loadSportsData(this.value);
        });
    }
});
</script>

@endsection