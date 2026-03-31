-- migration_006_paired_with.sql
-- Adds paired_with field to lizards for breeder pair tracking.

ALTER TABLE lizards
  ADD COLUMN paired_with VARCHAR(255) NOT NULL DEFAULT '';
