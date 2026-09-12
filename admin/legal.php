<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_login();

const LEGAL_KEYS = ['legal_impressum_body', 'legal_datenschutz_body'];

$saved = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    foreach (LEGAL_KEYS as $key) {
        if (array_key_exists($key, $_POST)) {
            set_content($key, trim((string) $_POST[$key]));
        }
    }
    $saved = true;
}

$rows = get_db()->query(
    'SELECT content_key, label, content_value FROM content_blocks WHERE content_key IN (\'legal_impressum_body\', \'legal_datenschutz_body\')'
)->fetchAll();
$byKey = [];
foreach ($rows as $row) {
    $byKey[$row['content_key']] = $row;
}
$token = csrf_token();

$page_title = 'Impressum & Datenschutz';
include __DIR__ . '/includes/layout_top.php';
?>
<?php if ($saved): ?><p class="form-success">Gespeichert.</p><?php endif; ?>
<p class="form-hint">Platzhalter <code>{{contact_name}}</code>, <code>{{contact_address}}</code>, <code>{{contact_phone}}</code>, <code>{{contact_email}}</code> werden automatisch durch die Kontaktdaten aus "Texte" ersetzt. HTML-Tags (&lt;h2&gt;, &lt;p&gt;, &lt;br&gt;) bitte beim Bearbeiten nicht entfernen.</p>

<form method="post" class="stacked-form">
  <input type="hidden" name="csrf_token" value="<?= e($token) ?>">

  <h2>Impressum <a href="../impressum.php" target="_blank" class="admin-preview-link">ansehen ↗</a></h2>
  <label>
    Seiteninhalt (HTML)
    <textarea name="legal_impressum_body" rows="20" class="textarea-html"><?= e($byKey['legal_impressum_body']['content_value'] ?? '') ?></textarea>
  </label>

  <h2>Datenschutzerklärung <a href="../datenschutz.php" target="_blank" class="admin-preview-link">ansehen ↗</a></h2>
  <label>
    Seiteninhalt (HTML)
    <textarea name="legal_datenschutz_body" rows="28" class="textarea-html"><?= e($byKey['legal_datenschutz_body']['content_value'] ?? '') ?></textarea>
  </label>

  <button type="submit">Speichern</button>
</form>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
