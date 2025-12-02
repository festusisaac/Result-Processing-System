<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add arm to classes table
        Schema::table('classes', function (Blueprint $table) {
            $table->string('arm')->nullable()->after('level');
        });

        // Create class_subject pivot table
        Schema::create('class_subject', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignUuid('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['class_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('arm');
        });

        Schema::dropIfExists('class_subject');
    }
};