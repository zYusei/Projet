<?php
require_once('./_partials.php'); 
$user = require_user();

$type = $_GET['type'] ?? '';
$diff = $_GET['difficulty'] ?? '';

$clauses = ['c.is_active=1'];
$p = [];

// Filtrage type & difficulté
if ($type && in_array($type, ['CODE','SQL','CTF'])) { 
    $clauses[] = 'c.type=?';       
    $p[] = $type; 
}
if ($diff && in_array($diff, ['Facile','Intermédiaire','Difficile'])) { 
    $clauses[] = 'c.difficulty=?'; 
    $p[] = $diff; 
}

// Exclure les challenges déjà réussis par cet utilisateur
$clauses[] = "c.id NOT IN (
    SELECT a.challenge_id
    FROM challenge_attempt a
    WHERE a.user_id=? AND a.status='PASSED'
)";
$p[] = $user['id'];

// Requête
$sql = "SELECT c.id, c.title, c.slug, c.type, c.difficulty, c.points, c.time_limit_sec
        FROM challenge c
        " . (count($clauses) ? " WHERE " . implode(' AND ', $clauses) : '') . "
        ORDER BY c.id DESC";

$rows = db_fetch_all($sql, $p);

render_head('Challenges — FunCodeLab');
?>
<h1>🏆 Challenges</h1>
<p class="subhead">Courtes épreuves chronométrées. Gagne des points et grimpe au classement.</p>

<form method="get" class="toolbar">

  <select name="type" onchange="this.form.submit()">
    <option value="">Tous les types</option>
    <?php foreach (['CODE','SQL','CTF'] as $t){ 
        $sel = $type === $t ? 'selected' : ''; 
        echo "<option $sel>$t</option>"; 
    } ?>
  </select>

  <select name="difficulty" onchange="this.form.submit()">
    <option value="">Toutes difficultés</option>
    <?php foreach (['Facile','Intermédiaire','Difficile'] as $d){ 
        $sel = $diff === $d ? 'selected' : ''; 
        echo "<option $sel>$d</option>"; 
    } ?>
  </select>

  <a href="leaderboard.php?period=week" class="btn-ranking">🏅 Classement hebdo</a>

  <?php if ($type || $diff): ?>
    <a href="index.php" class="btn-ghost" style="white-space:nowrap; padding:.6rem 1rem; border-radius:12px;">↺ Réinitialiser</a>
  <?php endif; ?>
</form>

<div class="cards">
  <?php foreach ($rows as $c): ?>
    <div class="card">
      <div class="pill"><?= htmlspecialchars($c['type']) ?></div>
      <div class="pill"><?= htmlspecialchars($c['difficulty']) ?></div>
      <div class="pill"><?= intval($c['points']) ?> pts</div>
      <h2 style="margin:.6rem 0 .2rem"><?= htmlspecialchars($c['title']) ?></h2>
      <p class="muted" style="margin:.2rem 0 .8rem">⏱️ <?= intval($c['time_limit_sec']/60) ?> min</p>
      <a class="btn" href="view.php?slug=<?= urlencode($c['slug']) ?>">Voir</a>
    </div>
  <?php endforeach; if (!count($rows)) echo '<p>Aucun challenge pour ces filtres.</p>'; ?>
</div>

<?php render_footer(); ?>
