<?php
// verify.php — confirmation d'e-mail avec rendu modernisé
require_once __DIR__.'/db.php';
require_once __DIR__.'/session.php';

$status = 'success';
$title  = 'Adresse vérifiée 🎉';
$message= 'Ton adresse email a bien été confirmée. Tu peux maintenant te connecter.';
$ctaPrimaryHref = 'connexion.php';
$ctaPrimaryText = 'Se connecter';
$ctaSecondaryHref = 'inscription.php';
$ctaSecondaryText = 'Retour à l’inscription';

try {
  $userId = isset($_GET['u']) ? (int)$_GET['u'] : 0;
  $token  = $_GET['t'] ?? '';

  if (!$userId || !$token) {
    http_response_code(400);
    $status='invalid';
    $title='Lien invalide';
    $message='Le lien de vérification est incomplet ou corrompu. Vérifie l’URL ou demande un nouvel e-mail.';
    $ctaPrimaryHref = 'resend_verification.php';
    $ctaPrimaryText = 'Renvoyer l’e-mail';
    $ctaSecondaryHref = 'connexion.php';
    $ctaSecondaryText = 'Se connecter';
  } else {
    $st = $pdo->prepare("SELECT id, email_verified_at, verify_token_hash, verify_token_expires FROM users WHERE id=:id");
    $st->execute([':id'=>$userId]);
    $user = $st->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      http_response_code(404);
      $status='invalid';
      $title='Utilisateur introuvable';
      $message='Ce compte n’existe pas ou a été supprimé.';
      $ctaPrimaryHref = 'inscription.php';
      $ctaPrimaryText = 'Créer un compte';
      $ctaSecondaryHref = 'connexion.php';
      $ctaSecondaryText = 'Se connecter';
    } elseif (!empty($user['email_verified_at'])) {
      $status='success';
      $title='Adresse déjà vérifiée';
      $message='Ton adresse e-mail était déjà confirmée. Tu peux directement te connecter.';
      $ctaPrimaryHref = 'connexion.php';
      $ctaPrimaryText = 'Se connecter';
      $ctaSecondaryHref = 'index.php';
      $ctaSecondaryText = 'Accueil';
    } else {
      $hash = hash('sha256', $token);
      $now  = (new DateTime())->format('Y-m-d H:i:s');

      if (!$user['verify_token_hash'] || !hash_equals($user['verify_token_hash'], $hash)) {
        http_response_code(400);
        $status='invalid';
        $title='Token invalide';
        $message='Le lien de vérification n’est pas valide. Demande un nouvel e-mail de vérification.';
        $ctaPrimaryHref = 'resend_verification.php';
        $ctaPrimaryText = 'Renvoyer l’e-mail';
        $ctaSecondaryHref = 'connexion.php';
        $ctaSecondaryText = 'Se connecter';
      } elseif (!$user['verify_token_expires'] || $user['verify_token_expires'] < $now) {
        http_response_code(400);
        $status='expired';
        $title='Lien expiré ⏳';
        $message='Ton lien a expiré (validité 24 h). Tu peux t’en faire renvoyer un nouveau.';
        $ctaPrimaryHref = 'resend_verification.php';
        $ctaPrimaryText = 'Renvoyer l’e-mail';
        $ctaSecondaryHref = 'connexion.php';
        $ctaSecondaryText = 'Se connecter';
      } else {
        $pdo->beginTransaction();
        $upd = $pdo->prepare("UPDATE users
          SET email_verified_at = NOW(), verify_token_hash = NULL, verify_token_expires = NULL
          WHERE id = :id");
        $upd->execute([':id'=>$userId]);
        $pdo->commit();

        $status='success';
        $title='Adresse vérifiée 🎉';
        $message='Merci ! Ton adresse email a bien été confirmée. Tu peux maintenant te connecter et profiter de FunCodeLab.';
        $ctaPrimaryHref = 'connexion.php';
        $ctaPrimaryText = 'Se connecter';
        $ctaSecondaryHref = 'index.php';
        $ctaSecondaryText = 'Aller à l’accueil';
      }
    }
  }
} catch (Throwable $e) {
  if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
  error_log('verify.php error: '.$e->getMessage());
  http_response_code(500);
  $status='error';
  $title='Oups, une erreur est survenue';
  $message='Un incident temporaire empêche la vérification. Réessaie dans quelques minutes.';
  $ctaPrimaryHref = 'connexion.php';
  $ctaPrimaryText = 'Se connecter';
  $ctaSecondaryHref = 'inscription.php';
  $ctaSecondaryText = 'Créer un compte';
}

