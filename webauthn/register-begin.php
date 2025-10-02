<?php
// webauthn/register-begin.php
declare(strict_types=1);
require_once __DIR__ . '/../lib/bootstrap.php';
use lbuchs\WebAuthn\WebAuthn;

$input = json_decode(file_get_contents('php://input'), true);
$email = isset($input['email']) ? trim($input['email']) : '';
if (!$email) { http_response_code(400); echo json_encode(['error'=>'email krävs']); exit; }

$user = ensureUser($email);

$server = new WebAuthn(RP_NAME, RP_ID, ORIGIN);

$challenge = random_bytes(32);
$_SESSION['challenge'] = $challenge;
$_SESSION['user_id'] = $user['id'];
$_SESSION['email'] = $user['email'];

$exclude = array_map(function($c){
    return base64_decode($c['credential_id']);
}, getCredentialsForUser((int)$user['id']));

list($createArgs, $state) = $server->prepareAttestation($challenge, $exclude, true, true);
$_SESSION['attestation_state'] = $state;

header('Content-Type: application/json');
echo json_encode($createArgs);
