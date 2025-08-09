<?php
require_once 'db.php';
require_once 'session.php';

$userId = isset($_GET['u']) ? (int)$_GET['u'] : 0;
$token  = $_GET['t'] ?? '';

if (!$userId || !$token) { http_response_code(400); exit('Lien invalide.'); }

$st = $pdo->prepare("SELECT id, email_verified_at, verify_token_hash, verify_token_expires FROM users WHERE id=:id");
$st->execute([':id'=>$userId]);
$user = $st->fetch(PDO::FETCH_ASSOC);
if (!$user) { http_response_code(404); exit('Utilisateur introuvable.'); }

if (!empty($user['email_verified_at'])) { exit('Adresse déjà vérifiée.'); }

$hash = hash('sha256', $token);
$now  = (new DateTime())->format('Y-m-d H:i:s');

if (!$user['verify_token_hash'] || !hash_equals($user['verify_token_hash'], $hash)) {
  http_response_code(400); exit('Token invalide.');
}
if (!$user['verify_token_expires'] || $user['verify_token_expires'] < $now) {
  http_response_code(400); exit('Token expiré, redemande un email de vérification.');
}

// OK : on valide
$pdo->beginTransaction();
$upd = $pdo->prepare("UPDATE users
  SET email_verified_at = NOW(), verify_token_hash = NULL, verify_token_expires = NULL
  WHERE id = :id");
$upd->execute([':id'=>$userId]);
$pdo->commit();

echo 'Adresse email vérifiée ! Tu peux maintenant utiliser toutes les fonctionnalités. <a href="connexion.php">Se connecter</a>';
