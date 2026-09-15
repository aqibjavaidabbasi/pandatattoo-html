<?php
require __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/contentful.php';

$PAGE = [
    'title'      => html_entity_decode('Gallery &#8211; Tatto Panda', ENT_QUOTES, 'UTF-8'),
    'body_class' => 'wp-singular page-template page-template-templates page-template-work page-template-templateswork-php page page-id-14 wp-custom-logo wp-embed-responsive wp-theme-studio wp-child-theme-studio-child no-sidebar excerpt-image-top',
    'css'        => null,
];

include __DIR__ . '/inc/head.php';
include __DIR__ . '/inc/header.php';

$artists = cf_artists(['limit' => 50, 'order' => 'fields.artistName']);
$artists_data = [];
foreach ($artists as $artist) {
    if (!empty($artist['portfolio_images']) || !empty($artist['profile_picture'])) {
        $cover_img = !empty($artist['profile_picture'])
            ? $artist['profile_picture']
            : (!empty($artist['portfolio_images'][0]['url']) ? $artist['portfolio_images'][0]['url'] : '');

        $artists_data[] = [
            'name'      => $artist['name'],
            'slug'      => $artist['slug'],
            'bio'       => !empty($artist['bio']) ? $artist['bio'] : '',
            'cover_img' => $cover_img,
            'images'    => !empty($artist['portfolio_images']) ? $artist['portfolio_images'] : [],
        ];
    }
}

$contentful_image_url = static function (string $url, array $params = []): string {
    if ($url === '') return '';
    $query = http_build_query(array_merge(['fm' => 'webp', 'q' => 80], $params));
    return $url . (str_contains($url, '?') ? '&' : '?') . $query;
};
$attr_url = static fn (?string $url): string => str_replace('&amp;', '&#038;', eu($url));

$featured_works = [
    ['title' => 'Tattoo Panda 5', 'file' => 'Tatu-Panda-Tattoos-19-1.jpg', 'src' => 'Tatu-Panda-Tattoos-19-1-768x960.jpg', 'srcset' => ['Tatu-Panda-Tattoos-19-1-768x960.jpg 768w', 'Tatu-Panda-Tattoos-19-1-240x300.jpg 240w', 'Tatu-Panda-Tattoos-19-1-819x1024.jpg 819w', 'Tatu-Panda-Tattoos-19-1.jpg 1080w']],
    ['title' => 'Tattoo Panda 4', 'file' => 'Tatu-Panda-Tattoos-17-1.jpg', 'src' => 'Tatu-Panda-Tattoos-17-1-768x960.jpg', 'srcset' => ['Tatu-Panda-Tattoos-17-1-768x960.jpg 768w', 'Tatu-Panda-Tattoos-17-1-240x300.jpg 240w', 'Tatu-Panda-Tattoos-17-1-819x1024.jpg 819w', 'Tatu-Panda-Tattoos-17-1.jpg 1080w']],
    ['title' => 'Tattoo Panda 3', 'file' => 'Tatu-Panda-Tattoos-7-1.jpg', 'src' => 'Tatu-Panda-Tattoos-7-1-768x960.jpg', 'srcset' => ['Tatu-Panda-Tattoos-7-1-768x960.jpg 768w', 'Tatu-Panda-Tattoos-7-1-240x300.jpg 240w', 'Tatu-Panda-Tattoos-7-1-819x1024.jpg 819w', 'Tatu-Panda-Tattoos-7-1.jpg 1080w']],
    ['title' => 'Tattoo Panda 2', 'file' => 'Tatu-Panda-Tattoos-2-1.jpg', 'src' => 'Tatu-Panda-Tattoos-2-1-768x960.jpg', 'srcset' => ['Tatu-Panda-Tattoos-2-1-768x960.jpg 768w', 'Tatu-Panda-Tattoos-2-1-240x300.jpg 240w', 'Tatu-Panda-Tattoos-2-1-819x1024.jpg 819w', 'Tatu-Panda-Tattoos-2-1.jpg 1080w']],
    ['title' => 'Tattoo Panda 1', 'file' => 'Tatu-Panda-Tattoos-11-1.jpg', 'src' => 'Tatu-Panda-Tattoos-11-1-768x960.jpg', 'srcset' => ['Tatu-Panda-Tattoos-11-1-768x960.jpg 768w', 'Tatu-Panda-Tattoos-11-1-240x300.jpg 240w', 'Tatu-Panda-Tattoos-11-1-819x1024.jpg 819w', 'Tatu-Panda-Tattoos-11-1.jpg 1080w']],
];
$has_featured = !empty($featured_works);
?>

