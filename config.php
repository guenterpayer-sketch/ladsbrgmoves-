<?php
// Zentrale Konfiguration. Enthält selbst keine echten Zugangsdaten.
//
// Werte kommen wahlweise aus:
// 1) Umgebungsvariablen (falls das Hosting das unterstützt), oder
// 2) einer lokalen Datei config.local.php (siehe config.local.php.example),
//    die NICHT eingecheckt wird – empfohlen für klassisches Shared-Hosting
//    wie All-Inkl, wo Umgebungsvariablen oft nicht einstellbar sind.

function env_or(string $name, string $default): string {
    $value = getenv($name);
    if ($value !== false) {
        return $value;
    }
    if (isset($_SERVER[$name])) {
        return (string) $_SERVER[$name];
    }
    if (isset($_ENV[$name])) {
        return (string) $_ENV[$name];
    }
    return $default;
}

$config = [
    'db' => [
        'host'     => env_or('LM_DB_HOST', '127.0.0.1'),
        'name'     => env_or('LM_DB_NAME', 'lndsbrgmoves'),
        'user'     => env_or('LM_DB_USER', 'root'),
        'password' => env_or('LM_DB_PASSWORD', ''),
        'charset'  => 'utf8mb4',
    ],
];

$localConfigFile = __DIR__ . '/config.local.php';
if (is_file($localConfigFile)) {
    $config = array_replace_recursive($config, require $localConfigFile);
}

return $config;
