@extends('layouts.app')

@section('title', 'Coach Lists')

@section('content')

<div class="space-y-6 pt-20 md:pt-6">

    <!-- Header Container -->
    <div class="flex flex-col md:flex-row items-center md:justify-between px-4 gap-4 mb-2">
        <div class="hidden md:block w-full md:w-1/4"></div>
        
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 text-center flex-1">Coaches List</h1>
        
        <div class="w-full md:w-1/4 flex justify-end">
            <a href="{{ route('coaches.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition shadow font-semibold flex items-center gap-2 w-max">
                <i class="bi bi-person-plus-fill"></i> Add New Coach
            </a>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <nav class="flex flex-nowrap overflow-x-auto space-x-4 border-b border-gray-200 pb-2 px-4">
        <button id="coach-tab-active" onclick="switchCoachTab('Active')" 
            class="px-6 py-3 font-semibold text-green-700 border-b-4 border-green-700 transition flex items-center gap-2 whitespace-nowrap">
            <i class="bi bi-person-check-fill"></i> Active Coaches
        </button>
        <button id="coach-tab-inactive" onclick="switchCoachTab('Inactive')" 
            class="px-6 py-3 font-semibold text-gray-500 border-b-4 border-transparent hover:text-green-700 transition flex items-center gap-2 whitespace-nowrap">
            <i class="bi bi-person-dash-fill"></i> Inactive / Former
        </button>
    </nav>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-lg shadow-sm border space-y-2 mx-4 md:mx-0">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-center">
            <input type="text" id="coachSearchInput" placeholder="Search name, ID..." 
                class="border border-gray-300 rounded px-3 py-2 text-sm w-full focus:ring-2 focus:ring-green-500 outline-none">

            <select id="coachSportFilter" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
                <option value="">All Sports</option>
                <?php $allSports = \App\Models\Sport::orderBy('name', 'asc')->get(); ?>
                <?php foreach($allSports as$sport): ?>
                    <option value="<?php echo strtolower($sport->name); ?>"><?php echo e($sport->name); ?></option>
                <?php endforeach; ?>
            </select>

            <select id="coachStatusFilter" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
            </select>

            <div class="flex items-center gap-2">
                <select id="coachPositionFilter" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
                    <option value="">All Positions</option>
                    <option value="head coach">Head Coach</option>
                    <option value="assistant coach">Assistant Coach</option>
                    <option value="trainer">Trainer</option>
                    <option value="manager">Manager</option>
                    <option value="other">Other</option>
                </select>

                <button type="button" id="clearCoachFilters" class="hidden bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded text-sm transition font-semibold whitespace-nowrap">
                    Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Coach Table -->
    <div class="bg-white rounded-xl shadow overflow-hidden border mx-4 md:mx-0">
        <div class="overflow-x-auto">
            <table class="w-full table-auto text-sm whitespace-nowrap" id="coachTable">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 border-b text-center">S No</th>
                        <th class="px-4 py-3 border-b text-center">Photo</th>
                        <th class="px-4 py-3 border-b text-left">Coach ID</th>
                        <th class="px-4 py-3 border-b text-left">Full Name</th>
                        <th class="px-4 py-3 border-b text-left">Sports Event</th>
                        <th class="px-4 py-3 border-b text-left">Position</th>
                        <th class="px-4 py-3 border-b text-center">Status</th>
                        <th class="px-4 py-3 border-b text-center sticky right-0 bg-gray-100 z-10 shadow-sm">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    <?php if(isset($coaches) && count($coaches) > 0): ?>
                        <?php foreach ($coaches as$coach): ?>
                            <?php 
                                $statusText = empty($coach->coach_status) ? 'Active' :$coach->coach_status; 
                                $mi = trim($coach->coach_middle_initial ?? '');
                                $nameDisplay = $coach->coach_last_name . ', ' .$coach->coach_first_name;
                                if (!empty($mi)) {
                                    $nameDisplay .= ' ' .$mi . '.';
                                }
                            ?>
                            <tr class="coach-row hover:bg-gray-50 transition" 
                                data-tab-status="<?php echo $statusText === 'Active' ? 'Active' : 'Inactive'; ?>"
                                data-status="<?php echo strtolower($statusText); ?>"
                                data-sport="<?php echo strtolower(str_replace('_', ' ', $coach->coach_sport_event ?? '')); ?>"
                                data-position="<?php echo strtolower($coach->position ?? ''); ?>">
                                
                                <td class="px-4 py-3 text-center text-gray-500 serial-number"></td>

                                <td class="px-4 py-3 text-center">
                                    <?php if($coach->coach_picture): ?>
                                        <img src="<?php echo asset('storage/' . $coach->coach_picture); ?>" class="w-9 h-9 object-cover rounded-full mx-auto border border-gray-300 shadow-sm">
                                    <?php else: ?>
                                        <div class="w-9 h-9 bg-gray-200 rounded-full mx-auto flex items-center justify-center text-gray-500 border border-gray-300 shadow-sm">
                                            <i class="bi bi-person"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td class="px-4 py-3 text-gray-600 searchable-id font-medium"><?php echo $coach->id; ?></td>
                                
                                <td class="px-4 py-3 font-semibold text-gray-900 searchable-name">
                                    <?php echo e($nameDisplay); ?>
                                </td>
                                
                                <td class="px-4 py-3 text-gray-700"><?php echo e(str_replace('_', ' ', $coach->coach_sport_event ?? '')); ?></td>
                                <td class="px-4 py-3 text-gray-700"><?php echo e($coach->position ?: 'Coach'); ?></td>
                                
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold <?php echo $statusText === 'Active' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800'; ?>">
                                        <?php echo e($statusText); ?>
                                    </span>
                                </td>
                                
                                <td class="px-4 py-3 text-center sticky right-0 bg-white z-10 shadow-sm border-l">
                                    <a href="<?php echo e(route('coaches.create', ['coach_id' => $coach->id])); ?>" 
                                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded text-xs font-semibold transition flex items-center justify-center gap-1">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                No coaches found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- 🚀 Pagination Links Footer -->
        @if(isset($coaches) && method_exists($coaches, 'links'))
            <div class="p-4 border-t bg-gray-50">
                {{ $coaches->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    let currentCoachTab = 'Active';

    function switchCoachTab(tabName, initStatus = null) {
        currentCoachTab = tabName;
        const activeTabBtn = document.getElementById('coach-tab-active');
        const inactiveTabBtn = document.getElementById('coach-tab-inactive');
        const statusFilter = document.getElementById('coachStatusFilter');

        if (tabName === 'Active') {
            activeTabBtn.className = "px-6 py-3 font-semibold text-green-700 border-b-4 border-green-700 transition flex items-center gap-2 whitespace-nowrap";
            inactiveTabBtn.className = "px-6 py-3 font-semibold text-gray-500 border-b-4 border-transparent hover:text-green-700 transition flex items-center gap-2 whitespace-nowrap";
            
            statusFilter.innerHTML = '<option value="active">Active</option>';
            statusFilter.disabled = true;
            statusFilter.className = "border border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed rounded px-3 py-2 text-sm w-full";
        } else {
            inactiveTabBtn.className = "px-6 py-3 font-semibold text-green-700 border-b-4 border-green-700 transition flex items-center gap-2 whitespace-nowrap";
            activeTabBtn.className = "px-6 py-3 font-semibold text-gray-500 border-b-4 border-transparent hover:text-green-700 transition flex items-center gap-2 whitespace-nowrap";
            
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
        applyCoachFilters();
    }

    function checkCoachFilterState() {
        const searchVal = document.getElementById('coachSearchInput').value;
        const sportVal = document.getElementById('coachSportFilter').value;
        const posVal = document.getElementById('coachPositionFilter').value;
        const clearBtn = document.getElementById('clearCoachFilters');

        if (searchVal !== '' || sportVal !== '' || posVal !== '') {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
    }

    function applyCoachFilters() {
        const searchInput = document.getElementById('coachSearchInput').value.toLowerCase();
        const sportFilter = document.getElementById('coachSportFilter').value.toLowerCase();
        const statusFilter = document.getElementById('coachStatusFilter').value.toLowerCase();
        const positionFilter = document.getElementById('coachPositionFilter').value.toLowerCase();
        
        const rows = document.querySelectorAll('.coach-row');
        let counter = 1;

        rows.forEach(row => {
            const rowTabStatus = row.getAttribute('data-tab-status');
            const rowSport = row.getAttribute('data-sport');
            const rowStatus = row.getAttribute('data-status');
            const rowPosition = row.getAttribute('data-position');
            
            const names = Array.from(row.querySelectorAll('.searchable-name')).map(td => td.textContent.toLowerCase()).join(' ');
            const id = row.querySelector('.searchable-id').textContent.toLowerCase();
            const matchesSearch = names.includes(searchInput) || id.includes(searchInput);

            if (rowTabStatus === currentCoachTab &&
                matchesSearch &&
                (sportFilter === "" || rowSport.includes(sportFilter)) &&
                (statusFilter === "" || rowStatus === statusFilter) &&
                (positionFilter === "" || rowPosition.includes(positionFilter))) {
                
                row.style.display = '';
                const serialCell = row.querySelector('.serial-number');
                if (serialCell) serialCell.textContent = counter++;
            } else {
                row.style.display = 'none';
            }
        });
        checkCoachFilterState();
    }

    document.getElementById('clearCoachFilters').addEventListener('click', function() {
        document.getElementById('coachSearchInput').value = '';
        document.getElementById('coachSportFilter').value = '';
        document.getElementById('coachPositionFilter').value = '';
        switchCoachTab('Active');
        this.classList.add('hidden');
    });

    document.getElementById('coachSearchInput').addEventListener('input', applyCoachFilters);
    document.getElementById('coachSportFilter').addEventListener('change', applyCoachFilters);
    document.getElementById('coachStatusFilter').addEventListener('change', applyCoachFilters);
    document.getElementById('coachPositionFilter').addEventListener('change', applyCoachFilters);

    document.addEventListener("DOMContentLoaded", () => {
        switchCoachTab('Active');
    });
</script>

@endsection