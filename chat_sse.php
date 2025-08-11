<?php
require_once 'db.php';
require_once 'session.php';
session_start();
$user = current_user();

ignore_user_abort(true);
set_time_limit(0);

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

$lastId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;
$started = time();

function sse_send($event, $data) {
  echo "event: {$event}\n";
  echo "data: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n\n";
  @ob_flush();
  @flush();
}

while (true) {
  if (connection_aborted()) break;

  // nouveaux messages
  $stmt = $pdo->prepare("
    SELECT m.id, m.username, m.message, m.created_at, m.parent_id,
           m.edited_at, m.attachment_url, m.is_deleted, m.is_flagged,
           COALESCE(u.avatar_url,'') AS avatar_url
    FROM chat_messages m
    LEFT JOIN users u ON u.id = m.user_id
    WHERE m.id > :id
    ORDER BY m.id ASC
  ");
  $stmt->execute([':id'=>$lastId]);
  $msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
  if ($msgs) {
    $lastId = max($lastId, (int)end($msgs)['id']);
    sse_send('messages', ['messages'=>$msgs, 'last_id'=>$lastId]);
  }

  // présence
  $pdo->exec("DELETE FROM chat_presence WHERE last_seen < (NOW() - INTERVAL 60 SECOND)");
  if ($user) {
    $stmt = $pdo->prepare("INSERT INTO chat_presence(user_id,username,last_seen) VALUES(:u,:n,NOW())
                           ON DUPLICATE KEY UPDATE last_seen = NOW()");
    $stmt->execute([':u'=>$user['id'], ':n'=>$user['username']]);
  }
  $online = $pdo->query("SELECT username FROM chat_presence ORDER BY username ASC")->fetchAll(PDO::FETCH_COLUMN);
  sse_send('presence', ['online'=>$online]);

  // typing: renvoyer qui tape encore
  $typing = $pdo->query("SELECT username FROM chat_presence WHERE typing_until IS NOT NULL AND typing_until > NOW()")->fetchAll(PDO::FETCH_COLUMN);
  if ($typing) sse_send('typing', ['typing'=>$typing]);

  // ping toutes les ~2s pendant 25s
  sse_send('ping', ['t'=>time()]);
  sleep(2);
  if (time() - $started > 25) break;
}
