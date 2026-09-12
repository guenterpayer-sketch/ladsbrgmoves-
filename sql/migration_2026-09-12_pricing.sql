-- Migration: Preise-Sektion redaktionell pflegbar machen (Tabelle
-- "pricing_plans" statt fest im Code hinterlegter Preiskarten).
-- Über phpMyAdmin einmalig ausführen.

CREATE TABLE IF NOT EXISTS pricing_plans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  tagline VARCHAR(255) NOT NULL DEFAULT '',
  price DECIMAL(6,2) NOT NULL,
  price_unit VARCHAR(100) NOT NULL DEFAULT 'pro Monat, pro Person',
  included_text VARCHAR(500) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO content_blocks (content_key, label, content_value) VALUES
('pricing_intro', 'Preise: einleitender Text', 'Ein Preis im Monat, alle Kurse deiner Altersgruppe inklusive. Deine erste Stunde ist unverbindlich – gefällt''s dir nicht, zahlst du nichts.'),
('pricing_footnote', 'Preise: Fußnote (Kündigung/Kleingedrucktes)', 'Monatlich kündbar, 2 Wochen Frist zum Monatsende, schriftlich. Kein Kleingedrucktes.');

INSERT INTO pricing_plans (name, tagline, price, price_unit, included_text, sort_order) VALUES
('Kinder', 'Erste Moves, großer Spaß', 52.00, 'pro Monat, pro Person', 'Kindertanzen, Hip Hop 3./4. & 5./6. Klasse, DanceKids, K-Pop 5./6. Klasse', 10),
('Jugendliche', 'Für alle, die durchstarten wollen', 52.00, 'pro Monat, pro Person', 'Hip Hop, K-Pop, Breakdance, ZUMBA®, Line Dance, Paartanz & Szenetänze', 20),
('Solo Erwachsene', 'Tanzen für dich, nach Feierabend', 59.00, 'pro Monat, pro Person', 'ZUMBA®, ZUMBA Gold®, Line Dance, Streetdance, Hip Hop, K-Pop, Contemporary', 30);
