<?php
// lib/bootstrap.php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/config.php';

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Composer autoload saknas. Kör: composer require lbuchs/webauthn']);
    exit;
}
require_once $autoload;

require_once __DIR__ . '/db.php';

function b64url(string $bin): string {
    return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
}
function b64url_dec(string $b64): string {
    $b64 = strtr($b64, '-_', '+/');
    return base64_decode($b64 . str_repeat('=', (4 - strlen($b64) % 4) % 4));
}
