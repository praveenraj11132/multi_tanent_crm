<?php
require __DIR__ . '/backend/config/database.php';

$companies = [
    ['name' => 'Acme Corp'],
    ['name' => 'Zefinix Ltd'],
];

foreach ($companies as $c) {
    $stmt = $pdo->prepare("INSERT INTO companies (name) VALUES (?)");
    $stmt->execute([$c['name']]);
}

echo "Companies seeded successfully\n";
