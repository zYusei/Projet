<?php
// register.php
require_once __DIR__.'/db.php';
require_once __DIR__.'/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: inscription.php'); exit;
}

// 1) CSRF
if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
  http_response_code(400); exit('Jeton CSRF invalide.');
}

// 2) Inputs
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
  http_response_code(422); exit('Champs manquants ou invalides.');
}

// 3) Unicité
$st = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email OR username = :username');
$st->execute([':email'=>$email, ':username'=>$username]);
if ($st->fetchColumn() > 0) {
  http_response_code(409); exit('Nom d’utilisateur ou e-mail déjà pris.');
}

// 4) Création utilisateur
$hashPwd = password_hash($password, PASSWORD_DEFAULT);

$pdo->beginTransaction();
$ins = $pdo->prepare('INSERT INTO users (username, email, password_hash, created_at) VALUES (:u, :e, :p, NOW())');
$ins->execute([':u'=>$username, ':e'=>$email, ':p'=>$hashPwd]);
$userId = (int)$pdo->lastInsertId();

// 5) Générer token de vérif + stocker (hashé) + expiration
$token = bin2hex(random_bytes(32));                   // token à envoyer par email
$hash  = hash('sha256', $token);                      // ce qu’on stocke
$exp   = (new DateTime('+24 hours'))->format('Y-m-d H:i:s');

$st = $pdo->prepare("UPDATE users SET verify_token_hash=:h, verify_token_expires=:e WHERE id=:id");
$st->execute([':h'=>$hash, ':e'=>$exp, ':id'=>$userId]);

$pdo->commit();

// 6) Construire l’URL de vérification
$baseUrl = rtrim((isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']), '/');
$verifyUrl = sprintf('%s/verify.php?u=%d&t=%s', $baseUrl, $userId, $token);

// 7) Envoyer l’e-mail (simple). En prod: PHPMailer/SMTP
$userEmail = $email;
$to = $userEmail;
$subject = "Vérifie ton email — FunCodeLab";
$headers = "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: FunCodeLab <no-reply@ton-domaine.tld>\r\n";

$body = '
  <h2>Bienvenue !</h2>
  <p>Confirme ton adresse en cliquant ci-dessous (valide 24h) :</p>
  <p><a href="'.$verifyUrl.'">'.$verifyUrl.'</a></p>
  <p>Si tu n\'es pas à l\'origine de cette création de compte, ignore ce message.</p>
';

@mail($to, $subject, $body, $headers); // remplace par PHPMailer/SMTP en prod

// 8) Rediriger vers une page qui explique la vérification
header('Location: verif-required.php'); exit;
