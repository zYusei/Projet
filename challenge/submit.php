<?php
require_once('./_partials.php');
$user = require_user();

/*
 * SUBMIT d'une tentative:
 *  - CTF : compare flag (exact ou regex:)
 *  - SQL : construit un mini dataset isolé (prefix), exécute la requête, compare au résultat attendu
 *  - Met à jour la tentative et crédite le leaderboard (week + month) sur le delta du best_score
 */

$attempt_id = intval($_POST['attempt_id'] ?? 0);
$att = db_fetch_one(
  "SELECT a.*, c.id AS cid, c.type, c.points
     FROM challenge_attempt a
     JOIN challenge c ON c.id=a.challenge_id
    WHERE a.id=? AND a.user_id=?",
  [$attempt_id, $user['id']]
);
if (!$att) { http_response_code(404); exit('attempt not found'); }

// expiration
$nowUtc = new DateTime('now', new DateTimeZone('UTC'));
$expUtc = new DateTime($att['expires_at'], new DateTimeZone('UTC'));
if ($nowUtc > $expUtc) {
  db_exec("UPDATE challenge_attempt SET status='EXPIRED' WHERE id=?", [$attempt_id]);
  header('Location: result.php?attempt_id='.$attempt_id);
  exit;
}

$payload  = '';
$ok       = false;
$feedback = '';

// ---------- Helpers ----------
function norm_rows(array $rows): array {
  foreach ($rows as &$r) {
    ksort($r);
    foreach ($r as &$v) {
      if (is_string($v)) { $v = trim($v); }
    }
  }
  usort(
    $rows,
    fn($a,$b)=>strcmp(json_encode($a,JSON_UNESCAPED_UNICODE), json_encode($b,JSON_UNESCAPED_UNICODE))
  );
  return $rows;
}

function leaderboard_upsert(string $period, int $user_id, int $deltaScore): void {
  if ($deltaScore <= 0) return;

  if ($period === 'WEEK') {
    $start = (new DateTimeImmutable('monday this week', new DateTimeZone('UTC')))->format('Y-m-d');
    $end   = (new DateTimeImmutable('sunday this week',  new DateTimeZone('UTC')))->format('Y-m-d');
  } else {
    $start = (new DateTimeImmutable('first day of this month', new DateTimeZone('UTC')))->format('Y-m-d');
    $end   = (new DateTimeImmutable('last day of this month',  new DateTimeZone('UTC')))->format('Y-m-d');
  }

  db_exec(
    "INSERT INTO challenge_leaderboard (period,period_start,period_end,user_id,score,rank)
     VALUES (?,?,?,?,?,NULL)
     ON DUPLICATE KEY UPDATE score=score+VALUES(score)",
    [$period, $start, $end, $user_id, $deltaScore]
  );

  // recalcul dense rank
  $rows = db_fetch_all(
    "SELECT user_id, score
       FROM challenge_leaderboard
      WHERE period=? AND period_start=?
      ORDER BY score DESC, user_id ASC",
    [$period, $start]
  );
  $rank = 0; $prev = null;
  foreach ($rows as $r) {
    if ($prev === null || (int)$r['score'] !== (int)$prev) $rank++;
    db_exec("UPDATE challenge_leaderboard SET rank=? WHERE period=? AND period_start=? AND user_id=?",
            [$rank, $period, $start, $r['user_id']]);
    $prev = (int)$r['score'];
  }
}

