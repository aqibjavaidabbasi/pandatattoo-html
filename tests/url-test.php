<?php
// base_path() is what makes a subfolder deploy work without config, and current_path()
// has to strip it back off or the nav loses its active state. Both are easy to get
// backwards, so they get checked here — once at the document root, once a level below it.
//
//   php tests/url-test.php
//
// base_path()/base_url() memoise, so the subfolder case is a re-exec of this same file.
$app       = realpath(__DIR__ . '/..');
$subfolder = ($argv[1] ?? '') === 'subfolder';
$_SERVER['DOCUMENT_ROOT'] = $subfolder ? dirname($app) : $app;

require __DIR__ . '/../inc/config.php';
unset($_ENV['SITE_URL']);          // after the require — config.php loads the real .env
putenv('SITE_URL=');               // blank => derive the base from the request

$fail = 0;
function ok($c, $l) { global $fail; if (!$c) { $fail++; echo "FAIL: $l\n"; } }

$_SERVER['HTTP_HOST'] = '127.0.0.1:8123';
$base = 'http://127.0.0.1:8123' . ($subfolder ? '/' . basename($app) : '');
$pre  = $subfolder ? '/' . basename($app) : '';

ok(base_path() === $pre, "base_path is '$pre'");
ok(base_url() === $base, 'base_url derives scheme + host + base path');
ok(url('/gallery/') === "$base/gallery/", 'url() is fully qualified');
ok(url('gallery/') === "$base/gallery/", 'url() tolerates a missing leading slash');
ok(str_starts_with(asset('/assets/css/blocks.css'), "$base/assets/css/blocks.css?v="),
   'asset() is absolute and keeps its cache-buster');
ok(asset('/assets/nope.css') === "$base/assets/nope.css",
   'asset() drops the cache-buster for a file that is not there');

// current_path() strips the base back off, so the nav still matches its path keys.
foreach ([["$pre/gallery/", '/gallery/'], ["$pre/gear.php", '/gear/'],
          ["$pre/index.php", '/'],        [$pre === '' ? '/' : $pre, '/']] as [$uri, $want]) {
    $_SERVER['REQUEST_URI'] = $uri;
    ok(current_path() === $want, "current_path('$uri') === '$want'");
}

// route() picks the URL form the host can actually serve. Pretty URLs need a rewrite;
// inside another site's document root that rewrite may never run, so the .php form is the
// default and must carry the base path and any #fragment intact.
putenv('PRETTY_URLS=0');
ok(route('/gallery/')  === "$base/gallery.php",  'route() emits .php when pretty URLs are off');
ok(route('/gallery')   === "$base/gallery.php",  'route() tolerates no trailing slash');
ok(route('/')          === "$base/",             'home stays / — DirectoryIndex needs no rewrite');
ok(route('/gallery/#dani-luz') === "$base/gallery.php#dani-luz", 'fragment survives');
ok(route('/terms-and-conditions/') === "$base/terms-and-conditions.php", 'hyphenated route');

putenv('PRETTY_URLS=1');
ok(route('/gallery/')  === "$base/gallery/",     'route() emits the pretty form when enabled');
ok(route('/gallery/#dani-luz') === "$base/gallery/#dani-luz", 'fragment survives either way');
putenv('PRETTY_URLS=0');

// Whichever form is emitted, current_path() must map it back to the same canonical key or
// the nav loses its active state.
foreach (["$pre/gallery.php" => '/gallery/', "$pre/gallery/" => '/gallery/'] as $uri => $want) {
    $_SERVER['REQUEST_URI'] = $uri;
    ok(current_path() === $want, "current_path('$uri') === '$want'");
}

$where = $subfolder ? 'in a subfolder' : 'at the document root';
echo $fail === 0 ? "url helpers $where: all checks passed\n" : "url helpers $where: $fail FAILED\n";

if (!$subfolder && $fail === 0) {
    passthru(PHP_BINARY . ' ' . escapeshellarg(__FILE__) . ' subfolder', $code);
    exit($code);
}
exit($fail === 0 ? 0 : 1);
