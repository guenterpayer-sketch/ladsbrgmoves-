-- LNDSBERG MOVES – Datenbankschema
-- Import z.B. über phpMyAdmin im All-Inkl KAS-Kundenmenü.

CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS content_blocks (
  content_key VARCHAR(100) PRIMARY KEY,
  label VARCHAR(255) NOT NULL,
  content_value TEXT NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category ENUM('Kinder','Jugendliche','Erwachsene') NOT NULL,
  name VARCHAR(150) NOT NULL,
  age_info VARCHAR(255) NOT NULL DEFAULT '',
  time_info VARCHAR(255) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS schedule (
  id INT AUTO_INCREMENT PRIMARY KEY,
  weekday ENUM('Mo','Di','Mi','Do','Fr','Sa','So') NOT NULL,
  time VARCHAR(20) NOT NULL,
  course_name VARCHAR(150) NOT NULL,
  age_info VARCHAR(255) NOT NULL DEFAULT '',
  trainer VARCHAR(100) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS trainers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  bio TEXT NOT NULL DEFAULT '',
  photo_path VARCHAR(255) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed: Texte (verifiziert von hiphoplandsberg.de, Stand 2026-09-10)
INSERT INTO content_blocks (content_key, label, content_value) VALUES
('site_title', 'Seitentitel (Browser-Tab)', 'LNDSBERG MOVES – Tanzschule Landsberg'),
('hero_kicker', 'Hero: Kicker-Zeile', 'EIN ANGEBOT DES TANZCENTER PAYER · LANDSBERG'),
('hero_title', 'Hero: Haupt-Überschrift', 'Dein Move. Deine Crew.'),
('hero_text', 'Hero: Beschreibungstext', 'Hip Hop, K-Pop & Urban Dance für Kids, Teens und Erwachsene. Komm vorbei und probier''s aus – kostenlos und unverbindlich.'),
('hero_cta', 'Hero: Button-Text', 'Schnupperstunde sichern'),
('stat_founded_year', 'Statistik: Gründungsjahr Tanzcenter Payer (für "X Jahre")', '2026'),
('cta_title', 'Schnupperstunden-Sektion: Überschrift', 'Schnupperstunde sichern'),
('cta_text', 'Schnupperstunden-Sektion: Text', 'Kostenlos und unverbindlich reinschnuppern – schreib uns einfach.'),
('contact_name', 'Kontakt: Firmenname', 'ADTV Tanzschule Tanzcenter Payer'),
('contact_address', 'Kontakt: Adresse', 'Max-Planck-Str. 2, 86899 Landsberg am Lech'),
('contact_phone', 'Kontakt: Telefon', '08191 / 30 67 56'),
('contact_email', 'Kontakt: E-Mail', 'mail@tanzcenter-payer.de');

-- Seed: Kurse (verifiziert von hiphoplandsberg.de)
INSERT INTO courses (category, name, age_info, time_info, sort_order) VALUES
('Kinder', 'Hip Hop Kids', '3./4. Klasse · ca. 8-10 Jahre', 'Mi 16:15 Uhr · Do 16:15 Uhr', 10),
('Kinder', 'Hip Hop Juniors', '5./6. Klasse · ca. 10-12 Jahre', 'Mi 17:30 Uhr · Do 17:30 Uhr', 20),
('Kinder', 'K-Pop Juniors', '5./6. Klasse · ca. 10-12 Jahre', 'Di 17:30 Uhr', 30),
('Jugendliche', 'Hip Hop Level 1', 'ab ca. 12 Jahre', 'Mo 15:30 Uhr · Do 17:15 Uhr · Fr 15:00 Uhr', 10),
('Jugendliche', 'Hip Hop Level 2', 'ab ca. 12 Jahre', 'Mo 16:45 Uhr · Fr 16:10 Uhr', 20),
('Jugendliche', 'Hip Hop Level 3', 'ab ca. 12 Jahre', 'Mo 17:45 Uhr', 30),
('Jugendliche', 'K-Pop Level 1', 'ab ca. 12 Jahre', 'Do 16:45 Uhr', 40),
('Jugendliche', 'K-Pop Level 2', 'ab ca. 12 Jahre', 'Mi 17:30 Uhr', 50),
('Jugendliche', 'K-Pop Level 3', 'ab ca. 12 Jahre', 'Mo 17:30 Uhr', 60),
('Jugendliche', 'Contemporary Level 1', 'ab ca. 16 Jahre', 'Do 18:30 Uhr', 70),
('Jugendliche', 'Contemporary Level 2', 'ab ca. 16 Jahre', 'Di 20:15 Uhr', 80),
('Erwachsene', 'Streetdance', 'ab 18 Jahre', 'Di 19:05 Uhr', 10),
('Erwachsene', 'Breakdance', 'ab 10 bzw. ab 13 Jahre', 'Mi 16:15 Uhr · Mi 17:20 Uhr', 20);

-- Seed: Stundenplan (verifiziert von hiphoplandsberg.de)
INSERT INTO schedule (weekday, time, course_name, age_info, trainer, sort_order) VALUES
('Mo', '15:30 Uhr', 'Hip Hop Level 1', 'Jugendliche ab ca. 12 Jahre', 'Mila', 10),
('Mo', '16:45 Uhr', 'Hip Hop Level 2', 'Jugendliche ab ca. 12 Jahre', 'Mila', 20),
('Mo', '17:30 Uhr', 'K-Pop Level 3', 'Jugendliche ab ca. 12 Jahre', 'Ann', 30),
('Mo', '17:45 Uhr', 'Hip Hop Level 3', 'Jugendliche ab ca. 12 Jahre', 'Mila', 40),
('Di', '17:15 Uhr', 'K-Pop Juniors', 'ca. 10-12 Jahre', 'Mila', 10),
('Di', '19:05 Uhr', 'Streetdance', 'ab 18 Jahre', 'Laura', 20),
('Di', '20:15 Uhr', 'Contemporary Level 2', 'ab ca. 16 Jahre', 'Fenja', 30),
('Mi', '16:15 Uhr', 'Hip Hop Kids', '3./4. Klasse (8-ca. 10 Jahre)', 'Mila', 10),
('Mi', '16:15 Uhr', 'Breakdance', 'ab 10 Jahre', 'Jonas', 20),
('Mi', '17:20 Uhr', 'Breakdance', 'ab 13 Jahre', 'Jonas', 30),
('Mi', '17:30 Uhr', 'Hip Hop Juniors', '5./6. Klasse (10-ca. 12 Jahre)', 'Mila', 40),
('Mi', '17:30 Uhr', 'K-Pop Level 2', 'Jugendliche ab 12 Jahre', 'Nora', 50),
('Do', '16:15 Uhr', 'Hip Hop Kids', '3./4. Klasse (8-ca. 10 Jahre)', 'Mila', 10),
('Do', '16:45 Uhr', 'K-Pop Level 1', 'Jugendliche ab ca. 12 Jahre', 'Ann', 20),
('Do', '17:15 Uhr', 'Hip Hop Level 1', 'Jugendliche ab ca. 12 Jahre', 'Mila', 30),
('Do', '17:30 Uhr', 'Hip Hop Juniors', '5./6. Klasse (10-ca. 12 Jahre)', 'Nora', 40),
('Do', '18:30 Uhr', 'Contemporary Level 1', 'Jugendliche ab ca. 16 Jahre', 'Fenja', 50),
('Fr', '15:00 Uhr', 'Hip Hop Level 1', 'Jugendliche ab ca. 12 Jahre', 'Ann', 10),
('Fr', '16:10 Uhr', 'Hip Hop Level 2', 'Jugendliche ab ca. 12 Jahre', 'Ann', 20);

-- Seed: Trainer (Fotos nur, wo auf der bestehenden Seite vorhanden; Bio-Texte fehlen noch – im Adminbereich nachtragen)
INSERT INTO trainers (name, bio, photo_path, sort_order) VALUES
('Mila', '', 'assets/img/trainers/mila.png', 10),
('Jonas', '', 'assets/img/trainers/jonas.png', 20),
('Ann', '', '', 30),
('Nora', '', 'assets/img/trainers/nora.png', 40),
('Fenja', '', '', 50),
('Laura', '', '', 60);
