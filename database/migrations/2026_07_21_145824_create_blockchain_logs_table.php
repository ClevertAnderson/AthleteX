<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Resolutions live in their OWN table, so the original ledger blocks are never edited.
     */
    public function up(): void
    {
        Schema::create('blockchain_resolutions', function (Blueprint $table) {
            $table->id();
            // The block that was flagged as tampered
            $table->unsignedBigInteger('blockchain_log_id')->index();
            // The admin who reviewed it
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('reason');
            // What the ledger expected vs. what was found at the time of review
            $table->string('expected_hash', 64)->nullable();
            $table->string('found_hash', 64)->nullable();
            // Fingerprint of the accepted (tampered) state. If the block is edited AGAIN later,
            // the fingerprint no longer matches and the block is flagged again.
            $table->string('state_fingerprint', 64);
            // The new block that records this resolution in the chain
            $table->unsignedBigInteger('resolution_block_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blockchain_resolutions');
    }
};