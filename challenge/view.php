<?php
require_once('./_partials.php'); $user=require_user();

$slug=$_GET['slug'] ?? '';
$ch = db_fetch_one("SELECT * FROM challenge WHERE slug=? AND is_active=1",[$slug]);
if(!$ch){ http_response_code(404); render_head('Challenge introuvable'); echo '<h1>Introuvable</h1>'; render_footer(); exit; }

render_head(htmlspecialchars($ch['title']).' — FunCodeLab');
?>
<a href="index.php" class="muted" style="text-decoration:none">← Retour</a>
<h1><?=htmlspecialchars($ch['title'])?></h1>
<p><span class="pill"><?=$ch['type']?></span> <span class="pill"><?=$ch['difficulty']?></span> <span class="pill"><?=$ch['points']?> pts</span> <span class="pill">⏱️ <?=intval($ch['time_limit_sec']/60)?> min</span></p>
<div class="card" style="margin-top:1rem">
  <pre style="white-space:pre-wrap;word-break:break-word; margin:0"><?=htmlspecialchars($ch['prompt_md'])?></pre>
</div>
<p style="margin-top:1rem">
  <a class="btn" href="start.php?slug=<?=urlencode($slug)?>">Démarrer</a>
</p>
<?php render_footer(); ?>
