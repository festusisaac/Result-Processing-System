<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$emails = ['admin@rms.com','admin@example.com'];
$users = App\Models\User::whereIn('email', $emails)->get();

if ($users->isEmpty()) {
    echo "No matching users found.\n";
    exit(0);
}

foreach ($users as $u) {
    $u->password = 'password';
    $u->save();
    echo "updated: {$u->email}\n";
}

echo "Done.\n";
