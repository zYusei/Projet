<?php
require_once('db.php');
require_once('session.php');
$user = current_user();

// ----- Démo : catalogue des parcours (tu peux ajuster/ajouter) -----
$catalog = [
  'programmation' => [
    // HTML (Facile)
    ['id'=>'prog_html_intro','title'=>'HTML — Structure','diff'=>'easy','icon'=>'📄','desc'=>'Balises, titres, paragraphes'],
    ['id'=>'prog_html_links_forms','title'=>'HTML — Liens & formulaires','diff'=>'easy','icon'=>'🔗','desc'=>'Liens, images, inputs de base'],

    // JavaScript (Facile → Intermédiaire)
    ['id'=>'prog_js_fundamentals','title'=>'JavaScript — Bases','diff'=>'easy','icon'=>'🟨','desc'=>'Variables, types, opérateurs'],
    ['id'=>'prog_js_dom_events','title'=>'JavaScript — DOM & événements','diff'=>'intermediate','icon'=>'🧩','desc'=>'Sélectionner, écouter, manipuler'],

    // PHP (Facile → Intermédiaire)
    ['id'=>'prog_php_intro','title'=>'PHP — Syntaxe & variables','diff'=>'easy','icon'=>'🐘','desc'=>'echo, variables, tableaux'],
    ['id'=>'prog_php_forms','title'=>'PHP — Formulaires & $_POST','diff'=>'intermediate','icon'=>'📮','desc'=>'Validation et traitement sécurisé'],

    // Python (Facile → Intermédiaire → Difficile)
    ['id'=>'prog_python_intro','title'=>'Python — Premiers pas','diff'=>'easy','icon'=>'🐍','desc'=>'Print, variables, types simples'],
    ['id'=>'prog_python_data','title'=>'Python — Listes & boucles','diff'=>'intermediate','icon'=>'📚','desc'=>'for, while, compréhensions'],
    ['id'=>'prog_python_advanced','title'=>'Python — Avancé','diff'=>'hard','icon'=>'🧠','desc'=>'Décorateurs, générateurs, gestion mémoire'],

    // C++ (Intermédiaire → Difficile)
    ['id'=>'prog_cpp_intro','title'=>'C++ — Bases','diff'=>'intermediate','icon'=>'💠','desc'=>'Types, iostream, boucles'],
    ['id'=>'prog_cpp_oop','title'=>'C++ — Programmation objet','diff'=>'hard','icon'=>'🏗️','desc'=>'Classes, héritage, polymorphisme'],
  ],

  // ===== Colonne du milieu -> Débutant : sécurité au quotidien (inchangée) =====
  'pentest' => [
    ['id'=>'b_sec_phishing_simple','title'=>'Le phishing (tout simple)','diff'=>'easy','icon'=>'🎣','desc'=>'Reconnaître un faux mail en 3 indices'],
    ['id'=>'b_fake_sites','title'=>'Vrai site ou faux ?','diff'=>'easy','icon'=>'🔍','desc'=>'URL, certificat, signaux visuels'],
    ['id'=>'b_passwords','title'=>'Mots de passe & manager','diff'=>'easy','icon'=>'🔐','desc'=>'Passphrase, coffre-fort, règles'],
    ['id'=>'b_2fa','title'=>'Activer 2FA partout','diff'=>'easy','icon'=>'🛡️','desc'=>'App d’authentification, codes de secours'],
    ['id'=>'b_updates','title'=>'Mises à jour & correctifs','diff'=>'easy','icon'=>'⬆️','desc'=>'OS, navigateur, extensions'],
    ['id'=>'b_downloads','title'=>'Pièces jointes & téléchargements','diff'=>'easy','icon'=>'📥','desc'=>'Vérifier avant d’ouvrir'],
    ['id'=>'b_wifi','title'=>'Wi-Fi public en sécurité','diff'=>'intermediate','icon'=>'📶','desc'=>'Partage, VPN, HTTPS'],
    ['id'=>'b_privacy','title'=>'Confidentialité & permissions','diff'=>'easy','icon'=>'🔎','desc'=>'Cookies, applis, traqueurs'],
  ],

  // ===== Cybersécurité : homogénéisée (10 items, crescendo) =====
  'cyber' => [
    // Facile
    ['id'=>'sec_basics','title'=>'Security Engineer','diff'=>'easy','icon'=>'🏗️','desc'=>'Principes & bonnes pratiques'],
    ['id'=>'sec_password_hygiene','title'=>'Hygiène des mots de passe','diff'=>'easy','icon'=>'🔐','desc'=>'Politiques, stockage, MFA'],
    ['id'=>'sec_network_basics','title'=>'Réseau sécurisé','diff'=>'easy','icon'=>'🌐','desc'=>'HTTPS, VPN, segmentation'],
    ['id'=>'sec_phishing_defense','title'=>'Défense anti-phishing','diff'=>'easy','icon'=>'🎣','desc'=>'Filtrage, formation, réponses'],

    // Intermédiaire
    ['id'=>'sec_devsecops','title'=>'DevSecOps','diff'=>'intermediate','icon'=>'∞','desc'=>'CI/CD, SAST/DAST, secrets'],
    ['id'=>'sec_cloud_aws','title'=>'Attacking & Defending AWS','diff'=>'intermediate','icon'=>'☁️','desc'=>'IAM, S3, attack paths'],
    ['id'=>'sec_threat_hunting','title'=>'Threat Hunting','diff'=>'intermediate','icon'=>'🕵️‍♂️','desc'=>'Hypothèses, IoC, télémétrie'],
    ['id'=>'sec_vuln_mgmt','title'=>'Gestion des vulnérabilités','diff'=>'intermediate','icon'=>'🧩','desc'=>'Scan, priorisation, patching'],

    // Difficile
    ['id'=>'sec_aei','title'=>'Endpoint Investigations','diff'=>'hard','icon'=>'🧊','desc'=>'DFIR, artefacts, timeline'],
    ['id'=>'sec_red_team','title'=>'Red Team Operations','diff'=>'hard','icon'=>'🎯','desc'=>'Intrusion, persistence, évasion'],
  ],
];

