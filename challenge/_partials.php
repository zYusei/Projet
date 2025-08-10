<?php
// challenge/_partials.php
require_once('../db.php');
require_once('../session.php');

/* ===== Debug en dev (à retirer en prod) ===== */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ===== Base URL dynamique =====
   Ex: si ton site est sous http://localhost/Projet/,
   alors $BASE_URL = '/Projet'. Sinon, '/'.
   On est dans /Projet/challenge/*. On remonte d'1 niveau. */
$BASE_URL = rtrim(dirname($_SERVER['SCRIPT_NAME'], 2), '/');
if ($BASE_URL === '') $BASE_URL = '/';
function url($path){ global $BASE_URL; return $BASE_URL . '/' . ltrim($path, '/'); }

/* ===== Helpers DB ===== */
function db_fetch_one($sql, $params=[]){ $st=db()->prepare($sql); $st->execute($params); return $st->fetch(PDO::FETCH_ASSOC); }
function db_fetch_all($sql, $params=[]){ $st=db()->prepare($sql); $st->execute($params); return $st->fetchAll(PDO::FETCH_ASSOC); }
function db_exec($sql, $params=[]){ $st=db()->prepare($sql); return $st->execute($params); }

function require_user() {
  $u = current_user();
  if (!$u) { header('Location: ' . url('connexion.php')); exit; }
  return $u;
}
function now_utc(){ return (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'); }

/* ===== Head + Navbar (même design que l’accueil) ===== */
function render_head($title='Challenges - FunCodeLab'){
  $challengesUrl = url('challenge/index.php');
  $parcoursUrl   = url('parcours.php');
  $indexUrl   = url('index.php');
  echo <<<HTML
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{$title}</title>
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
      --fs-h1: clamp(2.0rem, 1.2rem + 3vw, 2.4rem);
      --fs-h2: clamp(1.2rem, 0.9rem + 1.2vw, 1.6rem);
      --fs-body: clamp(0.98rem, 0.92rem + .3vw, 1.05rem);
    }
    html[data-theme="dark"]{
      --accent:#5aa1ff; --accent-2:#ffb02e; --accent-3:#ffd54d;
      --text:#eef2f7; --text-muted:#b4bfd0;
      --bg-grad-1:#0f172a; --bg-grad-2:#1f2937;
      --surface:#0f1624; --surface-2:#131c2c; --border:#253145;
      --navbar:#0d1422; --navbar-shadow:0 2px 8px rgba(0,0,0,.55);
      --card-shadow:0 20px 30px rgba(0,0,0,.45), 0 0 0 1px rgba(255,255,255,.04);
      --badge-bg:#b38600;
    }

    *{box-sizing:border-box; font-family:'Plus Jakarta Sans',sans-serif;}
    body{min-height:100vh; margin:0; background:linear-gradient(135deg,var(--bg-grad-1),var(--bg-grad-2)); color:var(--text); font-size:var(--fs-body);}

    /* NAVBAR */
    .main-navbar{background:var(--navbar); box-shadow:var(--navbar-shadow); position:sticky; top:0; z-index:999;}
    .nav-content{max-width:1100px; margin:auto; padding:1rem 2rem; display:flex; justify-content:space-between; align-items:center;}
    .logo{font-size:1.6rem; font-weight:700; color:var(--accent);}
    .nav-links{display:flex; gap:1rem; list-style:none; align-items:center; margin:0; padding:0}
    .nav-links a{text-decoration:none; color:var(--text); font-weight:600; opacity:.9}
    .nav-links a:hover{opacity:1}
    .nav-btn{background:var(--accent-2); color:#fff !important; padding:.5rem 1rem; border-radius:10px}
    .nav-btn:hover{background:var(--accent-3); color:#222 !important}
    .theme-toggle{display:inline-flex; align-items:center; gap:.5rem; border:1.5px solid var(--border); padding:.35rem .6rem; border-radius:999px; background:transparent; cursor:pointer; color:var(--text); font-weight:700}
    .theme-pill{width:36px; height:20px; background:var(--surface-2); border:1px solid var(--border); border-radius:999px; position:relative;}
    .theme-knob{position:absolute; top:50%; left:2px; transform:translateY(-50%); width:16px; height:16px; border-radius:50%; background:var(--accent); transition:left .25s ease;}
    html[data-theme="dark"] .theme-knob{left:18px;}
    .avatar{width:28px; height:28px; border-radius:50%; background:var(--accent); color:#fff; font-weight:800; display:inline-flex; align-items:center; justify-content:center; font-size:.9rem; text-transform:uppercase;}
    .avatar-img{width:28px; height:28px; border-radius:50%; object-fit:cover; display:block;}
    .nav-user{position:relative}
    .user-btn{display:flex; align-items:center; gap:.6rem; padding:.35rem .6rem; border:1.5px solid var(--border); border-radius:999px; background:var(--surface-2); cursor:pointer}
    .dropdown{position:absolute; right:0; top:calc(100% + 12px); min-width:190px; background:var(--surface); border:1px solid var(--border); border-radius:12px; box-shadow:0 16px 40px rgba(0,0,0,.25); padding:.4rem; display:none;}
    .dropdown.show{display:block}
    .dropdown a{display:flex; align-items:center; gap:.6rem; padding:.55rem .7rem; border-radius:8px; color:var(--text); text-decoration:none; font-weight:700}
    .dropdown .sep{height:1px; background:var(--border); margin:.35rem 0}

    /* PAGE CONTAINER */
    .container {
    max-width: 1100px;
    margin: 3rem auto 0; /* marge top augmentée à 3rem */
    padding: 2.2rem 1.4rem;
    }

    .page {
    background: var(--surface);
    border-radius: 20px;
    box-shadow: var(--card-shadow);
    padding: 5rem;
    }


    /* TITRES + SOUS-EN-TÊTE */
    h1{font-size:var(--fs-h1); margin:0 0 .6rem 0; background:linear-gradient(90deg,var(--accent),var(--accent-2)); -webkit-background-clip:text; -webkit-text-fill-color:transparent;}
    .subhead{color:var(--text-muted); margin-bottom:1.1rem}

    /* TOOLBAR (filtres + bouton classement) */
    .toolbar{display:flex; gap:.6rem; align-items:center; margin:1rem 0 1.2rem; flex-wrap:wrap}
    .toolbar select{
      flex:1; min-width:240px; background:var(--surface-2); color:var(--text);
      border:1px solid var(--border); border-radius:12px; padding:.65rem .8rem;
      outline:none;
    }
    .toolbar select:hover{border-color:color-mix(in oklab, var(--accent) 35%, var(--border));}
    .btn-ranking{
      margin-left:auto; white-space:nowrap; display:inline-flex; align-items:center; gap:.45rem;
      padding:.6rem 1rem; border-radius:12px; text-decoration:none; font-weight:800; border:0;
      background:linear-gradient(90deg,var(--accent),#88b6ff); color:#fff; box-shadow:0 10px 18px rgba(0,0,0,.12);
      transition:transform .15s ease, box-shadow .2s ease, filter .2s ease;
    }
    .btn-ranking:hover{ transform:translateY(-1px); box-shadow:0 12px 22px rgba(0,0,0,.16); filter:saturate(1.05); }

    /* CARDS */
    .cards{display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:1rem}
    .card{
      background:var(--surface-2); border:1px solid var(--border); border-radius:16px; padding:1rem;
      box-shadow:0 10px 20px rgba(0,0,0,.08);
      transition:transform .15s ease, box-shadow .2s ease, border-color .2s ease;
    }
    .card:hover{ transform:translateY(-2px); box-shadow:0 16px 30px rgba(0,0,0,.12); border-color:color-mix(in oklab, var(--accent) 35%, var(--border)); }
    .pill{
      display:inline-block; border:1px solid var(--border); border-radius:999px; padding:.18rem .6rem;
      margin-right:.35rem; font-weight:700; font-size:.9rem; color:var(--text);
      background:color-mix(in srgb, var(--surface) 75%, transparent);
    }
    .btn{
      display:inline-block; border:0; background:linear-gradient(90deg,var(--accent),#88b6ff); color:#fff;
      padding:.6rem 1rem; border-radius:12px; font-weight:800; text-decoration:none; cursor:pointer;
      transition:transform .15s ease, box-shadow .2s ease, filter .2s ease;
    }
    .btn:hover{ transform:translateY(-1px); box-shadow:0 10px 18px rgba(0,0,0,.14); filter:saturate(1.05); }

    input,textarea,select{background:#0f1530; color:#fff; border:1px solid #253145; border-radius:10px; padding:.65rem}
    .timer{font-weight:800; border:1px solid var(--border); border-radius:999px; padding:.25rem .7rem; display:inline-block}
    .muted{color:var(--text-muted)}

    :where(a, button, input, textarea, select):focus-visible{
      outline:3px solid color-mix(in oklab, var(--accent) 72%, white);
      outline-offset:3px; border-radius:10px;
    }
  </style>
</head>
<body>
<nav class="main-navbar" role="navigation" aria-label="Navigation principale">
  <div class="nav-content">
    <div class="logo">FunCodeLab</div>
    <ul class="nav-links" id="primaryNav">
        <li><a href="{$indexUrl}">Accueil</a></li>
      <li><a href="{$challengesUrl}">Challenges</a></li>
      <li><a href="{$parcoursUrl}">Parcours</a></li>
      <li>
        <button id="themeToggle" class="theme-toggle" type="button" aria-pressed="false" aria-label="Basculer le thème">
          <span id="themeLabel">Clair</span>
          <span class="theme-pill" aria-hidden="true"><span class="theme-knob"></span></span>
        </button>
      </li>
HTML;
  $u = current_user();
  if ($u){
    $initial = strtoupper(substr($u['username'],0,1));
    $name    = htmlspecialchars($u['username']);
    $profil  = url('profil.php');
    $logout  = url('logout.php');

    // URL absolu de l’avatar (si présent)
    $avatarUrl = '';
    if (!empty($u['avatar_url'])) {
      $avatarUrl = htmlspecialchars(url($u['avatar_url']));
    }

    echo <<<HTML
      <li class="nav-user">
        <button class="user-btn" id="userBtn" aria-haspopup="menu" aria-expanded="false">
HTML;
    if ($avatarUrl) {
      echo '<img src="'.$avatarUrl.'" alt="" class="avatar-img">';
    } else {
      echo '<span class="avatar">'.$initial.'</span>';
    }
    echo <<<HTML
          <span class="user-name">{$name}</span>
        </button>
        <div class="dropdown" id="userMenu" role="menu" aria-labelledby="userBtn">
          <a href="{$profil}" role="menuitem">👤 Mon profil</a>
        </div>
      </li>
HTML;
  } else {
    $login  = url('connexion.php');
    $signup = url('inscription.php');
    echo '<li><a href="'.$login.'">Connexion</a></li><li><a href="'.$signup.'" class="nav-btn">Inscription</a></li>';
  }
  echo <<<HTML
    </ul>
  </div>
</nav>
<div class="container"><div class="page">
HTML;
}

function render_footer(){
  echo <<<HTML
</div></div>
<footer style="text-align:center;color:var(--text-muted);padding:1.2rem 0">© 2025 FunCodeLab — Apprendre en s'amusant</footer>
<script>
(function(){
  // Thème (identique à l’accueil)
  const root=document.documentElement, toggle=document.getElementById('themeToggle'), label=document.getElementById('themeLabel');
  const sysDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  const saved = localStorage.getItem('theme'); const initial = saved || (sysDark?'dark':'light'); setTheme(initial);
  toggle?.addEventListener('click',()=>{ const cur=root.getAttribute('data-theme')==='dark'?'dark':'light'; setTheme(cur==='dark'?'light':'dark',true); });
  function setTheme(mode,persist){ root.setAttribute('data-theme',mode); if(label) label.textContent=(mode==='dark')?'Sombre':'Clair'; if(toggle) toggle.setAttribute('aria-pressed', mode==='dark'?'true':'false'); if(persist) localStorage.setItem('theme',mode); }

  // Menu utilisateur
  const userBtn=document.getElementById('userBtn'), userMenu=document.getElementById('userMenu');
  const close=()=>{ userMenu?.classList.remove('show'); userBtn?.setAttribute('aria-expanded','false'); };
  userBtn?.addEventListener('click',e=>{ e.stopPropagation(); const open=userMenu.classList.toggle('show'); userBtn.setAttribute('aria-expanded', open?'true':'false'); });
  document.addEventListener('click',e=>{ if(userMenu && !userMenu.contains(e.target) && !userBtn.contains(e.target)) close(); });
  document.addEventListener('keydown',e=>{ if(e.key==='Escape') close(); });
})();
</script>
</body></html>
HTML;
}
