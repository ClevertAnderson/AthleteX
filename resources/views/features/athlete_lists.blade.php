@extends('layouts.app')

@section('title', 'Athlete Lists')

@section('content')

<div class="space-y-6">

    <div class="flex items-center justify-between px-4">
        <a href="{{ route('student.athlete') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition flex items-center gap-1">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h1 class="text-2xl font-bold text-gray-800 mx-auto">Student Athletes List</h1>
        <div class="w-[100px]"></div> <!-- Spacer for centering -->
    </div>

    {{-- 🚀 NEW TAB NAVIGATION --}}
    <nav class="flex border-b-2 border-gray-200">
        <button id="tab-active" onclick="switchTab('Active')" 
            class="px-6 py-3 font-semibold text-green-700 border-b-4 border-green-700 transition flex items-center gap-2">
            <i class="bi bi-person-check-fill"></i> Active Athletes
        </button>
        <button id="tab-inactive" onclick="switchTab('Inactive')" 
            class="px-6 py-3 font-semibold text-gray-500 border-b-4 border-transparent hover:text-green-700 transition flex items-center gap-2">
            <i class="bi bi-person-dash-fill"></i> Inactive / Alumni
        </button>
    </nav>

    {{-- Filter Section --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border space-y-2">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="text" id="athleteSearchInput" placeholder="Search name, ID…" 
                class="border border-gray-300 rounded px-3 py-2 text-sm w-full focus:ring-2 focus:ring-green-500 outline-none">

            <!-- 🚀 Dynamic Sports Dropdown -->
            <select id="athleteSportFilter" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
                <option value="">All Sports</option>
                @if(isset($sports))
                    @foreach($sports as $sport)
                        <option value="{{ strtolower($sport->name) }}">{{ $sport->name }}</option>
                    @endforeach
                @endif
            </select>

            <!-- 🚀 Smart Status Filter (Managed by JS) -->
            <select id="athleteStatusFilter" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
                <!-- Javascript will dynamically swap these options based on the active tab -->
            </select>

            <select id="athleteClassificationFilter" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
                <option value="">All Classifications</option>
                <option value="class a">Class A</option>
                <option value="class b">Class B</option>
                <option value="class c">Class C</option>
                <option value="alumni">Alumni</option> <!-- Added Alumni here -->
            </select>
        </div>
    </div>

    <!-- 🚀 CLEANED UP TABLE -->
    <div class="bg-white rounded-xl shadow overflow-hidden border">
        <div class="overflow-x-auto">
            <table class="w-full table-auto text-sm whitespace-nowrap" id="athleteTable">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 border-b text-center">S No</th>
                        <th class="px-4 py-3 border-b text-left">Stud ID</th>
                        <th class="px-4 py-3 border-b text-left">Full Name</th>
                        <th class="px-4 py-3 border-b text-left">Sports Event</th>
                        <th class="px-4 py-3 border-b text-left">Classification</th>
                        <th class="px-4 py-3 border-b text-center">Approval</th>
                        <th class="px-4 py-3 border-b text-center">Status</th>
                        <th class="px-4 py-3 border-b text-center sticky right-0 bg-gray-100 z-10 shadow-sm">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @foreach ($athletes as $index => $athlete)
                        <tr class="athlete-row hover:bg-gray-50 transition" 
                            data-tab-status="{{ ($athlete->status === 'Active' && $athlete->classification !== 'Alumni') ? 'Active' : 'Inactive' }}"
                            data-status="{{ strtolower($athlete->status) }}"
                            data-sport="{{ strtolower(str_replace('_', ' ', $athlete->sport_event)) }}"
                            data-classification="{{ strtolower(str_replace('_', ' ', $athlete->classification)) }}">
                            
                            <td class="px-4 py-3 text-center text-gray-500 serial-number"></td>
                            <td class="px-4 py-3 text-gray-600 searchable-id font-medium">{{ $athlete->student_id }}</td>
                            
                            <td class="px-4 py-3 font-semibold text-gray-900 searchable-name">
                                {{ $athlete->last_name }}, {{ $athlete->first_name }} {{ substr($athlete->middle_name, 0, 1) }}.
                            </td>
                            
                            <td class="px-4 py-3 text-gray-700">{{ str_replace('_', ' ', $athlete->sport_event) }}</td> 
                            <td class="px-4 py-3 text-gray-700">{{ str_replace('_', ' ', $athlete->classification) }}</td>
                            
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                    {{ $athlete->approval_status == 'approved' ? 'bg-blue-100 text-blue-800' : ($athlete->approval_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($athlete->approval_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $athlete->status == 'Active' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800' }}">
                                    {{ $athlete->status }}
                                </span>
                            </td>
                            
                            <td class="px-4 py-3 text-center sticky right-0 bg-white z-10 shadow-sm border-l">
                                <a href="{{ route('student.athlete', ['id' => $athlete->id]) }}" 
                                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded text-xs font-semibold transition flex items-center justify-center gap-1">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let currentTab = 'Active';

    // 🚀 SMART TAB SWITCHER
    function switchTab(tabName, initStatus = null) {
        currentTab = tabName;
        const activeTabBtn = document.getElementById('tab-active');
        const inactiveTabBtn = document.getElementById('tab-inactive');
        const statusFilter = document.getElementById('athleteStatusFilter');

        if (tabName === 'Active') {
            activeTabBtn.className = "px-6 py-3 font-semibold text-green-700 border-b-4 border-green-700 transition flex items-center gap-2";
            inactiveTabBtn.className = "px-6 py-3 font-semibold text-gray-500 border-b-4 border-transparent hover:text-green-700 transition flex items-center gap-2";
            
            // Lock Status Filter for Active Tab
            statusFilter.innerHTML = '<option value="active">Active</option>';
            statusFilter.disabled = true;
            statusFilter.className = "border border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed rounded px-3 py-2 text-sm w-full";
        } else {
            inactiveTabBtn.className = "px-6 py-3 font-semibold text-green-700 border-b-4 border-green-700 transition flex items-center gap-2";
            activeTabBtn.className = "px-6 py-3 font-semibold text-gray-500 border-b-4 border-transparent hover:text-green-700 transition flex items-center gap-2";
            
            // Remove 'Active' from Inactive Tab options
            statusFilter.innerHTML = `
                <option value="">All Statuses</option>
                <option value="inactive">Inactive</option>
                <option value="transfered">Transfered</option>
                <option value="graduated">Graduated</option>
            `;
            statusFilter.disabled = false;
            statusFilter.className = "border border-gray-300 rounded px-3 py-2 text-sm w-full outline-none focus:ring-2 focus:ring-green-500";
            
            if (initStatus) {
                statusFilter.value = initStatus;
            } else {
                statusFilter.value = "";
            }
        }
        applyFilters();
    }

    function applyFilters() {
        const searchInput = document.getElementById('athleteSearchInput').value.toLowerCase();
        const sportFilter = document.getElementById('athleteSportFilter').value.toLowerCase();
        const statusFilter = document.getElementById('athleteStatusFilter').value.toLowerCase();
        const classFilter = document.getElementById('athleteClassificationFilter').value.toLowerCase();
        
        const rows = document.querySelectorAll('.athlete-row');
        let counter = 1;

        rows.forEach(row => {
            const rowTabStatus = row.getAttribute('data-tab-status');
            const rowSport = row.getAttribute('data-sport');
            const rowStatus = row.getAttribute('data-status');
            const rowClass = row.getAttribute('data-classification');
            
            const names = Array.from(row.querySelectorAll('.searchable-name')).map(td => td.textContent.toLowerCase()).join(' ');
            const id = row.querySelector('.searchable-id').textContent.toLowerCase();
            const matchesSearch = names.includes(searchInput) || id.includes(searchInput);

            if (rowTabStatus === currentTab &&
                matchesSearch &&
                (sportFilter === "" || rowSport.includes(sportFilter)) &&
                (statusFilter === "" || rowStatus === statusFilter) &&
                (classFilter === "" || rowClass === classFilter)) {
                
                row.style.display = '';
                const serialCell = row.querySelector('.serial-number');
                if (serialCell) serialCell.textContent = counter++;
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Event Listeners
    document.getElementById('athleteSearchInput').addEventListener('input', applyFilters);
    document.getElementById('athleteSportFilter').addEventListener('change', applyFilters);
    document.getElementById('athleteStatusFilter').addEventListener('change', applyFilters);
    document.getElementById('athleteClassificationFilter').addEventListener('change', applyFilters);

    // 🚀 READ URL ON LOAD AND AUTO-NAVIGATE
    document.addEventListener("DOMContentLoaded", () => {
        const urlParams = new URLSearchParams(window.location.search);
        const statusParam = urlParams.get('status') ? urlParams.get('status').toLowerCase() : '';

        if (statusParam === 'alumni') {
            // Auto-filter classification to Alumni and switch to Inactive tab
            document.getElementById('athleteClassificationFilter').value = 'alumni';
            switchTab('Inactive');
        } else if (['inactive', 'graduated', 'transfered'].includes(statusParam)) {
            // Auto-filter status and switch to Inactive tab
            switchTab('Inactive', statusParam);
        } else {
            // Default load
            switchTab('Active');
        }
    });
</script>

@endsection