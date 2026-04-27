<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')
                  ->constrained('invoices')
                  ->cascadeOnDelete();

            $table->decimal('collected_amount', 12, 2);

            // cheque / neft / upi / cash
            $table->string('payment_mode', 30)->default('cheque');

            // Bank transaction ref, UTR, cheque number etc.
            $table->string('reference_number')->nullable();

            $table->text('remarks')->nullable();

            // Who entered this collection (Accounts team user)
            // $table->foreignId('collected_by')
            //       ->constrained('users')
            //       ->cascadeOnDelete();
            $table->unsignedBigInteger('collected_by')->nullable();

            $table->date('collected_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
