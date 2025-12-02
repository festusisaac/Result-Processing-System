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
        if (!Schema::hasTable('scratch_card_batches')) {
            return;
        }

        Schema::table('scratch_card_batches', function (Blueprint $table) {
            if (!Schema::hasColumn('scratch_card_batches', 'session_id')) {
                $table->uuid('session_id')->nullable()->after('name');
                $table->foreign('session_id')
                      ->references('id')
                      ->on('academic_sessions')
                      ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('scratch_card_batches')) {
            return;
        }

        Schema::table('scratch_card_batches', function (Blueprint $table) {
            if (Schema::hasColumn('scratch_card_batches', 'session_id')) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $sm->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
                // Drop foreign if exists
                try {
                    $table->dropForeign(['session_id']);
                } catch (\Exception $e) {
                    // ignore if foreign does not exist
                }

                $table->dropColumn('session_id');
            }
        });
    }
};
