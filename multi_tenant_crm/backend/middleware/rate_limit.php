<?php

function rateLimit($pdo, $identifier, $endpoint, $limit = 50, $minutes = 1)
{
    $stmt = $pdo->prepare("
        SELECT * FROM rate_limits
        WHERE identifier = ? AND endpoint = ?
    ");
    $stmt->execute([$identifier, $endpoint]);
    $row = $stmt->fetch();

    $now = new DateTime();

    if ($row) {
        $last = new DateTime($row['last_attempt']);
        $diff = $now->getTimestamp() - $last->getTimestamp();

        if ($diff < ($minutes * 60)) {
            if ($row['attempts'] >= $limit) {
                http_response_code(429);
                echo json_encode(["error" => "Too many requests"]);
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE rate_limits
                SET attempts = attempts + 1, last_attempt = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$row['id']]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE rate_limits
                SET attempts = 1, last_attempt = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$row['id']]);
        }
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO rate_limits (identifier, endpoint, last_attempt)
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$identifier, $endpoint]);
    }
}
