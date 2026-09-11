<?php
declare(strict_types=1);
// Einmalige Ersteinrichtung: legt den ersten Admin-Account an.
// Funktioniert nur, solange die Tabelle admin_users leer ist – danach bitte
// diese Datei vom Server löschen oder zumindest umbenennen.
require_once __DIR__ . '/../includes/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$db = get_db();
$existingCount = (int) $db->query('SELECT COUNT(*) AS c FROM admin_users')->fetch()['c'];

if ($existingCount > 0) {
    http_response_code(403);
    echo 'Es existiert bereits ein Admin-Account. Diese Datei bitte vom Server entfernen.';
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if ($username === '' || strlen($password) < 8) {
        $error = 'Benutzername darf nicht leer sein, Passwort braucht mindestens 8 Zeichen.';
    } else {
        $stmt = $db->prepare('INSERT INTO admin_users (username, password_hash) VALUES (:u, :p)');
        $stmt->execute(['u' => $username, 'p' => password_hash($password, PASSWORD_DEFAULT)]);
        header('Location: login.php');
        exit;
    }
}
$token = csrf_token();
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ersteinrichtung · LNDSBRG MOVES</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-body">
<form class="login-box" method="post" novalidate>
  <h1>LNDSBRG MOVES</h1>
  <p class="login-box__subtitle">Ersten Admin-Account anlegen</p>
  <?php if ($error): ?><p class="form-error"><?= e($error) ?></p><?php endif; ?>
  <label>Benutzername
    <input type="text" name="username" required autofocus>
  </label>
  <label>Passwort (mind. 8 Zeichen)
    <input type="password" name="password" required minlength="8">
  </label>
  <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
  <button type="submit">Account anlegen</button>
</form>
</body>
</html>
