<?php
// Temporäres Diagnose-Skript – nach dem Debuggen wieder löschen.
header('Content-Type: text/plain; charset=utf-8');

echo "1) PHP läuft. Version: " . PHP_VERSION . "\n";
echo "2) Aktueller Ordner: " . __DIR__ . "\n";

$configPath = __DIR__ . '/config.php';
echo "3) Suche config.php unter: $configPath\n";
echo "   Existiert: " . (is_file($configPath) ? 'ja' : 'NEIN') . "\n";

try {
    require_once $configPath;
    echo "4) config.php erfolgreich geladen.\n";
    echo "   DB_HOST definiert: " . (defined('DB_HOST') ? DB_HOST : 'NEIN') . "\n";
    echo "   DB_NAME definiert: " . (defined('DB_NAME') ? DB_NAME : 'NEIN') . "\n";
    echo "   DB_USER definiert: " . (defined('DB_USER') ? DB_USER : 'NEIN') . "\n";
    echo "   DB_CHARSET definiert: " . (defined('DB_CHARSET') ? DB_CHARSET : 'NEIN') . "\n";
} catch (\Throwable $e) {
    echo "4) FEHLER beim Laden von config.php:\n";
    echo "   " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo "   in " . $e->getFile() . " Zeile " . $e->getLine() . "\n";
    exit;
}

try {
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
    echo "5) Versuche DB-Verbindung mit DSN: $dsn\n";
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "6) DB-Verbindung erfolgreich!\n";
    $count = $pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn();
    echo "7) Anzahl Kurse in DB: $count\n";
} catch (\Throwable $e) {
    echo "5/6) FEHLER bei der DB-Verbindung:\n";
    echo "   " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo "   in " . $e->getFile() . " Zeile " . $e->getLine() . "\n";
    exit;
}

echo "ALLES OK.\n";
