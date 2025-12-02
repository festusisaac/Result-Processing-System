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
        // Add session_id to all relevant tables
        $tables = ['terms', 'students', 'scores', 'comments', 'attendance'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'session_id')) {
                        $table->uuid('session_id')->nullable()->after('id');
                        $table->foreign('session_id')
                            ->references('id')
                            ->on('academic_sessions')
                            ->onDelete('cascade');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['terms', 'students', 'scores', 'comments', 'attendance'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'session_id')) {
                        $table->dropForeign(['session_id']);
                        $table->dropColumn('session_id');
                    }
                });
            }
        }
    }
};