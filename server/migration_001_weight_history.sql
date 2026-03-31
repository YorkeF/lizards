-- migration_001_weight_history.sql
-- Run this on existing installations to add weight history support.
-- For fresh installs, schema.sql already includes these statements.
--
-- Note: existing lizards.weight_g values are intentionally NOT migrated
-- into lizard_weights because the original measurement dates are unknown.
-- Those values will continue to display on profile pages unchanged.
-- The triggers will take over and keep weight_g current once the admin
-- records the first real dated entry for each lizard.

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
