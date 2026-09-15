<?php
/**
 * Config, env loading and the handful of helpers that replace WordPress.
 * Loaded first by every page.
 */
declare(strict_types=1);

// --- env ---------------------------------------------------------------
// `.env.php` is preferred over `.env` because it survives a host that ignores our
// .htaccess: requested directly it is EXECUTED and returns nothing, where a plain .env is
// served as text — DB password and CMA token included. It returns ['KEY' => 'value'].
$__env_php = __DIR__ . '/../.env.php';
if (is_readable($__env_php)) {
    foreach ((array) (require $__env_php) as $k => $v) {
        if ((string) $v !== '') $_ENV[trim((string) $k)] = (string) $v;
    }
}

$__env_file = __DIR__ . '/../.env';
if (!is_readable($__env_php) && is_readable($__env_file)) {
    foreach (file($__env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $v = trim($v);
        // A blank line in .env must not mask a real environment variable set by the host
        // (Apache SetEnv, cPanel, a container). Blank means "not configured here".
        if ($v !== '') $_ENV[trim($k)] = $v;
    }
}

function env(string $key, string $default = ''): string {
    // A real environment variable wins over the .env file, which wins over the default.
    // That order lets the host (cPanel, SetEnv, a container) override a deployed .env
    // without editing files, and lets a test run override both.
    foreach ([getenv($key) ?: null, $_ENV[$key] ?? null] as $v) {
        if ($v !== null && $v !== '') return (string) $v;
    }
    return $default;
}

// --- helpers (the WordPress replacements) -------------------------------

/** esc_html / esc_attr */
function e(?string $s): string {
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** esc_url */
function eu(?string $url): string {
    return htmlspecialchars(filter_var((string) $url, FILTER_SANITIZE_URL), ENT_QUOTES, 'UTF-8');
}

/**
 * The app's path under the document root — '' at the domain root, '/panda-site' in a
 * subfolder. Derived from the directory this file lives in, so it is right wherever the
 * site is dropped, with no config to keep in sync.
 */
function base_path(): string {
    static $path = null;
    if ($path !== null) return $path;
    $root = str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
    $app  = str_replace('\\', '/', (string) realpath(__DIR__ . '/..'));
    // ponytail: '' when realpath fails or the app sits outside the docroot (CLI, symlinked
    // deploy). Set SITE_URL if that ever happens on a real host.
    return $path = ($root !== '' && $app !== '' && str_starts_with($app . '/', $root . '/'))
        ? rtrim(substr($app, strlen($root)), '/')
        : '';
}

/**
 * Origin + base path that every emitted URL is prefixed with.
 * SITE_URL wins when set and is used verbatim — it must include the subfolder if the app
 * lives in one. Left blank, the base is derived from the request and base_path().
 */
function base_url(): string {
    static $base = null;
    if ($base !== null) return $base;

    $base = rtrim(env('SITE_URL'), '/');
    if ($base !== '') return $base;

    $https = ($_SERVER['HTTPS'] ?? 'off') !== 'off'
          || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    return $base = ($https ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . base_path();
}

/** Absolute URL for a site route: url('/gallery/'). */
function url(string $path = '/'): string {
    return base_url() . '/' . ltrim($path, '/');
}

/**
 * Are pretty URLs available? They need mod_rewrite and an .htaccess the host actually
 * honours; neither is guaranteed when the site is dropped inside another site's document
 * root. Off by default, so links point straight at real files and nothing depends on a
 * rewrite. Set PRETTY_URLS=1 once /gallery/ is confirmed working on the target host.
 */
function pretty_urls(): bool {
    return env('PRETTY_URLS', '0') === '1';
}

/**
 * Absolute URL for a page route. Takes the canonical path ('/gallery/') so nav matching
 * and current_path() keep one vocabulary, and emits whichever form the host can serve.
 * Any ?query or #fragment is carried across intact.
 */
function route(string $path = '/'): string {
    $parts  = preg_split('/(?=[?#])/', '/' . ltrim($path, '/'), 2);
    $p      = $parts[0];
    $suffix = $parts[1] ?? '';
    if (!pretty_urls() && preg_match('#^/([a-z0-9-]+)/?$#', $p, $m)) {
        $p = '/' . $m[1] . '.php';
    }
    return url($p . $suffix);
}

/** Absolute asset URL with a cache-busting mtime. */
function asset(string $path): string {
    $path = '/' . ltrim($path, '/');
    $file = __DIR__ . '/..' . $path;
    return url($path) . (is_file($file) ? '?v=' . filemtime($file) : '');
}

/** Current path, normalised with a trailing slash — used for nav active state. */
function current_path(): string {
    $p = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $b = base_path();
    if ($b !== '' && str_starts_with($p, $b)) $p = substr($p, strlen($b)) ?: '/';
    if (preg_match('#^/([^/]+)\.php$#', $p, $m)) {
        $p = $m[1] === 'index' ? '/' : '/' . $m[1] . '/';
    }
    return rtrim($p, '/') . '/';
}

/** Page metadata set by each page before including head.php. */
$PAGE = ['title' => 'Tatto Panda', 'body_class' => '', 'css' => null];
