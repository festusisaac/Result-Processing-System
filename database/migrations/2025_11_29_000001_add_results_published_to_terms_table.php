<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResultsPublishedToTermsTable extends Migration
{
    public function up()
    {
        Schema::table('terms', function (Blueprint $table) {
            $table->boolean('results_published')->default(false)->after('term_name');
        });
    }

    public function down()
    {
        Schema::table('terms', function (Blueprint $table) {
            $table->dropColumn('results_published');
        });
    }
}
