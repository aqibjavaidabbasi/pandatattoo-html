<?php
/**
 * MySQL access for the Tattoo Fight voting feature.
 *
 * WordPress kept this data in a custom table (`{prefix}artist_votes`) plus two custom post
 * types (`contests`, `voting-leaderboard`) with ACF meta. Those become three real tables —
 * see data/schema.sql. Same columns, same semantics.
 */
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', env('DB_HOST', 'localhost'), env('DB_NAME'));
    $pdo = new PDO($dsn, env('DB_USER'), env('DB_PASS'), [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

/** wp_send_json_success() — vote.js reads response.success and response.data.message. */
function json_success(array $data = []): never {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

/** wp_send_json_error(). */
function json_error(array $data = []): never {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'data' => $data]);
    exit;
}

/** The active contest: the one whose start/end window contains now. Cached 5 min, as in WP. */
function active_contest_id(): int {
    $cache = __DIR__ . '/../data/cache/active_contest.json';
    if (is_readable($cache) && filemtime($cache) + 300 > time()) {
        return (int) file_get_contents($cache);
    }
    // WordPress used $wpdb, which returns null on a failed query rather than throwing, so an
    // unreachable database rendered the page's empty state. PDO throws, so catch it and do the
    // same: no contest, empty state, error in the log — never a 500 on a content page.
    try {
        $row = db()->query(
            "SELECT id FROM contests
              WHERE start_date <= NOW() AND end_date >= NOW() AND status = 'publish'
              ORDER BY end_date ASC LIMIT 1"
        )->fetch();
    } catch (PDOException $e) {
        error_log('active_contest_id: database unavailable - ' . $e->getMessage());
        return 0;
    }
    $id = (int) ($row['id'] ?? 0);
    if (!is_dir(dirname($cache))) @mkdir(dirname($cache), 0775, true);
    @file_put_contents($cache, (string) $id);
    return $id;
}
