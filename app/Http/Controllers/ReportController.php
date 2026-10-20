<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Coach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function coachIndex()
    {
        $user = auth()->user();
        $coachId = $user->coach->id ?? $user->coach_id ?? null;

        // 1. Get standard file upload reports
        $reports = \App\Models\Report::where('coach_id', $coachId)->get();

        // 2. Get active athletes assigned to this coach for the dropdown
        $athletes = \App\Models\Athlete::where('coach_id', $coachId)
                                     ->where('status', 'Active')
                                     ->orderBy('last_name')
                                     ->get();

        // 3. Get the medical incident reports submitted by this coach
        $incidentReports = \App\Models\IncidentReport::with('athlete')
                                     ->where('coach_id', $coachId)
                                     ->orderBy('created_at', 'desc')
                                     ->get();

        // Map the athlete name directly into the incident object for the Blade view
        $incidentReports->map(function($incident) {
            $incident->first_name = $incident->athlete->first_name ?? 'Unknown';
            $incident->last_name = $incident->athlete->last_name ?? 'Athlete';
            return $incident;
        });

        return view('features.reports.coach_reports', compact('reports', 'athletes', 'incidentReports'));
    }

    // Coach uploads a report
    public function coachStore(Request $request)
    {
        if (auth()->user()->role !== 'coach') {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $coachId = auth()->user()->coach->id ?? null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('reports/' . $coachId, $fileName, 'public');

            Report::create([
                'coach_id' => $coachId,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'status' => 'pending',
            ]);

            return back()->with('success', 'Report submitted successfully!');
        }

        return back()->with('error', 'Failed to upload file.');
    }

    // Admin views all reports
    public function adminIndex()
    {
        // 1. Fetch standard file reports (Keep whatever logic you already had here)
        $reports = \App\Models\Report::orderBy('created_at', 'desc')->get();

        // 2. NEW: Fetch ALL medical incidents across the entire system for the SDO
        $incidentReports = \Illuminate\Support\Facades\DB::table('incident_reports')
            ->join('athletes', 'incident_reports.athlete_id', '=', 'athletes.id')
            ->select('incident_reports.*', 'athletes.first_name', 'athletes.last_name')
            ->orderBy('incident_reports.created_at', 'desc')
            ->get();

        // Pass both variables to the admin view
        return view('features.reports.admin_reports', compact('reports', 'incidentReports'));
    }

    // Admin marks report as received
    public function markReceived(Report $report)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $report->update([
            'status' => 'received',
            'received_at' => now(),
        ]);

        return back()->with('success', 'Report marked as received.');
    }

    // Admin marks report as rejected
    public function markRejected(Report $report)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $report->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Report marked as rejected.');
    }

    // Download report
    public function download(Report $report)
    {
        // Coaches can only download their own reports
        // Admins can download any report
        if (auth()->user()->role === 'coach') {
            $coachId = auth()->user()->coach->id ?? null;
            if ($report->coach_id !== $coachId) {
                abort(403);
            }
        } elseif (auth()->user()->role !== 'admin') {
            abort(403);
        }

        if (!Storage::disk('public')->exists($report->file_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($report->file_path, $report->file_name);
    }

    // ==========================================
    // NEW: ADMIN APPROVE & GENERATE TICKET
    // ==========================================
    public function approveIncident($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Generate a random ticket number (e.g., SDO-IR-58392)
        $ticketNo = 'SDO-IR-' . strtoupper(substr(uniqid(), -5));

        \Illuminate\Support\Facades\DB::table('incident_reports')
            ->where('id', $id)
            ->update([
                'status' => 'SDO_Approved',
                'insurance_ticket_no' => $ticketNo,
                'updated_at' => now()
            ]);

        return back()->with('success', "Incident Approved! Ticket generated: {$ticketNo}");
    }

}
