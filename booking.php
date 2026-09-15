<?php
require __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/contentful.php';
require_once __DIR__ . '/inc/sanitize.php';

$artist_id = wp_sanitize_text_field($_GET['artistId'] ?? '');
$artist_slug = wp_sanitize_text_field($_GET['artist'] ?? '');
$artist_name = '';
$artist = null;

if ($artist_id !== '') {
    $artist = cf_artist_by_id($artist_id);
    if ($artist && !empty($artist['name'])) {
        $artist_name = $artist['name'];
    } else {
        $artist = null;
    }
}

if (!$artist && $artist_slug !== '') {
    $artist = cf_artist_by_slug($artist_slug);
    if ($artist) {
        $artist_id = $artist['id'];
        $artist_name = $artist['name'];
    }
}

$PAGE = [
    'title'      => html_entity_decode('booking &#8211; Tatto Panda', ENT_QUOTES, 'UTF-8'),
    'body_class' => 'wp-singular page-template page-template-templates page-template-booking page-template-templatesbooking-php page page-id-880 wp-custom-logo wp-embed-responsive wp-theme-studio wp-child-theme-studio-child no-sidebar excerpt-image-top',
    'css'        => 'booking.css',
];

include __DIR__ . '/inc/head.php';
include __DIR__ . '/inc/header.php';
$booking_modal_args = [
    'inline'      => true,
    'artist_id'   => $artist_id,
    'artist_name' => $artist_name,
    'artist_slug' => $artist ? ($artist['slug'] ?? '') : '',
];
?>

<div class="standalone-booking-page">
    <div class="hd-booking-hero">
        <div class="hd-booking-eyebrow">Panda Tattoo Studio</div>
        <?php if ($artist_name): ?>
            <h1 class="hd-booking-title">Book with <?= e($artist_name) ?></h1>
            <p class="hd-booking-subtitle">Fill out the form below to request an appointment</p>
        <?php else: ?>
            <h1 class="hd-booking-title">Book Your Tattoo Appointment</h1>
            <p class="hd-booking-subtitle">Fill out the form below to schedule your session</p>
        <?php endif; ?>
    </div>
    
    <!-- Inline booking form - wrapper class for standalone styling -->
    <div class="standalone-booking-wrapper">
        <?php include __DIR__ . '/inc/booking-modal.php'; ?>
	    </div><!-- #content -->
		
<?php include __DIR__ . '/inc/footer.php'; ?>