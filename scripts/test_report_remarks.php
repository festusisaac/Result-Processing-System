<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ReportSetting;

$sample = "100-70:EXCELLENT\n60-69:VERY GOOD\n50-59:GOOD\n45-49:FAIR\n40-44:POOR\n0-39:VERY POOR";
ReportSetting::set('remarks', $sample);

$rules = ReportSetting::getRemarksRules();
echo "Parsed remarks rules:\n";
print_r($rules);

echo "Computed remarks for sample scores:\n";
foreach ([95, 67, 53, 47, 42, 30] as $s) {
    echo "Score {$s} => " . ReportSetting::computeRemarkFromScore($s) . "\n";
}
