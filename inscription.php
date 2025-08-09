<?php
// inscription.php — affiche uniquement le formulaire
// (le POST est traité par register.php)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Inscription FunCodeLab</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    :root{
      --bleu-vif:#007BFF; --orange-vif:#FFA500; --jaune-moutarde:#FFC107;
      --vert-clair:#28A745; --gris-clair:#F5F5F5; --gris-fonce:#333333;
      --blanc:#FFFFFF; --input-bg:#f9f9f9; --input-border:#ddd; --shadow-light:rgba(0,0,0,0.08);
    }
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Plus Jakarta Sans',sans-serif;}
    body{min-height:100vh;background:linear-gradient(135deg,var(--bleu-vif),var(--orange-vif));display:flex;flex-direction:column;align-items:center;}
    .main-navbar{background:var(--blanc);box-shadow:0 2px 8px rgba(0,0,0,.08);position:fixed;top:0;left:0;right:0;z-index:999;}
    .nav-content{max-width:1100px;margin:auto;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center;}
    .logo{font-size:1.6rem;font-weight:700;color:var(--bleu-vif);user-select:none;}
    .nav-links{display:flex;gap:1.5rem;list-style:none;}
    .nav-links a{text-decoration:none;color:var(--gris-fonce);font-weight:600;transition:color .3s;}
    .nav-links a:hover{color:var(--orange-vif);}
    .nav-btn{background:var(--orange-vif);padding:.5rem 1rem;border-radius:8px;color:#fff !important;transition:background-color .3s;}
    .nav-btn:hover{background:var(--jaune-moutarde);color:var(--gris-fonce) !important;}
    .nav-btn:focus{background:var(--jaune-moutarde);color:var(--gris-fonce);outline:none;}
    .login-container{background:var(--blanc);border-radius:20px;box-shadow:0 20px 30px var(--shadow-light),0 0 0 1px rgba(0,0,0,.05);width:100%;max-width:420px;padding:3rem 3.5rem;margin-top:8rem;animation:fadeInScale .7s ease forwards;}
    @keyframes fadeInScale{0%{opacity:0;transform:scale(.95);}100%{opacity:1;transform:scale(1);}}
    .login-header{text-align:center;margin-bottom:2.5rem;}
    .login-header svg{width:50px;height:50px;margin-bottom:.5rem;fill:var(--orange-vif);filter:drop-shadow(0 0 2px rgba(255,165,0,.7));animation:bounce 2.5s infinite ease-in-out;}
    @keyframes bounce{0%,100%{transform:translateY(0);}50%{transform:translateY(-8px);}}
    .login-header h1{font-weight:700;font-size:2.4rem;color:var(--bleu-vif);letter-spacing:2px;user-select:none;}
    form{display:flex;flex-direction:column;gap:1.8rem;}
    label{font-weight:600;font-size:.95rem;margin-bottom:.4rem;color:var(--gris-fonce);display:block;user-select:none;}
    .input-wrapper{position:relative;width:100%;}
    .input-wrapper svg{position:absolute;left:18px;top:53%;transform:translateY(-50%);width:24px;height:24px;fill:var(--jaune-moutarde);pointer-events:none;transition:fill .3s;}
    .input-wrapper svg.icon-password{top:40%;}
    input[type="text"],input[type="email"],input[type="password"]{width:100%;padding:.85rem 1rem .85rem 45px;border:2.5px solid var(--input-border);border-radius:12px;font-size:1rem;background:var(--input-bg);color:var(--gris-fonce);transition:border-color .4s, box-shadow .4s;outline-offset:3px;}
    input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus{border-color:var(--bleu-vif);box-shadow:0 0 8px var(--bleu-vif);background:#e9f0ff;}
    input[type="text"]:focus + svg, input[type="email"]:focus + svg, input[type="password"]:focus + svg{fill:var(--bleu-vif);}
    .error-message{font-size:.85rem;color:#dc3545;margin-top:4px;font-weight:600;user-select:none;min-height:1.2em;}
    .btn-submit{position:relative;overflow:hidden;background:var(--orange-vif);color:#fff;font-weight:700;padding:.85rem 0;border:none;border-radius:14px;cursor:pointer;font-size:1.25rem;box-shadow:0 8px 15px rgba(255,165,0,.4);transition:background-color .35s, box-shadow .35s;user-select:none;}
    .btn-submit:hover,.btn-submit:focus-visible{background:var(--jaune-moutarde);color:var(--gris-fonce);box-shadow:0 10px 20px rgba(255,193,7,.6);outline:none;}
    .btn-submit:focus-visible{outline:3px solid var(--bleu-vif);outline-offset:4px;}
    .btn-submit::after{content:"";position:absolute;width:100px;height:100px;background:rgba(255,255,255,.4);border-radius:50%;pointer-events:none;transform:translate(-50%,-50%) scale(0);opacity:0;transition:transform .5s, opacity 1s;top:50%;left:50%;}
    .btn-submit:active::after{transform:translate(-50%,-50%) scale(1);opacity:1;transition:0s;}
    .muted-link{display:block;text-align:right;font-size:.9rem;color:var(--bleu-vif);font-weight:600;text-decoration:underline;background:none;border:none;padding:0;margin-top:.25rem;cursor:pointer;}
    .muted-link:hover,.muted-link:focus{color:var(--orange-vif);text-decoration:none;outline:none;}
    .success-message{color:var(--vert-clair);margin-top:1.4rem;font-weight:700;text-align:center;font-size:1.05rem;user-select:none;animation:fadeIn .5s ease forwards;}
    @keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
  </style>
</head>
<body>
<nav class="main-navbar">
  <div class="nav-content">
    <div class="logo">FunCodeLab</div>
    <ul class="nav-links">
      <li><a href="index.php">Accueil</a></li>
      <li><a href="connexion.php" class="nav-btn">Connexion</a></li>
    </ul>
  </div>
</nav>

<main class="login-container" role="main" aria-label="Formulaire d'inscription FunCodeLab">
  <header class="login-header">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" aria-hidden="true"><path d="M32 4L12 60h40L32 4z"/></svg>
    <h1>Inscription</h1>
  </header>

  <!-- IMPORTANT: on envoie vers register.php -->
  <form id="registerForm" method="POST" action="register.php" autocomplete="off" novalidate>
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

      <div id="passwordStrengthWrapper" style="margin-top:6px;">
        <div id="passwordStrengthBar" style="height:8px;width:100%;border-radius:6px;background-color:#ddd;transition:background-color .3s;"></div>
        <div id="passwordStrengthText" style="margin-top:4px;font-size:.85rem;font-weight:600;user-select:none;color:#333;">Force du mot de passe</div>
      </div>
    </div>

    <button type="submit" class="btn-submit" aria-label="Créer mon compte FunCodeLab">S’inscrire</button>
    <button type="button" class="muted-link" onclick="location.href='connexion.php'">Déjà un compte ? Connectez-vous</button>
  </form>

  <div id="successMessage" class="success-message" role="alert" style="display:none;">Inscription réussie !</div>
</main>

<script>
  // JS inchangé (vérifs côté client)
  const form = document.getElementById('registerForm');
  const successMessage = document.getElementById('successMessage');
  const usernameInput = form.username, emailInput = form.email, passwordInput = form.password;
  const usernameError = document.getElementById('usernameError'), emailError = document.getElementById('emailError'), passwordError = document.getElementById('passwordError');

  form.addEventListener('submit', function (e) {
    // On laisse POST partir vers register.php, mais on fait une validation rapide
    let valid = true;
    usernameError.textContent = emailError.textContent = passwordError.textContent = '';
    successMessage.style.display = 'none';

    if (!usernameInput.value.trim()) { usernameError.textContent = 'Le nom d’utilisateur est obligatoire.'; valid = false; }
    if (!emailInput.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) { emailError.textContent = 'Veuillez saisir une adresse e-mail valide.'; valid = false; }
    if (!passwordInput.value.trim()) { passwordError.textContent = 'Le mot de passe est obligatoire.'; valid = false; }

    if (!valid) e.preventDefault(); // bloque l'envoi si erreurs côté client
  });

  [usernameInput, emailInput, passwordInput].forEach(i => i.addEventListener('input', () => {
    if (i===usernameInput) usernameError.textContent = '';
    if (i===emailInput) emailError.textContent = '';
    if (i===passwordInput) passwordError.textContent = '';
    successMessage.style.display = 'none';
  }));

  const passwordStrengthBar = document.getElementById('passwordStrengthBar');
  const passwordStrengthText = document.getElementById('passwordStrengthText');
  passwordInput.addEventListener('input', () => {
    const v = passwordInput.value; let s = 0;
    if (!v) { passwordStrengthBar.style.backgroundColor='#ddd'; passwordStrengthText.textContent='Force du mot de passe'; passwordStrengthText.style.color='#333'; return; }
    if (v.length>=8) s++; if (/[A-Z]/.test(v)) s++; if (/[0-9]/.test(v)) s++; if (/[\W_]/.test(v)) s++;
    if (s<=2){ passwordStrengthBar.style.backgroundColor='#dc3545'; passwordStrengthText.textContent='Faible'; passwordStrengthText.style.color='#dc3545'; }
    else if (s===3){ passwordStrengthBar.style.backgroundColor='#ffc107'; passwordStrengthText.textContent='Moyen'; passwordStrengthText.style.color='#856404'; }
    else { passwordStrengthBar.style.backgroundColor='#28a745'; passwordStrengthText.textContent='Fort'; passwordStrengthText.style.color='#155724'; }
  });
</script>
</body>
</html>
