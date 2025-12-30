<?php

function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64UrlDecode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

function generateJWT($payload, $secret, $expirySeconds = 3600) {
    $header = base64UrlEncode(json_encode([
        "alg" => "HS256",
        "typ" => "JWT"
    ]));

    $payload['exp'] = time() + $expirySeconds;
    $payloadEncoded = base64UrlEncode(json_encode($payload));

    $signature = hash_hmac(
        'sha256',
        "$header.$payloadEncoded",
        $secret,
        true
    );

    $signatureEncoded = base64UrlEncode($signature);

    return "$header.$payloadEncoded.$signatureEncoded";
}

function verifyJWT($token, $secret) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;

    [$header, $payload, $signature] = $parts;

    $validSignature = base64UrlEncode(
        hash_hmac('sha256', "$header.$payload", $secret, true)
    );

    if (!hash_equals($validSignature, $signature)) return false;

    $data = json_decode(base64UrlDecode($payload), true);

    if ($data['exp'] < time()) return false;

    return $data;
}
