<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_login();

$db = get_db();
$message = null;
$weekdays = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
$categories = ['Kinder', 'Jugendliche', 'Erwachsene', 'Gemischt'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'delete') {
        $stmt = $db->prepare('DELETE FROM schedule WHERE id = :id');
        $stmt->execute(['id' => (int) $_POST['id']]);
        $message = 'Eintrag gelöscht.';
    } elseif ($action === 'create' || $action === 'update') {
        $courseId = trim((string) ($_POST['course_id'] ?? ''));
        $data = [
            'weekday'     => (string) $_POST['weekday'],
            'time'        => trim((string) $_POST['time']),
            'course_name' => trim((string) $_POST['course_name']),
            'course_id'   => $courseId !== '' ? (int) $courseId : null,
            'age_info'    => trim((string) $_POST['age_info']),
            'trainer'     => trim((string) $_POST['trainer']),
            'category'    => (string) $_POST['category'],
            'sort_order'  => (int) $_POST['sort_order'],
        ];
        if (!in_array($data['weekday'], $weekdays, true) || $data['course_name'] === '' || $data['time'] === '' || !in_array($data['category'], $categories, true)) {
            $message = 'Bitte Wochentag, Uhrzeit, Kategorie und Kursname angeben.';
        } elseif ($action === 'create') {
            $stmt = $db->prepare(
                'INSERT INTO schedule (weekday, time, course_name, course_id, age_info, trainer, category, sort_order) VALUES (:weekday, :time, :course_name, :course_id, :age_info, :trainer, :category, :sort_order)'
            );
            $stmt->execute($data);
            $message = 'Eintrag angelegt.';
        } else {
            $data['id'] = (int) $_POST['id'];
            $stmt = $db->prepare(
                'UPDATE schedule SET weekday=:weekday, time=:time, course_name=:course_name, course_id=:course_id, age_info=:age_info, trainer=:trainer, category=:category, sort_order=:sort_order WHERE id=:id'
            );
            $stmt->execute($data);
            $message = 'Eintrag aktualisiert.';
        }
    }
}

// Für das Auswahlfeld: alle Kurse mit Anzeige-Label, das Kategorie + Alter
// unterscheidbar macht (wichtig, wenn derselbe Kursname mehrfach vorkommt,
// z.B. eine Alterstrennung wie bei Breakdance).
$allCourses = $db->query('SELECT id, category, name, age_info, slug, nimbus_online_id FROM courses ORDER BY category, name, id')->fetchAll();

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
  <label>Kursname (Anzeige im Stundenplan)
    <input type="text" name="course_name" required value="<?= e($editing['course_name'] ?? '') ?>">
  </label>
  <label>Verknüpfter Kurs (für den Buchungs-Button)
    <select name="course_id">
      <option value="">– kein Kurs verknüpft (freier Eintrag, kein Buchungslink) –</option>
      <?php foreach ($allCourses as $course): ?>
        <?php
          $label = $course['name'];
          if ($course['age_info']) { $label .= ' – ' . $course['age_info']; }
          $label .= ' (' . $course['category'] . ')';
          if ($course['slug'] === null || $course['nimbus_online_id'] === '') { $label .= ' [nicht buchbar]'; }
        ?>
        <option value="<?= (int) $course['id'] ?>" <?= (int) ($editing['course_id'] ?? 0) === (int) $course['id'] ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Altersangabe
    <input type="text" name="age_info" value="<?= e($editing['age_info'] ?? '') ?>">
  </label>
  <label>Trainer
    <input type="text" name="trainer" value="<?= e($editing['trainer'] ?? '') ?>">
  </label>
  <label>Kategorie (Farbe im Stundenplan)
    <select name="category" required>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= e($cat) ?>" <?= ($editing['category'] ?? 'Jugendliche') === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
      <?php endforeach; ?>
    </select>
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
    <thead><tr><th>Zeit</th><th>Kurs</th><th>Alter</th><th>Trainer</th><th>Kategorie</th><th>Buchbar</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($items as $item): ?>
      <tr>
        <td><?= e($item['time']) ?></td>
        <td><?= e($item['course_name']) ?></td>
        <td><?= e($item['age_info']) ?></td>
        <td><?= e($item['trainer']) ?></td>
        <td><?= e($item['category']) ?></td>
        <td><?= $item['course_slug'] ? '✓' : '—' ?></td>
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
    <?php if (!$items): ?><tr><td colspan="7">Keine Termine an diesem Tag.</td></tr><?php endif; ?>
    </tbody>
  </table>
<?php endforeach; ?>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
