<?php
$db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
$rows = $db->query('SELECT id, email, password FROM users');
foreach ($rows as $row) {
    echo $row['id'] . ' | ' . $row['email'] . ' | ' . $row['password'] . PHP_EOL;
}

// Verify default password for the first user
$rows = $db->query('SELECT password FROM users LIMIT 1');
$first = $rows->fetch();
if ($first) {
    $hash = $first['password'];
    $plain = 'password';
    $ok = password_verify($plain, $hash) ? 'YES' : 'NO';
    echo "Password verification for '{$plain}': {$ok}" . PHP_EOL;
}
