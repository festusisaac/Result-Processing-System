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
        if (Schema::hasTable('student_skills_attributes')) {
            Schema::table('student_skills_attributes', function (Blueprint $table) {
                if (!Schema::hasColumn('student_skills_attributes', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_skills_attributes')) {
            Schema::table('student_skills_attributes', function (Blueprint $table) {
                if (Schema::hasColumn('student_skills_attributes', 'deleted_at')) {
                    $table->dropColumn('deleted_at');
                }
            });
        }
    }
};
