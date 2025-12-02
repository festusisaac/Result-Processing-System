<?php
$db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
$stmt = $db->query("SELECT name, sql FROM sqlite_master WHERE type='index' AND tbl_name='terms'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) {
    echo "No indexes found for 'terms' table\n";
} else {
    foreach ($rows as $r) {
        echo ($r['name'] ?? '') . ' | ' . ($r['sql'] ?? '') . PHP_EOL;
    }
}
