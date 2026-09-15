<?php
/**
 * <head> — mirrors what staging emits, minus the WordPress leakage
 * (feeds, oembed, wp-json, xmlrpc, shortlink).
 *
 * Stylesheet ORDER IS LOAD-BEARING — it is the cascade the whole site depends on.
 * Do not reorder without diffing against reference/html/.
 */
require_once __DIR__ . '/config.php';
?><!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<title><?= e($PAGE['title'] ?? 'Tatto Panda') ?></title>
<meta name="robots" content="max-image-preview:large">
<link rel="dns-prefetch" href="//cdn.jsdelivr.net">
<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">

<?php include __DIR__ . '/tracking-head.php'; ?>

<link rel="stylesheet" href="<?= asset('/assets/css/contact-form-7.css') ?>" media="all">
<link rel="stylesheet" href="<?= asset('/assets/css/studio-parent.css') ?>" media="all">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" media="all">
<link rel="stylesheet" href="<?= asset('/assets/css/studio-child.css') ?>" media="all">
<link rel="stylesheet" href="<?= asset('/assets/css/custom-fonts.css') ?>" media="all">
<link rel="stylesheet" href="<?= asset('/assets/css/typicons.css') ?>" media="all">
<link rel="stylesheet" href="<?= asset('/assets/css/blocks.css') ?>" media="all">
<?php if (!empty($PAGE['css'])): ?>
<link rel="stylesheet" href="<?= asset('/assets/css/pages/' . $PAGE['css']) ?>" media="all">
<?php endif; ?>

<script src="<?= asset('/assets/js/vendor/jquery.min.js') ?>"></script>
<script src="<?= asset('/assets/js/vendor/jquery-migrate.min.js') ?>"></script>

<link rel="icon" href="<?= base_url() ?>/assets/img/cropped-panda-icon-rich-black-scaled-1-32x32.png" sizes="32x32">
<link rel="icon" href="<?= base_url() ?>/assets/img/cropped-panda-icon-rich-black-scaled-1-192x192.png" sizes="192x192">
<link rel="apple-touch-icon" href="<?= base_url() ?>/assets/img/cropped-panda-icon-rich-black-scaled-1-180x180.png">
<meta name="msapplication-TileImage" content="<?= base_url() ?>/assets/img/cropped-panda-icon-rich-black-scaled-1-270x270.png">
</head>
