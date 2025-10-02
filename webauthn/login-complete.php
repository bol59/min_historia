<?php
// webauthn/login-complete.php
declare(strict_types=1);
require_once __DIR__ . '/../lib/bootstrap.php';
use lbuchs\WebAuthn\WebAuthn;
use lbuchs\WebAuthn\Binary\ByteBuffer;

if (!isset($_SESSION['assertion_state'], $_SESSION['challenge'], $_SESSION['user_id'])) {
    http_response_code(400); echo json_encode(['ok'=>false,'error'=>'session saknas']); exit;
}

$input = json_decode(file_get_contents('php://input'), true);

try {
    $server = new WebAuthn(RP_NAME, RP_ID, ORIGIN);

    $clientDataJSON   = new ByteBuffer(base64_decode($input['response']['clientDataJSON']));
    $authenticatorData= new ByteBuffer(base64_decode($input['response']['authenticatorData']));
    $signature        = new ByteBuffer(base64_decode($input['response']['signature']));
    $userHandle       = isset($input['response']['userHandle']) && $input['response']['userHandle'] !== null
                        ? new ByteBuffer(base64_decode($input['response']['userHandle'])) : null;
    $credentialId     = base64_decode($input['rawId']);

    $cred = getCredentialById(b64url($credentialId));
    if (!$cred) { throw new Exception('Credential okänd'); }

    $server->processGet(
        $clientDataJSON,
        $authenticatorData,
        $signature,
        $_SESSION['assertion_state'],
        $_SESSION['challenge'],
        $cred['public_key_pem'],
        $credentialId,
        $userHandle
    );

    $_SESSION['logged_in'] = true;
    $_SESSION['logged_in_email'] = $_SESSION['email'];

    echo json_encode(['ok'=>true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['ok'=>false, 'error'=>$e->getMessage()]);
}
