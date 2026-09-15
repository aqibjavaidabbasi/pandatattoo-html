<?php
/**
 * Contact Form 7 "Nomination Form" handler.
 *
 * Port of store_artist_to_custom_post(): move the uploaded artist photo into uploads and
 * create a voting_leaderboard row. The response shape is CF7-like because the form markup
 * still uses the captured CF7 classes and unit tag.
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/sanitize.php';

function nomination_response(string $status, string $message, array $invalid_fields = []): never {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'contact_form_id' => 427,
        'status' => $status,
        'message' => $message,
        'posted_data_hash' => '',
        'into' => '#wpcf7-f427-o1',
        'invalid_fields' => $invalid_fields,
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

function nomination_invalid(string $field, string $message): array {
    return [[
        'field' => $field,
        'message' => $message,
        'idref' => null,
        'error_id' => 'wpcf7-f427-o1-ve-' . $field,
    ]];
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    nomination_response('validation_failed', 'Error: Submission failed.');
}

$artist_name = wp_sanitize_text_field($_POST['artist-name'] ?? '');
$ig_handle = wp_sanitize_text_field($_POST['ig-handle'] ?? '');
$why_compete = trim(strip_tags((string) ($_POST['why-compete'] ?? '')));
$why_compete = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $why_compete) ?? '';
$portfolio_link = filter_var(trim((string) ($_POST['portfolio-link'] ?? '')), FILTER_SANITIZE_URL);
$photo_url = '';

if ($artist_name === '') {
    nomination_response(
        'validation_failed',
        'Error: Artist name is required.',
        nomination_invalid('artist-name', 'Error: Artist name is required.')
    );
}

$photo = $_FILES['artist-photo'] ?? null;
if (is_array($photo) && ($photo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    if (($photo['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        $message = in_array($photo['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
            ? 'The uploaded file is too large.'
            : 'There was an error uploading the file.';
        nomination_response('validation_failed', $message, nomination_invalid('artist-photo', $message));
    }

    if ((int) ($photo['size'] ?? 0) > 5 * 1024 * 1024) {
        nomination_response(
            'validation_failed',
            'The uploaded file is too large.',
            nomination_invalid('artist-photo', 'The uploaded file is too large.')
        );
    }

    $tmp = (string) ($photo['tmp_name'] ?? '');
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? (string) finfo_file($finfo, $tmp) : '';
    if ($finfo) finfo_close($finfo);

    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    if (!isset($extensions[$mime])) {
        nomination_response(
            'validation_failed',
            'You are not allowed to upload files of this type.',
            nomination_invalid('artist-photo', 'You are not allowed to upload files of this type.')
        );
    }

    $filename = wp_sanitize_file_name($photo['name'] ?? '');
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        $filename = preg_replace('/\.[^.]+$/', '', $filename) . '.' . $extensions[$mime];
    }
    if ($filename === '' || $filename[0] === '.') {
        $filename = 'artist-photo.' . $extensions[$mime];
    }

    $month = date('Y/m');
    $destination_dir = __DIR__ . '/../assets/img/nominations/' . $month;
    if (!is_dir($destination_dir) && !mkdir($destination_dir, 0775, true)) {
        nomination_response('validation_failed', 'Photo upload failed. Please try again.', nomination_invalid('artist-photo', 'Photo upload failed. Please try again.'));
    }

    $destination_path = $destination_dir . '/' . $filename;
    if (!file_exists($destination_path) && !move_uploaded_file($tmp, $destination_path)) {
        nomination_response('validation_failed', 'Photo upload failed. Please try again.', nomination_invalid('artist-photo', 'Photo upload failed. Please try again.'));
    }
    $photo_url = url('/assets/img/nominations/' . $month . '/' . $filename);
}

try {
    db()->prepare(
        "INSERT INTO voting_leaderboard
         (artist_name, ig_handle, why_compete, portfolio_link, artist_photo, votes, `rank`, feature_in_round, status, created_at)
         VALUES (?, ?, ?, ?, ?, 0, 0, 0, 'publish', NOW())"
    )->execute([$artist_name, $ig_handle, $why_compete, $portfolio_link, $photo_url]);
} catch (Throwable $e) {
    error_log('Error inserting nomination: ' . $e->getMessage());
    nomination_response('validation_failed', 'Error: Failed to create artist post.');
}

nomination_response('mail_sent', 'Submission successful!');
