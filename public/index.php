<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

$courses = get_courses_grouped();
$schedule = get_schedule_grouped();
$trainers = get_trainers();
$bookable = get_bookable_courses();

$brandName = get_content('brand_name', 'LNDSBRG MOVES');
$firstLetter = mb_substr(preg_replace('/[^A-Za-z]/', '', $brandName) ?: 'L', 0, 1);

$categoryIcons = [
    'Kinder' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="7" r="3.2"/><path d="M5 21c0-4 3-6.5 7-6.5S19 17 19 21"/></svg>',
    'Jugendliche' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 2l2.3 5.8L20 9l-4.6 3.8L16.5 19 12 15.6 7.5 19l1.1-6.2L4 9l5.7-1.2z"/></svg>',
    'Erwachsene' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M13 2L4 14h7l-1 8 9-12h-7l1-8z"/></svg>',
];

$categoryLabels = ['Kinder' => 'Kinder', 'Jugendliche' => 'Jugendliche', 'Erwachsene' => 'Erwachsene'];
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(get_content('site_title', $brandName)) ?></title>
<meta name="description" content="<?= e(get_content('hero_text')) ?>">
<link rel="icon" href="assets/img/logo-icon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site-header">
  <nav class="site-nav">
    <a href="#top" class="logo">
      <span class="logo__main"><?= e($brandName) ?></span>
      <span class="logo__sub">
        <span class="logo__line"></span>
        <span class="logo__sub-text"><?= e(get_content('brand_subtitle', 'URBAN DANCE')) ?></span>
        <span class="logo__line"></span>
      </span>
    </a>
    <button class="site-nav__toggle" id="nav-toggle" aria-expanded="false" aria-controls="nav-links" aria-label="Menü öffnen">
      <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M2 5h14M2 9h14M2 13h14"/></svg>
    </button>
    <div class="site-nav__links" id="nav-links">
      <a href="#kurse">Kurse</a>
      <a href="#plan">Stundenplan</a>
      <a href="#trainer">Trainer</a>
      <a href="#kurse" class="site-nav__cta"><?= e(get_content('hero_cta_primary', 'Schnupperstunde sichern')) ?></a>
    </div>
  </nav>
</header>

