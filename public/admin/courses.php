<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_login();

$db = get_db();
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'delete') {
        $stmt = $db->prepare('DELETE FROM courses WHERE id = :id');
        $stmt->execute(['id' => (int) $_POST['id']]);
        $message = 'Kurs gelöscht.';
    } elseif ($action === 'create' || $action === 'update') {
        $data = [
            'category'         => (string) $_POST['category'],
            'name'             => trim((string) $_POST['name']),
            'age_info'         => trim((string) $_POST['age_info']),
            'time_info'        => trim((string) $_POST['time_info']),
            'slug'             => trim((string) $_POST['slug']) ?: null,
            'nimbus_online_id' => trim((string) $_POST['nimbus_online_id']),
            'sort_order'       => (int) $_POST['sort_order'],
        ];
        if (!in_array($data['category'], ['Kinder', 'Jugendliche', 'Erwachsene'], true) || $data['name'] === '') {
            $message = 'Bitte Kategorie und Namen angeben.';
        } elseif ($action === 'create') {
            try {
                $stmt = $db->prepare(
                    'INSERT INTO courses (category, name, age_info, time_info, slug, nimbus_online_id, sort_order) VALUES (:category, :name, :age_info, :time_info, :slug, :nimbus_online_id, :sort_order)'
                );
                $stmt->execute($data);
                $message = 'Kurs angelegt.';
            } catch (PDOException $ex) {
                $message = 'Dieser Anmelde-Schlüssel wird bereits von einem anderen Kurs verwendet.';
            }
        } else {
            $data['id'] = (int) $_POST['id'];
            try {
                $stmt = $db->prepare(
                    'UPDATE courses SET category=:category, name=:name, age_info=:age_info, time_info=:time_info, slug=:slug, nimbus_online_id=:nimbus_online_id, sort_order=:sort_order WHERE id=:id'
                );
                $stmt->execute($data);
                $message = 'Kurs aktualisiert.';
            } catch (PDOException $ex) {
                $message = 'Dieser Anmelde-Schlüssel wird bereits von einem anderen Kurs verwendet.';
            }
        }
    }
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
$editing = null;
if ($editId) {
    $stmt = $db->prepare('SELECT * FROM courses WHERE id = :id');
    $stmt->execute(['id' => $editId]);
    $editing = $stmt->fetch() ?: null;
}

$grouped = get_courses_grouped();
$token = csrf_token();
$page_title = 'Kurse';
include __DIR__ . '/includes/layout_top.php';
?>
<?php if ($message): ?><p class="form-success"><?= e($message) ?></p><?php endif; ?>

<h2><?= $editing ? 'Kurs bearbeiten' : 'Neuen Kurs anlegen' ?></h2>
<form method="post" class="inline-form">
  <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
  <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
  <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $editing['id'] ?>"><?php endif; ?>
  <label>Kategorie
    <select name="category" required>
      <?php foreach (['Kinder', 'Jugendliche', 'Erwachsene'] as $cat): ?>
        <option value="<?= e($cat) ?>" <?= ($editing['category'] ?? '') === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Kursname
    <input type="text" name="name" required value="<?= e($editing['name'] ?? '') ?>">
  </label>
  <label>Altersangabe
    <input type="text" name="age_info" value="<?= e($editing['age_info'] ?? '') ?>">
  </label>
  <label>Zeitangabe
    <input type="text" name="time_info" value="<?= e($editing['time_info'] ?? '') ?>">
  </label>
  <label>Anmelde-Schlüssel (slug, z.B. "hiphop-l1")
    <input type="text" name="slug" value="<?= e($editing['slug'] ?? '') ?>" placeholder="leer lassen = kein Buchungslink">
  </label>
  <label>NimbusCloud Kurs-ID
    <input type="text" name="nimbus_online_id" value="<?= e($editing['nimbus_online_id'] ?? '') ?>" placeholder="z.B. 27">
  </label>
  <label>Reihenfolge (Zahl, kleiner = weiter oben)
    <input type="number" name="sort_order" value="<?= (int) ($editing['sort_order'] ?? 0) ?>">
  </label>
  <button type="submit"><?= $editing ? 'Speichern' : 'Anlegen' ?></button>
  <?php if ($editing): ?><a href="courses.php" class="button-secondary">Abbrechen</a><?php endif; ?>
</form>

<?php foreach ($grouped as $category => $items): ?>
  <h2><?= e($category) ?></h2>
  <table class="admin-table">
    <thead><tr><th>Name</th><th>Alter</th><th>Zeiten</th><th>Buchbar</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($items as $item): ?>
      <tr>
        <td><?= e($item['name']) ?></td>
        <td><?= e($item['age_info']) ?></td>
        <td><?= e($item['time_info']) ?></td>
        <td><?= ($item['slug'] && $item['nimbus_online_id']) ? '✓' : '—' ?></td>
        <td class="admin-table__actions">
          <a href="?edit=<?= (int) $item['id'] ?>">Bearbeiten</a>
          <form method="post" onsubmit="return confirm('Kurs wirklich löschen?');">
            <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
            <button type="submit" class="button-danger">Löschen</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$items): ?><tr><td colspan="5">Noch keine Kurse in dieser Kategorie.</td></tr><?php endif; ?>
    </tbody>
  </table>
<?php endforeach; ?>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
