@extends('layouts.app')

@section('title', 'My Reports')

@section('content')
<div id="tab-content" class="bg-white p-6 rounded w-full">
    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-4" role="alert">
                <div class="flex items-center">
                    <i class="bi bi-check-circle-fill text-xl me-2"></i>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm mb-4" role="alert">
                <div class="flex items-center mb-2">
                    <i class="bi bi-exclamation-triangle-fill text-xl me-2"></i>
                    <span class="font-bold">Please fix the following errors:</span>
                </div>
                <ul class="list-disc ms-8 mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex-1 text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-0">My Reports</h1>
        </div>

        <div class="flex justify-center mb-4">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadReportModal">
                <i class="bi bi-cloud-upload me-1"></i>
                Upload Standard Report
            </button>
        </div>

        <!-- 🚑 SDO SMART INCIDENT FORM WIZARD -->
        <div class="bg-red-50 border-l-4 border-red-500 rounded shadow p-6 mb-6">
            <div class="flex items-center mb-3">
                <i class="bi bi-heart-pulse text-red-600 text-2xl me-2"></i>
                <h2 class="text-xl font-bold text-red-700 mb-0">Official SDO Incident Report</h2>
            </div>
            <p class="text-sm text-red-600 mb-6">Step <span id="step-counter">1</span> of 3: Please provide the details of the incident below.</p>

            <form action="{{ route('incidents.report') }}" method="POST" id="smartIncidentForm">
                @csrf
                <input type="hidden" name="coach_id" value="{{ auth()->user()->coach->id ?? 1 }}">

                <!-- ================= STEP 1: CONTEXT & LOCATION ================= -->
                <div id="step1">
                    <h4 class="text-md font-bold text-gray-800 mb-3 border-b border-red-200 pb-2">Part 1: Context & Location</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-danger">Select Primary Athlete <span class="text-red-500">*</span></label>
                            <select name="athlete_id" class="form-select border-danger text-gray-700" required>
                                <option value="" disabled selected>-- Choose an Athlete --</option>
                                @if(isset($athletes) && count($athletes) > 0)
                                    @foreach($athletes as $athlete)
                                        <option value="{{ $athlete->id }}">{{ $athlete->first_name }} {{ $athlete->last_name }}</option>
                                    @endforeach
                                @else
                                    <option value="" disabled>No athletes found. Please add athletes first.</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-danger">All Person/s Involved <span class="text-red-500">*</span></label>
                            <input type="text" name="persons_involved" class="form-control border-danger" placeholder="Full names of everyone involved" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-danger">Date of Incident <span class="text-red-500">*</span></label>
                            <input type="date" name="incident_date" class="form-control border-danger" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-danger">Time of Incident <span class="text-red-500">*</span></label>
                            <input type="time" name="incident_time" class="form-control border-danger" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-danger">Exact Location <span class="text-red-500">*</span></label>
                            <input type="text" name="exact_location" class="form-control border-danger" placeholder="e.g., Main Gym, Court B" required>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="button" onclick="nextStep(2)" class="btn btn-danger">Next: Incident Type <i class="bi bi-arrow-right ms-1"></i></button>
                    </div>
                </div>

                <!-- ================= STEP 2: INCIDENT TYPE ================= -->
                <div id="step2" class="hidden">
                    <h4 class="text-md font-bold text-gray-800 mb-3 border-b border-red-200 pb-2">Part 2: Incident Classification</h4>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-danger">Incident Title <span class="text-red-500">*</span></label>
                            <input type="text" name="incident_title" class="form-control border-danger" placeholder="A brief title (e.g., Sprained Ankle during Practice)" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-danger">Incident Type <span class="text-red-500">*</span></label>
                            <select id="incident_type" name="incident_type" class="form-select border-danger text-gray-700" onchange="handleTypeChange()" required>
                                <option value="" disabled selected>-- Select Type --</option>
                                <option value="Injury">Injury</option>
                                <option value="Equipment Malfunction">Equipment Malfunction</option>
                                <option value="Facility Hazard">Facility Hazard</option>
                                <option value="Behavioral Issues">Behavioral Issues</option>
                                <option value="Medical Emergency">Medical Emergency</option>
                                <option value="Transportation-Related Incident">Transportation-Related Incident</option>
                                <option value="Property Damage">Property Damage</option>
                                <option value="Unauthorized use of school facility/property">Unauthorized use of school facility/property</option>
                                <option value="Holding of activity without approval">Holding of activity without approval</option>
                                <option value="Intoxicated Person">Intoxicated Person</option>
                                <option value="Smoking">Smoking</option>
                                <option value="Illegal Drugs">Illegal Drugs</option>
                                <option value="Accident">Accident</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>

                        <!-- Dynamic Specifics Container -->
                        <div class="col-md-6 hidden" id="specify_container">
                            <label class="form-label fw-bold text-danger">Please Specify Details <span class="text-red-500">*</span></label>
                            
                            <select id="specify_intoxicated" class="form-select border-danger hidden">
                                <option value="Concealing intoxicating beverages">Concealing intoxicating beverages</option>
                                <option value="Hangover">Hangover</option>
                            </select>

                            <select id="specify_drugs" class="form-select border-danger hidden">
                                <option value="Concealing">Concealing</option>
                                <option value="Using">Using</option>
                                <option value="Selling">Selling</option>
                            </select>

                            <input type="text" id="incident_type_specify" class="form-control border-danger hidden" placeholder="Describe specifics...">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between">
                        <button type="button" onclick="prevStep(1)" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Back</button>
                        <button type="button" onclick="nextStep(3)" class="btn btn-danger">Next: Narrative <i class="bi bi-arrow-right ms-1"></i></button>
                    </div>
                </div>

                <!-- ================= STEP 3: NARRATIVE & ACTIONS ================= -->
                <div id="step3" class="hidden">
                    <h4 class="text-md font-bold text-gray-800 mb-3 border-b border-red-200 pb-2">Part 3: Narrative & Resolution</h4>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-danger">Description of the Incident <span class="text-red-500">*</span></label>
                            <textarea name="incident_details" class="form-control border-danger" rows="3" placeholder="What exactly happened?" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-danger">Immediate Action/s Taken <span class="text-red-500">*</span></label>
                            <textarea name="immediate_actions" class="form-control border-danger" rows="2" placeholder="e.g., Applied ice, notified campus security, sent to clinic" required></textarea>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between">
                        <button type="button" onclick="prevStep(2)" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Back</button>
                        <button type="submit" class="btn btn-danger text-lg font-bold shadow-lg">
                            <i class="bi bi-send-exclamation me-1"></i> Submit Official Report
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- 🧠 JAVASCRIPT FOR THE SMART WIZARD -->
        <script>
            function nextStep(step) {
                let currentStep = step - 1;
                let container = document.getElementById('step' + currentStep);
                let inputs = container.querySelectorAll('input[required], select[required], textarea[required]');
                
                for (let input of inputs) {
                    if (!input.checkValidity()) {
                        input.reportValidity(); 
                        return; 
                    }
                }

                document.getElementById('step1').classList.add('hidden');
                document.getElementById('step2').classList.add('hidden');
                document.getElementById('step3').classList.add('hidden');

                document.getElementById('step' + step).classList.remove('hidden');
                document.getElementById('step-counter').innerText = step;
            }

            function prevStep(step) {
                document.getElementById('step1').classList.add('hidden');
                document.getElementById('step2').classList.add('hidden');
                document.getElementById('step3').classList.add('hidden');

                document.getElementById('step' + step).classList.remove('hidden');
                document.getElementById('step-counter').innerText = step;
            }

            function handleTypeChange() {
                let type = document.getElementById('incident_type').value;
                let specifyContainer = document.getElementById('specify_container');
                let specifyInput = document.getElementById('incident_type_specify');
                let specifySelectIntoxicated = document.getElementById('specify_intoxicated');
                let specifySelectDrugs = document.getElementById('specify_drugs');

                specifyContainer.classList.add('hidden');
                
                specifyInput.classList.add('hidden'); 
                specifyInput.removeAttribute('name');
                specifyInput.removeAttribute('required');
                
                specifySelectIntoxicated.classList.add('hidden'); 
                specifySelectIntoxicated.removeAttribute('name');
                
                specifySelectDrugs.classList.add('hidden'); 
                specifySelectDrugs.removeAttribute('name');

                if (type === 'Intoxicated Person') {
                    specifyContainer.classList.remove('hidden');
                    specifySelectIntoxicated.classList.remove('hidden');
                    specifySelectIntoxicated.setAttribute('name', 'incident_type_specify');
                } else if (type === 'Illegal Drugs') {
                    specifyContainer.classList.remove('hidden');
                    specifySelectDrugs.classList.remove('hidden');
                    specifySelectDrugs.setAttribute('name', 'incident_type_specify');
                } else if (type === 'Accident' || type === 'Others') {
                    specifyContainer.classList.remove('hidden');
                    specifyInput.classList.remove('hidden');
                    specifyInput.setAttribute('name', 'incident_type_specify');
                    specifyInput.setAttribute('required', 'required');
                    specifyInput.placeholder = type === 'Accident' ? 'Describe the accident...' : 'Please specify...';
                }
            }
        </script>

        <!-- 🚑 MEDICAL INCIDENTS HISTORY TABLE -->
        <h3 class="text-xl font-bold text-gray-800 mt-8 mb-4">My Medical Incident Reports</h3>
        <div class="bg-white rounded shadow p-6 overflow-x-auto mb-8 border-t-4 border-red-500">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-red-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Athlete</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Incident Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Ticket No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Date Submitted</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-red-800 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($incidentReports ?? [] as $index => $incident)
                        <tr class="hover:bg-red-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-800">
                                {{ $incident->first_name }} {{ $incident->last_name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $incident->incident_details }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($incident->status === 'Pending')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending SDO Approval</span>
                                @elseif($incident->status === 'SDO_Approved')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">SDO Approved</span>
                                @elseif($incident->status === 'Ticket_Claimed')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ticket Claimed</span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $incident->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm font-bold {{ $incident->insurance_ticket_no ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $incident->insurance_ticket_no ?? 'Awaiting Code...' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($incident->created_at)->format('M d, Y g:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded shadow-sm text-sm transition duration-150" data-bs-toggle="modal" data-bs-target="#viewIncidentModal{{ $incident->id }}">
                                    <i class="bi bi-printer me-1"></i> Print
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="bi bi-shield-check text-3xl mb-2 block text-gray-300"></i>
                                No medical incidents reported yet. Safe season!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Standard Reports Table -->
        <h3 class="text-xl font-bold text-gray-800 mt-8 mb-4">Standard File Uploads</h3>
        <div class="bg-white rounded shadow p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Received</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reports as $index => $report)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">{{ $report->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                                <a href="{{ route('reports.download', $report->id) }}" class="hover:underline">
                                    {{ $report->file_name }}
                                </a>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $report->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ $report->received_at ? $report->received_at->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('reports.download', $report->id) }}" class="text-blue-600 hover:text-blue-800" title="Download">
                                    <i class="bi bi-download"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">No standard reports submitted yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<div class="modal fade" id="uploadReportModal" tabindex="-1" aria-labelledby="uploadReportModalLabel" aria-hidden="true" data-bs-backdrop="false" style="background-color: rgba(0, 0, 0, 0.6); z-index: 105000;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Upload Standard Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form method="POST" action="{{ route('coach.reports.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Report Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3"></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select File <span class="text-red-500">*</span></label>
                        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
                        <small class="form-text text-muted">Max file size: 10MB</small>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Report</button>
                    </div>

                </form>
            </div>

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