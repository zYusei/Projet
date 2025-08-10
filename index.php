<?php
require_once('db.php');
require_once('session.php');
$user = current_user();

// Badges de démo si non fournis
if (!isset($badges)) {
  $badges = [
    ['user'=>'Alice','badge'=>'Maître du JavaScript','date'=>'2025-08-01'],
    ['user'=>'Bob','badge'=>'Explorateur SQL','date'=>'2025-08-03'],
    ['user'=>'Claire','badge'=>'Débuggeur Pro','date'=>'2025-08-05'],
  ];
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
    /* =================== Thèmes =================== */
    :root{
      --accent:#007BFF; --accent-2:#FFA500; --accent-3:#FFC107;
      --text:#101926; --text-muted:#4a5568;

      --bg-grad-1:#2185ff; --bg-grad-2:#ffb347;

      --surface:#ffffff;
      --surface-2:#f5f7fb;
      --border:#e5e9f2;

      --navbar:#ffffff;
      --navbar-shadow:0 2px 8px rgba(0,0,0,.08);

      --card-shadow:0 20px 30px rgba(0,0,0,.08), 0 0 0 1px rgba(0,0,0,.05);

      --badge-bg:#FFC107;

      --fs-h1: clamp(2.0rem, 1.2rem + 3vw, 2.8rem);
      --fs-h2: clamp(1.2rem, 0.9rem + 1.2vw, 1.8rem);
      --fs-body: clamp(0.98rem, 0.92rem + .3vw, 1.05rem);
    }
    html[data-theme="dark"]{
      --accent:#5aa1ff; --accent-2:#ffb02e; --accent-3:#ffd54d;
      --text:#eef2f7; --text-muted:#b4bfd0;

      --bg-grad-1:#0f172a; --bg-grad-2:#1f2937;

      --surface:#0f1624;
      --surface-2:#131c2c;
      --border:#253145;

      --navbar:#0d1422;
      --navbar-shadow:0 2px 8px rgba(0,0,0,.55);

      --card-shadow:0 20px 30px rgba(0,0,0,.45), 0 0 0 1px rgba(255,255,255,.04);

      --badge-bg:#b38600;
    }

    *{margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans',sans-serif;}
    body{
      min-height:100vh;
      background:linear-gradient(135deg,var(--bg-grad-1),var(--bg-grad-2));
      color:var(--text);
      font-size:var(--fs-body);
    }

    /* =================== NAVBAR + toggle =================== */
    .main-navbar{background:var(--navbar); box-shadow:var(--navbar-shadow); position:sticky; top:0; z-index:999;}
    .nav-content{max-width:1100px; margin:auto; padding:1rem 2rem; display:flex; justify-content:space-between; align-items:center;}
    .logo{font-size:1.6rem; font-weight:700; color:var(--accent); user-select:none;}
    .nav-links{display:flex; gap:1rem; list-style:none; align-items:center;}
    .nav-links a{text-decoration:none; color:var(--text); font-weight:600; opacity:.9; transition:opacity .2s;}
    .nav-links a:hover{opacity:1;}
    .nav-btn{
      background:var(--accent-2); color:#fff !important; padding:.5rem 1rem; border-radius:10px; transition:background-color .2s, color .2s;
    }
    .nav-btn:hover{background:var(--accent-3); color:#222 !important;}

    /* Toggle thème */
    .theme-toggle{
      display:inline-flex; align-items:center; gap:.5rem; background:transparent; border:1.5px solid var(--border);
      padding:.35rem .6rem; border-radius:999px; cursor:pointer; color:var(--text); font-weight:700;
    }
    .theme-toggle:focus-visible{outline:3px solid var(--accent); outline-offset:3px;}
    .theme-pill{width:36px; height:20px; background:var(--surface-2); border:1px solid var(--border); border-radius:999px; position:relative;}
    .theme-knob{position:absolute; top:50%; left:2px; transform:translateY(-50%); width:16px; height:16px; border-radius:50%; background:var(--accent); transition:left .25s ease;}
    html[data-theme="dark"] .theme-knob{left:18px;}

    /* =================== AVATAR + MENU UTILISATEUR =================== */
    .avatar{width:28px; height:28px; border-radius:50%; background:var(--accent); color:#fff; font-weight:800;
            display:inline-flex; align-items:center; justify-content:center; font-size:.9rem; text-transform:uppercase;}
    .avatar-img{width:28px; height:28px; border-radius:50%; object-fit:cover; display:block;}

    .nav-user{position:relative}
    .user-btn{display:flex; align-items:center; gap:.6rem; padding:.35rem .6rem; border:1.5px solid var(--border);
              border-radius:999px; background:var(--surface-2); cursor:pointer}
    .user-name{font-weight:800; font-size:.95rem; color:var(--text)}
    .dropdown{
      position:absolute; right:0; top:calc(100% + 12px);
      min-width:190px; background:var(--surface); border:1px solid var(--border);
      border-radius:12px; box-shadow:0 16px 40px rgba(0,0,0,.25); padding:.4rem; display:none;
    }
    .dropdown.show{display:block}
    .dropdown a{display:flex; align-items:center; gap:.6rem; padding:.55rem .7rem; border-radius:8px; color:var(--text); text-decoration:none; font-weight:700}
    .dropdown a:hover{background:color-mix(in srgb, var(--surface-2) 80%, transparent)}
    .dropdown .sep{height:1px; background:var(--border); margin:.35rem 0}

    /* =================== CONTENU =================== */
   .main-content-wrapper {
    display:flex;
    justify-content:center;
    align-items:flex-start;
    padding:5rem 1.5rem; /* plus aucun espace en haut */
}
    .home-container{
      background:var(--surface);
      max-width:960px; width:100%; border-radius:20px;
      box-shadow:var(--card-shadow); padding:3rem;
      animation:fadeInScale .8s ease forwards;
      will-change: transform;
    }
    @keyframes fadeInScale{0%{opacity:0; transform:scale(.97);} 100%{opacity:1; transform:scale(1);}}

    .home-header{text-align:center; margin-bottom:2.5rem;}
    .home-header h1{
      font-size:var(--fs-h1); font-weight:800; letter-spacing:.2px;
      background:linear-gradient(90deg,var(--accent),var(--accent-2));
      -webkit-background-clip:text; -webkit-text-fill-color:transparent;
    }
    .home-header p{font-size:1.05em; margin-top:.5rem; color:var(--text-muted);}

    .btn-cta{
      display:inline-block; margin-top:2rem; background:var(--accent-2); color:#fff; padding:.9rem 2rem;
      font-size:1.1rem; font-weight:800; border:none; border-radius:12px; text-decoration:none;
      box-shadow:0 8px 18px rgba(0,0,0,.15); transition:background-color .2s, box-shadow .2s, color .2s, transform .15s ease;
    }
    .btn-cta:hover{background:var(--accent-3); color:#222; box-shadow:0 10px 25px rgba(0,0,0,.2); transform:translateY(-1px);}

    section{margin-top:3rem;}
    h2.section-title{font-size:var(--fs-h2); margin-bottom:1.5rem; color:var(--text); text-align:center;}

    .badge-grid{display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1.5rem;}
    .badge-card{
      background:var(--badge-bg); color:#1f1f1f;
      padding:1.2rem; border-radius:12px; text-align:center;
    }
    html[data-theme="dark"] .badge-card{color:#0d0d0d;}
    .badge-card h3{font-size:1.1rem; font-weight:700; margin-bottom:.4rem;}
    .badge-card p{margin-bottom:.2rem;}

    .feature-grid{display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1.5rem;}
    .feature-card{
      background:var(--surface-2); border-left:5px solid var(--accent); border-radius:12px; padding:1.5rem; font-weight:600; color:var(--text);
    }

    @media (prefers-reduced-motion: no-preference){
      .badge-card, .feature-card { transition: transform .18s ease; }
      .badge-card:hover, .feature-card:hover { transform: translateY(-2px) scale(1.01); }
      .btn-cta      { transition: background-color .2s, box-shadow .2s, color .2s, transform .18s; }
    }

    :where(a, button, input):focus-visible{
      outline:3px solid color-mix(in oklab, var(--accent) 72%, white);
      outline-offset:3px; border-radius:10px;
    }

    footer{margin-top:3rem; text-align:center; font-size:.9rem; color:var(--text-muted);}
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
        <!-- Bouton utilisateur + mini-menu -->
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

        <!-- bouton déconnexion visible en plus (comme sur ta capture) -->
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

    <footer>
      © 2025 FunCodeLab — Apprendre en s'amusant
    </footer>
  </div>
</div>

<!-- =================== Scripts : Thème + Menu utilisateur + Parallaxe =================== -->
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
      const next = current === 'dark' ? 'light' : 'dark';
      setTheme(next, true);
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

    userBtn?.addEventListener('click', (e)=>{
      e.stopPropagation();
      const open = userMenu.classList.toggle('show');
      userBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    document.addEventListener('click', (e)=>{ if(userMenu && !userMenu.contains(e.target) && !userBtn.contains(e.target)) closeUser(); });
    document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeUser(); });

    /* ---------- Parallaxe légère ---------- */
    const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const home = document.getElementById('homeContainer');
    if (!prefersReduced && home){
      let targetX = 0, currentX = 0;
      const maxShift = 6;
      function animate(){
        currentX += (targetX - currentX) * 0.1;
        home.style.transform = `translate3d(${currentX}px,0,0)`;
        requestAnimationFrame(animate);
      }
      animate();
      window.addEventListener('mousemove', (e)=>{
        const ratio = (e.clientX / window.innerWidth) - 0.5;
        targetX = ratio * maxShift;
      }, {passive:true});
      window.addEventListener('mouseleave', ()=>{ targetX = 0; }, {passive:true});
    }
  })();
</script>
</body>
</html>
