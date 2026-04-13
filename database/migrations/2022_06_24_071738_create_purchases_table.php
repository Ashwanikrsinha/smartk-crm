<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->nullable();
            $table->date('voucher_date');
            $table->string('voucher_type')->nullable();
            $table->string('gu_id');
            $table->string('party_name')->nullable();
            $table->string('ledger_name');
            $table->string('entered_by')->nullable();
            $table->json('buyer_address_list')->nullable();
            $table->json('inventories_list')->nullable();
            $table->json('ledger_entries_list')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
