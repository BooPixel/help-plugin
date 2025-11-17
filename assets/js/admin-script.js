/**
 * Scripts for BooChat Connect admin page
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
        
    });
    
})(jQuery);
