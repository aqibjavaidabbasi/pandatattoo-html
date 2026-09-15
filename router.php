<?php
/**
 * Dev router for PHP's built-in server. Mirrors .htaccess so local behaviour matches Apache.
 *
 *   php -S 127.0.0.1:8123 router.php
 *
 * Without it the built-in server falls back to the document root's index.php for any
 * directory-style URI, so every pretty URL (/gear/, /gallery/) silently serves the home page
 * and clicking the nav looks like every page is identical. Not used in production — SiteGround
 * runs Apache and reads .htaccess.
 */
$path = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/', '/');
if ($path === '') $path = '/';

// Never serve internals (matches the .htaccess deny rules)
if (preg_match('#^/(inc|data|reference|tests)(/|$)#', $path) || str_starts_with(basename($path), '.env')) {
    http_response_code(403);
    exit('Forbidden');
}

// Compatibility routes, in .htaccess order
$routes = [
    '#^/wp-admin/admin-ajax\.php$#'                                  => '/api/admin-ajax.php',
    '#^/wp-json/contact-form-7/v1/contact-forms/[0-9]+/feedback$#'   => '/api/nominate.php',
    '#^/wp-json/custom/v1/booking-submit$#'                          => '/api/booking-submit.php',
];
foreach ($routes as $re => $target) {
    if (preg_match($re, $path)) { require __DIR__ . $target; return true; }
}

if ($path === '/give') { header('Location: /giveaway/', true, 301); exit; }
if ($path === '/')     { require __DIR__ . '/index.php'; return true; }

// Real files (assets) are served as-is
if ($path !== '/' && is_file(__DIR__ . $path)) return false;

// /foo/ -> foo.php
if (preg_match('#^/([a-z0-9-]+)$#', $path, $m) && is_file(__DIR__ . "/{$m[1]}.php")) {
    require __DIR__ . "/{$m[1]}.php";
    return true;
}

http_response_code(404);
require __DIR__ . '/404.php';
return true;
