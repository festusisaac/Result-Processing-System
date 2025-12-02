<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ClassRoom;
use Illuminate\Support\Facades\DB;

$c = ClassRoom::with('subjects')->orderBy('name')->first();
$result = null;
if ($c) {
    $result = ['id' => $c->id, 'name' => $c->name, 'subjects' => $c->subjects->pluck('name')->toArray()];
} else {
    $result = ['id' => null, 'name' => null, 'subjects' => []];
}

$pivots = [];
if ($c) {
     $pivots = DB::table('class_subject')->where('class_room_id', $c->id)->get()->map(function($r){ return (array) $r; })->toArray();
}

echo json_encode(['class' => $result, 'pivots' => $pivots], JSON_PRETTY_PRINT);
