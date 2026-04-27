<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {

            // HSN code for GST compliance
            $table->string('hsn_code', 20)->nullable();

            // MRP visible to SP in PO form
            $table->decimal('mrp', 10, 2)->default(0)->after('rate');

            // School-specific selling price
            $table->decimal('school_price', 10, 2)->default(0)->after('mrp');

            // GST percentage for this product (e.g. 18)
            $table->decimal('gst_rate', 5, 2)->default(0)->after('school_price');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['hsn_code', 'mrp', 'school_price', 'gst_rate']);
        });
    }
};
