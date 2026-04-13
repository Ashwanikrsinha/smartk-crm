<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('name');
            $table->string('item_id')->nullable();
            $table->foreignId('master_id');
            $table->string('unit')->nullable();
            $table->string('part_number')->nullable();
            $table->string('group');
            $table->string('item_alias')->nullable();
            $table->string('category');
            $table->string('hsn_code');
            $table->decimal('tax_rate', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
