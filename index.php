<?php
// Dev-server guard: PHP's built-in server, run without router.php, falls back to this file for
// every unmatched directory URI — so /gear/ and /gallery/ silently render the home page and it
// looks as though every page is identical. Fail loudly instead. cli-server only; Apache never
// reaches this because .htaccess rewrites first.
if (PHP_SAPI === 'cli-server') {
    $req = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/');
    if ($req !== '' && $req !== '/index.php') {
        http_response_code(500);
        exit('<h1 style="font:16px/1.6 system-ui;padding:2rem">Dev server started without the router.'
           . '</h1><pre style="font:14px/1.6 ui-monospace;padding:0 2rem">You requested '
           . htmlspecialchars($req) . '/ but got the home page.

Restart with:

    php -S 127.0.0.1:8123 router.php

Without router.php the built-in server ignores .htaccess and serves index.php
for every pretty URL, making all pages look identical.</pre>');
    }
}
require __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/contentful.php';

$PAGE = [
    'title'      => 'Tatto Panda',
    'body_class' => 'home wp-singular page-template page-template-templates page-template-home-dev page-template-templateshome-dev-php page page-id-130 wp-custom-logo wp-embed-responsive wp-theme-studio wp-child-theme-studio-child no-sidebar excerpt-image-top',
    'css'        => 'home.css',
];

$artists = cf_artists(['limit' => 50, 'order' => 'fields.artistName']);
$active_artists = array_filter($artists, static function (array $artist): bool {
    return !empty($artist['profile_picture']) || !empty($artist['portfolio_images']);
});
$awards = json_decode((string) file_get_contents(__DIR__ . '/data/awards.json'), true) ?: [];

// ponytail: studio hours hardcoded 11:00-21:00 daily, same as the footer line. Move to config if
// the days ever differ from each other.
$studio_now  = new DateTime('now', new DateTimeZone('America/New_York'));
$studio_hour = (int) $studio_now->format('G');
$studio_open = $studio_hour >= 11 && $studio_hour < 21;

include __DIR__ . '/inc/head.php';
include __DIR__ . '/inc/header.php';
?>

<div class="main_layout">
    <div class="main_slider">

                                    <section class="section_1 hd-section hd-hero-section">
                    <div class="hd-section-inner">
                        <div class="hd-hero-top-wrap">
                            
                            <div class="hd-hero-col-left">
                                <!-- Live Studio Status Card -->
                                <div class="hd-hero-status-card">
                                    <div class="hd-status-col">
                                        <span class="hd-status-label">Current Time</span>
                                        <span class="hd-status-val" id="hdStatusTime"><?= $studio_now->format('g:i A T') ?></span>
                                    </div>
                                    <div class="hd-status-divider"></div>
                                    <div class="hd-status-col">
                                        <span class="hd-status-label">Studio Hours</span>
                                        <span class="hd-status-val hd-status-indicator <?= $studio_open ? 'open' : 'closed' ?>" id="hdStatusOpen">
                                            <span class="hd-status-dot"></span> <span id="hdStatusOpenText"><?= $studio_open ? 'OPEN (11AM–9PM)' : 'CLOSED (OPENS 11AM)' ?></span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Main Tagline Headline -->
                                <div class="hd-hero-top-headline">
                                                                            <h1 class="hd-tagline">Skin Art<br />
For Those Who Only Accept the Best in Life
</h1>
                                                                    </div>

                                <!-- Desktop Editorial Description -->
                                <p class="hd-hero-desktop-desc hd-desktop-only">
                                    Panda Tattoo is a Miami tattoo studio built around detail, realism, and work that actually holds weight. Led by Tatu Panda, our artists create clean, high-level tattoos for clients who care about the art, the process, and getting it done right.
                                </p>

                                <!-- Desktop Action CTAs -->
                                <div class="hd-hero-desktop-actions hd-desktop-only" x-data>
                                    <button type="button" @click="$dispatch('open-booking-modal')" onclick="window.dispatchEvent(new CustomEvent('open-booking-modal'))" class="ghl-booking-btn hd-hero-cta" aria-label="Book Consultation">
                                        <span>Book Consultation</span>
                                    </button>
                                </div>

                                <!-- Logotype (Mobile) -->
                                <div class="hd-logotype hd-mobile-only">
                                    <img
                                        src="<?= base_url() ?>/assets/img/panda-logotype-bone-scaled.png"
                                        alt="Tatu Panda"
                                        loading="eager"
                                    >
                                </div>

                                <!-- Obvious Visual Horizontal Slide Cue (Mobile Only) -->
                                <div class="hd-hero-scroll-cue hd-mobile-only" onclick="document.querySelectorAll('.global-slider-dot')[1]?.click()" role="button" aria-label="Slide to explore website">
                                    <span class="hd-scroll-cue-pulse"></span>
                                    <span class="hd-scroll-cue-text">Slide to Explore</span>
                                    <span class="hd-scroll-cue-arrow">
                                        <svg width="18" height="12" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 6H17M17 6L12 1M17 6L12 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <!-- Desktop Right Column: Studio Video & Presentation -->
                            <div class="hd-hero-col-right hd-desktop-only">
                                <div class="hd-hero-video-card">
                                    <video class="hd-hero-video" autoplay muted loop playsinline poster="<?= base_url() ?>/assets/img/Tattoo-Artist-Tatu-Panda-3-1.jpg">
                                        <source src="<?= base_url() ?>/assets/img/lv-0-20250516033811_ri6iFCJ4.mp4" type="video/mp4">
                                    </video>
                                    <div class="hd-hero-video-overlay">
                                        <span class="hd-hero-video-badge">Miami Creative Corridor</span>
                                        <div class="hd-hero-video-logo">
                                            <img src="<?= base_url() ?>/assets/img/panda-logotype-bone-scaled.png" alt="PANDA">
                                        </div>
                                    </div>
                                </div>
                                <div class="hd-hero-highlights">
                                    <span class="hd-highlight-chip">✦ Custom Tattoo Work</span>
                                    <span class="hd-highlight-chip">✦ Tattoo Specialist</span>
                                    <span class="hd-highlight-chip">✦ Renowned</span>
                                    
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
                    
                                    <section class="section_2 think_making">
                    <div class="cmn_container">
                        <div class="content_wrap">
                            <div class="hd-artists-header-info">
                                <div>
                                    <div class="hd-kicker hd-desktop-only">Featured Artists</div>
                                    <h3 class="fs_14 section-header-title">Artists</h3>
                                    <p class="hd-artists-desc hd-desktop-only">
                                        Panda Tattoo brings together a selected group of artists known for clean work, strong technique, and a serious commitment to the craft. Each artist has their own style, but the standard stays the same: high-level tattoos done with precision.
                                    </p>
                                </div>
                                <div class="hd-artists-desktop-nav hd-desktop-only">
                                    <button type="button" class="hd-artist-nav-arrow hd-artist-prev" aria-label="Scroll left">←</button>
                                    <button type="button" class="hd-artist-nav-arrow hd-artist-next" aria-label="Scroll right">→</button>
                                    <span class="hd-artists-scroll-hint">Scroll Gallery</span>
                                </div>
                                <div class="hd-artists-left-cta hd-desktop-only" x-data>
                                    <button type="button" @click="$dispatch('open-booking-modal')" onclick="window.dispatchEvent(new CustomEvent('open-booking-modal'))" class="ghl-booking-btn button" aria-label="Book Artist">
                                        <span>Book Artist</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Artist Cards - Vertical Scroll Cards -->
                        <?php if (!empty($active_artists)): ?>
                            <div class="artist-vertical-cards-wrap">
                                <?php foreach ($active_artists as $artist): ?>
                                    <?php
                                    $artist_slug = $artist['slug'];
                                    $artist_name = $artist['name'];
                                    $artist_img_url = !empty($artist['profile_picture'])
                                        ? $artist['profile_picture']
                                        : (!empty($artist['portfolio_images'][0]['url']) ? $artist['portfolio_images'][0]['url'] : '');

                                    if (empty($artist_img_url)) {
                                        continue;
                                    }
                                    ?>
                                    <a href="<?= route('/gallery/#' . e($artist_slug)) ?>" class="artist-vertical-card-link">
                                        <div class="artist-vertical-card">
                                            <div class="artist-vertical-image">
                                                <img src="<?= eu($artist_img_url) ?>" alt="<?= e($artist_name) ?>" loading="lazy">
                                            </div>
                                            <div class="artist-vertical-name"><?= e($artist_name) ?></div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Fixed Bottom Book Appointment CTA on Artist Section -->
                    <div class="artist-section-cta-fixed" x-data>
                        <button @click="$dispatch('open-booking-modal')" class="ghl-booking-btn button" aria-label="Book Appointment">
                            <span class="button-content">Book Appointment</span>
                        </button>
                    </div>
                </section>
                    
                                    <section class="section_3 service_education">
                    <div class="cmn_container">
                        <div class="content_wrap">
                            <div class="service_content image_1"><img src="<?= base_url() ?>/assets/img/Tatu-Panda-Tattoos-2-1.jpg" alt="img"></div>
                            <div class="service_content image_2"><img src="<?= base_url() ?>/assets/img/panda7.jpg" alt="img"></div>
                            <div class="service_content image_3"><img src="<?= base_url() ?>/assets/img/panda9.jpg" alt="img"></div>
                        </div>
                    </div>
                </section>
                    



                                    <section class="section_5 post_scriptum">
                    <div class="cmn_container">
                        <div class="content_wrap hd-post-scriptum-wrap">
                                                            <div class="hd-ps-eyebrow">
                                    <span class="hd-ps-tag">POST SCRIPTUM</span>
                                </div>
                                                        
                                                            <div class="hd-ps-content">
                                    <p>These aren’t just tattoos.<br />
They’re bookmarks, for victories, losses, rebirths, and revelations.<br />
Each line marks a moment. Each session carries a story.<br />
This isn’t flash. This is&nbsp;&nbsp;forever.<br />
We can’t take this shit off. So we make it count.</p>
                                </div>
                            
                            <div class="hd-ps-side-card">
                                                                    <div class="hd-ps-ingredients">
                                                                                    <h4 class="hd-ps-subtitle">INGREDIENTS</h4>
                                        
                                                                                <div class="hd-ps-tags-list">
                                                                                            <span class="hd-ps-tag-item">
                                                    <span class="hd-ps-tag-bullet">✦</span>
                                                    Truth                                                </span>
                                                                                            <span class="hd-ps-tag-item">
                                                    <span class="hd-ps-tag-bullet">✦</span>
                                                    Precision                                                </span>
                                                                                            <span class="hd-ps-tag-item">
                                                    <span class="hd-ps-tag-bullet">✦</span>
                                                    Story                                                </span>
                                                                                            <span class="hd-ps-tag-item">
                                                    <span class="hd-ps-tag-bullet">✦</span>
                                                    Soul                                                </span>
                                                                                            <span class="hd-ps-tag-item">
                                                    <span class="hd-ps-tag-bullet">✦</span>
                                                    Detail                                                </span>
                                                                                            <span class="hd-ps-tag-item">
                                                    <span class="hd-ps-tag-bullet">✦</span>
                                                    Discipline                                                </span>
                                                                                    </div>
                                    </div>
                                
                                <!-- Action CTA -->
                                <div class="hd-ps-action-wrap" x-data>
                                    <button type="button" @click="$dispatch('open-booking-modal')" onclick="window.dispatchEvent(new CustomEvent('open-booking-modal'))" class="ghl-booking-btn button hd-ps-cta-btn" aria-label="Book Appointment">
                                        <span>Book Appointment</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                    

        <section class="section_6 awards_wrp">
            <div class="cmn_container">
                <div class="main_head">
                    <h2 class="fs_20 text-white">Ink for Icons</h2>
                    <h3 class="text-white">
                        From A-listers to tastemakers, Tatu Panda’s work lives on the skin of the world’s most
                        recognized names.
                    </h3>
                </div>
                <div class="award_list">
                    <ul class="list-unstyled">
                        <?php foreach ($awards as $award): ?>
                                <li>
                                    <a href="<?= eu($award['award_link'] ?? '') ?>" target="_blank">
                                        <div class="title">
                                            <?= $award['title'] ?? '' ?>                                        </div>
                                        <div class="award">
                                            <span>
                                                <?= $award['award_name'] ?? '' ?>                                            </span>
                                        </div>
                                        <div class="year">
                                            <?= $award['year'] ?? '' ?>                                        </div>
                                    </a>
                                </li>
                        <?php endforeach; ?>
                                                    </ul>
                </div>
            </div>
        </section>

        <!-- Section 7: Final Section (Image, Panda Logo & Contact Info) -->
        <section class="section_7 hd-final-section">
            <div class="cmn_container hd-final-container">
                <div class="hd-final-content-wrap">
                    <!-- Top Image (Desktop Left Column) -->
                    <div class="hd-final-image-wrap">
                        <img src="<?= base_url() ?>/assets/img/Tattoo-Artist-Tatu-Panda-3-1.jpg" alt="Panda Tattoo Studio" class="hd-final-image" loading="lazy">
                        <div class="hd-final-image-tag">Start Your Next Piece</div>
                    </div>

                    <!-- Details (Desktop Right Column, wrapped in hd-final-details-wrap) -->
                    <div class="hd-final-details-wrap">
                        <!-- Panda Wordmark Logo -->
                        <div class="hd-final-logo-wrap">
                            <img src="<?= base_url() ?>/assets/img/panda-logotype-bone-scaled.png" alt="PANDA" class="hd-final-logo-img">
                        </div>

                        <!-- Desktop Header / Title -->
                        <div class="hd-final-title hd-desktop-only">
                            <h2>Visit Our Studio</h2>
                            <p>Located in Miami’s creative corridor. Consultations are available by appointment, with select walk-ins welcome.</p>
                        </div>

                        <!-- Contact Details Below Logo (Clean, simple like desktop) -->
                        <div class="hd-final-contact-wrap">
                            <!-- Location (Address) -> Opens Google Maps (Full Width Single Line) -->
                            <div class="hd-final-contact-item hd-final-address-item">
                                <a href="https://maps.google.com/?q=254+NW+36th+St,+Miami,+FL+33127" target="_blank" rel="noopener noreferrer" class="hd-final-contact-link" aria-label="Open Google Maps for 254 NW 36th St, Miami, FL 33127">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hd-final-icon"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    <span>254 NW 36th St, Miami, FL 33127</span>
                                </a>
                            </div>

                            <!-- Phone (Opens Dial Pad) & Hours -->
                            <div class="hd-final-contact-row">
                                <div class="hd-final-contact-item">
                                    <a href="tel:7869199998" class="hd-final-contact-link" aria-label="Call 786-919-9998">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hd-final-icon"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                        <span>(786) 919-9998</span>
                                    </a>
                                </div>

                                <div class="hd-final-contact-item hd-final-hours-item">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hd-final-icon"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <span>Mon – Sun: 11:00 AM – 9:00 PM</span>
                                </div>
                            </div>

                            <!-- Desktop Appointment Action -->
                            <div class="hd-final-action-wrap hd-desktop-only" x-data>
                                <button type="button" @click="$dispatch('open-booking-modal')" onclick="window.dispatchEvent(new CustomEvent('open-booking-modal'))" class="ghl-booking-btn button hd-final-cta-btn" aria-label="Book Appointment">
                                    <span>Book Appointment</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- Desktop Floating Slide Navigation Arrows -->
    <button type="button" class="hd-desktop-nav-arrow hd-nav-prev hd-desktop-only" id="hdDesktopPrev" aria-label="Previous Slide">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
    </button>
    <button type="button" class="hd-desktop-nav-arrow hd-nav-next hd-desktop-only" id="hdDesktopNext" aria-label="Next Slide">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </button>

    <!-- Sticky Prominent Global Slider Dots -->
    <div class="global-slider-dots-wrapper" id="globalSliderDots" role="tablist" aria-label="Section Navigation"></div>
</div>

<!-- Modal Structure -->
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
<script>
    jQuery(function ($) {
        const windowWidth = $(window).width();
        const $et_studio_slider = $('.main_slider');
        const $sections = $et_studio_slider.children('section');
        const sectionCount = $sections.length;
        const dotsContainer = document.getElementById('globalSliderDots');
        let currentSlideIndex = 0;

        // Global listener for all booking buttons to open modal
        $(document).on('click', '.ghl-booking-btn, .hd-ps-cta-btn', function (e) {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('open-booking-modal'));
        });

        // Render global sticky dots strictly for direct child sections
        const slideNames = ['Studio', 'Artists', 'Craft', 'Manifesto', 'Icons', 'Visit'];
        if (dotsContainer && sectionCount > 0) {
            dotsContainer.innerHTML = '';
            $sections.each(function (index) {
                const dot = document.createElement('button');
                dot.className = 'global-slider-dot' + (index === 0 ? ' is-active' : '');
                dot.setAttribute('data-index', index);
                dot.setAttribute('data-title', slideNames[index] || ('Slide ' + (index + 1)));
                dot.setAttribute('type', 'button');
                dot.setAttribute('role', 'tab');
                dot.setAttribute('aria-label', 'Go to ' + (slideNames[index] || ('slide ' + (index + 1))));
                dotsContainer.appendChild(dot);
            });
        }

        function updateDesktopNavArrows(activeIndex) {
            if ($('#hdDesktopPrev').length) {
                if (activeIndex <= 0) {
                    $('#hdDesktopPrev').addClass('is-disabled');
                } else {
                    $('#hdDesktopPrev').removeClass('is-disabled');
                }
            }
            if ($('#hdDesktopNext').length) {
                if (activeIndex >= sectionCount - 1) {
                    $('#hdDesktopNext').addClass('is-disabled');
                } else {
                    $('#hdDesktopNext').removeClass('is-disabled');
                }
            }
        }

        function updateActiveDot(activeIndex) {
            currentSlideIndex = activeIndex;
            updateDesktopNavArrows(activeIndex);
            if (dotsContainer) {
                const dots = dotsContainer.querySelectorAll('.global-slider-dot');
                dots.forEach((d, i) => {
                    if (i === activeIndex) {
                        d.classList.add('is-active');
                        d.setAttribute('aria-selected', 'true');
                    } else {
                        d.classList.remove('is-active');
                        d.setAttribute('aria-selected', 'false');
                    }
                });
            }
        }

        if (windowWidth >= 991) {
            function scrollToSection(targetIndex) {
                if (targetIndex < 0 || targetIndex >= sectionCount) return;
                const targetSection = $sections.get(targetIndex);
                if (targetSection) {
                    const headerHeight = 74;
                    const elementPosition = targetSection.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerHeight;
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                    updateActiveDot(targetIndex);
                }
            }

            // Dot click navigation on desktop: smooth scroll vertically
            $(dotsContainer).on('click', '.global-slider-dot', function (e) {
                e.preventDefault();
                const targetIndex = parseInt($(this).data('index'), 10);
                scrollToSection(targetIndex);
            });

            // Horizontal Artist card navigation buttons
            $(document).on('click', '.hd-artist-prev', function (e) {
                e.preventDefault();
                const wrap = document.querySelector('.artist-vertical-cards-wrap');
                if (wrap) wrap.scrollBy({ left: -320, behavior: 'smooth' });
            });

            $(document).on('click', '.hd-artist-next', function (e) {
                e.preventDefault();
                const wrap = document.querySelector('.artist-vertical-cards-wrap');
                if (wrap) wrap.scrollBy({ left: 320, behavior: 'smooth' });
            });

            // Horizontal wheel scroll on artist cards container on desktop
            const artistWrapEl = document.querySelector('.artist-vertical-cards-wrap');
            if (artistWrapEl) {
                artistWrapEl.addEventListener('wheel', function (e) {
                    if (window.innerWidth >= 991) {
                        if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
                            e.preventDefault();
                            artistWrapEl.scrollLeft += e.deltaY;
                        }
                    }
                }, { passive: false });
            }

            // Update active dot on vertical window scroll
            let scrollTimeout;
            window.addEventListener('scroll', function () {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function () {
                    const scrollPosition = window.pageYOffset + 200;
                    let activeIndex = 0;
                    $sections.each(function (i, sec) {
                        if (sec.offsetTop <= scrollPosition) {
                            activeIndex = i;
                        }
                    });
                    updateActiveDot(activeIndex);
                }, 40);
            }, { passive: true });
        } else {
            // Mobile navigation & touch scroll sync
            const sliderEl = document.querySelector('.main_slider');
            if (dotsContainer && sliderEl) {
                $(dotsContainer).on('click', '.global-slider-dot', function (e) {
                    e.preventDefault();
                    const targetIndex = parseInt($(this).data('index'), 10);
                    const targetSection = $sections.get(targetIndex);
                    if (targetSection) {
                        sliderEl.scrollTo({
                            left: targetSection.offsetLeft,
                            behavior: 'smooth'
                        });
                        updateActiveDot(targetIndex);
                    }
                });

                let scrollTimer;
                sliderEl.addEventListener('scroll', function () {
                    window.clearTimeout(scrollTimer);
                    scrollTimer = setTimeout(function () {
                        const scrollLeft = sliderEl.scrollLeft;
                        let activeIndex = 0;
                        let minDistance = Infinity;
                        $sections.each(function (i, sec) {
                            const dist = Math.abs(sec.offsetLeft - scrollLeft);
                            if (dist < minDistance) {
                                minDistance = dist;
                                activeIndex = i;
                            }
                        });
                        updateActiveDot(activeIndex);
                    }, 30);
                }, { passive: true });
            }
        }
    });
