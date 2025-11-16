/**
 * Scripts for Help Plugin admin page
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Action button
        $('#help-plugin-action-btn').on('click', function(e) {
            e.preventDefault();
            
            var $message = $('#help-plugin-message');
            
            // Toggle message visibility
            $message.fadeIn();
            
            // Hide after 5 seconds
            setTimeout(function() {
                $message.fadeOut();
            }, 5000);
        });
        
    });
    
})(jQuery);

