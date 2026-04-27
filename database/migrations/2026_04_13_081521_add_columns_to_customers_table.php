<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {

            // Unique auto-generated code e.g. SCH-2025-0001
            $table->string('school_code', 20)->unique()->nullable()->after('id');

            // Who brought this school in
            $table->foreignId('lead_source_id')
                  ->nullable()
                  ->constrained('lead_sources')
                  ->nullOnDelete()
                  ->after('school_code');

            // Which SP created this school (for edit-lock logic)
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('lead_source_id');

            // GST number for invoicing
            $table->string('gstin', 15)->nullable()->after('gst_number');

            // School's own PO reference number (institutional)
            $table->string('school_po_number')->nullable()->after('gstin');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['lead_source_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'school_code',
                'lead_source_id',
                'created_by',
                'gstin',
                'school_po_number',
            ]);
        });
    }
};
