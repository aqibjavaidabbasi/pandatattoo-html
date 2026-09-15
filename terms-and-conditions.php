<?php
require __DIR__ . '/inc/config.php';
$PAGE = [
    'title'      => html_entity_decode('terms-and-conditions &#8211; Tatto Panda', ENT_QUOTES, 'UTF-8'),
    'body_class' => 'wp-singular page-template-default page page-id-888 wp-custom-logo wp-embed-responsive wp-theme-studio wp-child-theme-studio-child no-sidebar excerpt-image-top',
    'css'        => 'terms-and-conditions.css',
];
include __DIR__ . '/inc/head.php';
include __DIR__ . '/inc/header.php';
?>
<div class="hd-legal-page">
    <div class="hd-legal-container">

        <!-- Top Hero Section (Div instead of header tag to prevent global theme CSS interference) -->
        <div class="hd-legal-hero">
            <div class="hd-legal-eyebrow">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span>Studio Policies &amp; Terms</span>
            </div>
            <h1 class="hd-legal-title">Terms and Conditions</h1>
            <div class="hd-legal-meta">
                <span class="hd-legal-meta-dot"></span>
                <span>Last updated: September 3, 2026</span>
            </div>
        </div>

        <!-- Introductory Card -->
        <div class="hd-legal-intro-card">
            <p>These Terms and Conditions govern your use of <a href="<?= base_url() ?>/" target="_blank" rel="noopener noreferrer">https://pandatattoo.com</a>, operated by <strong>Panda Tattoo LLC</strong> ("we," "us," or "our"). By accessing or using this website, you agree to these Terms. If you do not agree, please do not use this website.</p>
        </div>

        <!-- Quick Jump Navigation / Table of Contents -->
        <nav class="hd-legal-toc-wrap" aria-label="Table of Contents">
            <div class="hd-legal-toc-header">
                <span class="hd-legal-toc-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                    Table of Contents
                </span>
                <span style="font-size: 11px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.1em;">17 Sections</span>
            </div>
            <div class="hd-legal-toc-grid">
                <a href="#section-1" class="hd-toc-link"><span class="hd-toc-num">01</span><span>Use of This Website</span></a>
                <a href="#section-2" class="hd-toc-link"><span class="hd-toc-num">02</span><span>Services &amp; Appointments</span></a>
                <a href="#section-3" class="hd-toc-link"><span class="hd-toc-num">03</span><span>SMS Consent &amp; Rules</span></a>
                <a href="#section-4" class="hd-toc-link"><span class="hd-toc-num">04</span><span>Call Recording</span></a>
                <a href="#section-5" class="hd-toc-link"><span class="hd-toc-num">05</span><span>Intellectual Property</span></a>
                <a href="#section-6" class="hd-toc-link"><span class="hd-toc-num">06</span><span>Your Content</span></a>
                <a href="#section-7" class="hd-toc-link"><span class="hd-toc-num">07</span><span>Photography &amp; Portfolio</span></a>
                <a href="#section-8" class="hd-toc-link"><span class="hd-toc-num">08</span><span>Age &amp; Identification</span></a>
                <a href="#section-9" class="hd-toc-link"><span class="hd-toc-num">09</span><span>Appointments &amp; Deposits</span></a>
                <a href="#section-10" class="hd-toc-link"><span class="hd-toc-num">10</span><span>Aftercare Guidance</span></a>
                <a href="#section-11" class="hd-toc-link"><span class="hd-toc-num">11</span><span>Third-Party Links</span></a>
                <a href="#section-12" class="hd-toc-link"><span class="hd-toc-num">12</span><span>No Warranties</span></a>
                <a href="#section-13" class="hd-toc-link"><span class="hd-toc-num">13</span><span>Limitation of Liability</span></a>
                <a href="#section-14" class="hd-toc-link"><span class="hd-toc-num">14</span><span>Indemnification</span></a>
                <a href="#section-15" class="hd-toc-link"><span class="hd-toc-num">15</span><span>Governing Law</span></a>
                <a href="#section-16" class="hd-toc-link"><span class="hd-toc-num">16</span><span>Changes to Terms</span></a>
                <a href="#section-17" class="hd-toc-link"><span class="hd-toc-num">17</span><span>Contact Studio</span></a>
            </div>
        </nav>

        <!-- Document Sections -->
        <main class="hd-legal-content">

            <!-- Section 1 -->
            <section id="section-1" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">01</span>
                    <h2 class="hd-section-title">Use of This Website</h2>
                </div>
                <div class="hd-section-body">
                    <p>You agree to use this website lawfully and not to disrupt or interfere with its operation. You are responsible for any information you submit and for ensuring it is accurate.</p>
                    <p>This website is intended for use by individuals 18 years of age or older.</p>
                </div>
            </section>

            <!-- Section 2 -->
            <section id="section-2" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">02</span>
                    <h2 class="hd-section-title">Services and Appointments</h2>
                </div>
                <div class="hd-section-body">
                    <p>Information on this website describes tattoo services offered at our studio. Any pricing, availability, or timeline shown is general information, not a binding quote. Final pricing depends on design, size, placement, and time required, and is confirmed during consultation.</p>
                </div>
            </section>

            <!-- Section 3 -->
            <section id="section-3" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">03</span>
                    <h2 class="hd-section-title">Consent to Receive Text Messages (SMS)</h2>
                </div>
                <div class="hd-section-body">
                    <p>By providing your phone number through any form on this website, by phone, or in person, you consent to receive text messages (SMS) from Panda Tattoo, including:</p>
                    <ul class="hd-legal-list">
                        <li class="hd-legal-list-item">Appointment confirmations, reminders, and scheduling updates</li>
                        <li class="hd-legal-list-item">Design and consultation follow-up</li>
                        <li class="hd-legal-list-item">Aftercare information following a session</li>
                        <li class="hd-legal-list-item">Requests for feedback or a review</li>
                        <li class="hd-legal-list-item">Occasional promotional offers</li>
                    </ul>

                    <div class="hd-callout-card hd-callout-accent">
                        <div class="hd-sub-heading" style="margin-top: 0;">Message frequency</div>
                        <p>Message frequency varies based on your interaction with us.</p>

                        <div class="hd-sub-heading">Opting out</div>
                        <p>You may opt out at any time by replying <strong>STOP</strong> to any message. A confirmation will be sent and messages will stop.</p>

                        <div class="hd-sub-heading">Support</div>
                        <p>Reply <strong>HELP</strong> for assistance, or get in touch through our contact page at <a href="<?= route('/contact/') ?>" target="_blank" rel="noopener noreferrer">https://pandatattoo.com/contact</a>.</p>

                        <div class="hd-sub-heading">Message and data rates</div>
                        <p>Standard message and data rates may apply according to your mobile plan.</p>

                        <div class="hd-sub-heading">Carrier liability</div>
                        <p>Mobile carriers are not liable for delayed or undelivered messages.</p>

                        <div class="hd-sub-heading">Privacy</div>
                        <p>No mobile information will be shared with third parties or affiliates for marketing or promotional purposes. All information is handled in accordance with our <a href="<?= route('/privacy-policy/') ?>">Privacy Policy</a>.</p>
                    </div>
                </div>
            </section>

            <!-- Section 4 -->
            <section id="section-4" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">04</span>
                    <h2 class="hd-section-title">Call Recording</h2>
                </div>
                <div class="hd-section-body">
                    <p>Calls to and from our business phone number may be recorded for quality assurance and training purposes. See our <a href="<?= route('/privacy-policy/') ?>">Privacy Policy</a> for details.</p>
                </div>
            </section>

            <!-- Section 5 -->
            <section id="section-5" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">05</span>
                    <h2 class="hd-section-title">Intellectual Property</h2>
                </div>
                <div class="hd-section-body">
                    <p>All content on this website, including text, images, logos, and graphics, is the property of Panda Tattoo LLC or its licensors and may not be copied, reproduced, or redistributed without permission.</p>
                    <p>Original designs created by our artists remain the intellectual property of the artist unless otherwise agreed in writing.</p>
                </div>
            </section>

            <!-- Section 6 -->
            <section id="section-6" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">06</span>
                    <h2 class="hd-section-title">Your Content</h2>
                </div>
                <div class="hd-section-body">
                    <p>Any reference images, design ideas, or other materials you send us remain yours. By sending them, you grant us permission to use them for the purpose of designing and performing your tattoo.</p>
                    <p>You confirm that anything you send us is yours to share and does not infringe anyone else's rights, including the rights of another artist.</p>
                </div>
            </section>

            <!-- Section 7 -->
            <section id="section-7" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">07</span>
                    <h2 class="hd-section-title">Photography and Portfolio Use</h2>
                </div>
                <div class="hd-section-body">
                    <p>Completed tattoo work may be photographed for our portfolio, website, and social media. Because these photographs show part of your body, we ask for your permission before taking or publishing them.</p>
                    <p>You may decline photography at any time, and you may ask us to remove a photograph of your tattoo from our portfolio or social media at any time by contacting us. We will remove it from channels we control as promptly as we are able.</p>
                    <p>We will not publish a photograph that identifies you by name without your permission.</p>
                </div>
            </section>

            <!-- Section 8 -->
            <section id="section-8" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">08</span>
                    <h2 class="hd-section-title">Age Requirement and Identification</h2>
                </div>
                <div class="hd-section-body">
                    <div class="hd-callout-card hd-callout-success">
                        <div class="hd-callout-title">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <span>Florida Statutory Age Policy</span>
                        </div>
                        <p><strong>You must meet the minimum legal age for tattooing in the State of Florida. Valid government-issued photo identification is required before any session, without exception.</strong></p>
                    </div>
                    <p>We reserve the right to refuse service where identification cannot be provided or where age cannot be verified.</p>
                </div>
            </section>

            <!-- Section 9 -->
            <section id="section-9" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">09</span>
                    <h2 class="hd-section-title">Appointments and Deposits</h2>
                </div>
                <div class="hd-section-body">
                    <p>A deposit may be required to hold an appointment. Deposit amount, refund conditions, and rescheduling windows are confirmed at the time of booking.</p>
                    <p>Nothing on this website creates a binding reservation. An appointment is held only once any required deposit has been received.</p>
                </div>
            </section>

            <!-- Section 10 -->
            <section id="section-10" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">10</span>
                    <h2 class="hd-section-title">Aftercare</h2>
                </div>
                <div class="hd-section-body">
                    <p>Aftercare instructions we provide are general guidance based on standard practice. They are not medical advice.</p>
                    <p>Healing outcomes vary by individual, placement, and adherence to aftercare. If you experience signs of infection or an adverse reaction, seek medical attention.</p>
                    <p>We are not responsible for outcomes arising from failure to follow aftercare guidance.</p>
                </div>
            </section>

            <!-- Section 11 -->
            <section id="section-11" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">11</span>
                    <h2 class="hd-section-title">Third-Party Links and Services</h2>
                </div>
                <div class="hd-section-body">
                    <p>This website may link to third-party websites or use third-party services. We do not control those sites or services and are not responsible for their content, privacy practices, or availability.</p>
                </div>
            </section>

            <!-- Section 12 -->
            <section id="section-12" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">12</span>
                    <h2 class="hd-section-title">No Warranties</h2>
                </div>
                <div class="hd-section-body">
                    <p>This website is provided on an "as is" basis. We make no warranty that it will be available, uninterrupted, or error-free. Nothing on this website constitutes professional advice for your specific situation.</p>
                </div>
            </section>

            <!-- Section 13 -->
            <section id="section-13" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">13</span>
                    <h2 class="hd-section-title">Limitation of Liability</h2>
                </div>
                <div class="hd-section-body">
                    <p>To the fullest extent permitted by law, Panda Tattoo LLC is not liable for indirect, incidental, or consequential damages arising from your use of this website. This section applies to use of the website and does not limit our obligations under any signed service agreement.</p>
                </div>
            </section>

            <!-- Section 14 -->
            <section id="section-14" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">14</span>
                    <h2 class="hd-section-title">Indemnification</h2>
                </div>
                <div class="hd-section-body">
                    <p>You agree to hold Panda Tattoo LLC harmless from claims arising out of your misuse of this website or violation of these Terms.</p>
                </div>
            </section>

            <!-- Section 15 -->
            <section id="section-15" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">15</span>
                    <h2 class="hd-section-title">Governing Law</h2>
                </div>
                <div class="hd-section-body">
                    <p>These Terms are governed by the laws of the State of Florida, without regard to conflict of law principles. Any dispute will be brought in the state or federal courts located in Miami-Dade County, Florida.</p>
                </div>
            </section>

            <!-- Section 16 -->
            <section id="section-16" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">16</span>
                    <h2 class="hd-section-title">Changes to These Terms</h2>
                </div>
                <div class="hd-section-body">
                    <p>We may update these Terms from time to time. Changes are effective when posted with an updated date, and continued use of the website constitutes acceptance.</p>
                </div>
            </section>

            <!-- Section 17 -->
            <section id="section-17" class="hd-legal-section">
                <div class="hd-section-header">
                    <span class="hd-section-badge">17</span>
                    <h2 class="hd-section-title">Contact</h2>
                </div>
                <div class="hd-section-body">
                    <p>Questions about these Terms can be sent through our contact page at <a href="<?= route('/contact/') ?>" target="_blank" rel="noopener noreferrer">https://pandatattoo.com/contact</a>.</p>
                    
                    <div style="margin-top: 20px; padding-top: 18px; border-top: 1px solid rgba(255,255,255,0.1);">
                        <p style="margin: 0; font-weight: 700; color: #ffffff; text-transform: uppercase; letter-spacing: 0.05em;">Panda Tattoo LLC</p>
                        <p style="margin: 6px 0 0 0; color: rgba(255,255,255,0.8); line-height: 1.6;">
                            <a href="https://maps.google.com/?q=254+NW+36th+St,+Miami,+FL+33127" target="_blank" rel="noopener noreferrer" style="color: rgba(255,255,255,0.85) !important;">254 NW 36th Street, Miami, FL 33127</a><br>
                            Direct Phone: <a href="tel:7869199998" style="color: #ffffff !important;">(786) 919-9998</a><br>
                            Hours: Mon &ndash; Sun 11:00 AM &ndash; 9:00 PM (Open Daily)
                        </p>
                    </div>
                </div>
            </section>

        </main>

        <!-- Studio Contact Card at Bottom (Div instead of footer tag to prevent global theme CSS interference) -->
        <div class="hd-legal-contact-card">
            <img src="<?= base_url() ?>/assets/img/panda-logotype-bone-scaled.png" alt="Panda Tattoo Miami" class="hd-legal-contact-logo" loading="lazy">
            <div class="hd-legal-contact-address">
                <strong>Panda Tattoo Studio</strong>
                <a href="https://maps.google.com/?q=254+NW+36th+St,+Miami,+FL+33127" target="_blank" rel="noopener noreferrer" style="color: rgba(255, 255, 255, 0.85) !important; text-decoration: none !important;">
                    <span>254 NW 36th St, Miami, FL 33127 &bull; Wynwood / Midtown Arts District</span>
                </a>
                <div class="hd-contact-info-pills">
                    <a href="tel:7869199998" class="hd-contact-info-pill">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        <span>(786) 919-9998</span>
                    </a>
                    <span class="hd-contact-info-pill">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>Mon &ndash; Sun: 11:00 AM &ndash; 9:00 PM</span>
                    </span>
                </div>
            </div>
            <div class="hd-contact-btn-group" x-data>
                <a href="<?= route('/contact/') ?>" class="hd-contact-action-btn" aria-label="Contact Studio">
                    <span>Contact Studio</span>
                </a>
                <button type="button" @click="$dispatch('open-booking-modal')" onclick="window.dispatchEvent(new CustomEvent('open-booking-modal'))" class="hd-contact-action-btn hd-btn-secondary ghl-booking-btn" aria-label="Book Tattoo Consultation">
                    <span>Book Consultation</span>
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Floating Back to Top Button -->
<button type="button" class="hd-back-top" id="hdBackToTop" aria-label="Back to Top">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;display:block;margin:auto;stroke:#ffffff;fill:none;"><polyline points="18 15 12 9 6 15" style="stroke:#ffffff;fill:none;stroke-width:2.5px;"></polyline></svg>
</button>

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
    document.addEventListener('DOMContentLoaded', function () {
        // Smooth Anchor Click Navigation
        document.querySelectorAll('.hd-toc-link').forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId && targetId.startsWith('#')) {
                    const targetEl = document.querySelector(targetId);
                    if (targetEl) {
                        e.preventDefault();
                        const headerOffset = 90;
                        const elementPosition = targetEl.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });

        // Back to Top button logic
        const backToTopBtn = document.getElementById('hdBackToTop');
        if (backToTopBtn) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 400) {
                    backToTopBtn.classList.add('is-visible');
                } else {
                    backToTopBtn.classList.remove('is-visible');
                }
            }, { passive: true });

            backToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    });
</script>
