<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number');
            $table->string('reference_number');
            $table->string('reference_type');
            $table->string('voucher_type');
            $table->string('voucher_master_id');
            $table->string('voucher_alter_id');
            $table->date('voucher_date');
            $table->string('company_name');
            $table->decimal('amount', 15, 2);
            $table->string('payment_mode');
            $table->string('cheque_number');
            $table->date('cheque_date');
            $table->string('bank_name');
            $table->string('debit_ledger');
            $table->string('credit_ledger');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
