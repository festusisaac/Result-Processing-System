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
            $table->foreignId('term_id')->nullable()->constrained('terms')->nullOnDelete();
            $table->uuid('session_id')->nullable();
            $table->foreign('session_id')->references('id')->on('academic_sessions')->nullOnDelete();
            $table->uuid('student_id')->nullable();
            $table->foreign('student_id')->references('id')->on('students')->nullOnDelete();
            $table->integer('usage_count')->default(0);
            $table->integer('max_usage')->default(5);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scratch_cards', function (Blueprint $table) {
            $table->dropForeign(['term_id']);
            $table->dropForeign(['session_id']);
            $table->dropForeign(['student_id']);
            $table->dropColumn(['term_id', 'session_id', 'student_id', 'usage_count', 'max_usage']);
        });
    }
};
