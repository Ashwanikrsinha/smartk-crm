<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')
                  ->constrained('invoices')
                  ->cascadeOnDelete();

            $table->decimal('billed_amount', 12, 2);

            // 'crm' = raised via CRM bill, 'manual' = Tally/offline entry
            $table->string('source', 20)->default('manual');

            // Bill reference if CRM (bill_id), or Tally/external reference
            $table->string('reference_number')->nullable();

            $table->text('remarks')->nullable();

            $table->foreignId('entered_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->date('billed_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_entries');
    }
};
