/**
 * Scripts for BooPixel AI Chat Connect for n8n admin page
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Action button
        $('#boochat-connect-action-btn').on('click', function(e) {
            e.preventDefault();
            
            var $message = $('#boochat-connect-message');
            
            // Toggle message visibility
            $message.fadeIn();
            
            // Hide after 5 seconds
            setTimeout(function() {
                $message.fadeOut();
            }, 5000);
        });
        
        // Welcome banner dismiss button
        $('.boochat-connect-dismiss-button').on('click', function(e) {
            e.preventDefault();
            var $banner = $('.boochat-connect-welcome-banner');
            $banner.fadeOut(300, function() {
                $banner.remove();
            });
        });
        
    });
    
})(jQuery);
