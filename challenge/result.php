<?php
require_once('./_partials.php'); 
$user = require_user();

$id = intval($_GET['attempt_id'] ?? 0);
$att = db_fetch_one(
    "SELECT a.*, c.title,c.slug,c.points 
     FROM challenge_attempt a 
     JOIN challenge c ON c.id=a.challenge_id
     WHERE a.id=? AND a.user_id=?", 
    [$id, $user['id']]
);
$last = db_fetch_one(
    "SELECT verdict,feedback,submitted_at 
     FROM challenge_submission 
     WHERE attempt_id=? 
     ORDER BY id DESC 
     LIMIT 1",
    [$id]
);

render_head('Résultat — FunCodeLab');
?>
<a href="view.php?slug=<?= htmlspecialchars($att['slug']) ?>" class="muted" style="text-decoration:none">← Énoncé</a>
<h1>Résultat — <?= htmlspecialchars($att['title']) ?></h1>

<div class="card">
  <p>
    Status : <span class="pill"><?= htmlspecialchars($att['status']) ?></span> 
    • Score : <strong><?= intval($att['best_score']) ?></strong> pts
  </p>
  
  <?php if ($att['duration_sec']): ?>
    <p>Temps : <?= intval($att['duration_sec'] / 60) ?>m <?= intval($att['duration_sec'] % 60) ?>s</p>
  <?php endif; ?>
  
  <?php if ($last): ?>
    <p>Dernier verdict : 
      <strong><?= htmlspecialchars($last['verdict']) ?></strong> — 
      <?= htmlspecialchars($last['feedback']) ?>
    </p>
  <?php endif; ?>
</div>

<p style="margin-top:1.5rem; text-align:center;">
  <a href="index.php" 
     style="
        display:inline-block;
        padding:0.6rem 1.2rem;
        background:linear-gradient(90deg,#4facfe,#00f2fe);
        color:#fff;
        font-weight:600;
        border-radius:8px;
        text-decoration:none;
        box-shadow:0 4px 10px rgba(0,0,0,0.15);
        transition:all 0.2s ease;
     "
     onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 6px 14px rgba(0,0,0,0.2)';"
     onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 10px rgba(0,0,0,0.15)';">
     Tous les challenges
  </a>
</p>

<?php render_footer(); ?>
