# LNDSBERG MOVES

One-Page-Website mit eigenem Redaktionssystem für Kurse, Stundenplan,
Trainer und Texte. Läuft auf klassischem PHP/MySQL-Hosting (entwickelt für
All-Inkl), ohne WordPress oder externe Dienste.

## Struktur

```
config.php                  Lädt DB-Zugangsdaten (Env-Variablen oder config.local.php)
config.local.php.example    Vorlage für lokale/Live-Zugangsdaten
sql/schema.sql               Datenbankschema + Beispiel-/Startdaten
includes/                    PHP-Hilfsfunktionen (DB-Zugriff, Content-Helper)
public/                      Web-Root – dieser Ordner kommt auf den Server
  index.php                  Die öffentliche One-Pager-Seite
  admin/                     Redaktionssystem (login-geschützt)
  assets/                    CSS, Bilder
```

Nur der Inhalt von `public/` gehört ins öffentliche Web-Verzeichnis.
`config.php`, `config.local.php` und `includes/` liegen eine Ebene darüber
und sind damit vom Browser aus nicht erreichbar.

## Lokal testen

Voraussetzungen: PHP 8.1+ mit `pdo_mysql`, ein MySQL/MariaDB-Server.

```bash
mysql -u root -e "CREATE DATABASE lndsbrgmoves CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql --default-character-set=utf8mb4 -u root lndsbrgmoves < sql/schema.sql
cp config.local.php.example config.local.php
# config.local.php mit den lokalen DB-Zugangsdaten anpassen
php -S 127.0.0.1:8000 -t public
```

Wichtig beim Import: immer `--default-character-set=utf8mb4` angeben,
sonst werden Umlaute/Sonderzeichen falsch importiert (Mojibake).

Danach im Browser:
- `http://127.0.0.1:8000/` – öffentliche Seite
- `http://127.0.0.1:8000/admin/setup.php` – legt den ersten Admin-Account an
  (funktioniert nur einmal, solange noch kein Account existiert)
- `http://127.0.0.1:8000/admin/login.php` – Login ins Redaktionssystem

## Deployment auf All-Inkl (KAS)

1. **Datenbank anlegen**: Im KAS-Kundenmenü unter "Datenbanken" eine neue
   MySQL-Datenbank samt Benutzer anlegen. Host ist bei All-Inkl in der Regel
   `localhost`.
2. **Schema importieren**: Über phpMyAdmin (im KAS verlinkt) die Datei
   `sql/schema.sql` importieren. Beim Import in phpMyAdmin unter "Format"
   sicherstellen, dass die Zeichenkodierung `utf8mb4`/`utf8` ist.
3. **Zugangsdaten hinterlegen**: `config.local.php.example` nach
   `config.local.php` kopieren und mit den echten Zugangsdaten aus Schritt 1
   befüllen. All-Inkl bietet auf klassischem Shared-Hosting in der Regel
   keine Möglichkeit, eigene Umgebungsvariablen für PHP zu setzen – deshalb
   ist `config.local.php` hier der zuverlässigere Weg. (Nicht verifiziert:
   ob euer konkretes Paket das doch unterstützt – im Zweifel im KAS unter
   PHP-Einstellungen prüfen.)
4. **Dateien hochladen**: Per FTP/SFTP den kompletten Projektordner
   hochladen – `config.php`, `config.local.php`, `includes/` und `sql/`
   liegen dabei **außerhalb** des öffentlichen `htdocs`-Ordners, nur der
   Inhalt von `public/` kommt in `htdocs` (bzw. in ein Unterverzeichnis
   davon, falls die Domain auf einen Unterordner zeigt).
5. **Admin-Account anlegen**: `https://eure-domain.de/admin/setup.php`
   einmalig aufrufen und Benutzername/Passwort vergeben.
6. **Setup-Datei entfernen**: Danach `public/admin/setup.php` vom Server
   löschen (sie verweigert nach dem ersten Account ohnehin den Dienst,
   aber sauberer ist, sie ganz zu entfernen).

## Was ich für den Seiteninhalt verifiziert habe

Texte, Kursangebot, Stundenplan, Logo und einige Trainer-Fotos wurden aus
der bestehenden Seite `hiphoplandsberg.de` (Stand 2026-09-10) übernommen.
**Offene Lücken, die im Redaktionssystem nachgetragen werden sollten:**

- Gründungsjahr des Tanzcenter Payer (`Texte` → "Statistik: Gründungsjahr")
  – aktuell auf das laufende Jahr gesetzt, dadurch zeigt die Startseite
  "0 Jahre" an.
- Bio-Texte der Trainer (aktuell alle leer).
- Fotos für Ann, Fenja und Laura (auf der alten Seite nicht gefunden).

## Sicherheitshinweise

- Passwörter werden gehasht gespeichert (`password_hash`/`password_verify`).
- Alle Formulare sind CSRF-geschützt.
- Alle Datenbankzugriffe laufen über parametrisierte Queries (PDO).
- Datei-Uploads (Trainer-Fotos) werden auf Bild-MIME-Typ und Größe geprüft
  und unter zufälligem Dateinamen gespeichert.
