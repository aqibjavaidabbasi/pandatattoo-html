<?php
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/db.php';

function tattoo_fight_artists(int $contest_id): array {
    // A dead database renders the empty state, as $wpdb did, rather than 500ing the page.
    // active_contest_id() caches its result for 5 minutes, so the id can outlive the
    // connection and reach this call with the database already gone.
    try {
        $stmt = db()->prepare(
            "SELECT id, artist_name, ig_handle, votes, artist_photo
             FROM voting_leaderboard
             WHERE contest_id = ? AND status = 'publish'
             ORDER BY created_at DESC, id DESC
             LIMIT 8"
        );
        $stmt->execute([$contest_id]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('tattoo_fight_artists: database unavailable - ' . $e->getMessage());
        return [];
    }
}

function tattoo_fight_media_url(?string $url): string {
    $url = trim((string) $url);
    if ($url === '') return '';

    $parts = parse_url($url);
    $host = strtolower((string) ($parts['host'] ?? ''));
    if (in_array($host, ['pandatattoo.com', 'www.pandatattoo.com', 'staging4.temp.tattoopanda.com', 'staging2.tattoopanda.com'], true)) {
        $filename = basename((string) ($parts['path'] ?? ''));
        if ($filename !== '') return url('/assets/img/' . $filename);
    }

    return $url;
}

function tattoo_fight_countdown(int $contest_id): array|string {
    $cache = __DIR__ . '/data/cache/active_contest_countdown.json';
    if (is_readable($cache) && filemtime($cache) + 60 > time()) {
        $cached = json_decode((string) file_get_contents($cache), true);
        if (is_array($cached) && (int) ($cached['contest_id'] ?? 0) === $contest_id) {
            return ['countdown' => (string) $cached['countdown'], 'progress' => (int) $cached['progress']];
        }
    }

    try {
        $stmt = db()->prepare('SELECT start_date, end_date FROM contests WHERE id = ? AND status = "publish" LIMIT 1');
        $stmt->execute([$contest_id]);
        $contest = $stmt->fetch();
    } catch (PDOException $e) {
        error_log('tattoo_fight_countdown: database unavailable - ' . $e->getMessage());
        $contest = false;
    }
    if (!$contest) return '<p>No active contests found.</p>';

    $current_date = time();
    $end_timestamp = strtotime((string) $contest['end_date']);
    $start_timestamp = strtotime((string) $contest['start_date']);
    if ($end_timestamp === false) return '<p>Error: Invalid end date format.</p>';

    $time_left = $end_timestamp - $current_date;
    if ($time_left <= 0) return '<p>Contest has ended.</p>';

    $days = floor($time_left / (60 * 60 * 24));
    $hours = floor(($time_left % (60 * 60 * 24)) / (60 * 60));
    $minutes = floor(($time_left % (60 * 60)) / 60);

    $countdown = '';
    if ($days > 0) {
        $countdown .= $days . 'd ';
    }
    if ($hours > 0 || $days > 0) {
        $countdown .= $hours . 'h ';
    }
    $countdown .= $minutes . 'm';

    $total_duration = $end_timestamp - (int) $start_timestamp;
    $elapsed_time = min($current_date, $end_timestamp) - (int) $start_timestamp;
    if ($total_duration <= 0) return '<p>Error: Invalid contest duration.</p>';

    $progress_percent = round(($elapsed_time / $total_duration) * 100);
    $progress_percent = max(0, min(100, $progress_percent));

    $result = ['contest_id' => $contest_id, 'countdown' => $countdown, 'progress' => $progress_percent];
    if (!is_dir(dirname($cache))) @mkdir(dirname($cache), 0775, true);
    @file_put_contents($cache, json_encode($result));

    return ['countdown' => $countdown, 'progress' => $progress_percent];
}

$active_contest_id = active_contest_id();
$artists = $active_contest_id ? tattoo_fight_artists($active_contest_id) : [];
$countdown = $active_contest_id ? tattoo_fight_countdown($active_contest_id) : null;

$PAGE = [
    'title'      => html_entity_decode('Tattoo Fight &#8211; Tatto Panda', ENT_QUOTES, 'UTF-8'),
    'body_class' => 'wp-singular page-template page-template-templates page-template-tattoo-fight page-template-templatestattoo-fight-php page page-id-397 wp-custom-logo wp-embed-responsive wp-theme-studio wp-child-theme-studio-child no-sidebar excerpt-image-top',
    'css'        => 'tattoo-fight.css',
];
include __DIR__ . '/inc/head.php';
include __DIR__ . '/inc/header.php';
?>
	

<div class="main_layout">
	<div class="tattoo">
		<div class="banner_wrapper">
			<div class="left_section">
			</div>
			<div class="right_section">
				<div class="ct_info">
					<div class="content_wrap">
												<div class="content fs_20">
							Tattoo Fight: Nominate & Vote Your Ink Champion						</div>
						
												<h3>Nominate the most badass tattoo artists and help them rise to the top.</h3>
																			<button class="button" onclick="window.open('#', '_blank')">
									<span class="button-content">Nominate a Tattoo Artist</span>
							</button>
											</div>
				</div>
			</div>

		</div>
		
		<section class="section_wrapper" id="nominate">
			<div class="left_section">
					<h2 class="">Nomination form </h2>
			</div>
			<div class="right_section">
				<div class="form_wrap">
					<div class="giveaway_form">
						
<div class="wpcf7 no-js" id="wpcf7-f427-o1" lang="en-US" dir="ltr" data-wpcf7-id="427">
<div class="screen-reader-response"><p role="status" aria-live="polite" aria-atomic="true"></p> <ul></ul></div>
<form action="<?= route('/tattoo-fight/#wpcf7-f427-o1') ?>" method="post" class="wpcf7-form init" aria-label="Contact form" enctype="multipart/form-data" novalidate="novalidate" data-status="init">
<fieldset class="hidden-fields-container"><input type="hidden" name="_wpcf7" value="427" /><input type="hidden" name="_wpcf7_version" value="6.1.7" /><input type="hidden" name="_wpcf7_locale" value="en_US" /><input type="hidden" name="_wpcf7_unit_tag" value="wpcf7-f427-o1" /><input type="hidden" name="_wpcf7_container_post" value="0" /><input type="hidden" name="_wpcf7_posted_data_hash" value="" />
</fieldset>
<div class="cf7_two_column">
	<p class="cf7_field"><br />
<span class="wpcf7-form-control-wrap" data-name="artist-name"><input size="40" maxlength="400" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false" placeholder="Artist Name" value="" type="text" name="artist-name" /></span>
	</p>
	<p class="cf7_field"><br />
<span class="wpcf7-form-control-wrap" data-name="ig-handle"><input size="40" maxlength="400" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false" placeholder="IG Handle" value="" type="text" name="ig-handle" /></span>
	</p>
	<p class="cf7_field"><br />
<span class="wpcf7-form-control-wrap" data-name="why-compete"><textarea cols="40" rows="10" maxlength="2000" class="wpcf7-form-control wpcf7-textarea wpcf7-validates-as-required" aria-required="true" aria-invalid="false" placeholder="Why should they compete?" name="why-compete"></textarea></span>
	</p>
	<p class="cf7_field"><br />
<span class="wpcf7-form-control-wrap" data-name="portfolio-link"><input size="40" maxlength="400" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false" placeholder="Portfolio Link" value="" type="text" name="portfolio-link" /></span>
	</p>
	<p class="cf7_field cf7_file_field"><br />
<label class="file-label">Upload Portfolio (JPG/PNG, max 5MB)</label><br />
<span class="wpcf7-form-control-wrap" data-name="artist-photo"><input size="40" class="wpcf7-form-control wpcf7-file wpcf7-validates-as-required modern-upload" accept=".jpg,.jpeg,.png" aria-required="true" aria-invalid="false" type="file" name="artist-photo" /></span>
	</p>
	<p class="cf7_submit"><br />
<input class="wpcf7-form-control wpcf7-submit has-spinner" type="submit" value="Nominate Now" />
	</p>
</div><div class="wpcf7-response-output" aria-hidden="true"></div>
</form>
</div>
					</div>
				</div>
			</div>

		</section>
		
			
	<section class="section_wrapper artists-grid">
	  <?php if ($active_contest_id): ?>
		<?php if ($artists): ?>
		  <?php foreach ($artists as $artist): ?>
			<div class="artist-card">
			  <?php if (!empty($artist['artist_photo'])): ?>
				<img src="<?= eu(tattoo_fight_media_url($artist['artist_photo'])) ?>" alt="<?= e($artist['artist_name']) ?>">
			  <?php else: ?>
				<img src="<?= base_url() ?>/assets/images/placeholder.jpg" alt="Artist">
			  <?php endif; ?>

			  <h3><?= e($artist['artist_name']) ?></h3>
			  <p><?= e($artist['ig_handle']) ?></p>
			  <p><b>Votes:</b> <?= e((string) $artist['votes']) ?></p>
				<button class="button" onclick="openVoteModal(<?= (int) $artist['id'] ?>);">
					<span class="button-content vote-now-btn">Vote Now</span>
				</button>
			</div>
		  <?php endforeach; ?>
		<?php else: ?>
		  <p>No artists found.</p>
		<?php endif; ?>
	  <?php else: ?>
			<div class="no-contest-message">
				<h2>No active giveaways right now</h2>
				<p>Follow us on Instagram for updates and future contests!</p>
			</div>
	  <?php endif; ?>
		</section>


		<section class="voting-status">
		  <div class="container">
			  <div class="box">
				<h2>Thanks for voting!</h2>
				<p>Share your support</p>
				<img src="https://static.vecteezy.com/system/resources/previews/036/594/092/non_2x/man-empty-avatar-photo-placeholder-for-social-networks-resumes-forums-and-dating-sites-male-and-female-no-photo-images-for-unfilled-user-profile-free-vector.jpg" alt="User" />
				<br>
				<button>Share</button>
			  </div>
			  <div class="right-section">
				<div class="arrow-box">You’re in 3<sup>rd</sup> place!</div>
				<div class="arrow-box">Just 5 votes behind 1st</div>
			  </div>
			</div>
		</section>
		

		<!-- SECTION 1: Countdown + Progress Bar -->
		<?php if ($active_contest_id && is_array($countdown)): ?>
		<section class="round-status">
			<div class="timer-box">
				<p class="timer-text">Round ends in : <strong><?= e($countdown['countdown']) ?></strong></p>
				<div class="progress-bar">
					<div class="progress-fill" style="width:<?= e((string) $countdown['progress']) ?>%!important ">
						<span class="progress-label"><?= e((string) $countdown['progress']) ?>%</span>
					</div>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<section class="past-battles">
    <h2>Watch Past Battles</h2>

            <div class="battle-videos">
                            <div class="video-card">
                                                                                </div>
                            <div class="video-card">
                                            <div class="tiktok-container">
                            <blockquote class="tiktok-embed" cite="https://www.tiktok.com/@crafterbhaiya/video/7531001225119157522" data-video-id="7531001225119157522" style="max-width: 605px;min-width: 325px"> <section> <a target="_blank" title="@crafterbhaiya" href="https://www.tiktok.com/@crafterbhaiya?refer=embed">@crafterbhaiya</a> Replying to @rohanrd123 To public ki demand py is diy mini robot kids toy ko krty hn colour ✌️🎨  . . <a title="diy" target="_blank" href="https://www.tiktok.com/tag/diy?refer=embed">#diy</a> <a title="colour" target="_blank" href="https://www.tiktok.com/tag/colour?refer=embed">#colour</a> <a title="fyp" target="_blank" href="https://www.tiktok.com/tag/fyp?refer=embed">#fyp</a> <a title="toy" target="_blank" href="https://www.tiktok.com/tag/toy?refer=embed">#toy</a> <a title="craft" target="_blank" href="https://www.tiktok.com/tag/craft?refer=embed">#craft</a> <a title="foryou" target="_blank" href="https://www.tiktok.com/tag/foryou?refer=embed">#foryou</a> <a title="wood" target="_blank" href="https://www.tiktok.com/tag/wood?refer=embed">#wood</a> <a title="kids" target="_blank" href="https://www.tiktok.com/tag/kids?refer=embed">#kids</a> <a title="crafter" target="_blank" href="https://www.tiktok.com/tag/crafter?refer=embed">#crafter</a> <a target="_blank" title="♬ original sound - Crafter Bhaiya" href="https://www.tiktok.com/music/original-sound-7531001257679719169?refer=embed">♬ original sound - Crafter Bhaiya</a> </section> </blockquote>                         </div>
                                    </div>
                    </Div>

        <!-- Load TikTok embed script (once) -->
        <script async src="https://www.tiktok.com/embed.js"></script>
    </section>


		
	</div>
	
	<!-- VOTE MODAL -->
	<div class="tattoo-vote-modal-overlay" id="voteModal">
		<div class="tattoo-vote-modal">
			<button type="button" class="tattoo-close-btn" onclick="closeVoteModal()">×</button>
			<h2>Cast Your Vote</h2>

			<form id="cast-vote">
				<input type="hidden" name="artist_id" id="voteArtistId">

				<div class="tattoo-vote-step-email">
					<input type="email" name="email" placeholder="Enter your email" required>
					<button type="submit" id="sendOtpBtn">Verify</button>
				</div>

				<div class="tattoo-vote-step-otp" style="display: none;">
					<input type="text" name="otp" placeholder="Enter OTP" maxlength="6">
					<button type="button" id="verifyOtpBtn">Submit Vote</button>
				</div>

				<div id="vote-response" class="tattoo-vote-response"></div>
			</form>

			<div class="tattoo-vote-thankyou" style="display: none;">
				<p>🎉 Thank you! Your vote has been recorded.</p>
				<button onclick="closeVoteModal()">Close</button>
			</div>
		</div>
	</div>

</div>


<script>
	const castVoteForm = document.getElementById('cast-vote');
	const sendOtpBtn = document.getElementById('sendOtpBtn');
	const verifyOtpBtn = document.getElementById('verifyOtpBtn');
	
	function openVoteModal(artistId) {
		document.body.classList.add('tattoo-body-lock'); // Prevent scroll
		document.getElementById('voteArtistId').value = artistId;
		document.getElementById('voteModal').style.display = 'flex';
	}

	function closeVoteModal() {
		document.getElementById('voteModal').style.display = 'none';
		document.body.classList.remove('tattoo-body-lock');
	}
	
	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
		  closeVoteModal();
		}
	  });
	
	castVoteForm.addEventListener('submit', function (e) {
		e.preventDefault();
		const email = this.email.value;
		const artistId = document.getElementById('voteArtistId').value;

		sendOtpBtn.disabled = true;
		fetch(vote_ajax.ajax_url, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams({
				action: 'send_otp_vote',
				email: email,
				artist_id: artistId
			})
		}).then(res => res.json())
		  .then(data => {
			  sendOtpBtn.disabled = false;
			  if (data.success) {
				  document.querySelector('.tattoo-vote-step-email').style.display = 'none';
				  document.querySelector('.tattoo-vote-step-otp').style.display = 'block';
				  showVoteResponse('OTP sent. Please check your email.', 'success');
			  } else {
				  showVoteResponse(data.data.message, 'error');
			  }
		  });
	});

	verifyOtpBtn.addEventListener('click', function () {
		const form = document.getElementById('cast-vote');
		const email = form.email.value;
		const otp = form.otp.value;
		const artistId = document.getElementById('voteArtistId').value;

		verifyOtpBtn.disabled = true;
		fetch(vote_ajax.ajax_url, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams({
				action: 'verify_otp_vote',
				email: email,
				otp: otp,
				artist_id: artistId
			})
		}).then(res => res.json())
		  .then(data => {
			  verifyOtpBtn.disabled = false;
			  if (data.success) {
				  form.style.display = 'none';
				  document.querySelector('.tattoo-vote-thankyou').style.display = 'block';
				  showVoteResponse('', ''); // Clear message
			  } else {
				  showVoteResponse(data.data.message, 'error');
			  }
		  });
	});

	function showVoteResponse(msg, type) {
		const resDiv = document.getElementById('vote-response');
		resDiv.textContent = msg;
		resDiv.className = 'tattoo-vote-response ' + type;
	}

	function closeVoteModal() {
		const modal = document.getElementById('voteModal');
		modal.style.display = 'none';

		// Reset modal fields
		document.querySelector('.tattoo-vote-step-email').style.display = 'block';
		document.querySelector('.tattoo-vote-step-otp').style.display = 'none';
		document.querySelector('.tattoo-vote-thankyou').style.display = 'none';

		const form = document.getElementById('cast-vote');
		form.reset();
		form.style.display = 'block';
		document.getElementById('vote-response').textContent = '';
		document.getElementById('voteArtistId').value = '';
	}
	// Example click trigger
