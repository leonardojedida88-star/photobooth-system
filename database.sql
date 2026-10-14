CREATE DATABASE IF NOT EXISTS photobooth_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE photobooth_db;

CREATE TABLE IF NOT EXISTS photos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  filename VARCHAR(190) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_photos_filename (filename),
  KEY idx_photos_created_at (created_at)
) ENGINE=InnoDB;
