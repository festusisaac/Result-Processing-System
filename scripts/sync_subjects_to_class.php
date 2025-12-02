<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ClassRoom;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

// Accept class id via argv[1], else use the example JSS 1 id
$target = $argv[1] ?? '2a378a06-714b-4767-ab6c-d61545612651';

$c = ClassRoom::find($target);
if (! $c) {
    echo json_encode(['ok' => false, 'message' => 'Class not found', 'class_id' => $target]) . PHP_EOL;
    exit(1);
}

$subjects = Subject::pluck('id')->toArray();
if (empty($subjects)) {
    echo json_encode(['ok' => false, 'message' => 'No subjects available to attach']) . PHP_EOL;
    exit(1);
}

DB::beginTransaction();
try {
    $c->subjects()->sync($subjects);
    DB::commit();
    $pivots = DB::table('class_subject')->where('class_room_id', $c->id)->get()->map(function($r){ return (array) $r; })->toArray();
    echo json_encode([
        'ok' => true,
        'class' => ['id' => $c->id, 'name' => $c->name],
        'attached_subject_count' => count($subjects),
        'pivots' => $pivots
    ], JSON_PRETTY_PRINT) . PHP_EOL;
} catch (\Exception $e) {
    DB::rollBack();
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]) . PHP_EOL;
    exit(1);
}
