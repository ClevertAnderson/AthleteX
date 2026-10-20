@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
<div id="tab-content" class="bg-[#c5e0b4] p-4 md:p-6 rounded w-full min-h-screen">
    <div class="bg-white border-[8px] md:border-[12px] border-[#d1e9f0] p-1 shadow-sm rounded-lg">

        <!-- Page Header -->
        <div class="bg-[#5bc0de] p-4 flex flex-col md:flex-row items-center justify-between gap-4 mb-6 rounded shadow-sm">
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-0">Attendance</h1>
                @if(isset($today))
                    <p class="text-sm text-gray-700 font-medium">Today: {{ \Carbon\Carbon::parse($today)->format('l, F j, Y') }}</p>
                @endif
            </div>
            <div class="w-full md:w-auto">
                <a href="{{ route('attendance.history') }}" 
                class="bg-green-700 text-white px-4 py-2 rounded shadow hover:bg-green-800 transition block text-center font-semibold">
                    📊 Attendance History
                </a>
            </div>
        </div>

        <!-- Admin Filter -->
        @if(auth()->user()->role === 'admin')
        <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 mb-4 w-full px-2">
            
            <select name="sport" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-2 w-full sm:w-auto flex-1 focus:ring-2 focus:ring-green-500 outline-none">
                <option value="">All Sports</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport->name }}" {{ request('sport') == $sport->name ? 'selected' : '' }}>
                        {{ $sport->name }}
                    </option>
                @endforeach
            </select>

            <select id="monthFilter" name="month" onchange="document.getElementById('dateFilter').value=''; this.form.submit()" class="border border-gray-300 rounded px-3 py-2 w-full sm:w-auto flex-1 focus:ring-2 focus:ring-green-500 outline-none">
                <option value="">All Months</option>
                @php
                    $monthsList = [
                        '01' => 'January', '02' => 'February', '03' => 'March',
                        '04' => 'April', '05' => 'May', '06' => 'June',
                        '07' => 'July', '08' => 'August', '09' => 'September',
                        '10' => 'October', '11' => 'November', '12' => 'December'
                    ];
                @endphp
                @foreach($monthsList as $num => $name)
                    <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>

            <input id="dateFilter" type="date" name="date" value="{{ request('date') }}" onchange="document.getElementById('monthFilter').value=''; this.form.submit()"
                class="border border-gray-300 rounded px-3 py-2 w-full sm:w-auto flex-1 focus:ring-2 focus:ring-green-500 outline-none" />

            @if(request('sport') || request('month') || request('date'))
                <a href="{{ route('admin.attendance') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition text-center font-semibold w-full sm:w-auto">
                    Clear Filters
                </a>
            @endif
        </form>
        @endif

        <!-- Coach Attendance Checking Button -->
        @if(auth()->user()->role === 'coach')
        <div class="flex justify-center md:justify-start px-2 mb-4">
            <button class="btn btn-success w-full md:w-auto font-semibold" data-bs-toggle="modal" data-bs-target="#attendanceModal">
                <i class="bi bi-clipboard-check me-1"></i> Check Attendance
            </button>
        </div>
        @endif

        <!-- Attendance Table -->
        <div class="bg-white rounded shadow border border-gray-300 overflow-hidden mx-2 md:mx-0">
            <div class="overflow-x-auto w-full">
                <table class="min-w-full divide-y divide-gray-300 whitespace-nowrap">
                    <thead class="bg-[#d1e9f0] border-b border-gray-300">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase border-r border-gray-200">#</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase border-r border-gray-200">Athlete</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase border-r border-gray-200">Sport</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase border-r border-gray-200">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase border-r border-gray-200">Remarks</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($athletesWithStatus as $index => $athlete)
                            <tr class="hover:bg-blue-50 transition-colors {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                <td class="px-6 py-3 text-sm border-r border-gray-200">
                                    {{ method_exists($athletesWithStatus, 'firstItem') && $athletesWithStatus->firstItem() ? $athletesWithStatus->firstItem() + $index : $index + 1 }}
                                </td>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900 border-r border-gray-200">{{ $athlete['last_name'] }}, {{ $athlete['first_name'] }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600 border-r border-gray-200">{{ $athlete['sport_event'] }}</td>
                                <td class="px-6 py-3 border-r border-gray-200">
                                    @if(strtolower($athlete['status']) === 'present')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 border border-green-200">Present</span>
                                    @elseif(strtolower($athlete['status']) === 'absent')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 border border-red-200">Absent</span>
                                    @elseif(strtolower($athlete['status']) === 'late')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">Late</span>
                                    @elseif(strtolower($athlete['status']) === 'excused')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">Excused</span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-500 border border-gray-200">Not Marked</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500 border-r border-gray-200">{{ $athlete['remarks'] ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($athlete['attendance_date'])->format('Y-m-d') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 font-medium">No athletes found for this filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- 🚀 Pagination Links Footer -->
            @if(method_exists($athletesWithStatus, 'links'))
                <div class="p-4 border-t bg-gray-50">
                    {{ $athletesWithStatus->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Attendance Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" data-bs-backdrop="false" style="background-color: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('coach.attendance.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label font-bold">Attendance Date</label>
                        <input type="date" name="attendance_date" class="form-control" value="{{ isset($today) ? $today : now()->toDateString() }}" max="{{ now()->toDateString() }}">
                        <small class="text-muted">Select today or a past date to back-log historical attendance.</small>
                    </div>

                    <div class="alert alert-info mb-3 text-sm" role="alert">
                        <strong>📋 How it works:</strong> Select the date you want to record, then click the buttons to cycle through the status.
                    </div>

                    <div class="table-responsive mb-3" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>#</th>
                                    <th>Athlete</th>
                                    <th>Sports</th>
                                    <th>Status</th>
                                    <th>Remarks (Optional)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($athletes))
                                    @foreach($athletes as $index => $athlete)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $athlete->last_name }}, {{ $athlete->first_name }}</td>
                                        <td>{{ $athlete->sport_event }}</td>
                                        <td>
                                            <input type="hidden" name="attendance[{{ $athlete->id }}][status]" value="present" class="attendance-hidden">
                                            <button type="button" class="btn btn-sm btn-outline-success attendance-toggle" title="Click to cycle through statuses">
                                                Present
                                            </button>
                                        </td>
                                        <td>
                                            <input type="text" name="attendance[{{ $athlete->id }}][remarks]" class="form-control form-control-sm" placeholder="e.g., Injured">
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Attendance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.appendChild(document.getElementById('attendanceModal'));

    const statuses = ['present', 'absent', 'late', 'excused'];
    const statusLabels = { 'present': 'Present', 'absent': 'Absent', 'late': 'Late', 'excused': 'Excused' };
    const statusClasses = { 'present': 'btn-outline-success', 'absent': 'btn-outline-danger', 'late': 'btn-outline-warning', 'excused': 'btn-outline-info' };

    document.querySelectorAll('.attendance-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const hiddenInput = this.previousElementSibling;
            let currentStatus = hiddenInput.value;
            let currentIndex = statuses.indexOf(currentStatus);
            let nextIndex = (currentIndex + 1) % statuses.length;
            let nextStatus = statuses[nextIndex];

            hiddenInput.value = nextStatus;
            this.textContent = statusLabels[nextStatus];
            this.className = 'btn btn-sm ' + statusClasses[nextStatus] + ' attendance-toggle';
        });
    });
});
</script>
@endsection