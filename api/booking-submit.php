<?php
/**
 * Booking form endpoint.
 *
 * Port of handle_booking_submission() (WP REST route custom/v1/booking-submit).
 * Same three stages, same order, same Contentful calls, same response shapes:
 *   1. optional image -> upload binary -> create asset -> process -> poll -> publish
 *   2. build the `appointments` entry payload
 *   3. create the entry, then publish it
 *
 * The CMA token lives here and never reaches the browser. The browser sends an artist
 * *slug*; the entry ID is resolved server-side from cf_artist_map().
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/contentful.php';
require_once __DIR__ . '/../inc/sanitize.php';

header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$space_id       = env('CONTENTFUL_SPACE_ID');
$cma_token      = env('CONTENTFUL_CMA_TOKEN');
$environment_id = env('CONTENTFUL_ENVIRONMENT', 'master');

if ($space_id === '' || $cma_token === '') {
    error_log('Contentful CMA credentials not configured; booking submission cannot proceed.');
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Booking is temporarily unavailable.']);
    exit;
}

/** One CMA request. Returns [http_code, decoded_body, raw_body]. */
function cma(string $method, string $url, array $headers, $body = null, int $timeout = 15): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_HTTPHEADER     => $headers,
    ]);
    if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    $raw  = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    $raw = $raw === false ? '' : (string) $raw;
    return [$code, json_decode($raw), $raw];
}

$auth = 'Authorization: Bearer ' . $cma_token;
$params = $_POST;
$asset_id = null;

// ---------- 1. IMAGE UPLOAD HANDLING ----------
if (!empty($_FILES['tattooImage']) && $_FILES['tattooImage']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['tattooImage'];

    // A. Upload binary
    [$code, $up] = cma('POST', "https://upload.contentful.com/spaces/$space_id/uploads",
        [$auth, 'Content-Type: application/octet-stream'],
        file_get_contents($file['tmp_name']));

    if ($code === 201 && isset($up->sys->id)) {
        $upload_id = $up->sys->id;

        // B. Create localized Asset pointing to the Upload
        $asset_body = ['fields' => [
            'title' => ['en-US' => 'Tattoo Concept - ' . wp_sanitize_text_field($params['fullName'] ?? '')],
            'file'  => ['en-US' => [
                'contentType' => $file['type'],
                'fileName'    => wp_sanitize_file_name($file['name']),
                'uploadFrom'  => ['sys' => ['type' => 'Link', 'linkType' => 'Upload', 'id' => $upload_id]],
            ]],
        ]];

        [$code, $asset] = cma('POST',
            "https://api.contentful.com/spaces/$space_id/environments/$environment_id/assets",
            [$auth, 'Content-Type: application/vnd.contentful.management.v1+json'],
            json_encode($asset_body));

        if ($code === 201 && isset($asset->sys->id)) {
            $asset_id      = $asset->sys->id;
            $asset_version = $asset->sys->version;

            // C. Process the asset for Contentful Delivery
            cma('PUT', "https://api.contentful.com/spaces/$space_id/environments/$environment_id/assets/$asset_id/files/en-US/process",
                [$auth, 'X-Contentful-Version: ' . $asset_version]);

            // D. Poll until processing finishes, then publish.
            // Processing is async: Contentful swaps `uploadFrom` for a real `url` and
            // bumps the version. Same budget as the original — 6 tries, 500 ms apart.
            $processed_version = null;
            for ($attempt = 0; $attempt < 6; $attempt++) {
                usleep(500 * 1000);
                [$c, $check] = cma('GET',
                    "https://api.contentful.com/spaces/$space_id/environments/$environment_id/assets/$asset_id",
                    [$auth], null, 10);
                if ($c === 200) {
                    $fd = $check->fields->file->{'en-US'} ?? null;
                    if ($fd && !empty($fd->url) && empty($fd->uploadFrom)) {
                        $processed_version = $check->sys->version;
                        break;
                    }
                }
            }

            // If polling timed out we still try with the last known version. Worst case is
            // a 409, swallowed as before: the entry is still created and the photo can be
            // published by hand.
            $publish_version = $processed_version ?? ($asset_version + 1);
            cma('PUT', "https://api.contentful.com/spaces/$space_id/environments/$environment_id/assets/$asset_id/published",
                [$auth, 'X-Contentful-Version: ' . $publish_version]);
        }
    }
}

