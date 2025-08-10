<?php
// ================== profil.php (édition profil + upload avatar) ==================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';
require_once 'session.php';
require_once __DIR__.'/lib/progression.php';

// Exige la connexion
require_login();
$user = current_user();

// Connexion PDO
$db = $pdo ?? ($conn ?? null);
if (!$db) die("Erreur : aucune connexion à la base de données.");

// Base URL absolue pour les redirections & liens
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$base = $protocol . "://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

// Sommes-nous sur la page profil ? (pour masquer "Mon profil" dans le menu)
$onProfile = basename($_SERVER['PHP_SELF']) === 'profil.php';

// Récupère les infos actuelles (inclut email_verified_at)
$stmt = $db->prepare("
  SELECT id, username, email, created_at, avatar_url, email_verified_at
  FROM users
  WHERE id = :id
  LIMIT 1
");
$stmt->execute([':id' => $user['id']]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$userData) die("Utilisateur introuvable.");

// Flag vérification email
$isVerified = !empty($userData['email_verified_at']);

// --------- Progression depuis la BDD ---------
$stats = get_user_stats($db, (int)$userData['id']);
$level       = (int)$stats['level'];
$xpCurrent   = (int)$stats['xp'];
$xpNext      = (int)$stats['need'];
$progressPct = (int)$stats['pct'];

$userBadges = get_user_badges($db, (int)$userData['id']);

// Messages et erreurs
$errors = [];
$success = flash('success');

// Soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $errors[] = "Le formulaire a expiré. Merci de réessayer.";
    } else {
        $newUsername = trim($_POST['username'] ?? '');
        if ($newUsername === '' || !preg_match('/^[A-Za-z0-9_.]{3,32}$/', $newUsername)) {
            $errors[] = "Pseudo invalide. Utilise 3–32 caractères : lettres/chiffres/._";
        } else if (strcasecmp($newUsername, $userData['username']) !== 0) {
            $q = $db->prepare("SELECT 1 FROM users WHERE username = :u AND id <> :id LIMIT 1");
            $q->execute([':u' => $newUsername, ':id' => $userData['id']]);
            if ($q->fetch()) $errors[] = "Ce pseudo est déjà pris.";
        }

        // Upload avatar (facultatif)
        $newAvatarUrl = null;
        $removeAvatar = !empty($_POST['remove_avatar']);

        if (
          !$removeAvatar && isset($_FILES['avatar']) && is_array($_FILES['avatar']) &&
          (int)($_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE
        ) {
            $f = $_FILES['avatar'];

            if ($f['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Échec de l’upload (code {$f['error']}).";
            } else if ($f['size'] > 2 * 1024 * 1024) {
                $errors[] = "Avatar trop volumineux (max 2 Mo).";
            } else {
                $fi = new finfo(FILEINFO_MIME_TYPE);
                $mime = $fi->file($f['tmp_name']);
                $allowed = ['image/png'=>'png','image/jpeg'=>'jpg','image/webp'=>'webp'];

                if (!isset($allowed[$mime])) {
                    $errors[] = "Format d’image non supporté (PNG, JPG, WEBP).";
                } else {
                    $dirAbs = __DIR__ . '/uploads/avatars';
                    if (!is_dir($dirAbs)) @mkdir($dirAbs, 0775, true);

                    $ext = $allowed[$mime];
                    $filename = bin2hex(random_bytes(12)) . '.' . $ext;
                    $destAbs  = $dirAbs . '/' . $filename;
                    $destRel  = 'uploads/avatars/' . $filename;

                    if (!is_uploaded_file($f['tmp_name'])) {
                        $errors[] = "Fichier non valide.";
                    } elseif (!@move_uploaded_file($f['tmp_name'], $destAbs)) {
                        $errors[] = "Impossible d’enregistrer l’avatar (droits d’écriture ?).";
                    } else {
                        @chmod($destAbs, 0664);
                        $newAvatarUrl = $destRel;
                    }
                }
            }
        }

        if (!$errors) {
            if ($removeAvatar) {
                $upd = $db->prepare("UPDATE users SET username = :u, avatar_url = NULL WHERE id = :id");
                $upd->execute([':u'=>$newUsername, ':id'=>$userData['id']]);
                $userData['avatar_url'] = null;
                $_SESSION['user']['avatar_url'] = null;
            } elseif ($newAvatarUrl !== null) {
                $upd = $db->prepare("UPDATE users SET username = :u, avatar_url = :a WHERE id = :id");
                $upd->execute([':u'=>$newUsername, ':a'=>$newAvatarUrl, ':id'=>$userData['id']]);
                $userData['avatar_url'] = $newAvatarUrl;
                $_SESSION['user']['avatar_url'] = $newAvatarUrl;
            } else {
                $upd = $db->prepare("UPDATE users SET username = :u WHERE id = :id");
                $upd->execute([':u'=>$newUsername, ':id'=>$userData['id']]);
            }

            $userData['username'] = $newUsername;
            $_SESSION['user']['username'] = $newUsername;

            flash('success', 'Profil mis à jour avec succès ✅');
            header("Location: {$base}/index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profil - FunCodeLab</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
/* ------------------ Thème ------------------ */
:root{
  --accent:#007BFF; --accent-2:#FFA500; --accent-3:#FFC107;
  --text:#101926; --text-muted:#4a5568;
  --bg-grad-1:#2185ff; --bg-grad-2:#ffb347;

  --surface:#ffffff; --surface-2:#ffffffc9;
  --glass: rgba(255,255,255,.58);
  --glass-border: rgba(0,0,0,.08);

  --input-border:#e7e7e7;
  --chip-bg:#eef5ff; --chip-text:#1a5fff;

  --card-shadow:0 30px 60px rgba(0,0,0,.12);

  --navbar:#ffffff; --navbar-shadow:0 2px 8px rgba(0,0,0,.08);
}
html[data-theme="dark"]{
  --accent:#5aa1ff; --accent-2:#ffb02e; --accent-3:#ffd54d;
  --text:#eef2f7; --text-muted:#b4bfd0;
  --bg-grad-1:#0f172a; --bg-grad-2:#1f2937;

  --surface:#121826; --surface-2:#1a2334cc;
  --glass: rgba(25,30,45,.6);
  --glass-border: rgba(255,255,255,.08);

  --input-border:#2b364b;
  --chip-bg:#1e2a44; --chip-text:#9cc2ff;

  --card-shadow:0 30px 60px rgba(0,0,0,.5);
  --navbar:#0f1624; --navbar-shadow:0 2px 8px rgba(0,0,0,.5);
}

*{box-sizing:border-box;margin:0;padding:0;font-family:'Plus Jakarta Sans',sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;}
body{min-height:100vh;color:var(--text);background:linear-gradient(135deg,var(--bg-grad-1),var(--bg-grad-2));}

/* ------------------ NAVBAR ------------------ */
.main-navbar{
  background:var(--navbar);
  box-shadow:var(--navbar-shadow);
  position:sticky;top:0;z-index:50;
  margin-bottom:10px;
}
.nav-content{max-width:1100px;margin:auto;padding:.8rem 1rem;display:flex;align-items:center;gap:.75rem;}
.logo{font-size:1.6rem;font-weight:800;color:var(--accent);text-decoration:none}
.nav-spacer{flex:1}

/* liens */
.nav-links{display:flex;gap:.8rem;align-items:center;list-style:none;}
.nav-links a{display:inline-flex;align-items:center;gap:.4rem;text-decoration:none;color:var(--text);font-weight:700;opacity:.9;padding:.45rem .7rem;border-radius:10px;}
.nav-links a:hover{opacity:1;background:color-mix(in srgb, var(--surface-2) 70%, transparent);}
.nav-btn{background:var(--accent-2);color:#fff !important;padding:.55rem .9rem;border-radius:12px;font-weight:800;}
.nav-btn:hover{background:var(--accent-3);color:#222 !important}

/* bouton thème */
.theme-toggle{display:inline-flex;align-items:center;gap:.5rem;background:transparent;border:1.5px solid var(--glass-border);padding:.35rem .6rem;border-radius:999px;cursor:pointer;color:var(--text);font-weight:700;}
.theme-toggle:focus-visible{outline:3px solid var(--accent);outline-offset:3px}
.theme-pill{width:36px;height:20px;background:var(--surface-2);border:1px solid var(--input-border);border-radius:999px;position:relative}
.theme-knob{position:absolute;top:50%;left:2px;transform:translateY(-50%);width:16px;height:16px;border-radius:50%;background:var(--accent);transition:left .25s ease}
html[data-theme="dark"] .theme-knob{left:18px}

/* menu utilisateur */
.nav-user{position:relative}
.user-btn{display:flex;align-items:center;gap:.6rem;padding:.35rem .6rem;border:1.5px solid var(--glass-border);border-radius:999px;background:var(--surface-2);cursor:pointer}
.user-avatar{width:28px;height:28px;border-radius:50%;object-fit:cover;background:var(--accent);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:800}
.user-name{font-weight:800;font-size:.95rem;color:var(--text)}
.dropdown{
  position:absolute;right:0;top:calc(100% + 12px);
  min-width:190px;background:var(--surface);border:1px solid var(--glass-border);
  border-radius:12px;box-shadow:0 16px 40px rgba(0,0,0,.25);padding:.4rem;display:none
}
.dropdown.show{display:block}
.dropdown a{display:flex;align-items:center;gap:.6rem;padding:.55rem .7rem;border-radius:8px;color:var(--text);text-decoration:none;font-weight:700}
.dropdown a:hover{background:color-mix(in srgb, var(--surface-2) 80%, transparent)}
.dropdown .sep{height:1px;background:var(--glass-border);margin:.35rem 0}

/* burger mobile */
.burger{display:none;align-items:center;justify-content:center;width:40px;height:40px;border:1.5px solid var(--glass-border);border-radius:10px;background:transparent;color:var(--text);cursor:pointer}
.burger span{width:18px;height:2px;background:var(--text);position:relative;display:block}
.burger span::before,.burger span::after{content:"";position:absolute;left:0;width:18px;height:2px;background:var(--text)}
.burger span::before{top:-6px}.burger span::after{top:6px}

@media (max-width:860px){
  .burger{display:flex}
  .nav-links{position:fixed;inset:56px 10px auto 10px;background:var(--surface);border:1px solid var(--glass-border);border-radius:14px;box-shadow:0 20px 50px rgba(0,0,0,.35);padding:.6rem;display:none;flex-direction:column;gap:.3rem;z-index:60}
  .nav-links.show{display:flex}
  .user-name{display:none}
}

/* COVER */
.cover{
  position:relative;width:100%;height:220px;
  background:
    radial-gradient(1200px 300px at 10% 0%, rgba(255,255,255,.15), transparent 60%),
    radial-gradient(900px 260px at 90% 10%, rgba(255,255,255,.1), transparent 60%),
    linear-gradient(120deg, var(--bg-grad-1) 0%, #3b82f6 45%, var(--bg-grad-2) 100%);
}
.cover::after{content:"";position:absolute;inset:0;backdrop-filter: blur(2px);pointer-events:none;}

/* WRAPPER */
.wrapper{max-width:980px;margin:-90px auto 2.2rem;padding:0 1rem;position:relative;z-index:10;}
.grid{display:grid;grid-template-columns:320px 1fr;gap:1.5rem;}
@media (max-width:860px){.grid{grid-template-columns:1fr;}}

/* GLASS CARDS */
.card,.form-card{background:var(--glass);border:1px solid var(--glass-border);border-radius:18px;box-shadow:var(--card-shadow);backdrop-filter: blur(10px);}
.card{padding:1.6rem;} .form-card{padding:1.6rem;}

/* Left card */
.hero{display:flex;align-items:center;gap:16px;margin-bottom:1rem;}
.avatar{width:84px;height:84px;border-radius:50%;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;flex-shrink:0;box-shadow:0 8px 18px rgba(0,0,0,.25);object-fit:cover;}
.username{font-size:1.55rem;font-weight:800;color:var(--text);}
.meta{color:var(--text-muted);margin-top:.35rem}

/* Icône vérifié (style Instagram) */
.verified-icon{
  width:20px;height:20px;display:inline-block;vertical-align:middle;
  filter: drop-shadow(0 0 1px rgba(0,0,0,.3));
}

/* ---- Progress ring ---- */
.progress{
  display:flex;align-items:center;gap:12px;margin-top:14px;padding:10px;border-radius:12px;
  background:var(--surface-2);border:1px solid var(--glass-border);
}
.ring{position:relative;width:64px;height:64px;}
.ring svg{transform:rotate(-90deg)}
.ring circle.track{stroke:color-mix(in srgb, var(--text-muted) 30%, transparent);opacity:.4}
.ring circle.ind{stroke:var(--accent);transition:stroke-dashoffset .6s ease}
.ring .lvl{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-weight:800;color:var(--text);}

/* Right card form */
h2{font-size:1.55rem;line-height:1.25;font-weight:800;color:var(--text);margin:.25rem 0 .75rem;}
hr.sep{border:none;border-top:1px dashed var(--glass-border);margin:1rem 0;}
.form-row{display:flex;flex-direction:column;margin-bottom:1rem;}
label{font-weight:800;margin-bottom:.45rem;color:var(--text);display:flex;align-items:center;gap:.5rem;}
label .ico{width:18px;height:18px;display:inline-block}
@keyframes floaty{0%,100%{transform:translateY(0)}50%{transform:translateY(-2px)}}
.form-row:focus-within label .ico{animation:floaty 1.6s ease-in-out infinite}

/* inputs */
input[type="text"]{color:var(--text);background:var(--surface-2);border:2px solid var(--input-border);border-radius:12px;padding:.75rem .9rem;font-size:1rem;backdrop-filter: blur(4px);}
input[type="text"]::placeholder{color:var(--text-muted)}
input[type="text"]:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px color-mix(in srgb, var(--accent) 30%, transparent);}
small.helper{color:var(--text-muted);margin-top:.25rem}

/* Dropzone */
.dz{position:relative;background:var(--surface-2);border:2px dashed var(--input-border);border-radius:14px;padding:18px;display:flex;align-items:center;gap:14px;cursor:pointer;backdrop-filter: blur(4px);}
.dz:hover{border-color:var(--accent);background:color-mix(in srgb, var(--surface-2) 88%, var(--accent) 12%);}
.dz-img{width:72px;height:72px;border-radius:50%;object-fit:cover;display:none;box-shadow:0 8px 18px rgba(0,0,0,.08);}
.dz-cta{color:var(--text-muted);font-size:.95rem;}
.dz-browse{color:var(--accent);text-decoration:underline;cursor:pointer}

/* Buttons & alerts */
.actions{display:flex;gap:.6rem;flex-wrap:wrap;margin-top:.6rem;}
.btn{border:none;border-radius:12px;padding:.7rem 1rem;font-weight:800;cursor:pointer;box-shadow:0 10px 18px rgba(0,0,0,.06);transition:.2s;}
.btn-primary{background:var(--accent);color:#fff;}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 14px 22px rgba(0,0,0,.18);}
.btn-secondary{background:color-mix(in srgb, var(--surface) 85%, #888 15%);color:var(--text);}
.btn-danger{background:#e66;color:#fff;}
.btn[disabled]{opacity:.7;cursor:not-allowed}

/* Toasts */
.toast-wrap{position:fixed;top:18px;right:18px;display:flex;flex-direction:column;gap:10px;z-index:50;}
.toast{min-width:260px;max-width:360px;padding:.8rem 1rem;border-radius:12px;font-weight:700;border:1px solid rgba(0,0,0,.06);box-shadow:0 10px 24px rgba(0,0,0,.12);transform:translateY(-12px);opacity:0;pointer-events:none;transition:transform .25s ease, opacity .25s ease;backdrop-filter: blur(8px);}
.toast.show{transform:translateY(0);opacity:1;pointer-events:auto;}
.toast.ok{background:#eafff2cc;color:#0a7a3b;border-color:#b8f3cd;}
.toast.err{background:#fff0f0cc;color:#b00020;border-color:#ffd1d1;}

/* Loader */
.loader{position:fixed;inset:0;background:rgba(0,0,0,.18);backdrop-filter: blur(2px);display:none;align-items:center;justify-content:center;z-index:40;}
.spinner{width:46px;height:46px;border-radius:50%;border:5px solid rgba(255,255,255,.18);border-top-color:var(--accent);animation:spin .9s linear infinite;}
@keyframes spin{to{transform:rotate(360deg)}}

/* Badges (profil) */
.chip-center{display:flex;justify-content:center;margin-top:8px}
.badge-grid-user{
  margin-top:.6rem;
  display:grid;
  grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
  gap:.55rem;
}
.ubadge{
  background: var(--surface-2);
  border:1px solid var(--glass-border);
  border-radius:12px;
  padding:.55rem .6rem;
  display:flex;align-items:center;gap:.5rem;
}
.uicon{
  width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;
  font-size:16px; box-shadow:0 1px 4px rgba(0,0,0,.15);
}
.uname{font-weight:700; font-size:.92rem; color:var(--text);}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="main-navbar" role="navigation" aria-label="Navigation principale">
  <div class="nav-content">
    <a class="logo" href="<?= htmlspecialchars($base) ?>/index.php">FunCodeLab</a>

    <button class="burger" id="burger" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="primaryNav">
      <span></span>
    </button>

    <div class="nav-spacer"></div>

    <ul id="primaryNav" class="nav-links" aria-hidden="true">
      <li><a href="<?= htmlspecialchars($base) ?>/index.php">Accueil</a></li>
      <li>
        <button id="themeToggle" class="theme-toggle" type="button" aria-pressed="false" aria-label="Basculer le thème">
          <span id="themeLabel">Clair</span>
          <span class="theme-pill" aria-hidden="true"><span class="theme-knob"></span></span>
        </button>
      </li>

      <!-- Menu utilisateur -->
      <li class="nav-user">
        <button class="user-btn" id="userBtn" aria-haspopup="menu" aria-expanded="false">
          <?php if (!empty($userData['avatar_url'])): ?>
            <img src="<?= htmlspecialchars($userData['avatar_url']) ?>" alt="" class="user-avatar">
          <?php else: ?>
            <span class="user-avatar"><?= strtoupper(substr($userData['username'],0,1)) ?></span>
          <?php endif; ?>
          <span class="user-name"><?= htmlspecialchars($userData['username']) ?></span>
        </button>
        <div class="dropdown" id="userMenu" role="menu" aria-labelledby="userBtn">
          <a href="<?= htmlspecialchars($base) ?>/index.php" role="menuitem">🏠 Accueil</a>
          <?php if (!$onProfile): ?>
            <a href="<?= htmlspecialchars($base) ?>/profil.php" role="menuitem">👤 Mon profil</a>
          <?php endif; ?>
          <div class="sep" role="separator"></div>
          <a href="<?= htmlspecialchars($base) ?>/logout.php" role="menuitem">🚪 Se déconnecter</a>
        </div>
      </li>

      <!-- bouton déconnexion visible desktop -->
      <li class="only-desktop"><a href="<?= htmlspecialchars($base) ?>/logout.php" class="nav-btn">Se déconnecter</a></li>
    </ul>
  </div>
</nav>

<!-- BANDEAU DE COUVERTURE -->
<div class="cover"></div>

<!-- TOASTS + LOADER -->
<div class="toast-wrap" id="toastWrap" aria-live="polite" aria-atomic="true"></div>
<div class="loader" id="loader" role="status" aria-live="assertive" aria-label="Chargement">
  <div class="spinner"></div>
</div>

<div class="wrapper">
  <?php
    $toastSuccess = $success ?: '';
    $toastError   = $errors ? implode(" \n", array_map('htmlspecialchars', $errors)) : '';
  ?>

  <div class="grid">
    <!-- Carte avatar / résumé + progression -->
    <section class="card">
      <div class="hero">
        <?php if (!empty($userData['avatar_url'])): ?>
          <img src="<?= htmlspecialchars($userData['avatar_url']) ?>" alt="Avatar" class="avatar" fetchpriority="high">
        <?php else: ?>
          <div class="avatar"><?= strtoupper(substr($userData['username'],0,1)) ?></div>
        <?php endif; ?>
        <div>
          <!-- Pseudo + pastille vérifiée -->
<div style="display:flex; align-items:center; gap:6px;">
  <div class="username"><?= htmlspecialchars($userData['username']) ?></div>
  <?php if ($isVerified): ?>
    <svg class="verified-icon"
         xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 24 24"
         width="20" height="20"
         role="img" aria-label="Compte vérifié">
      <title>Vérifié</title>
      <circle cx="12" cy="12" r="12" fill="#3897f0"/>
      <path d="M10 15l-3-3 1.4-1.4L10 12.2l5.6-5.6L17 8l-7 7z" fill="#fff"/>
    </svg>
  <?php endif; ?>
</div>


          <div class="meta"><?= htmlspecialchars($userData['email']) ?></div>
          <div class="meta">Inscrit le <?= htmlspecialchars(date("d/m/Y", strtotime($userData['created_at']))) ?></div>
        </div>
      </div>

      <!-- Progression -->
      <div class="progress" id="progress"
           data-pct="<?= (int)$progressPct ?>"
           data-xpcur="<?= (int)$xpCurrent ?>"
           data-xpnext="<?= (int)$xpNext ?>">
        <div class="ring">
          <svg width="64" height="64" viewBox="0 0 64 64" aria-hidden="true">
            <circle class="track" cx="32" cy="32" r="28" fill="none" stroke-width="8" />
            <circle class="ind"   cx="32" cy="32" r="28" fill="none" stroke-width="8"
                    stroke-linecap="round" stroke-dasharray="175.93" stroke-dashoffset="175.93" />
          </svg>
          <div class="lvl"><?= (int)$level ?></div>
        </div>
        <div>
          <div class="badge">Niveau <?= (int)$level ?></div>
          <div class="meta"><strong><?= (int)$xpCurrent ?></strong> / <?= (int)$xpNext ?> XP • <?= (int)$progressPct ?>%</div>
        </div>
      </div>

      <div class="chip-center"><span class="badge">Badges</span></div>

      <?php if (!empty($userBadges)): ?>
        <div class="badge-grid-user">
          <?php foreach ($userBadges as $b): ?>
            <div class="ubadge" title="<?= htmlspecialchars($b['name']) ?>">
              <span class="uicon" style="background:<?= htmlspecialchars($b['color'] ?: '#ffd166') ?>;">
                <?= htmlspecialchars($b['icon'] ?: '🏅') ?>
              </span>
              <div class="uname"><?= htmlspecialchars($b['name']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="meta" style="text-align:center;margin-top:.25rem;">Pas encore de badge… continue !</div>
      <?php endif; ?>
    </section>

    <!-- Formulaire d’édition -->
    <section class="form-card">
      <h2>Modifier mon profil</h2>
      <hr class="sep" />
      <form id="profileForm" method="POST" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">

        <div class="form-row">
          <label for="username">
            <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-3 0-8 1.5-8 4v2h16v-2c0-2.5-5-4-8-4Z"/></svg>
            Pseudo
          </label>
          <input type="text" id="username" name="username"
                 value="<?= htmlspecialchars($userData['username']) ?>" required
                 pattern="[A-Za-z0-9_.]{3,32}" />
          <small class="helper">3–32 caractères : lettres, chiffres, point et underscore seulement.</small>
        </div>

        <div class="form-row">
          <label for="avatar">
            <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 5a4 4 0 1 1-4 4 4 0 0 1 4-4Zm7 14v-1a5 5 0 0 0-5-5H10a5 5 0 0 0-5 5v1Z"/></svg>
            Avatar
          </label>

          <div id="dropzone" class="dz">
            <img id="avatarPreview" class="dz-img" alt="">
            <div class="dz-cta">
              <strong>Glisse-dépose</strong> ou <span class="dz-browse">choisis un fichier</span><br>
              <small>PNG, JPG ou WEBP — 2 Mo max.</small>
            </div>
            <input type="file" id="avatar" name="avatar" accept="image/png,image/jpeg,image/webp" hidden>
          </div>

          <div class="actions">
            <button type="button" id="chooseAvatar" class="btn btn-secondary">Choisir</button>
            <?php if (!empty($userData['avatar_url'])): ?>
              <button type="button" id="removeAvatarBtn" class="btn btn-danger">Supprimer l’avatar</button>
            <?php else: ?>
              <button type="button" id="removeAvatarBtn" class="btn btn-danger" style="display:none;">Supprimer l’avatar</button>
            <?php endif; ?>
          </div>

          <input type="hidden" name="remove_avatar" id="remove_avatar" value="">
        </div>

        <div class="actions">
          <button id="saveBtn" class="btn btn-primary" type="submit">Enregistrer</button>
          <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/index.php">Annuler</a>
        </div>
      </form>
    </section>
  </div>
</div>

<script>
(function(){
  // ---------- Thème ----------
  const root = document.documentElement;
  const toggle = document.getElementById('themeToggle');
  const label  = document.getElementById('themeLabel');
  const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  const savedTheme = localStorage.getItem('theme');
  setTheme(savedTheme || (prefersDark ? 'dark' : 'light'));
  toggle?.addEventListener('click', () => {
    const mode = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    setTheme(mode, true);
  });
  function setTheme(mode, persist){
    root.setAttribute('data-theme', mode);
    if (label) label.textContent = (mode === 'dark') ? 'Sombre' : 'Clair';
    if (toggle) toggle.setAttribute('aria-pressed', mode === 'dark' ? 'true' : 'false');
    if (persist) localStorage.setItem('theme', mode);
  }

  // ---------- NAV (burger) ----------
  const burger = document.getElementById('burger');
  const nav    = document.getElementById('primaryNav');
  const closeNav = ()=>{ nav?.classList.remove('show'); burger?.setAttribute('aria-expanded','false'); nav?.setAttribute('aria-hidden','true'); };
  burger?.addEventListener('click', ()=>{
    const open = nav.classList.toggle('show');
    burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    nav.setAttribute('aria-hidden', open ? 'false' : 'true');
  });
  window.addEventListener('resize', ()=>{ if (window.innerWidth>860) closeNav(); });
  document.addEventListener('click', (e)=>{ if(window.innerWidth<=860 && !nav.contains(e.target) && !burger.contains(e.target)) closeNav(); });
  document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeNav(); });

  // ---------- Menu utilisateur ----------
  const userBtn = document.getElementById('userBtn');
  const userMenu= document.getElementById('userMenu');
  const closeUser = ()=>{ userMenu?.classList.remove('show'); userBtn?.setAttribute('aria-expanded','false'); };
  userBtn?.addEventListener('click', (e)=>{
    e.stopPropagation();
    const open = userMenu.classList.toggle('show');
    userBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
  document.addEventListener('click', (e)=>{ if(userMenu && !userMenu.contains(e.target) && !userBtn.contains(e.target)) closeUser(); });
  document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeUser(); });

  // ---------- Progress ring ----------
  const prog = document.getElementById('progress');
  if (prog){
    const pct = parseInt(prog.dataset.pct || '0', 10);
    const C = 175.93; // 2πr avec r=28
    const ind = prog.querySelector('circle.ind');
    const offset = C - (C * pct / 100);
    ind.style.strokeDashoffset = offset;
  }

  // ---------- Dropzone ----------
  const dz = document.getElementById('dropzone');
  const fileInput = document.getElementById('avatar');
  const preview = document.getElementById('avatarPreview');
  const chooseBtn = document.getElementById('chooseAvatar');
  const removeBtn = document.getElementById('removeAvatarBtn');
  const removeField = document.getElementById('remove_avatar');

  const setPreview = file=>{
    if(!file) return;
    const ok = ['image/png','image/jpeg','image/webp'].includes(file.type);
    if(!ok) return toast('Format non supporté (PNG, JPG, WEBP).', 'err');
    const r = new FileReader();
    r.onload = e => { preview.src = e.target.result; preview.style.display='block'; removeField.value=''; removeBtn.style.display='inline-block'; };
    r.readAsDataURL(file);
  };

  dz?.addEventListener('click', ()=> fileInput.click());
  chooseBtn?.addEventListener('click', ()=> fileInput.click());
  dz?.querySelector('.dz-browse')?.addEventListener('click', (e)=>{ e.stopPropagation(); fileInput.click(); });
  fileInput?.addEventListener('change', e => setPreview(e.target.files[0]));
  dz?.addEventListener('dragover', e => { e.preventDefault(); dz.style.borderColor='var(--accent)'; });
  dz?.addEventListener('dragleave', ()=> dz.style.borderColor='var(--input-border)');
  dz?.addEventListener('drop', e => {
    e.preventDefault(); dz.style.borderColor='var(--input-border)';
    const f = e.dataTransfer.files[0]; if(!f) return;
    const dt = new DataTransfer(); dt.items.add(f); fileInput.files = dt.files;
    setPreview(f);
  });

  removeBtn?.addEventListener('click', ()=>{
    if(!confirm('Supprimer votre avatar ?')) return;
    if (fileInput) fileInput.value = '';
    if (preview){ preview.src=''; preview.style.display='none'; }
    removeField.value='1';
    toast('Avatar marqué pour suppression.', 'ok');
  });

  <?php if (!empty($userData['avatar_url'])): ?>
    (function(){ const p = document.getElementById('avatarPreview'); if(p){ p.src = "<?= htmlspecialchars($userData['avatar_url']) ?>"; p.style.display='block'; }})();
  <?php endif; ?>

  // ---------- Toasts ----------
  const wrap = document.getElementById('toastWrap');
  function toast(message, type='ok', timeout=2800){
    const t = document.createElement('div');
    t.className = 'toast ' + (type === 'err' ? 'err' : 'ok');
    t.innerHTML = message.replace(/\n/g,'<br>');
    wrap.appendChild(t);
    t.offsetHeight; t.classList.add('show');
    setTimeout(()=>{ t.classList.remove('show'); setTimeout(()=>wrap.removeChild(t), 250); }, timeout);
  }
  const serverSuccess = <?= json_encode($toastSuccess, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;
  const serverError   = <?= json_encode($toastError,   JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;
  if (serverSuccess) toast(serverSuccess, 'ok', 3200);
  if (serverError)   toast(serverError,   'err', 4200);

  // ---------- Loader ----------
  const form   = document.getElementById('profileForm');
  const saveBtn= document.getElementById('saveBtn');
  const loader = document.getElementById('loader');
  form?.addEventListener('submit', ()=>{
    saveBtn?.setAttribute('disabled','disabled');
    if (saveBtn) saveBtn.textContent = 'Enregistrement...';
    if (loader) loader.style.display = 'flex';
  });
})();
</script>
</body>
</html>
