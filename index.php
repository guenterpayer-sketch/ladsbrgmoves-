<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$courses = get_courses_grouped();
$schedule = get_schedule_grouped();
$trainers = get_trainers();
$bookable = get_bookable_courses();

$brandName = get_content('brand_name', 'LNDSBRG MOVES');

$categoryLabels = ['Kinder' => 'Kinder', 'Jugendliche' => 'Jugendliche', 'Erwachsene' => 'Erwachsene'];
$weekdayLabels = ['Mo' => 'Montag', 'Di' => 'Dienstag', 'Mi' => 'Mittwoch', 'Do' => 'Donnerstag', 'Fr' => 'Freitag', 'Sa' => 'Samstag', 'So' => 'Sonntag'];

// SEO: Basis-URL, Meta-Beschreibung und strukturierte Daten (Schema.org DanceSchool),
// damit die Seite bei Google für Suchen wie "Hip Hop", "K-Pop" oder "Urban Dance"
// (jeweils mit Ortsbezug) korrekt eingeordnet werden kann.
$siteUrl = 'https://lndsbrgmoves.de/';
$metaDescription = get_content('hero_text');
$ogImage = $siteUrl . 'assets/img/logo-lockup.png';

// "Max-Planck-Str. 2, 86899 Landsberg am Lech" -> Straße / PLZ / Ort für die
// PostalAddress im JSON-LD. Fällt bei unerwartetem Format auf die Rohadresse zurück,
// statt einen Fehler zu werfen (Adresse kommt aus dem Admin-Textfeld "Kontakt").
$rawAddress = get_content('contact_address');
$street = $rawAddress;
$postalCode = '';
$locality = '';
$addressParts = array_map('trim', explode(',', $rawAddress, 2));
if (count($addressParts) === 2) {
    $street = $addressParts[0];
    if (preg_match('/^(\d{4,5})\s+(.+)$/', $addressParts[1], $m)) {
        $postalCode = $m[1];
        $locality = $m[2];
    } else {
        $locality = $addressParts[1];
    }
}

$structuredData = [
    '@context' => 'https://schema.org',
    '@type' => 'DanceSchool',
    'name' => $brandName,
    'alternateName' => get_content('contact_name'),
    'url' => $siteUrl,
    'logo' => $ogImage,
    'image' => $ogImage,
    'description' => $metaDescription,
    'telephone' => get_content('contact_phone'),
    'email' => get_content('contact_email'),
    'address' => array_filter([
        '@type' => 'PostalAddress',
        'streetAddress' => $street,
        'postalCode' => $postalCode,
        'addressLocality' => $locality,
        'addressCountry' => 'DE',
    ]),
    'areaServed' => $locality !== '' ? $locality : 'Landsberg am Lech',
    'makesOffer' => array_map(
        fn(array $course) => [
            '@type' => 'Offer',
            'itemOffered' => ['@type' => 'Course', 'name' => $course['name']],
        ],
        array_merge(...array_values($courses))
    ),
];
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(get_content('site_title', $brandName)) ?></title>
<meta name="description" content="<?= e($metaDescription) ?>">
<link rel="canonical" href="<?= e($siteUrl) ?>">
<link rel="icon" href="assets/img/logo-icon.png">

<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e($brandName) ?>">
<meta property="og:locale" content="de_DE">
<meta property="og:url" content="<?= e($siteUrl) ?>">
<meta property="og:title" content="<?= e(get_content('site_title', $brandName)) ?>">
<meta property="og:description" content="<?= e($metaDescription) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e(get_content('site_title', $brandName)) ?>">
<meta name="twitter:description" content="<?= e($metaDescription) ?>">
<meta name="twitter:image" content="<?= e($ogImage) ?>">

<script type="application/ld+json"><?= json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="stories-page">

<header class="site-header">
  <nav class="site-nav">
    <a href="#top" class="logo">
      <img class="logo__img" src="assets/img/logo-lockup.png" alt="<?= e($brandName) ?>">
    </a>
    <button class="nav-toggle" id="nav-toggle" aria-expanded="false" aria-controls="nav-links" aria-label="Menü öffnen">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M2 4h12M2 8h12M2 12h12"/></svg>
    </button>
  </nav>
  <div class="nav-links" id="nav-links">
    <a href="#kurse">Kurse</a>
    <a href="#plan">Stundenplan</a>
    <a href="#trainer">Trainer</a>
    <a href="#preise">Preise</a>
    <a href="impressum.php">Impressum</a>
    <a href="datenschutz.php">Datenschutz</a>
  </div>
</header>

<main>

<div class="story-progress" id="story-progress" role="tablist" aria-label="Seiten"></div>

<div class="stories-wrap">
<button type="button" class="story-nav-btn story-nav-btn--prev" id="story-prev" aria-label="Zurück" hidden>
  <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 2 4 7l5 5"/></svg>
