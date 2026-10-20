@extends('layouts.app')

@section('content')
@php
    // The table counts 1, 2, 3 from the top, so map real block ids -> the number shown on screen
    $displayMap = [];
    foreach ($logs as $i => $l) { $displayMap[$l->id] = $i + 1; }
    $shown = fn($id) => '#' . ($displayMap[$id] ?? $id);

    $resolvedBlocks = $resolvedBlocks ?? [];
    $blockDetails   = $blockDetails ?? [];
@endphp

<!-- Main Container -->
<div class="p-8 relative z-10">
    
    <h1 class="text-3xl font-bold text-[#2e4e1f] mb-6">Data Integrity Ledger</h1>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #86efac;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-weight:600;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-weight:600;">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <!-- ========================================== -->
    <!-- THE MAIN ALARM BANNER -->
    <!-- ========================================== -->
    @if($isSecure)
        <div class="bg-green-600 text-white p-6 rounded-lg shadow-lg mb-8 flex items-center justify-between transition-all duration-500">
            <div>
                <h2 class="text-2xl font-black mb-1"><i class="bi bi-shield-check mr-2"></i> SYSTEM SECURE</h2>
                <p class="text-green-100">Data Integrity Ledger is Intact. No unresolved database tampering detected.</p>
                @if(count($resolvedBlocks) > 0)
                    <p class="text-green-100" style="margin-top:6px;font-size:13px;">
                        <i class="bi bi-info-circle"></i>
                        {{ count($resolvedBlocks) }} previously flagged block(s) were reviewed and resolved by an administrator (see the "Resolved" rows below).
                    </p>
                @endif
            </div>
            <div class="text-5xl"><i class="bi bi-check-circle-fill"></i></div>
        </div>
    @else
        <div class="bg-red-600 text-white p-6 rounded-lg shadow-lg mb-8 flex items-center justify-between animate-pulse">
            <div>
                <h2 class="text-2xl font-black mb-1"><i class="bi bi-shield-exclamation mr-2"></i> CRITICAL ALERT: DATA TAMPERING DETECTED!</h2>
                <p class="text-red-100">The data integrity ledger is compromised at Block(s): <strong class="text-white">{{ collect($tamperedBlocks)->map($shown)->implode(', ') }}</strong>. Manual database modification detected! An administrator must review and resolve each flagged block.</p>
            </div>
            <div class="text-5xl"><i class="bi bi-exclamation-triangle-fill"></i></div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- THE QUICK STATS SCOREBOARD -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-[#2e4e1f]">
            <p class="text-gray-500 text-xs font-bold tracking-wider mb-1">TOTAL BLOCKS CHAINED</p>
            <p class="text-4xl font-black text-[#2e4e1f]">{{ $totalBlocks }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow border-l-4 {{ $isSecure ? 'border-green-500' : 'border-red-600' }}">
            <p class="text-gray-500 text-xs font-bold tracking-wider mb-1">COMPROMISED RECORDS</p>
            <p class="text-4xl font-black {{ $isSecure ? 'text-green-500' : 'text-red-600' }}">{{ count($tamperedBlocks) }}</p>
            @if(count($resolvedBlocks) > 0)
                <p style="font-size:12px;color:#b45309;font-weight:600;margin-top:4px;">+ {{ count($resolvedBlocks) }} resolved</p>
            @endif
        </div>

        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
            <p class="text-gray-500 text-xs font-bold tracking-wider mb-1">LAST VERIFICATION SCAN</p>
            <p class="text-2xl font-bold text-blue-600 mt-2">{{ now()->format('h:i:s A') }}</p>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- LEDGER FILTERS & SEARCH -->
    <!-- ========================================== -->
    <div class="bg-white p-5 rounded-lg shadow mb-6 border border-gray-200">
        <form method="GET" action="{{ url()->current() }}" class="flex flex-col md:flex-row gap-4 items-end">
            
            <!-- Hidden Submit Button (Guarantees hitting "Enter" in the search bar works) -->
            <button type="submit" class="hidden"></button>

            <!-- Search Bar -->
            <div class="flex-grow w-full">
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Search User or Action</label>
                <div class="flex items-center w-full border border-gray-300 rounded-lg bg-white overflow-hidden shadow-sm focus-within:ring-2 focus-within:ring-[#2e4e1f] transition-all">
                    <div class="pl-3 flex items-center justify-center text-gray-500">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Type and press Enter to search..." 
                        class="w-full border-none focus:outline-none focus:ring-0 py-2.5 px-3 text-sm text-gray-700 bg-transparent m-0">
                </div>
            </div>
            
            <!-- Start Date (Auto-submits on change) -->
            <div class="w-full md:w-44">
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#2e4e1f] focus:outline-none text-sm shadow-sm text-gray-700">
            </div>
            
            <!-- End Date (Auto-submits on change) -->
            <div class="w-full md:w-44">
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#2e4e1f] focus:outline-none text-sm shadow-sm text-gray-700">
            </div>
            
            <!-- Dynamic Clear Button (Only shows if a filter is currently applied) -->
            @if(request()->hasAny(['search', 'start_date', 'end_date']))
            <div class="flex gap-2 w-full md:w-auto">
                <a href="{{ url()->current() }}" class="flex-1 md:flex-none bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 px-5 py-2.5 rounded-lg font-bold shadow-sm transition flex items-center justify-center text-center">
                    <i class="bi bi-x-lg mr-2"></i> Clear
                </a>
            </div>
            @endif
            
        </form>
    </div>

    <!-- ========================================== -->
    <!-- THE IMMUTABLE LEDGER TABLE -->
    <!-- ========================================== -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex flex-col md:flex-row justify-between gap-4 items-center">
            <h3 class="font-bold text-gray-800">Audit Trail History</h3>
            <span class="text-xs font-semibold text-gray-500 bg-gray-200 px-3 py-1 rounded-full">Showing {{ $filteredCount }} result(s)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse relative z-20">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="p-4 border-b">Block #</th>
                        <th class="p-4 border-b">Timestamp</th>
                        <th class="p-4 border-b">User</th>
                        <th class="p-4 border-b">Action</th>
                        <th class="p-4 border-b">Current Hash (Signature)</th>
                        <th class="p-4 border-b text-center">Status</th>
                        <th class="p-4 border-b text-center">Details</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($logs as $log)
                        @php
                            $isTampered = in_array($log->id, $tamperedBlocks);
                            $resolution = $resolvedBlocks[$log->id] ?? null;
                            $detail     = $blockDetails[$log->id] ?? null;
                        @endphp
                        <tr class="hover:bg-gray-50 border-b transition">
                            <!-- Swapped $log->id for $loop->iteration to count 1, 2, 3 downwards -->
                            <td class="p-4 font-bold text-gray-800">#{{ $loop->iteration }}</td>
                            <td class="p-4 text-gray-500 font-medium">{{ $log->created_at->format('M d, Y - h:i A') }}</td>
                            <td class="p-4 font-semibold text-[#2e4e1f]">{{ $log->user ? $log->user->name : 'System/Guest' }}</td>
                            <td class="p-4 text-gray-700">{{ $log->action }}</td>
                            <td class="p-4 font-mono text-xs text-gray-400" title="{{ $log->current_hash }}">
                                {{ substr($log->current_hash, 0, 16) }}...
                            </td>
                            <td class="p-4 text-center">
                                @if($isTampered)
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold border border-red-200 shadow-sm"><i class="bi bi-x-circle-fill mr-1"></i>Tampered</span>
                                @elseif($resolution)
                                    <span style="background:#fef3c7;color:#b45309;border:1px solid #fcd34d;padding:4px 12px;border-radius:9999px;font-size:12px;font-weight:700;white-space:nowrap;display:inline-block;"
                                          title="Resolved by {{ optional($resolution->resolver)->name ?? 'Admin' }} on {{ $resolution->created_at->format('M d, Y h:i A') }} - {{ $resolution->reason }}">
                                        <i class="bi bi-shield-check mr-1"></i>Resolved
                                    </span>
                                @else
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200 shadow-sm"><i class="bi bi-check-circle-fill mr-1"></i>Valid</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div style="display:flex;justify-content:center;align-items:center;gap:8px;flex-wrap:wrap;">
                                    <button type="button" 
                                            class="bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-700 font-semibold py-1.5 px-4 rounded-full text-xs transition shadow-sm view-data-btn"
                                            data-block="{{ $log->id }}"
                                            data-display="{{ $loop->iteration }}"
                                            data-action="{{ $log->action }}"
                                            @if($resolution)
                                                data-resolved-by="{{ optional($resolution->resolver)->name ?? 'Admin' }}"
                                                data-resolved-at="{{ $resolution->created_at->format('M d, Y h:i A') }}"
                                                data-resolved-reason="{{ $resolution->reason }}"
                                            @endif>
                                        View Data
                                    </button>

                                    @if($isTampered && $detail)
                                        <button type="button"
                                                class="resolve-btn"
                                                style="background:#dc2626;color:#fff;border:0;font-weight:700;font-size:12px;padding:7px 16px;border-radius:9999px;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,.2);"
                                                data-block="{{ $log->id }}"
                                                data-display="{{ $loop->iteration }}"
                                                data-expected="{{ $detail['expected'] }}"
                                                data-found="{{ $detail['found'] }}">
                                            <i class="bi bi-wrench-adjustable-circle"></i> Resolve
                                        </button>
                                    @endif
                                </div>
                                
                                <textarea id="payload-{{ $log->id }}" class="hidden">{!! $log->payload ?? $log->details ?? $log->data ?? '{}' !!}</textarea>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500 font-medium">
                                <i class="bi bi-folder-x text-3xl block mb-2 text-gray-300"></i>
                                No ledger records match your current filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div> 
<!-- END OF MAIN CONTAINER -->


<!-- ========================================== -->
<!-- BULLETPROOF SECURITY DATA MODAL -->
<!-- ========================================== -->
<div id="dataModal" class="fixed inset-0 hidden items-center justify-center p-4" style="z-index: 9999; background-color: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px);">
    
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 flex flex-col transform transition-all" style="width: 100%; max-width: 650px; max-height: 75vh;">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b flex flex-col md:flex-row justify-between gap-4 items-center bg-gray-50 rounded-t-xl shrink-0">
            <div class="flex items-center gap-3">
                <div class="bg-green-100 p-2 rounded-full text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Block Details</h3>
            </div>
            <button id="closeModalBtn" class="text-gray-400 hover:text-red-500 transition text-2xl font-bold leading-none">&times;</button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto grow bg-white">
            <div class="mb-5">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Action Recorded</span>
                <p id="modalAction" class="text-md font-bold text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-200"></p>
            </div>

            <!-- Shown only for blocks an admin already resolved -->
            <div id="modalResolution" style="display:none;background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:12px 14px;margin-bottom:20px;color:#92400e;font-size:14px;">
                <strong><i class="bi bi-shield-check"></i> Resolved by an administrator</strong>
                <div style="margin-top:4px;">By: <span id="modalResolvedBy"></span> &middot; <span id="modalResolvedAt"></span></div>
                <div>Reason: <span id="modalResolvedReason"></span></div>
            </div>
            
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Cryptographic Payload (Raw JSON)</span>
                <!-- Hacker Terminal Theme -->
                <div class="rounded-lg p-4 mt-2 overflow-y-auto shadow-inner border border-gray-800" style="background-color: #0d1117; max-height: 300px;">
                    <pre><code id="modalPayload" class="text-sm font-mono whitespace-pre-wrap" style="color: #3fb950; tab-size: 4;"></code></pre>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t bg-gray-50 rounded-b-xl flex justify-end shrink-0">
            <button id="closeModalFooterBtn" class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-2.5 px-6 rounded-lg transition shadow-md">Close Audit View</button>
        </div>
    </div>
</div>


<!-- ========================================== -->
<!-- RESOLVE TAMPERED BLOCK MODAL -->
<!-- ========================================== -->
<div id="resolveModal" class="fixed inset-0 hidden items-center justify-center p-4" style="z-index: 10000; background-color: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px);">
    <div style="background:#fff;border-radius:12px;width:100%;max-width:600px;max-height:90vh;overflow-y:auto;box-shadow:0 25px 50px rgba(0,0,0,.4);">
        <div style="background:#dc2626;color:#fff;padding:16px 24px;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;font-size:18px;font-weight:800;"><i class="bi bi-shield-exclamation"></i> Resolve Block <span id="resolveBlockNum"></span></h3>
            <button type="button" id="resolveCloseX" style="background:none;border:0;color:#fff;font-size:26px;line-height:1;cursor:pointer;">&times;</button>
        </div>

        <form id="resolveForm" method="POST" action="">
            @csrf
            <div style="padding:20px 24px;">
                <p style="margin:0 0 14px;color:#374151;font-size:14px;">
                    This block no longer matches its cryptographic signature, which means the data was changed outside the system (for example directly in the database).
                    Review it, then record your decision. <strong>The original block is not edited or deleted</strong> - your decision is added to the ledger as a new block.
                </p>

                <div style="margin-bottom:10px;">
                    <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Expected signature (stored in the ledger)</div>
                    <div id="resolveExpected" style="font-family:monospace;font-size:12px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:6px;padding:8px;word-break:break-all;color:#166534;"></div>
                </div>
                <div style="margin-bottom:16px;">
                    <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Signature found now (recalculated)</div>
                    <div id="resolveFound" style="font-family:monospace;font-size:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:8px;word-break:break-all;color:#991b1b;"></div>
                </div>

                <label for="resolveReason" style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:4px;">
                    Reason / findings <span style="color:#dc2626;">*</span>
                </label>
                <textarea id="resolveReason" name="reason" rows="3" maxlength="500" required
                          placeholder="e.g. Rows were added manually by IT during database testing; no real records were affected."
                          style="width:100%;box-sizing:border-box;border:1px solid #d1d5db;border-radius:8px;padding:10px;font-size:14px;"></textarea>
                <div style="display:flex;justify-content:space-between;margin-top:4px;font-size:12px;color:#6b7280;">
                    <span id="resolveError" style="color:#dc2626;"></span>
                    <span><span id="resolveCount">0</span>/500 (min. 5)</span>
                </div>
            </div>

            <div style="padding:14px 24px;background:#f9fafb;border-top:1px solid #e5e7eb;border-radius:0 0 12px 12px;display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" id="resolveCancel" style="background:#e5e7eb;color:#374151;border:0;padding:10px 20px;border-radius:8px;font-weight:700;cursor:pointer;">Cancel</button>
                <button type="submit" id="resolveSubmit" disabled style="background:#16a34a;color:#fff;border:0;padding:10px 20px;border-radius:8px;font-weight:700;cursor:pointer;opacity:.5;">
                    <i class="bi bi-check2-circle"></i> Acknowledge &amp; Resolve
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    /* ---------------- VIEW DATA MODAL ---------------- */
    const modal = document.getElementById('dataModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalAction = document.getElementById('modalAction');
    const modalPayload = document.getElementById('modalPayload');
    const modalResolution = document.getElementById('modalResolution');
    
    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex'); 
    };

    document.getElementById('closeModalBtn').addEventListener('click', closeModal);
    document.getElementById('closeModalFooterBtn').addEventListener('click', closeModal);

    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    document.querySelectorAll('.view-data-btn').forEach(button => {
        button.addEventListener('click', function() {
            const blockNumber = this.getAttribute('data-block'); // True database ID for fetching payload
            const displayNum = this.getAttribute('data-display'); // 1, 2, 3 counter for the UI
            const action = this.getAttribute('data-action');
            
            const rawPayload = document.getElementById('payload-' + blockNumber).value;
            let formattedPayload = '';

            try {
                const parsed = JSON.parse(rawPayload);
                formattedPayload = JSON.stringify(parsed, null, 4);
            } catch (e) {
                formattedPayload = rawPayload;
            }

            // Sets the modal title to match the visual 1, 2, 3 counter
            modalTitle.innerText = `Block #${displayNum} Audit Data`;
            modalAction.innerText = action;
            modalPayload.innerText = formattedPayload;

            // Resolution info (only for blocks an admin already resolved)
            if (this.hasAttribute('data-resolved-by')) {
                document.getElementById('modalResolvedBy').innerText = this.getAttribute('data-resolved-by');
                document.getElementById('modalResolvedAt').innerText = this.getAttribute('data-resolved-at');
                document.getElementById('modalResolvedReason').innerText = this.getAttribute('data-resolved-reason');
                modalResolution.style.display = 'block';
            } else {
                modalResolution.style.display = 'none';
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex'); 
        });
    });

    /* ---------------- RESOLVE MODAL ---------------- */
    const resolveModal = document.getElementById('resolveModal');
    const resolveForm = document.getElementById('resolveForm');
    const resolveReason = document.getElementById('resolveReason');
    const resolveSubmit = document.getElementById('resolveSubmit');
    const resolveCount = document.getElementById('resolveCount');
    const resolveError = document.getElementById('resolveError');

    const closeResolve = () => {
        resolveModal.classList.add('hidden');
        resolveModal.classList.remove('flex');
    };

    function updateResolveState() {
        const len = resolveReason.value.trim().length;
        resolveCount.textContent = resolveReason.value.length;
        const ok = len >= 5;
        resolveSubmit.disabled = !ok;
        resolveSubmit.style.opacity = ok ? '1' : '.5';
    }

    document.querySelectorAll('.resolve-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('resolveBlockNum').textContent = '#' + this.getAttribute('data-display');
            document.getElementById('resolveExpected').textContent = this.getAttribute('data-expected');
            document.getElementById('resolveFound').textContent = this.getAttribute('data-found');
            resolveForm.action = "{{ url('/admin/security-ledger') }}/" + this.getAttribute('data-block') + "/resolve";
            resolveReason.value = '';
            resolveError.textContent = '';
            updateResolveState();
            resolveModal.classList.remove('hidden');
            resolveModal.classList.add('flex');
            setTimeout(() => resolveReason.focus(), 50);
        });
    });

    resolveReason.addEventListener('input', updateResolveState);
    document.getElementById('resolveCancel').addEventListener('click', closeResolve);
    document.getElementById('resolveCloseX').addEventListener('click', closeResolve);
    resolveModal.addEventListener('click', function(e) { if (e.target === resolveModal) closeResolve(); });

    resolveForm.addEventListener('submit', function(e) {
        if (resolveReason.value.trim().length < 5) {
            e.preventDefault();
            resolveError.textContent = 'Please type a reason (at least 5 characters).';
            return;
        }
        resolveSubmit.disabled = true;
        resolveSubmit.innerHTML = 'Saving...';
    });
});
</script>
@endsection