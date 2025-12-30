<?php
require '../config/database.php';
require '../config/jwt.php';

$env = parse_ini_file(__DIR__ . '/../.env');

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid credentials"]);
    exit;
}

$token = generateJWT([
    "user_id" => $user['id'],
    "company_id" => $user['company_id'],
    "role" => $user['role']
], $env['JWT_SECRET']);

echo json_encode([
    "token" => $token,
    "user" => [
        "id" => $user['id'],
        "name" => $user['name'],
        "role" => $user['role']
    ]
]);
