<?php
require_once('./_partials.php'); $user=require_user();

$id = intval($_GET['attempt_id'] ?? 0);
$att = db_fetch_one("SELECT a.*, c.title,c.slug,c.type,c.points,c.time_limit_sec
                     FROM challenge_attempt a JOIN challenge c ON c.id=a.challenge_id
                     WHERE a.id=? AND a.user_id=?", [$id,$user['id']]);
if(!$att){ http_response_code(404); render_head('Attempt introuvable'); echo '<h1>Introuvable</h1>'; render_footer(); exit; }

render_head(htmlspecialchars($att['title']).' — Play');
$expired = (new DateTime('now', new DateTimeZone('UTC')) > new DateTime($att['expires_at'], new DateTimeZone('UTC')));
?>
<a href="view.php?slug=<?=htmlspecialchars($att['slug'])?>" class="muted" style="text-decoration:none">← Énoncé</a>
<h1><?=htmlspecialchars($att['title'])?></h1>
<p><span class="pill"><?=$att['type']?></span> <span class="pill"><?=$att['points']?> pts</span> <span id="timer" class="timer" data-expires="<?=$att['expires_at']?>">…</span></p>

<form class="card" method="post" action="submit.php" style="margin-top:1rem">
  <input type="hidden" name="attempt_id" value="<?=$id?>">
  <?php if($att['type']==='CTF'): ?>
    <label style="font-weight:800;display:block;margin-bottom:.4rem">Flag (format FCL{...})</label>
    <input type="text" name="flag" placeholder="FCL{...}" <?= $expired?'disabled':'' ?>>
  <?php elseif($att['type']==='SQL'): ?>
    <label style="font-weight:800;display:block;margin-bottom:.4rem">Requête SQL (SELECT uniquement)</label>
    <textarea name="sql" rows="10" placeholder="SELECT ..." <?= $expired?'disabled':'' ?>></textarea>
    <p class="muted" style="margin:.4rem 0 0">MVP: runner SQL à brancher (SQLite). La soumission est enregistrée, mais l’évaluation n’est pas encore active.</p>
  <?php else: ?>
    <p>Type non pris en charge dans ce MVP.</p>
  <?php endif; ?>
  <p style="margin-top:1rem"><button class="btn" id="submitBtn" <?= $expired?'disabled':'' ?>>Soumettre</button></p>
</form>

<script>
(function(){
  const t=document.getElementById('timer'); if(!t) return;
  const expires=new Date(t.dataset.expires+'Z');
  function tick(){
    const now=new Date(); let diff=Math.max(0,Math.floor((expires-now)/1000));
    const m=String(Math.floor(diff/60)).padStart(2,'0'), s=String(diff%60).padStart(2,'0');
    t.textContent=`⏱️ ${m}:${s}`;
    if(diff===0){ document.getElementById('submitBtn')?.setAttribute('disabled','disabled'); }
    else requestAnimationFrame(tick);
  } tick();
})();
</script>
<?php render_footer(); ?>
