<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // BM approval tracking (SM approval already uses approved_by/approved_at)
            $table->foreignId('bm_approved_by')
                  ->nullable()
                  ->after('approved_by')
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('bm_approved_at')->nullable()->after('bm_approved_by');

            // SM-level rejection reason (existing rejection_reason is for BM rejection)
            $table->text('sm_rejection_reason')->nullable()->after('rejection_reason');
        });

        // Status values for invoices.status:
        // draft | submitted | sm_approved | approved | rejected | bm_rejected
        // The 'status' column is already a string — no enum change needed.
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['bm_approved_by']);
            $table->dropColumn(['bm_approved_by', 'bm_approved_at', 'sm_rejection_reason']);
        });
    }
};
