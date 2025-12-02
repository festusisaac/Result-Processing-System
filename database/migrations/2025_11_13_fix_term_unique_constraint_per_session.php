<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove old global UNIQUE constraint on term_name
        Schema::table('terms', function (Blueprint $table) {
            // Drop the simple UNIQUE index on term_name
            $table->dropUnique(['term_name']);
        });

        // Add composite UNIQUE constraint: term_name + session_id (so each session has its own FIRST/SECOND/THIRD)
        Schema::table('terms', function (Blueprint $table) {
            $table->unique(['term_name', 'session_id'], 'unique_term_name_per_session');
        });
    }

    public function down(): void
    {
        Schema::table('terms', function (Blueprint $table) {
            $table->dropUnique('unique_term_name_per_session');
            $table->unique('term_name');
        });
    }
};
