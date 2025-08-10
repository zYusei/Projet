<?php
require_once('db.php');
require_once('session.php');

$courses = [
    'prog_html_intro' => [
        'title' => 'HTML — Structure',
        'content' => '<p>Apprends la structure de base d\'une page HTML : &lt;html&gt;, &lt;head&gt;, &lt;body&gt;.</p>'
    ],
    'prog_html_links_forms' => [
        'title' => 'HTML — Liens & formulaires',
        'content' => '<p>Découvre comment insérer des liens, des images et créer des formulaires simples.</p>'
    ],
    // Ajoute les autres...
];

$id = $_GET['id'] ?? '';
if (!isset($courses[$id])) {
    http_response_code(404);
    echo "Cours introuvable.";
    exit;
}
$course = $courses[$id];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($course['title']) ?></title>
<style>
body { font-family: sans-serif; max-width: 800px; margin: auto; padding: 20px; }
.question { border: 1px solid #ccc; padding: 10px; border-radius: 8px; margin: 20px 0; }
</style>
</head>
<body>
<h1><?= htmlspecialchars($course['title']) ?></h1>
<div><?= $course['content'] ?></div>

<div class="question">
    <p><strong>Question :</strong> Exemple de question liée au cours.</p>
    <label><input type="radio" name="q1" data-correct="true"> Bonne réponse</label><br>
    <label><input type="radio" name="q1"> Mauvaise réponse</label><br>
    <button onclick="checkAnswer('q1')">Vérifier</button>
</div>

<script>
function checkAnswer(name) {
    const sel = document.querySelector(`input[name="${name}"]:checked`);
    if (!sel) return alert('Choisis une réponse.');
    alert(sel.dataset.correct ? '✅ Correct' : '❌ Incorrect');
}
</script>

<p><a href="parcours.php">← Retour aux parcours</a></p>
</body>
</html>
