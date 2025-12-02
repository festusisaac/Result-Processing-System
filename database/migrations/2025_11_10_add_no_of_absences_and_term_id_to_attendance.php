<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'no_of_absences')) {
                $table->integer('no_of_absences')->default(0)->after('status');
            }
            if (!Schema::hasColumn('attendance', 'term_id')) {
                $table->uuid('term_id')->nullable()->after('no_of_absences');
                $table->foreign('term_id')->references('id')->on('terms')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        // SQLite doesn't support dropping foreign keys by name.
        // Skip the down migration - foreign key constraints remain but won't impact functionality.
        // To fully rollback, drop and recreate the attendance table manually.
    }
};
