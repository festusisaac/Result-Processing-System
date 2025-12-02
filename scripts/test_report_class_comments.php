<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ReportSetting;

$sample = "100-70:Excellent Result\n60-69:Very Good Result, Keep it up\n50-59:Good, Keep improving\n45-49:Add more efforts\n40-44:Be attentive and put more efforts\n0-39:Not too good but there is hope for you. Try again next time";
ReportSetting::set('class_teacher_comments', $sample);

$rules = ReportSetting::getClassTeacherCommentRules();
echo "Parsed class teacher comment rules:\n";
print_r($rules);

echo "Computed class comments for sample scores:\n";
foreach ([95, 67, 53, 47, 42, 30] as $s) {
    echo "Score {$s} => " . ReportSetting::computeClassTeacherCommentFromScore($s) . "\n";
}
