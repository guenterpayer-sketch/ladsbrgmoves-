<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
$brandName = get_content('brand_name', 'LNDSBRG MOVES');
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Impressum · <?= e($brandName) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/legal.css">
</head>
<body>
<div class="legal-page">
  <a href="index.php" class="legal-back">← Zurück</a>
  <h1>Impressum</h1>

  <h2>Angaben gemäß § 5 TMG</h2>
  <p>
    <?= e(get_content('contact_name')) ?><br>
    Inhaber: Günter Payer<br>
    <?= e(get_content('contact_address')) ?>
  </p>

  <h2>Kontakt</h2>
  <p>
    Telefon: <?= e(get_content('contact_phone')) ?><br>
    E-Mail: <?= e(get_content('contact_email')) ?>
  </p>

  <h2>Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h2>
  <p>
    Günter Payer<br>
    <?= e(get_content('contact_address')) ?>
  </p>

  <p style="margin-top:32px;font-size:0.8rem;color:var(--text-dimmer);">
    Hinweis: Dies ist ein nach § 5 TMG minimal notwendiges Standard-Impressum auf Basis der
    verfügbaren Kontaktdaten. Bitte prüfen, ob weitere Pflichtangaben ergänzt werden müssen
    (z.B. Umsatzsteuer-ID, Handelsregistereintrag), falls zutreffend.
  </p>
</div>
</body>
</html>