</button>
<button type="button" class="story-nav-btn story-nav-btn--next" id="story-next" aria-label="Weiter">
  <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 2l5 5-5 5"/></svg>
</button>

<div class="stories" id="stories">

  <!-- Slide 1: Intro -->
  <section class="panel panel--intro" id="top">
    <div class="glow" aria-hidden="true"></div>
    <img class="hero-logo" src="assets/img/logo-lockup.png" alt="<?= e($brandName) ?>">
    <p class="badge"><?= e(get_content('hero_kicker')) ?></p>
    <h1>
      <span><?= e(get_content('hero_title_line1')) ?></span><br>
      <span class="grad-text"><?= e(get_content('hero_title_line2')) ?></span>
    </h1>
    <div class="scroll-hint scroll-hint--mobile">
      <span>Swipe für mehr</span>
      <svg width="20" height="14" viewBox="0 0 20 14" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M1 7h18M13 1l6 6-6 6"/></svg>
    </div>
    <div class="scroll-hint scroll-hint--desktop">
      <span>Scroll für mehr</span>
      <svg width="14" height="20" viewBox="0 0 14 20" fill="none" stroke="currentColor" stroke-width="1.3"><rect x="1" y="1" width="12" height="18" rx="6"/><circle cx="7" cy="6" r="1.4" fill="currentColor" stroke="none"/></svg>
    </div>
  </section>

  <!-- Slide 2: Pitch + CTA -->
  <section class="panel panel--pitch">
    <div class="panel__inner">
      <p><?= e(get_content('hero_text')) ?></p>
      <a href="#kurse" class="button-primary"><?= e(get_content('hero_cta_primary', 'Schnupperstunde sichern')) ?></a>
      <br>
      <a href="#kurse" class="button-secondary">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 7h10M8 3l4 4-4 4"/></svg>
        <?= e(get_content('hero_cta_secondary', 'Kurse entdecken')) ?>
      </a>
    </div>
  </section>

  <!-- Slide 3: Kurse -->
  <section class="panel" id="kurse">
    <div class="panel__inner">
      <h2 class="panel__title">Unsere <span class="grad-text">Kurse</span></h2>
      <p class="panel__intro-text">Klick auf einen buchbaren Kurs, um freie Termine für die kostenlose Schnupperstunde zu sehen.</p>

      <div class="tab-group" data-group="kurse">
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
                <?php $cardBody = '<div class="course-card__name">' . e($course['name']) . '</div>'
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
    </div>
  </section>

  <!-- Slide 4: Stundenplan (Tag für Tag, wie hiphoplandsberg.de) -->
  <section class="panel" id="plan">
    <div class="panel__inner">
      <h2 class="panel__title">Stunden<span class="grad-text">plan</span></h2>
      <div class="schedule-legend">
        <span class="schedule-legend__item"><span class="schedule-legend__dot" style="background:var(--cat-kinder)"></span>Kinder</span>
        <span class="schedule-legend__item"><span class="schedule-legend__dot" style="background:var(--cat-jugendliche)"></span>Jugendliche</span>
        <span class="schedule-legend__item"><span class="schedule-legend__dot" style="background:var(--cat-erwachsene)"></span>Erwachsene</span>
        <span class="schedule-legend__item"><span class="schedule-legend__dot" style="background:var(--cat-gemischt)"></span>Jugend &amp; Erwachsene</span>
      </div>

      <div class="tab-group" data-group="plan">
        <div class="tabs" role="tablist">
          <?php $first = true; foreach ($weekdayLabels as $dayKey => $dayLabel): ?>
            <button type="button" class="tab-btn" role="tab" data-tab-target="day-<?= e($dayKey) ?>" aria-selected="<?= $first ? 'true' : 'false' ?>"><?= e($dayKey) ?></button>
          <?php $first = false; endforeach; ?>
        </div>

        <?php $first = true; foreach ($weekdayLabels as $dayKey => $dayLabel): ?>
          <?php $items = $schedule[$dayKey] ?? []; ?>
          <div class="tab-panel <?= $first ? 'is-active' : '' ?>" id="day-<?= e($dayKey) ?>">
            <h3 class="schedule-day-title"><?= e($dayLabel) ?></h3>
            <?php if ($items): ?>
              <ul class="schedule-list">
                <?php foreach ($items as $entry): ?>
                  <?php $entryBookable = !empty($entry['course_slug']); ?>
                  <?php $entryBody = '<span class="schedule-entry__time">' . e($entry['time']) . '</span>'
                      . '<span class="schedule-entry__course">' . e($entry['course_name']) . '</span>'
                      . ($entry['trainer'] ? '<span class="schedule-entry__meta">– ' . e($entry['trainer']) . '</span>' : '')
                      . ($entryBookable ? '<span class="schedule-entry__cta">Termine ansehen →</span>' : ''); ?>
                  <li>
                    <?php if ($entryBookable): ?>
                      <button type="button" class="schedule-entry schedule-entry--bookable schedule-entry--<?= e(category_slug($entry['category'])) ?>" onclick="lmOpenModal('<?= e($entry['course_slug']) ?>')"><?= $entryBody ?></button>
                    <?php else: ?>
                      <div class="schedule-entry schedule-entry--<?= e(category_slug($entry['category'])) ?>"><?= $entryBody ?></div>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="schedule-empty">Keine Kurse an diesem Tag.</p>
            <?php endif; ?>
          </div>
        <?php $first = false; endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Slide 5: Trainer (ohne Fotos) -->
  <section class="panel" id="trainer">
    <div class="panel__inner">
      <h2 class="panel__title">Deine <span class="grad-text">Trainer</span></h2>
      <?php if (get_content('trainer_intro')): ?><p class="panel__intro-text"><?= e(get_content('trainer_intro')) ?></p><?php endif; ?>
      <div class="trainer-grid">
        <?php foreach ($trainers as $i => $trainer): ?>
          <button type="button" class="trainer-card" onclick="trOpenModal(<?= (int) $i ?>)">
            <div class="trainer-card__avatar"><?= e(mb_substr($trainer['name'], 0, 1)) ?></div>
            <div class="trainer-card__name"><?= e($trainer['name']) ?></div>
          </button>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Slide 6: Preise -->
  <section class="panel" id="preise">
    <div class="panel__inner">
      <h2 class="panel__title">Unsere <span class="grad-text">Preise</span></h2>
      <p class="panel__intro-text"><?= e(get_content('pricing_intro')) ?></p>

      <div class="price-grid">
        <?php foreach (get_pricing_plans() as $plan): ?>
          <div class="price-card">
            <div class="price-card__name"><?= e($plan['name']) ?></div>
            <?php if ($plan['tagline'] !== ''): ?><div class="price-card__tagline"><?= e($plan['tagline']) ?></div><?php endif; ?>
            <div class="price-card__price"><?= number_format((float) $plan['price'], 0) ?>&nbsp;€ <span class="price-card__unit"><?= e($plan['price_unit']) ?></span></div>
            <?php if ($plan['included_text'] !== ''): ?><div class="price-card__list"><?= e($plan['included_text']) ?></div><?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>

      <p class="price-footnote"><?= e(get_content('pricing_footnote')) ?></p>
    </div>
  </section>

  <!-- Slide 7: Closing CTA + Footer -->
  <section class="panel panel--closing">
    <div class="panel__inner">
      <h2 class="panel__title">Bereit?</h2>
      <p>Kostenlose Schnupperstunde, unverbindlich – such dir oben deinen Kurs aus.</p>
      <a href="#kurse" class="button-primary">Zu den Kursen</a>

      <footer class="site-footer">
        <div class="site-footer__inner">
          <div>
            <?= e(get_content('footer_offer_text')) ?><br>
            <?= e(get_content('contact_name')) ?> · <?= e(get_content('contact_address')) ?><br>
            <a href="tel:<?= e(preg_replace('/\s+/', '', get_content('contact_phone'))) ?>"><?= e(get_content('contact_phone')) ?></a> ·
            <a href="mailto:<?= e(get_content('contact_email')) ?>"><?= e(get_content('contact_email')) ?></a>
          </div>
          <div class="site-footer__links">
            <a href="impressum.php">Impressum</a>
            <a href="datenschutz.php">Datenschutz</a>
          </div>
        </div>
      </footer>
    </div>
  </section>

