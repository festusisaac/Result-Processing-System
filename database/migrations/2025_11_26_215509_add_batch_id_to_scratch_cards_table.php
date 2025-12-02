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
        Schema::table('scratch_cards', function (Blueprint $table) {
            $table->uuid('batch_id')->nullable()->after('session_id');
            $table->foreign('batch_id')->references('id')->on('scratch_card_batches')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scratch_cards', function (Blueprint $table) {
            //
        });
    }
};