// ---------- Evaluation ----------
if ($att['type'] === 'CTF') {
  $payload = trim($_POST['flag'] ?? '');
  if ($payload === '') { $feedback = 'Flag vide.'; }
  else {
    $asset = db_fetch_one("SELECT content FROM challenge_asset WHERE challenge_id=? AND kind='CTF_FLAG' LIMIT 1",
                          [$att['cid']]);
    if (!$asset) {
      $feedback = 'Flag non configuré.';
    } else {
      $expected = trim($asset['content']);
      if (str_starts_with($expected, 'regex:')) {
        $pattern = substr($expected, 6);
        $ok = @preg_match('/'.$pattern.'/u', $payload) === 1;
      } else {
        $ok = hash_equals($expected, $payload);
      }
      $feedback = $ok ? 'Bien joué 🎉' : 'Incorrect';
    }
  }
}
elseif ($att['type'] === 'SQL') {
  $payload = trim($_POST['sql'] ?? '');
  if (!preg_match('/^\s*SELECT\b/i', $payload) || preg_match('/\b(drop|truncate|update|insert|delete|alter)\b/i',$payload)) {
    $ok = false; $feedback = 'Seules les requêtes SELECT sont autorisées.';
  } else {
    // Assets
    $schema = db_fetch_one("SELECT content FROM challenge_asset WHERE challenge_id=? AND kind='SQL_SCHEMA' LIMIT 1",
                           [$att['cid']])['content'] ?? '';
    $seed   = db_fetch_one("SELECT content FROM challenge_asset WHERE challenge_id=? AND kind='SQL_SEED'   LIMIT 1",
                           [$att['cid']])['content'] ?? '';

    // Expected: priorité ATTACHMENT expected_* sinon SQL_EXPECT (CSV)
    $attach = db_fetch_one("SELECT content FROM challenge_asset WHERE challenge_id=? AND kind='ATTACHMENT' LIMIT 1",
                           [$att['cid']])['content'] ?? '';
    $expectedCsv = null;
    $expectedSql = null;

    if (stripos($attach,'expected_sql:') === 0) {
      $expectedSql = trim(substr($attach, 13));
    } elseif (stripos($attach,'expected_csv:') === 0) {
      $expectedCsv = trim(substr($attach, 13));
    } else {
      $sqlExp = db_fetch_one("SELECT content FROM challenge_asset WHERE challenge_id=? AND kind='SQL_EXPECT' LIMIT 1",
                             [$att['cid']])['content'] ?? '';
      if ($sqlExp !== '') $expectedCsv = trim($sqlExp);
    }

    if ($schema === '' || ($expectedCsv === null && $expectedSql === null)) {
      $ok=false; $feedback='Dataset SQL manquant.'; 
    } else {
      $pdo = db();

      // 1) détecte les noms de tables dans le DDL
      $tbls = [];
      if (preg_match_all('/CREATE\s+(?:TEMPORARY\s+)?TABLE\s+`?([a-zA-Z0-9_]+)`?/i', $schema, $m)) {
        $tbls = array_unique($m[1]);
      }

      // 2) prefix d’isolation
      $prefix = 'tmpc_'.$att['cid'].'_'.$user['id'].'_'.substr(bin2hex(random_bytes(3)),0,6);
      $prefixTable = function(string $sql) use ($tbls, $prefix) {
        foreach ($tbls as $t) {
          $re = '/\b`?'.preg_quote($t,'/').'`?\b/i';
          $sql = preg_replace($re, $prefix.'_'.$t, $sql);
        }
        return $sql;
      };

      $ddl     = $prefixTable($schema);
      $seedSql = $prefixTable($seed);
      $userSql = $prefixTable($payload);

      try {
        // IMPORTANT: pas de transaction autour du DDL (MySQL commit implicite)
        foreach (array_filter(array_map('trim', explode(';', $ddl))) as $sql) {
          if ($sql!=='') $pdo->exec($sql);
        }
        foreach (array_filter(array_map('trim', explode(';', $seedSql))) as $sql) {
          if ($sql!=='') $pdo->exec($sql);
        }

        // Requête utilisateur
        $st = $pdo->query($userSql);
        $rowsUser = $st ? $st->fetchAll(PDO::FETCH_ASSOC) : [];

        // Expected (CSV ou SQL)
        $expectedRows = [];
        if ($expectedSql !== null) {
          $expSqlPrefixed = $prefixTable($expectedSql);
          $st2 = $pdo->query($expSqlPrefixed);
          $expectedRows = $st2 ? $st2->fetchAll(PDO::FETCH_ASSOC) : [];
        } else {
          $lines = preg_split('/\R/', trim($expectedCsv));
          $hdr   = str_getcsv(array_shift($lines));
          foreach ($lines as $ln) {
            if (trim($ln)==='') continue;
            $expectedRows[] = array_combine($hdr, str_getcsv($ln));
          }
        }

        // Comparaison
        $ok = json_encode(norm_rows($rowsUser),JSON_UNESCAPED_UNICODE)
           === json_encode(norm_rows($expectedRows),JSON_UNESCAPED_UNICODE);
        $feedback = $ok ? 'Requête correcte ✅' : 'Résultat incorrect ❌';
      } catch (Throwable $e) {
        // si une transaction était ouverte par ailleurs, on la rollback en sécurité
        if (method_exists($pdo,'inTransaction') && $pdo->inTransaction()) {
          try { $pdo->rollBack(); } catch (Throwable $e2) {}
        }
        $ok = false;
        $feedback = 'Erreur SQL: '.$e->getMessage();
      } finally {
        // Nettoyage des tables temporaires (quoi qu’il arrive)
        foreach ($tbls as $t) {
          try { $pdo->exec("DROP TABLE IF EXISTS `{$prefix}_{$t}`"); } catch (Throwable $e3) {}
        }
      }
    }
  }
}
else {
  $ok=false; $feedback='Type non supporté pour le moment.';
}

// ---------- Enregistre la soumission ----------
db_exec(
  "INSERT INTO challenge_submission (attempt_id,submitted_at,payload,verdict,feedback)
   VALUES (?,?,?,?,?)",
  [$attempt_id, now_utc(), $payload, $ok?'ACCEPTED':'REJECTED', $feedback]
);

// ---------- Met à jour tentative + leaderboard si succès ----------
if ($ok) {
  $newBest = max((int)$att['best_score'], (int)$att['points']);
  $delta   = $newBest - (int)$att['best_score']; // delta à créditer

  db_exec(
    "UPDATE challenge_attempt
        SET status='PASSED',
            best_score=?,
            finished_at=NOW(),
            duration_sec=TIMESTAMPDIFF(SECOND, started_at, NOW())
      WHERE id=?",
    [$newBest, $attempt_id]
  );

  leaderboard_upsert('WEEK',  (int)$user['id'], $delta);
  leaderboard_upsert('MONTH', (int)$user['id'], $delta);
}

header('Location: result.php?attempt_id='.$attempt_id);
