/**
 * Admin customization page scripts
 *
 * @package BooChat_Connect
 */

(function($) {
    'use strict';
    
    var mediaUploader;
    
    // Upload icon button
    $('.boochat-connect-upload-icon-button').on('click', function(e) {
        e.preventDefault();
        
        // If the uploader object has already been created, reopen it
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        // Create the media uploader
        mediaUploader = wp.media({
            title: (typeof boochatConnectCustomization !== 'undefined' && boochatConnectCustomization.chooseIcon) ? boochatConnectCustomization.chooseIcon : 'Choose Chat Icon',
            button: {
                text: (typeof boochatConnectCustomization !== 'undefined' && boochatConnectCustomization.useIcon) ? boochatConnectCustomization.useIcon : 'Use this icon'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        // When an image is selected, run a callback
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            
            // Update the hidden input
            $('#chat_icon').val(attachment.url);
            
            // Update the preview
            $('.boochat-connect-icon-preview').html(
                '<img src="' + attachment.url + '" alt="Chat Icon" style="max-width: 48px; max-height: 48px; display: block; margin-bottom: 10px;">'
            );
            
            // Show remove button if not already visible
            if ($('.boochat-connect-remove-icon-button').length === 0) {
                var removeIconText = (typeof boochatConnectCustomization !== 'undefined' && boochatConnectCustomization.removeIcon) ? boochatConnectCustomization.removeIcon : 'Remove Icon';
                $('.boochat-connect-upload-icon-button').after(
                    '<button type="button" class="button boochat-connect-remove-icon-button" style="margin-left: 10px;">' + removeIconText + '</button>'
                );
            }
        });
        
        // Open the uploader
        mediaUploader.open();
    });
    
    // Remove icon button
    $(document).on('click', '.boochat-connect-remove-icon-button', function(e) {
        e.preventDefault();
        
        // Clear the hidden input
        $('#chat_icon').val('');
        
        // Update the preview to show default emoji
        $('.boochat-connect-icon-preview').html(
            '<span style="display: block; margin-bottom: 10px; font-size: 24px;">💬</span>'
        );
        
        // Remove the remove button
        $(this).remove();
    });
    
})(jQuery);

