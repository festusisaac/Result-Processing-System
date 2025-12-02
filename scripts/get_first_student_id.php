<?php
$dbFile = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbFile)) {
    echo "";
    exit(0);
}
try {
    $db = new PDO('sqlite:' . $dbFile);
    $row = $db->query("select id from students limit 1")->fetch(PDO::FETCH_ASSOC);
    echo $row['id'] ?? "";
} catch (Exception $e) {
    echo "";
}
