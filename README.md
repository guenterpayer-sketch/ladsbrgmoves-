# LNDSBRG MOVES

One-Page-Website mit eigenem Redaktionssystem für Kurse, Stundenplan,
Trainer und Texte, inklusive echter Kursbuchung über NimbusCloud. Läuft auf
klassischem PHP/MySQL-Hosting (entwickelt für All-Inkl), ohne WordPress.

Design (Farben, Typografie, Layout-Sprache) folgt dem im Projektbrief v3
dokumentierten Corporate Design; Markenname gemäß dortiger Korrektur
"LNDSBRG MOVES" (ohne E nach dem L).

## Struktur

```
config.php                  Echte DB-Zugangsdaten (nicht im Git, nicht deployed – siehe unten)
config.example.php          Vorlage für config.php
sql/schema.sql               Datenbankschema + Beispiel-/Startdaten
includes/                    PHP-Hilfsfunktionen (DB-Zugriff, Content-Helper)
public/                      Web-Root – dieser Ordner kommt auf den Server
  index.php                  Die öffentliche One-Pager-Seite
  impressum.php               Impressum
  datenschutz.php             Datenschutzerklärung
  admin/                     Redaktionssystem (login-geschützt)
  assets/                    CSS, Bilder
```

`config.php` enthält die echten Datenbank-Zugangsdaten als einfache
`define()`-Konstanten (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`,
`DB_CHARSET`). Die Datei ist bewusst **nicht** im Git-Repository und wird
auch **nicht** über den Deploy-Workflow hochgeladen – sie muss einmalig
direkt auf dem Server angelegt werden (siehe Deployment-Abschnitt).

Auf dem Server liegen `config.php` und `includes/` zwar im selben Ordner wie
`public/` (siehe Deployment-Hinweis unten), sind aber über eine `.htaccess`
vor direktem Browser-Zugriff gesperrt.

## Lokal testen

Voraussetzungen: PHP 8.1+ mit `pdo_mysql`, ein MySQL/MariaDB-Server.

```bash
mysql -u root -e "CREATE DATABASE lndsbrgmoves CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql --default-character-set=utf8mb4 -u root lndsbrgmoves < sql/schema.sql
cp config.example.php config.php
# config.php mit den lokalen DB-Zugangsdaten anpassen
php -S 127.0.0.1:8000 -t public
```

Wichtig beim Import: immer `--default-character-set=utf8mb4` angeben,
sonst werden Umlaute/Sonderzeichen falsch importiert (Mojibake).

Danach im Browser:
- `http://127.0.0.1:8000/` – öffentliche Seite
- `http://127.0.0.1:8000/admin/setup.php` – legt den ersten Admin-Account an
  (funktioniert nur einmal, solange noch kein Account existiert)
- `http://127.0.0.1:8000/admin/login.php` – Login ins Redaktionssystem

## Kursbuchung über NimbusCloud

Ein Klick auf einen buchbaren Kurs öffnet ein Modal mit einem eingebetteten
NimbusCloud-Anmeldeformular (`tanzcenter-payer.nimbuscloud.at`) – exakt der
Flow, der schon auf der bisherigen Seite lief. Die Zuordnung Kurs → NimbusCloud-ID
ist pro Kurs im Redaktionssystem unter **Kurse** hinterlegt (Feld "Anmelde-Schlüssel"
+ "NimbusCloud Kurs-ID"). Ist eines der beiden Felder leer, wird der Kurs ohne
Buchungslink angezeigt (aktuell z.B. "Breakdance", da dafür auf der alten Seite
keine Verknüpfung existierte).

Die aktuell hinterlegten NimbusCloud-IDs wurden direkt aus dem Live-Code von
hiphoplandsberg.de übernommen (Stand 2026-09-10) – dort als unsichtbarer
"Benutzerdefinierter HTML"-Block eingebunden. Falls sich Kurs-IDs bei NimbusCloud
ändern, einfach im Redaktionssystem unter "Kurse" aktualisieren.

## Deployment auf All-Inkl (KAS)

1. **Datenbank anlegen**: Im KAS-Kundenmenü unter "Datenbanken" eine neue
   MySQL-Datenbank samt Benutzer anlegen. Host ist bei All-Inkl in der Regel
   `localhost`.
2. **Schema importieren**: Über phpMyAdmin (im KAS verlinkt) die Datei
   `sql/schema.sql` importieren. Beim Import in phpMyAdmin unter "Format"
   sicherstellen, dass die Zeichenkodierung `utf8mb4`/`utf8` ist.
3. **Dateien hochladen**: Der GitHub-Actions-Workflow (`.github/workflows/deploy.yml`,
   manuell auslösbar unter "Actions") lädt `public/` in den Web-Root und
   `config.example.php`, `includes/`, `sql/`, `.htaccess` daneben in denselben
   bzw. einen separaten Ordner hoch (abhängig von den Secrets `FTP_PUBLIC_DIR`
   / `FTP_APP_DIR`) – `config.php` selbst lädt er **nicht** hoch.
4. **Zugangsdaten hinterlegen**: `config.example.php` (liegt jetzt auf dem
   Server) einmalig manuell per WebFTP/FTP zu `config.php` kopieren bzw.
   umbenennen und mit den echten Zugangsdaten aus Schritt 1 befüllen (die
   `define()`-Zeilen für `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`).
   Diese Datei danach nicht mehr über den Workflow anfassen lassen – sie
   bleibt bewusst serverseitig und wird bei jedem Deploy übersprungen.
5. **Admin-Account anlegen**: `https://eure-domain.de/admin/setup.php`
   einmalig aufrufen und Benutzername/Passwort vergeben.
6. **Setup-Datei entfernen**: Danach `public/admin/setup.php` vom Server
   löschen (sie verweigert nach dem ersten Account ohnehin den Dienst,
   aber sauberer ist, sie ganz zu entfernen).

## Was verifiziert bzw. übernommen wurde

- Texte, Kursangebot, Stundenplan, Logo-Schriftzug, NimbusCloud-Kurs-IDs und
  einige Trainer-Fotos: aus dem Live-Code von `hiphoplandsberg.de`
  (Stand 2026-09-10).
- Design-System (Farben, Bebas Neue, Layout-Muster): aus dem Projektbrief v3
  und den mitgelieferten HTML-Entwürfen.
- Datenschutztext: Basis ist die bisherige Datenschutzerklärung von
  hiphoplandsberg.de, ergänzt um einen Hinweis zur NimbusCloud-Einbindung.
- Impressum: selbst zusammengestellt aus den verfügbaren Kontaktdaten
  (Minimalversion nach § 5 TMG) – **bitte rechtlich prüfen (lassen)**, ob
  weitere Pflichtangaben nötig sind.

**Offene Lücken, die im Redaktionssystem nachgetragen werden sollten:**

- Bio-Texte der Trainer (aktuell alle leer).
- Fotos für Ann, Fenja und Laura (auf der alten Seite nicht gefunden).
- Einleitungstext der Trainer-Sektion (aktuell ein Platzhaltertext von mir,
  im Adminbereich unter "Texte" → "Trainer-Sektion: einleitender Text").
- Domain-Entscheidung (laut letztem Stand noch offen zwischen
  hiphoplandsberg.de weiterführen oder auf eine neue Domain wechseln –
  die im Projektbrief genannte Zieldomain "lndsbergmoves.de" weicht in der
  Schreibweise vom korrigierten Markennamen "LNDSBRG MOVES" ab, das müsste
  noch abgeglichen werden).

## Sicherheitshinweise

- Passwörter werden gehasht gespeichert (`password_hash`/`password_verify`).
- Alle Formulare sind CSRF-geschützt.
- Alle Datenbankzugriffe laufen über parametrisierte Queries (PDO).
- Datei-Uploads (Trainer-Fotos) werden auf Bild-MIME-Typ und Größe geprüft
  und unter zufälligem Dateinamen gespeichert.
