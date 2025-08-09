-- Crée la base
CREATE DATABASE IF NOT EXISTS funcodelab
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE funcodelab;

-- 1) Utilisateurs
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

-- 2) Journal des tentatives (optionnel)
CREATE TABLE IF NOT EXISTS login_attempts (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email      VARCHAR(190) NOT NULL,
  ip         VARBINARY(16) NULL,
  success    TINYINT(1)   NOT NULL,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_email_created (email, created_at)
) ENGINE=InnoDB;

-- 3) Table "badges" de démo de l'accueil (si tu l'utilises encore)
CREATE TABLE IF NOT EXISTS badges (
  id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user  VARCHAR(64)  NOT NULL,
  badge VARCHAR(128) NOT NULL,
  date  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;  -- <-- le point-virgule manquait ici

-- 4) Catalogue des types de badges
CREATE TABLE IF NOT EXISTS badge_definitions (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code        VARCHAR(64)  NOT NULL UNIQUE,   -- ex: 'premier_quiz'
  name        VARCHAR(128) NOT NULL,
  icon        VARCHAR(16)  NULL,              -- emoji
  color       VARCHAR(16)  NULL,              -- ex: #ffd166
  description TEXT         NULL,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 5) Attribution des badges aux utilisateurs
CREATE TABLE IF NOT EXISTS user_badges (
  user_id   INT UNSIGNED NOT NULL,
  badge_id  INT UNSIGNED NOT NULL,
  earned_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, badge_id),
  KEY idx_earned (earned_at),
  CONSTRAINT fk_ub_user  FOREIGN KEY (user_id)  REFERENCES users(id)             ON DELETE CASCADE,
  CONSTRAINT fk_ub_badge FOREIGN KEY (badge_id) REFERENCES badge_definitions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6) Stats d’XP / niveau par utilisateur
CREATE TABLE IF NOT EXISTS user_stats (
  user_id    INT UNSIGNED NOT NULL PRIMARY KEY,
  level      INT          NOT NULL DEFAULT 1,
  xp         INT          NOT NULL DEFAULT 0,  -- XP dans le niveau courant
  xp_total   INT          NOT NULL DEFAULT 0,  -- cumul historique
  updated_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_us_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seeds (optionnels)
INSERT INTO badges (user, badge) VALUES
('Alice', 'Badge HTML Débutant'),
('Bob',   'Badge CSS Intermédiaire'),
('Chloé', 'Badge JS Avancé');

INSERT IGNORE INTO badge_definitions (code, name, icon, color, description) VALUES
('bienvenue',    'Bienvenue !',     '🎉', '#4ade80', 'Inscription réussie'),
('premier_quiz', 'Premier quiz',    '🥇', '#60a5fa', 'Tu as terminé ton premier quiz'),
('score_80',     'Score 80%+',      '💡', '#fbbf24', 'Score ≥ 80% sur un quiz'),
('score_100',    'Score parfait',   '🏆', '#f59e0b', 'Score 100% sur un quiz');
