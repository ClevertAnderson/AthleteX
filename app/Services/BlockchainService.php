<?php

namespace App\Services;

use App\Models\BlockchainLog;
use App\Models\BlockchainResolution;
use Illuminate\Support\Facades\Auth;

class BlockchainService
{
    /**
     * This method intercepts an action, hashes it, and chains it to the last block.
     */
    public static function logAction($action, $payloadArray)
    {
        // 1. Get the very last block in the database
        $lastBlock = BlockchainLog::latest('id')->first();
        
        // If there is no previous block, this is the "Genesis" (first) block.
        $previousHash = $lastBlock ? $lastBlock->current_hash : null;

        // 2. Convert the user's data into a JSON string
        $payloadJson = json_encode($payloadArray);

        // 3. Combine everything together to prepare for hashing
        // Even the timestamp is included so the hash is totally unique to this exact second
        $timestamp = now()->toDateTimeString();
        $dataToHash = $action . $payloadJson . $previousHash . $timestamp;

        // 4. GENERATE THE HASH: Run it through the SHA-256 cryptographic algorithm
        $currentHash = hash('sha256', $dataToHash);

        // 5. Save the new chained block permanently to the database
        return BlockchainLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'payload' => $payloadJson,
            'previous_hash' => $previousHash,
            'current_hash' => $currentHash,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    /**
     * Recalculates the entire chain to check for tampering.
     *
     * Returns:
     *  - is_secure        : true when NO unresolved problem exists
     *  - tampered_blocks  : ids of blocks that are flagged and NOT yet resolved
     *  - resolved_blocks  : [block id => BlockchainResolution] for flagged blocks an admin already reviewed
     *  - details          : [block id => ['expected' => ..., 'found' => ..., 'fingerprint' => ...]] for every flagged block
     */
    public static function verifyChain()
    {
        $logs = BlockchainLog::orderBy('id', 'asc')->get();
        $firstId = optional($logs->first())->id;
        $previousHashTracker = null;

        $flagged = [];   // id => details
        foreach ($logs as $log) {
            // 1. RECALCULATE the math to see if the data was edited outside the app.
            // getRawOriginal keeps the database timestamp format exactly as stored.
            $timestamp = $log->getRawOriginal('created_at');
            $dataToHash = $log->action . $log->payload . $log->previous_hash . $timestamp;
            $recalculatedHash = hash('sha256', $dataToHash);

            // 2. The alarm goes off if the content was edited OR the chain link is broken
            if ($recalculatedHash !== $log->current_hash
                || ($log->id != $firstId && $log->previous_hash !== $previousHashTracker)) {

                $flagged[$log->id] = [
                    'expected'    => $log->current_hash,
                    'found'       => $recalculatedHash,
                    // Identifies this exact broken state (so a NEW edit after resolving is caught again)
                    'fingerprint' => hash('sha256', implode('|', [
                        $recalculatedHash,
                        (string) $log->current_hash,
                        (string) $log->previous_hash,
                        (string) $previousHashTracker,
                    ])),
                ];
            }

            $previousHashTracker = $log->current_hash;
        }

        // 3. A flagged block counts as "resolved" only if an admin accepted EXACTLY this state
        $resolutions = $flagged
            ? BlockchainResolution::with('resolver')->whereIn('blockchain_log_id', array_keys($flagged))->get()->groupBy('blockchain_log_id')
            : collect();

        $tamperedBlocks = [];
        $resolvedBlocks = [];

        foreach ($flagged as $id => $info) {
            $match = ($resolutions[$id] ?? collect())->firstWhere('state_fingerprint', $info['fingerprint']);
            if ($match) {
                $resolvedBlocks[$id] = $match;
            } else {
                $tamperedBlocks[] = $id;
            }
        }

        return [
            'is_secure'       => count($tamperedBlocks) === 0,
            'tampered_blocks' => $tamperedBlocks,
            'resolved_blocks' => $resolvedBlocks,
            'details'         => $flagged,
        ];
    }

    /**
     * An admin reviews a flagged block and records the resolution.
     * The original block is NEVER edited: the decision is stored in its own table
     * AND written as a brand-new block at the end of the chain.
     *
     * @return BlockchainResolution|null  null when the block is not currently flagged
     */
    public static function resolveBlock(int $blockId, string $reason): ?BlockchainResolution
    {
        $verification = self::verifyChain();

        // Only an unresolved, currently-flagged block can be resolved
        if (! in_array($blockId, $verification['tampered_blocks'], true)) {
            return null;
        }

        $info = $verification['details'][$blockId];

        // 1. Record the decision in the chain itself (new block appended at the end)
        $resolutionBlock = self::logAction(
            'Integrity issue on Block #' . $blockId . ' reviewed and resolved. Reason: ' . $reason,
            [
                'resolved_block'  => $blockId,
                'expected_hash'   => $info['expected'],
                'found_hash'      => $info['found'],
                'reason'          => $reason,
                'resolved_by'     => Auth::id(),
            ]
        );

        // 2. Remember exactly which broken state was accepted
        return BlockchainResolution::create([
            'blockchain_log_id'   => $blockId,
            'resolved_by'         => Auth::id(),
            'reason'              => $reason,
            'expected_hash'       => $info['expected'],
            'found_hash'          => $info['found'],
            'state_fingerprint'   => $info['fingerprint'],
            'resolution_block_id' => $resolutionBlock->id,
        ]);
    }
}