// 	document.querySelectorAll('.vote-now-btn').forEach(btn => {
// 		btn.addEventListener('click', function () {
// 			const artistId = this.getAttribute('data-artist-id');
// 			openVoteModal(artistId);
// 		});
// 	});
jQuery(function($) {
	
	const fileInput = document.querySelector('.modern-upload');

	if (!fileInput) return;

	// Create a container for image preview
	const previewContainer = document.createElement('div');
	previewContainer.style.marginTop = '10px';
	fileInput.parentNode.appendChild(previewContainer);

	fileInput.addEventListener('change', function () {
		const file = this.files[0];
		previewContainer.innerHTML = ''; // Clear previous preview

		if (file && file.type.startsWith('image/')) {
			const reader = new FileReader();
			reader.onload = function (e) {
				const img = document.createElement('img');
				img.src = e.target.result;
				img.style.maxWidth = '50px';
				img.style.height = 'auto';
				img.style.border = '1px solid #ccc';
				img.style.borderRadius = '5px';
				previewContainer.appendChild(img);
			};
			reader.readAsDataURL(file);
		}
	});
	
	document.getElementsByTagName('body').style.overflow = 'scroll';
	const windowHeight = $(window).height();
	const $mainSlider = $('.tattoo'); // Adjusted selector for Tattoo page
	const $sections = $mainSlider.children('div, section'); // All direct sections
	let sectionCount = $sections.length;
	let currentY = 0;
	let targetY = 0;

	// Correct maxScroll using reduce instead of buggy outerHeight math
	let totalHeight = 0;
	$sections.each(function() {
		totalHeight += $(this).outerHeight(true);
	});
	let maxScroll = -(totalHeight - windowHeight);

	// Animate the scroll
	function animateScroll() {
		currentY += (targetY - currentY) * 0.08;
		currentY = Math.max(currentY, maxScroll); // Lower bound
		currentY = Math.min(currentY, 0);         // Upper bound (top)
		$mainSlider.css('transform', `translateY(${currentY}px)`);
		requestAnimationFrame(animateScroll);
	}

	animateScroll();

	// Scroll event
	window.addEventListener('wheel', function(e) {
		const delta = e.deltaY;
		targetY -= delta;
		targetY = Math.min(0, Math.max(targetY, maxScroll));
		e.preventDefault(); // Stop native scroll
	}, { passive: false });

	// Resize logic
	$(window).on('resize', function() {
		totalHeight = 0;
		$sections.each(function() {
			totalHeight += $(this).outerHeight(true);
		});
		maxScroll = -(totalHeight - $(window).height());
		if ($(window).height() !== windowHeight) {
			window.location.reload();
		}
	});
});



