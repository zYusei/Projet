<?php
/**
 * FunCodeLab - Accueil + Chat global (widget flottant discret)
 * - Upload d’image fiable (sélection, drag&drop, paste) + preview
 * - SSE si dispo, sinon fallback polling auto (avec statut visible)
 * - Présence/typing (si SSE dispo)
 * - Réponse + menu moderne (éditer / supprimer / signaler)
 * - Aperçu d’URL (Open Graph)
 *
 * Dépendances côté serveur :
 *   - chat_sse.php (SSE)
 *   - chat_actions.php (send/edit/delete/report/upload/typing)
 *   - link_preview.php (aperçu Open Graph)
 */

require_once('db.php');
require_once('session.php');

session_start();
$user = current_user();

/* ---------- Config LinkedIn ---------- */
$linkedin_1_url  = 'https://www.linkedin.com/in/valentio-pinto-1372531b6/';
$linkedin_2_url  = 'https://www.linkedin.com/in/lucas-huon-4003b025a/';
$linkedin_1_name = 'Ton LinkedIn';
$linkedin_2_name = 'Collègue';

/* ---------- CSRF ---------- */
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf'];

/* ---------- Badges (démo) ---------- */
if (!isset($badges)) {
  $badges = [
    ['user'=>'Alice','badge'=>'Maître du JavaScript','date'=>'2025-08-01'],
    ['user'=>'Bob','badge'=>'Explorateur SQL','date'=>'2025-08-03'],
    ['user'=>'Claire','badge'=>'Débuggeur Pro','date'=>'2025-08-05'],
  ];
}

/* ===========================================================
   === Endpoints AJAX legacy (fetch/send) — Conservés ========
   =========================================================== */
