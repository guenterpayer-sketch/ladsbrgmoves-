-- Migration: Stundenplan-Einträge über eine echte Kurs-ID statt über den
-- Kursnamen verknüpfen (schedule.course_id -> courses.id).
--
-- Hintergrund: Der bisherige Namensabgleich konnte nicht unterscheiden,
-- wenn derselbe Kursname mehrfach in "courses" vorkommt, aber jeweils eine
-- andere NimbusCloud-Buchung meint (z.B. Breakdance getrennt nach
-- Altersgruppe). Über phpMyAdmin einmalig ausführen.

ALTER TABLE schedule ADD COLUMN course_id INT NULL DEFAULT NULL AFTER course_name;
ALTER TABLE schedule ADD CONSTRAINT fk_schedule_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL;

-- Automatische Verknüpfung nur dort, wo der Kursname eindeutig genau einem
-- buchbaren Kurs entspricht. Mehrdeutige Fälle (z.B. Breakdance, Contemporary
-- mit mehreren Kurs-Einträgen desselben Namens) bleiben bewusst NULL und
-- müssen einmalig im Admin unter "Stundenplan" manuell zugeordnet werden
-- (neues Auswahlfeld "Verknüpfter Kurs").
UPDATE schedule s
JOIN (
  SELECT name FROM courses
  WHERE slug IS NOT NULL AND nimbus_online_id != ''
  GROUP BY name
  HAVING COUNT(*) = 1
) uniq ON uniq.name = s.course_name
JOIN courses c ON c.name = s.course_name AND c.slug IS NOT NULL AND c.nimbus_online_id != ''
SET s.course_id = c.id;
