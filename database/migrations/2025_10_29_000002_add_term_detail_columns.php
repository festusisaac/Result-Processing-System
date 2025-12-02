<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terms', function (Blueprint $table) {
            $table->date('begins_at')->nullable()->after('active');
            $table->date('ends_at')->nullable()->after('begins_at');
            $table->integer('times_open')->nullable()->after('ends_at');
            $table->string('terminal_duration')->nullable()->after('times_open');
            $table->date('next_term_begins')->nullable()->after('terminal_duration');
        });
    }

    public function down(): void
    {
        // Skipped - the fresh table creation migration (2025_10_29_000003) drops the entire table
        // so this alteration is no longer needed on rollback.
    }
};
