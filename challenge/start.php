<?php
require_once('./_partials.php'); $user=require_user();

$slug=$_GET['slug'] ?? '';
$ch = db_fetch_one("SELECT * FROM challenge WHERE slug=? AND is_active=1",[$slug]);
if(!$ch) { http_response_code(404); exit('Not found'); }

$now = new DateTime('now', new DateTimeZone('UTC'));
$exp = (clone $now)->modify('+'.intval($ch['time_limit_sec']).' seconds');

db_exec("INSERT INTO challenge_attempt (challenge_id,user_id,started_at,expires_at) VALUES (?,?,?,?)", [
  $ch['id'], $user['id'], $now->format('Y-m-d H:i:s'), $exp->format('Y-m-d H:i:s')
]);
$attempt_id = db()->lastInsertId();
header('Location: play.php?attempt_id='.$attempt_id);