</script>

<script>
    // Keeps the hero status card live after page load; PHP renders the first value.
    (function () {
        const timeEl = document.getElementById('hdStatusTime');
        const openEl = document.getElementById('hdStatusOpen');
        const textEl = document.getElementById('hdStatusOpenText');
        if (!timeEl || !openEl || !textEl) return;

        const TZ = 'America/New_York';
        const timeFmt = new Intl.DateTimeFormat('en-US', { timeZone: TZ, hour: 'numeric', minute: '2-digit', timeZoneName: 'short' });
        const hourFmt = new Intl.DateTimeFormat('en-US', { timeZone: TZ, hour: 'numeric', hour12: false });

        function tick() {
            const now = new Date();
            timeEl.textContent = timeFmt.format(now);
            const hour = parseInt(hourFmt.format(now), 10) % 24; // some engines report midnight as 24
            const isOpen = hour >= 11 && hour < 21;
            openEl.classList.toggle('open', isOpen);
            openEl.classList.toggle('closed', !isOpen);
            textEl.textContent = isOpen ? 'OPEN (11AM–9PM)' : 'CLOSED (OPENS 11AM)';
        }

        tick();
        setInterval(tick, 1000);
    })();

    const bullet = document.querySelector('.bullet');
    if (bullet) {
        let mouseX = 0, mouseY = 0;
        let currentX = 0, currentY = 0;

        document.addEventListener('mousemove', (e) => {
            mouseX = e.pageX;
            mouseY = e.pageY;
        });

        function animate() {
            currentX += (mouseX - currentX) * 0.08;
            currentY += (mouseY - currentY) * 0.08;

            bullet.style.left = `${currentX}px`;
            bullet.style.top = `${currentY}px`;

            requestAnimationFrame(animate);
        }

        animate();
    }
</script>

