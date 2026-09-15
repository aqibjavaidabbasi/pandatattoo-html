<?php
/**
 * Site footer. Mirrors reference/html/home.html.
 * The `social` nav menu renders empty on staging, so it is intentionally omitted.
 */
?>
<script>
	jQuery(function($) {
	    $('#menu-toggle').on('click', function() {
	        $('#offcanvas-menu').addClass('active');
	        $('#menu-overlay').addClass('active');
	    });

	    $('#menu-close, #menu-overlay').on('click', function() {
	        $('#offcanvas-menu').removeClass('active');
	        $('#menu-overlay').removeClass('active');
	    });

	    $(document).on('click', '.ghl-booking-btn, .hd-header-booking-btn', function (e) {
	        e.preventDefault();
	        window.dispatchEvent(new CustomEvent('open-booking-modal'));
	    });
	});
</script>

	<div class="copyright">
		&copy; <?= date('Y') ?>	</div>

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info">
			<span class="site-copyright">
				Copyright &copy; <?= date('Y') ?> <a href="<?= base_url() ?>/"> Tatto Panda</a>			</span>
			<span class="sep">&nbsp;&bull;&nbsp;</span>
			<span class="privacy-policy">
				<a href="<?= route('/privacy-policy/') ?>">Privacy Policy</a>
			</span>
			<span class="sep">&nbsp;&bull;&nbsp;</span>
			<span class="terms-conditions">
				<a href="<?= route('/terms-and-conditions/') ?>">Terms &amp; Conditions</a>
			</span>
			<span class="sep">&nbsp;&bull;&nbsp;</span>
			<span class="theme-name">
				Theme: Studio			</span>
			<span class="theme-by">
				by			</span>
			<span class="theme-author">
				<a href="https://catchthemes.com/" target="_blank">
					Catch Themes				</a>
			</span>

	         		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.0/dist/cdn.min.js" defer></script>

<?php include __DIR__ . '/tracking-body-end.php'; ?>

</body>
</html>
