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
            'tamperedBlocks' => $verification['tampered_blocks'],
            'logs' => $logs,
            'totalBlocks' => $totalBlocks,
            'filteredCount' => $logs->count()
        ]);
    }
}