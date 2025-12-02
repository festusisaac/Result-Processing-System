<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete existing affective traits (skills that don't start with psychomotor-)
        \App\Models\SkillsAttribute::where('slug', 'not like', 'psychomotor-%')->forceDelete();

        // Run the seeder to insert new skills
        $seeder = new \Database\Seeders\SkillsAttributeSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No operation
    }
};
