<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Connexion FunCodeLab</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bleu-vif: #007BFF;
      --orange-vif: #FFA500;
      --jaune-moutarde: #FFC107;
      --vert-clair: #28A745;
      --gris-clair: #F5F5F5;
      --gris-fonce: #333333;
      --blanc: #FFFFFF;
      --input-bg: #f9f9f9;
      --input-border: #ddd;
      --shadow-light: rgba(0,0,0,0.08);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    body {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, var(--bleu-vif), var(--orange-vif));
      padding: 2rem;
      overflow: hidden;
    }

    .login-container {
      background: var(--blanc);
      border-radius: 20px;
      box-shadow:
        0 20px 30px var(--shadow-light),
        0 0 0 1px rgba(0,0,0,0.05);
      width: 100%;
      max-width: 420px;
      padding: 3rem 3.5rem;
      position: relative;
      animation: fadeInScale 0.7s ease forwards;
    }

    @keyframes fadeInScale {
      0% {
        opacity: 0;
        transform: scale(0.95);
      }
      100% {
        opacity: 1;
        transform: scale(1);
      }
    }

    .login-header {
      text-align: center;
      margin-bottom: 2.5rem;
      position: relative;
    }

    .login-header svg {
      width: 50px;
      height: 50px;
      margin-bottom: 0.5rem;
      fill: var(--orange-vif);
      filter: drop-shadow(0 0 2px rgba(255,165,0,0.7));
      animation: bounce 2.5s infinite ease-in-out;
      pointer-events: none;
      user-select: none;
    }

    @keyframes bounce {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-8px);
      }
    }

    .login-header h1 {
      font-weight: 700;
      font-size: 2.4rem;
      color: var(--bleu-vif);
      letter-spacing: 2px;
      user-select: none;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1.8rem;
    }

    label {
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 0.4rem;
      color: var(--gris-fonce);
      display: block;
      user-select: none;
    }

    .input-wrapper {
      position: relative;
      width: 100%;
    }

    .input-wrapper svg {
      position: absolute;
      left: 18px;
      top: 53%;
      transform: translateY(-50%);
      width: 24px;
      height: 24px;
      fill: var(--jaune-moutarde);
      pointer-events: none;
      transition: fill 0.3s ease;
      user-select: none;
    }

    /* Monter uniquement l’icône cadenas du mot de passe */
    .input-wrapper svg.icon-password {
      top: 40%;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 0.85rem 1rem 0.85rem 45px;
      border: 2.5px solid var(--input-border);
      border-radius: 12px;
      font-size: 1rem;
      background: var(--input-bg);
      color: var(--gris-fonce);
      transition: border-color 0.4s ease, box-shadow 0.4s ease;
      outline-offset: 3px;
    }

    input[type="email"]:focus,
    input[type="password"]:focus {
      border-color: var(--bleu-vif);
      box-shadow: 0 0 8px var(--bleu-vif);
      background: #e9f0ff;
    }

    input[type="email"]:focus + svg,
    input[type="password"]:focus + svg {
      fill: var(--bleu-vif);
    }

    .error-message {
      font-size: 0.85rem;
      color: #dc3545;
      margin-top: 4px;
      font-weight: 600;
      user-select: none;
      min-height: 1.2em; /* garder l’espace même quand vide */
    }

    .btn-submit {
      position: relative;
      overflow: hidden;
      background-color: var(--orange-vif);
      color: var(--blanc);
      font-weight: 700;
      padding: 0.85rem 0;
      border: none;
      border-radius: 14px;
      cursor: pointer;
      font-size: 1.25rem;
      box-shadow: 0 8px 15px rgba(255,165,0,0.4);
      transition: background-color 0.35s ease, box-shadow 0.35s ease;
      user-select: none;
    }

    .btn-submit:hover,
    .btn-submit:focus-visible {
      background-color: var(--jaune-moutarde);
      color: var(--gris-fonce);
      box-shadow: 0 10px 20px rgba(255,193,7,0.6);
      outline: none;
    }

    .btn-submit:focus-visible {
      outline: 3px solid var(--bleu-vif);
      outline-offset: 4px;
    }

    /* Ripple effect on button click */
    .btn-submit::after {
      content: "";
      position: absolute;
      width: 100px;
      height: 100px;
      background: rgba(255, 255, 255, 0.4);
      border-radius: 50%;
      pointer-events: none;
      transform: scale(0);
      opacity: 0;
      transition: transform 0.5s ease, opacity 1s ease;
      top: 50%;
      left: 50%;
      transform-origin: center;
      transform: translate(-50%, -50%) scale(0);
    }

    .btn-submit:active::after {
      transform: translate(-50%, -50%) scale(1);
      opacity: 1;
      transition: 0s;
    }

    .forgot-password {
      text-align: right;
      font-size: 0.9rem;
      color: var(--bleu-vif);
      cursor: pointer;
      user-select: none;
      font-weight: 600;
      text-decoration: underline;
      transition: color 0.3s ease;
      background: none;
      border: none;
      padding: 0;
      margin-top: 0.25rem;
    }

    .forgot-password:hover,
    .forgot-password:focus {
      color: var(--orange-vif);
      outline: none;
      text-decoration: none;
    }

    .success-message {
      color: var(--vert-clair);
      margin-top: 1.4rem;
      font-weight: 700;
      text-align: center;
      font-size: 1.05rem;
      user-select: none;
      animation: fadeIn 0.5s ease forwards;
    }

    @keyframes fadeIn {
      from {opacity: 0;}
      to {opacity: 1;}
    }
  </style>
