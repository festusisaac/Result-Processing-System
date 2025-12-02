<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            if (!Schema::hasColumn('scores', 'ca1_score')) {
                $table->decimal('ca1_score', 5, 2)->nullable()->after('ca_score');
            }
            if (!Schema::hasColumn('scores', 'ca2_score')) {
                $table->decimal('ca2_score', 5, 2)->nullable()->after('ca1_score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn(['ca1_score', 'ca2_score']);
        });
    }
};
