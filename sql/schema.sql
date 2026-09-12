-- LNDSBRG MOVES – Datenbankschema
-- Import z.B. über phpMyAdmin im All-Inkl KAS-Kundenmenü.
-- WICHTIG: Import immer mit --default-character-set=utf8mb4 (CLI) bzw.
-- Format "utf8mb4" (phpMyAdmin), sonst werden Umlaute falsch importiert.

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
  -- eindeutiger, technischer Schlüssel für die Anmelde-Verlinkung (z.B. "hiphop-l1");
  -- NULL = kein Buchungslink. NULL statt '' erlaubt mehrere Kurse ohne Slug
  -- (MySQL UNIQUE-Index lässt mehrere NULLs zu, aber nicht mehrere leere Strings).
  slug VARCHAR(60) NULL DEFAULT NULL,
  -- NimbusCloud-Kurs-ID für die Online-Anmeldung; leer = kein Buchungslink (Kurs wird ohne "Schnupperstunde"-Button angezeigt)
  nimbus_online_id VARCHAR(20) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0,
  UNIQUE KEY uniq_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS schedule (
  id INT AUTO_INCREMENT PRIMARY KEY,
  weekday ENUM('Mo','Di','Mi','Do','Fr','Sa','So') NOT NULL,
  time VARCHAR(20) NOT NULL,
  course_name VARCHAR(150) NOT NULL,
  -- optionale Verknüpfung zu einem konkreten Kurs (für den Buchungslink).
  -- NULL = freier Stundenplan-Eintrag ohne Buchung. Bewusst über eine ID statt
  -- über course_name verknüpft, da derselbe Kursname mehrfach in "courses"
  -- vorkommen kann (z.B. eine Alterstrennung wie bei Breakdance), aber jeweils
  -- eine andere NimbusCloud-Buchung meint.
  course_id INT NULL DEFAULT NULL,
  age_info VARCHAR(255) NOT NULL DEFAULT '',
  trainer VARCHAR(100) NOT NULL DEFAULT '',
  category ENUM('Kinder','Jugendliche','Erwachsene','Gemischt') NOT NULL DEFAULT 'Jugendliche',
  sort_order INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_schedule_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS trainers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  bio TEXT NOT NULL DEFAULT '',
  photo_path VARCHAR(255) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pricing_plans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  tagline VARCHAR(255) NOT NULL DEFAULT '',
  price DECIMAL(6,2) NOT NULL,
  price_unit VARCHAR(100) NOT NULL DEFAULT 'pro Monat, pro Person',
  included_text VARCHAR(500) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed: Texte (verifiziert von hiphoplandsberg.de, Stand 2026-09-10; Markenname
-- gemäß Projektbrief v3 korrigiert auf "LNDSBRG MOVES")
INSERT INTO content_blocks (content_key, label, content_value) VALUES
('site_title', 'Seitentitel (Browser-Tab)', 'LNDSBRG MOVES – Tanzschule Landsberg'),
('brand_name', 'Markenname (Logo-Text)', 'LNDSBRG MOVES'),
('brand_subtitle', 'Logo: Zeile unter dem Markennamen', 'URBAN DANCE'),
('hero_kicker', 'Hero: Badge-Zeile', 'EIN ANGEBOT DES TANZCENTER PAYER · LANDSBERG'),
('hero_title_line1', 'Hero: Überschrift Zeile 1', 'DEIN MOVE.'),
('hero_title_line2', 'Hero: Überschrift Zeile 2 (farbig)', 'DEINE CREW.'),
('hero_text', 'Hero: Beschreibungstext', 'Hip Hop, K-Pop & Urban Dance für Kids, Teens und Erwachsene. Komm vorbei und probier''s aus – kostenlos und unverbindlich.'),
('hero_cta_primary', 'Hero: Haupt-Button', 'Schnupperstunde sichern'),
('hero_cta_secondary', 'Hero: Zweiter Button', 'Kurse entdecken'),
('trainer_intro', 'Trainer-Sektion: einleitender Text', 'Unser Team bringt euch Woche für Woche in Bewegung – mit Erfahrung, Spaß an der Sache und einem Blick für jedes Level.'),
('contact_name', 'Kontakt: Firmenname', 'ADTV Tanzschule Tanzcenter Payer'),
('contact_address', 'Kontakt: Adresse', 'Max-Planck-Str. 2, 86899 Landsberg am Lech'),
('contact_phone', 'Kontakt: Telefon', '08191 / 30 67 56'),
('contact_email', 'Kontakt: E-Mail', 'mail@tanzcenter-payer.de'),
('footer_offer_text', 'Footer: Zusatzzeile', 'Ein Angebot der Tanzschule Payer · tcpayer.de'),
('pricing_intro', 'Preise: einleitender Text', 'Ein Preis im Monat, alle Kurse deiner Altersgruppe inklusive. Deine erste Stunde ist unverbindlich – gefällt''s dir nicht, zahlst du nichts.'),
('pricing_footnote', 'Preise: Fußnote (Kündigung/Kleingedrucktes)', 'Monatlich kündbar, 2 Wochen Frist zum Monatsende, schriftlich. Kein Kleingedrucktes.');

-- Seed: Kurse (Namen/Zeiten verifiziert von hiphoplandsberg.de; slug + NimbusCloud-ID
-- direkt aus dem Live-Code der Seite übernommen. "Breakdance" hat auf der Live-Seite
-- keine Anmelde-Verknüpfung, daher dort bewusst keine nimbus_online_id.)
INSERT INTO courses (category, name, age_info, time_info, slug, nimbus_online_id, sort_order) VALUES
('Kinder', 'Hip Hop Kids', '3./4. Klasse · ca. 8-10 Jahre', 'Mi 16:15 Uhr · Do 16:15 Uhr', 'hiphop-kids', '11', 10),
('Kinder', 'Hip Hop Juniors', '5./6. Klasse · ca. 10-12 Jahre', 'Mi 17:30 Uhr · Do 17:30 Uhr', 'hiphop-juniors', '12', 20),
('Kinder', 'K-Pop Juniors', '5./6. Klasse · ca. 10-12 Jahre', 'Di 17:30 Uhr', 'kpop-juniors', '114', 30),
('Jugendliche', 'Hip Hop Level 1', 'ab ca. 12 Jahre', 'Mo 15:30 Uhr · Do 17:15 Uhr · Fr 15:00 Uhr', 'hiphop-l1', '27', 10),
('Jugendliche', 'Hip Hop Level 2', 'ab ca. 12 Jahre', 'Mo 16:45 Uhr · Fr 16:10 Uhr', 'hiphop-l2', '28', 20),
('Jugendliche', 'Hip Hop Level 3', 'ab ca. 12 Jahre', 'Mo 17:45 Uhr', 'hiphop-l3', '29', 30),
('Jugendliche', 'K-Pop Level 1', 'ab ca. 12 Jahre', 'Do 16:45 Uhr', 'kpop-l1', '32', 40),
('Jugendliche', 'K-Pop Level 2', 'ab ca. 12 Jahre', 'Mi 17:30 Uhr', 'kpop-l2', '33', 50),
('Jugendliche', 'K-Pop Level 3', 'ab ca. 12 Jahre', 'Mo 17:30 Uhr', 'kpop-l3', '34', 60),
('Jugendliche', 'Contemporary Level 1', 'ab ca. 16 Jahre · gemeinsam mit Erwachsenen', 'Do 18:30 Uhr', 'contemp-l1', '81', 70),
('Jugendliche', 'Contemporary Level 2', 'ab ca. 16 Jahre · gemeinsam mit Erwachsenen', 'Di 20:15 Uhr', 'contemp-l2', '122', 80),
('Erwachsene', 'Streetdance', 'ab 18 Jahre', 'Di 19:05 Uhr', 'streetdance', '19', 10),
('Erwachsene', 'Breakdance', 'ab 10 bzw. ab 13 Jahre', 'Mi 16:15 Uhr · Mi 17:20 Uhr', 'breakdance', '', 20);

-- Seed: Stundenplan (verifiziert von hiphoplandsberg.de)
INSERT INTO schedule (weekday, time, course_name, age_info, trainer, category, sort_order) VALUES
('Mo', '15:30 Uhr', 'Hip Hop Level 1', 'Jugendliche ab ca. 12 Jahre', 'Mila', 'Jugendliche', 10),
('Mo', '16:45 Uhr', 'Hip Hop Level 2', 'Jugendliche ab ca. 12 Jahre', 'Mila', 'Jugendliche', 20),
('Mo', '17:30 Uhr', 'K-Pop Level 3', 'Jugendliche ab ca. 12 Jahre', 'Ann', 'Jugendliche', 30),
('Mo', '17:45 Uhr', 'Hip Hop Level 3', 'Jugendliche ab ca. 12 Jahre', 'Mila', 'Jugendliche', 40),
('Di', '17:15 Uhr', 'K-Pop Juniors', 'ca. 10-12 Jahre', 'Mila', 'Kinder', 10),
('Di', '19:05 Uhr', 'Streetdance', 'ab 18 Jahre', 'Laura', 'Erwachsene', 20),
('Di', '20:15 Uhr', 'Contemporary Level 2', 'ab ca. 16 Jahre', 'Fenja', 'Gemischt', 30),
('Mi', '16:15 Uhr', 'Hip Hop Kids', '3./4. Klasse (8-ca. 10 Jahre)', 'Mila', 'Kinder', 10),
('Mi', '16:15 Uhr', 'Breakdance', 'ab 10 Jahre', 'Jonas', 'Erwachsene', 20),
('Mi', '17:20 Uhr', 'Breakdance', 'ab 13 Jahre', 'Jonas', 'Erwachsene', 30),
('Mi', '17:30 Uhr', 'Hip Hop Juniors', '5./6. Klasse (10-ca. 12 Jahre)', 'Mila', 'Kinder', 40),
('Mi', '17:30 Uhr', 'K-Pop Level 2', 'Jugendliche ab 12 Jahre', 'Nora', 'Jugendliche', 50),
('Do', '16:15 Uhr', 'Hip Hop Kids', '3./4. Klasse (8-ca. 10 Jahre)', 'Mila', 'Kinder', 10),
('Do', '16:45 Uhr', 'K-Pop Level 1', 'Jugendliche ab ca. 12 Jahre', 'Ann', 'Jugendliche', 20),
('Do', '17:15 Uhr', 'Hip Hop Level 1', 'Jugendliche ab ca. 12 Jahre', 'Mila', 'Jugendliche', 30),
('Do', '17:30 Uhr', 'Hip Hop Juniors', '5./6. Klasse (10-ca. 12 Jahre)', 'Nora', 'Kinder', 40),
('Do', '18:30 Uhr', 'Contemporary Level 1', 'Jugendliche ab ca. 16 Jahre', 'Fenja', 'Gemischt', 50),
('Fr', '15:00 Uhr', 'Hip Hop Level 1', 'Jugendliche ab ca. 12 Jahre', 'Ann', 'Jugendliche', 10),
('Fr', '16:10 Uhr', 'Hip Hop Level 2', 'Jugendliche ab ca. 12 Jahre', 'Ann', 'Jugendliche', 20);

-- Seed: Trainer (Fotos nur, wo auf der bestehenden Seite vorhanden; Bio-Texte fehlen noch – im Adminbereich nachtragen)
INSERT INTO trainers (name, bio, photo_path, sort_order) VALUES
('Mila', '', 'assets/img/trainers/mila.png', 10),
('Jonas', '', 'assets/img/trainers/jonas.png', 20),
('Ann', '', '', 30),
('Nora', '', 'assets/img/trainers/nora.png', 40),
('Fenja', '', '', 50),
('Laura', '', '', 60);

-- Seed: Preise (Original-Text, siehe Canva-Mockup; Erwachsene Paartanz bewusst
-- ausgenommen, da das nicht zum Hip-Hop/K-Pop/Urban-Dance-Angebot von LNDSBRG
-- MOVES gehört)
INSERT INTO pricing_plans (name, tagline, price, price_unit, included_text, sort_order) VALUES
('Kinder', 'Erste Moves, großer Spaß', 52.00, 'pro Monat, pro Person', 'Kindertanzen, Hip Hop 3./4. & 5./6. Klasse, DanceKids, K-Pop 5./6. Klasse', 10),
('Jugendliche', 'Für alle, die durchstarten wollen', 52.00, 'pro Monat, pro Person', 'Hip Hop, K-Pop, Breakdance, ZUMBA®, Line Dance, Paartanz & Szenetänze', 20),
('Solo Erwachsene', 'Tanzen für dich, nach Feierabend', 59.00, 'pro Monat, pro Person', 'ZUMBA®, ZUMBA Gold®, Line Dance, Streetdance, Hip Hop, K-Pop, Contemporary', 30);
