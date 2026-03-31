-- Run this once on a fresh database.
-- For existing installations, run only the lizard_images CREATE at the bottom.

CREATE TABLE IF NOT EXISTS lizards (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  name           VARCHAR(255)  NOT NULL DEFAULT '',
  species        VARCHAR(255)  NOT NULL DEFAULT '',
  morph          VARCHAR(255)  NOT NULL DEFAULT '',
  locality       VARCHAR(255)  NOT NULL DEFAULT '',
  gender         VARCHAR(50)   NOT NULL DEFAULT '',
  date_of_birth  VARCHAR(50)   NOT NULL DEFAULT '',
  sire           VARCHAR(255)  NOT NULL DEFAULT '',
  dame           VARCHAR(255)  NOT NULL DEFAULT '',
  weight_g       FLOAT                  DEFAULT NULL,
  price          FLOAT                  DEFAULT NULL,
  available             TINYINT(1)    NOT NULL DEFAULT 0,
  description           TEXT          NOT NULL DEFAULT '',
  photo                 VARCHAR(1000) NOT NULL DEFAULT '',
  obtained_from         VARCHAR(255)  NOT NULL DEFAULT '',
  is_breeder            TINYINT(1)    NOT NULL DEFAULT 0,
  hide_weight_history   TINYINT(1)    NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS lizard_images (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  lizard_id  INT NOT NULL,
  filename   VARCHAR(500) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  FOREIGN KEY (lizard_id) REFERENCES lizards(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS lizard_weights (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  lizard_id  INT   NOT NULL,
  weighed_on DATE  NOT NULL,
  weight_g   FLOAT NOT NULL,
  FOREIGN KEY (lizard_id) REFERENCES lizards(id) ON DELETE CASCADE,
  UNIQUE KEY uq_lizard_date (lizard_id, weighed_on)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TRIGGER IF EXISTS trg_weights_after_insert;
CREATE TRIGGER trg_weights_after_insert
AFTER INSERT ON lizard_weights
FOR EACH ROW
  UPDATE lizards
  SET weight_g = (
    SELECT weight_g FROM lizard_weights
    WHERE lizard_id = NEW.lizard_id
    ORDER BY weighed_on DESC LIMIT 1
  )
  WHERE id = NEW.lizard_id;

DROP TRIGGER IF EXISTS trg_weights_after_delete;
CREATE TRIGGER trg_weights_after_delete
AFTER DELETE ON lizard_weights
FOR EACH ROW
  UPDATE lizards
  SET weight_g = (
    SELECT weight_g FROM lizard_weights
    WHERE lizard_id = OLD.lizard_id
    ORDER BY weighed_on DESC LIMIT 1
  )
  WHERE id = OLD.lizard_id;
