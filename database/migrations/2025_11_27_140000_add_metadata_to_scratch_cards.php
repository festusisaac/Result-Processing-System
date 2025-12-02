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
        if (!Schema::hasTable('scratch_cards')) {
            return;
        }

        Schema::table('scratch_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('scratch_cards', 'metadata')) {
                $table->json('metadata')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('scratch_cards')) {
            return;
        }

        Schema::table('scratch_cards', function (Blueprint $table) {
            if (Schema::hasColumn('scratch_cards', 'metadata')) {
                $table->dropColumn('metadata');
            }
        });
    }
};