$autoRedirect = ($status === 'success') ? $ctaPrimaryHref : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Vérification d’e-mail • FunCodeLab</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet">
<style>
:root{
  --accent:#007BFF; --accent-2:#FFA500; --accent-3:#63E6BE;
  --ok:#16a34a; --warn:#f59e0b; --err:#ef4444; --info:#0ea5e9;
  --text:#0b1220; --muted:#5b667a; --surface:#fff; --surface-2:#f6f8fc; --border:#e6ebf3;
}
body{
  background:linear-gradient(135deg,var(--accent),var(--accent-2));
  font-family:'Plus Jakarta Sans',sans-serif;
  display:flex;justify-content:center;align-items:center;
  min-height:100vh; padding:1rem; color:var(--text);
}
.card{
  background:var(--surface);border-radius:24px;padding:2rem;
  box-shadow:0 25px 60px rgba(0,0,0,.15);
  max-width:600px;width:100%;animation:fadeInUp .6s ease forwards;
}
.status-icon{
  width:64px;height:64px;border-radius:50%;
  display:flex;justify-content:center;align-items:center;
  background:linear-gradient(135deg,var(--accent),var(--accent-2));
  color:white;font-size:28px;animation:rotateGradient 3s linear infinite;
  margin-bottom:1rem;
}
h1{
  font-size:1.8rem;font-weight:800;
  background:linear-gradient(90deg,var(--accent),var(--accent-2));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
p.msg{margin-top:1rem;color:var(--muted);font-size:1.05rem;line-height:1.6;}
.badge{
  padding:.4rem .8rem;border-radius:12px;display:inline-block;margin-bottom:.5rem;
  backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.3);
}
.badge.ok{color:var(--ok);background:rgba(22,163,74,0.1);}
.badge.err{color:var(--err);background:rgba(239,68,68,0.1);}
.badge.warn{color:var(--warn);background:rgba(245,158,11,0.1);}
.badge.info{color:var(--info);background:rgba(14,165,233,0.1);}
.cta{margin-top:1.5rem;display:flex;gap:.8rem;flex-wrap:wrap;}
.btn{
  padding:.8rem 1.2rem;border-radius:12px;font-weight:800;text-decoration:none;
  display:inline-flex;align-items:center;gap:.4rem;transition:.2s ease;
}
.btn.primary{background:var(--accent);color:white;box-shadow:0 4px 12px rgba(0,0,0,0.15);}
.btn.primary:hover{box-shadow:0 6px 16px rgba(0,0,0,0.25);transform:translateY(-1px);}
.btn.secondary{background:var(--surface-2);color:var(--text);}
.btn.secondary:hover{background:var(--border);}
@keyframes fadeInUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
@keyframes rotateGradient{0%{filter:hue-rotate(0);}100%{filter:hue-rotate(360deg);}}
.confetti{position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;overflow:hidden;}
.confetti span{
  position:absolute;width:8px;height:8px;background:var(--accent);
  animation:fall 3s linear infinite;
}
@keyframes fall{0%{transform:translateY(-10vh) rotate(0);}100%{transform:translateY(100vh) rotate(360deg);}}
</style>
<?php if ($autoRedirect): ?>
<script>setTimeout(()=>{location.href=<?= json_encode($autoRedirect) ?>},4000);</script>
<?php endif; ?>
</head>
<body>
<div class="card">
  <div class="status-icon">
    <?= $status==='success'?'✅':($status==='expired'?'⏳':($status==='invalid'?'⚠️':'❌')) ?>
  </div>
  <div class="badge <?= $status==='success'?'ok':($status==='expired'?'warn':($status==='invalid'?'info':'err')) ?>">
    <?= $status==='success'?'Succès':($status==='expired'?'Lien expiré':($status==='invalid'?'Information':'Erreur')) ?>
  </div>
  <h1><?= htmlspecialchars($title) ?></h1>
  <p class="msg"><?= htmlspecialchars($message) ?></p>
  <div class="cta">
    <a href="<?= htmlspecialchars($ctaPrimaryHref) ?>" class="btn primary">👉 <?= htmlspecialchars($ctaPrimaryText) ?></a>
    <a href="<?= htmlspecialchars($ctaSecondaryHref) ?>" class="btn secondary"><?= htmlspecialchars($ctaSecondaryText) ?></a>
  </div>
  <?php if ($status==='success'): ?><p class="meta">Redirection dans quelques secondes...</p><?php endif; ?>
</div>

<?php if ($status==='success'): ?>
<div class="confetti">
  <?php for($i=0;$i<40;$i++): ?>
    <span style="left:<?= rand(0,100) ?>%;animation-delay:<?= rand(0,300)/100 ?>s;background:hsl(<?= rand(0,360) ?>,70%,60%);"></span>
  <?php endfor; ?>
</div>
<?php endif; ?>
</body>
</html>