<main id="top">
  <section class="hero">
    <div class="hero__stripes" aria-hidden="true"></div>
    <div class="hero__ghost" aria-hidden="true"><?= e($firstLetter) ?></div>
    <div class="hero__content">
      <div class="hero__inner">
        <p class="badge"><?= e(get_content('hero_kicker')) ?></p>
        <h1 class="hero__title">
          <?= e(get_content('hero_title_line1')) ?><br>
          <span><?= e(get_content('hero_title_line2')) ?></span>
        </h1>
        <p class="hero__text"><?= e(get_content('hero_text')) ?></p>
        <div class="btns">
          <a href="#kurse" class="button-primary"><?= e(get_content('hero_cta_primary', 'Schnupperstunde sichern')) ?></a>
          <a href="#kurse" class="button-secondary">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="7" stroke="#e03636" stroke-width="1"/><path d="M6.5 5.5l4 2.5-4 2.5V5.5z" fill="#e03636"/></svg>
            <?= e(get_content('hero_cta_secondary', 'Kurse entdecken')) ?>
          </a>
        </div>
        <div class="hero__stats">
          <div class="stat">
            <span class="stat__value" data-countup data-end="<?= (int) get_content('stat_courses_value', '0') ?>" data-suffix="<?= e(get_content('stat_courses_suffix')) ?>">0</span>
            <span class="stat__label"><?= e(get_content('stat_courses_label')) ?></span>
          </div>
          <div class="stat">
            <span class="stat__value" data-countup data-end="<?= (int) get_content('stat_years_value', '0') ?>" data-suffix="<?= e(get_content('stat_years_suffix')) ?>">0</span>
            <span class="stat__label"><?= e(get_content('stat_years_label')) ?></span>
          </div>
          <div class="stat">
            <span class="stat__value" data-countup data-end="<?= (int) get_content('stat_trainers_value', '0') ?>" data-suffix="<?= e(get_content('stat_trainers_suffix')) ?>">0</span>
            <span class="stat__label"><?= e(get_content('stat_trainers_label')) ?></span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="kurse" class="section">
    <div class="section__inner">
      <h2 class="section__title">UNSERE <span>KURSE</span></h2>
      <p class="section__intro">Klick auf einen buchbaren Kurs, um freie Termine für die kostenlose Schnupperstunde zu sehen.</p>

      <div class="tabs" role="tablist">
        <?php $first = true; foreach ($categoryLabels as $catKey => $catLabel): ?>
          <button type="button" class="tab-btn" role="tab" data-tab-target="tab-<?= e(category_slug($catKey)) ?>" aria-selected="<?= $first ? 'true' : 'false' ?>"><?= e($catLabel) ?></button>
        <?php $first = false; endforeach; ?>
      </div>

      <?php $first = true; foreach ($categoryLabels as $catKey => $catLabel): ?>
        <div class="tab-panel <?= $first ? 'is-active' : '' ?>" id="tab-<?= e(category_slug($catKey)) ?>">
          <div class="course-grid">
            <?php foreach ($courses[$catKey] as $course): ?>
              <?php $bookableCard = $course['slug'] !== '' && $course['nimbus_online_id'] !== ''; ?>
              <?php $cardBody = '<div class="course-card__icon">' . $categoryIcons[$catKey] . '</div>'
                  . '<div class="course-card__name">' . e($course['name']) . '</div>'
                  . '<div class="course-card__meta">' . ($course['age_info'] ? e($course['age_info']) . '<br>' : '') . e($course['time_info']) . '</div>'
                  . ($bookableCard ? '<span class="course-card__cta">Termine ansehen →</span>' : ''); ?>
              <?php if ($bookableCard): ?>
                <button type="button" class="course-card course-card--bookable" onclick="lmOpenModal('<?= e($course['slug']) ?>')"><?= $cardBody ?></button>
              <?php else: ?>
                <div class="course-card"><?= $cardBody ?></div>
              <?php endif; ?>
            <?php endforeach; ?>
            <?php if (!$courses[$catKey]): ?><p class="schedule-empty">Noch keine Kurse in dieser Kategorie.</p><?php endif; ?>
          </div>
        </div>
      <?php $first = false; endforeach; ?>
    </div>
  </section>

  <section id="plan" class="section">
    <div class="section__inner">
      <h2 class="section__title">STUNDEN<span>PLAN</span></h2>
      <div class="schedule-legend">
        <span class="schedule-legend__item"><span class="schedule-legend__dot" style="background:var(--cat-kinder)"></span>Kinder</span>
        <span class="schedule-legend__item"><span class="schedule-legend__dot" style="background:var(--cat-jugendliche)"></span>Jugendliche</span>
        <span class="schedule-legend__item"><span class="schedule-legend__dot" style="background:var(--cat-erwachsene)"></span>Erwachsene</span>
        <span class="schedule-legend__item"><span class="schedule-legend__dot" style="background:var(--cat-gemischt)"></span>Jugend &amp; Erwachsene</span>
      </div>
      <div class="schedule-grid">
        <?php foreach ($schedule as $weekday => $items): ?>
          <div class="schedule-day">
            <h3><?= e($weekday) ?></h3>
            <?php if ($items): ?>
              <ul>
                <?php foreach ($items as $entry): ?>
                  <li class="schedule-entry schedule-entry--<?= e(category_slug($entry['category'])) ?>">
                    <span class="schedule-entry__time"><?= e($entry['time']) ?></span>
                    <span class="schedule-entry__course"><?= e($entry['course_name']) ?></span>
                    <?php if ($entry['trainer']): ?><span class="schedule-entry__meta">– <?= e($entry['trainer']) ?></span><?php endif; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="schedule-empty">–</p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="trainer" class="section">
    <div class="section__inner">
      <h2 class="section__title">DEINE <span>TRAINER</span></h2>
      <?php if (get_content('trainer_intro')): ?><p class="section__intro"><?= e(get_content('trainer_intro')) ?></p><?php endif; ?>
      <div class="trainer-grid">
        <?php foreach ($trainers as $trainer): ?>
          <div class="trainer-card">
            <?php if ($trainer['photo_path']): ?>
              <img class="trainer-card__photo" src="<?= e($trainer['photo_path']) ?>" alt="<?= e($trainer['name']) ?>">
            <?php else: ?>
              <div class="trainer-card__placeholder"><?= e(mb_substr($trainer['name'], 0, 1)) ?></div>
            <?php endif; ?>
            <div>
              <div class="trainer-card__name"><?= e($trainer['name']) ?></div>
              <?php if ($trainer['bio']): ?><div class="trainer-card__bio"><?= e($trainer['bio']) ?></div><?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="section section--hint">
    <div class="section__inner">
      <h2 class="section__title">SO EINFACH GEHT'S</h2>
      <p>Kurs auswählen, freien Termin für die kostenlose Schnupperstunde buchen, vorbeikommen. Keine Verpflichtung, keine Vorkenntnisse nötig.</p>
    </div>
  </section>
