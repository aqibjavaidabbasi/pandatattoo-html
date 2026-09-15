<?php
// A blank .env line must not mask a host-set environment variable.
putenv('PANDA_TEST_KEY=from-environment');
$_ENV['PANDA_TEST_BLANK'] = '';
require __DIR__ . '/../inc/config.php';
$fail = 0;
function ok($c,$l){ global $fail; if(!$c){$fail++; echo "FAIL: $l\n";} }
ok(env('PANDA_TEST_KEY') === 'from-environment', 'getenv() is consulted');
ok(env('PANDA_TEST_BLANK', 'fallback') === 'fallback', 'blank value falls through to default');
ok(env('PANDA_TEST_ABSENT', 'd') === 'd', 'absent key uses default');

// A real environment variable must beat a value loaded from .env, so a host or a test run
// can override a deployed file without editing it.
$_ENV['PANDA_TEST_PRECEDENCE'] = 'from-dotenv';
putenv('PANDA_TEST_PRECEDENCE=from-environment');
ok(env('PANDA_TEST_PRECEDENCE') === 'from-environment', 'real env var overrides .env value');
echo $fail === 0 ? "config.php: all checks passed\n" : "config.php: $fail FAILED\n";
exit($fail === 0 ? 0 : 1);
