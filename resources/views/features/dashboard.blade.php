@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="flex bg-gray-100 h-full">

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-full">

        <!-- Dashboard Content -->
        <main class="p-6 flex-1 overflow-y-auto h-full">

            <!-- KPI Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

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
                @if(auth()->user()->role === 'admin')
                    
                    <!-- 5. CLICKABLE: Total Coaches -->
                    <a href="{{ route('coaches.index', ['status' => 'Active']) }}" class="block bg-white p-5 rounded-xl shadow hover:shadow-lg hover:scale-105 border border-transparent hover:border-blue-500 transition-all duration-200 cursor-pointer no-underline group">
                        <p class="text-gray-500 group-hover:text-blue-500 transition-colors">Total Coaches</p>
                        <h2 class="text-3xl font-bold text-blue-600">
                            {{ $coachesCount ?? 0 }}
                        </h2>
                    </a>

                    <!-- 6. CLICKABLE Pending Approvals -->
                    <a href="{{ route('admin.approvals') }}" class="block bg-white p-5 rounded-xl shadow hover:shadow-lg hover:scale-105 border border-transparent hover:border-red-600 transition-all duration-200 cursor-pointer no-underline group">
                        <p class="text-gray-500 group-hover:text-red-600 transition">Pending Approvals</p>
                        <h2 class="text-3xl font-bold text-red-600">
                            {{ $pendingApprovals ?? 0 }}
                        </h2>
                    </a>

                    <!-- 7. Active Sports Programs -->
                    <a href="{{ route('sports') }}" class="block bg-white p-5 rounded-xl shadow hover:shadow-lg hover:scale-105 border border-transparent hover:border-indigo-600 transition-all duration-200 cursor-pointer no-underline group">
                        <p class="text-gray-500 group-hover:text-indigo-600 transition-colors">Active Sports</p>
                        <h2 class="text-3xl font-bold text-indigo-600">
                            {{ $activeSports ?? 0 }}
                        </h2>
                    </a>
                @endif

            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

                <!-- Monthly Achievements Chart -->
                <div class="bg-white p-6 rounded-xl shadow h-96 border border-transparent hover:border-green-600 transition duration-300">
                    <h3 class="font-semibold text-lg mb-4 text-gray-700">Achievements Per Month</h3>
                    <div class="h-[85%] cursor-pointer" title="Click a bar to view achievements">
                        <canvas id="achievementChart" class="w-full h-full"></canvas>
                    </div>
                </div>

            </div>

        </main>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    let rawAchievements = @json($achievementsMonthly ?? []);
    let monthlyAchievements = Array.from({ length: 12 }, (_, i) => rawAchievements[i + 1] ?? 0);

    const monthlyLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    const ctx = document.getElementById('achievementChart');

    if (ctx) {
        new Chart(ctx, {
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
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let count = context.parsed.y;
                                return count === 1 
                                    ? count + ' Record (Click to view details)' 
                                    : count + ' Records (Click to view details)';
                            }
                        }
                    }
                },
                onHover: (event, chartElement) => {
                    event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const monthIndex = elements[0].index;
                        const monthNumber = monthIndex + 1;
                        window.location.href = `/achievements?month=${monthNumber}`;
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Months',
                            font: { size: 14, weight: 'bold' }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        title: {
                            display: true,
                            text: 'Number of Achievements',
                            font: { size: 14, weight: 'bold' }
                        }
                    }
                }
            }
        });
    }

});
</script>

@endsection