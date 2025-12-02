<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Migrate existing remarks text into structured JSON in value_json
        $remarksRow = DB::table('report_settings')->where('key', 'remarks')->first();
        if ($remarksRow && !empty(trim($remarksRow->value ?? ''))) {
            $raw = trim($remarksRow->value);
            $lines = preg_split('/\r?\n/', $raw);
            $rules = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;
                // accept either 'min-max:Remark' or 'min-max Remark' or 'min-max\tRemark'
                if (str_contains($line, ':')) {
                    [$range, $remark] = array_map('trim', explode(':', $line, 2));
                } else {
                    // split on whitespace after range
                    $parts = preg_split('/\s+/', $line, 2);
                    if (count($parts) < 2) continue;
                    [$range, $remark] = [$parts[0], trim($parts[1])];
                }
                if (!str_contains($range, '-')) continue;
                [$min, $max] = array_map('trim', explode('-', $range, 2));
                if (!is_numeric($min) || !is_numeric($max)) continue;
                $rules[] = ['min' => (int)$min, 'max' => (int)$max, 'remark' => $remark];
            }

            usort($rules, function ($a, $b) { return $b['min'] <=> $a['min']; });

            if (!empty($rules)) {
                DB::table('report_settings')->where('key', 'remarks')->update(['value_json' => json_encode($rules)]);
            }
        }
    }

    public function down()
    {
        // No-op: we keep the value_json column; reverting would require reconstructing text.
    }
};
