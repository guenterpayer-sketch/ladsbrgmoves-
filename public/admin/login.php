<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $error = 'Sitzung abgelaufen, bitte erneut versuchen.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if (attempt_login($username, $password)) {
            header('Location: dashboard.php');
            exit;
        }
        $error = 'Benutzername oder Passwort ist falsch.';
    }
}
$token = csrf_token();
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Anmelden · Redaktionssystem · LNDSBRG MOVES</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-body">
<form class="login-box" method="post" novalidate>
  <h1>LNDSBRG MOVES</h1>
  <p class="login-box__subtitle">Redaktionssystem</p>
  <?php if ($error): ?>
    <p class="form-error"><?= e($error) ?></p>
  <?php endif; ?>
  <label>Benutzername
    <input type="text" name="username" autocomplete="username" required autofocus>
  </label>
  <label>Passwort
    <input type="password" name="password" autocomplete="current-password" required>
  </label>
  <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
  <button type="submit">Anmelden</button>
</form>
</body>
</html>
