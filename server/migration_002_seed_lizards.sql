-- migration_002_seed_lizards.sql
-- Inserts all lizards into the lizards table.
-- Uses INSERT IGNORE so re-running this is safe if records already exist.
-- Images are left empty to be added via the admin panel.

INSERT IGNORE INTO lizards (name, species, morph, locality, gender, date_of_birth, sire, dame, weight_g, price, available, description, photo, obtained_from) VALUES
  ('Kiwi',         'Leachianus', '', '62.5% Nuu Ana / 37.5% Nuu Ami',      'Female', '2022-09-05', '', '', 156,  NULL, 0, '', '', 'Rican Geckos'),
  ('Mango',        'Leachianus', '', 'Nuu Ana',                             'Female', '2023-06-01', '', '', 114,  NULL, 0, '', '', 'Rehomed from Cat''s Critters'),
  ('Swamp',        'Leachianus', '', 'GT x Nuu Ami',                        'Female', '2023-09-15', '', '', 160,  NULL, 0, '', '', 'St Louis Redditor'),
  ('Spaghetti',    'Leachianus', '', 'Moro',                                'Female', '2022-06-01', '', '', 174,  NULL, 0, '', '', ''),
  ('Meatball',     'Leachianus', '', 'Moro',                                'Male',   '2022-06-01', '', '', 266,  NULL, 0, '', '', ''),
  ('Marsh',        'Leachianus', '', 'Carl Vargas Sakura',                  'Male',   '2021-01-01', '', '', 220,  NULL, 0, '', '', ''),
  ('Dewi',         'Leachianus', '', 'Poindimie',                           'Female', '2024-07-15', '', '', 44,   NULL, 0, '', '', ''),
  ('Bayou',        'Leachianus', '', 'Pine Island',                         'Female', '2021-01-01', '', '', NULL, NULL, 0, '', '', 'S&A Exotics'),
  ('Beetle',       'Leachianus', '', '',                                    '',       '',           '', '', NULL, NULL, 0, '', '', 'Adam Lieberman'),
  ('Moth',         'Leachianus', '', '',                                    '',       '',           '', '', NULL, NULL, 0, '', '', 'Adam Lieberman'),
  ('Snowy',        'Leachianus', '', '',                                    '',       '',           '', '', NULL, NULL, 0, '', '', ''),
  ('Jormungandr',  'Leachianus', '', '50% SHCL / 50% Troger',              'Male',   '',           '', '', NULL, NULL, 0, '', '', 'Adam Lieberman'),
  ('Hali',         'Leachianus', '', 'MT Koghis, 50% Troger / 50% Friedel','Female', '2024-01-01', '', '', NULL, NULL, 0, '', '', 'Garden State Exotics'),
  ('Llewellyn',    'Leachianus', '', 'Poindimie',                           '',       '',           '', '', NULL, NULL, 0, '', '', ''),
  ('Tadhg',        'Chahoua',    '', 'Pine Island',                         'Male',   '',           '', '', NULL, NULL, 0, '', '', '');
