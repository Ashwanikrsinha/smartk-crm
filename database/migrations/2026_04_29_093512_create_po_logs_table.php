<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // What changed
            $table->string('action', 50); // 'billed', 'collected', 'pdc_cleared', 'status_change', 'approved', 'rejected'
            $table->string('reference_number')->nullable();
            $table->string('payment_mode', 30)->nullable();
            $table->string('billing_source', 20)->nullable(); // manual / crm
            $table->decimal('amount', 12, 2)->default(0);

            // Snapshot of invoice state AFTER this entry
            $table->decimal('snapshot_po_amount',      12, 2)->default(0);
            $table->decimal('snapshot_billed_amount',  12, 2)->default(0);
            $table->decimal('snapshot_collected',      12, 2)->default(0);
            $table->decimal('snapshot_outstanding',    12, 2)->default(0);
            $table->decimal('snapshot_pending_po',     12, 2)->default(0);

            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('invoice_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_logs');
    }
};
