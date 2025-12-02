<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ReplaceResultsPublishedWithEnumOnTerms extends Migration
{
    public function up()
    {
        Schema::table('terms', function (Blueprint $table) {
            // Add the new result_status column if it doesn't exist
            if (! Schema::hasColumn('terms', 'result_status')) {
                // Determine the existing value to map
                $table->string('result_status')->default('DRAFT')->after('term_name');
            }
        });

        // Migrate existing results_published booleans to the new enum
        if (Schema::hasColumn('terms', 'results_published')) {
            DB::table('terms')->where('results_published', true)->update(['result_status' => 'PUBLISHED']);
            DB::table('terms')->where('results_published', false)->update(['result_status' => 'DRAFT']);
        }

        // Drop the old boolean column if it exists
        Schema::table('terms', function (Blueprint $table) {
            if (Schema::hasColumn('terms', 'results_published')) {
                $table->dropColumn('results_published');
            }
        });
    }

    public function down()
    {
        Schema::table('terms', function (Blueprint $table) {
            if (Schema::hasColumn('terms', 'result_status')) {
                $table->dropColumn('result_status');
            }
        });

        Schema::table('terms', function (Blueprint $table) {
            $table->boolean('results_published')->default(false)->after('term_name');
        });
    }
}
