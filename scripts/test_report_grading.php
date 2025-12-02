<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ReportSetting;

ReportSetting::set('grading', "70-100:A\n60-69:B\n50-59:C\n45-49:D\n40-44:E\n0-39:F");

$rules = ReportSetting::getGradingRules();
echo "Parsed grading rules:\n";
print_r($rules);

echo "Computed grades for sample scores:\n";
foreach ([95, 67, 53, 47, 42, 30] as $s) {
    echo "Score {$s} => " . ReportSetting::computeGradeFromScore($s) . "\n";
}
