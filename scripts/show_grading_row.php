<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ReportSetting;

$row = ReportSetting::where('key', 'grading')->first();
if (!$row) {
    echo "No grading row found.\n";
    exit(0);
}

echo "value (text):\n";
echo ($row->value ?? '') . "\n\n";

echo "value_json (parsed):\n";
print_r($row->value_json);
