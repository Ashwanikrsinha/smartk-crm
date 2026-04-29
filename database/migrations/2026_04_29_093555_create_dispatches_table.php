<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->cascadeOnDelete();

            $table->string('dispatch_number')->unique(); // DISP-2025-0001
            $table->date('dispatch_date');

            // Optional transport details
            $table->string('bilty_number')->nullable();
            $table->string('challan_number')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();

            $table->text('remarks')->nullable();

            $table->foreignId('dispatched_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->index('invoice_id');
        });

        Schema::create('dispatch_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dispatch_id')
                ->constrained('dispatches')
                ->cascadeOnDelete();

            $table->foreignId('invoice_item_id')
                ->constrained('invoice_items')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('items');

            $table->decimal('quantity_dispatched', 10, 2);
            $table->string('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_items');
        Schema::dropIfExists('dispatches');
    }
};
