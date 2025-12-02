<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResultStatusToScratchCardBatches extends Migration
{
    public function up()
    {
        Schema::table('scratch_card_batches', function (Blueprint $table) {
            $table->string('result_status')->default('DRAFT')->after('status');
            $table->string('published_by')->nullable()->after('result_status');
            $table->timestamp('published_at')->nullable()->after('published_by');
        });
    }

    public function down()
    {
        Schema::table('scratch_card_batches', function (Blueprint $table) {
            $table->dropColumn(['result_status','published_by','published_at']);
        });
    }
}
