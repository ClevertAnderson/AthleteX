<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlockchainLog;
use App\Services\BlockchainService;

class BlockchainController extends Controller
{
    public function index(Request $request)
    {
        // 1. Run the cryptographic math engine to check if the chain is broken
        $verification = BlockchainService::verifyChain();
        
        // 2. Always get the TOTAL absolute blocks for the scoreboard (unfiltered)
        $totalBlocks = BlockchainLog::count();

        // 3. Build the query engine (🚀 STRICTLY LATEST TO OLDEST BY TIMESTAMP)
        $query = BlockchainLog::with('user')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        // Apply Date Filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Apply Keyword Search (Strictly searches 'name' to prevent SQL errors)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('action', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQ) use ($search) {
                      $userQ->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // 4. Fetch the final filtered logs
        $logs = $query->get();
        
        // 5. Send data to the dashboard
        return view('features.blockchain_dashboard', [
            'isSecure' => $verification['is_secure'],
            // Flagged AND not yet resolved (these get the red status + Resolve button)
            'tamperedBlocks' => $verification['tampered_blocks'],
            // Flagged but already reviewed by an admin: [block id => resolution (reason, who, when)]
            'resolvedBlocks' => $verification['resolved_blocks'],
            // Expected vs. found hash for every flagged block (shown in the Resolve window)
            'blockDetails' => $verification['details'],
            'logs' => $logs,
            'totalBlocks' => $totalBlocks,
            'filteredCount' => $logs->count()
        ]);
    }

    /**
     * Admin reviews a flagged block and resolves it (reason is required).
     */
    public function resolve(Request $request, $id)
    {
        $validated = $request->validate(
            ['reason' => ['required', 'string', 'min:5', 'max:500']],
            [
                'reason.required' => 'Please type the reason for resolving this block.',
                'reason.min'      => 'The reason must be at least 5 characters.',
            ]
        );

        $resolution = BlockchainService::resolveBlock((int) $id, trim($validated['reason']));

        if (! $resolution) {
            return back()->with('error', 'Block #' . $id . ' is not currently flagged, so there is nothing to resolve.');
        }

        return back()->with('success', 'Block #' . $id . ' was reviewed and marked as resolved. The decision was recorded in the ledger.');
    }
}