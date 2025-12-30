<?php
require '../config/jwt.php';

$env = parse_ini_file(__DIR__ . '/../.env');

$headers = array_change_key_case(getallheaders(), CASE_LOWER);
var_dump($headers);

$authHeader = $headers['authorization']
    ?? $_SERVER['HTTP_AUTHORIZATION']
    ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
    ?? '';



if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$token = str_replace('Bearer ', '', $authHeader);
$payload = verifyJWT($token, $env['JWT_SECRET']);

if (!$payload) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid or expired token"]);
    exit;
}

$_REQUEST['auth'] = $payload;
