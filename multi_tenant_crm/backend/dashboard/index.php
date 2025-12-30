<?php
require '../config/database.php';
require '../middleware/auth.php';
require '../utils/role_guard.php';

$auth = $_REQUEST['auth'];

/* ROLE CHECK */
allowRoles(['admin', 'super_admin'], $auth);

$companyId = $auth['company_id'];

/* TOTAL LEADS COUNTS */

$daily = $pdo->prepare("
    SELECT COUNT(*) FROM leads 
    WHERE company_id = ? AND DATE(created_at) = CURDATE()
");
$daily->execute([$companyId]);

$weekly = $pdo->prepare("
    SELECT COUNT(*) FROM leads 
    WHERE company_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
");
$weekly->execute([$companyId]);

$monthly = $pdo->prepare("
    SELECT COUNT(*) FROM leads 
    WHERE company_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
");
$monthly->execute([$companyId]);

/*  LEADS PER STAFF */

$perStaff = $pdo->prepare("
    SELECT assigned_to, COUNT(*) as total 
    FROM leads 
    WHERE company_id = ?
    GROUP BY assigned_to
");
$perStaff->execute([$companyId]);

/*  CONVERSION RATE */

$totalLeads = $pdo->prepare("
    SELECT COUNT(*) FROM leads WHERE company_id = ?
");
$totalLeads->execute([$companyId]);
$total = $totalLeads->fetchColumn();

$convertedLeads = $pdo->prepare("
    SELECT COUNT(*) FROM leads 
    WHERE company_id = ? AND status = 'Converted'
");
$convertedLeads->execute([$companyId]);
$converted = $convertedLeads->fetchColumn();

$conversionRate = $total > 0 ? round(($converted / $total) * 100, 2) : 0;

/*  RECENT ACTIVITY LOGS */

$logs = $pdo->prepare("
    SELECT action, description, created_at 
    FROM activity_logs 
    WHERE company_id = ?
    ORDER BY created_at DESC
    LIMIT 10
");
$logs->execute([$companyId]);

/*  RESPONSE */

echo json_encode([
    "total_leads" => [
        "daily" => (int)$daily->fetchColumn(),
        "weekly" => (int)$weekly->fetchColumn(),
        "monthly" => (int)$monthly->fetchColumn()
    ],
    "leads_per_staff" => $perStaff->fetchAll(PDO::FETCH_ASSOC),
    "conversion_rate" => $conversionRate,
    "recent_activities" => $logs->fetchAll(PDO::FETCH_ASSOC)
]);
