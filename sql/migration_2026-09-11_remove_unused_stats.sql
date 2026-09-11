-- Entfernt die "Statistik"-Textbausteine aus dem Redaktionssystem.
-- Diese stammten aus einer früheren Design-Version mit "Vertrauens-Boxen"
-- (z.B. "15+ Kurse pro Woche"), die im aktuellen Design nicht mehr angezeigt
-- werden. Sie standen aber weiter in der Datenbank und tauchten dadurch als
-- verwirrende, wirkungslose Felder unter "Texte" im Adminbereich auf.
--
-- Einmalig über phpMyAdmin im All-Inkl KAS ausführen (wie beim ursprünglichen
-- schema.sql-Import).

DELETE FROM content_blocks WHERE content_key IN (
  'stat_courses_value', 'stat_courses_suffix', 'stat_courses_label',
  'stat_years_value', 'stat_years_suffix', 'stat_years_label',
  'stat_trainers_value', 'stat_trainers_suffix', 'stat_trainers_label'
);
