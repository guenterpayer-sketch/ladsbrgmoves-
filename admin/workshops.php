<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_login();

$db = get_db();
$message = null;
$today = workshop_today();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'delete') {
        $stmt = $db->prepare('DELETE FROM workshops WHERE id = :id');
        $stmt->execute(['id' => (int) $_POST['id']]);
        $message = 'Workshop gelöscht.';
    } elseif ($action === 'create' || $action === 'update') {
        $data = [
            'title'            => trim((string) $_POST['title']),
            'event_date'       => trim((string) $_POST['event_date']),
            'time_info'        => trim((string) $_POST['time_info']),
            'trainer'          => trim((string) $_POST['trainer']),
            'price'            => trim((string) $_POST['price']),
            'description'      => trim((string) $_POST['description']),
            'nimbus_online_id' => trim((string) $_POST['nimbus_online_id']),
        ];
        $parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $data['event_date']);
        if ($data['title'] === '') {
            $message = 'Bitte einen Titel angeben.';
        } elseif ($parsedDate === false || $parsedDate->format('Y-m-d') !== $data['event_date']) {
            $message = 'Bitte ein gültiges Datum angeben.';
        } elseif ($data['nimbus_online_id'] !== '' && !ctype_digit($data['nimbus_online_id'])) {
            $message = 'Die NimbusCloud-ID darf nur aus Ziffern bestehen (oder leer bleiben).';
        } elseif ($action === 'create') {
            $stmt = $db->prepare(
                'INSERT INTO workshops (title, event_date, time_info, trainer, price, description, nimbus_online_id) VALUES (:title, :event_date, :time_info, :trainer, :price, :description, :nimbus_online_id)'
            );
            $stmt->execute($data);
            $message = 'Workshop angelegt.';
        } else {
            $data['id'] = (int) $_POST['id'];
            $stmt = $db->prepare(
                'UPDATE workshops SET title=:title, event_date=:event_date, time_info=:time_info, trainer=:trainer, price=:price, description=:description, nimbus_online_id=:nimbus_online_id WHERE id=:id'
            );
            $stmt->execute($data);
            $message = 'Workshop aktualisiert.';
        }
    }
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
$editing = null;
if ($editId) {
    $stmt = $db->prepare('SELECT * FROM workshops WHERE id = :id');
    $stmt->execute(['id' => $editId]);
    $editing = $stmt->fetch() ?: null;
}

$workshops = $db->query('SELECT * FROM workshops ORDER BY event_date DESC, id DESC')->fetchAll();
$token = csrf_token();
$page_title = 'Workshops';
include __DIR__ . '/includes/layout_top.php';
?>
<?php if ($message): ?><p class="form-success"><?= e($message) ?></p><?php endif; ?>

<p class="form-hint">Workshops erscheinen automatisch auf der Website, solange ihr Datum heute oder in der Zukunft liegt. Danach verschwinden sie von selbst; gibt es keinen kommenden Workshop, entfällt der ganze Bereich (inkl. Menüpunkt).</p>

<h2><?= $editing ? 'Workshop bearbeiten' : 'Neuen Workshop anlegen' ?></h2>
<form method="post" class="inline-form">
  <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
  <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
  <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $editing['id'] ?>"><?php endif; ?>
  <label>Titel
    <input type="text" name="title" required value="<?= e($editing['title'] ?? '') ?>">
  </label>
  <label>Datum
    <input type="date" name="event_date" required value="<?= e($editing['event_date'] ?? '') ?>">
  </label>
  <label>Uhrzeit (z.B. "14:00–16:00 Uhr")
    <input type="text" name="time_info" value="<?= e($editing['time_info'] ?? '') ?>">
  </label>
  <label>Trainer
    <input type="text" name="trainer" value="<?= e($editing['trainer'] ?? '') ?>">
  </label>
  <label>Preis (Freitext, z.B. "25 €")
    <input type="text" name="price" value="<?= e($editing['price'] ?? '') ?>">
  </label>
  <label>Beschreibung (Zeilenumbrüche werden übernommen)
    <textarea name="description" rows="4"><?= e($editing['description'] ?? '') ?></textarea>
  </label>
  <label>NimbusCloud-ID für die Anmeldung (leer = ohne Anmelde-Button)
    <input type="text" name="nimbus_online_id" inputmode="numeric" value="<?= e($editing['nimbus_online_id'] ?? '') ?>" placeholder="z.B. 150">
  </label>
  <button type="submit"><?= $editing ? 'Speichern' : 'Anlegen' ?></button>
  <?php if ($editing): ?><a href="workshops.php" class="button-secondary">Abbrechen</a><?php endif; ?>
</form>

<table class="admin-table">
  <thead><tr><th>Datum</th><th>Titel</th><th>Trainer</th><th>Preis</th><th>Anmeldung</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($workshops as $workshop): ?>
    <tr>
      <td><?= e(format_workshop_date($workshop['event_date'])) ?><?= $workshop['time_info'] !== '' ? '<br><small>' . e($workshop['time_info']) . '</small>' : '' ?></td>
      <td><?= e($workshop['title']) ?></td>
      <td><?= e($workshop['trainer'] ?: '—') ?></td>
      <td><?= e($workshop['price'] ?: '—') ?></td>
      <td><?= $workshop['nimbus_online_id'] !== '' ? '✓' : '—' ?></td>
      <td><?= $workshop['event_date'] >= $today ? 'sichtbar' : 'vorbei (ausgeblendet)' ?></td>
      <td class="admin-table__actions">
        <a href="?edit=<?= (int) $workshop['id'] ?>">Bearbeiten</a>
        <form method="post" onsubmit="return confirm('Workshop wirklich löschen?');">
          <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int) $workshop['id'] ?>">
          <button type="submit" class="button-danger">Löschen</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$workshops): ?><tr><td colspan="7">Noch keine Workshops angelegt.</td></tr><?php endif; ?>
  </tbody>
</table>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
