<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ClassRoom;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

$classes = ClassRoom::orderBy('name')->get();
$subjects = Subject::orderBy('name')->get();

$result = [];
foreach ($classes as $c) {
    $attached = DB::table('class_subject')->where('class_room_id', $c->id)->pluck('subject_id')->toArray();
    $attachedNames = [];
    foreach ($attached as $sid) {
        $s = $subjects->firstWhere('id', $sid);
        $attachedNames[] = $s ? $s->name : $sid;
    }
    $notAttached = $subjects->filter(fn($s) => !in_array($s->id, $attached))->map(fn($s)=>$s->name)->values()->toArray();
    $result[] = [
        'class_id' => $c->id,
        'class_name' => $c->name,
        'attached_subjects' => $attachedNames,
        'not_attached_subjects' => $notAttached
    ];
}

echo json_encode($result, JSON_PRETTY_PRINT);
