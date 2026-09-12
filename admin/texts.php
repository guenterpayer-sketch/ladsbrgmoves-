<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_login();

$saved = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $existing = array_keys(get_all_content());
    foreach ($existing as $key) {
        if (array_key_exists($key, $_POST)) {
            set_content($key, trim((string) $_POST[$key]));
        }
    }
    $saved = true;
}

$rows = get_db()->query('SELECT content_key, label, content_value FROM content_blocks ORDER BY label')->fetchAll();
$token = csrf_token();

$page_title = 'Texte';
include __DIR__ . '/includes/layout_top.php';
?>
<?php if ($saved): ?><p class="form-success">Gespeichert.</p><?php endif; ?>
<form method="post" class="stacked-form">
  <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
  <?php foreach ($rows as $row): ?>
    <label>
      <?= e($row['label']) ?>
      <?php if (strlen($row['content_value']) > 300): ?>
        <textarea name="<?= e($row['content_key']) ?>" rows="20" class="textarea-html"><?= e($row['content_value']) ?></textarea>
        <p class="form-hint">Dieses Feld enthält HTML (Überschriften &lt;h2&gt;, Absätze &lt;p&gt;). Bitte beim Bearbeiten die Tags nicht entfernen, sonst geht die Formatierung verloren.</p>
      <?php elseif (strlen($row['content_value']) > 80): ?>
        <textarea name="<?= e($row['content_key']) ?>" rows="3"><?= e($row['content_value']) ?></textarea>
      <?php else: ?>
        <input type="text" name="<?= e($row['content_key']) ?>" value="<?= e($row['content_value']) ?>">
      <?php endif; ?>
    </label>
  <?php endforeach; ?>
  <button type="submit">Speichern</button>
</form>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
