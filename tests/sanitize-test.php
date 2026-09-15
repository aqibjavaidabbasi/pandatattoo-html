<?php
require __DIR__ . '/../inc/sanitize.php';
$fail = 0;
function ok($c, $l) { global $fail; if (!$c) { $fail++; echo "FAIL: $l\n"; } }

ok(wp_sanitize_text_field('  hello  world ') === 'hello world', 'collapses whitespace');
ok(wp_sanitize_text_field("line1\nline2") === 'line1 line2', 'newlines become spaces');
ok(wp_sanitize_text_field('<script>alert(1)</script>x') === 'alert(1)x', 'strips tags');
ok(wp_sanitize_text_field(null) === '', 'null is safe');

// artistSlug goes through sanitize_key before the map lookup — tampering must not resolve
ok(wp_sanitize_key('Dani-Luz') === 'dani-luz', 'lowercases');
ok(wp_sanitize_key('no-preference') === 'no-preference', 'keeps dashes');
ok(wp_sanitize_key('../../etc/passwd') === 'etcpasswd', 'strips path traversal');
ok(wp_sanitize_key("panda' OR 1=1") === 'pandaor11', 'strips quotes and spaces');

ok(wp_sanitize_file_name('my photo.jpg') === 'my-photo.jpg', 'spaces to dashes');
ok(wp_sanitize_file_name('../../evil.php') === 'evil.php', 'strips traversal');
ok(wp_sanitize_file_name('a<b>c*d.png') === 'abcd.png', 'strips unsafe chars');

echo $fail === 0 ? "sanitize.php: all checks passed\n" : "sanitize.php: $fail FAILED\n";
exit($fail === 0 ? 0 : 1);
