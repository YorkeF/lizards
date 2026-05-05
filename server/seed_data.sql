-- seed_data.sql
-- Run AFTER schema_fresh.sql to populate lizards and their weight history.
-- Fully idempotent — INSERT IGNORE skips rows that already exist.

-- ── Lizards ──────────────────────────────────────────────────────────────────

INSERT IGNORE INTO lizards (name, species, morph, locality, gender, date_of_birth, sire, dame, weight_g, price, available, description, photo, obtained_from) VALUES
  ('Kiwi',         'Leachianus', '', '62.5% Nuu Ana / 37.5% Nuu Ami',       'Female', '2022-09-05', '', '', 156,  NULL, 0, '', '', 'Rican Geckos'),
  ('Mango',        'Leachianus', '', 'Nuu Ana',                              'Female', '2023-06-01', '', '', 114,  NULL, 0, '', '', 'Rehomed from Cat''s Critters'),
  ('Swamp',        'Leachianus', '', 'GT x Nuu Ami',                         'Female', '2023-09-15', '', '', 160,  NULL, 0, '', '', 'St Louis Redditor'),
  ('Spaghetti',    'Leachianus', '', 'Moro',                                 'Female', '2022-06-01', '', '', 174,  NULL, 0, '', '', ''),
  ('Meatball',     'Leachianus', '', 'Moro',                                 'Male',   '2022-06-01', '', '', 266,  NULL, 0, '', '', ''),
  ('Marsh',        'Leachianus', '', 'Carl Vargas Sakura',                   'Male',   '2021-01-01', '', '', 220,  NULL, 0, '', '', ''),
  ('Dewi',         'Leachianus', '', 'Poindimie',                            'Female', '2024-07-15', '', '', 44,   NULL, 0, '', '', ''),
  ('Bayou',        'Leachianus', '', 'Pine Island',                          'Female', '2021-01-01', '', '', NULL, NULL, 0, '', '', 'S&A Exotics'),
  ('Beetle',       'Leachianus', '', '',                                     '',       '',           '', '', NULL, NULL, 0, '', '', 'Adam Lieberman'),
  ('Moth',         'Leachianus', '', '',                                     '',       '',           '', '', NULL, NULL, 0, '', '', 'Adam Lieberman'),
  ('Snowy',        'Leachianus', '', '',                                     '',       '',           '', '', NULL, NULL, 0, '', '', ''),
  ('Jormungandr',  'Leachianus', '', '50% SHCL / 50% Troger',               'Male',   '',           '', '', NULL, NULL, 0, '', '', 'Adam Lieberman'),
  ('Hali',         'Leachianus', '', 'MT Koghis, 50% Troger / 50% Friedel', 'Female', '2024-01-01', '', '', NULL, NULL, 0, '', '', 'Garden State Exotics'),
  ('Llewellyn',    'Leachianus', '', 'Poindimie',                            '',       '',           '', '', NULL, NULL, 0, '', '', ''),
  ('Tadhg',        'Chahoua',    '', 'Pine Island',                          'Male',   '',           '', '', NULL, NULL, 0, '', '', '');

-- ── Weight history ────────────────────────────────────────────────────────────
-- Triggers are dropped here to prevent conflicts during bulk insert,
-- then recreated afterwards. The final UPDATE syncs lizards.weight_g.

DROP TRIGGER IF EXISTS trg_weights_after_insert;
DROP TRIGGER IF EXISTS trg_weights_after_delete;

