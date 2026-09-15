<?php
/**
 * Port of send_otp_vote(). Issues a 6-digit OTP valid for 5 minutes and records the
 * pending vote row. Blocks a second verified vote from the same email for the same
 * artist on the same day, before spending an email on it.
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/sanitize.php';

$email     = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_SANITIZE_EMAIL);
$phone     = wp_sanitize_text_field($_POST['phone'] ?? '');
$artist_id = (int) ($_POST['artist_id'] ?? 0);

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !$artist_id) {
    json_error(['message' => 'Invalid request']);
}

$otp     = random_int(100000, 999999);
$expires = time() + 300;                       // 5 mins
$ip      = $_SERVER['REMOTE_ADDR'] ?? '';
$today   = date('Y-m-d');

$already = db()->prepare(
    'SELECT COUNT(*) FROM artist_votes WHERE email = ? AND artist_id = ? AND vote_date = ? AND verified = 1'
);
$already->execute([$email, $artist_id, $today]);
if ((int) $already->fetchColumn() > 0) {
    json_error(['message' => 'You have already voted today']);
}

db()->prepare(
    'INSERT INTO artist_votes (email, phone, ip_address, artist_id, otp, otp_expires_at, vote_date, created_at, verified)
     VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 0)'
)->execute([$email, $phone, $ip, $artist_id, $otp, date('Y-m-d H:i:s', $expires), $today]);

// WordPress queued this through wp_schedule_single_event (+5s) because wp-cron fires on the
// next request. There is no cron here, so it is sent inline — the user-visible behaviour and
// the response message are unchanged. Mail goes through PHP mail(), which is exactly what
// wp_mail() was already doing: no SMTP plugin was installed on the WordPress site.
$sent = mail(
    $email,
    'Your Vote OTP - Tattoo Panda',
    "Your one-time password is: {$otp}\n\nThis code expires in 5 minutes.",
    'Content-Type: text/html; charset=UTF-8'
);
if (!$sent) {
    error_log("Failed to send OTP email to: {$email}");
}

json_success(['message' => 'OTP will be sent shortly']);
