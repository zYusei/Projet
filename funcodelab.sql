-- ===========================
-- FunCodeLab bootstrap (safe)
-- ===========================

-- Create DB
CREATE DATABASE IF NOT EXISTS funcodelab
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE funcodelab;

-- 1) Users
CREATE TABLE IF NOT EXISTS users (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username          VARCHAR(32)  NOT NULL,
  email             VARCHAR(190) NOT NULL,
  password_hash     VARCHAR(255) NOT NULL,
  created_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  last_login_at     DATETIME     NULL,
  is_email_verified TINYINT(1)   NOT NULL DEFAULT 0,
  UNIQUE KEY uniq_email (email),
  UNIQUE KEY uniq_username (username),
  INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- 2) Login attempts (optional)
CREATE TABLE IF NOT EXISTS login_attempts (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email      VARCHAR(190) NOT NULL,
  ip         VARBINARY(16) NULL,
  success    TINYINT(1)   NOT NULL,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_email_created (email, created_at)
) ENGINE=InnoDB;

-- 3) Simple "badges" feed for homepage demo
CREATE TABLE IF NOT EXISTS badges (
  id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user  VARCHAR(64)  NOT NULL,
  badge VARCHAR(128) NOT NULL,
  date  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4) Badge catalog
CREATE TABLE IF NOT EXISTS badge_definitions (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code        VARCHAR(64)  NOT NULL UNIQUE,
  name        VARCHAR(128) NOT NULL,
  icon        VARCHAR(16)  NULL,
  color       VARCHAR(16)  NULL,
  description TEXT         NULL,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 5) User ↔ badges
CREATE TABLE IF NOT EXISTS user_badges (
  user_id   INT UNSIGNED NOT NULL,
  badge_id  INT UNSIGNED NOT NULL,
  earned_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, badge_id),
  KEY idx_earned (earned_at),
  CONSTRAINT fk_ub_user  FOREIGN KEY (user_id)  REFERENCES users(id)             ON DELETE CASCADE,
  CONSTRAINT fk_ub_badge FOREIGN KEY (badge_id) REFERENCES badge_definitions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6) XP / level
CREATE TABLE IF NOT EXISTS user_stats (
  user_id    INT UNSIGNED NOT NULL PRIMARY KEY,
  level      INT          NOT NULL DEFAULT 1,
  xp         INT          NOT NULL DEFAULT 0,
  xp_total   INT          NOT NULL DEFAULT 0,
  updated_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_us_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7) Challenges
CREATE TABLE IF NOT EXISTS challenge (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(160) NOT NULL,
  slug  VARCHAR(160) NOT NULL UNIQUE,
  type  ENUM('CODE','SQL','CTF') NOT NULL,
  difficulty ENUM('Facile','Intermédiaire','Difficile') NOT NULL,
  points INT NOT NULL DEFAULT 100,
  time_limit_sec INT NOT NULL DEFAULT 900,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  starts_at DATETIME NULL,
  ends_at   DATETIME NULL,
  prompt_md TEXT NOT NULL,
  metadata JSON NULL
) ENGINE=InnoDB;

