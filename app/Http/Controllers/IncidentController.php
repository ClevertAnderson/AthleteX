<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use App\Models\Athlete;
use App\Services\BlockchainService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IncidentController extends Controller
{
    /**
     * Store a newly created incident report in the database.
     * Route: POST /incidents/report
     */
    public function createReport(Request $request)
    {
        // 1. Validate the incoming form data
        $validated = $request->validate([
            'athlete_id'            => 'required|exists:athletes,id',
            'incident_title'        => 'required|string|max:255',
            'incident_type'         => 'required|string|max:255',
            'incident_type_specify' => 'nullable|string|max:255',
            'incident_date'         => 'required|date',
            'incident_time'         => 'required|date_format:H:i',
            'exact_location'        => 'required|string|max:255',
            'persons_involved'      => 'nullable|string|max:255',
            'incident_details'      => 'required|string',
            'immediate_actions'     => 'nullable|string',
        ]);

        try {
            // FIX 1: Use safe navigation (?->) so it doesn't crash if the user has no coach profile
            $coachId = Auth::user()->coach?->id ?? Auth::user()->coach_id ?? null;

            if (!$coachId) {
                return back()->withErrors(['error' => 'You must be assigned as a Coach to submit an incident report.'])->withInput();
            }

            // 3. Create the report
            $incident = IncidentReport::create([
                'athlete_id'            => $validated['athlete_id'],
                'coach_id'              => $coachId,
                'incident_title'        => $validated['incident_title'],
                'incident_type'         => $validated['incident_type'],
                'incident_type_specify' => $validated['incident_type_specify'] ?? null,
                'incident_date'         => $validated['incident_date'],
                'incident_time'         => $validated['incident_time'],
                'exact_location'        => $validated['exact_location'],
                'persons_involved'      => $validated['persons_involved'] ?? null,
                'incident_details'      => $validated['incident_details'],
                'immediate_actions'     => $validated['immediate_actions'] ?? null,
                'status'                => 'Pending',
            ]);

            // FIX 2: Temporarily wrapped the Blockchain service in a try/catch so it doesn't break the submission if the class is missing
            try {
                if (class_exists(\App\Services\BlockchainService::class)) {
                    \App\Services\BlockchainService::logAction('Created Incident Report: ' . $incident->incident_title, $incident->toArray());
                }
            } catch (\Exception $blockchainError) {
                \Illuminate\Support\Facades\Log::warning('Blockchain log skipped: ' . $blockchainError->getMessage());
            }

            return redirect()->back()->with('success', 'Incident report successfully submitted and is pending SDO review.');

        } catch (\Exception $e) {
            // FIX 3: This will physically print the exact line of code that crashed on your screen!
            return back()->withErrors(['error' => 'CRASH DETAILS: ' . $e->getMessage() . ' on line ' . $e->getLine()])->withInput();
        }
    }

    /**
     * Update the status of an existing incident report.
     * Route: PUT /incidents/approve/{report_id}
     */
    public function approveTicket(Request $request, $report_id)
    {
        // 1. Validate the incoming status update
        $validated = $request->validate([
            'status' => 'required|in:Pending,SDO_Approved,Ticket_Claimed,Cleared',
            'insurance_ticket_no' => 'nullable|string|max:50'
        ]);

        try {
            // 2. Find and update the record
            $incident = IncidentReport::findOrFail($report_id);
            
            $incident->update([
                'status' => $validated['status'],
                'insurance_ticket_no' => $validated['insurance_ticket_no'] ?? $incident->insurance_ticket_no
            ]);

            // 3. Log the status change
            BlockchainService::logAction('Updated Incident Ticket Status to ' . $validated['status'] . ': ' . $incident->incident_title, $incident->toArray());

            return redirect()->back()->with('success', 'Incident report status successfully updated to ' . str_replace('_', ' ', $validated['status']) . '.');

        } catch (\Exception $e) {
            Log::error('Error updating incident ticket: ' . $e->getMessage());
            return back()->withErrors(['error' => 'System Error: ' . $e->getMessage()])->withInput();
        }
    }
}