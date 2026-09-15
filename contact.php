<?php
require __DIR__ . '/inc/config.php';
$PAGE = [
    'title'      => html_entity_decode('Contact &#8211; Tatto Panda', ENT_QUOTES, 'UTF-8'),
    'body_class' => 'wp-singular page-template page-template-templates page-template-contact page-template-templatescontact-php page page-id-19 wp-custom-logo wp-embed-responsive wp-theme-studio wp-child-theme-studio-child no-sidebar excerpt-image-top',
    'css'        => 'contact.css',
];
include __DIR__ . '/inc/head.php';
include __DIR__ . '/inc/header.php';
?>
<div class="hd-contact-page">
    <div class="hd-contact-container">

        <!-- Top Hero Section -->
        <div class="hd-contact-hero">
            <h1 class="hd-contact-title">Contact</h1>
        </div>

        <!-- Main Grid Layout -->
        <div class="hd-contact-grid">

            <!-- Left Column: Info & Visual Cards -->
            <div class="hd-contact-left">
                <!-- Compact Studio & Social Channels Card -->
                <div class="hd-contact-main-card">
                    <div class="hd-card-header">
                        <span class="hd-card-tag">Studio & Social Channels</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hd-card-icon"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                    </div>

                                            <div class="hd-custom-contact-info">
                            <ul>
<li><a href="#">Panda Tattoo 254 NW 36th St, Miami, FL 33127</a>&nbsp;&nbsp;<a href="https://maps.app.goo.gl/YQyqjnjFqijJA5oP9" style="font-size:12px">Open in Google Maps</a></li>
<li><strong>Phone : </strong><a href="tel:+17869199998">(786) 919-9998</a></li>
<li><strong>Follow on Social:</strong><br /><a href="https://www.instagram.com/tatu_panda/" target="_blank">&nbsp;&nbsp;&nbsp;&#8211;&nbsp;@tatu_panda</a><br /><strong>&nbsp;&nbsp;&nbsp;&#8211;&nbsp; </strong><a href="https://www.instagram.com/pandatattoomia/" target="_blank">@pandatattoomia</a><br /><a href="https://www.instagram.com/pandatattooacademy/" target="_blank"><strong>&nbsp;&nbsp;&nbsp;&#8211;&nbsp; </strong>@pandatattooacademy</a></li>
</ul>
                        </div>
                    
                    <!-- One Liner Studio Hours -->
                    <div class="hd-contact-hours-line">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hd-card-icon"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span><strong>Hours:</strong> Mon &ndash; Sun 11:00 AM &ndash; 9:00 PM</span>
                        <span class="hd-badge-open">Open Daily</span>
                    </div>
                </div>

                <!-- Studio Photo & Brand Accent -->
                <div class="hd-contact-visual-card">
                    <div class="hd-contact-visual-img-wrap">
                        <img src="<?= base_url() ?>/assets/img/Tattoo-Artist-Tatu-Panda-3-1.jpg" alt="Panda Tattoo Studio Miami" class="hd-contact-visual-img" loading="lazy">
                    </div>
                    <img src="<?= base_url() ?>/assets/img/panda-logotype-bone-scaled.png" alt="PANDA" class="hd-contact-visual-logo" loading="lazy">
                </div>
            </div>

            <!-- Right Column: Message Form -->
            <div class="hd-contact-right">
                <div class="hd-form-header">
                    <h2 class="hd-form-title">Send a Message</h2>
                    <p class="hd-form-subtitle">Fill out your inquiry and our team will get back to you promptly.</p>
                </div>

                <div class="hd-contact-iframe-wrap">
                    <iframe
                        src="https://link.smartwebsite360.com/widget/form/B1LLvOARhRLPJ7570tJD"
                        style="width:100%;height:100%;border:none;border-radius:8px"
                        id="inline-B1LLvOARhRLPJ7570tJD" 
                        data-layout="{'id':'INLINE'}"
                        data-trigger-type="alwaysShow"
                        data-trigger-value=""
                        data-activation-type="alwaysActivated"
                        data-activation-value=""
                        data-deactivation-type="neverDeactivate"
                        data-deactivation-value=""
                        data-form-name="Contact Form"
                        data-height="581"
                        data-layout-iframe-id="inline-B1LLvOARhRLPJ7570tJD"
                        data-form-id="B1LLvOARhRLPJ7570tJD"
                        title="Contact Form"
                    >
                    </iframe>
                    <script src="https://link.smartwebsite360.com/js/form_embed.js"></script>
                </div>
            </div>

        </div>
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