-- 8) Challenge assets
CREATE TABLE IF NOT EXISTS challenge_asset (
  id INT AUTO_INCREMENT PRIMARY KEY,
  challenge_id INT NOT NULL,
  kind ENUM('CODE_TESTS','SQL_SCHEMA','SQL_SEED','SQL_EXPECT','CTF_FLAG','ATTACHMENT') NOT NULL,
  content MEDIUMTEXT NOT NULL,
  FOREIGN KEY (challenge_id) REFERENCES challenge(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9) Attempts
CREATE TABLE IF NOT EXISTS challenge_attempt (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  challenge_id INT NOT NULL,
  user_id INT NOT NULL,
  started_at DATETIME NOT NULL,
  expires_at DATETIME NOT NULL,
  finished_at DATETIME NULL,
  status ENUM('IN_PROGRESS','PASSED','FAILED','EXPIRED') NOT NULL DEFAULT 'IN_PROGRESS',
  best_score INT NOT NULL DEFAULT 0,
  duration_sec INT NULL,
  FOREIGN KEY (challenge_id) REFERENCES challenge(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10) Submissions history
CREATE TABLE IF NOT EXISTS challenge_submission (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  attempt_id BIGINT NOT NULL,
  submitted_at DATETIME NOT NULL,
  payload MEDIUMTEXT NOT NULL,
  verdict ENUM('PENDING','ACCEPTED','REJECTED') NOT NULL,
  feedback TEXT NULL,
  FOREIGN KEY (attempt_id) REFERENCES challenge_attempt(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 11) Materialized leaderboards
CREATE TABLE IF NOT EXISTS challenge_leaderboard (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  period ENUM('WEEK','MONTH') NOT NULL,
  period_start DATE NOT NULL,
  period_end   DATE NOT NULL,
  user_id INT NOT NULL,
  score  INT NOT NULL,
  rank   INT NULL,
  UNIQUE KEY uniq_user_period (period, period_start, user_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS chat_messages (
   id INT AUTO_INCREMENT PRIMARY KEY,
   user_id INT NULL,
   username VARCHAR(80) NOT NULL,
   message TEXT NOT NULL,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
CREATE INDEX idx_chat_created ON chat_messages(created_at);

-- messages (threads, édition, pièces jointes, modération)
ALTER TABLE chat_messages
  ADD parent_id INT NULL,
  ADD edited_at DATETIME NULL,
  ADD attachment_url VARCHAR(255) NULL,
  ADD is_deleted TINYINT(1) DEFAULT 0,
  ADD is_flagged TINYINT(1) DEFAULT 0;

CREATE INDEX idx_chat_parent ON chat_messages(parent_id);
CREATE INDEX idx_chat_uid_time ON chat_messages(user_id, created_at);

-- réactions
CREATE TABLE IF NOT EXISTS chat_reactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  message_id INT NOT NULL,
  user_id INT NOT NULL,
  emoji VARCHAR(8) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_react (message_id, user_id, emoji),
  FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- signalements
CREATE TABLE IF NOT EXISTS chat_reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  message_id INT NOT NULL,
  reporter_id INT NOT NULL,
  reason VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- liste d’insultes / mots interdits (extensible)
CREATE TABLE IF NOT EXISTS bad_words (
  id INT AUTO_INCREMENT PRIMARY KEY,
  word VARCHAR(128) UNIQUE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- présence (simple)
CREATE TABLE IF NOT EXISTS chat_presence (
  user_id INT PRIMARY KEY,
  username VARCHAR(80) NOT NULL,
  last_seen TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  typing_until TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- Safe schema tweaks (idempotent) ----------
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS avatar_url         VARCHAR(255) NULL AFTER email,
  ADD COLUMN IF NOT EXISTS is_email_verified  TINYINT(1)   NOT NULL DEFAULT 0 AFTER last_login_at,
  ADD COLUMN IF NOT EXISTS email_verified_at  DATETIME     NULL AFTER is_email_verified,
  ADD COLUMN IF NOT EXISTS verify_token_hash  CHAR(64)     NULL AFTER password_hash,
  ADD COLUMN IF NOT EXISTS verify_token_expires DATETIME   NULL AFTER verify_token_hash;

-- ---------- Demo data (idempotent) ----------

-- Homepage badges feed
INSERT INTO badges (user, badge) VALUES
('Alice','Badge HTML Débutant'),
('Bob','Badge CSS Intermédiaire'),
('Chloé','Badge JS Avancé')
ON DUPLICATE KEY UPDATE badge=VALUES(badge);

-- Badge definitions
INSERT IGNORE INTO badge_definitions (code,name,icon,color,description) VALUES
('bienvenue','Bienvenue !','🎉','#4ade80','Inscription réussie'),
('premier_quiz','Premier quiz','🥇','#60a5fa','Tu as terminé ton premier quiz'),
('score_80','Score 80%+','💡','#fbbf24','Score ≥ 80% sur un quiz'),
('score_100','Score parfait','🏆','#f59e0b','Score 100% sur un quiz');

-- Demo users (password = test1234)
INSERT INTO users (username,email,password_hash,created_at,avatar_url,is_email_verified,email_verified_at)
VALUES
('Alice','alice@example.com','$2y$10$2qgN0G8bIY7m5v4r7m2QVu8mL5G4k1Vx3tY9x7jv0S6g5G1Yw1/2m',NOW(),NULL,1,NOW()),
('Bob','bob@example.com','$2y$10$2qgN0G8bIY7m5v4r7m2QVu8mL5G4k1Vx3tY9x7jv0S6g5G1Yw1/2m',NOW(),NULL,0,NULL),
('Charlie','charlie@example.com','$2y$10$2qgN0G8bIY7m5v4r7m2QVu8mL5G4k1Vx3tY9x7jv0S6g5G1Yw1/2m',NOW(),NULL,0,NULL)
ON DUPLICATE KEY UPDATE username=VALUES(username);

-- Challenges (unique by slug)
INSERT INTO challenge
(title,slug,type,difficulty,points,time_limit_sec,is_active,prompt_md)
VALUES
('CTF — Flag dans la note','ctf-note-1','CTF','Facile',100,600,1,'Télécharge note.txt et trouve le flag FCL{...} (indice: commentaires HTML).')
ON DUPLICATE KEY UPDATE title=VALUES(title);

INSERT INTO challenge_asset (challenge_id,kind,content)
SELECT c.id,'CTF_FLAG','FCL{hidden_in_comments}'
FROM challenge c
LEFT JOIN challenge_asset ca ON ca.challenge_id=c.id AND ca.kind='CTF_FLAG'
WHERE c.slug='ctf-note-1' AND ca.id IS NULL;

INSERT INTO challenge
(title,slug,type,difficulty,points,time_limit_sec,is_active,prompt_md)
VALUES
('CTF — Regex flag','ctf-regex-1','CTF','Intermédiaire',150,900,1,'Trouve le flag FCL{...}. Il suit un motif particulier.')
ON DUPLICATE KEY UPDATE title=VALUES(title);

INSERT INTO challenge_asset (challenge_id,kind,content)
SELECT c.id,'CTF_FLAG','regex:^FCL\\{[a-z0-9_]{8,}\\}$'
FROM challenge c
LEFT JOIN challenge_asset ca ON ca.challenge_id=c.id AND ca.kind='CTF_FLAG'
WHERE c.slug='ctf-regex-1' AND ca.id IS NULL;

INSERT INTO challenge
(title,slug,type,difficulty,points,time_limit_sec,is_active,prompt_md)
VALUES
('SQL — Utilisateurs premium','sql-premium-1','SQL','Facile',120,900,1,'Afficher les `name` des utilisateurs premium, ordre alphabétique.')
ON DUPLICATE KEY UPDATE title=VALUES(title);

-- === Dataset pour le challenge SQL (schema + seed + expected) ===
SET @cid := (SELECT id FROM challenge WHERE slug='sql-premium-1');

-- Nettoyage idempotent
DELETE FROM challenge_asset
WHERE challenge_id=@cid AND kind IN ('SQL_SCHEMA','SQL_SEED','SQL_EXPECT');

-- Schéma SQLite pour l'évaluateur
INSERT INTO challenge_asset (challenge_id,kind,content) VALUES
(@cid,'SQL_SCHEMA','CREATE TABLE demo_users (id INTEGER PRIMARY KEY, name TEXT NOT NULL, premium INTEGER NOT NULL);');

-- Données de test
INSERT INTO challenge_asset (challenge_id,kind,content) VALUES
(@cid,'SQL_SEED','INSERT INTO demo_users (id,name,premium) VALUES
  (1,"Alice",1),
  (2,"Bob",0),
  (3,"Charlie",1),
  (4,"Diane",0),
  (5,"Zoé",1);');

-- Résultat attendu (CSV simple: entêtes + lignes)
INSERT INTO challenge_asset (challenge_id,kind,content) VALUES
(@cid,'SQL_EXPECT','name\nAlice\nCharlie\nZoé');

-- ---------- Demo attempts (expires_at is required) ----------
INSERT INTO challenge_attempt
(challenge_id,user_id,started_at,expires_at,finished_at,status,best_score,duration_sec)
SELECT c.id,u.id,NOW(),DATE_ADD(NOW(),INTERVAL 15 MINUTE),NOW(),'PASSED',100,300
FROM challenge c JOIN users u
WHERE c.slug='sql-premium-1' AND u.email='alice@example.com'
LIMIT 1;

INSERT INTO challenge_attempt
(challenge_id,user_id,started_at,expires_at,finished_at,status,best_score,duration_sec)
SELECT c.id,u.id,NOW(),DATE_ADD(NOW(),INTERVAL 15 MINUTE),NOW(),'PASSED',90,420
FROM challenge c JOIN users u
WHERE c.slug='sql-premium-1' AND u.email='bob@example.com'
LIMIT 1;

INSERT INTO challenge_attempt
(challenge_id,user_id,started_at,expires_at,finished_at,status,best_score,duration_sec)
SELECT c.id,u.id,NOW(),DATE_ADD(NOW(),INTERVAL 15 MINUTE),NOW(),'PASSED',150,600
FROM challenge c JOIN users u
WHERE c.slug='ctf-regex-1' AND u.email='alice@example.com'
LIMIT 1;

INSERT INTO challenge_attempt
(challenge_id,user_id,started_at,expires_at,finished_at,status,best_score,duration_sec)
SELECT c.id,u.id,NOW(),DATE_ADD(NOW(),INTERVAL 15 MINUTE),NOW(),'PASSED',70,700
FROM challenge c JOIN users u
WHERE c.slug='ctf-regex-1' AND u.email='charlie@example.com'
LIMIT 1;

-- ---------- Weekly leaderboard build (idempotent) ----------
SET @week_start := DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY);
SET @week_end   := DATE_ADD(@week_start, INTERVAL 6 DAY);

INSERT INTO challenge_leaderboard (period,period_start,period_end,user_id,score,rank)
SELECT 'WEEK', @week_start, @week_end, a.user_id, SUM(a.best_score), NULL
FROM challenge_attempt a
WHERE a.started_at >= @week_start
GROUP BY a.user_id
ON DUPLICATE KEY UPDATE score=VALUES(score);

-- Dense rank
SET @r := 0; SET @prev := -1;
UPDATE challenge_leaderboard cl
JOIN (
  SELECT user_id, score,
         @r := @r + (score <> @prev) AS rnk,
         @prev := score
  FROM challenge_leaderboard
  WHERE period='WEEK' AND period_start=@week_start
  ORDER BY score DESC
) t ON t.user_id = cl.user_id
SET cl.rank = t.rnk
WHERE cl.period='WEEK' AND cl.period_start=@week_start;

-- ---------- Monthly leaderboard build (idempotent) ----------
SET @month_start := DATE_FORMAT(CURDATE(), '%Y-%m-01');
SET @month_end   := LAST_DAY(CURDATE());

INSERT INTO challenge_leaderboard (period, period_start, period_end, user_id, score, rank)
SELECT 'MONTH', @month_start, @month_end, a.user_id, SUM(a.best_score), NULL
FROM challenge_attempt a
WHERE a.started_at >= @month_start
  AND a.started_at < DATE_ADD(@month_end, INTERVAL 1 DAY)
GROUP BY a.user_id
ON DUPLICATE KEY UPDATE score = VALUES(score);

SET @r := 0; SET @prev := -1;
UPDATE challenge_leaderboard cl
JOIN (
  SELECT user_id, score,
         @r := @r + (score <> @prev) AS rnk,
         @prev := score
  FROM challenge_leaderboard
  WHERE period='MONTH' AND period_start=@month_start
  ORDER BY score DESC
) t ON t.user_id = cl.user_id
SET cl.rank = t.rnk
WHERE cl.period='MONTH' AND cl.period_start=@month_start;
