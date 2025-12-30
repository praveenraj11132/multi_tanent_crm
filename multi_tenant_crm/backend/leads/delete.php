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
    SET deleted_at = NOW()
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
    'LEAD_DELETED',
    "Lead ID {$data['id']} soft deleted"
);

echo json_encode(["message" => "Lead deleted successfully"]);
