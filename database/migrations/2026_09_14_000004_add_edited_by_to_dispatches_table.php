<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            if (!Schema::hasColumn('dispatches', 'edited_by')) {
                $table->unsignedBigInteger('edited_by')->nullable()->after('dispatched_by');
            }
            if (!Schema::hasColumn('dispatches', 'edited_at')) {
                $table->timestamp('edited_at')->nullable()->after('edited_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            if (Schema::hasColumn('dispatches', 'edited_by')) {
                $table->dropColumn('edited_by');
            }
            if (Schema::hasColumn('dispatches', 'edited_at')) {
                $table->dropColumn('edited_at');
            }
        });
    }
};
