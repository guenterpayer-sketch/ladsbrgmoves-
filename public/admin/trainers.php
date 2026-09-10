<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_login();

$db = get_db();
$message = null;

const ALLOWED_MIME = [
    'image/png'  => 'png',
    'image/jpeg' => 'jpg',
    'image/webp' => 'webp',
];
const UPLOAD_DIR_FS = __DIR__ . '/../assets/img/uploads/trainers/';
const UPLOAD_DIR_WEB = 'assets/img/uploads/trainers/';
const MAX_UPLOAD_BYTES = 5 * 1024 * 1024;

function handle_photo_upload(): ?string {
    if (empty($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES['photo'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Fehler beim Hochladen des Fotos.');
    }
    if ($file['size'] > MAX_UPLOAD_BYTES) {
        throw new RuntimeException('Foto ist zu groß (max. 5 MB).');
    }
    $mime = mime_content_type($file['tmp_name']);
    if (!isset(ALLOWED_MIME[$mime])) {
        throw new RuntimeException('Nur PNG, JPG oder WebP sind als Foto erlaubt.');
    }
    $extension = ALLOWED_MIME[$mime];
    $filename = bin2hex(random_bytes(16)) . '.' . $extension;
    if (!is_dir(UPLOAD_DIR_FS)) {
        mkdir(UPLOAD_DIR_FS, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR_FS . $filename)) {
        throw new RuntimeException('Foto konnte nicht gespeichert werden.');
    }
    return UPLOAD_DIR_WEB . $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $action = (string) ($_POST['action'] ?? '');
    try {
        if ($action === 'delete') {
            $stmt = $db->prepare('DELETE FROM trainers WHERE id = :id');
            $stmt->execute(['id' => (int) $_POST['id']]);
            $message = 'Trainer gelöscht.';
        } elseif ($action === 'create' || $action === 'update') {
            $data = [
                'name'       => trim((string) $_POST['name']),
                'bio'        => trim((string) $_POST['bio']),
                'sort_order' => (int) $_POST['sort_order'],
            ];
            if ($data['name'] === '') {
                throw new RuntimeException('Bitte einen Namen angeben.');
            }
            $photoPath = handle_photo_upload();

            if ($action === 'create') {
                $data['photo_path'] = $photoPath ?? '';
                $stmt = $db->prepare(
                    'INSERT INTO trainers (name, bio, photo_path, sort_order) VALUES (:name, :bio, :photo_path, :sort_order)'
                );
                $stmt->execute($data);
                $message = 'Trainer angelegt.';
            } else {
                $data['id'] = (int) $_POST['id'];
                if ($photoPath !== null) {
                    $stmt = $db->prepare(
                        'UPDATE trainers SET name=:name, bio=:bio, photo_path=:photo_path, sort_order=:sort_order WHERE id=:id'
                    );
                    $data['photo_path'] = $photoPath;
                } else {
                    $stmt = $db->prepare(
                        'UPDATE trainers SET name=:name, bio=:bio, sort_order=:sort_order WHERE id=:id'
                    );
                }
                $stmt->execute($data);
                $message = 'Trainer aktualisiert.';
            }
        }
    } catch (RuntimeException $ex) {
        $message = $ex->getMessage();
    }
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
$editing = null;
if ($editId) {
    $stmt = $db->prepare('SELECT * FROM trainers WHERE id = :id');
    $stmt->execute(['id' => $editId]);
    $editing = $stmt->fetch() ?: null;
}

$trainers = get_trainers();
$token = csrf_token();
$page_title = 'Trainer';
include __DIR__ . '/includes/layout_top.php';
?>
<?php if ($message): ?><p class="form-success"><?= e($message) ?></p><?php endif; ?>

<h2><?= $editing ? 'Trainer bearbeiten' : 'Neuen Trainer anlegen' ?></h2>
<form method="post" class="inline-form" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
  <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
  <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $editing['id'] ?>"><?php endif; ?>
  <label>Name
    <input type="text" name="name" required value="<?= e($editing['name'] ?? '') ?>">
  </label>
  <label>Kurzbeschreibung
    <textarea name="bio" rows="3"><?= e($editing['bio'] ?? '') ?></textarea>
  </label>
  <label>Foto (PNG/JPG/WebP, max. 5 MB)
    <input type="file" name="photo" accept="image/png,image/jpeg,image/webp">
  </label>
  <?php if (!empty($editing['photo_path'])): ?>
    <img src="../<?= e($editing['photo_path']) ?>" alt="" class="admin-thumb">
  <?php endif; ?>
  <label>Reihenfolge (Zahl, kleiner = weiter oben)
    <input type="number" name="sort_order" value="<?= (int) ($editing['sort_order'] ?? 0) ?>">
  </label>
  <button type="submit"><?= $editing ? 'Speichern' : 'Anlegen' ?></button>
  <?php if ($editing): ?><a href="trainers.php" class="button-secondary">Abbrechen</a><?php endif; ?>
</form>

<table class="admin-table">
  <thead><tr><th>Foto</th><th>Name</th><th>Beschreibung</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($trainers as $trainer): ?>
    <tr>
      <td><?php if ($trainer['photo_path']): ?><img src="../<?= e($trainer['photo_path']) ?>" alt="" class="admin-thumb admin-thumb--small"><?php else: ?>—<?php endif; ?></td>
      <td><?= e($trainer['name']) ?></td>
      <td><?= e($trainer['bio'] ?: '(kein Text hinterlegt)') ?></td>
      <td class="admin-table__actions">
        <a href="?edit=<?= (int) $trainer['id'] ?>">Bearbeiten</a>
        <form method="post" onsubmit="return confirm('Trainer wirklich löschen?');">
          <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int) $trainer['id'] ?>">
          <button type="submit" class="button-danger">Löschen</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