// ----- Progression utilisateur (essai BDD, sinon valeurs de démo) -----
$progress = [];
if ($user) {
  try {
    if (isset($pdo)) { $db = $pdo; }
    elseif (isset($conn) && $conn instanceof PDO) { $db = $conn; }
    else { $db = null; }
    if ($db) {
      $st = $db->prepare("SELECT path_id, percent FROM user_progress WHERE user_id = :uid");
      $st->execute([':uid'=>$user['id']]);
      foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $progress[$row['path_id']] = max(0, min(100, (int)$row['percent']));
      }
    }
  } catch (Throwable $e) { /* silencieux -> démo */ }
}
if (!$progress && $user) {
  $progress = ['prog_js_basics'=>35, 'pt_jr'=>12, 'sec_devsecops'=>60];
}
function path_progress($id, $progress) {
  return isset($progress[$id]) ? (int)$progress[$id] : 0;
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Parcours - FunCodeLab</title>
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
      --easy:#16a34a; --inter:#f59e0b; --hard:#ef4444;
    }
    html[data-theme="dark"]{
      --accent:#5aa1ff; --accent-2:#ffb02e; --accent-3:#ffd54d;
      --text:#eef2f7; --text-muted:#b4bfd0;
      --bg-grad-1:#0f172a; --bg-grad-2:#1f2937;
      --surface:#0f1624; --surface-2:#131c2c; --border:#253145;
      --navbar:#0d1422; --navbar-shadow:0 2px 8px rgba(0,0,0,.55);
      --card-shadow:0 20px 30px rgba(0,0,0,.45), 0 0 0 1px rgba(255,255,255,.04);
      --badge-bg:#b38600;
      --easy:#22c55e; --inter:#fbbf24; --hard:#f87171;
    }

    /* >>> Améliore le contraste en mode clair */
    html[data-theme="light"]{
      --text:#0b1220;        /* titres & texte principal plus foncés */
      --text-muted:#2f3a4a;  /* sous-texte plus foncé */
    }

    *{margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans',sans-serif;}
    body{min-height:100vh;background:linear-gradient(135deg,var(--bg-grad-1),var(--bg-grad-2));color:var(--text);font-size:var(--fs-body);}

    .main-navbar{background:var(--navbar); box-shadow:var(--navbar-shadow); position:sticky; top:0; z-index:999;}
    .nav-content{max-width:1100px; margin:auto; padding:1rem 2rem; display:flex; justify-content:space-between; align-items:center;}
    .logo{font-size:1.6rem; font-weight:700; color:var(--accent); user-select:none;}
    .nav-links{display:flex; gap:1rem; list-style:none; align-items:center;}
    .nav-links a{text-decoration:none; color:var(--text); font-weight:600; opacity:.9; transition:opacity .2s;}
    .nav-links a:hover{opacity:1;}
    .nav-btn{background:var(--accent-2); color:#fff !important; padding:.5rem 1rem; border-radius:10px;}
    .nav-btn:hover{background:var(--accent-3); color:#222 !important;}
    .theme-toggle{display:inline-flex; align-items:center; gap:.5rem; background:transparent; border:1.5px solid var(--border); padding:.35rem .6rem; border-radius:999px; cursor:pointer; color:var(--text); font-weight:700;}
    .theme-pill{width:36px; height:20px; background:var(--surface-2); border:1px solid var(--border); border-radius:999px; position:relative;}
    .theme-knob{position:absolute; top:50%; left:2px; transform:translateY(-50%); width:16px; height:16px; border-radius:50%; background:var(--accent); transition:left .25s ease}
    html[data-theme="dark"] .theme-knob{left:18px;}

    .avatar{width:28px;height:28px;border-radius:50%;background:var(--accent);color:#fff;font-weight:800;display:inline-flex;align-items:center;justify-content:center;font-size:.9rem;text-transform:uppercase;}
    .avatar-img{width:28px;height:28px;border-radius:50%;object-fit:cover;display:block;}
    .nav-user{position:relative}
    .user-btn{display:flex;align-items:center;gap:.6rem;padding:.35rem .6rem;border:1.5px solid var(--border);border-radius:999px;background:var(--surface-2);cursor:pointer}
    .user-name{font-weight:800;font-size:.95rem;color:var(--text)}
    .dropdown{position:absolute;right:0;top:calc(100% + 12px);min-width:190px;background:var(--surface);border:1px solid var(--border);border-radius:12px;box-shadow:0 16px 40px rgba(0,0,0,.25);padding:.4rem;display:none}
    .dropdown.show{display:block}
    .dropdown a{display:flex;align-items:center;gap:.6rem;padding:.55rem .7rem;border-radius:8px;color:var(--text);text-decoration:none;font-weight:700}
    .dropdown a:hover{background:color-mix(in srgb, var(--surface-2) 80%, transparent)}

    .wrap{max-width:1100px;margin:0 auto;padding:1rem 1.5rem;}
    .hero{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin:1rem 0 1.2rem;}
    .hero h1{
      font-size:var(--fs-h1);font-weight:800;
      background:linear-gradient(90deg,var(--accent),var(--accent-2));
      -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    }
    html[data-theme="light"] .hero h1{
      background:linear-gradient(90deg,#0a47ff,#005de6);
      -webkit-background-clip:text;-webkit-text-fill-color:transparent;
      text-shadow:0 1px 0 rgba(0,0,0,.08);
    }
    .hero p{color:var(--text-muted)}

    .cols{display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-top:.6rem}
    @media (max-width:980px){.cols{grid-template-columns:1fr}}
    .col{background:var(--surface);border:1px solid var(--border);border-radius:16px;box-shadow:var(--card-shadow);overflow:hidden}
    .col-head{padding:1rem 1.1rem;background:var(--surface-2);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
    .col-head h2{font-size:1.1rem;font-weight:800}
    .col-body{padding:1rem}

    /* lien qui entoure la carte */
    .path-link{display:block; text-decoration:none; color:inherit;}
    .path{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:.9rem;display:grid;grid-template-columns:auto 1fr auto;gap:.75rem;align-items:center;margin-bottom:.8rem; transition: transform .15s ease, box-shadow .15s ease;}
    .path:hover{ transform: scale(1.02); box-shadow:0 10px 20px rgba(0,0,0,.08); }
    .ico{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;background:var(--surface-2)}
    .path h3{font-size:1rem;margin-bottom:.15rem}
    .path p{font-size:.9rem;color:var(--text-muted); margin-bottom:.10rem;}

    .diff{
      font-weight:800;font-size:.8rem;border-radius:999px;padding:.2rem .5rem;
      display:inline-flex;align-items:center;gap:.35rem;
      margin-top:.35rem;
    }
    .diff.easy{background:color-mix(in srgb, var(--easy) 18%, transparent); color:var(--easy); border:1px solid color-mix(in srgb, var(--easy) 45%, transparent);}
    .diff.inter{background:color-mix(in srgb, var(--inter) 18%, transparent); color:var(--inter); border:1px solid color-mix(in srgb, var(--inter) 45%, transparent);}
    .diff.hard{background:color-mix(in srgb, var(--hard) 18%, transparent); color:var(--hard); border:1px solid color-mix(in srgb, var(--hard) 45%, transparent);}

    /* ===== Cercle de progression (SVG affiné + contrasté) ===== */
    .progress{display:flex; align-items:center; justify-content:flex-end;}
    .cprog{ --size:52px; --stroke:4.5; --pct:0; width:var(--size); height:var(--size); position:relative; }
    html[data-theme="light"] .cprog{ --track:#d4d9e0; --ring:#ffffff; }
    html[data-theme="dark"]  .cprog{ --track:#1f2433; --ring:#0f1624; }
    .cprog svg{width:100%; height:100%; display:block;}
    .cprog circle{fill:none; stroke-width:var(--stroke);}
    .cprog .bg{ stroke: var(--track); }
    .cprog .fg{
      stroke: var(--arc-url);
      stroke-linecap: round;
      pathLength: 100;
      stroke-dasharray: var(--pct) 100;
      transform: rotate(-90deg);
      transform-origin: 50% 50%;
      filter: drop-shadow(0 1px 1px rgba(0,0,0,.15));
      transition: stroke-dasharray .45s ease;
    }
    .cprog::after{
      content:"";
      position:absolute; inset:calc(var(--stroke) * 1.6);
      border-radius:50%;
      background:var(--ring);
      border:1px solid color-mix(in srgb, var(--track) 60%, transparent);
    }
    .cprog-label{ position:absolute; inset:0; display:grid; place-items:center; font-weight:800; font-size:.85rem; color:var(--text); user-select:none; }

    /* CTA “Voir mon profil” : texte bleu comme le logo */
    .cta{margin-top:.6rem}
    .cta a{
      display:inline-block;text-decoration:none;font-weight:800;
      padding:.45rem .75rem;border-radius:10px;border:1px solid var(--border);
      background:var(--surface-2); color:var(--accent);
    }
    .cta a:hover{border-color:var(--accent); background:color-mix(in srgb, var(--surface-2) 70%, transparent);}
  </style>
</head>
<body>

<nav class="main-navbar" role="navigation" aria-label="Navigation principale">
  <div class="nav-content">
    <div class="logo">FunCodeLab</div>
    <ul class="nav-links">
      <li><a href="index.php">Accueil</a></li>
      <li><a href="parcours.php">Parcours</a></li>
      <li><a href="#">Quiz</a></li>
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

<div class="wrap">
  <div class="hero">
    <div>
      <h1>Parcours</h1>
      <p>Choisis ta voie et suis ta progression. Facile → Intermédiaire → Difficile.</p>
    </div>
    <?php if($user): ?>
      <div class="cta"><a href="profil.php">Voir mon profil</a></div>
    <?php endif; ?>
  </div>

  <div class="cols">
    <!-- Programmation -->
    <section class="col">
      <div class="col-head"><h2>Programmation</h2><span class="diff easy">Facile → Difficile</span></div>
      <div class="col-body">
        <?php foreach ($catalog['programmation'] as $idx => $p): $pct = path_progress($p['id'],$progress); $gid='grad_'.preg_replace('/[^a-z0-9_]+/i','_',$p['id']); $target = 'programmation/'.($idx+1).'.php'; ?>
          <a class="path-link" href="<?= htmlspecialchars($target) ?>" aria-label="<?= htmlspecialchars($p['title']) ?>">
            <article class="path" data-id="<?= htmlspecialchars($p['id']) ?>">
              <div class="ico"><?= $p['icon'] ?></div>
              <div>
                <h3><?= htmlspecialchars($p['title']) ?></h3>
                <p><?= htmlspecialchars($p['desc']) ?></p>
                <span class="diff <?= $p['diff']==='hard'?'hard':($p['diff']==='intermediate'?'inter':($p['diff']==='inter'?'inter':'easy')) ?>">
                  <?= $p['diff']==='hard'?'Difficile':($p['diff']==='intermediate'?'Intermédiaire':($p['diff']==='inter'?'Intermédiaire':'Facile')) ?>
                </span>
              </div>
              <div class="progress" aria-label="Progression <?= $pct ?>%">
                <div class="cprog" style="--pct: <?= $pct ?>; --arc-url: url(#<?= $gid ?>);">
                  <svg viewBox="0 0 36 36" aria-hidden="true" focusable="false">
                    <defs>
                      <linearGradient id="<?= $gid ?>" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%"   stop-color="#0b75ff"/>
                        <stop offset="100%" stop-color="#6eb3ff"/>
                      </linearGradient>
                    </defs>
                    <circle class="bg" cx="18" cy="18" r="15.915"></circle>
                    <circle class="fg" cx="18" cy="18" r="15.915"></circle>
                  </svg>
                  <div class="cprog-label"><?= $pct ?>%</div>
                </div>
              </div>
            </article>
          </a>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Débutant : sécurité au quotidien -->
    <section class="col">
      <div class="col-head"><h2>Débutant</h2><span class="diff easy">Essentiels</span></div>
      <div class="col-body">
        <?php foreach ($catalog['pentest'] as $idx => $p): $pct = path_progress($p['id'],$progress); $gid='grad_'.preg_replace('/[^a-z0-9_]+/i','_',$p['id']); $target = 'debutant/'.($idx+1).'.php'; ?>
          <a class="path-link" href="<?= htmlspecialchars($target) ?>" aria-label="<?= htmlspecialchars($p['title']) ?>">
            <article class="path" data-id="<?= htmlspecialchars($p['id']) ?>">
              <div class="ico"><?= $p['icon'] ?></div>
              <div>
                <h3><?= htmlspecialchars($p['title']) ?></h3>
                <p><?= htmlspecialchars($p['desc']) ?></p>
                <span class="diff <?= $p['diff']==='hard'?'hard':($p['diff']==='intermediate'?'inter':($p['diff']==='inter'?'inter':'easy')) ?>">
                  <?= $p['diff']==='hard'?'Difficile':($p['diff']==='intermediate'?'Intermédiaire':($p['diff']==='inter'?'Intermédiaire':'Facile')) ?>
                </span>
              </div>
              <div class="progress" aria-label="Progression <?= $pct ?>%">
                <div class="cprog" style="--pct: <?= $pct ?>; --arc-url: url(#<?= $gid ?>);">
                  <svg viewBox="0 0 36 36" aria-hidden="true" focusable="false">
                    <defs>
                      <linearGradient id="<?= $gid ?>" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%"   stop-color="#0b75ff"/>
                        <stop offset="100%" stop-color="#6eb3ff"/>
                      </linearGradient>
                    </defs>
                    <circle class="bg" cx="18" cy="18" r="15.915"></circle>
                    <circle class="fg" cx="18" cy="18" r="15.915"></circle>
                  </svg>
                  <div class="cprog-label"><?= $pct ?>%</div>
                </div>
              </div>
            </article>
          </a>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Cybersécurité -->
    <section class="col">
      <div class="col-head"><h2>Cybersécurité</h2><span class="diff hard">Facile → Difficile</span></div>
      <div class="col-body">
        <?php foreach ($catalog['cyber'] as $idx => $p): $pct = path_progress($p['id'],$progress); $gid='grad_'.preg_replace('/[^a-z0-9_]+/i','_',$p['id']); $target = 'cyber/'.($idx+1).'.php'; ?>
          <a class="path-link" href="<?= htmlspecialchars($target) ?>" aria-label="<?= htmlspecialchars($p['title']) ?>">
            <article class="path" data-id="<?= htmlspecialchars($p['id']) ?>">
              <div class="ico"><?= $p['icon'] ?></div>
              <div>
                <h3><?= htmlspecialchars($p['title']) ?></h3>
                <p><?= htmlspecialchars($p['desc']) ?></p>
                <span class="diff <?= $p['diff']==='hard'?'hard':($p['diff']==='intermediate'?'inter':($p['diff']==='inter'?'inter':'easy')) ?>">
                  <?= $p['diff']==='hard'?'Difficile':($p['diff']==='intermediate'?'Intermédiaire':($p['diff']==='inter'?'Intermédiaire':'Facile')) ?>
                </span>
              </div>
              <div class="progress" aria-label="Progression <?= $pct ?>%">
                <div class="cprog" style="--pct: <?= $pct ?>; --arc-url: url(#<?= $gid ?>);">
                  <svg viewBox="0 0 36 36" aria-hidden="true" focusable="false">
                    <defs>
                      <linearGradient id="<?= $gid ?>" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%"   stop-color="#0b75ff"/>
                        <stop offset="100%" stop-color="#6eb3ff"/>
                      </linearGradient>
                    </defs>
                    <circle class="bg" cx="18" cy="18" r="15.915"></circle>
                    <circle class="fg" cx="18" cy="18" r="15.915"></circle>
                  </svg>
                  <div class="cprog-label"><?= $pct ?>%</div>
                </div>
              </div>
            </article>
          </a>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</div>

<script>
  (function(){
    const root=document.documentElement, toggle=document.getElementById('themeToggle'), label=document.getElementById('themeLabel');
    const prefersDark=window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const saved=localStorage.getItem('theme'); setTheme(saved || (prefersDark?'dark':'light'));
    toggle?.addEventListener('click',()=>{const mode=root.getAttribute('data-theme')==='dark'?'light':'dark'; setTheme(mode,true);});
    function setTheme(mode,persist){ root.setAttribute('data-theme',mode); if(label) label.textContent=(mode==='dark')?'Sombre':'Clair'; toggle?.setAttribute('aria-pressed', mode==='dark'?'true':'false'); if(persist) localStorage.setItem('theme',mode); }
    const userBtn=document.getElementById('userBtn'), userMenu=document.getElementById('userMenu');
    const closeUser=()=>{userMenu?.classList.remove('show'); userBtn?.setAttribute('aria-expanded','false');};
    userBtn?.addEventListener('click',e=>{e.stopPropagation(); const open=userMenu.classList.toggle('show'); userBtn.setAttribute('aria-expanded',open?'true':'false');});
    document.addEventListener('click',e=>{ if(userMenu && !userMenu.contains(e.target) && !userBtn.contains(e.target)) closeUser(); });
    document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeUser(); });
  })();
</script>
</body>
</html>
