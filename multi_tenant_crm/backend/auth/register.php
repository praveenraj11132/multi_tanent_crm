<?php
require '../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'] ?? null;
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;
$company_id = $data['company_id'] ?? null;
$role = $data['role'] ?? 'staff';

if (!$name || !$email || !$password || !$company_id) {
    http_response_code(400);
    echo json_encode(["error" => "Missing fields"]);
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $pdo->prepare(
    "INSERT INTO users (company_id, name, email, password, role)
     VALUES (?, ?, ?, ?, ?)"
);
$stmt->execute([$company_id, $name, $email, $hash, $role]);

echo json_encode(["message" => "User registered successfully"]);
