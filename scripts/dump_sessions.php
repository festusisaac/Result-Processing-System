<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sessions = App\Models\AcademicSession::all();
if ($sessions->isEmpty()) {
    echo "No sessions found\n";
} else {
    foreach ($sessions as $s) {
        echo implode('|', [$s->id, $s->name, $s->active ? 'active' : 'inactive']) . PHP_EOL;
    }
}

$active = App\Models\AcademicSession::getActive();
if ($active) {
    echo "Active session: {$active->id} - {$active->name}\n";
} else {
    echo "No active session.\n";
}
