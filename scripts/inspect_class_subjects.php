<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

$out = [];
try {
    // Table info for pivot
    $out['pivot_table_info'] = DB::select("PRAGMA table_info('class_subject')");
    // All rows in pivot
    $out['pivot_rows'] = DB::table('class_subject')->get()->map(fn($r)=> (array) $r)->toArray();
} catch (Exception $e) {
    $out['pivot_error'] = $e->getMessage();
}

try {
    $out['classes'] = DB::table('classes')->select('id','name')->limit(20)->get()->map(fn($r)=>(array)$r)->toArray();
    $out['subjects'] = DB::table('subjects')->select('id','name')->limit(50)->get()->map(fn($r)=>(array)$r)->toArray();
} catch (Exception $e) {
    $out['data_error'] = $e->getMessage();
}

echo json_encode($out, JSON_PRETTY_PRINT);
