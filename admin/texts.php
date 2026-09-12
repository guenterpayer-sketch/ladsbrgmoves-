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

// Legal-Texte (Impressum/Datenschutz) haben eine eigene, größere Verwaltungsseite.
$rows = get_db()->query(
    'SELECT content_key, label, content_value FROM content_blocks WHERE content_key NOT IN (\'legal_impressum_body\', \'legal_datenschutz_body\')'
)->fetchAll();
$byKey = [];
foreach ($rows as $row) {
    $byKey[$row['content_key']] = $row;
}

// Feste Gruppierung für eine übersichtliche Darstellung statt einer langen,
// alphabetischen Liste. Felder, die in keiner Gruppe stehen, landen automatisch
// unter "Sonstiges", damit nichts verloren geht.
$groups = [
    'Allgemein' => ['site_title', 'brand_name', 'brand_subtitle'],
    'Hero-Sektion (Slide 1)' => ['hero_kicker', 'hero_title_line1', 'hero_title_line2', 'hero_text', 'hero_cta_primary', 'hero_cta_secondary'],
    'Trainer-Sektion' => ['trainer_intro'],
    'Preise-Sektion' => ['pricing_intro', 'pricing_footnote'],
    'Kontakt' => ['contact_name', 'contact_address', 'contact_phone', 'contact_email'],
    'Footer' => ['footer_offer_text'],
];
$groupedKeys = array_merge(...array_values($groups));
$groups['Sonstiges'] = array_keys(array_diff_key($byKey, array_flip($groupedKeys)));

$token = csrf_token();

$page_title = 'Texte';
include __DIR__ . '/includes/layout_top.php';
?>
<?php if ($saved): ?><p class="form-success">Gespeichert.</p><?php endif; ?>
<form method="post" class="stacked-form">
  <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
  <?php foreach ($groups as $groupLabel => $keys): ?>
    <?php $keys = array_values(array_filter($keys, fn($k) => isset($byKey[$k]))); ?>
    <?php if (!$keys): continue; endif; ?>
    <h2><?= e($groupLabel) ?></h2>
    <?php foreach ($keys as $key): $row = $byKey[$key]; ?>
      <label>
        <?= e($row['label']) ?>
        <?php if (strlen($row['content_value']) > 80): ?>
          <textarea name="<?= e($row['content_key']) ?>" rows="3"><?= e($row['content_value']) ?></textarea>
        <?php else: ?>
          <input type="text" name="<?= e($row['content_key']) ?>" value="<?= e($row['content_value']) ?>">
        <?php endif; ?>
      </label>
    <?php endforeach; ?>
  <?php endforeach; ?>
  <button type="submit">Speichern</button>
</form>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