INSERT IGNORE INTO lizard_weights (lizard_id, weighed_on, weight_g)
SELECT l.id, w.weighed_on, w.weight_g
FROM lizards l
INNER JOIN (

  -- Kiwi
  SELECT 'Kiwi' AS name, '2024-09-26' AS weighed_on, 54  AS weight_g UNION ALL
  SELECT 'Kiwi', '2024-10-06',  47  UNION ALL
  SELECT 'Kiwi', '2024-10-10',  46  UNION ALL
  SELECT 'Kiwi', '2024-10-18',  48  UNION ALL
  SELECT 'Kiwi', '2024-10-23',  48  UNION ALL
  SELECT 'Kiwi', '2024-10-26',  54  UNION ALL
  SELECT 'Kiwi', '2024-10-29',  50  UNION ALL
  SELECT 'Kiwi', '2024-11-02',  52  UNION ALL
  SELECT 'Kiwi', '2024-11-05',  50  UNION ALL
  SELECT 'Kiwi', '2024-11-22',  56  UNION ALL
  SELECT 'Kiwi', '2024-12-05',  66  UNION ALL
  SELECT 'Kiwi', '2024-12-29',  74  UNION ALL
  SELECT 'Kiwi', '2025-01-15',  78  UNION ALL
  SELECT 'Kiwi', '2025-01-21',  74  UNION ALL
  SELECT 'Kiwi', '2025-02-17',  80  UNION ALL
  SELECT 'Kiwi', '2025-03-07',  86  UNION ALL
  SELECT 'Kiwi', '2025-03-23',  100 UNION ALL
  SELECT 'Kiwi', '2025-04-07',  102 UNION ALL
  SELECT 'Kiwi', '2025-04-19',  110 UNION ALL
  SELECT 'Kiwi', '2025-08-07',  122 UNION ALL
  SELECT 'Kiwi', '2025-08-24',  130 UNION ALL
  SELECT 'Kiwi', '2025-09-24',  154 UNION ALL
  SELECT 'Kiwi', '2025-10-29',  156 UNION ALL

  -- Mango
  SELECT 'Mango', '2025-08-07',  96  UNION ALL
  SELECT 'Mango', '2025-08-24',  96  UNION ALL
  SELECT 'Mango', '2025-09-21',  114 UNION ALL
  SELECT 'Mango', '2025-09-24',  122 UNION ALL
  SELECT 'Mango', '2025-10-12',  114 UNION ALL
  SELECT 'Mango', '2025-10-29',  126 UNION ALL
  SELECT 'Mango', '2025-12-15',  136 UNION ALL
  SELECT 'Mango', '2025-12-29',  122 UNION ALL

  -- Swamp
  SELECT 'Swamp', '2025-01-07',  58  UNION ALL
  SELECT 'Swamp', '2025-01-21',  62  UNION ALL
  SELECT 'Swamp', '2025-02-17',  78  UNION ALL
  SELECT 'Swamp', '2025-03-09',  84  UNION ALL
  SELECT 'Swamp', '2025-03-23',  96  UNION ALL
  SELECT 'Swamp', '2025-04-07',  106 UNION ALL
  SELECT 'Swamp', '2025-04-19',  106 UNION ALL
  SELECT 'Swamp', '2025-08-07',  150 UNION ALL
  SELECT 'Swamp', '2025-08-24',  186 UNION ALL
  SELECT 'Swamp', '2025-09-21',  164 UNION ALL
  SELECT 'Swamp', '2025-09-25',  176 UNION ALL
  SELECT 'Swamp', '2025-10-07',  160 UNION ALL
  SELECT 'Swamp', '2025-10-29',  172 UNION ALL
  SELECT 'Swamp', '2025-12-29',  168 UNION ALL

  -- Spaghetti
  SELECT 'Spaghetti', '2025-08-24',  174 UNION ALL
  SELECT 'Spaghetti', '2025-09-24',  190 UNION ALL
  SELECT 'Spaghetti', '2025-09-25',  184 UNION ALL
  SELECT 'Spaghetti', '2025-10-06',  186 UNION ALL
  SELECT 'Spaghetti', '2025-10-29',  192 UNION ALL

  -- Meatball
  SELECT 'Meatball', '2025-08-24',  214 UNION ALL
  SELECT 'Meatball', '2025-08-30',  220 UNION ALL
  SELECT 'Meatball', '2025-09-16',  234 UNION ALL
  SELECT 'Meatball', '2025-09-21',  230 UNION ALL
  SELECT 'Meatball', '2025-09-24',  258 UNION ALL
  SELECT 'Meatball', '2025-09-25',  236 UNION ALL
  SELECT 'Meatball', '2025-10-06',  258 UNION ALL
  SELECT 'Meatball', '2025-10-29',  266 UNION ALL

  -- Marsh
  SELECT 'Marsh', '2025-09-21',  220 UNION ALL
  SELECT 'Marsh', '2025-10-06',  244 UNION ALL
  SELECT 'Marsh', '2025-10-29',  238 UNION ALL

  -- Dewi
  SELECT 'Dewi', '2025-09-21',  44 UNION ALL
  SELECT 'Dewi', '2025-10-06',  40 UNION ALL
  SELECT 'Dewi', '2025-10-29',  52 UNION ALL
  SELECT 'Dewi', '2025-12-29',  60 UNION ALL

  -- Jormungandr
  SELECT 'Jormungandr', '2025-10-15',  48 UNION ALL
  SELECT 'Jormungandr', '2025-10-29',  56 UNION ALL
  SELECT 'Jormungandr', '2025-11-20',  58 UNION ALL
  SELECT 'Jormungandr', '2025-12-13',  76 UNION ALL
  SELECT 'Jormungandr', '2025-12-29',  64 UNION ALL

  -- Hali
  SELECT 'Hali', '2025-10-16',  50 UNION ALL
  SELECT 'Hali', '2025-10-29',  50 UNION ALL
  SELECT 'Hali', '2025-12-13',  62 UNION ALL
  SELECT 'Hali', '2025-12-29',  42 UNION ALL

  -- Bayou
  SELECT 'Bayou', '2025-10-29',  206 UNION ALL
  SELECT 'Bayou', '2025-12-15',  212 UNION ALL
  SELECT 'Bayou', '2025-12-29',  190 UNION ALL

  -- Moth
  SELECT 'Moth', '2025-11-20',  48 UNION ALL
  SELECT 'Moth', '2025-12-29',  50 UNION ALL

  -- Beetle
  SELECT 'Beetle', '2025-11-20',  54 UNION ALL

  -- Llewellyn
  SELECT 'Llewellyn', '2025-12-15',  52 UNION ALL
  SELECT 'Llewellyn', '2025-12-29',  46 UNION ALL

  -- Snowy
  SELECT 'Snowy', '2025-12-15',  52 UNION ALL
  SELECT 'Snowy', '2025-12-29',  44 UNION ALL

  -- Tadhg
  SELECT 'Tadhg', '2025-12-15',  56 UNION ALL
  SELECT 'Tadhg', '2025-12-29',  52

) w ON l.name = w.name;

-- Sync lizards.weight_g to the most recent entry for each lizard
UPDATE lizards l
SET l.weight_g = (
  SELECT weight_g FROM lizard_weights
  WHERE lizard_id = l.id
  ORDER BY weighed_on DESC LIMIT 1
)
WHERE l.id > 0 AND EXISTS (SELECT 1 FROM lizard_weights WHERE lizard_id = l.id);

-- Recreate triggers
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
