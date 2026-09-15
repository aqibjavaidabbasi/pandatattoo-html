<?php
require __DIR__ . '/inc/config.php';

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
