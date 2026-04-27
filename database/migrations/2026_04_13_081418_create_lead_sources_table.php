<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Seed default values
        DB::table('lead_sources')->insert([
            ['name' => 'Digital Campaign', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Organic',          'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Referral',         'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cold Calling',     'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Channel Partner',  'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_sources');
    }
};
