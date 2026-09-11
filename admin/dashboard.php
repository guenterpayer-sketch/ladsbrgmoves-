<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_login();

$page_title = 'Übersicht';
include __DIR__ . '/includes/layout_top.php';
?>
<div class="admin-grid">
  <a class="admin-card" href="texts.php">
    <h2>Texte</h2>
    <p>Überschriften, Hero-Text, Kontaktdaten und weitere feste Textbausteine bearbeiten.</p>
  </a>
  <a class="admin-card" href="courses.php">
    <h2>Kurse</h2>
    <p>Kursübersicht (Kinder / Jugendliche / Erwachsene) pflegen.</p>
  </a>
  <a class="admin-card" href="schedule.php">
    <h2>Stundenplan</h2>
    <p>Wochenplan mit Uhrzeiten und Trainer-Zuordnung pflegen.</p>
  </a>
  <a class="admin-card" href="trainers.php">
    <h2>Trainer</h2>
    <p>Trainer-Profile inkl. Foto und Kurzbeschreibung pflegen.</p>
  </a>
  <a class="admin-card" href="users.php">
    <h2>Nutzer</h2>
    <p>Weitere Redaktions-Accounts anlegen oder entfernen.</p>
  </a>
</div>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
