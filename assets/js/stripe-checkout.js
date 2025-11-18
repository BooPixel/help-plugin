(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Check if configuration is available
        if (typeof boochatConnectStripe === 'undefined') {
            console.error('BooChat Connect Stripe configuration not found');
            $('#stripe-checkout-btn').prop('disabled', true).text('Configuration Error');
            return;
        }
        
        $('#stripe-checkout-btn').on('click', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const originalText = $button.text();
            const loadingText = (boochatConnectStripe.loading) ? boochatConnectStripe.loading : 'Loading...';
            $button.prop('disabled', true).text(loadingText);
            
            $.ajax({
                url: boochatConnectStripe.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'boochat_connect_create_stripe_session',
                    nonce: boochatConnectStripe.nonce
                },
                dataType: 'json',
                success: function(response) {
                    if (response && response.success && response.data && response.data.checkout_url) {
                        // Redirect to Stripe Checkout
                        window.location.href = response.data.checkout_url;
                    } else {
                        const errorMsg = (response && response.data && response.data.message) 
                            ? response.data.message 
                            : (boochatConnectStripe.failedCheckout ? boochatConnectStripe.failedCheckout : 'Failed to create checkout session. Please try again.');
                        alert(errorMsg);
                        $button.prop('disabled', false).text(originalText);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response:', xhr.responseText);
                    let errorMsg = (boochatConnectStripe.errorConnecting) ? boochatConnectStripe.errorConnecting : 'Error connecting to server. Please try again.';
                    
                    if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.data && response.data.message) {
                                errorMsg = response.data.message;
                            }
                        } catch (e) {
                            // Not JSON, use default message
                        }
                    }
                    
                    alert(errorMsg);
                    $button.prop('disabled', false).text(originalText);
                }
            });
        });
    });
    
})(jQuery);

