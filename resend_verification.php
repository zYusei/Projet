<?php
require_once 'db.php';
require_once 'session.php';
$u = current_user();
if (!$u) { header('Location: connexion.php'); exit; }
if (!empty($u['email_verified_at'])) { exit('Déjà vérifié.'); }

// rate limit simple : pas plus d’un lien toutes les 10 minutes
$st = $pdo->prepare("SELECT verify_token_expires FROM users WHERE id=:id");
$st->execute([':id'=>$u['id']]);
$row = $st->fetch(PDO::FETCH_ASSOC);

$token = bin2hex(random_bytes(32));
$hash  = hash('sha256', $token);
$exp   = (new DateTime('+24 hours'))->format('Y-m-d H:i:s');

$up = $pdo->prepare("UPDATE users SET verify_token_hash=:h, verify_token_expires=:e WHERE id=:id");
$up->execute([':h'=>$hash, ':e'=>$exp, ':id'=>$u['id']]);

$verifyUrl = sprintf('%s/verify.php?u=%d&t=%s',
  rtrim((isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']),'/'),
  $u['id'], $token
);

$to = $u['email'];
$subject = "Nouveau lien de vérification — FunCodeLab";
$headers = "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: FunCodeLab <no-reply@ton-domaine.tld>\r\n";
$body = '<p>Voici ton nouveau lien (valide 24h) :</p><p><a href="'.$verifyUrl.'">'.$verifyUrl.'</a></p>';

@mail($to, $subject, $body, $headers);

echo 'Lien renvoyé ! Vérifie ta boîte mail (et tes spams).';
