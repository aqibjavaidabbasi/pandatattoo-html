<?php
/**
 * Port of test_votes_field() — the diagnostic endpoint that reads an artist's vote count.
 * Carried over because it exists on the WordPress site, not because anything calls it.
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';

$artist_id = (int) ($_GET['artist_id'] ?? 0);
if (!$artist_id) {
    json_error(['message' => 'Artist ID is required']);
}

$stmt = db()->prepare('SELECT votes FROM voting_leaderboard WHERE id = ?');
$stmt->execute([$artist_id]);
$row = $stmt->fetch();

if ($row === false) {
    json_error(['message' => 'ACF field not found or artist ID invalid']);
}

json_success(['artist_id' => $artist_id, 'votes' => $row['votes']]);
