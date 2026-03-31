-- migration_004_breeder_flags.sql
-- Adds is_breeder and hide_weight_history flags to lizards table.

ALTER TABLE lizards
  ADD COLUMN is_breeder          TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN hide_weight_history TINYINT(1) NOT NULL DEFAULT 0;
