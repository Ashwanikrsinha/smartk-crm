<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ═══════════════════════════════════════════════════
// Run: php artisan notifications:table
// Then: php artisan migrate
// This migration handles the leave table addition
// ═══════════════════════════════════════════════════

return new class extends Migration
{
    public function up(): void
    {
        // Add manager_remarks to leaves table
        Schema::table('leaves', function (Blueprint $table) {
            $table->text('manager_remarks')->nullable()->after('status');
            $table->string('leave_type', 30)->nullable()->after('user_id');
        });

        // notifications table is created via:
        // php artisan notifications:table
        // php artisan migrate
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn(['manager_remarks', 'leave_type']);
        });
    }
};
