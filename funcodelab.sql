-- Crée la base (change le charset si besoin)
CREATE DATABASE IF NOT EXISTS funcodelab
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE funcodelab;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username         VARCHAR(32)  NOT NULL,
  email            VARCHAR(190) NOT NULL,
  password_hash    VARCHAR(255) NOT NULL,
  created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  last_login_at    DATETIME     NULL,
  is_email_verified TINYINT(1)  NOT NULL DEFAULT 0,
  UNIQUE KEY uniq_email (email),
  UNIQUE KEY uniq_username (username),
  INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- (Optionnel mais utile) journal des tentatives
CREATE TABLE IF NOT EXISTS login_attempts (
  id        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email     VARCHAR(190) NOT NULL,
  ip        VARBINARY(16) NULL,
  success   TINYINT(1) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_email_created (email, created_at)
) ENGINE=InnoDB;

-- Table pour tes badges de l'accueil (vu que index.php affiche $badges)
CREATE TABLE IF NOT EXISTS badges (
  id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user   VARCHAR(64) NOT NULL,
  badge  VARCHAR(128) NOT NULL,
  date   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Seed de démo (supprime si inutile)
INSERT INTO badges (user, badge) VALUES
('Alice', 'Badge HTML Débutant'),
('Bob',   'Badge CSS Intermédiaire'),
('Chloé', 'Badge JS Avancé');