</head>
<body>

  <main class="login-container" role="main" aria-label="Formulaire de connexion FunCodeLab">
    <header class="login-header">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" aria-hidden="true" focusable="false" >
        <path d="M32 4L12 60h40L32 4z"/>
      </svg>
      <h1>FunCodeLab</h1>
    </header>

    <form id="loginForm" autocomplete="off" novalidate>
      <div class="input-wrapper">
        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" placeholder="exemple@funcodelab.com" aria-describedby="emailError" required />
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" >
          <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4
            v2h16v-2c0-2.66-5.33-4-8-4z"/>
        </svg>
        <div id="emailError" class="error-message" aria-live="polite"></div>
      </div>

      <div class="input-wrapper">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" placeholder="Votre mot de passe" aria-describedby="passwordError" required />
        <svg class="icon-password" viewBox="0 0 24 24" aria-hidden="true" focusable="false" >
          <path d="M12 17a2 2 0 100-4 2 2 0 000 4zm6-6v-3a6 6 0 00-12 0v3H4v10h16V11h-2zm-8-3a4 4 0 018 0v3H10v-3z"/>
        </svg>
        <div id="passwordError" class="error-message" aria-live="polite"></div>

        <!-- AJOUT DE LA BARRE DE FORCE MOT DE PASSE -->
        <div id="passwordStrengthWrapper" style="margin-top:6px;">
          <div id="passwordStrengthBar" style="
            height: 8px;
            width: 100%;
            border-radius: 6px;
            background-color: #ddd;
            transition: background-color 0.3s ease;
          "></div>
          <div id="passwordStrengthText" style="
            margin-top: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            user-select: none;
            color: #333;
          ">Force du mot de passe</div>
        </div>
      </div>

      <button type="submit" class="btn-submit" aria-label="Se connecter à FunCodeLab">Se connecter</button>

      <button type="button" class="forgot-password" aria-label="Mot de passe oublié ?">Mot de passe oublié ?</button>
    </form>

    <div id="successMessage" class="success-message" role="alert" style="display:none;">
      Connexion réussie !
    </div>
  </main>

  <script>
    const form = document.getElementById('loginForm');
    const successMessage = document.getElementById('successMessage');

    const emailInput = form.email;
    const passwordInput = form.password;

    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');

    form.addEventListener('submit', function(event) {
      event.preventDefault();

      // Reset messages
      emailError.textContent = '';
      passwordError.textContent = '';
      successMessage.style.display = 'none';

      let valid = true;

      if (!emailInput.value.trim()) {
        emailError.textContent = 'L\'adresse e-mail est obligatoire.';
        valid = false;
      } else if (!validateEmail(emailInput.value.trim())) {
        emailError.textContent = 'Veuillez saisir une adresse e-mail valide.';
        valid = false;
      }

      if (!passwordInput.value.trim()) {
        passwordError.textContent = 'Le mot de passe est obligatoire.';
        valid = false;
      }

      if (valid) {
        // Simule succès
        successMessage.style.display = 'block';
        form.reset();
        emailInput.focus();
        // Reset strength bar quand on reset
        passwordStrengthBar.style.backgroundColor = '#ddd';
        passwordStrengthText.textContent = 'Force du mot de passe';
        passwordStrengthText.style.color = '#333';
      }
    });

    // Nettoie le message d'erreur quand on entre dans le champ
    [emailInput, passwordInput].forEach(input => {
      input.addEventListener('input', () => {
        if(input === emailInput) emailError.textContent = '';
        if(input === passwordInput) passwordError.textContent = '';
        successMessage.style.display = 'none';
      });
    });

    function validateEmail(email) {
      // Regex simple pour validation d'email
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    }

    // AJOUT POUR LA FORCE DU MOT DE PASSE
    const passwordStrengthBar = document.getElementById('passwordStrengthBar');
    const passwordStrengthText = document.getElementById('passwordStrengthText');

    passwordInput.addEventListener('input', () => {
      const val = passwordInput.value;
      const strength = calculatePasswordStrength(val);

      // Change la couleur de la barre selon la force
      if (strength === 0) {
        passwordStrengthBar.style.backgroundColor = '#ddd';
        passwordStrengthText.textContent = 'Force du mot de passe';
        passwordStrengthText.style.color = '#333';
      } else if (strength <= 2) {
        passwordStrengthBar.style.backgroundColor = '#dc3545'; // rouge
        passwordStrengthText.textContent = 'Faible';
        passwordStrengthText.style.color = '#dc3545';
      } else if (strength === 3) {
        passwordStrengthBar.style.backgroundColor = '#ffc107'; // jaune moutarde
        passwordStrengthText.textContent = 'Moyen';
        passwordStrengthText.style.color = '#856404';
      } else {
        passwordStrengthBar.style.backgroundColor = '#28a745'; // vert clair
        passwordStrengthText.textContent = 'Fort';
        passwordStrengthText.style.color = '#155724';
      }
    });

    function calculatePasswordStrength(password) {
      let score = 0;

      if (!password) return 0;
      if (password.length >= 8) score++;
      if (/[A-Z]/.test(password)) score++;
      if (/[0-9]/.test(password)) score++;
      if (/[\W_]/.test(password)) score++; // caractère spécial

      // Limiter à 4
      return Math.min(score, 4);
    }
  </script>
</body>
</html>