</div>

</div>

</main>

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

<div class="lm-modal-bg" id="tr-modal" role="dialog" aria-modal="true" aria-labelledby="tr-modal-title">
  <div class="lm-modal">
    <div class="lm-modal__head">
      <div class="lm-modal__title" id="tr-modal-title">Trainer</div>
      <button type="button" class="lm-modal__close" onclick="trCloseModal()" aria-label="Schließen">✕</button>
    </div>
    <div class="tr-modal__body">
      <img class="tr-modal__photo" id="tr-modal-photo" alt="" hidden>
      <div class="trainer-card__avatar tr-modal__avatar" id="tr-modal-avatar"></div>
      <div class="tr-modal__name" id="tr-modal-name"></div>
      <p class="tr-modal__bio" id="tr-modal-bio"></p>
    </div>
  </div>
</div>

<script>
var lmKurse = <?= json_encode(array_column($bookable, null, 'slug'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
var lmTrainers = <?= json_encode(array_map(function (array $t): array {
    return [
        'name' => $t['name'],
        'initial' => mb_substr($t['name'], 0, 1),
        'bio' => $t['bio'] !== '' ? $t['bio'] : 'Bio-Text folgt in Kürze.',
        'photo' => $t['photo_path'] !== '' ? $t['photo_path'] : null,
    ];
}, $trainers), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

function trOpenModal(i) {
  var t = lmTrainers[i];
  if (!t) return;
  document.getElementById('tr-modal-title').textContent = t.name;
  document.getElementById('tr-modal-name').textContent = t.name;
  document.getElementById('tr-modal-bio').textContent = t.bio;
  var photoEl = document.getElementById('tr-modal-photo');
  var avatarEl = document.getElementById('tr-modal-avatar');
  if (t.photo) {
    photoEl.src = t.photo;
    photoEl.alt = t.name;
    photoEl.hidden = false;
    avatarEl.hidden = true;
  } else {
    photoEl.hidden = true;
    photoEl.src = '';
    avatarEl.hidden = false;
    avatarEl.textContent = t.initial;
  }
  document.getElementById('tr-modal').classList.add('is-open');
  document.body.style.overflow = 'hidden';
}

function trCloseModal() {
  document.getElementById('tr-modal').classList.remove('is-open');
  document.body.style.overflow = '';
}

document.getElementById('tr-modal').addEventListener('click', function (e) {
  if (e.target === this) trCloseModal();
});
var lmBaseUrl = 'https://tanzcenter-payer.nimbuscloud.at/index.php?c=PublicCustomers&site=99&what=courses&level=';

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
  if (e.key === 'Escape') { lmCloseModal(); trCloseModal(); }
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

// Tabs (pro Gruppe unabhängig: Kurse-Kategorien, Stundenplan-Tage)
document.querySelectorAll('.tab-group').forEach(function (group) {
  group.querySelectorAll('.tab-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      group.querySelectorAll('.tab-btn').forEach(function (b) { b.setAttribute('aria-selected', 'false'); });
      group.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.remove('is-active'); });
      btn.setAttribute('aria-selected', 'true');
      document.getElementById(btn.dataset.tabTarget).classList.add('is-active');
    });
  });
});

