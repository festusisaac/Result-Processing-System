<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublishMetaToTerms extends Migration
{
    public function up()
    {
        Schema::table('terms', function (Blueprint $table) {
            if (! Schema::hasColumn('terms', 'published_by')) {
                $table->string('published_by')->nullable()->after('results_published');
            }
            if (! Schema::hasColumn('terms', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('published_by');
            }
        });
    }

    public function down()
    {
        Schema::table('terms', function (Blueprint $table) {
            if (Schema::hasColumn('terms', 'published_at')) $table->dropColumn('published_at');
            if (Schema::hasColumn('terms', 'published_by')) $table->dropColumn('published_by');
        });
    }
}
