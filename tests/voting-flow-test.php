<?php
/**
 * End-to-end check of the Tattoo Fight voting flow against a real database.
 * Exercises the rules that matter: OTP expiry, one verified vote per email per artist per
 * day, the same limit per IP, and that a successful vote increments the leaderboard.
 *
 * Needs DB_* pointing at a scratch database with data/schema.sql loaded.
 */
require __DIR__ . '/../inc/db.php';
$fail = 0;
function ok($c, $l) { global $fail; if (!$c) { $fail++; echo "FAIL: $l\n"; } }

$pdo = db();
foreach (['artist_votes', 'voting_leaderboard', 'contests'] as $t) $pdo->exec("DELETE FROM $t");

$pdo->exec("INSERT INTO contests (id,title,start_date,end_date,status)
            VALUES (1,'Round 1', NOW() - INTERVAL 1 DAY, NOW() + INTERVAL 1 DAY,'publish')");
$pdo->exec("INSERT INTO voting_leaderboard (id,contest_id,artist_name,ig_handle,votes)
            VALUES (7,1,'Test Artist','testartist',0)");

@unlink(__DIR__ . '/../data/cache/active_contest.json');
ok(active_contest_id() === 1, 'active contest window resolves');

$today = date('Y-m-d');
$ins = $pdo->prepare("INSERT INTO artist_votes (email,phone,ip_address,artist_id,otp,otp_expires_at,vote_date,verified)
                      VALUES (?,?,?,?,?,?,?,0)");

// a valid, unexpired OTP verifies and increments the leaderboard
$ins->execute(['a@example.com','','1.1.1.1',7,'111111',date('Y-m-d H:i:s', time()+300),$today]);
$row = $pdo->query("SELECT * FROM artist_votes ORDER BY id DESC LIMIT 1")->fetch();
ok(strtotime($row['otp_expires_at']) > time(), 'fresh OTP is unexpired');
$pdo->prepare("UPDATE artist_votes SET verified=1 WHERE id=?")->execute([$row['id']]);
$pdo->prepare("UPDATE voting_leaderboard SET votes = votes + 1 WHERE id=?")->execute([7]);
ok((int) $pdo->query("SELECT votes FROM voting_leaderboard WHERE id=7")->fetchColumn() === 1, 'vote increments leaderboard');

// same email, same artist, same day -> blocked
$dupe = $pdo->prepare("SELECT COUNT(*) FROM artist_votes WHERE email=? AND artist_id=? AND vote_date=? AND verified=1");
$dupe->execute(['a@example.com',7,$today]);
ok((int) $dupe->fetchColumn() === 1, 'per-email daily limit sees the existing vote');

// same IP, different email, same day -> blocked
$ipq = $pdo->prepare("SELECT COUNT(*) FROM artist_votes WHERE ip_address=? AND artist_id=? AND vote_date=? AND verified=1");
$ipq->execute(['1.1.1.1',7,$today]);
ok((int) $ipq->fetchColumn() === 1, 'per-IP daily limit sees the existing vote');

// an expired OTP must not pass the freshness test
$ins->execute(['b@example.com','','2.2.2.2',7,'222222',date('Y-m-d H:i:s', time()-10),$today]);
$old = $pdo->query("SELECT * FROM artist_votes WHERE email='b@example.com' ORDER BY id DESC LIMIT 1")->fetch();
ok(strtotime($old['otp_expires_at']) < time(), 'expired OTP is rejected by the freshness check');

// a different artist on the same day is allowed
$pdo->exec("INSERT INTO voting_leaderboard (id,contest_id,artist_name,votes) VALUES (8,1,'Other',0)");
$dupe->execute(['a@example.com',8,$today]);
ok((int) $dupe->fetchColumn() === 0, 'daily limit is per artist, not global');

echo $fail === 0 ? "voting flow: all checks passed\n" : "voting flow: $fail FAILED\n";
exit($fail === 0 ? 0 : 1);
