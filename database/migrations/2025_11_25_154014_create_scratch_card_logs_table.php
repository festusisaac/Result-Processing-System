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
        Schema::create('scratch_card_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('scratch_card_id')->nullable(); // Nullable because failed attempts might not match a card
            $table->string('action'); // 'validate', 'redeem', 'generate'
            $table->boolean('status'); // true = success, false = failure
            $table->string('failure_reason')->nullable();
            $table->json('details')->nullable(); // Store input data like pin/serial
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->foreign('scratch_card_id')->references('id')->on('scratch_cards')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scratch_card_logs');
    }
};
