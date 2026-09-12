<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_login();

$db = get_db();
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'delete') {
        $stmt = $db->prepare('DELETE FROM pricing_plans WHERE id = :id');
        $stmt->execute(['id' => (int) $_POST['id']]);
        $message = 'Preiskarte gelöscht.';
    } elseif ($action === 'create' || $action === 'update') {
        $data = [
            'name'          => trim((string) $_POST['name']),
            'tagline'       => trim((string) $_POST['tagline']),
            'price'         => (string) $_POST['price'],
            'price_unit'    => trim((string) $_POST['price_unit']),
            'included_text' => trim((string) $_POST['included_text']),
            'sort_order'    => (int) $_POST['sort_order'],
        ];
        if ($data['name'] === '' || $data['price'] === '' || !is_numeric($data['price'])) {
            $message = 'Bitte Name und einen gültigen Preis angeben.';
        } elseif ($action === 'create') {
            $stmt = $db->prepare(
                'INSERT INTO pricing_plans (name, tagline, price, price_unit, included_text, sort_order) VALUES (:name, :tagline, :price, :price_unit, :included_text, :sort_order)'
            );
            $stmt->execute($data);
            $message = 'Preiskarte angelegt.';
        } else {
            $data['id'] = (int) $_POST['id'];
            $stmt = $db->prepare(
                'UPDATE pricing_plans SET name=:name, tagline=:tagline, price=:price, price_unit=:price_unit, included_text=:included_text, sort_order=:sort_order WHERE id=:id'
            );
            $stmt->execute($data);
            $message = 'Preiskarte aktualisiert.';
        }
    }
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
$editing = null;
if ($editId) {
    $stmt = $db->prepare('SELECT * FROM pricing_plans WHERE id = :id');
    $stmt->execute(['id' => $editId]);
    $editing = $stmt->fetch() ?: null;
}

$plans = get_pricing_plans();
$token = csrf_token();
$page_title = 'Preise';
include __DIR__ . '/includes/layout_top.php';
?>
<?php if ($message): ?><p class="form-success"><?= e($message) ?></p><?php endif; ?>

<p>Die Einleitung und die Fußnote der Preise-Sektion werden unter <a href="texts.php">Texte</a> gepflegt (Felder "Preise: einleitender Text" / "Preise: Fußnote").</p>

<h2><?= $editing ? 'Preiskarte bearbeiten' : 'Neue Preiskarte anlegen' ?></h2>
<form method="post" class="inline-form">
  <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
  <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
  <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $editing['id'] ?>"><?php endif; ?>
  <label>Name (z.B. "Kinder")
    <input type="text" name="name" required value="<?= e($editing['name'] ?? '') ?>">
  </label>
  <label>Tagline (kurzer Zusatztext unter dem Namen)
    <input type="text" name="tagline" value="<?= e($editing['tagline'] ?? '') ?>">
  </label>
  <label>Preis (in Euro, z.B. 52.00)
    <input type="text" name="price" required value="<?= e($editing['price'] ?? '') ?>">
  </label>
  <label>Einheit (z.B. "pro Monat, pro Person")
    <input type="text" name="price_unit" value="<?= e($editing['price_unit'] ?? 'pro Monat, pro Person') ?>">
  </label>
  <label>Enthaltene Kurse (ein Text, mit Kommas getrennt)
    <textarea name="included_text" rows="3"><?= e($editing['included_text'] ?? '') ?></textarea>
  </label>
  <label>Reihenfolge (Zahl, kleiner = weiter links)
    <input type="number" name="sort_order" value="<?= (int) ($editing['sort_order'] ?? 0) ?>">
  </label>
  <button type="submit"><?= $editing ? 'Speichern' : 'Anlegen' ?></button>
  <?php if ($editing): ?><a href="pricing.php" class="button-secondary">Abbrechen</a><?php endif; ?>
</form>

<table class="admin-table">
  <thead><tr><th>Name</th><th>Tagline</th><th>Preis</th><th>Enthalten</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($plans as $plan): ?>
    <tr>
      <td><?= e($plan['name']) ?></td>
      <td><?= e($plan['tagline']) ?></td>
      <td><?= number_format((float) $plan['price'], 2, ',', '.') ?>&nbsp;€ <small><?= e($plan['price_unit']) ?></small></td>
      <td><?= e($plan['included_text']) ?></td>
      <td class="admin-table__actions">
        <a href="?edit=<?= (int) $plan['id'] ?>">Bearbeiten</a>
        <form method="post" onsubmit="return confirm('Preiskarte wirklich löschen?');">
          <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int) $plan['id'] ?>">
          <button type="submit" class="button-danger">Löschen</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$plans): ?><tr><td colspan="5">Keine Preiskarten angelegt.</td></tr><?php endif; ?>
  </tbody>
</table>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
