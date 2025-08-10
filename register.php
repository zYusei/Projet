<?php
// register.php
require_once __DIR__.'/db.php';
require_once __DIR__.'/session.php';

// DEV only: logs détaillés (à retirer en prod)
ini_set('display_errors', '0');
error_reporting(E_ALL);

// petite aide pour rediriger proprement
function redirect_with(string $query) {
  $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  header('Location: ' . ($basePath ? $basePath : '') . '/inscription.php?' . $query);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: inscription.php'); exit;
}

// 1) CSRF
if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
  redirect_with('error=' . urlencode('Jeton CSRF invalide.'));
}

try {
  // 2) Inputs
  $username = trim($_POST['username'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    redirect_with('error=' . urlencode('Champs manquants ou invalides.'));
  }

  // 3) Unicité
  $st = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email OR username = :username');
  $st->execute([':email'=>$email, ':username'=>$username]);
  if ($st->fetchColumn() > 0) {
    redirect_with('error=' . urlencode('Nom d’utilisateur ou e-mail déjà pris.'));
  }

  // 4) Création utilisateur + token
  $hashPwd = password_hash($password, PASSWORD_DEFAULT);
  $pdo->beginTransaction();

  $ins = $pdo->prepare('INSERT INTO users (username, email, password_hash, created_at) VALUES (:u, :e, :p, NOW())');
  $ins->execute([':u'=>$username, ':e'=>$email, ':p'=>$hashPwd]);
  $userId = (int)$pdo->lastInsertId();

  $token = bin2hex(random_bytes(32));
  $hash  = hash('sha256', $token);
  $exp   = (new DateTime('+24 hours'))->format('Y-m-d H:i:s');

  $st = $pdo->prepare("UPDATE users SET verify_token_hash=:h, verify_token_expires=:e WHERE id=:id");
  $st->execute([':h'=>$hash, ':e'=>$exp, ':id'=>$userId]);

  $pdo->commit();

} catch (Throwable $e) {
  if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
  error_log('register.php DB error: '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine());
  redirect_with('error=' . urlencode('Erreur base de données.'));
}

// 5) Lien de vérification (hors try DB)
$baseUrl   = rtrim((isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']), '/');
$verifyUrl = sprintf('%s/verify.php?u=%d&t=%s', $baseUrl, $userId, $token);

// 6) Envoi de l’e-mail — jamais bloquant
$ok = false;
try {
  $mailerPath = __DIR__.'/send_email.php';
  if (!is_readable($mailerPath)) {
    throw new RuntimeException('send_email.php introuvable: '.$mailerPath);
  }
  require_once $mailerPath;

  if (!function_exists('sendVerificationEmail')) {
    throw new RuntimeException('sendVerificationEmail() non trouvée (autoload ?).');
  }

  $ok = sendVerificationEmail($email, $username, $verifyUrl);
  if (!$ok) throw new RuntimeException('sendVerificationEmail() a renvoyé false');
} catch (Throwable $e) {
  error_log('register.php mail error: '.$e->getMessage());
  // on continue avec ok=false
}

// 7) Redirection finale (jamais de page blanche)
redirect_with($ok ? 'sent=1' : 'error=' . urlencode("Impossible d'envoyer l'email (vérifie la config SMTP)."));
