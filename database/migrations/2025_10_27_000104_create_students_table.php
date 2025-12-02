<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('adm_no')->unique();
            $table->string('full_name');
            $table->uuid('class_id')->nullable();
            $table->uuid('session_id')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->timestamps();

            $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');
            $table->foreign('session_id')->references('id')->on('academic_sessions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
