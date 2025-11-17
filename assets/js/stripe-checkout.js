(function($) {
    'use strict';
    
    $(document).ready(function() {
        $('#stripe-checkout-btn').on('click', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const originalText = $button.text();
            $button.prop('disabled', true).text('Loading...');
            
            $.ajax({
                url: boochatConnectStripe.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'boochat_connect_create_stripe_session',
                    nonce: boochatConnectStripe.nonce
                },
                success: function(response) {
                    if (response.success && response.data.checkout_url) {
                        // Redirect to Stripe Checkout
                        window.location.href = response.data.checkout_url;
                    } else {
                        alert(response.data.message || 'Failed to create checkout session. Please try again.');
                        $button.prop('disabled', false).text(originalText);
                    }
                },
                error: function() {
                    alert('Error connecting to server. Please try again.');
                    $button.prop('disabled', false).text(originalText);
                }
            });
        });
    });
    
})(jQuery);

