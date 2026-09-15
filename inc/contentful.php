<?php
/**
 * Contentful Delivery API reads.
 *
 * Straight port of get_contentful_artists() / get_contentful_asset_url() /
 * get_contentful_artist_by_slug() / get_contentful_artist_by_id() from the WordPress
 * child theme's functions.php. Same field fallbacks, same normalised array shape, same
 * 5-minute cache TTL — WP transients become files under data/cache/.
 *
 * Reads only. The CMA write token is never used here; see api/booking-submit.php.
 */
declare(strict_types=1);

require_once __DIR__ . '/config.php';

const CF_CACHE_TTL = 300; // 5 * MINUTE_IN_SECONDS, as in the original

function cf_config(): array {
    static $c = null;
    return $c ??= [
        'space_id'    => env('CONTENTFUL_SPACE_ID'),
        'cda_token'   => env('CONTENTFUL_CDA_TOKEN'),
        'environment' => env('CONTENTFUL_ENVIRONMENT', 'master'),
    ];
}

/** WordPress sanitize_title(): lowercase, strip accents, non-alphanumerics to dashes. */
function cf_sanitize_title(string $title): string {
    $t = trim($title);
    if (function_exists('iconv')) {
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $t);
        if ($ascii !== false) $t = $ascii;
    }
    $t = strtolower(strip_tags($t));
    $t = preg_replace('/[^a-z0-9\s\-]/', '', $t) ?? '';
    $t = preg_replace('/[\s\-]+/', '-', $t) ?? '';
    return trim($t, '-');
}

// --- cache (replaces get_transient / set_transient) ----------------------

function cf_cache_get(string $key) {
    $f = __DIR__ . '/../data/cache/' . md5($key) . '.json';
    if (!is_readable($f) || filemtime($f) + CF_CACHE_TTL < time()) return false;
    $v = json_decode((string) file_get_contents($f), true);
    return $v === null ? false : $v;
}

function cf_cache_set(string $key, $value): void {
    $dir = __DIR__ . '/../data/cache';
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
    $f = $dir . '/' . md5($key) . '.json';
    $tmp = $f . '.' . getmypid() . '.tmp';   // write-then-rename: a failed write never truncates a good cache file
    if (file_put_contents($tmp, json_encode($value)) !== false) @rename($tmp, $f);
}

/** Clears every cached Contentful response. Port of clear_contentful_artists_cache(). */
function cf_cache_clear(): void {
    foreach (glob(__DIR__ . '/../data/cache/*.json') ?: [] as $f) @unlink($f);
}

// --- HTTP ---------------------------------------------------------------

/** GET a CDA URL. Returns the decoded body, or null on any failure (as the original did). */
function cf_get(string $url): ?array {
    $cfg = cf_config();
    if ($cfg['cda_token'] === '') {
        error_log('Contentful CDA token not configured. Set CONTENTFUL_CDA_TOKEN in .env');
        return null;
    }
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $cfg['cda_token']],
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($body === false) { error_log('Contentful fetch error: ' . $err); return null; }
    if ($code !== 200)   { error_log("Contentful fetch failed with code: $code - Response: $body"); return null; }

    return json_decode((string) $body, true) ?: null;
}

// --- public API ---------------------------------------------------------

/** Port of get_contentful_asset_url(). */
function cf_asset_url(string $asset_id, array $assets): string {
    foreach ($assets as $asset) {
        if (($asset['sys']['id'] ?? null) === $asset_id) {
            $url = $asset['fields']['file']['url'] ?? '';
            if ($url === '') return '';
            return str_starts_with($url, '//') ? 'https:' . $url : $url;
        }
    }
    return '';
}

/**
 * Port of get_contentful_artists().
 * $args: ['limit' => int, 'order' => string, 'slug' => string]
 * Slug filtering happens in PHP because Contentful's model has no slug field.
 */
