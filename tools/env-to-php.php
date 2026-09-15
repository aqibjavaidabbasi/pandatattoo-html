<?php
/**
 * Convert .env to .env.php, which a misconfigured host cannot serve as readable text.
 *
 *   php tools/env-to-php.php          # writes .env.php next to .env
 *
 * Prints nothing but a count — the values never pass through the terminal. Delete .env
 * once the site is confirmed working; inc/config.php prefers .env.php when both exist.
 */
declare(strict_types=1);

$src = __DIR__ . '/../.env';
$dst = __DIR__ . '/../.env.php';
if (!is_readable($src)) { fwrite(STDERR, "No .env found at $src\n"); exit(1); }
if (file_exists($dst))  { fwrite(STDERR, "$dst already exists — delete it first.\n"); exit(1); }

$vars = [];
foreach (file($src, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) continue;
    [$k, $v] = explode('=', $line, 2);
    $vars[trim($k)] = trim($v);
}
$out = "<?php\n// Generated from .env by tools/env-to-php.php. Never commit this file.\nreturn [\n";
foreach ($vars as $k => $v) {
    $out .= sprintf("    %s => %s,\n", var_export($k, true), var_export($v, true));
}
$out .= "];\n";

file_put_contents($dst, $out);
chmod($dst, 0640);
echo count($vars) . " key(s) written to .env.php\nDelete .env once the site is verified.\n";
