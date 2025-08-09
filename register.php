<?php
// register.php
require_once __DIR__.'/db.php';
require_once __DIR__.'/session.php';

try {
  // DEV: show errors (remove in prod)
  ini_set('display_errors', '1');
  ini_set('display_startup_errors', '1');
  error_reporting(E_ALL);

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

  // 5) Token de vérif
  $token = bin2hex(random_bytes(32));
  $hash  = hash('sha256', $token);
  $exp   = (new DateTime('+24 hours'))->format('Y-m-d H:i:s');

  $st = $pdo->prepare("UPDATE users SET verify_token_hash=:h, verify_token_expires=:e WHERE id=:id");
  $st->execute([':h'=>$hash, ':e'=>$exp, ':id'=>$userId]);

  $pdo->commit();

  // 6) Lien de vérification
  $baseUrl   = rtrim((isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']), '/');
  $verifyUrl = sprintf('%s/verify.php?u=%d&t=%s', $baseUrl, $userId, $token);

  // 7) Envoi email —> CHARGER ICI SEULEMENT
  require_once __DIR__.'/send_email.php'; // << moved here
  $ok = sendVerificationEmail($email, $username, $verifyUrl);

  // 8) Redirection
  $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  header('Location: ' . ($basePath ? $basePath : '') . '/inscription.php?' . ($ok ? 'sent=1' : 'error=' . urlencode("Impossible d'envoyer l'email")));
  exit;

} catch (Throwable $e) {
  if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) { $pdo->rollBack(); }
  error_log('register.php error: '.$e->getMessage());
  http_response_code(500);
  echo 'Une erreur est survenue. Réessaie plus tard.';
}