function cf_artists(array $args = []): array {
    $cfg = cf_config();
    if ($cfg['cda_token'] === '') return [];

    $cache_key = 'contentful_artists_' . md5(serialize($args));
    $cached = cf_cache_get($cache_key);
    if ($cached !== false) return $cached;

    $query = [
        'content_type' => 'artists',
        'limit'        => isset($args['limit']) ? (int) $args['limit'] : 100,
        'order'        => !empty($args['order']) ? $args['order'] : 'fields.artistName',
    ];
    if (!empty($args['slug'])) $query['limit'] = 100; // fetch all, filter below

    $body = cf_get(sprintf(
        'https://cdn.contentful.com/spaces/%s/environments/%s/entries?%s',
        $cfg['space_id'], $cfg['environment'], http_build_query($query)
    ));
    if (empty($body['items'])) return [];

    $includes = $body['includes']['Asset'] ?? [];
    $search   = !empty($args['slug']) ? cf_sanitize_title($args['slug']) : null;
    $artists  = [];

    foreach ($body['items'] as $item) {
        $f    = $item['fields'] ?? [];
        $name = $f['artistName'] ?? $f['Name'] ?? $f['name'] ?? '';
        $slug = cf_sanitize_title($name);

        if ($search !== null && $slug !== $search) continue;

        $profile = '';
        $photo = $f['artistPhoto'] ?? $f['Artist photo'] ?? null;
        if (!empty($photo['sys']['id'])) $profile = cf_asset_url($photo['sys']['id'], $includes);

        $portfolio = [];
        foreach (($f['portfolioImages'] ?? $f['Portfolio Images'] ?? []) as $ref) {
            $id = $ref['sys']['id'] ?? null;
            if (!$id) continue;
            $url = cf_asset_url($id, $includes);
            if ($url !== '') $portfolio[] = ['url' => $url, 'id' => $id, 'alt' => $name];
        }

        $artists[] = [
            'id'               => $item['sys']['id'],
            'name'             => $name,
            'slug'             => $slug,
            'bio'              => $f['bio'] ?? $f['Bio'] ?? '',
            'instagram_handle' => $f['instagram'] ?? $f['Instagram'] ?? '',
            'profile_picture'  => $profile,
            'portfolio_images' => $portfolio,
            'booking_link'     => $f['bookingLink'] ?? $f['Booking link'] ?? '',
            'date'             => $f['date'] ?? $f['Date'] ?? '',
            'personal_website' => $f['personalWebsite'] ?? $f['Personal Website'] ?? '',
        ];
    }

    cf_cache_set($cache_key, $artists);
    return $artists;
}

/** Port of get_contentful_artist_by_slug(). */
function cf_artist_by_slug(string $slug): ?array {
    $a = cf_artists(['slug' => $slug]);
    return $a[0] ?? null;
}

/**
 * Port of get_contentful_artist_by_id().
 * Note: like the original, this returns empty profile_picture / portfolio_images —
 * a single-entry fetch carries no `includes`, and the WP version never made the
 * follow-up asset call. Kept as-is deliberately.
 */
function cf_artist_by_id(string $entry_id): ?array {
    $cfg = cf_config();
    $cache_key = 'contentful_artist_' . $entry_id;
    $cached = cf_cache_get($cache_key);
    if ($cached !== false) return $cached;

    $body = cf_get(sprintf(
        'https://cdn.contentful.com/spaces/%s/environments/%s/entries/%s',
        $cfg['space_id'], $cfg['environment'], $entry_id
    ));
    $f = $body['fields'] ?? [];
    if (empty($f)) return null;

    $name = $f['artistName'] ?? $f['Name'] ?? $f['name'] ?? '';
    $artist = [
        'id'               => $entry_id,
        'name'             => $name,
        'slug'             => cf_sanitize_title($name),
        'bio'              => $f['Bio'] ?? $f['bio'] ?? '',
        'instagram_handle' => $f['Instagram'] ?? $f['instagram'] ?? '',
        'booking_link'     => $f['Booking link'] ?? $f['bookingLink'] ?? '',
        'date'             => $f['Date'] ?? $f['date'] ?? '',
        'personal_website' => $f['Personal Website'] ?? $f['personalWebsite'] ?? '',
        'profile_picture'  => '',
        'portfolio_images' => [],
    ];

    cf_cache_set($cache_key, $artist);
    return $artist;
}

/**
 * Authoritative artist slug -> Contentful entry ID map.
 * Port of get_artist_contentful_map(). The browser submits a slug; the server resolves
 * the ID. Never trust a browser-submitted Contentful ID.
 */
function cf_artist_map(): array {
    return [
        'ashley'   => '4HTZPxDL5FIlZPIWQYOtDA',
        'alex'     => '4cLACT6oSvUnR7fBdr3EUI',
        'panda'    => '1UCE5riOhbyXo9TSf7K9vH',
        'onyx'     => '6L3zJOoVqFSJfOoJYsmuuQ',
        'ilay'     => '5fbaDqOJYbSBydlzOOThFV',
        'edwin'    => '5NHcTTLn7bYS4v4yFWIEtf',
        'dani-luz' => '1zLMKzw2gImumAgh3oZCUh',
        'sophie'   => '1rOSDfszUKQklCNUj6UZ5E',
        'nicole'   => '1Pk0S4ngOCop0Og3XikaQH',
        'isabela'  => '7miXQaRZAR4uIFyMsX0eJ',
    ];
}
