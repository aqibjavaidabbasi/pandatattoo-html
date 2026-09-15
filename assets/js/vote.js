/**
 * Artist Voting System
 * Handles OTP-based voting for tattoo fight contests
 */

jQuery(document).ready(function($) {
    // Vote button click handler
    $(document).on('click', '.vote-button', function(e) {
        e.preventDefault();
        
        const $button = $(this);
        const artistId = $button.data('artist-id');
        const artistName = $button.data('artist-name');
        
        // Show vote modal/form
        showVoteModal(artistId, artistName);
    });
    
    // Send OTP
    $(document).on('submit', '#vote-form', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const email = $form.find('input[name="email"]').val();
        const phone = $form.find('input[name="phone"]').val();
        const artistId = $form.find('input[name="artist_id"]').val();
        
        $.ajax({
            url: vote_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'send_otp_vote',
                email: email,
                phone: phone,
                artist_id: artistId
            },
            beforeSend: function() {
                $form.find('.vote-submit').prop('disabled', true).text('Sending...');
            },
            success: function(response) {
                if (response.success) {
                    // Show OTP verification form
                    showOtpForm(email, artistId);
                } else {
                    alert(response.data.message || 'Error sending OTP');
                }
            },
            error: function() {
                alert('Error sending OTP. Please try again.');
            },
            complete: function() {
                $form.find('.vote-submit').prop('disabled', false).text('Send OTP');
            }
        });
    });
    
    // Verify OTP
    $(document).on('submit', '#otp-form', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const email = $form.find('input[name="email"]').val();
        const otp = $form.find('input[name="otp"]').val();
        const artistId = $form.find('input[name="artist_id"]').val();
        
        $.ajax({
            url: vote_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'verify_otp_vote',
                email: email,
                otp: otp,
                artist_id: artistId
            },
            beforeSend: function() {
                $form.find('.otp-submit').prop('disabled', true).text('Verifying...');
            },
            success: function(response) {
                if (response.success) {
                    alert('Vote recorded successfully!');
                    // Reload or update vote count
                    location.reload();
                } else {
                    alert(response.data.message || 'Invalid OTP');
                }
            },
            error: function() {
                alert('Error verifying OTP. Please try again.');
            },
            complete: function() {
                $form.find('.otp-submit').prop('disabled', false).text('Verify & Vote');
            }
        });
    });
    
    function showVoteModal(artistId, artistName) {
        // Implementation depends on your modal system
        console.log('Show vote modal for artist:', artistId, artistName);
    }
    
    function showOtpForm(email, artistId) {
        // Show OTP verification form
        console.log('Show OTP form for:', email, artistId);
    }
});
