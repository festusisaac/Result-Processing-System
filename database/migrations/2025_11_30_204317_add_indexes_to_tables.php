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
        // Indexes for scores table
        Schema::table('scores', function (Blueprint $table) {
            $table->index('student_id');
            $table->index('term_id');
            $table->index('session_id');
            $table->index('subject_id');
            // Composite indexes for common queries
            $table->index(['student_id', 'term_id', 'session_id'], 'idx_scores_student_term_session');
            $table->index(['term_id', 'session_id'], 'idx_scores_term_session');
        });

        // Indexes for students table
        Schema::table('students', function (Blueprint $table) {
            $table->index('class_id');
            $table->index('session_id');
            $table->index('adm_no');
            $table->index(['class_id', 'session_id'], 'idx_students_class_session');
        });

        // Indexes for scratch_cards table
        Schema::table('scratch_cards', function (Blueprint $table) {
            $table->index('serial');
            $table->index('pin');
            $table->index('term_id');
            $table->index('session_id');
            $table->index('batch_id');
            $table->index(['serial', 'pin'], 'idx_scratch_cards_serial_pin');
        });

        // Indexes for attendance table
        Schema::table('attendance', function (Blueprint $table) {
            $table->index('student_id');
            $table->index('term_id');
            $table->index(['student_id', 'term_id'], 'idx_attendance_student_term');
        });

        // Indexes for student_skills_attributes table
        Schema::table('student_skills_attributes', function (Blueprint $table) {
            $table->index('student_id');
            $table->index('term_id');
            $table->index('skill_attribute_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['term_id']);
            $table->dropIndex(['session_id']);
            $table->dropIndex(['subject_id']);
            $table->dropIndex('idx_scores_student_term_session');
            $table->dropIndex('idx_scores_term_session');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['class_id']);
            $table->dropIndex(['session_id']);
            $table->dropIndex(['adm_no']);
            $table->dropIndex('idx_students_class_session');
        });

        Schema::table('scratch_cards', function (Blueprint $table) {
            $table->dropIndex(['serial']);
            $table->dropIndex(['pin']);
            $table->dropIndex(['term_id']);
            $table->dropIndex(['session_id']);
            $table->dropIndex(['batch_id']);
            $table->dropIndex('idx_scratch_cards_serial_pin');
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['term_id']);
            $table->dropIndex('idx_attendance_student_term');
        });

        Schema::table('student_skills_attributes', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['term_id']);
            $table->dropIndex(['skill_attribute_id']);
        });
    }
};
