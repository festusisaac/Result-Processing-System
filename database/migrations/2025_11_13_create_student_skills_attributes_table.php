<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_skills_attributes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('skill_attribute_id');
            $table->uuid('term_id')->nullable();
            $table->integer('score')->default(0); // 1-5 scale
            $table->uuid('recorded_by')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('skill_attribute_id')->references('id')->on('skills_attributes')->onDelete('cascade');
            $table->foreign('term_id')->references('id')->on('terms')->onDelete('set null');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_skills_attributes');
    }
};
