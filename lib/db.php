<?php
// lib/db.php
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

function getUserByEmail(string $email): ?array {
    $st = db()->prepare('SELECT * FROM users WHERE email = ?');
    $st->execute([$email]);
    $row = $st->fetch();
    return $row ?: null;
}

function ensureUser(string $email): array {
    $u = getUserByEmail($email);
    if ($u) return $u;
    $st = db()->prepare('INSERT INTO users (email, display_name) VALUES (?, ?)');
    $st->execute([$email, $email]);
    $id = (int)db()->lastInsertId();
    return ['id'=>$id,'email'=>$email,'display_name'=>$email];
}

function saveCredential(int $userId, string $credentialId, string $publicKeyPem, int $signCount): void {
    $st = db()->prepare('INSERT INTO credentials (user_id, credential_id, public_key_pem, sign_count) VALUES (?, ?, ?, ?)');
    $st->execute([$userId, $credentialId, $publicKeyPem, $signCount]);
}

function getCredentialsForUser(int $userId): array {
    $st = db()->prepare('SELECT * FROM credentials WHERE user_id = ?');
    $st->execute([$userId]);
    return $st->fetchAll();
}

function getCredentialById(string $credentialId): ?array {
    $st = db()->prepare('SELECT * FROM credentials WHERE credential_id = ?');
    $st->execute([$credentialId]);
    $row = $st->fetch();
    return $row ?: null;
}

function updateSignCount(int $credId, int $signCount): void {
    $st = db()->prepare('UPDATE credentials SET sign_count = ? WHERE id = ?');
    $st->execute([$signCount, $credId]);
}