</script>

<script>
  const bullet = document.querySelector('.bullet');
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
</script>
<script id="wp-hooks-js" src="<?= asset('/assets/js/vendor/hooks.min.js') ?>"></script>
<script id="wp-i18n-js" src="<?= asset('/assets/js/vendor/i18n.min.js') ?>"></script>
<script id="wp-i18n-js-after">
wp.i18n.setLocaleData( { 'text direction\u0004ltr': [ 'ltr' ] } );
//# sourceURL=wp-i18n-js-after
</script>
<script id="swv-js" src="<?= asset('/assets/js/vendor/cf7/swv-index.js') ?>"></script>
<script id="contact-form-7-js-before">
var wpcf7 = {
    "api": {
        "root": "<?= url('/api/nominate.php/') ?>",
        "namespace": "contact-form-7/v1"
    },
    "cached": 1
};
//# sourceURL=contact-form-7-js-before
</script>
<script id="contact-form-7-js" src="<?= asset('/assets/js/vendor/cf7/index.js') ?>"></script>
<script>
var vote_ajax = {"ajax_url":<?= json_encode(url('/api/admin-ajax.php')) ?>};
</script>
<script src="<?= asset('/assets/js/vote.js') ?>"></script>
	    </div><!-- #content -->
		
<?php include __DIR__ . '/inc/footer.php'; ?>
