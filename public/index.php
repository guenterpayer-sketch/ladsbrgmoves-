<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

$courses = get_courses_grouped();
$schedule = get_schedule_grouped();
$trainers = get_trainers();

$coursesPerWeek = count_courses_per_week();
$trainerCount = count_trainers();
$years = years_since_founded();
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(get_content('site_title', 'LNDSBERG MOVES')) ?></title>
<meta name="description" content="<?= e(get_content('hero_text')) ?>">
<link rel="icon" href="assets/img/logo-icon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site-header">
  <nav class="site-nav">
    <a href="#top" class="site-nav__brand">
      <img src="assets/img/logo.png" alt="LNDSBERG MOVES">
    </a>
    <div class="site-nav__links">
      <a href="#kurse">Kurse</a>
      <a href="#plan">Stundenplan</a>
      <a href="#trainer">Trainer</a>
      <a href="#schnupperstunde" class="site-nav__cta"><?= e(get_content('hero_cta', 'Schnupperstunde sichern')) ?></a>
    </div>
  </nav>
</header>

<main id="top">
  <section class="hero">
    <p class="hero__kicker"><?= e(get_content('hero_kicker')) ?></p>
    <h1 class="hero__title"><?= e(get_content('hero_title')) ?></h1>
    <p class="hero__text"><?= e(get_content('hero_text')) ?></p>
    <a href="#schnupperstunde" class="button-primary"><?= e(get_content('hero_cta', 'Schnupperstunde sichern')) ?></a>

    <div class="hero__stats">
      <div class="stat">
        <span class="stat__value"><?= (int) $coursesPerWeek ?></span>
        <span class="stat__label">Kurse pro Woche</span>
      </div>
      <div class="stat">
        <span class="stat__value"><?= (int) $years ?></span>
        <span class="stat__label">Jahre Tanzcenter Payer</span>
      </div>
      <div class="stat">
        <span class="stat__value"><?= (int) $trainerCount ?></span>
        <span class="stat__label">Ausgebildete Tanzlehrer</span>
      </div>
    </div>
  </section>

  <section id="kurse" class="section">
    <h2 class="section__title">Kurse</h2>
    <div class="course-groups">
      <?php foreach ($courses as $category => $items): ?>
        <?php if (!$items) continue; ?>
        <div class="course-group">
          <h3><?= e($category) ?></h3>
          <ul class="course-list">
            <?php foreach ($items as $course): ?>
              <li class="course-card">
                <strong><?= e($course['name']) ?></strong>
                <?php if ($course['age_info']): ?><span class="course-card__age"><?= e($course['age_info']) ?></span><?php endif; ?>
                <?php if ($course['time_info']): ?><span class="course-card__time"><?= e($course['time_info']) ?></span><?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section id="plan" class="section section--alt">
    <h2 class="section__title">Stundenplan</h2>
    <div class="schedule-grid">
      <?php foreach ($schedule as $weekday => $items): ?>
        <div class="schedule-day">
          <h3><?= e($weekday) ?></h3>
          <?php if ($items): ?>
            <ul>
              <?php foreach ($items as $entry): ?>
                <li>
                  <span class="schedule-time"><?= e($entry['time']) ?></span>
                  <span class="schedule-course"><?= e($entry['course_name']) ?></span>
                  <?php if ($entry['age_info']): ?><span class="schedule-age"><?= e($entry['age_info']) ?></span><?php endif; ?>
                  <?php if ($entry['trainer']): ?><span class="schedule-trainer">– <?= e($entry['trainer']) ?></span><?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="schedule-empty">–</p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section id="trainer" class="section">
    <h2 class="section__title">Trainer</h2>
    <div class="trainer-grid">
      <?php foreach ($trainers as $trainer): ?>
        <div class="trainer-card">
          <?php if ($trainer['photo_path']): ?>
            <img src="<?= e($trainer['photo_path']) ?>" alt="<?= e($trainer['name']) ?>">
          <?php else: ?>
            <div class="trainer-card__placeholder"><?= e(mb_substr($trainer['name'], 0, 1)) ?></div>
          <?php endif; ?>
          <h3><?= e($trainer['name']) ?></h3>
          <?php if ($trainer['bio']): ?><p><?= e($trainer['bio']) ?></p><?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section id="schnupperstunde" class="section section--cta">
    <h2 class="section__title"><?= e(get_content('cta_title', 'Schnupperstunde sichern')) ?></h2>
    <p><?= e(get_content('cta_text')) ?></p>
    <a href="mailto:<?= e(get_content('contact_email')) ?>" class="button-primary">Jetzt anfragen</a>
  </section>
</main>

<footer class="site-footer">
  <p><?= e(get_content('contact_name')) ?></p>
  <p><?= e(get_content('contact_address')) ?></p>
  <p>
    <a href="tel:<?= e(preg_replace('/\s+/', '', get_content('contact_phone'))) ?>"><?= e(get_content('contact_phone')) ?></a>
    ·
    <a href="mailto:<?= e(get_content('contact_email')) ?>"><?= e(get_content('contact_email')) ?></a>
  </p>
</footer>

</body>
</html>
