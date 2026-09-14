<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->integer('no_of_boxes')->default(0)->after('dispatch_date');
            $table->decimal('total_weight', 10, 2)->default(0)->after('no_of_boxes');
        });
    }

    public function down(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropColumn(['no_of_boxes', 'total_weight']);
        });
    }
};
