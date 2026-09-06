@extends('layouts.app')

@section('title', 'SDO Reports Dashboard')

@section('content')
<div id="tab-content" class="bg-white p-6 rounded w-full">
    <div class="space-y-6">

        <!-- 📢 FEEDBACK ALERTS -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-4" role="alert">
                <div class="flex items-center">
                    <i class="bi bi-check-circle-fill text-xl me-2"></i>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm mb-4" role="alert">
                <div class="flex items-center mb-2">
                    <i class="bi bi-exclamation-triangle-fill text-xl me-2"></i>
                    <span class="font-bold">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Page Header -->
        <div class="flex-1 text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-0">SDO Reports Dashboard</h1>
        </div>

        <!-- ============================================== -->
        <!-- 🚑 SDO EMERGENCY MEDICAL DASHBOARD (TABBED)  -->
        <!-- ============================================== -->
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-xl font-bold text-red-700 mb-0">
                <i class="bi bi-exclamation-octagon-fill me-2"></i> Medical Incidents
            </h3>
        </div>

        <!-- TABS NAVIGATION -->
        <nav class="flex border-b-2 border-gray-200 mb-4">
            <button id="tab-pending" onclick="switchIncidentTab('pending')" 
                class="px-6 py-3 font-semibold text-red-700 border-b-4 border-red-700 transition flex items-center gap-2">
                <i class="bi bi-hourglass-split"></i> Pending Reports
            </button>
            <button id="tab-approved" onclick="switchIncidentTab('approved')" 
                class="px-6 py-3 font-semibold text-gray-500 border-b-4 border-transparent hover:text-green-700 transition flex items-center gap-2">
                <i class="bi bi-check-circle"></i> Approved Reports
            </button>
        </nav>

        <!-- PENDING TABLE TAB -->
        <div id="table-pending" class="bg-white rounded shadow p-6 overflow-x-auto mb-8 border-t-4 border-red-600 transition-all">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-red-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase">Athlete</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase">Incident Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase">Date Submitted</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-red-800 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse(collect($incidentReports ?? [])->where('status', 'Pending') as $incident)
                        <tr class="hover:bg-red-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#{{ $incident->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-800">
                                {{ $incident->first_name }} {{ $incident->last_name }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-red-700">{{ $incident->incident_title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($incident->created_at)->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center space-x-2">
                                <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded shadow-sm text-sm transition duration-150" data-bs-toggle="modal" data-bs-target="#viewIncidentModal{{ $incident->id }}">
                                    <i class="bi bi-file-earmark-text"></i> View Form
                                </button>
                                
                                <form action="/incidents/approve/{{ $incident->id }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded shadow-sm text-sm transition duration-150">
                                        <i class="bi bi-check2-circle"></i> Confirm & Approve
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="bi bi-shield-check text-3xl mb-2 block text-gray-300"></i>
                                No pending medical incidents. You are all caught up!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- APPROVED TABLE TAB -->
        <div id="table-approved" class="hidden bg-white rounded shadow p-6 overflow-x-auto mb-8 border-t-4 border-green-600 transition-all">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-green-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase">Athlete</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase">Incident Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase">Ticket Number</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-green-800 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse(collect($incidentReports ?? [])->where('status', '!=', 'Pending') as $incident)
                        <tr class="hover:bg-green-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#{{ $incident->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-800">
                                {{ $incident->first_name }} {{ $incident->last_name }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-700">{{ $incident->incident_title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-green-100 text-green-800">
                                    <i class="bi bi-upc-scan me-1"></i> {{ $incident->insurance_ticket_no }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded shadow-sm text-sm transition duration-150" data-bs-toggle="modal" data-bs-target="#viewIncidentModal{{ $incident->id }}">
                                    <i class="bi bi-file-earmark-text"></i> View Document
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="bi bi-folder2-open text-3xl mb-2 block text-gray-300"></i>
                                No approved incidents yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <script>
            function switchIncidentTab(tab) {
                const pendingTabBtn = document.getElementById('tab-pending');
                const approvedTabBtn = document.getElementById('tab-approved');
                const tablePending = document.getElementById('table-pending');
                const tableApproved = document.getElementById('table-approved');

                if (tab === 'pending') {
                    pendingTabBtn.className = "px-6 py-3 font-semibold text-red-700 border-b-4 border-red-700 transition flex items-center gap-2";
                    approvedTabBtn.className = "px-6 py-3 font-semibold text-gray-500 border-b-4 border-transparent hover:text-green-700 transition flex items-center gap-2";
                    tablePending.classList.remove('hidden');
                    tableApproved.classList.add('hidden');
                } else {
                    approvedTabBtn.className = "px-6 py-3 font-semibold text-green-700 border-b-4 border-green-700 transition flex items-center gap-2";
                    pendingTabBtn.className = "px-6 py-3 font-semibold text-gray-500 border-b-4 border-transparent hover:text-red-700 transition flex items-center gap-2";
                    tableApproved.classList.remove('hidden');
                    tablePending.classList.add('hidden');
                }
            }
        </script>
        <!-- ============================================== -->

        <!-- Standard Reports Table -->
        <h3 class="text-xl font-bold text-gray-800 mt-8 mb-4">Standard Coach Reports</h3>
        <div class="bg-white rounded shadow p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Coach</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reports as $index => $report)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $report->coach->coach_first_name }} {{ $report->coach->coach_last_name }}</td>
                            <td class="px-6 py-4">{{ $report->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                                {{ $report->file_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($report->status === 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($report->status === 'received')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Received</span>
                                @elseif($report->status === 'rejected')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $report->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <a href="{{ route('reports.download', $report->id) }}" class="text-blue-600 hover:text-blue-800" title="Download">
                                    <i class="bi bi-download"></i>
                                </a>

                                @if($report->status === 'pending')
                                    <form method="POST" action="{{ route('reports.mark-received', $report->id) }}" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-600 hover:text-green-800" title="Mark as Received" onclick="return confirm('Mark this report as received?')">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('reports.mark-rejected', $report->id) }}" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Mark as Rejected" onclick="return confirm('Mark this report as rejected?')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">No reports submitted yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- 📝 FORMAL SDO INCIDENT REPORT MODAL / PRINT -->
<!-- ========================================== -->
<style>
    @media print {
        /* 1. Completely hide the main dashboard so it takes up zero space */
        #tab-content {
            display: none !important;
        }

        /* 2. Remove the dark shadow overlay from the modal and make it pure white */
        .modal {
            background-color: white !important; 
            padding: 0 !important;
        }

        /* 3. Stretch the document to fill the paper */
        .modal-dialog {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
        }

        .modal-content {
            border: none !important;
            box-shadow: none !important;
        }

        /* 4. Hide the close buttons and print buttons */
        .modal-header, .modal-footer {
            display: none !important;
        }

        /* 5. Force the paper size and keep the colors */
        @page { 
            size: letter portrait;
            margin: 0.5in; 
        }
        * { 
            -webkit-print-color-adjust: exact !important; 
            print-color-adjust: exact !important; 
        }
    }
</style>

@foreach($incidentReports ?? [] as $incident)
<div class="modal fade" id="viewIncidentModal{{ $incident->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background-color: rgba(0,0,0,0.6); z-index: 99999;">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content shadow-2xl rounded-0 border-0">
            
            <!-- Web UI Header (Hidden when printing) -->
            <div class="modal-header bg-light border-bottom print:hidden">
                <h5 class="modal-title fw-bold text-dark">Document Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- ACTUAL PAPER DOCUMENT START -->
            <div class="modal-body p-10 bg-white" id="printArea{{ $incident->id }}">
                
                <!-- OFFICIAL LETTERHEAD (With Actual Logo) -->
                <div class="flex justify-between items-end pb-2 border-b-2 border-black mb-4">
                    <div class="flex items-center">
                        <!-- Official UC Logo (.png) -->
                        <img src="{{ asset('images/UC_Official_Logo.png') }}" alt="University of the Cordilleras" class="h-14 object-contain" style="max-width: 300px;">
                    </div>
                    <div class="text-sm font-semibold italic text-black pb-1">
                        Sports Development Office
                    </div>
                </div>

                <!-- Document Title -->
                <div class="text-center mb-6">
                    <h2 class="text-lg font-black text-black uppercase tracking-widest m-0">Incident Report Form</h2>
                </div>

                <!-- Traditional Paper Tables -->
                <table class="w-full border-collapse border border-black text-sm text-black mb-4">
                    <tr>
                        <td class="border border-black p-2 w-1/4 font-bold bg-gray-100">IR Number:</td>
                        <td class="border border-black p-2 w-1/4 font-mono font-bold">{{ $incident->insurance_ticket_no ?? 'PENDING' }}</td>
                        <td class="border border-black p-2 w-1/4 font-bold bg-gray-100">Date Submitted:</td>
                        <td class="border border-black p-2 w-1/4">{{ \Carbon\Carbon::parse($incident->created_at)->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="border border-black p-2 font-bold bg-gray-100">Incident Title:</td>
                        <td class="border border-black p-2" colspan="3">{{ $incident->incident_title }}</td>
                    </tr>
                    <tr>
                        <td class="border border-black p-2 font-bold bg-gray-100">Classification:</td>
                        <td class="border border-black p-2" colspan="3">{{ $incident->incident_type }} {{ $incident->incident_type_specify ? '- ' . $incident->incident_type_specify : '' }}</td>
                    </tr>
                    <tr>
                        <td class="border border-black p-2 font-bold bg-gray-100">Date Occurred:</td>
                        <td class="border border-black p-2">{{ \Carbon\Carbon::parse($incident->incident_date)->format('M d, Y') }}</td>
                        <td class="border border-black p-2 font-bold bg-gray-100">Time Occurred:</td>
                        <td class="border border-black p-2">{{ \Carbon\Carbon::parse($incident->incident_time)->format('h:i A') }}</td>
                    </tr>
                    <tr>
                        <td class="border border-black p-2 font-bold bg-gray-100">Exact Location:</td>
                        <td class="border border-black p-2" colspan="3">{{ $incident->exact_location }}</td>
                    </tr>
                </table>

                <table class="w-full border-collapse border border-black text-sm text-black mb-4">
                    <tr>
                        <td class="border border-black p-2 w-1/4 font-bold bg-gray-100" colspan="2">Persons Involved</td>
                    </tr>
                    <tr>
                        <td class="border border-black p-2 w-1/4 font-bold">Primary Athlete:</td>
                        <td class="border border-black p-2">{{ $incident->first_name }} {{ $incident->last_name }}</td>
                    </tr>
                    <tr>
                        <td class="border border-black p-2 w-1/4 font-bold">All Person(s) Involved:</td>
                        <td class="border border-black p-2">{{ $incident->persons_involved }}</td>
                    </tr>
                </table>

                <table class="w-full border-collapse border border-black text-sm text-black mb-4">
                    <tr>
                        <td class="border border-black p-2 font-bold bg-gray-100">Description of the Incident</td>
                    </tr>
                    <tr>
                        <td class="border border-black p-4 align-top whitespace-pre-wrap" style="min-height: 120px;">{{ $incident->incident_details }}</td>
                    </tr>
                </table>

                <table class="w-full border-collapse border border-black text-sm text-black mb-8">
                    <tr>
                        <td class="border border-black p-2 font-bold bg-gray-100">Immediate Action(s) Taken</td>
                    </tr>
                    <tr>
                        <td class="border border-black p-4 align-top whitespace-pre-wrap" style="min-height: 120px;">{{ $incident->immediate_actions }}</td>
                    </tr>
                </table>

                <!-- Signatures Matching the Physical Form -->
                <table class="w-full border-collapse text-sm text-black mt-10">
                    <tr>
                        <td class="w-1/2 text-center pb-2 px-8">
                            <div class="border-b border-black w-full mb-1"></div>
                            <span class="font-bold text-black block text-sm">Reporting Individual</span>
                            <span class="text-xs text-black italic">Signature over Printed Name</span>
                        </td>
                        <td class="w-1/2 text-center pb-2 px-8 relative">
                            @if($incident->status !== 'Pending')
                                <!-- Digital Verification Stamp -->
                                <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 rotate-[-5deg] border-4 border-red-600 text-red-600 font-black uppercase tracking-widest px-4 py-1 opacity-60 text-lg pointer-events-none whitespace-nowrap">
                                    SDO VERIFIED
                                </div>
                            @endif
                            <div class="border-b border-black w-full mb-1"></div>
                            <span class="font-bold text-black block text-sm">SDO Director</span>
                            <span class="text-xs text-black italic">University of the Cordilleras</span>
                        </td>
                    </tr>
                </table>

                <!-- Exact Footer text format from your original paper image -->
                <div class="flex justify-between border-t border-black mt-8 pt-1 text-[10px] text-black font-sans">
                    <div>
                        UC-ADM-SDO-FORM-IR<br>
                        August 17, 2023 Rev. 00
                    </div>
                    <div class="text-right">
                        Page 1 of 1
                    </div>
                </div>

            </div>

            <!-- Admin Action Buttons (Hidden when printing) -->
            <div class="modal-footer bg-light border-top p-3 print:hidden">
                <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Close</button>
                
                <div class="flex space-x-2">
                    <button type="button" class="btn btn-dark fw-bold shadow-sm" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print Document
                    </button>

                    <!-- Only show Approve button if Pending AND user is Admin -->
                    @if($incident->status === 'Pending' && auth()->check() && auth()->user()->role === 'admin')
                        <form action="/incidents/approve/{{ $incident->id }}" method="POST" class="m-0">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-danger font-bold px-4 shadow">
                                <i class="bi bi-check2-circle"></i> Approve & Generate Ticket
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endforeach

@endsection