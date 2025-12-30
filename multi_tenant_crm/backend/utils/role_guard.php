<?php
function allowRoles(array $allowedRoles, array $auth) {
    if (!in_array($auth['role'], $allowedRoles)) {
        http_response_code(403);
        echo json_encode(["error" => "Access denied"]);
        exit;
    }
}
