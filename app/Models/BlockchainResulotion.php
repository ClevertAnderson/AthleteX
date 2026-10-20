<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockchainResolution extends Model
{
    protected $fillable = [
        'blockchain_log_id',
        'resolved_by',
        'reason',
        'expected_hash',
        'found_hash',
        'state_fingerprint',
        'resolution_block_id',
    ];

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function block()
    {
        return $this->belongsTo(BlockchainLog::class, 'blockchain_log_id');
    }
}