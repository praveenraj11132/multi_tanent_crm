<?php
require '../config/database.php';
require '../middleware/auth.php';
require '../utils/activity_logger.php';
require '../utils/role_guard.php';

$auth = $_REQUEST['auth'];
$data = json_decode(file_get_contents("php://input"), true);

/* ROLE CHECK */
allowRoles(['admin', 'staff', 'super_admin'], $auth);

/* RATE LIMIT */
$identifier = $_SERVER['REMOTE_ADDR'] . '_' . $auth['id'];
rateLimit($pdo, $identifier, 'LEAD_UPDATE');

/* STAFF RESTRICTION */
if ($auth['role'] === 'staff') {
    $check = $pdo->prepare("
        SELECT id FROM leads
        WHERE id = ? AND assigned_to = ? AND company_id = ?
    ");
    $check->execute([
        $data['id'],
        $auth['id'],
        $auth['company_id']
    ]);

    if (!$check->fetch()) {
        http_response_code(403);
        echo json_encode(["error" => "You can update only assigned leads"]);
        exit;
    }
}

/* UPDATE QUERY */
$stmt = $pdo->prepare("
    UPDATE leads
    SET status = ?, priority = ?, notes = ?, assigned_to = ?
    WHERE id = ? AND company_id = ?
");

$stmt->execute([
    $data['status'],
    $data['priority'],
    $data['notes'],
    $data['assigned_to'],
    $data['id'],
    $auth['company_id']
]);

/* ACTIVITY LOG */
logActivity(
    $pdo,
    $auth['company_id'],
    $auth['id'],
    'LEAD_UPDATED',
    "Lead ID {$data['id']} updated"
);

echo json_encode(["message" => "Lead updated"]);
