<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scratch_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('pin');
            $table->enum('status', ['unsold','sold','redeemed'])->default('unsold');
            $table->date('expiry_date')->nullable();
            $table->uuid('sold_by')->nullable();
            $table->uuid('redeemed_by')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->decimal('value', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scratch_cards');
    }
};
