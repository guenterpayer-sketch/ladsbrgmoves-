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
<title>Datenschutz · <?= e($brandName) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/legal.css">
</head>
<body>
<div class="legal-page">
  <a href="index.php" class="legal-back">← Zurück</a>
  <h1>Datenschutzerklärung</h1>

  <h2>Allgemeiner Datenschutzpassus</h2>
  <p>Die folgenden Hinweise geben einen einfachen Überblick darüber, was mit Ihren
  personenbezogenen Daten passiert, wenn Sie diese Website besuchen. Personenbezogene
  Daten sind alle Daten, mit denen Sie persönlich identifiziert werden können.</p>

  <h2>Hinweis zur verantwortlichen Stelle</h2>
  <p>
    <?= e(get_content('contact_name')) ?>, Günter Payer<br>
    <?= e(get_content('contact_address')) ?><br>
    Telefon: <?= e(get_content('contact_phone')) ?><br>
    E-Mail: <?= e(get_content('contact_email')) ?>
  </p>
  <p>Verantwortliche Stelle ist die natürliche oder juristische Person, die allein oder
  gemeinsam mit anderen über die Zwecke und Mittel der Verarbeitung von personenbezogenen
  Daten entscheidet.</p>

  <h2>Wie erfassen wir Ihre Daten?</h2>
  <p>Ihre Daten werden zum einen dadurch erhoben, dass Sie uns diese mitteilen, z.B. über ein
  Anmeldeformular für eine Schnupperstunde. Andere Daten werden automatisch beim Besuch der
  Website erfasst (z.B. Internetbrowser, Betriebssystem, Uhrzeit des Seitenaufrufs).</p>

  <h2>Wofür nutzen wir Ihre Daten?</h2>
  <p>Ein Teil der Daten wird erhoben, um eine fehlerfreie Bereitstellung der Website zu
  gewährleisten. Andere Daten können zur Analyse Ihres Nutzerverhaltens verwendet werden.</p>

  <h2>Anmeldeformular für Schnupperstunden (NimbusCloud)</h2>
  <p>Für die Buchung einer kostenlosen Schnupperstunde wird ein Anmeldeformular unseres
  externen Anbieters NimbusCloud (tanzcenter-payer.nimbuscloud.at) eingebunden. Beim Öffnen
  dieses Formulars werden Daten an NimbusCloud übertragen; es gilt zusätzlich die
  Datenschutzerklärung von NimbusCloud.</p>

  <h2>Welche Rechte haben Sie bezüglich Ihrer Daten?</h2>
  <p>Sie haben jederzeit das Recht, unentgeltlich Auskunft über Herkunft, Empfänger und
  Zweck Ihrer gespeicherten personenbezogenen Daten zu erhalten sowie deren Berichtigung,
  Sperrung oder Löschung zu verlangen. Hierzu sowie zu weiteren Fragen können Sie sich
  jederzeit an die oben genannte Adresse wenden. Außerdem steht Ihnen ein Beschwerderecht
  bei der zuständigen Aufsichtsbehörde zu.</p>

  <h2>SSL-/TLS-Verschlüsselung</h2>
  <p>Diese Seite nutzt aus Sicherheitsgründen eine SSL- bzw. TLS-Verschlüsselung. Eine
  verschlüsselte Verbindung erkennen Sie daran, dass die Adresszeile des Browsers von
  „http://" auf „https://" wechselt und an dem Schloss-Symbol in der Browserzeile.</p>

  <h2>Cookies</h2>
  <p>Diese Website verwendet ggf. Cookies, um das Angebot nutzerfreundlicher, effektiver und
  sicherer zu machen. Die meisten der verwendeten Cookies sind sogenannte
  „Session-Cookies", die nach Ende des Besuchs automatisch gelöscht werden.</p>

  <p style="margin-top:32px;font-size:0.8rem;color:var(--text-dimmer);">
    Hinweis: Dieser Text basiert auf der bisherigen Datenschutzerklärung von
    hiphoplandsberg.de und wurde um den Hinweis zur NimbusCloud-Einbindung ergänzt. Bitte
    fachlich prüfen (lassen), bevor die Seite live geht.
  </p>
</div>
</body>
</html>
