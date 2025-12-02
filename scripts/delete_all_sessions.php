<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\AcademicSession;

echo "Deleting all academic sessions and related session flags...\n";

DB::transaction(function () {
    // Delete via query to avoid model-level side-effects; this removes all sessions.
    $count = DB::table('academic_sessions')->count();
    echo "Found {$count} sessions. Deleting...\n";
    DB::table('academic_sessions')->delete();
    echo "All sessions deleted.\n";
});

// Quick verification
$remaining = AcademicSession::count();
echo "Remaining sessions: {$remaining}\n";

if (AcademicSession::getActive()) {
    echo "Warning: an active session still exists.\n";
} else {
    echo "No active session found.\n";
}
