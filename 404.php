<?php
require __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/contentful.php';

// Artist vanity URLs (/dani-luz, /sophie) send visitors to that artist's booking form, as
// they did on WordPress. ponytail: handled here rather than with its own rewrite rule —
// every unmatched path already lands on 404.php under both Apache and the dev router.
// 302, not 301: a browser-cached permanent redirect would outlive any future /<slug> page.
$vanity_slug = strtolower(basename(trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', '/')));
$vanity_map  = cf_artist_map();
if (isset($vanity_map[$vanity_slug])) {
    header('Location: ' . route('/booking/?artist=' . rawurlencode($vanity_slug) . '&artistId=' . rawurlencode($vanity_map[$vanity_slug])), true, 302);
    exit;
}

// Reached through a rewrite, not ErrorDocument (see .htaccess), so the status is ours to set.
http_response_code(404);

$search_value = isset($_GET['s']) && is_scalar($_GET['s']) ? (string) $_GET['s'] : '';
$PAGE = [
    'title'      => html_entity_decode('Page not found &#8211; Tatto Panda', ENT_QUOTES, 'UTF-8'),
    'body_class' => 'error404 wp-custom-logo wp-embed-responsive wp-theme-studio wp-child-theme-studio-child no-sidebar excerpt-image-top',
    'css'        => null,
];
include __DIR__ . '/inc/head.php';
include __DIR__ . '/inc/header.php';
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">
				<header class="page-header">
					<h1 class="page-title">Oops! That page can&rsquo;t be found.</h1>
				</header><!-- .page-header -->

				<div class="page-content">
					<p>It looks like nothing was found at this location. Maybe try one of the links below or a search?</p>

					<form role="search" method="get" class="search-form" action="<?= base_url() ?>/">
						<label>
							<span class="screen-reader-text">Search for:</span>
							<input type="search" class="search-field" placeholder="Search..." value="<?= e($search_value) ?>" name="s" title="Search for:">
						</label>
						<input type="submit" class="search-submit" value="Search">
					</form>

				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

	    </div><!-- #content -->
		
<?php include __DIR__ . '/inc/footer.php'; ?>
