<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::create('visit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id');
            $table->decimal('quantity', 10, 2);
            $table->foreignId('unit_id');
            $table->string('description', 250)->nullable();
            $table->decimal('rate', 10, 3);
        });

        Schema::create('visit_attachemts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id');
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
        Schema::dropIfExists('visit_attachments');
    }
}
