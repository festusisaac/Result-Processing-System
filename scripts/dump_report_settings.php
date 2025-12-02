<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ReportSetting;

$rows = ReportSetting::all()->map(function($r){
    return [
        'key' => $r->key,
        'value' => $r->value,
        'value_json' => $r->value_json,
    ];
})->toArray();

echo json_encode($rows, JSON_PRETTY_PRINT);
