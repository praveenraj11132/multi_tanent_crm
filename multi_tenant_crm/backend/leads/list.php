<?php
require '../config/database.php';
require '../middleware/auth.php';

$auth = $_REQUEST['auth'];

if ($auth['role'] === 'staff') {
    $stmt = $pdo->prepare("
        SELECT * FROM leads
        WHERE company_id = ?
        AND assigned_to = ?
        AND is_deleted = 0
    ");
    $stmt->execute([$auth['company_id'], $auth['user_id']]);
} else {
    $stmt = $pdo->prepare("
        SELECT * FROM leads
        WHERE company_id = ?
        AND is_deleted = 0
    ");
    $stmt->execute([$auth['company_id']]);
}

$leads = $stmt->fetchAll();

echo json_encode($leads);
