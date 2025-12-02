<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('report_settings', function (Blueprint $table) {
            // Add a JSON column to store structured values
            $table->json('value_json')->nullable()->after('value');
        });

        // Migrate existing grading text into structured JSON
        $gradingRow = DB::table('report_settings')->where('key', 'grading')->first();
        if ($gradingRow && !empty(trim($gradingRow->value ?? ''))) {
            $raw = trim($gradingRow->value);
            $lines = preg_split('/\r?\n/', $raw);
            $rules = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;
                if (!str_contains($line, ':')) continue;
                [$range, $grade] = array_map('trim', explode(':', $line, 2));
                if (!str_contains($range, '-')) continue;
                [$min, $max] = array_map('trim', explode('-', $range, 2));
                if (!is_numeric($min) || !is_numeric($max)) continue;
                $rules[] = ['min' => (int)$min, 'max' => (int)$max, 'grade' => $grade];
            }

            // sort by min desc
            usort($rules, function ($a, $b) { return $b['min'] <=> $a['min']; });

            if (!empty($rules)) {
                DB::table('report_settings')->where('key', 'grading')->update(['value_json' => json_encode($rules)]);
            }
        }
    }

    public function down()
    {
        Schema::table('report_settings', function (Blueprint $table) {
            $table->dropColumn('value_json');
        });
    }
};
