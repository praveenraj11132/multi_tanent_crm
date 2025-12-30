<?php
require '../config/database.php';
require '../middleware/auth.php';
var_dump('hai');

require '../middleware/rate_limit.php';
require '../utils/activity_logger.php';
require '../utils/role_guard.php';

$auth = $_REQUEST['auth'];
file_put_contents(
    __DIR__ . '/debug.log',
    json_encode($auth) . PHP_EOL,
    FILE_APPEND
);

/* ROLE CHECK */
allowRoles(['admin', 'super_admin'], $auth);

/* RATE LIMIT */
$identifier = $_SERVER['REMOTE_ADDR'] . '_' . $auth['id'];
rateLimit($pdo, $identifier, 'LEAD_CREATE');

/* REQUEST DATA */
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['full_name'])) {
    http_response_code(400);
    echo json_encode(["error" => "Full name is required"]);
    exit;
}

/* INSERT LEAD */
$stmt = $pdo->prepare("
    INSERT INTO leads
    (company_id, assigned_to, full_name, phone, email, source, status, priority, notes)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $auth['company_id'],
    $data['assigned_to'] ?? null,
    $data['full_name'],
    $data['phone'] ?? null,
    $data['email'] ?? null,
    $data['source'] ?? null,
    $data['status'] ?? 'New',
    $data['priority'] ?? 'Medium',
    $data['notes'] ?? null
]);
ini_set('display_errors', 1);
error_reporting(E_ALL);
$leadId = $pdo->lastInsertId();

/* ACTIVITY LOG */
logActivity(
    $pdo,
    $auth['company_id'],
    $auth['id'],
    'LEAD_CREATED',
    "Lead ID {$leadId} created"
);

echo json_encode(["message" => "Lead created successfully"]);
