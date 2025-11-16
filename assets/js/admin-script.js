/**
 * Scripts para a página do admin do Help Plugin
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Botão de ação
        $('#help-plugin-action-btn').on('click', function(e) {
            e.preventDefault();
            
            var $message = $('#help-plugin-message');
            
            // Alternar visibilidade da mensagem
            $message.fadeIn();
            
            // Ocultar após 5 segundos
            setTimeout(function() {
                $message.fadeOut();
            }, 5000);
        });
        
    });
    
})(jQuery);

