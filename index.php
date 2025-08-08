<?php require_once('db.php'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Accueil - FunCodeLab</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bleu-vif: #007BFF;
      --orange-vif: #FFA500;
      --jaune-moutarde: #FFC107;
      --vert-clair: #28A745;
      --gris-clair: #F5F5F5;
      --gris-fonce: #333333;
      --blanc: #FFFFFF;
      --shadow-light: rgba(0, 0, 0, 0.08);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    body {
      background: linear-gradient(135deg, var(--bleu-vif), var(--orange-vif));
      min-height: 100vh;
      color: var(--gris-fonce);
    }

    /* NAVBAR */
    .main-navbar {
      background: var(--blanc);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      position: sticky;
      top: 0;
      z-index: 999;
    }

    .nav-content {
      max-width: 1100px;
      margin: auto;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      font-size: 1.6rem;
      font-weight: 700;
      color: var(--bleu-vif);
      user-select: none;
    }

    .nav-links {
      display: flex;
      gap: 1.5rem;
      list-style: none;
    }

    .nav-links a {
      text-decoration: none;
      color: var(--gris-fonce);
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .nav-links a:hover {
      color: var(--orange-vif);
    }

    .nav-btn {
      background-color: var(--orange-vif);
      padding: 0.5rem 1rem;
      border-radius: 8px;
      color: white !important;
      transition: background-color 0.3s ease;
    }

    .nav-btn:hover {
      background-color: var(--jaune-moutarde);
      color: var(--gris-fonce) !important;
    }

    /* MAIN WRAPPER */
    .main-content-wrapper {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 3rem 1.5rem;
    }

    .home-container {
      background: var(--blanc);
      max-width: 960px;
      width: 100%;
      border-radius: 20px;
      box-shadow: 0 20px 30px var(--shadow-light), 0 0 0 1px rgba(0, 0, 0, 0.05);
      padding: 3rem;
      animation: fadeInScale 0.8s ease forwards;
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

    .home-header {
      text-align: center;
      margin-bottom: 2.5rem;
    }

    .home-header h1 {
      font-size: 2.8rem;
      font-weight: 700;
      color: var(--bleu-vif);
    }

    .home-header p {
      font-size: 1.2rem;
      margin-top: 0.5rem;
    }

    .btn-cta {
      display: inline-block;
      margin-top: 2rem;
      background-color: var(--orange-vif);
      color: var(--blanc);
      padding: 0.9rem 2rem;
      font-size: 1.1rem;
      font-weight: bold;
      border: none;
      border-radius: 12px;
      text-decoration: none;
      box-shadow: 0 8px 18px rgba(255, 165, 0, 0.3);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .btn-cta:hover {
      background-color: var(--jaune-moutarde);
      color: var(--gris-fonce);
      box-shadow: 0 10px 25px rgba(255, 193, 7, 0.4);
    }

    section {
      margin-top: 3rem;
    }

    h2.section-title {
      font-size: 1.8rem;
      margin-bottom: 1.5rem;
      color: var(--gris-fonce);
      text-align: center;
    }

    .badge-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1.5rem;
    }

    .badge-card {
      background-color: var(--jaune-moutarde);
      padding: 1.2rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      text-align: center;
      transition: transform 0.3s ease;
    }

    .badge-card:hover {
      transform: translateY(-4px);
    }

    .badge-card h3 {
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 0.4rem;
    }

    .badge-card p {
      margin-bottom: 0.2rem;
    }

    .feature-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1.5rem;
    }

    .feature-card {
      background: var(--gris-clair);
      border-left: 5px solid var(--bleu-vif);
      border-radius: 12px;
      padding: 1.5rem;
      font-weight: 600;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s ease;
    }

    .feature-card:hover {
      transform: scale(1.02);
    }

    footer {
      margin-top: 3rem;
      text-align: center;
      font-size: 0.9rem;
      color: #666;
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="main-navbar">
    <div class="nav-content">
      <div class="logo">FunCodeLab</div>
      <ul class="nav-links">
        <li><a href="index.php">Accueil</a></li>
        <li><a href="#">Quiz</a></li>
        <li><a href="#">Parcours</a></li>
        <li><a href="connexion.php">Connexion</a></li>
        <li><a href="inscription.php" class="nav-btn">Inscription</a></li>
      </ul>
    </div>
  </nav>

  <!-- CONTENU -->
  <div class="main-content-wrapper">
    <div class="home-container">
      <header class="home-header">
        <h1>FunCodeLab</h1>
        <p>Apprends à coder avec plaisir ! Quiz, jeux, parcours & badges 🎓</p>
        <a href="connexion.php" class="btn-cta">Commencer maintenant</a>
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

</body>
</html>
