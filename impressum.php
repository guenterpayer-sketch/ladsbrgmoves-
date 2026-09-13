<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
$brandName = get_content('brand_name', 'LNDSBRG MOVES');
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Impressum · <?= e($brandName) ?></title>
<link rel="canonical" href="https://lndsbrgmoves.de/impressum.php">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/legal.css">
</head>
<body>
<div class="legal-page">
  <a href="index.php" class="legal-back">← Zurück</a>
  <h1>Impressum</h1>

  <?= render_legal_body('legal_impressum_body') ?>
</div>
</body>
</html>
