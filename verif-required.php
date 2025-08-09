<?php
require_once __DIR__.'/session.php';
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Vérification requise</title>
<style>body{font-family:system-ui;margin:2rem} .box{max-width:620px;margin:auto;padding:1rem 1.2rem;border:1px solid #ddd;border-radius:12px}</style>
</head>
<body>
  <div class="box">
    <h1>Vérifie ton e-mail 📧</h1>
    <p>Nous t’avons envoyé un lien de confirmation. Pense à vérifier tes spams.</p>
    <p><a href="resend_verification.php">Renvoyer l’e-mail</a> • <a href="connexion.php">Se connecter</a></p>
  </div>
</body>
</html>
