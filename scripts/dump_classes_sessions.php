<?php
$db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');

echo "Classes:\n";
foreach ($db->query('SELECT id, name, level FROM classes') as $row) {
    echo $row['id'] . ' | ' . $row['name'] . ' | ' . ($row['level'] ?? '') . PHP_EOL;
}

echo "\nSessions:\n";
foreach ($db->query('SELECT id, name, active FROM academic_sessions') as $row) {
    echo $row['id'] . ' | ' . $row['name'] . ' | active: ' . ($row['active'] ? 'yes' : 'no') . PHP_EOL;
}
