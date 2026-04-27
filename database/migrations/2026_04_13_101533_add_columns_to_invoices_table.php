<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {

            // Unique PO number e.g. PO-2025-0001
            $table->string('po_number', 20)->unique()->nullable()->after('id');

            // Delivery due date from SP
            $table->date('delivery_due_date')->nullable()->after('invoice_date');

            // PO lifecycle status
            // Old status column had: not interested / follow up / mature
            // We modify it to the new workflow states
            // NOTE: run this AFTER backing up old status data if needed
            $table->string('status')->default('draft')->change();

            // Approval tracking
            // $table->foreignId('approved_by')
            //       ->nullable()
            //       ->constrained('users')
            //       ->nullOnDelete()
            //       ->after('status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');

            $table->timestamp('approved_at')->nullable()->after('approved_by');

            // Rejection tracking
            $table->text('rejection_reason')->nullable()->after('approved_at');

            // Financial tracking columns (A, B, C, D, E)
            // A = amount (already exists on invoices table — total PO amount)
            // B = billing_amount (invoice/sales bill raised against this PO)
            $table->decimal('billing_amount', 12, 2)->default(0)->after('amount');

            // D = collected_amount (sum of all collections against this PO)
            $table->decimal('collected_amount', 12, 2)->default(0)->after('billing_amount');

            // E = outstanding_amount (billing_amount - collected_amount), stored for fast queries
            $table->decimal('outstanding_amount', 12, 2)->default(0)->after('collected_amount');

            // C = pending_po_amount (amount - billing_amount), stored for fast queries
            $table->decimal('pending_po_amount', 12, 2)->default(0)->after('outstanding_amount');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'po_number',
                'delivery_due_date',
                'approved_by',
                'approved_at',
                'rejection_reason',
                'billing_amount',
                'collected_amount',
                'outstanding_amount',
                'pending_po_amount',
            ]);
            $table->string('status')->default('follow up')->change();
        });
    }
};
