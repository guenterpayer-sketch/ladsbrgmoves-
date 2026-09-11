<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** @return array<string,string> */
function get_all_content(): array {
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $rows = get_db()->query('SELECT content_key, content_value FROM content_blocks')->fetchAll();
    $cache = [];
    foreach ($rows as $row) {
        $cache[$row['content_key']] = $row['content_value'];
    }
    return $cache;
}

function get_content(string $key, string $default = ''): string {
    $all = get_all_content();
    return $all[$key] ?? $default;
}

function set_content(string $key, string $value): void {
    $stmt = get_db()->prepare(
        'UPDATE content_blocks SET content_value = :value WHERE content_key = :key'
    );
    $stmt->execute(['value' => $value, 'key' => $key]);
}

/** @return array<string,array<int,array<string,mixed>>> Kurse gruppiert nach Kategorie */
function get_courses_grouped(): array {
    $rows = get_db()->query(
        'SELECT * FROM courses ORDER BY FIELD(category, "Kinder","Jugendliche","Erwachsene"), sort_order, name'
    )->fetchAll();
    $grouped = ['Kinder' => [], 'Jugendliche' => [], 'Erwachsene' => []];
    foreach ($rows as $row) {
        $grouped[$row['category']][] = $row;
    }
    return $grouped;
}

/**
 * @return array<string,array<int,array<string,mixed>>> Stundenplan gruppiert nach Wochentag.
 * Jeder Eintrag bekommt zusätzlich "course_slug", sofern sein course_name exakt
 * einem buchbaren Kurs (Kurse-Verwaltung, mit Anmelde-Schlüssel + NimbusCloud-ID)
 * entspricht – darüber wird die Kachel im Stundenplan klickbar/buchbar.
 */
function get_schedule_grouped(): array {
    $order = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
    $rows = get_db()->query(
        'SELECT s.*, c.slug AS course_slug
         FROM schedule s
         LEFT JOIN courses c ON c.name = s.course_name AND c.slug IS NOT NULL AND c.nimbus_online_id != \'\'
         ORDER BY FIELD(s.weekday,"Mo","Di","Mi","Do","Fr","Sa","So"), s.sort_order, s.time'
    )->fetchAll();
    $grouped = array_fill_keys($order, []);
    foreach ($rows as $row) {
        $grouped[$row['weekday']][] = $row;
    }
    return $grouped;
}

/** @return array<int,array<string,mixed>> */
function get_trainers(): array {
    return get_db()->query('SELECT * FROM trainers ORDER BY sort_order, name')->fetchAll();
}

/** @return array<int,array<string,string>> Kurse mit NimbusCloud-Anbindung, für das Buchungs-Modal */
function get_bookable_courses(): array {
    $rows = get_db()->query(
        "SELECT slug, name, nimbus_online_id FROM courses WHERE slug != '' AND nimbus_online_id != ''"
    )->fetchAll();
    return $rows;
}

function category_slug(string $category): string {
    $map = ['Kinder' => 'kinder', 'Jugendliche' => 'jugendliche', 'Erwachsene' => 'erwachsene', 'Gemischt' => 'gemischt'];
    return $map[$category] ?? 'jugendliche';
}

function csrf_token(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check(): bool {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $sent = $_POST['csrf_token'] ?? '';
    return is_string($sent) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $sent);
}