<!-- Fancybox 5 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">
<link rel="stylesheet" href="<?= asset('/assets/css/pages/gallery.css') ?>" media="all">


<div class="main_work_layout">
    
    <!-- Top Hero Section -->
    <div class="hd-gallery-hero">
        <div class="hd-gallery-eyebrow">Panda Tattoo Studio</div>
        <h1 class="hd-gallery-title">Gallery</h1>
        <p class="hd-gallery-subtitle">Explore signature artwork and portfolio pieces crafted by our resident artists.</p>

        <!-- Horizontal Artist Filter Navigation Pills -->
        <?php if (!empty($artists_data)): ?>
            <div class="hd-artist-nav-wrap">
                <div class="hd-artist-nav-list">
                    <a href="#all" class="hd-nav-pill is-active" data-artist-target="all">
                        <span>All Artists</span>
                        <span class="hd-nav-pill-count">(<?= count($artists_data) ?>)</span>
                    </a>
                    
                    <?php if ($has_featured): ?>
                        <a href="#featured" class="hd-nav-pill" data-artist-target="featured">
                            <span>Featured Work</span>
                            <span class="hd-nav-pill-count">(<?= count($featured_works) ?>)</span>
                        </a>
                    <?php endif; ?>

                    <?php foreach ($artists_data as $artist): ?>
                        <a href="#<?= e($artist['slug']) ?>" class="hd-nav-pill" data-artist-target="<?= e($artist['slug']) ?>">
                            <span><?= e($artist['name']) ?></span>
                            <?php if (!empty($artist['images'])): ?>
                                <span class="hd-nav-pill-count">(<?= count($artist['images']) ?>)</span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="hd-gallery-container">

        <!-- ====================================================
             VIEW 1: ALL ARTISTS OVERVIEW GRID
             (Exact Home Page Section 2 Card Design)
             ==================================================== -->
        <div id="view-all-artists" class="hd-artists-overview-wrap">
            <div class="hd-artists-overview-grid">
                <?php foreach ($artists_data as $artist): ?>
                    <?php
                    $artist_slug = $artist['slug'];
                    $artist_name = $artist['name'];
                    $artist_img_url = $artist['cover_img'];
                    $piece_count = count($artist['images']);
                    if (empty($artist_img_url)) {
                        continue;
                    }
                    ?>
                    <div class="artist-vertical-card-link" data-select-artist="<?= e($artist_slug) ?>" role="button" tabindex="0" aria-label="View <?= e($artist_name) ?> Gallery">
                        <div class="artist-vertical-card">
                            <div class="artist-vertical-image">
                                <img src="<?= $attr_url($artist_img_url) ?>" alt="<?= e($artist_name) ?>" loading="lazy">
                                <?php if ($piece_count > 0): ?>
                                    <span class="artist-vertical-badge"><?= $piece_count ?> Works</span>
                                <?php endif; ?>
                            </div>
                            <div class="artist-vertical-name">
                                <span><?= e($artist_name) ?></span>
                                <span class="artist-vertical-cta-text">
                                    View Portfolio &rarr;
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ====================================================
             VIEW 2: FEATURED STUDIO WORK (If Present in WP)
             ==================================================== -->
        <?php if ($has_featured): ?>
            <div id="view-artist-featured" class="hd-single-artist-wrap">
                <div class="hd-artist-profile-header">
                    <button class="hd-back-all-btn" data-back-all aria-label="Back to all artists">
                        &larr; All Artists
                    </button>
                    <div class="hd-artist-profile-center">
                        <div class="hd-artist-profile-meta">
                            <h2>Featured Studio Work</h2>
                            <span><?= count($featured_works) ?> Pieces in Collection</span>
                        </div>
                    </div>
                    <div x-data>
                        <button @click="$dispatch('open-booking-modal')" class="hd-artist-book-btn" aria-label="Book Appointment">
                            Book Appointment
                        </button>
                    </div>
                </div>

                <div class="hd-artwork-grid">
                    <?php foreach ($featured_works as $work): ?>
                        <?php $srcset = implode(', ', array_map(static fn ($item) => url('/assets/img/' . $item), $work['srcset'])); ?>
                        <a data-fancybox="gallery-featured" href="<?= base_url() ?>/assets/img/<?= e($work['file']) ?>" class="hd-artwork-card" aria-label="<?= e($work['title']) ?>">
                            <img width="768" height="960" src="<?= base_url() ?>/assets/img/<?= e($work['src']) ?>" class="attachment-medium_large size-medium_large" alt="<?= e($work['title']) ?>" loading="lazy" decoding="async" sizes="auto, (max-width: 600px) 50vw, (max-width: 1024px) 33vw, 25vw" srcset="<?= e($srcset) ?>" />                            <div class="hd-artwork-overlay">
                                <div class="hd-zoom-icon-badge">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ====================================================
             VIEW 3: INDIVIDUAL ARTIST GALLERIES
             (Only the active artist is visible on selection)
             ==================================================== -->
        <?php if (!empty($artists_data)): ?>
            <?php foreach ($artists_data as $artist): ?>
                <div id="view-artist-<?= e($artist['slug']) ?>" class="hd-single-artist-wrap">
                    
                    <!-- Artist Profile Bar -->
                    <div class="hd-artist-profile-header">
                        <button class="hd-back-all-btn" data-back-all aria-label="Back to all artists">
                            &larr; All Artists
                        </button>
                        <div class="hd-artist-profile-center">
                            <?php if (!empty($artist['cover_img'])): ?>
                                <div class="hd-artist-avatar-thumb">
                                    <img src="<?= $attr_url($artist['cover_img']) ?>" alt="<?= e($artist['name']) ?>" loading="lazy">
                                </div>
                            <?php endif; ?>
                            <div class="hd-artist-profile-meta">
                                <h2><?= e($artist['name']) ?></h2>
                                <span><?= count($artist['images']) ?> Pieces in Portfolio</span>
                            </div>
                        </div>
                        <div x-data>
                            <button @click="$dispatch('open-booking-modal')" class="hd-artist-book-btn" aria-label="Book with <?= e($artist['name']) ?>">
                                Book With <?= e(explode(' ', trim($artist['name']))[0]) ?>
                            </button>
                        </div>
                    </div>

                    <!-- Selected Artist Artworks Grid (NO SLIDER) -->
                    <div class="hd-artwork-grid">
                        <?php
                        $count = 1;
                        foreach ($artist['images'] as $img):
                            if (empty($img['url'])) continue;
                            $img_original = $img['url'];
                            $img_full = $contentful_image_url($img_original, [
                                'w' => 2000,
                                'fit' => 'scale'
                            ]);
                            $img_src = $contentful_image_url($img_original, [
                                'w' => 800,
                                'h' => 1000,
                                'fit' => 'thumb',
                                'f' => 'center'
                            ]);
                            $img_srcset = implode(', ', [
                                $attr_url($contentful_image_url($img_original, [
                                    'w' => 480,
                                    'h' => 600,
                                    'fit' => 'thumb',
                                    'f' => 'center'
                                ])) . ' 480w',
                                $attr_url($contentful_image_url($img_original, [
                                    'w' => 768,
                                    'h' => 960,
                                    'fit' => 'thumb',
                                    'f' => 'center'
                                ])) . ' 768w',
                                $attr_url($contentful_image_url($img_original, [
                                    'w' => 1200,
                                    'h' => 1500,
                                    'fit' => 'thumb',
                                    'f' => 'center'
                                ])) . ' 1200w'
                            ]);
                            $img_alt = !empty($img['alt']) ? $img['alt'] : $artist['name'] . ' - Tattoo Artwork ' . $count;
                        ?>
                            <a data-fancybox="gallery-<?= e($artist['slug']) ?>" href="<?= $attr_url($img_full) ?>" class="hd-artwork-card" aria-label="<?= e($img_alt) ?>">
                                <img
                                    src="<?= $attr_url($img_src) ?>"
                                    srcset="<?= $img_srcset ?>"
                                    sizes="(max-width: 600px) 50vw, (max-width: 1024px) 33vw, 25vw"
                                    alt="<?= e($img_alt) ?>"
                                    loading="lazy"
                                    decoding="async"
                                >
                                <div class="hd-artwork-overlay">
                                    <div class="hd-zoom-icon-badge">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                                    </div>
                                </div>
                            </a>
                        <?php
                            $count++;
                        endforeach;
                        ?>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

