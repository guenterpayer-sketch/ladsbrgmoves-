<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_login();

$db = get_db();
$message = null;
$currentUserId = (int) $_SESSION['admin_user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $action = (string) ($_POST['action'] ?? '');
    try {
        if ($action === 'create') {
            $username = trim((string) $_POST['username']);
            $password = (string) $_POST['password'];
            if ($username === '' || strlen($password) < 8) {
                throw new RuntimeException('Benutzername darf nicht leer sein, Passwort braucht mindestens 8 Zeichen.');
            }
            $stmt = $db->prepare('SELECT COUNT(*) AS c FROM admin_users WHERE username = :u');
            $stmt->execute(['u' => $username]);
            if ((int) $stmt->fetch()['c'] > 0) {
                throw new RuntimeException('Dieser Benutzername ist bereits vergeben.');
            }
            $stmt = $db->prepare('INSERT INTO admin_users (username, password_hash) VALUES (:u, :p)');
            $stmt->execute(['u' => $username, 'p' => password_hash($password, PASSWORD_DEFAULT)]);
            $message = 'Nutzer angelegt.';
        } elseif ($action === 'delete') {
            $id = (int) $_POST['id'];
            if ($id === $currentUserId) {
                throw new RuntimeException('Der eigene, aktuell eingeloggte Account kann nicht gelöscht werden.');
            }
            $totalCount = (int) $db->query('SELECT COUNT(*) AS c FROM admin_users')->fetch()['c'];
            if ($totalCount <= 1) {
                throw new RuntimeException('Der letzte verbleibende Account kann nicht gelöscht werden.');
            }
            $stmt = $db->prepare('DELETE FROM admin_users WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $message = 'Nutzer gelöscht.';
        }
    } catch (RuntimeException $ex) {
        $message = $ex->getMessage();
    }
}

$users = $db->query('SELECT id, username, created_at FROM admin_users ORDER BY username')->fetchAll();
$token = csrf_token();
$page_title = 'Nutzer';
include __DIR__ . '/includes/layout_top.php';
?>
<?php if ($message): ?><p class="form-success"><?= e($message) ?></p><?php endif; ?>

<h2>Neuen Nutzer anlegen</h2>
<form method="post" class="inline-form">
  <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
  <input type="hidden" name="action" value="create">
  <label>Benutzername
    <input type="text" name="username" required>
  </label>
  <label>Passwort (mind. 8 Zeichen)
    <input type="password" name="password" required minlength="8">
  </label>
  <button type="submit">Anlegen</button>
</form>

<table class="admin-table">
  <thead><tr><th>Benutzername</th><th>Angelegt am</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($users as $user): ?>
    <tr>
      <td><?= e($user['username']) ?><?= (int) $user['id'] === $currentUserId ? ' (du)' : '' ?></td>
      <td><?= e($user['created_at']) ?></td>
      <td class="admin-table__actions">
        <?php if ((int) $user['id'] !== $currentUserId): ?>
          <form method="post" onsubmit="return confirm('Nutzer wirklich löschen?');">
            <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
            <button type="submit" class="button-danger">Löschen</button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