function json_response($data, $code = 200) {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

$action = $_GET['action'] ?? null;
if ($action === 'chat_fetch') {
  $since_id = isset($_GET['since_id']) ? (int)$_GET['since_id'] : 0;
  $limit = isset($_GET['limit']) ? max(1, min(120, (int)$_GET['limit'])) : 60;

  try {
    if ($since_id > 0) {
      $stmt = $pdo->prepare("
        SELECT m.id, m.username, m.message, m.created_at, m.parent_id, m.is_deleted, m.edited_at, m.attachment_url,
               COALESCE(u.avatar_url, '') AS avatar_url
        FROM chat_messages m
        LEFT JOIN users u ON u.id = m.user_id
        WHERE m.id > :sid
        ORDER BY m.id ASC
        LIMIT :lim
      ");
      $stmt->bindValue(':sid', $since_id, PDO::PARAM_INT);
      $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      json_response(['ok'=>true, 'messages'=>$rows]);
    } else {
      $stmt = $pdo->prepare("
        SELECT m.id, m.username, m.message, m.created_at, m.parent_id, m.is_deleted, m.edited_at, m.attachment_url,
               COALESCE(u.avatar_url, '') AS avatar_url
        FROM chat_messages m
        LEFT JOIN users u ON u.id = m.user_id
        ORDER BY m.id DESC
        LIMIT :lim
      ");
      $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $rows = array_reverse($rows);
      json_response(['ok'=>true, 'messages'=>$rows]);
    }
  } catch (Throwable $e) {
    json_response(['ok'=>false, 'error'=>'Erreur fetch messages'], 500);
  }
}

if ($action === 'chat_send') {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok'=>false, 'error'=>'Méthode invalide'], 405);
  }
  if (!$user) {
    json_response(['ok'=>false, 'error'=>'Authentification requise'], 401);
  }
  $token = $_POST['csrf'] ?? '';
  if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
    json_response(['ok'=>false, 'error'=>'CSRF invalide'], 403);
  }
  $now = time();
  if (!empty($_SESSION['last_chat_post']) && ($now - $_SESSION['last_chat_post']) < 2) {
    json_response(['ok'=>false, 'error'=>'Trop rapide, doucement 🙂'], 429);
  }

  $msg = trim($_POST['message'] ?? '');
  if ($msg === '' || mb_strlen($msg) > 1000) {
    json_response(['ok'=>false, 'error'=>'Message vide ou trop long'], 422);
  }

  $username = $user['username'] ?? ('User'.($user['id'] ?? ''));
  $msg = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $msg);

  try {
    $stmt = $pdo->prepare("INSERT INTO chat_messages(user_id, username, message) VALUES(:uid, :un, :msg)");
    $stmt->execute([
      ':uid' => $user['id'] ?? null,
      ':un'  => $username,
      ':msg' => $msg
    ]);
    $_SESSION['last_chat_post'] = $now;
    json_response(['ok'=>true, 'id'=>$pdo->lastInsertId()]);
  } catch (Throwable $e) {
    json_response(['ok'=>false, 'error'=>'Erreur envoi message'], 500);
  }
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Accueil - FunCodeLab</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <meta name="color-scheme" content="light dark">
  <style>
    :root{
      --accent:#007BFF; --accent-2:#FFA500; --accent-3:#FFC107;
      --text:#101926; --text-muted:#4a5568;
      --bg-grad-1:#2185ff; --bg-grad-2:#ffb347;
      --surface:#ffffff; --surface-2:#f5f7fb; --border:#e5e9f2;
      --navbar:#ffffff; --navbar-shadow:0 2px 8px rgba(0,0,0,.08);
      --card-shadow:0 20px 30px rgba(0,0,0,.08), 0 0 0 1px rgba(0,0,0,.05);
      --badge-bg:#FFC107;
      --fs-h1: clamp(2.0rem, 1.2rem + 3vw, 2.8rem);
      --fs-h2: clamp(1.2rem, 0.9rem + 1.2vw, 1.8rem);
      --fs-body: clamp(0.98rem, 0.92rem + .3vw, 1.05rem);
      --menu-bg: color-mix(in oklab, var(--surface) 98%, transparent);
      --menu-border: var(--border);
    }
    html[data-theme="dark"]{
      --accent:#5aa1ff; --accent-2:#ffb02e; --accent-3:#ffd54d;
      --text:#eef2f7; --text-muted:#b4bfd0;
      --bg-grad-1:#0f172a; --bg-grad-2:#1f2937;
      --surface:#0f1624; --surface-2:#131c2c; --border:#253145;
      --navbar:#0d1422; --navbar-shadow:0 2px 8px rgba(0,0,0,.55);
      --card-shadow:0 20px 30px rgba(0,0,0,.45), 0 0 0 1px rgba(255,255,255,.04);
      --badge-bg:#b38600;
      --menu-bg: color-mix(in oklab, var(--surface-2) 92%, transparent);
      --menu-border: var(--border);
    }

    *{margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans',sans-serif;}
    body{min-height:100vh; background:linear-gradient(135deg,var(--bg-grad-1),var(--bg-grad-2)); color:var(--text); font-size:var(--fs-body);}

    /* NAVBAR */
    .main-navbar{background:var(--navbar); box-shadow:var(--navbar-shadow); position:sticky; top:0; z-index:999;}
    .nav-content{max-width:1100px; margin:auto; padding:1rem 2rem; display:flex; justify-content:space-between; align-items:center;}
    .logo{font-size:1.6rem; font-weight:700; color:var(--accent); user-select:none;}
    .nav-links{display:flex; gap:1rem; list-style:none; align-items:center;}
    .nav-links a{text-decoration:none; color:var(--text); font-weight:600; opacity:.9; transition:opacity .2s;}
    .nav-links a:hover{opacity:1;}
    .nav-btn{background:var(--accent-2); color:#fff !important; padding:.5rem 1rem; border-radius:10px; transition:background-color .2s, color .2s;}
    .nav-btn:hover{background:var(--accent-3); color:#222 !important;}

    .theme-toggle{display:inline-flex; align-items:center; gap:.5rem; background:transparent; border:1.5px solid var(--border);
      padding:.35rem .6rem; border-radius:999px; cursor:pointer; color:var(--text); font-weight:700;}
    .theme-toggle:focus-visible{outline:3px solid var(--accent); outline-offset:3px;}
    .theme-pill{width:36px; height:20px; background:var(--surface-2); border:1px solid var(--border); border-radius:999px; position:relative;}
    .theme-knob{position:absolute; top:50%; left:2px; transform:translateY(-50%); width:16px; height:16px; border-radius:50%; background:var(--accent); transition:left .25s ease;}
    html[data-theme="dark"] .theme-knob{left:18px;}

    /* AVATAR */
    .avatar{width:28px; height:28px; border-radius:50%; background:var(--accent); color:#fff; font-weight:800; display:inline-flex; align-items:center; justify-content:center; font-size:.9rem; text-transform:uppercase;}
    .avatar-img{width:28px; height:28px; border-radius:50%; object-fit:cover; display:block;}

    .nav-user{position:relative}
    .user-btn{display:flex; align-items:center; gap:.6rem; padding:.35rem .6rem; border:1.5px solid var(--border); border-radius:999px; background:var(--surface-2); cursor:pointer}
    .user-name{font-weight:800; font-size:.95rem; color:var(--text)}
    .dropdown{position:absolute; right:0; top:calc(100% + 12px); min-width:190px; background:var(--surface); border:1px solid var(--border);
      border-radius:12px; box-shadow:0 16px 40px rgba(0,0,0,.25); padding:.4rem; display:none;}
    .dropdown.show{display:block}
    .dropdown a{display:flex; align-items:center; gap:.6rem; padding:.55rem .7rem; border-radius:8px; color:var(--text); text-decoration:none; font-weight:700}
    .dropdown a:hover{background:color-mix(in srgb, var(--surface-2) 80%, transparent)}
    .dropdown .sep{height:1px; background:var(--border); margin:.35rem 0}

    /* CONTENU */
    .main-content-wrapper{display:flex; justify-content:center; align-items:flex-start; padding:5rem 1.5rem;}
    .home-container{background:var(--surface); max-width:960px; width:100%; border-radius:20px; box-shadow:var(--card-shadow); padding:3rem; animation:fadeInScale .8s ease forwards; will-change: transform;}
    @keyframes fadeInScale{0%{opacity:0; transform:scale(.97);} 100%{opacity:1; transform:scale(1);}}
    .home-header{text-align:center; margin-bottom:2.5rem;}
    .home-header h1{font-size:var(--fs-h1); font-weight:800; letter-spacing:.2px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); -webkit-background-clip:text; -webkit-text-fill-color:transparent;}
    .home-header p{font-size:1.05em; margin-top:.5rem; color:var(--text-muted);}
    .btn-cta{display:inline-block; margin-top:2rem; background:var(--accent-2); color:#fff; padding:.9rem 2rem; font-size:1.1rem; font-weight:800; border:none; border-radius:12px; text-decoration:none; box-shadow:0 8px 18px rgba(0,0,0,.15); transition:background-color .2s, box-shadow .2s, color .2s, transform .15s ease;}
    .btn-cta:hover{background:var(--accent-3); color:#222; box-shadow:0 10px 25px rgba(0,0,0,.2); transform:translateY(-1px);}
    section{margin-top:3rem;}
    h2.section-title{font-size:var(--fs-h2); margin-bottom:1.5rem; color:var(--text); text-align:center;}
    .badge-grid{display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1.5rem;}
    .badge-card{background:var(--badge-bg); color:#1f1f1f; padding:1.2rem; border-radius:12px; text-align:center;}
    html[data-theme="dark"] .badge-card{color:#0d0d0d;}
    .badge-card h3{font-size:1.1rem; font-weight:700; margin-bottom:.4rem;}
    .badge-card p{margin-bottom:.2rem;}
    .feature-grid{display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1.5rem;}
    .feature-card{background:var(--surface-2); border-left:5px solid var(--accent); border-radius:12px; padding:1.5rem; font-weight:600; color:var(--text);}

    /* Chat flottant */
    .chat-float { position: fixed; left: 20px; bottom: 20px; z-index: 1000; }
    .chat-toggle { display:flex; align-items:center; gap:.5rem; padding:.55rem .85rem; border-radius:999px; border:1px solid var(--border);
      background:color-mix(in oklab, var(--surface-2) 85%, transparent); backdrop-filter: blur(6px); box-shadow: 0 8px 18px rgba(0,0,0,.25); color:var(--text); font-weight:800; cursor:pointer; user-select:none;}
    .chat-toggle .dot { display:none; width:8px; height:8px; border-radius:999px; background:var(--accent-2); }
    .chat-toggle.has-unread .dot { display:inline-block; }

    .chat-panel { position:relative; width:min(92vw, 400px); height: 480px; margin-top:.6rem; border:1px solid var(--border); border-radius:16px;
      overflow:hidden; background:color-mix(in oklab, var(--surface-2) 92%, transparent); backdrop-filter: blur(8px);
      box-shadow: 0 20px 40px rgba(0,0,0,.35), 0 0 0 1px rgba(255,255,255,.03); display:none; }
    .chat-panel.open { display:block; }
    .chat-header{ padding:.75rem 1rem; background:linear-gradient(90deg, color-mix(in oklab, var(--accent) 85%, #000) , color-mix(in oklab, var(--accent-2) 75%, #000));
      color:#fff; font-weight:800; display:flex; justify-content:space-between; align-items:center; font-size:.95rem; border-bottom:1px solid rgba(0,0,0,.1);}
    .chat-body{ position:relative; display:flex; flex-direction:column; gap:.6rem; padding:.75rem .75rem 0 .75rem; height: calc(480px - 54px - 84px);
      overflow:auto; scroll-behavior:smooth; background:color-mix(in oklab, var(--surface) 90%, transparent);}
    .chat-empty{color:var(--text-muted); text-align:center; padding:1.25rem .75rem;}
    .msg{ position:relative; display:grid; grid-template-columns:auto 1fr auto; gap:.55rem; align-items:start; background:color-mix(in oklab, var(--surface-2) 85%, transparent);
      border:1px solid var(--border); border-radius:12px; padding:.5rem .6rem;}
    .msg .avatar{width:30px; height:30px; font-size:.9rem;}
    .msg .avatar-img{width:30px; height:30px; border-radius:50%; object-fit:cover; display:block;}
    .msg-user{font-weight:800; margin-bottom:.1rem; font-size:.92rem;}
    .msg-text{white-space:pre-wrap; word-wrap:break-word; font-size:.95rem;}
    .msg-time{font-size:.78rem; color:var(--text-muted); margin-left:.4rem; white-space:nowrap;}

    .msg-actions{ margin-top:.35rem; display:flex; gap:.35rem; opacity:.85 }
    .btn-link{border:none; background:transparent; padding:.15rem .35rem; border-radius:8px; cursor:pointer; font-weight:700;}
    .btn-link:hover{background:color-mix(in oklab, var(--surface-2) 70%, transparent);}

    /* Menu moderne */
    .kebab{border:none; background:transparent; width:28px; height:28px; border-radius:8px; cursor:pointer; font-size:18px; line-height:1;}
    .kebab:hover{background:color-mix(in oklab, var(--surface-2) 70%, transparent);}
    .action-menu{
      position:absolute; right:8px; top:36px; min-width:180px; background:var(--menu-bg);
      border:1px solid var(--menu-border); border-radius:12px; box-shadow:0 16px 40px rgba(0,0,0,.25); padding:.35rem;
      display:none; z-index:5;
    }
    .action-menu.show{display:block;}
    .action-menu button{
      width:100%; display:flex; align-items:center; gap:.5rem; border:none; background:transparent; padding:.55rem .7rem;
      border-radius:10px; cursor:pointer; font-weight:700; color:var(--text);
    }
    .action-menu button:hover{background:color-mix(in oklab, var(--surface-2) 80%, transparent);}
    .action-menu .danger{color:#c93737;}

    /* Zone input */
    .chat-input { display:flex; flex-wrap:wrap; gap:.6rem; padding:.68rem; border-top:1px solid var(--border); background:var(--surface); align-items:center; }
    .chat-input textarea{ flex:1; min-height:44px; max-height:44px; padding:0 12px; border:1.5px solid var(--border); border-radius:10px; background:var(--surface-2); color:var(--text); font-size:.95rem; display:flex; align-items:center; }
    .chat-input button{ height:44px; padding:0 16px; border:none; border-radius:10px; font-weight:700; font-size:.95rem; cursor:pointer; background:var(--accent); color:#fff; box-shadow:0 4px 10px rgba(0,0,0,.15); min-width:96px; }
    .chat-input button[disabled]{opacity:.6; cursor:not-allowed;}
    .chat-icon-btn{height:44px; min-width:44px; display:inline-flex; align-items:center; justify-content:center; border:1.5px solid var(--border); background:var(--surface-2); border-radius:10px; cursor:pointer; font-size:1.05rem;}
    .chat-icon-btn[aria-pressed="true"]{outline:3px solid color-mix(in oklab, var(--accent) 60%, white);}

    /* Preview pièce jointe */
    .attach-preview{display:flex; align-items:center; gap:.5rem; background:color-mix(in oklab, var(--surface-2) 85%, transparent); border:1px dashed var(--border); border-radius:10px; padding:.4rem .6rem;}
    .attach-preview img{max-height:44px; border-radius:8px;}
    .attach-remove{border:none; background:transparent; cursor:pointer; font-weight:800; padding:.25rem .4rem; border-radius:8px;}
    .attach-remove:hover{background:color-mix(in oklab, var(--surface-2) 70%, transparent);}

    /* Drag & drop hint (dans la body, pas sur le header) */
    .drop-hint{ display:none; position:absolute; inset:0; border:2px dashed var(--accent); border-radius:12px; background:rgba(0,0,0,.08);
      align-items:center; justify-content:center; font-weight:800; }
    .chat-body.dragover .drop-hint{ display:flex; }

    .link-card{border:1px solid var(--border); border-radius:10px; padding:.5rem; margin-top:.35rem; background:color-mix(in oklab, var(--surface-2) 80%, transparent);}

    @media (max-width: 600px){
      .chat-panel { width: min(96vw, 420px); height: 60vh; }
      .chat-body  { height: calc(60vh - 54px - 84px); }
    }

    @media (prefers-reduced-motion: no-preference){
      .badge-card, .feature-card, .msg { transition: transform .18s ease; }
      .badge-card:hover, .feature-card:hover, .msg:hover { transform: translateY(-2px) }
      .chat-toggle { transition: transform .16s ease, box-shadow .2s ease; }
      .chat-toggle:hover { transform: translateY(-1px); }
    }

    :where(a, button, input, textarea):focus-visible{ outline:3px solid color-mix(in oklab, var(--accent) 72%, white); outline-offset:3px; border-radius:10px; }

    /* Footer */
    .site-footer{ margin-top:3rem; text-align:center; font-size:.9rem; color:var(--text-muted); display:flex; flex-direction:column; align-items:center; gap:.5rem;}
    .socials{display:flex; gap:1rem; align-items:center; justify-content:center;}
    .socials a{display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; color:#0A66C2; text-decoration:none; border-radius:6px; background:transparent;}
    .socials a:hover{filter:brightness(1.1);}
    .socials svg{width:26px; height:26px; fill:currentColor;}
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="main-navbar" role="navigation" aria-label="Navigation principale">
  <div class="nav-content">
    <div class="logo">FunCodeLab</div>
    <ul class="nav-links" id="primaryNav">
      <li><a href="challenge/index.php">Challenge</a></li>
      <li><a href="parcours.php">Parcours</a></li>

      <!-- Toggle thème -->
      <li>
        <button id="themeToggle" class="theme-toggle" type="button" aria-pressed="false" aria-label="Basculer le thème">
          <span id="themeLabel">Clair</span>
          <span class="theme-pill" aria-hidden="true"><span class="theme-knob"></span></span>
        </button>
      </li>

      <?php if ($user): ?>
        <li class="nav-user">
          <button class="user-btn" id="userBtn" aria-haspopup="menu" aria-expanded="false">
            <?php if (!empty($user['avatar_url'])): ?>
              <img src="<?= htmlspecialchars($user['avatar_url']) ?>" alt="" class="avatar-img">
            <?php else: ?>
              <span class="avatar"><?= strtoupper(substr($user['username'],0,1)) ?></span>
            <?php endif; ?>
            <span class="user-name"><?= htmlspecialchars($user['username']) ?></span>
          </button>
          <div class="dropdown" id="userMenu" role="menu" aria-labelledby="userBtn">
            <a href="profil.php" role="menuitem">👤 Mon profil</a>
          </div>
        </li>
        <li><a href="logout.php" class="nav-btn">Se déconnecter</a></li>
      <?php else: ?>
        <li><a href="connexion.php">Connexion</a></li>
        <li><a href="inscription.php" class="nav-btn">Inscription</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<!-- CONTENU -->
<div class="main-content-wrapper">
  <div class="home-container" id="homeContainer">
    <header class="home-header">
      <h1>FunCodeLab</h1>
      <p>Apprends à coder avec plaisir ! Quiz, jeux, parcours & badges 🎓</p>
      <?php if (!$user): ?>
        <a href="connexion.php" class="btn-cta">Commencer maintenant</a>
      <?php endif; ?>
    </header>

    <section class="badges">
      <h2 class="section-title">🎖️ Derniers badges débloqués</h2>
      <div class="badge-grid">
        <?php foreach ($badges as $badge): ?>
          <div class="badge-card">
            <h3><?= htmlspecialchars($badge['user']) ?></h3>
            <p><?= htmlspecialchars($badge['badge']) ?></p>
            <span><?= htmlspecialchars($badge['date']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="features">
      <h2 class="section-title">💡 Pourquoi FunCodeLab ?</h2>
      <div class="feature-grid">
        <div class="feature-card">🎯 Quiz ludiques et adaptatifs</div>
        <div class="feature-card">👾 Mini-jeux éducatifs</div>
        <div class="feature-card">📚 Parcours par thème</div>
        <div class="feature-card">🏆 Système de badges et de classement</div>
      </div>
    </section>

    <!-- ===== Footer principal ===== -->
    <footer class="site-footer">
      <div>© FunCodeLab — Tous droits réservés. Reproduction interdite sans autorisation.</div>
      <div class="socials">
        <a href="<?= htmlspecialchars($linkedin_1_url) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= htmlspecialchars($linkedin_1_name) ?>">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.447 20.452H16.89v-5.569c0-1.327-.027-3.036-1.849-3.036-1.853 0-2.137 1.445-2.137 2.939v5.666H9.348V9h3.41v1.561h.049c.476-.9 1.637-1.849 3.37-1.849 3.604 0 4.27 2.37 4.27 5.455v6.285zM5.337 7.433a1.989 1.989 0 1 1 0-3.978 1.989 1.989 0 0 1 0 3.978zM6.896 20.452H3.779V9h3.117v11.452z"/></svg>
        </a>
        <a href="<?= htmlspecialchars($linkedin_2_url) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= htmlspecialchars($linkedin_2_name) ?>">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.447 20.452H16.89v-5.569c0-1.327-.027-3.036-1.849-3.036-1.853 0-2.137 1.445-2.137 2.939v5.666H9.348V9h3.41v1.561h.049c.476-.9 1.637-1.849 3.37-1.849 3.604 0 4.27 2.37 4.27 5.455v6.285zM5.337 7.433a1.989 1.989 0 1 1 0-3.978 1.989 1.989 0 0 1 0 3.978zM6.896 20.452H3.779V9h3.117v11.452z"/></svg>
        </a>
      </div>
    </footer>
  </div>
</div>

<!-- =================== Widget Chat flottant =================== -->
<div class="chat-float" aria-live="polite">
  <button class="chat-toggle" id="chatToggle" type="button" aria-expanded="false" aria-controls="chatPanel">
    <span class="dot" id="chatUnread" aria-hidden="true"></span>
    💬 Chat
  </button>

  <div class="chat-panel" id="chatPanel" role="dialog" aria-label="Chat global">
    <div class="chat-header">
      <span>Chat de la communauté</span>
      <small id="chatStatus" style="opacity:.95">Connecté</small>
    </div>

    <div id="chatBody" class="chat-body" aria-live="polite" aria-relevant="additions text">
      <div class="drop-hint" id="dropHint">Dépose ton image ici 📷</div>
      <div class="chat-empty" id="chatEmpty">Aucun message pour le moment. Dis bonjour 👋</div>
    </div>

    <div class="chat-input">
      <form id="chatForm" style="display:contents" enctype="multipart/form-data">
        <button id="soundBtn" type="button" class="chat-icon-btn" aria-pressed="true" title="Son des nouveaux messages">🔔</button>
        <label for="chatFile" class="chat-icon-btn" title="Joindre une image">🖼️</label>
        <input id="chatFile" name="file" type="file" accept="image/*" style="display:none">
        <div id="attachPreview" class="attach-preview" style="display:none">
          <img id="attachImg" alt="aperçu">
          <button type="button" id="attachRemove" class="attach-remove" title="Retirer">✕</button>
        </div>
        <!-- NOTE: textarea NON required -> permet image seule -->
        <textarea id="chatMessage" name="message" placeholder="<?= $user ? 'Écris ton message…' : 'Connecte-toi pour écrire un message' ?>" <?= $user ? '' : 'disabled' ?> maxlength="1000"></textarea>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" id="parentId" name="parent_id" value="">
        <input type="hidden" id="attachmentInput" name="attachment" value="">
        <button id="chatSend" type="submit" <?= $user ? '' : 'disabled' ?> disabled>Envoyer</button>
      </form>
      <audio id="notifySound" preload="auto">
        <source src="assets/notify.mp3" type="audio/mpeg">
      </audio>
    </div>
  </div>
</div>

<!-- =================== Scripts =================== -->
<script>
  (function(){
    /* ---------- Thème ---------- */
    const root = document.documentElement;
    const toggle = document.getElementById('themeToggle');
    const label  = document.getElementById('themeLabel');
    const systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const saved = localStorage.getItem('theme');
    const initial = saved || (systemPrefersDark ? 'dark' : 'light');
    setTheme(initial);
    toggle?.addEventListener('click', () => {
      const current = root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
      setTheme(current === 'dark' ? 'light' : 'dark', true);
    });
    function setTheme(mode, persist){
      root.setAttribute('data-theme', mode);
      if (label) label.textContent = (mode === 'dark') ? 'Sombre' : 'Clair';
      if (toggle) toggle.setAttribute('aria-pressed', mode === 'dark' ? 'true' : 'false');
      if (persist) localStorage.setItem('theme', mode);
    }

    /* ---------- Mini-menu utilisateur ---------- */
    const userBtn  = document.getElementById('userBtn');
    const userMenu = document.getElementById('userMenu');
    const closeUser = () => { userMenu?.classList.remove('show'); userBtn?.setAttribute('aria-expanded','false'); };
    userBtn?.addEventListener('click', (e)=>{ e.stopPropagation(); const open = userMenu.classList.toggle('show'); userBtn.setAttribute('aria-expanded', open ? 'true' : 'false'); });
    document.addEventListener('click', (e)=>{ if(userMenu && !userMenu.contains(e.target) && !userBtn.contains(e.target)) closeUser(); });
    document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeUser(); });

    /* ---------- Parallaxe légère ---------- */
    const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const home = document.getElementById('homeContainer');
    if (!prefersReduced && home){
      let targetX = 0, currentX = 0; const maxShift = 6;
      function animate(){ currentX += (targetX - currentX) * 0.1; home.style.transform = `translate3d(${currentX}px,0,0)`; requestAnimationFrame(animate); }
      animate();
      window.addEventListener('mousemove', (e)=>{ const ratio = (e.clientX / window.innerWidth) - 0.5; targetX = ratio * maxShift; }, {passive:true});
      window.addEventListener('mouseleave', ()=>{ targetX = 0; }, {passive:true});
    }

    /* =================== Chat (SSE + fallback polling) =================== */
    const chatBody   = document.getElementById('chatBody');
    const chatEmpty  = document.getElementById('chatEmpty');
    const chatForm   = document.getElementById('chatForm');
    const chatMsg    = document.getElementById('chatMessage');
    const chatSend   = document.getElementById('chatSend');
    const chatStatus = document.getElementById('chatStatus');
    const chatToggle = document.getElementById('chatToggle');
    const chatPanel  = document.getElementById('chatPanel');
    const fileInput  = document.getElementById('chatFile');
    const attachInput= document.getElementById('attachmentInput');
    const parentId   = document.getElementById('parentId');
    const notifySound= document.getElementById('notifySound');
    const soundBtn   = document.getElementById('soundBtn');

    const attachPreview = document.getElementById('attachPreview');
    const attachImg     = document.getElementById('attachImg');
    const attachRemove  = document.getElementById('attachRemove');
    const dropHint      = document.getElementById('dropHint');

    let lastId = 0;
    let firstLoad = true;
    let unreadCount = 0;
    let es = null;
    let pollTimer = null;

    // toggle son
    let soundOn = (localStorage.getItem('chat_sound') ?? '1') === '1';
    soundBtn.setAttribute('aria-pressed', soundOn ? 'true' : 'false');
    soundBtn.addEventListener('click', ()=>{
      soundOn = !soundOn;
      soundBtn.setAttribute('aria-pressed', soundOn ? 'true':'false');
      localStorage.setItem('chat_sound', soundOn ? '1' : '0');
    });

    chatToggle?.addEventListener('click', ()=>{
      const open = chatPanel.classList.toggle('open');
      chatToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open){
        unreadCount = 0;
        chatToggle.classList.remove('has-unread');
        requestAnimationFrame(()=>{ chatBody.scrollTop = chatBody.scrollHeight; });
      }
    });

    function escapeHtml(s){ return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m])); }
    function linkify(text){
      text = text.replace(/@([A-Za-z0-9_]{2,20})/g, '<a class="mention" href="profil.php?u=$1">@$1</a>');
      const urlRegex = /\b(https?:\/\/[^\s<]+)\b/g;
      return text.replace(urlRegex, '<a class="ext" href="$1" target="_blank" rel="noopener noreferrer">$1</a>');
    }
    function fmtDate(iso){ const d = new Date(iso.replace(' ','T')+'Z'); return d.toLocaleString([], {hour:'2-digit', minute:'2-digit', day:'2-digit', month:'2-digit'}); }
    function avatarFor(name, url){ if (url) return `<img class="avatar-img" src="${String(url).replace(/"/g,'&quot;')}" alt="">`; const c = (name||'?').charAt(0).toUpperCase(); return `<span class="avatar" aria-hidden="true">${c}</span>`; }

    async function buildPreviewIfAny(container){
      const a = container.querySelector('a.ext');
      if (!a) return;
      try{
        const res = await fetch('link_preview.php?url='+encodeURIComponent(a.href));
        const j = await res.json();
        if (j.ok && (j.meta.title || j.meta.image)) {
          const card = document.createElement('div');
          card.className='link-card';
          card.innerHTML = `
            ${j.meta.image ? `<img src="${j.meta.image}" alt="" style="max-width:100%; border-radius:8px; margin-top:.4rem">`:''}
            <div style="font-weight:700; margin-top:.25rem">${escapeHtml(j.meta.title||a.href)}</div>
            <div style="opacity:.8">${escapeHtml(j.meta.desc||'')}</div>
          `;
          container.appendChild(card);
        }
      }catch{}
    }

    function renderMessageHTML(m){
      return `
        ${avatarFor(m.username, m.avatar_url)}
        <div>
          <div class="msg-user">${escapeHtml(m.username)} ${m.parent_id ? '<span style="opacity:.7">↪</span>':''}</div>
          <div class="msg-text">${m.is_deleted ? '<i>message supprimé</i>' : linkify(escapeHtml(m.message || ''))}${m.edited_at?' <span style="opacity:.6">(modifié)</span>':''}</div>
          ${m.attachment_url ? `<div style="margin-top:.4rem"><img src="${m.attachment_url}" alt="" style="max-width:100%; border-radius:8px"></div>`:''}
          <div class="msg-actions">
            <button class="btn-link reply" title="Répondre">↩ Répondre</button>
            <button class="kebab more" title="Plus">⋯</button>
          </div>
          <div class="action-menu" role="menu">
            <button class="edit" role="menuitem">✏️ Éditer</button>
            <button class="delete danger" role="menuitem">🗑️ Supprimer</button>
            <button class="report" role="menuitem">🚩 Signaler</button>
          </div>
        </div>
        <div class="msg-time" aria-label="Heure du message">${fmtDate(m.created_at)}</div>
      `;
    }

    function renderListReplace(list){
      chatBody.innerHTML = '';
      if (!list || !list.length){
        chatEmpty.style.display = '';
        return;
      }
      chatEmpty.style.display = 'none';
      const frag = document.createDocumentFragment();
      for (const m of list){
        const item = document.createElement('div');
        item.className = 'msg';
        item.setAttribute('data-id', m.id);
        item.innerHTML = renderMessageHTML(m);
        frag.appendChild(item);
        buildPreviewIfAny(item.querySelector('.msg-text'));
      }
      chatBody.appendChild(frag);
      requestAnimationFrame(()=>{ chatBody.scrollTop = chatBody.scrollHeight; });
      lastId = Math.max(...list.map(r => Number(r.id) || 0), lastId);
      firstLoad = false;
    }

    function appendMessages(list){
      if (!list || !list.length) return;
      const wasAtBottom = chatBody.scrollTop + chatBody.clientHeight >= chatBody.scrollHeight - 80;
      if (chatEmpty) chatEmpty.style.display = 'none';
      const frag = document.createDocumentFragment();

      for (const m of list){
        lastId = Math.max(lastId, Number(m.id));
        const item = document.createElement('div');
        item.className = 'msg';
        item.setAttribute('data-id', m.id);
        item.innerHTML = renderMessageHTML(m);
        frag.appendChild(item);
        buildPreviewIfAny(item.querySelector('.msg-text'));
      }
      chatBody.appendChild(frag);

      if (chatPanel.classList.contains('open') && (firstLoad || wasAtBottom)) {
        chatBody.scrollTop = chatBody.scrollHeight;
      }
      if (!chatPanel.classList.contains('open')){
        unreadCount += list.length;
        if (unreadCount > 0) chatToggle.classList.add('has-unread');
        try { if (soundOn) { notifySound.currentTime = 0; notifySound.play(); } } catch(e){}
      }
      firstLoad = false;
    }

    async function loadRecentAndReplace(limit=60){
      try{
        const params = new URLSearchParams({ action:'chat_fetch', since_id: '0', limit: String(limit) });
        const res = await fetch(`?${params.toString()}`, { headers:{'Accept':'application/json'} });
        const data = await res.json();
        if (data.ok){ renderListReplace(data.messages); chatStatus.textContent='Connecté'; }
      }catch{ chatStatus.textContent='Hors ligne'; }
    }

    function startPolling(){
      if (pollTimer) return;
      pollTimer = setInterval(loadRecentAndReplace, 4000);
    }
    function stopPolling(){
      if (pollTimer){ clearInterval(pollTimer); pollTimer = null; }
    }

    function openSSE(){
      try{
        if (es) es.close();
        es = new EventSource('chat_sse.php?last_id='+lastId);
        es.addEventListener('open', ()=>{ chatStatus.textContent='Connecté'; stopPolling(); });
        es.addEventListener('messages', e=>{
          const data = JSON.parse(e.data||'{}');
          if (Array.isArray(data.messages) && data.messages.length){
            lastId = Math.max(lastId, Number(data.last_id||0));
            appendMessages(data.messages);
          }
        });
        es.addEventListener('presence', e=>{
          const {online=[]} = JSON.parse(e.data||'{}');
          chatStatus.textContent = `${online.length} en ligne`;
        });
        es.addEventListener('typing', e=>{
          const {typing=[]} = JSON.parse(e.data||'{}');
          if (typing.length) chatStatus.textContent = `✍️ ${typing.join(', ')} écrit...`;
        });
        es.onerror = ()=>{ chatStatus.textContent='Hors ligne'; startPolling(); };
      }catch{ startPolling(); }
    }

    (async function boot(){
      await loadRecentAndReplace(60);
      openSSE();
      setInterval(()=> loadRecentAndReplace(60), 30000);
    })();

    /* =================== Upload image (fiable) =================== */
    function showPreview(src){
      attachImg.src = src;
      attachPreview.style.display = '';
    }
    function clearAttachment(){
      attachPreview.style.display = 'none';
      attachImg.src = '';
      attachInput.value = '';
      fileInput.value = '';
      refreshSendState();
    }

    // Active/désactive le bouton Envoyer
    function refreshSendState(){
      const hasText = chatMsg.value.trim().length > 0;
      const hasImg  = attachInput.value.trim().length > 0;
      chatSend.disabled = !(hasText || hasImg);
    }

    // Sélection fichier -> upload immédiat puis preview
    fileInput?.addEventListener('change', async ()=>{
      if (!fileInput.files.length) return;
      const file = fileInput.files[0];
      await uploadFileAndPreview(file);
    });

    // Drag & Drop uniquement sur la zone messages (ne recouvre plus le header)
    ['dragenter','dragover'].forEach(ev=>{
      chatBody.addEventListener(ev, (e)=>{ e.preventDefault(); e.stopPropagation(); chatBody.classList.add('dragover'); }, false);
    });
    ['dragleave','drop'].forEach(ev=>{
      chatBody.addEventListener(ev, (e)=>{ e.preventDefault(); e.stopPropagation(); chatBody.classList.remove('dragover'); }, false);
    });
    chatBody.addEventListener('drop', async (e)=>{
      const file = e.dataTransfer.files?.[0];
      if (file && file.type.startsWith('image/')) await uploadFileAndPreview(file);
    });

    // Paste depuis presse-papier
    document.addEventListener('paste', async (e)=>{
      const item = [...(e.clipboardData?.items||[])].find(i=>i.type.startsWith('image/'));
      if (item){
        const file = item.getAsFile();
        if (file) await uploadFileAndPreview(file);
      }
    });

    attachRemove?.addEventListener('click', clearAttachment);

    async function uploadFileAndPreview(file){
      try{
        const fd = new FormData();
        fd.append('action','upload');
        fd.append('csrf', chatForm.csrf.value);
        fd.append('file', file);
        const r = await fetch('chat_actions.php', {method:'POST', body:fd});
        const j = await r.json();
        if (j.ok && j.url){
          attachInput.value = j.url;
          const reader = new FileReader();
          reader.onload = () => showPreview(reader.result);
          reader.readAsDataURL(file);
          refreshSendState();
        } else {
          alert(j.error || 'Upload refusé');
        }
      }catch(err){
        alert('Upload échoué');
      }
    }

    /* =================== Envoi =================== */
    chatMsg.addEventListener('input', refreshSendState);

    chatForm?.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const hasText = chatMsg.value.trim().length > 0;
      const hasImg  = attachInput.value.trim().length > 0;
      if (!hasText && !hasImg) return; // rien à envoyer

      // rendu optimiste
      const optimistic = {
        id: 'tmp_'+Date.now(),
        username: <?= json_encode($user['username'] ?? 'Moi') ?>,
        message: chatMsg.value,
        created_at: new Date().toISOString().slice(0,19).replace('T',' '),
        parent_id: parentId.value || null,
        is_deleted: 0,
        edited_at: null,
        attachment_url: attachInput.value || '',
        avatar_url: <?= json_encode($user['avatar_url'] ?? '') ?>
      };
      appendMessages([optimistic]);

      chatSend.disabled = true;
      try{
        const fd = new FormData(chatForm);
        fd.append('action','send');
        const res = await fetch('chat_actions.php', {method:'POST', body:fd});
        const j = await res.json();
        if (!j.ok) { alert(j.error||'Erreur'); }
      } catch{ alert('Impossible d\'envoyer le message'); }
      finally {
        chatMsg.value = '';
        parentId.value = '';
        clearAttachment();
        await loadRecentAndReplace(60);
        chatMsg.focus();
        refreshSendState();
      }
    });

    // Typing
    let typingTimer=null;
    chatMsg?.addEventListener('input', ()=>{
      clearTimeout(typingTimer);
      const qs = new URLSearchParams({action:'typing', csrf:chatForm.csrf.value});
      fetch('chat_actions.php', {method:'POST', body:qs}).catch(()=>{});
      typingTimer=setTimeout(()=>{},1200);
    });

    /* =================== Actions (répondre / menu moderne) =================== */
    function closeAllMenus(){
      document.querySelectorAll('.action-menu.show').forEach(el => el.classList.remove('show'));
    }

    chatBody?.addEventListener('click', async (e)=>{
      const msgEl = e.target.closest('.msg');
      if (!msgEl) return;
      const id = msgEl.dataset.id;

      if (e.target.closest('.reply')) {
        document.getElementById('parentId').value = id;
        chatMsg.focus();
        return;
      }

      if (e.target.closest('.more')) {
        const menu = msgEl.querySelector('.action-menu');
        const open = !menu.classList.contains('show');
        closeAllMenus();
        if (open) menu.classList.add('show');
        return;
      }

      if (e.target.closest('.edit')) {
        const cur = msgEl.querySelector('.msg-text')?.textContent.trim() || '';
        const nv = prompt('Modifier le message :', cur);
        if (nv!=null) {
          try{
            const fd = new URLSearchParams({action:'edit', id, message:nv, csrf:chatForm.csrf.value});
            const r = await fetch('chat_actions.php', {method:'POST', body:fd}); const j=await r.json();
            if (!j.ok) alert(j.error||'Édition refusée');
          }catch{}
          await loadRecentAndReplace(60);
        }
        closeAllMenus();
        return;
      }

      if (e.target.closest('.delete')) {
        if (!confirm('Supprimer ce message ?')) { closeAllMenus(); return; }
        try{
          const fd = new URLSearchParams({action:'delete', id, csrf:chatForm.csrf.value});
          const r = await fetch('chat_actions.php', {method:'POST', body:fd}); const j=await r.json();
          if (!j.ok) alert(j.error||'Suppression refusée');
        }catch{}
        await loadRecentAndReplace(60);
        closeAllMenus();
        return;
      }

      if (e.target.closest('.report')) {
        const reason = prompt('Raison du signalement :')||'';
        try{
          const fd = new URLSearchParams({action:'report', message_id:id, reason, csrf:chatForm.csrf.value});
          await fetch('chat_actions.php', {method:'POST', body:fd});
        }catch{}
        alert('Signalé, merci.');
        closeAllMenus();
        return;
      }
    });

    document.addEventListener('click', (e)=>{
      if (!e.target.closest('.action-menu') && !e.target.closest('.more')) closeAllMenus();
    });
    document.addEventListener('keydown', (e)=>{ if (e.key==='Escape') closeAllMenus(); });

    // Init état bouton
    refreshSendState();
  })();
</script>
</body>
</html>
