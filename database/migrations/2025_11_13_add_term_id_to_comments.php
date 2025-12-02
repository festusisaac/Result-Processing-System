<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table) {
                if (!Schema::hasColumn('comments', 'term_id')) {
                    $table->uuid('term_id')->nullable()->after('student_id');
                    $table->foreign('term_id')->references('id')->on('terms')->onDelete('set null');
                }
                if (!Schema::hasColumn('comments', 'author_id_foreign')) {
                    $table->foreign('author_id')->references('id')->on('users')->onDelete('set null');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table) {
                if (Schema::hasColumn('comments', 'term_id')) {
                    $table->dropForeign(['term_id']);
                    $table->dropColumn('term_id');
                }
            });
        }
    }
};
