<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing pivot table
        Schema::dropIfExists('class_subject');

        // Recreate with correct column names
        Schema::create('class_subject', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(uuid())'));
            $table->foreignUuid('class_room_id')->constrained('classes')->onDelete('cascade');
            $table->foreignUuid('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['class_room_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_subject');
    }
};