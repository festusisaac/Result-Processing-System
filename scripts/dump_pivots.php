<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::table('class_subject')->get()->map(function($r){ return (array) $r; })->toArray();
echo json_encode($rows, JSON_PRETTY_PRINT);
