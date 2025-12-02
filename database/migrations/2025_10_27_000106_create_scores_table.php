<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('subject_id');
            $table->decimal('ca_score', 5, 2)->default(0);
            $table->decimal('exam_score', 5, 2)->default(0);
            $table->decimal('total_score', 5, 2)->storedAs('ca_score + exam_score');
            $table->string('grade')->nullable();
            $table->string('remark')->nullable();
            $table->uuid('term_id')->nullable();
            $table->uuid('session_id')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('term_id')->references('id')->on('terms')->onDelete('set null');
            $table->foreign('session_id')->references('id')->on('academic_sessions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
