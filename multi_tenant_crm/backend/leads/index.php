<?php
require '../config/database.php';
require '../middleware/auth.php';
require '../utils/role_guard.php';

$auth = $_REQUEST['auth'];

/* ROLE CHECK */
allowRoles(['admin', 'staff', 'super_admin'], $auth);

$companyId = $auth['company_id'];

/* Pagination */
$page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? min(50, (int)$_GET['limit']) : 10;
$offset = ($page - 1) * $limit;

/* Base condition */
$where = "company_id = ? AND deleted_at IS NULL";
$params = [$companyId];

/* STAFF: only assigned leads */
if ($auth['role'] === 'staff') {
    $where .= " AND assigned_to = ?";
    $params[] = $auth['id'];
}

/* SEARCH */
if (!empty($_GET['q'])) {
    $where .= " AND (
        full_name LIKE ?
        OR email LIKE ?
        OR phone LIKE ?
    )";
    $search = '%' . $_GET['q'] . '%';
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
}

/*  STATUS FILTER */
if (!empty($_GET['status'])) {
    $where .= " AND status = ?";
    $params[] = $_GET['status'];
}

/*  PRIORITY FILTER */
if (!empty($_GET['priority'])) {
    $where .= " AND priority = ?";
    $params[] = $_GET['priority'];
}

/* ASSIGNED STAFF FILTER (Admin only) */
if (!empty($_GET['assigned_to']) && $auth['role'] !== 'staff') {
    $where .= " AND assigned_to = ?";
    $params[] = $_GET['assigned_to'];
}

/* Total count */
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM leads WHERE $where");
$countStmt->execute($params);
$totalRecords = (int)$countStmt->fetchColumn();

/* Data query */
$sql = "
    SELECT id, full_name, email, phone, status, priority, assigned_to, created_at
    FROM leads
    WHERE $where
    ORDER BY created_at DESC
    LIMIT $limit OFFSET $offset
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

/* Response */
echo json_encode([
    "page" => $page,
    "limit" => $limit,
    "total_records" => $totalRecords,
    "total_pages" => ceil($totalRecords / $limit),
    "filters" => [
        "search" => $_GET['q'] ?? null,
        "status" => $_GET['status'] ?? null,
        "priority" => $_GET['priority'] ?? null,
        "assigned_to" => $_GET['assigned_to'] ?? null
    ],
    "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
