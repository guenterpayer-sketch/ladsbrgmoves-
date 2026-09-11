<?php
// Kopiere diese Datei zu "config.php" (im selben Ordner) und trage dort die
// echten Zugangsdaten ein. config.php wird nicht ins Git eingecheckt (siehe
// .gitignore) und auch nicht über den Deploy-Workflow hochgeladen.
//
// Bei All-Inkl: Datenbankname, -benutzer und -passwort findest du im
// KAS-Kundenmenü unter "Datenbanken". Host ist bei All-Inkl in der Regel
// "localhost".

define('DB_HOST', 'localhost');
define('DB_NAME', 'DEIN_DB_NAME');
define('DB_USER', 'DEIN_DB_USER');
define('DB_PASSWORD', 'DEIN_DB_PASSWORT');
define('DB_CHARSET', 'utf8mb4');
