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
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'level')) {
                $table->dropColumn('level');
            }
            
            if (!Schema::hasColumn('classes', 'promoting_class_name')) {
                $table->string('promoting_class_name')->nullable();
            }
            
            if (!Schema::hasColumn('classes', 'repeating_class_name')) {
                $table->string('repeating_class_name')->nullable();
            }
            
            if (!Schema::hasColumn('classes', 'teacher_id')) {
                $table->uuid('teacher_id')->nullable();
                $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->string('level')->nullable();
            if (Schema::hasColumn('classes', 'teacher_id')) {
                $table->dropForeign(['teacher_id']);
            }
            $table->dropColumn(['promoting_class_name', 'repeating_class_name', 'teacher_id']);
        });
    }
};
