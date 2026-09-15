<?php
/**
 * admin-ajax dispatcher.
 *
 * The Tattoo Fight page's JS posts to admin-ajax.php with an `action` parameter, exactly as
 * it did under WordPress. Keeping this shape means the page's JavaScript is carried over
 * byte-for-byte instead of being rewritten. .htaccess maps the old
 * /wp-admin/admin-ajax.php path here.
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/sanitize.php';

$action = wp_sanitize_key($_REQUEST['action'] ?? '');

switch ($action) {
    case 'send_otp_vote':   require __DIR__ . '/send-otp.php';     break;
    case 'verify_otp_vote': require __DIR__ . '/verify-otp.php';   break;
    case 'test_votes_field': require __DIR__ . '/test-votes-field.php'; break;
    default:
        http_response_code(400);
        echo '0';   // what WordPress returns for an unknown action
}
