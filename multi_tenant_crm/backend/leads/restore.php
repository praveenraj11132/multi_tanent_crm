<?php
require '../config/database.php';
require '../middleware/auth.php';
require '../utils/activity_logger.php';
require '../utils/role_guard.php';

$auth = $_REQUEST['auth'];
allowRoles(['admin', 'super_admin'], $auth);

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $pdo->prepare("
    UPDATE leads
    SET deleted_at = NULL
    WHERE id = ? AND company_id = ?
");

$stmt->execute([
    $data['id'],
    $auth['company_id']
]);

logActivity(
    $pdo,
    $auth['company_id'],
    $auth['id'],
    'LEAD_RESTORED',
    "Lead ID {$data['id']} restored"
);

echo json_encode(["message" => "Lead restored successfully"]);
