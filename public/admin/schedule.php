<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_login();

$db = get_db();
$message = null;
$weekdays = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'delete') {
        $stmt = $db->prepare('DELETE FROM schedule WHERE id = :id');
        $stmt->execute(['id' => (int) $_POST['id']]);
        $message = 'Eintrag gelöscht.';
    } elseif ($action === 'create' || $action === 'update') {
        $data = [
            'weekday'     => (string) $_POST['weekday'],
            'time'        => trim((string) $_POST['time']),
            'course_name' => trim((string) $_POST['course_name']),
            'age_info'    => trim((string) $_POST['age_info']),
            'trainer'     => trim((string) $_POST['trainer']),
            'sort_order'  => (int) $_POST['sort_order'],
        ];
        if (!in_array($data['weekday'], $weekdays, true) || $data['course_name'] === '' || $data['time'] === '') {
            $message = 'Bitte Wochentag, Uhrzeit und Kursname angeben.';
        } elseif ($action === 'create') {
            $stmt = $db->prepare(
                'INSERT INTO schedule (weekday, time, course_name, age_info, trainer, sort_order) VALUES (:weekday, :time, :course_name, :age_info, :trainer, :sort_order)'
            );
            $stmt->execute($data);
            $message = 'Eintrag angelegt.';
        } else {
            $data['id'] = (int) $_POST['id'];
            $stmt = $db->prepare(
                'UPDATE schedule SET weekday=:weekday, time=:time, course_name=:course_name, age_info=:age_info, trainer=:trainer, sort_order=:sort_order WHERE id=:id'
            );
            $stmt->execute($data);
            $message = 'Eintrag aktualisiert.';
        }
    }
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
$editing = null;
if ($editId) {
    $stmt = $db->prepare('SELECT * FROM schedule WHERE id = :id');
    $stmt->execute(['id' => $editId]);
    $editing = $stmt->fetch() ?: null;
}

$grouped = get_schedule_grouped();
$token = csrf_token();
$page_title = 'Stundenplan';
include __DIR__ . '/includes/layout_top.php';
?>
<?php if ($message): ?><p class="form-success"><?= e($message) ?></p><?php endif; ?>

<h2><?= $editing ? 'Eintrag bearbeiten' : 'Neuen Termin anlegen' ?></h2>
<form method="post" class="inline-form">
  <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
  <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
  <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $editing['id'] ?>"><?php endif; ?>
  <label>Wochentag
    <select name="weekday" required>
      <?php foreach ($weekdays as $wd): ?>
        <option value="<?= e($wd) ?>" <?= ($editing['weekday'] ?? '') === $wd ? 'selected' : '' ?>><?= e($wd) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Uhrzeit
    <input type="text" name="time" required placeholder="17:30 Uhr" value="<?= e($editing['time'] ?? '') ?>">
  </label>
  <label>Kursname
    <input type="text" name="course_name" required value="<?= e($editing['course_name'] ?? '') ?>">
  </label>
  <label>Altersangabe
    <input type="text" name="age_info" value="<?= e($editing['age_info'] ?? '') ?>">
  </label>
  <label>Trainer
    <input type="text" name="trainer" value="<?= e($editing['trainer'] ?? '') ?>">
  </label>
  <label>Reihenfolge (Zahl, kleiner = weiter oben)
    <input type="number" name="sort_order" value="<?= (int) ($editing['sort_order'] ?? 0) ?>">
  </label>
  <button type="submit"><?= $editing ? 'Speichern' : 'Anlegen' ?></button>
  <?php if ($editing): ?><a href="schedule.php" class="button-secondary">Abbrechen</a><?php endif; ?>
</form>

<?php foreach ($grouped as $weekday => $items): ?>
  <h2><?= e($weekday) ?></h2>
  <table class="admin-table">
    <thead><tr><th>Zeit</th><th>Kurs</th><th>Alter</th><th>Trainer</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($items as $item): ?>
      <tr>
        <td><?= e($item['time']) ?></td>
        <td><?= e($item['course_name']) ?></td>
        <td><?= e($item['age_info']) ?></td>
        <td><?= e($item['trainer']) ?></td>
        <td class="admin-table__actions">
          <a href="?edit=<?= (int) $item['id'] ?>">Bearbeiten</a>
          <form method="post" onsubmit="return confirm('Eintrag wirklich löschen?');">
            <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
            <button type="submit" class="button-danger">Löschen</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$items): ?><tr><td colspan="5">Keine Termine an diesem Tag.</td></tr><?php endif; ?>
    </tbody>
  </table>
<?php endforeach; ?>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
