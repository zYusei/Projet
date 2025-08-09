<?php
// verify.php — confirmation d'e-mail avec rendu soigné
require_once __DIR__.'/db.php';
require_once __DIR__.'/session.php';

$status = 'success'; // success | error | expired | invalid
$title  = 'Adresse vérifiée 🎉';
$message= 'Ton adresse email a bien été confirmée. Tu peux maintenant te connecter.';
$ctaPrimaryHref = 'connexion.php';
$ctaPrimaryText = 'Se connecter';
$ctaSecondaryHref = 'inscription.php';
$ctaSecondaryText = 'Retour à l’inscription';

// ——— Vérifications serveur (identiques à ta logique) ———
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
      http_response_code(200);
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
        // OK : on valide
        $pdo->beginTransaction();
        $upd = $pdo->prepare("UPDATE users
          SET email_verified_at = NOW(), verify_token_hash = NULL, verify_token_expires = NULL
          WHERE id = :id");
        $upd->execute([':id'=>$userId]);
        $pdo->commit();

        http_response_code(200);
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

// Auto-redirect doux après succès (4s)
$autoRedirect = ($status === 'success') ? $ctaPrimaryHref : '';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light dark">
  <meta name="robots" content="noindex,nofollow">
  <title>Vérification d’e-mail • FunCodeLab</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --accent:#007BFF; --accent-2:#FFA500; --accent-3:#63E6BE;
      --text:#0b1220; --muted:#5b667a;
      --bg1:#2185ff; --bg2:#ffb347;
      --surface:#ffffff; --surface-2:#f6f8fc; --border:#e6ebf3;
      --ok:#16a34a; --warn:#f59e0b; --err:#ef4444; --info:#0ea5e9;
      --shadow: 0 30px 60px rgba(0,0,0,.15), 0 0 0 1px rgba(0,0,0,.04);
    }
    html[data-theme="dark"]{
      --accent:#5aa1ff; --accent-2:#ffb02e; --accent-3:#4ade80;
      --text:#eef2f7; --muted:#9aa5b5;
      --bg1:#0f172a; --bg2:#1f2937;
      --surface:#0f1624; --surface-2:#131c2c; --border:#243247;
      --ok:#22c55e; --warn:#fbbf24; --err:#f87171; --info:#38bdf8;
      --shadow: 0 40px 90px rgba(0,0,0,.5), 0 0 0 1px rgba(255,255,255,.06);
    }
    *{box-sizing:border-box;margin:0;padding:0}
    body{
      min-height:100vh;
      background:linear-gradient(135deg,var(--bg1),var(--bg2));
      color:var(--text);
      font-family:'Plus Jakarta Sans',system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
      display:grid; place-items:center; padding:2rem 1rem;
    }
    .shell{max-width:780px;width:100%}
    .card{
      background:var(--surface); border:1px solid var(--border); border-radius:24px;
      box-shadow:var(--shadow); overflow:hidden;
      display:grid; grid-template-columns:1fr; 
    }
    .head{
      padding:1.6rem 1.8rem; background:linear-gradient(90deg, color-mix(in srgb, var(--surface) 75%, transparent), transparent);
      border-bottom:1px solid var(--border); display:flex; align-items:center; gap:1rem;
    }
    .badge{
      font-weight:800; font-size:.85rem; padding:.25rem .6rem; border-radius:999px; border:1px solid;
      display:inline-flex; align-items:center; gap:.45rem;
    }
    .badge.ok{ color:var(--ok); border-color:color-mix(in srgb, var(--ok) 60%, transparent); background:color-mix(in srgb, var(--ok) 12%, transparent); }
    .badge.err{ color:var(--err); border-color:color-mix(in srgb, var(--err) 60%, transparent); background:color-mix(in srgb, var(--err) 12%, transparent); }
    .badge.warn{ color:var(--warn); border-color:color-mix(in srgb, var(--warn) 60%, transparent); background:color-mix(in srgb, var(--warn) 12%, transparent); }
    .badge.info{ color:var(--info); border-color:color-mix(in srgb, var(--info) 60%, transparent); background:color-mix(in srgb, var(--info) 12%, transparent); }
    h1{ font-size:clamp(1.6rem, 1.1rem + 1.4vw, 2.2rem); font-weight:800; letter-spacing:.2px; }
    .body{ padding:2rem 1.8rem 2.2rem; display:grid; gap:1rem; }
    .msg{ color:var(--muted); font-size:1.05rem; line-height:1.6; }
    .cta{ display:flex; gap:.6rem; flex-wrap:wrap; margin-top:.2rem; }
    .btn{
      appearance:none; border:none; cursor:pointer; text-decoration:none; font-weight:800;
      padding:.85rem 1rem; border-radius:12px; display:inline-flex; align-items:center; gap:.5rem;
      transition: transform .05s ease, box-shadow .2s ease, background .2s ease;
    }
    .btn:active{ transform:translateY(1px); }
    .btn.primary{ background:var(--accent); color:#fff; box-shadow:0 10px 18px rgba(0,0,0,.15); }
    .btn.primary:hover{ background:color-mix(in srgb, var(--accent) 85%, #0000); }
    .btn.secondary{ background:var(--surface-2); color:var(--text); border:1px solid var(--border); }
    .meta{font-size:.86rem; color:var(--muted)}
    .status-icon{
      width:44px;height:44px; border-radius:12px; display:grid;place-items:center;
      background:var(--surface-2); border:1px solid var(--border); flex:0 0 auto;
      font-size:22px;
    }
    .confetti{ position:fixed; inset:0; pointer-events:none; opacity:.4; }
  </style>
  <?php if ($autoRedirect): ?>
  <script>
    // redirection douce après succès
    setTimeout(()=>{ location.href = <?= json_encode($autoRedirect) ?>; }, 4000);
  </script>
  <?php endif; ?>
</head>
<body>
  <div class="shell">
    <div class="card">
      <div class="head">
        <div class="status-icon">
          <?php
            // icône selon l'état
            if ($status==='success')       echo '✅';
            elseif ($status==='expired')   echo '⏳';
            elseif ($status==='invalid')   echo '⚠️';
            else                           echo '❌';
          ?>
        </div>
        <div>
          <div class="badge <?= $status==='success'?'ok':($status==='expired'?'warn':($status==='invalid'?'info':'err')) ?>">
            <?php
              echo $status==='success'?'Succès':
                   ($status==='expired'?'Lien expiré':
                   ($status==='invalid'?'Information':'Erreur'));
            ?>
          </div>
          <h1><?= htmlspecialchars($title) ?></h1>
        </div>
      </div>

      <div class="body">
        <p class="msg"><?= htmlspecialchars($message) ?></p>

        <div class="cta">
          <a class="btn primary" href="<?= htmlspecialchars($ctaPrimaryHref) ?>">👉 <?= htmlspecialchars($ctaPrimaryText) ?></a>
          <a class="btn secondary" href="<?= htmlspecialchars($ctaSecondaryHref) ?>"><?= htmlspecialchars($ctaSecondaryText) ?></a>
        </div>

        <?php if ($status==='success'): ?>
          <p class="meta">Redirection automatique dans quelques secondes…</p>
        <?php elseif ($status==='expired' || $status==='invalid'): ?>
          <p class="meta">Besoin d’aide ? Contacte le support ou renvoie-toi un nouveau lien de vérification.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php if ($status==='success'): ?>
  <!-- petit confetti SVG discret -->
  <svg class="confetti" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
    <defs>
      <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0" stop-color="var(--accent)"/><stop offset="1" stop-color="var(--accent-3)"/>
      </linearGradient>
    </defs>
    <g fill="url(#g)" opacity=".35">
      <?php for($i=0;$i<36;$i++): $x=rand(0,100); $y=rand(0,100); $r=rand(1,3); ?>
        <circle cx="<?= $x ?>" cy="<?= $y ?>" r="<?= $r ?>"></circle>
      <?php endfor; ?>
    </g>
  </svg>
  <?php endif; ?>
</body>
</html>
