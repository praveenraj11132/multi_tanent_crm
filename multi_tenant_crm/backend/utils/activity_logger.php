<?php
function logActivity($pdo, $companyId, $userId, $action, $entity = null, $entityId = null)
{
    $stmt = $pdo->prepare("
        INSERT INTO activity_logs
        (company_id, user_id, action, entity, entity_id)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $companyId,
        $userId,
        $action,
        $entity,
        $entityId
    ]);
}
