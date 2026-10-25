-- Migration: Workshops (einzelne, datierte Veranstaltungen) redaktionell pflegbar.
-- Über phpMyAdmin einmalig ausführen (--default-character-set=utf8mb4!).
-- Bewusst ohne Beispieldaten: Workshops sind zeitgebunden, ein Seed würde
-- live einen falschen Termin anzeigen.

CREATE TABLE IF NOT EXISTS workshops (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  event_date DATE NOT NULL,
  time_info VARCHAR(100) NOT NULL DEFAULT '',
  trainer VARCHAR(100) NOT NULL DEFAULT '',
  price VARCHAR(50) NOT NULL DEFAULT '',
  description TEXT NOT NULL,
  -- NimbusCloud-ID für die Anmeldung (wie bei Kursen); leer = Workshop ohne Buchungsbutton
  nimbus_online_id VARCHAR(20) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
