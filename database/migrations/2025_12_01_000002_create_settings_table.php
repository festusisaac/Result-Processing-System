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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'school_name', 'value' => 'RMS School', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_address', 'value' => '123 Education Lane, Knowledge City', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_phone', 'value' => '+1 234 567 8900', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_email', 'value' => 'info@rmsschool.com', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_motto', 'value' => 'Empowering the Next Generation', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_description', 'value' => 'We provide a world-class education that nurtures creativity, critical thinking, and character development in every student.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
