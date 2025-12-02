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
        Schema::table('subjects', function (Blueprint $table) {
            $table->uuid('class_id')->nullable()->after('name');
            $table->foreign('class_id')
                ->references('id')
                ->on('classes')
                ->onDelete('cascade');

            $table->uuid('teacher_id')->nullable()->after('class_id');
            $table->foreign('teacher_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->uuid('session_id')->nullable()->after('teacher_id');
            $table->foreign('session_id')
                ->references('id')
                ->on('academic_sessions')
                ->onDelete('cascade');
        });

        Schema::table('subject_groups', function (Blueprint $table) {
            $table->uuid('class_id')->nullable()->after('name');
            $table->foreign('class_id')
                ->references('id')
                ->on('classes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_groups', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropColumn('session_id');
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
        });
    }
};
