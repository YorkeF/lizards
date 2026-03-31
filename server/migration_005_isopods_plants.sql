-- migration_005_isopods_plants.sql
-- Adds isopods and plants tables with image support.

CREATE TABLE IF NOT EXISTS isopods (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  name            VARCHAR(255) NOT NULL DEFAULT '',
  scientific_name VARCHAR(255) NOT NULL DEFAULT '',
  description     TEXT         NOT NULL DEFAULT '',
  price           FLOAT                 DEFAULT NULL,
  available       TINYINT(1)   NOT NULL DEFAULT 0,
  obtained_from   VARCHAR(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS isopod_images (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  isopod_id  INT NOT NULL,
  filename   VARCHAR(500) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  FOREIGN KEY (isopod_id) REFERENCES isopods(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS plants (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  name            VARCHAR(255) NOT NULL DEFAULT '',
  scientific_name VARCHAR(255) NOT NULL DEFAULT '',
  description     TEXT         NOT NULL DEFAULT '',
  price           FLOAT                 DEFAULT NULL,
  available       TINYINT(1)   NOT NULL DEFAULT 0,
  care_level      VARCHAR(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS plant_images (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  plant_id   INT NOT NULL,
  filename   VARCHAR(500) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  FOREIGN KEY (plant_id) REFERENCES plants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
