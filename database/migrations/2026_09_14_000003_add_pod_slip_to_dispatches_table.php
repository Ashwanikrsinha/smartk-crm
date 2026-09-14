<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            if (!Schema::hasColumn('dispatches', 'pod_slip')) {
                $table->string('pod_slip')->nullable()->after('bilty_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            if (Schema::hasColumn('dispatches', 'pod_slip')) {
                $table->dropColumn('pod_slip');
            }
        });
    }
};