// ---------- 2. ENTRY PAYLOAD CONSTRUCTION ----------
$fields = [];

$field_map = [
    'fullName'          => 'fullName',
    'email'             => 'email',
    'phoneNumber'       => 'phoneNumber',
    'age'               => 'ageType',
    'gender'            => 'gender',
    'size'              => 'size',
    'color'             => 'colorType',
    'tattooDescription' => 'tattooDescription',
    'location'          => 'miamiStatus',
    'scheduleType'      => 'scheduleType',
    'desiredTiming'     => 'desiredTiming',
    'bodyPositionImage' => 'bodyPosition',
    'instagram'         => 'instagram',
];
foreach ($field_map as $post_key => $cf_key) {
    if (!empty($params[$post_key])) {
        $fields[$cf_key] = ['en-US' => wp_sanitize_text_field($params[$post_key])];
    }
}

// Artist reference — validated server-side; the browser never submits a Contentful ID.
$artist_slug = wp_sanitize_key($params['artistSlug'] ?? '');
$artist_map  = cf_artist_map();

if ($artist_slug === '') {
    http_response_code(400);
    echo json_encode(['code' => 'missing_artist', 'message' => 'Artist selection is required.']);
    exit;
}
if ($artist_slug === 'no-preference') {
    $artist_entry_id = null;              // explicit no-preference: omit the field entirely
} elseif (array_key_exists($artist_slug, $artist_map)) {
    $artist_entry_id = $artist_map[$artist_slug];
} else {
    http_response_code(400);              // tampered slug or direct POST
    echo json_encode(['code' => 'invalid_artist', 'message' => 'Invalid artist selection.']);
    exit;
}

if ($artist_entry_id) {
    $fields['artistName'] = ['en-US' => ['sys' => [
        'type' => 'Link', 'linkType' => 'Entry', 'id' => $artist_entry_id,
    ]]];
}

if (!empty($params['styles']) && is_array($params['styles'])) {
    $fields['selectedTattooStyles'] = ['en-US' => implode(', ', array_map('wp_sanitize_text_field', $params['styles']))];
}
if (!empty($params['artist_addons']) && is_array($params['artist_addons'])) {
    $fields['artistAddons'] = ['en-US' => implode(', ', array_map('wp_sanitize_text_field', $params['artist_addons']))];
}
// `somethingDifferent` is a Boolean in the appointments content type. Sending the label
// string instead made Contentful reject the whole entry, so ticking "Something different
// (if none above apply)" hard-failed every submission that used it.
if (!empty($params['somethingDifferent'])) {
    $fields['somethingDifferent'] = ['en-US' => true];
}

$fields['submissionDate'] = ['en-US' => gmdate('c')];

if ($asset_id) {
    $fields['tattooImage'] = ['en-US' => ['sys' => [
        'type' => 'Link', 'linkType' => 'Asset', 'id' => $asset_id,
    ]]];
}

// ---------- 3. CREATE & PUBLISH ENTRY ----------
[$code, $entry, $raw] = cma('POST',
    "https://api.contentful.com/spaces/$space_id/environments/$environment_id/entries",
    [$auth,
     'Content-Type: application/vnd.contentful.management.v1+json',
     'X-Contentful-Content-Type: appointments'],
    json_encode(['fields' => $fields]));

if ($code >= 400 || !isset($entry->sys->id)) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to create entry. Contentful says: ' . $raw]);
    exit;
}

[$pub_code, $published, $pub_raw] = cma('PUT',
    "https://api.contentful.com/spaces/$space_id/environments/$environment_id/entries/{$entry->sys->id}/published",
    [$auth, 'X-Contentful-Version: ' . $entry->sys->version]);

// The customer's request is recorded either way — the entry exists — so they still get a
// success screen rather than a reason to submit again. A failed publish leaves it as a
// draft the studio will not see in a published-only view, so it has to be logged loudly.
if ($pub_code >= 400) {
    error_log("Contentful: entry {$entry->sys->id} created but NOT published (HTTP $pub_code): $pub_raw");
}

echo json_encode(['status' => 'success', 'entry' => $published]);
