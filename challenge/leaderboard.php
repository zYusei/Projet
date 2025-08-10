<?php
require_once('./_partials.php'); 
$user = require_user();

$periodUi  = (($_GET['period'] ?? 'week') === 'month') ? 'month' : 'week';
$periodSql = ($periodUi === 'month') ? 'MONTH' : 'WEEK';

$rows = db_fetch_all("
  SELECT lb.*, u.username
  FROM challenge_leaderboard lb
  JOIN users u ON u.id = lb.user_id
  WHERE lb.period = ?
    AND lb.period_start = (
      CASE 
        WHEN ? = 'WEEK' THEN CURDATE() - INTERVAL (WEEKDAY(CURDATE())) DAY
        ELSE DATE_FORMAT(CURDATE(), '%Y-%m-01')
      END
    )
  ORDER BY lb.rank ASC
", [$periodSql, $periodSql]);

render_head('Classement — FunCodeLab');
?>
<h1>🥇 Classement <?= $periodUi === 'week' ? 'hebdomadaire' : 'mensuel' ?></h1>
<p class="subhead">La somme des meilleurs scores par challenge (période en cours).</p>

<div class="toolbar" style="display:flex; flex-wrap:wrap; gap:.6rem; align-items:center; margin-top:.5rem">
  <a href="?period=week"  class="btn-ranking<?= $periodUi==='week'  ? ' active' : '' ?>">📅 Hebdo</a>
  <a href="?period=month" class="btn-ranking<?= $periodUi==='month' ? ' active' : '' ?>">🗓️ Mensuel</a>
  <a href="index.php" class="btn-ranking" style="margin-left:auto;">⬅ Retour aux challenges</a>
</div>

<?php if (!$rows): ?>
  <div class="card" style="margin-top:.6rem">
    <p class="muted" style="margin:0;">Aucun score pour cette période.</p>
  </div>
<?php else: ?>
  <div class="cards" style="margin-top:.6rem">
    <?php foreach ($rows as $r): 
      $rank = (int)$r['rank'];
      $score = (int)$r['score'];
      $usern = htmlspecialchars($r['username']);
      $medal = '';
      if ($rank === 1) $medal = '🥇';
      elseif ($rank === 2) $medal = '🥈';
      elseif ($rank === 3) $medal = '🥉';
    ?>
      <div class="card" style="display:flex; justify-content:space-between; align-items:center; gap:.8rem">
        <div style="display:flex; align-items:center; gap:.6rem; font-weight:800;">
          <span class="pill" style="min-width:56px; text-align:center">#<?= $rank ?></span>
          <span><?= $medal ?> <?= $usern ?></span>
        </div>
        <div><span class="pill"><?= $score ?> pts</span></div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php render_footer(); ?>
