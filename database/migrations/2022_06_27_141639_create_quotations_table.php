<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });


        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_number')->unique();
            $table->date('quotation_date');
            $table->foreignId('user_id');
            $table->foreignId('customer_id');
            $table->foreignId('visit_id');
            $table->text('remarks')->nullable();
        });

        
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id');
            $table->foreignId('product_id');
            $table->decimal('quantity', 10, 2);
            $table->foreignId('unit_id');
            $table->string('description', 250)->nullable();
            $table->decimal('rate', 10, 3);
        });

        Schema::create('quotation_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id');
            $table->string('filename');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('units');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotation_attachments');
    }
}