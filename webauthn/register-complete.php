<?php
// webauthn/register-complete.php
declare(strict_types=1);
require_once __DIR__ . '/../lib/bootstrap.php';
use lbuchs\WebAuthn\WebAuthn;
use lbuchs\WebAuthn\Binary\ByteBuffer;

if (!isset($_SESSION['attestation_state'], $_SESSION['challenge'], $_SESSION['user_id'])) {
    http_response_code(400); echo json_encode(['ok'=>false,'error'=>'session saknas']); exit;
}

$input = json_decode(file_get_contents('php://input'), true);

try {
    $server = new WebAuthn(RP_NAME, RP_ID, ORIGIN);

    $clientDataJSON = new ByteBuffer(base64_decode($input['response']['clientDataJSON']));
    $attestationObject = new ByteBuffer(base64_decode($input['response']['attestationObject']));

    $data = $server->processCreate($clientDataJSON, $attestationObject, $_SESSION['attestation_state'], $_SESSION['challenge']);

    $credentialId = b64url($data->credentialId);
    $publicKeyPem = $data->credentialPublicKeyPem;
    $signCount = $data->signCount ?? 0;

    saveCredential((int)$_SESSION['user_id'], $credentialId, $publicKeyPem, (int)$signCount);

    echo json_encode(['ok'=>true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['ok'=>false, 'error'=>$e->getMessage()]);
}