</main>

<footer class="site-footer">
  <div class="site-footer__inner">
    <div>
      <div><?= e(get_content('footer_offer_text')) ?></div>
      <div class="site-footer__contact">
        <?= e(get_content('contact_name')) ?> · <?= e(get_content('contact_address')) ?> ·
        <a href="tel:<?= e(preg_replace('/\s+/', '', get_content('contact_phone'))) ?>"><?= e(get_content('contact_phone')) ?></a> ·
        <a href="mailto:<?= e(get_content('contact_email')) ?>"><?= e(get_content('contact_email')) ?></a>
      </div>
    </div>
    <div class="site-footer__links">
      <a href="impressum.php">Impressum</a>
      <a href="datenschutz.php">Datenschutz</a>
    </div>
  </div>
</footer>

<div class="lm-modal-bg" id="lm-modal" role="dialog" aria-modal="true" aria-labelledby="lm-modal-title">
  <div class="lm-modal">
    <div class="lm-modal__head">
      <div class="lm-modal__title" id="lm-modal-title">Kurs</div>
      <button type="button" class="lm-modal__close" onclick="lmCloseModal()" aria-label="Schließen">✕</button>
    </div>
    <div class="lm-modal__body">
      <iframe id="lm-modal-iframe" src="" title="Kursanmeldung"></iframe>
    </div>
  </div>
</div>

<script>
var lmKurse = <?= json_encode(array_column($bookable, null, 'slug'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
var lmBaseUrl = 'https://tanzcenter-payer.nimbuscloud.at/index.php?c=PublicCustomers&what=courses&level=';

function lmOpenModal(slug) {
  var kurs = lmKurse[slug];
  if (!kurs) return;
  document.getElementById('lm-modal-title').textContent = kurs.name;
  document.getElementById('lm-modal-iframe').src = lmBaseUrl + kurs.nimbus_online_id;
  document.getElementById('lm-modal').classList.add('is-open');
  document.body.style.overflow = 'hidden';
}

function lmCloseModal() {
  document.getElementById('lm-modal').classList.remove('is-open');
  document.getElementById('lm-modal-iframe').src = '';
  document.body.style.overflow = '';
}

document.getElementById('lm-modal').addEventListener('click', function (e) {
  if (e.target === this) lmCloseModal();
});
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') lmCloseModal();
});

// Mobile-Navigation
var navToggle = document.getElementById('nav-toggle');
var navLinks = document.getElementById('nav-links');
navToggle.addEventListener('click', function () {
  var isOpen = navLinks.classList.toggle('is-open');
  navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
});
navLinks.querySelectorAll('a').forEach(function (link) {
  link.addEventListener('click', function () {
    navLinks.classList.remove('is-open');
    navToggle.setAttribute('aria-expanded', 'false');
  });
});

// Kurs-Tabs
document.querySelectorAll('.tab-btn').forEach(function (btn) {
  btn.addEventListener('click', function () {
    document.querySelectorAll('.tab-btn').forEach(function (b) { b.setAttribute('aria-selected', 'false'); });
    document.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.remove('is-active'); });
    btn.setAttribute('aria-selected', 'true');
    document.getElementById(btn.dataset.tabTarget).classList.add('is-active');
  });
});

// Stats: Count-up beim Sichtbarwerden
var countEls = document.querySelectorAll('[data-countup]');
var counted = false;
function runCountUp() {
  if (counted) return;
  counted = true;
  countEls.forEach(function (el) {
    var end = parseInt(el.dataset.end, 10) || 0;
    var suffix = el.dataset.suffix || '';
    var duration = 1200;
    var start = performance.now();
    function tick(now) {
      var progress = Math.min((now - start) / duration, 1);
      el.textContent = Math.round(progress * end) + suffix;
      if (progress < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  });
}
if (countEls.length) {
  var observer = new IntersectionObserver(function (entries) {
    if (entries[0].isIntersecting) runCountUp();
  }, { threshold: 0.4 });
  observer.observe(countEls[0]);
}
</script>

</body>
</html>
