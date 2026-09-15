<?php
/**
 * Body open + site header. Markup mirrors reference/html/home.html byte-for-byte,
 * except that menu URLs are root-relative and the logos are served locally.
 */
$__here = current_path();
$__nav = [
    ['/',         'Home',    23, null],
    ['/gallery/', 'Gallery', 129, 14],
    ['/contact/', 'Contact', 128, 19],
];
/** Renders the nav <li>s, reproducing WordPress's menu classes. */
function panda_nav(array $items, string $here, bool $with_ids): void {
    foreach ($items as [$href, $label, $id, $page_id]) {
        $type = $href === '/' ? 'custom menu-item-object-custom' : 'post_type menu-item-object-page';
        $cur  = $href === $here
            ? ($page_id ? ' current-menu-item page_item page-item-' . $page_id . ' current_page_item' : ' current-menu-item current_page_item')
            : '';
        $idat = $with_ids ? ' id="menu-item-' . $id . '"' : '';
        $aria = $href === $here ? ' aria-current="page"' : '';
        echo '<li' . $idat . ' class="menu-item menu-item-type-' . $type . $cur . ' menu-item-' . $id . '">'
           . '<a href="' . route($href) . '"' . $aria . '>' . e($label) . '</a></li>' . "\n";
    }
}
?>
<body class="<?= e($PAGE['body_class'] ?? '') ?>">

<?php include __DIR__ . '/tracking-body-open.php'; ?>

	<header>
		<div class="logo_wrap animate__animated animate__fadeInDown">
			<a href="<?= base_url() ?>/" aria-label="Homepage" style="pointer-events: auto;">
				<div class="mbm-diff">
					<img src="<?= base_url() ?>/assets/img/panda-icon-bone-white-scaled.png" class="logo__et difference"/>
				</div>
			</a>
		</div>

		<!-- Toggle Button -->
		<button id="menu-toggle" aria-label="Open Menu">
			<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4 18L20 18" stroke="#000000" stroke-width="2" stroke-linecap="round"></path> <path d="M4 12L20 12" stroke="#000000" stroke-width="2" stroke-linecap="round"></path> <path d="M4 6L20 6" stroke="#000000" stroke-width="2" stroke-linecap="round"></path> </g></svg>
		</button>

		<!-- Off-Canvas Menu -->
		<div id="offcanvas-menu">
		    <button id="menu-close" aria-label="Close Menu">×</button>
		    <div class="menu-main-menu-container"><ul id="menu-main-menu" class="offcanvas-nav">
<?php panda_nav($__nav, $__here, true); ?>
</ul></div>		    <a class="mobile_logo" href="<?= base_url() ?>/" aria-label="Homepage" style="pointer-events: auto;">
		    	<div class="mbm-diff">
					<img src="<?= base_url() ?>/assets/img/panda-logotype-bone-scaled.png" class="logo__et difference"/>
		    	</div>
		    </a>
		</div>

		<!-- Overlay -->
		<div id="menu-overlay"></div>

		   <!-- Navigation -->
		   <nav id="site-navigation" class="main-navigation">
		     <ul id="primary-menu" class="menu d-flex">
<?php panda_nav($__nav, $__here, false); ?>
</ul>		   </nav>
		   <div class="header-desktop-cta hd-desktop-only" x-data>
		       <button @click="$dispatch('open-booking-modal')" onclick="window.dispatchEvent(new CustomEvent('open-booking-modal'))" class="ghl-booking-btn hd-header-booking-btn" aria-label="Book Appointment">
		           <span>Book Now</span>
		       </button>
		   </div>
		   <div class="bullet"></div>
	</header>
			<div id="content" class="site-content">
