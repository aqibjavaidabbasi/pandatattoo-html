<?php
/**
 * Port of verify_otp_vote(). Checks the OTP against the most recent row for this
 * email+artist, then re-checks the per-IP and per-email daily limits before counting
 * the vote. Error strings are preserved verbatim — the page surfaces them directly.
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/sanitize.php';

$email     = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_SANITIZE_EMAIL);
$artist_id = (int) ($_POST['artist_id'] ?? 0);
$user_otp  = wp_sanitize_text_field($_POST['otp'] ?? '');
$ip        = $_SERVER['REMOTE_ADDR'] ?? '';
$today     = date('Y-m-d');

$stmt = db()->prepare('SELECT * FROM artist_votes WHERE email = ? AND artist_id = ? ORDER BY id DESC LIMIT 1');
$stmt->execute([$email, $artist_id]);
$record = $stmt->fetch();

if (!$record || !hash_equals((string) $record['otp'], $user_otp) || strtotime((string) $record['otp_expires_at']) < time()) {
    json_error(['message' => 'Invalid or expired OTP']);
}

$ip_check = db()->prepare(
    'SELECT COUNT(*) FROM artist_votes WHERE ip_address = ? AND artist_id = ? AND vote_date = ? AND verified = 1'
);
$ip_check->execute([$ip, $artist_id, $today]);
if ((int) $ip_check->fetchColumn() > 0) {
    json_error(['message' => 'You have already voted from this IP today']);
}

$email_check = db()->prepare(
    'SELECT COUNT(*) FROM artist_votes WHERE email = ? AND artist_id = ? AND vote_date = ? AND verified = 1'
);
$email_check->execute([$email, $artist_id, $today]);
if ((int) $email_check->fetchColumn() > 0) {
    json_error(['message' => 'You have already voted today']);
}

db()->prepare('UPDATE artist_votes SET verified = 1 WHERE id = ?')->execute([$record['id']]);
db()->prepare('UPDATE voting_leaderboard SET votes = votes + 1 WHERE id = ?')->execute([$artist_id]);

json_success(['message' => 'Vote recorded']);
