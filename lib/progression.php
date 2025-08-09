<?php
// lib/progression.php
// Fonctions niveau/XP + badges

function level_need(int $level): int {
  // Courbe simple et progressive (à ajuster si tu veux)
  return max(150, (int) round(200 + 70 * pow(max(1,$level), 1.12)));
}

function ensure_user_stats(PDO $pdo, int $userId): void {
  $pdo->prepare("INSERT IGNORE INTO user_stats (user_id, level, xp, xp_total) VALUES (:id, 1, 0, 0)")
      ->execute([':id'=>$userId]);
}

function get_user_stats(PDO $pdo, int $userId): array {
  $st = $pdo->prepare("SELECT level, xp, xp_total FROM user_stats WHERE user_id=:id LIMIT 1");
  $st->execute([':id'=>$userId]);
  $s = $st->fetch(PDO::FETCH_ASSOC);
  if (!$s) { ensure_user_stats($pdo, $userId); $s=['level'=>1,'xp'=>0,'xp_total'=>0]; }
  $need = level_need((int)$s['level']);
  $pct  = (int)round(((int)$s['xp'] / max(1,$need))*100);
  return [
    'level'=>(int)$s['level'],
    'xp'   =>(int)$s['xp'],
    'xp_total'=>(int)$s['xp_total'],
    'need' =>$need,
    'pct'  =>max(0,min(100,$pct))
  ];
}

function add_xp(PDO $pdo, int $userId, int $delta): array {
  ensure_user_stats($pdo, $userId);
  $pdo->beginTransaction();
  try{
    $st = $pdo->prepare("SELECT level, xp, xp_total FROM user_stats WHERE user_id=:id FOR UPDATE");
    $st->execute([':id'=>$userId]);
    $s = $st->fetch(PDO::FETCH_ASSOC);
    if (!$s) { $s = ['level'=>1,'xp'=>0,'xp_total'=>0]; }

    $level=(int)$s['level'];
    $xp   =(int)$s['xp'] + $delta;
    $xpt  =(int)$s['xp_total'] + $delta;
    $leveled=false;

    while ($xp >= level_need($level)) {
      $xp -= level_need($level);
      $level++;
      $leveled=true;
    }

    $pdo->prepare("UPDATE user_stats SET level=:l, xp=:xp, xp_total=:xpt, updated_at=NOW() WHERE user_id=:id")
        ->execute([':l'=>$level, ':xp'=>$xp, ':xpt'=>$xpt, ':id'=>$userId]);

    $pdo->commit();
    return ['level'=>$level,'xp'=>$xp,'xp_total'=>$xpt,'leveled'=>$leveled];
  }catch(Throwable $e){
    $pdo->rollBack();
    throw $e;
  }
}

function get_badge_id_by_code(PDO $pdo, string $code): ?int {
  $st=$pdo->prepare("SELECT id FROM badge_definitions WHERE code=:c LIMIT 1");
  $st->execute([':c'=>$code]);
  $id=$st->fetchColumn();
  return $id ? (int)$id : null;
}

function award_badge(PDO $pdo, int $userId, string $badgeCode): bool {
  $bid = get_badge_id_by_code($pdo, $badgeCode);
  if (!$bid) return false;
  return $pdo->prepare("INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (:u,:b)")
             ->execute([':u'=>$userId, ':b'=>$bid]);
}

function get_user_badges(PDO $pdo, int $userId): array {
  $sql="SELECT bd.name, bd.icon, bd.color, ub.earned_at
        FROM user_badges ub
        JOIN badge_definitions bd ON bd.id=ub.badge_id
        WHERE ub.user_id=:u
        ORDER BY ub.earned_at DESC";
  $st=$pdo->prepare($sql);
  $st->execute([':u'=>$userId]);
  return $st->fetchAll(PDO::FETCH_ASSOC);
}

function get_recent_badges(PDO $pdo, int $limit=9): array {
  $sql="SELECT u.username, bd.name AS badge, bd.icon, ub.earned_at
        FROM user_badges ub
        JOIN users u ON u.id=ub.user_id
        JOIN badge_definitions bd ON bd.id=ub.badge_id
        ORDER BY ub.earned_at DESC
        LIMIT :lim";
  $st=$pdo->prepare($sql);
  $st->bindValue(':lim', $limit, PDO::PARAM_INT);
  $st->execute();
  return $st->fetchAll(PDO::FETCH_ASSOC);
}
