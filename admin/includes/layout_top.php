<?php
/** @var string $page_title */
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?> · Redaktionssystem · LNDSBRG MOVES</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
  <nav class="admin-nav">
    <div class="admin-nav__brand">LNDSBRG MOVES<span>Redaktion</span></div>
    <a href="dashboard.php">Übersicht</a>
    <a href="texts.php">Texte</a>
    <a href="courses.php">Kurse</a>
    <a href="schedule.php">Stundenplan</a>
    <a href="trainers.php">Trainer</a>
    <a href="pricing.php">Preise</a>
    <a href="users.php">Nutzer</a>
    <a href="../index.php" target="_blank">Seite ansehen ↗</a>
    <a href="logout.php" class="admin-nav__logout">Abmelden</a>
  </nav>
  <main class="admin-main">
    <h1><?= e($page_title) ?></h1>