// Horizontales Story-Swipe: Slides, Fortschrittsbalken, Prev/Next
var stories = document.getElementById('stories');
var slides = Array.prototype.slice.call(stories.children);
var progress = document.getElementById('story-progress');
var prevBtn = document.getElementById('story-prev');
var nextBtn = document.getElementById('story-next');

slides.forEach(function (slide, i) {
  var seg = document.createElement('button');
  seg.type = 'button';
  seg.className = 'story-progress__seg';
  seg.setAttribute('role', 'tab');
  seg.setAttribute('aria-label', 'Seite ' + (i + 1) + ' von ' + slides.length);
  seg.addEventListener('click', function () {
    slide.scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' });
  });
  progress.appendChild(seg);
});

var segs = Array.prototype.slice.call(progress.children);
var activeIndex = 0;

function setActiveIndex(index) {
  activeIndex = index;
  segs.forEach(function (seg, i) {
    seg.classList.toggle('is-active', i === index);
    seg.classList.toggle('is-done', i < index);
  });
  prevBtn.hidden = index === 0;
  nextBtn.hidden = index === slides.length - 1;
}

function goToSlide(index) {
  index = Math.max(0, Math.min(slides.length - 1, index));
  slides[index].scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' });
}

prevBtn.addEventListener('click', function () { goToSlide(activeIndex - 1); });
nextBtn.addEventListener('click', function () { goToSlide(activeIndex + 1); });

document.addEventListener('keydown', function (e) {
  if (document.getElementById('lm-modal').classList.contains('is-open')) return;
  if (document.getElementById('tr-modal').classList.contains('is-open')) return;
  if (e.key === 'ArrowRight') goToSlide(activeIndex + 1);
  if (e.key === 'ArrowLeft') goToSlide(activeIndex - 1);
});

var slideObserver = new IntersectionObserver(function (entries) {
  entries.forEach(function (entry) {
    if (entry.isIntersecting && entry.intersectionRatio > 0.6) {
      setActiveIndex(slides.indexOf(entry.target));
    }
  });
}, { root: stories, threshold: [0.6] });

slides.forEach(function (slide) { slideObserver.observe(slide); });
setActiveIndex(0);

// Anker-Links (Nav, CTA-Buttons): auf dem Handy horizontal zum Slide,
// auf dem Desktop (klassischer One-Pager) normal vertikal zur Sektion
document.querySelectorAll('a[href^="#"]').forEach(function (link) {
  link.addEventListener('click', function (e) {
    var target = document.getElementById(link.getAttribute('href').slice(1));
    if (!target) return;
    e.preventDefault();
    target.scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'start' });
  });
});
</script>

</body>
</html>
