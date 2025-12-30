<?php
require __DIR__ . '/backend/config/database.php';

$users = [
    ['company_id'=>1,'name'=>'Super Admin','email'=>'superadmin@test.com','password'=>'password123','role'=>'super_admin'],
    ['company_id'=>1,'name'=>'Admin User','email'=>'admin@test.com','password'=>'password123','role'=>'admin'],
    ['company_id'=>1,'name'=>'Staff User','email'=>'staff@test.com','password'=>'password123','role'=>'staff'],
];

foreach ($users as $u) {
    $stmt = $pdo->prepare("INSERT INTO users (company_id, name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $u['company_id'],
        $u['name'],
        $u['email'],
        password_hash($u['password'], PASSWORD_DEFAULT),
        $u['role']
    ]);
}

echo "Users seeded successfully\n";
