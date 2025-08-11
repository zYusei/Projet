<?php
require_once 'db.php';
require_once 'session.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

$user = current_user();
if (!$user) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'Auth requise']); exit; }

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$csrf = $_POST['csrf'] ?? $_GET['csrf'] ?? '';
if (!hash_equals($_SESSION['csrf'] ?? '', $csrf)) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'CSRF']); exit; }

function bad_words_filter($pdo, $msg) {
  $rows = $pdo->query("SELECT word FROM bad_words")->fetchAll(PDO::FETCH_COLUMN);
  foreach ($rows as $w) {
    if ($w === '') continue;
    $msg = preg_replace('/\b'.preg_quote($w,'/').'\b/iu', str_repeat('•', mb_strlen($w)), $msg);
  }
  return $msg;
}

switch ($action) {

  case 'send':
    // ratelimit simple: 5 msgs / 60s
    $st = $pdo->prepare("SELECT COUNT(*) FROM chat_messages WHERE user_id=:u AND created_at > (NOW() - INTERVAL 60 SECOND)");
    $st->execute([':u'=>$user['id']]); $count = (int)$st->fetchColumn();
    if ($count >= 5) { http_response_code(429); echo json_encode(['ok'=>false,'error'=>'Trop de messages (1min)']); exit; }

    $msg = trim($_POST['message'] ?? '');
    $parent = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    if ($msg==='' && empty($_POST['attachment'])) { echo json_encode(['ok'=>false,'error'=>'vide']); exit; }
    $msg = preg_replace('/[\x00-\x1F]/u','',$msg);
    $msg = bad_words_filter($pdo,$msg);

    $stmt = $pdo->prepare("INSERT INTO chat_messages(user_id,username,message,parent_id,attachment_url) VALUES(:u,:n,:m,:p,:a)");
    $stmt->execute([
      ':u'=>$user['id'], ':n'=>$user['username'], ':m'=>$msg,
      ':p'=>$parent ?: null,
      ':a'=> $_POST['attachment'] ?? null
    ]);
    echo json_encode(['ok'=>true,'id'=>$pdo->lastInsertId()]);
    break;

  case 'edit':
    $id = (int)$_POST['id'];
    // fenêtre 5 min
    $st = $pdo->prepare("SELECT user_id, created_at FROM chat_messages WHERE id=:id");
    $st->execute([':id'=>$id]); $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row || (int)$row['user_id'] !== (int)$user['id']) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'forbidden']); exit; }
    if (strtotime($row['created_at']) < time()-300) { http_response_code(422); echo json_encode(['ok'=>false,'error'=>'Trop tard']); exit; }
    $msg = bad_words_filter($pdo, trim($_POST['message'] ?? ''));
    $pdo->prepare("UPDATE chat_messages SET message=:m, edited_at=NOW() WHERE id=:id")
        ->execute([':m'=>$msg, ':id'=>$id]);
    echo json_encode(['ok'=>true]); break;

  case 'delete':
    $id = (int)$_POST['id'];
    $st = $pdo->prepare("SELECT user_id FROM chat_messages WHERE id=:id");
    $st->execute([':id'=>$id]); $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row || (int)$row['user_id'] !== (int)$user['id']) { http_response_code(403); echo json_encode(['ok'=>false]); exit; }
    $pdo->prepare("UPDATE chat_messages SET is_deleted=1 WHERE id=:id")->execute([':id'=>$id]);
    echo json_encode(['ok'=>true]); break;

  case 'react':
    $message = (int)$_POST['message_id'];
    $emoji   = substr($_POST['emoji'] ?? '',0,8);
    // toggle
    $ins = $pdo->prepare("INSERT IGNORE INTO chat_reactions(message_id,user_id,emoji) VALUES(:m,:u,:e)");
    $ins->execute([':m'=>$message, ':u'=>$user['id'], ':e'=>$emoji]);
    if ($ins->rowCount()===0) {
      $pdo->prepare("DELETE FROM chat_reactions WHERE message_id=:m AND user_id=:u AND emoji=:e")
          ->execute([':m'=>$message, ':u'=>$user['id'], ':e'=>$emoji]);
    }
    echo json_encode(['ok'=>true]); break;

  case 'report':
    $message = (int)$_POST['message_id'];
    $reason  = substr(trim($_POST['reason'] ?? ''),0,255);
    $pdo->prepare("INSERT INTO chat_reports(message_id,reporter_id,reason) VALUES(:m,:u,:r)")
        ->execute([':m'=>$message, ':u'=>$user['id'], ':r'=>$reason]);
    $pdo->prepare("UPDATE chat_messages SET is_flagged=1 WHERE id=:m")->execute([':m'=>$message]);
    echo json_encode(['ok'=>true]); break;

  case 'typing':
    $pdo->prepare("INSERT INTO chat_presence(user_id,username,last_seen,typing_until)
                   VALUES(:u,:n,NOW(), DATE_ADD(NOW(), INTERVAL 4 SECOND))
                   ON DUPLICATE KEY UPDATE last_seen=NOW(), typing_until=DATE_ADD(NOW(), INTERVAL 4 SECOND)")
        ->execute([':u'=>$user['id'], ':n'=>$user['username']]);
    echo json_encode(['ok'=>true]); break;

  case 'upload':
    if (empty($_FILES['file']) || $_FILES['file']['error']!==UPLOAD_ERR_OK) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'upload']); exit; }
    $f = $_FILES['file'];
    // scan mime
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($f['tmp_name']);
    if (!in_array($mime, ['image/png','image/jpeg','image/webp','image/gif'])) { http_response_code(415); echo json_encode(['ok'=>false,'error'=>'type']); exit; }
    // dossier
    $dir = __DIR__ . '/uploads/chat';
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    $ext = ['image/png'=>'png','image/jpeg'=>'jpg','image/webp'=>'webp','image/gif'=>'gif'][$mime];
    $name = 'chat_' . $user['id'] . '_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
    $path = $dir . '/' . $name;
    move_uploaded_file($f['tmp_name'], $path);
    // (optionnel) redimension via GD/Imagick ici
    $url = 'uploads/chat/' . $name;
    echo json_encode(['ok'=>true,'url'=>$url]); break;

  default:
    http_response_code(400); echo json_encode(['ok'=>false,'error'=>'action']);
}
