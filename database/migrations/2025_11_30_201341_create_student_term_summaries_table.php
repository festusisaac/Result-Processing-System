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
        Schema::create('student_term_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('term_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            
            $table->decimal('total_score', 8, 2)->default(0);
            $table->decimal('average_score', 8, 2)->default(0);
            $table->integer('position')->nullable();
            $table->integer('class_size')->default(0);
            $table->integer('number_of_subjects')->default(0);
            $table->integer('total_obtainable')->default(0);
            
            $table->timestamps();
            
            // Ensure one summary per student per term per session
            $table->unique(['student_id', 'term_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_term_summaries');
    }
};