</div>

<!-- Exact Home Page Style Fixed Bottom Book Appointment CTA -->
<div class="artist-section-cta-fixed" x-data>
    <button @click="$dispatch('open-booking-modal')" class="ghl-booking-btn button" aria-label="Book Appointment">
        <span class="button-content">Book Appointment</span>
    </button>
</div>

<!-- Modal Structure for Booking -->
<div id="ghlBookingModal" class="ghl-modal">
    <div class="ghl-modal-content">
        <span class="ghl-close">&times;</span>
        <iframe src="https://link.smartwebsite360.com/widget/booking/oHN0M6e18FAfLByWox01"
            style="width: 100%; border: none; overflow: hidden; height: 600px;" scrolling="no"
            id="oHN0M6e18FAfLByWox01_1753171389626">
        </iframe>
        <script src="https://link.smartwebsite360.com/js/form_embed.js" type="text/javascript"></script>
    </div>
</div>


<?php include __DIR__ . '/inc/booking-modal.php'; ?>
	    </div><!-- #content -->
		
<?php include __DIR__ . '/inc/footer.php'; ?>

<!-- Fancybox Lightbox Script -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Fancybox v5
        if (typeof Fancybox !== 'undefined') {
            Fancybox.bind('[data-fancybox]', {
                Thumbs: {
                    autoStart: true,
                },
                Toolbar: {
                    display: {
                        left: ["infobar"],
                        middle: [],
                        right: ["iterateZoom", "close"],
                    },
                },
                on: {
                    "init": () => {
                        document.body.classList.add('has-fancybox');
                    },
                    "ready": () => {
                        document.body.classList.add('has-fancybox');
                    },
                    "destroy": () => {
                        document.body.classList.remove('has-fancybox');
                    },
                    "close": () => {
                        document.body.classList.remove('has-fancybox');
                    }
                }
            });
        }

        const overviewView = document.getElementById('view-all-artists');
        const singleArtistViews = document.querySelectorAll('.hd-single-artist-wrap');
        const navPills = document.querySelectorAll('.hd-nav-pill');
        const galleryContainer = document.querySelector('.main_work_layout');

        // Function to activate a specific artist view
        function showArtist(targetSlug, updateUrl = true) {
            if (!targetSlug || targetSlug === 'all') {
                // Show all artists overview
                if (overviewView) overviewView.style.display = 'block';
                singleArtistViews.forEach(v => v.classList.remove('is-visible'));

                // Update active pill
                navPills.forEach(pill => {
                    if (pill.getAttribute('data-artist-target') === 'all') {
                        pill.classList.add('is-active');
                    } else {
                        pill.classList.remove('is-active');
                    }
                });

                if (updateUrl && window.location.hash !== '#all') {
                    history.pushState(null, null, window.location.pathname);
                }
            } else {
                const targetView = document.getElementById('view-artist-' + targetSlug);
                if (targetView) {
                    // Hide overview and hide other artists
                    if (overviewView) overviewView.style.display = 'none';
                    singleArtistViews.forEach(v => v.classList.remove('is-visible'));

                    // Show selected artist only
                    targetView.classList.add('is-visible');

                    // Update active pill
                    navPills.forEach(pill => {
                        if (pill.getAttribute('data-artist-target') === targetSlug) {
                            pill.classList.add('is-active');
                        } else {
                            pill.classList.remove('is-active');
                        }
                    });

                    if (updateUrl) {
                        history.pushState(null, null, '#' + targetSlug);
                    }
                }
            }

            // Scroll smoothly to gallery top if user is down the page
            if (window.scrollY > 200) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        // Click on Navigation Pills
        navPills.forEach(pill => {
            pill.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.getAttribute('data-artist-target');
                showArtist(target, true);
            });
        });

        // Click on Artist Overview Cards
        document.querySelectorAll('[data-select-artist]').forEach(card => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                const slug = this.getAttribute('data-select-artist');
                showArtist(slug, true);
            });
            // Enter key accessibility
            card.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const slug = this.getAttribute('data-select-artist');
                    showArtist(slug, true);
                }
            });
        });

        // Click on Back to All Artists buttons
        document.querySelectorAll('[data-back-all]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                showArtist('all', true);
            });
        });

        // Handle initial hash on page load (e.g. /gallery/#artist-slug from homepage)
        function checkHash() {
            const rawHash = window.location.hash.replace('#', '').trim();
            if (rawHash && rawHash !== 'all') {
                const targetView = document.getElementById('view-artist-' + rawHash);
                if (targetView) {
                    showArtist(rawHash, false);
                }
            } else {
                showArtist('all', false);
            }
        }

        checkHash();

        // Listen for browser Back/Forward buttons
        window.addEventListener('popstate', function() {
            checkHash();
        });
    });
</script>
