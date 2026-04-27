<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdcs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')
                  ->constrained('invoices')
                  ->cascadeOnDelete();

            // PDC label e.g. PDC 1, PDC 2
            $table->string('pdc_label', 20)->default('PDC 1');

            $table->date('cheque_date');
            $table->string('cheque_number');
            $table->string('bank_name')->nullable();
            $table->decimal('amount', 12, 2);

            // pending / cleared / bounced
            $table->string('status', 20)->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdcs');
    }
};
