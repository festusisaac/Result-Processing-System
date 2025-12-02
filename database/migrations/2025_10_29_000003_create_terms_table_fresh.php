<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old table if exists and create fresh table for terms
        Schema::dropIfExists('terms');

        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->enum('term_name', ['FIRST','SECOND','THIRD'])->unique();
            $table->date('term_begins');
            $table->date('term_ends');
            $table->integer('school_opens');
            $table->string('terminal_duration')->nullable();
            $table->date('next_term_begins');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};
