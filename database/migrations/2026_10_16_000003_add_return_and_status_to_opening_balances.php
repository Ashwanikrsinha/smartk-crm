<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_opening_balances', function (Blueprint $table) {
            if (!Schema::hasColumn('school_opening_balances', 'status')) {
                $table->string('status', 30)->default('carry_forward')->after('source');
            }
        });

        Schema::table('opening_balance_collections', function (Blueprint $table) {
            if (!Schema::hasColumn('opening_balance_collections', 'billed_amount')) {
                $table->decimal('billed_amount', 14, 2)->default(0)->after('collected_amount');
            }
            if (!Schema::hasColumn('opening_balance_collections', 'gst_amount')) {
                $table->decimal('gst_amount', 14, 2)->default(0)->after('billed_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('opening_balance_collections', function (Blueprint $table) {
            if (Schema::hasColumn('opening_balance_collections', 'gst_amount')) {
                $table->dropColumn('gst_amount');
            }
            if (Schema::hasColumn('opening_balance_collections', 'billed_amount')) {
                $table->dropColumn('billed_amount');
            }
        });

        Schema::table('school_opening_balances', function (Blueprint $table) {
            if (Schema::hasColumn('school_opening_balances', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
