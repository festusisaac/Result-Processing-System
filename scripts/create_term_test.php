<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Term;
use App\Models\AcademicSession;

$active = AcademicSession::getActive();
if (!$active) {
    echo "No active session\n";
    exit(1);
}

$data = [
    'term_name' => 'FIRST',
    'term_begins' => '2025-09-01',
    'term_ends' => '2025-11-30',
    'school_opens' => 60,
    'terminal_duration' => '2 weeks',
    'next_term_begins' => '2026-01-10'
];

try {
    $term = Term::withTrashed()->updateOrCreate([
        'term_name' => $data['term_name'],
        'session_id' => $active->id,
    ], [
        'term_begins' => $data['term_begins'],
        'term_ends' => $data['term_ends'],
        'school_opens' => $data['school_opens'],
        'terminal_duration' => $data['terminal_duration'],
        'next_term_begins' => $data['next_term_begins'],
    ]);

    if (method_exists($term, 'trashed') && $term->trashed()) {
        $term->restore();
    }
    echo "Term saved: id={$term->id}\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
