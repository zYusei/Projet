<?php
// Affiche le formulaire d'inscription
require_once __DIR__.'/session.php';
$base  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Inscription • FunCodeLab</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet" />
  <style>
    :root{
      --accent:#007BFF; --accent-2:#FFA500; --accent-3:#FFC107;
      --text:#101926; --text-muted:#4a5568;
      --bg-grad-1:#2185ff; --bg-grad-2:#ffb347;
      --surface:#ffffff; --surface-2:#ffffff; --input-border:#e0e4ea;
      --navbar:#ffffff; --navbar-shadow:0 2px 8px rgba(0,0,0,.08);
      --danger:#dc3545; --warn:#ffc107; --ok:#28a745;
    }
    html[data-theme="dark"]{
      --accent:#5aa1ff; --accent-2:#ffb02e; --accent-3:#ffd54d;
      --text:#eef2f7; --text-muted:#b4bfd0;
      --bg-grad-1:#0f172a; --bg-grad-2:#1f2937;
      --surface:#121826; --surface-2:#162032; --input-border:#2b364b;
      --navbar:#0f1624; --navbar-shadow:0 2px 8px rgba(0,0,0,.5);
      --danger:#ff6b6b; --warn:#ffd166; --ok:#5bd49f;
    }
    *{ box-sizing:border-box; margin:0; padding:0; font-family:'Plus Jakarta Sans',sans-serif; }
    body{ min-height:100vh; background:linear-gradient(135deg,var(--bg-grad-1),var(--bg-grad-2)); color:var(--text); display:flex; flex-direction:column; align-items:center; }
    .main-navbar{ background:var(--navbar); box-shadow:var(--navbar-shadow); position:fixed; top:0; left:0; right:0; z-index:9; }
    .nav-content{ max-width:1100px; margin:auto; padding:1rem 2rem; display:flex; justify-content:space-between; align-items:center; }
    .logo{ font-size:1.6rem; font-weight:700; color:var(--accent); }
    .nav-links{ display:flex; gap:1rem; list-style:none; align-items:center; }
    .nav-links a{ text-decoration:none; color:var(--text); font-weight:600; opacity:.9; }
    .nav-links a:hover{ opacity:1; }
    .nav-btn{ background:var(--accent-2); padding:.5rem 1rem; border-radius:10px; color:#fff !important; }
    .nav-btn:hover{ background:var(--accent-3); color:#222 !important; }
    .theme-toggle{ display:inline-flex;align-items:center;gap:.5rem;background:transparent;border:1.5px solid var(--input-border);
      padding:.35rem .6rem;border-radius:999px;cursor:pointer;color:var(--text);font-weight:700;}
    .theme-toggle:focus-visible{outline:3px solid var(--accent);outline-offset:3px}
    .theme-pill{width:36px;height:20px;background:var(--surface-2);border:1px solid var(--input-border);border-radius:999px;position:relative}
    .theme-knob{position:absolute;top:50%;left:2px;transform:translateY(-50%);width:16px;height:16px;border-radius:50%;background:var(--accent);transition:left .25s ease}
    html[data-theme="dark"] .theme-knob{left:18px}
    .login-container{ background:var(--surface); color:var(--text); border-radius:20px; box-shadow:0 20px 30px rgba(0,0,0,.12),0 0 0 1px rgba(0,0,0,.05);
      width:100%; max-width:420px; padding:3rem 3.5rem; margin-top:8rem; animation:fadeInScale .7s ease forwards; }
    @keyframes fadeInScale{ 0%{opacity:0;transform:scale(.96);} 100%{opacity:1;transform:scale(1);} }
    .login-header{ text-align:center; margin-bottom:2.2rem; }
    .login-header svg{ width:50px; height:50px; margin-bottom:.5rem; fill:var(--accent-2); filter:drop-shadow(0 0 2px rgba(0,0,0,.1)); }
    .login-header h1{ font-weight:800; font-size:2rem; color:var(--accent); letter-spacing:1px; }
    form{ display:flex; flex-direction:column; gap:1.2rem; }
    label{ font-weight:700; font-size:.95rem; margin-bottom:.35rem; color:var(--text); display:block; }
    .input-wrapper{ position:relative; width:100%; }
    .input-wrapper svg{ position:absolute; left:18px; top:53%; transform:translateY(-50%); width:22px; height:22px; fill:var(--accent-3); pointer-events:none; transition:fill .3s; }
    .input-wrapper svg.icon-password{ top:40%; }
    input[type="text"],input[type="email"],input[type="password"]{
      width:100%; padding:.85rem 1rem .85rem 48px; border:2px solid var(--input-border); border-radius:12px;
      font-size:1rem; background:var(--surface-2); color:var(--text); transition:border-color .3s, box-shadow .3s;
    }
    input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus{
      border-color:var(--accent); box-shadow:0 0 0 3px color-mix(in srgb, var(--accent) 25%, transparent);
      outline:none;
    }
    input[type="text"]:focus + svg, input[type="email"]:focus + svg, input[type="password"]:focus + svg{ fill:var(--accent); }
    .error-message{ font-size:.85rem; color:var(--danger); margin-top:4px; font-weight:600; min-height:1.2em; }
    .btn-submit{ background:var(--accent-2); color:#fff; font-weight:800; padding:.9rem 0; border:none; border-radius:14px; cursor:pointer; font-size:1.1rem;
      box-shadow:0 8px 15px rgba(0,0,0,.15); transition:background-color .2s, box-shadow .2s, transform .08s ease-in; }
    .btn-submit:hover,.btn-submit:focus-visible{ background:var(--accent-3); color:#222; box-shadow:0 10px 20px rgba(0,0,0,.2); outline:none; }
    .btn-submit:active{ transform:translateY(1px); }
    .muted-link{ display:block; text-align:right; font-size:.9rem; color:var(--accent); font-weight:700; text-decoration:underline; background:none; border:none; padding:0; margin-top:.35rem; cursor:pointer; }
    .muted-link:hover,.muted-link:focus{ color:var(--accent-2); text-decoration:none; outline:none; }
    #passwordStrengthWrapper{ margin-top:6px; }
    #passwordStrengthBar{ height:8px; width:100%; border-radius:6px; background-color:#ddd; transition:background-color .3s; }
    #passwordStrengthText{ margin-top:4px; font-size:.85rem; font-weight:700; color:var(--text-muted); }
  </style>
</head>
<body>
<nav class="main-navbar">
  <div class="nav-content">
    <div class="logo">FunCodeLab</div>
    <ul class="nav-links">
      <li><a href="index.php">Accueil</a></li>
      <li>
        <button id="themeToggle" class="theme-toggle" type="button" aria-pressed="false" aria-label="Basculer le thème">
          <span id="themeLabel">Clair</span>
          <span class="theme-pill" aria-hidden="true"><span class="theme-knob"></span></span>
        </button>
      </li>
      <li><a href="connexion.php" class="nav-btn">Connexion</a></li>
    </ul>
  </div>
</nav>

<main class="login-container" role="main" aria-label="Formulaire d'inscription FunCodeLab">
  <header class="login-header">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" aria-hidden="true"><path d="M32 4L12 60h40L32 4z"/></svg>
    <h1>Inscription</h1>
  </header>

  <form id="registerForm" method="POST" action="<?= htmlspecialchars($base) ?>/register.php" autocomplete="off" novalidate>
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
    <div class="input-wrapper">
      <label for="username">Nom d’utilisateur</label>
      <input type="text" id="username" name="username" placeholder="Votre pseudo" aria-describedby="usernameError" required />
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
      <div id="usernameError" class="error-message" aria-live="polite"></div>
    </div>

    <div class="input-wrapper">
      <label for="email">Adresse e-mail</label>
      <input type="email" id="email" name="email" placeholder="exemple@funcodelab.com" aria-describedby="emailError" required />
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
      <div id="emailError" class="error-message" aria-live="polite"></div>
    </div>

    <div class="input-wrapper">
      <label for="password">Mot de passe</label>
      <input type="password" id="password" name="password" placeholder="Votre mot de passe" aria-describedby="passwordError" required />
      <svg class="icon-password" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17a2 2 0 100-4 2 2 0 000 4zm6-6v-3a6 6 0 00-12 0v3H4v10h16V11h-2zm-8-3a4 4 0 018 0v3H10v-3z"/></svg>
      <div id="passwordError" class="error-message" aria-live="polite"></div>

      <div id="passwordStrengthWrapper">
        <div id="passwordStrengthBar"></div>
        <div id="passwordStrengthText">Force du mot de passe</div>
      </div>
    </div>

    <button type="submit" class="btn-submit">S’inscrire</button>
    <button type="button" class="muted-link" onclick="location.href='<?= htmlspecialchars($base) ?>/connexion.php'">Déjà un compte ? Connectez-vous</button>
  </form>
</main>

<script>
  (function(){
    const root = document.documentElement;
    const toggle = document.getElementById('themeToggle');
    const label  = document.getElementById('themeLabel');
    const systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('theme');
    const initial = savedTheme || (systemPrefersDark ? 'dark' : 'light');
    setTheme(initial);
    toggle.addEventListener('click', () => {
      const current = root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
      const next = current === 'dark' ? 'light' : 'dark';
      setTheme(next, true);
    });
    function setTheme(mode, persist){
      root.setAttribute('data-theme', mode);
      label.textContent = (mode === 'dark') ? 'Sombre' : 'Clair';
      toggle.setAttribute('aria-pressed', mode === 'dark' ? 'true' : 'false');
      if (persist) localStorage.setItem('theme', mode);
    }
  })();

  // Validation + jauge
  const form = document.getElementById('registerForm');
  const usernameInput = form.username, emailInput = form.email, passwordInput = form.password;
  const usernameError = document.getElementById('usernameError'), emailError = document.getElementById('emailError'), passwordError = document.getElementById('passwordError');
  const passwordStrengthBar = document.getElementById('passwordStrengthBar'), passwordStrengthText = document.getElementById('passwordStrengthText');
  form.addEventListener('submit', function (e) {
    let valid = true;
    usernameError.textContent = emailError.textContent = passwordError.textContent = '';
    if (!usernameInput.value.trim()) { usernameError.textContent = 'Le nom d’utilisateur est obligatoire.'; valid = false; }
    if (!emailInput.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) { emailError.textContent = 'Veuillez saisir une adresse e-mail valide.'; valid = false; }
    if (!passwordInput.value.trim()) { passwordError.textContent = 'Le mot de passe est obligatoire.'; valid = false; }
    if (!valid) e.preventDefault();
  });
  [usernameInput, emailInput, passwordInput].forEach(i => i.addEventListener('input', () => {
    if (i===usernameInput) usernameError.textContent = '';
    if (i===emailInput) emailError.textContent = '';
    if (i===passwordInput) passwordError.textContent = '';
  }));
  passwordInput.addEventListener('input', () => {
    const v = passwordInput.value; let s = 0;
    if (!v) { passwordStrengthBar.style.backgroundColor='#ddd'; passwordStrengthText.textContent='Force du mot de passe'; passwordStrengthText.style.color='var(--text-muted)'; return; }
    if (v.length>=8) s++; if (/[A-Z]/.test(v)) s++; if (/[0-9]/.test(v)) s++; if (/[\W_]/.test(v)) s++;
    if (s<=2){ passwordStrengthBar.style.backgroundColor='var(--danger)'; passwordStrengthText.textContent='Faible'; passwordStrengthText.style.color='var(--danger)'; }
    else if (s===3){ passwordStrengthBar.style.backgroundColor='var(--warn)'; passwordStrengthText.textContent='Moyen'; passwordStrengthText.style.color='var(--warn)'; }
    else { passwordStrengthBar.style.backgroundColor='var(--ok)'; passwordStrengthText.textContent='Fort'; passwordStrengthText.style.color='var(--ok)'; }
  });
</script>
</body>
</html>
