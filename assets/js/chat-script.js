/**
 * Chat Widget Script
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Check if configuration is loaded
        if (typeof boopixelAiChatForN8nAjax === 'undefined') {
            console.error('Chat configuration not found');
            return;
        }
        
        const $popup = $('#boochat-connect-popup');
        const $chatWindow = $('#boochat-connect-chat-window');
        const $chatForm = $('#boochat-connect-chat-form');
        const $chatInput = $('#boochat-connect-chat-input');
        const $chatMessages = $('#boochat-connect-chat-messages');
        const $popupClose = $('.boochat-connect-popup-close');
        const $chatClose = $('.boochat-connect-chat-close');
        const $chatSend = $('.boochat-connect-chat-send');
        
        // Session ID storage key
        const SESSION_ID_KEY = 'boopixel_ai_chat_for_n8n_session_id';
        
        /**
         * Get or create session ID
         */
        function getSessionId() {
            let sessionId = localStorage.getItem(SESSION_ID_KEY);
            if (!sessionId) {
                sessionId = generateSessionId();
                localStorage.setItem(SESSION_ID_KEY, sessionId);
            }
            return sessionId;
        }
        
        /**
         * Generate session ID
         */
        function generateSessionId() {
            const array = new Uint8Array(16);
            crypto.getRandomValues(array);
            return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
        }
        
        // Show popup after page load (with delay for better UX)
        setTimeout(function() {
            $popup.removeClass('hidden');
        }, 1000);
        
        // Open chat when clicking popup
        $popup.on('click', function(e) {
            // Don't close if clicking the close button
            if ($(e.target).closest('.boochat-connect-popup-close').length) {
                return;
            }
            
            e.preventDefault();
            openChat();
        });
        
        // Close popup button
        $popupClose.on('click', function(e) {
            e.stopPropagation();
            closePopup();
        });
        
        // Close chat button
        $chatClose.on('click', function(e) {
            e.preventDefault();
            closeChat();
        });
        
        // Chat form submit
        $chatForm.on('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            sendMessage();
            return false;
        });
        
        // Enter key to send message
        $chatInput.on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                sendMessage();
            }
        });
        
        // Button click as alternative
        $chatSend.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $chatForm.trigger('submit');
            return false;
        });
        
        /**
         * Open chat window
         */
        function openChat() {
            $chatWindow.addClass('open');
            $popup.addClass('hidden');
            $chatInput.focus();
        }
        
        /**
         * Close chat window
         */
        function closeChat() {
            $chatWindow.removeClass('open');
            
            // Show popup again after a delay
            setTimeout(function() {
                $popup.removeClass('hidden');
            }, 300);
        }
        
        /**
         * Close popup
         */
        function closePopup() {
            $popup.addClass('hidden');
        }
        
        /**
         * Send message
         */
        function sendMessage() {
            const message = $chatInput.val().trim();
            
            if (!message) {
                return;
            }
            
            // Store user message
            const userMessage = message;
            
            // Disable form while sending
            setFormDisabled(true);
            
            // Clear input immediately
            $chatInput.val('');
            
            // Add user message immediately
            addMessage(userMessage, 'user');
            
            // Add loading indicator
            const loadingId = 'loading-' + Date.now();
            if (typeof boopixelAiChatForN8nAjax !== 'undefined') {
                addMessage(boopixelAiChatForN8nAjax.loadingText, 'system', loadingId);
            } else {
                addMessage('Aguardando resposta...', 'system', loadingId);
            }
            
            // Get session ID
            const sessionId = getSessionId();
            
            // Check if AJAX object is defined
            if (typeof boopixelAiChatForN8nAjax === 'undefined') {
                $('#' + loadingId).remove();
                addMessage('Erro: Configuração do plugin não encontrada. Recarregue a página.', 'system');
                setFormDisabled(false);
                return;
            }
            
            // Send AJAX request to WordPress, which will call the API
            $.ajax({
                url: boopixelAiChatForN8nAjax.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'boopixel_ai_chat_for_n8n_send_message',
                    nonce: boopixelAiChatForN8nAjax.nonce,
                    sessionId: sessionId,
                    chatInput: userMessage
                },
                dataType: 'json',
                success: function(response) {
                    // Remove loading message
                    $('#' + loadingId).remove();
                    
                    if (response.success && response.data) {
                        // Update session ID if provided
                        if (response.data.sessionId) {
                            localStorage.setItem(SESSION_ID_KEY, response.data.sessionId);
                        }
                        
                        // Add API response (output from the API)
                        addMessage(response.data.message, 'admin');
                    } else {
                        // Show error message
                        const errorMsg = response.data && response.data.message 
                            ? response.data.message 
                            : (boopixelAiChatForN8nAjax.errorText || 'Erro ao enviar mensagem');
                        addMessage(errorMsg, 'system');
                    }
                },
                error: function(xhr, status, error) {
                    // Remove loading message
                    $('#' + loadingId).remove();
                    
                    // Show error message
                    let errorMsg = 'Error sending message. Please try again.';
                    
                    // Try to get error message from configuration
                    if (typeof boopixelAiChatForN8nAjax !== 'undefined' && boopixelAiChatForN8nAjax.errorText) {
                        errorMsg = boopixelAiChatForN8nAjax.errorText;
                    }
                    
                    // Try to parse error from response
                    if (xhr.responseText) {
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.data && errorResponse.data.message) {
                                errorMsg = errorResponse.data.message;
                            } else if (errorResponse.message) {
                                errorMsg = errorResponse.message;
                            }
                        } catch (e) {
                            // If response is not JSON, check status
                            if (xhr.status === 0) {
                                errorMsg = 'Network error. Please check your connection.';
                            } else if (xhr.status === 403) {
                                errorMsg = 'Security check failed. Please refresh the page.';
                            } else if (xhr.status === 500) {
                                errorMsg = 'Server error. Please try again later.';
                            }
                        }
                    } else if (xhr.status === 0) {
                        errorMsg = 'Network error. Please check your connection.';
                    }
                    
                    addMessage(errorMsg, 'system');
                },
                complete: function() {
                    // Re-enable form
                    setFormDisabled(false);
                    $chatInput.focus();
                }
            });
        }
        
        /**
         * Enable/disable chat form
         */
        function setFormDisabled(disabled) {
            $chatInput.prop('disabled', disabled);
            $chatSend.prop('disabled', disabled);
            
            if (disabled) {
                $chatSend.addClass('disabled');
            } else {
                $chatSend.removeClass('disabled');
            }
        }
        
        /**
         * Add message to chat
         */
        function addMessage(text, type, id) {
            const messageClass = 'boochat-connect-chat-message-' + type;
            const idAttr = id ? ' id="' + escapeHtml(id) + '"' : '';
            const formattedText = formatMessage(text);
            const messageHTML = '<div class="boochat-connect-chat-message ' + messageClass + '"' + idAttr + '><p>' + formattedText + '</p></div>';
            $chatMessages.append(messageHTML);
            scrollToBottom();
        }
        
        /**
         * Format message (convert \n to <br>, preserve markdown-like formatting)
         */
        function formatMessage(text) {
            if (!text) return '';
            
            // Escape HTML first
            let formatted = escapeHtml(text);
            
            // Convert \n to <br>
            formatted = formatted.replace(/\n/g, '<br>');
            
            // Convert **bold** to <strong>
            formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            
            // Convert *italic* to <em>
            formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
            
            // Convert bullet points (* item) to list items
            formatted = formatted.replace(/^\* (.+?)$/gm, '<span class="boochat-connect-bullet">• $1</span>');
            
            return formatted;
        }
        
        /**
         * Scroll to bottom of chat
         */
        function scrollToBottom() {
            const $chatBody = $('.boochat-connect-chat-body');
            $chatBody.scrollTop($chatBody[0].scrollHeight);
        }
        
        /**
         * Escape HTML to prevent XSS
         */
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Close chat when clicking outside (optional)
        $(document).on('click', function(e) {
            if ($chatWindow.hasClass('open') && 
                !$(e.target).closest('#boochat-connect-chat-window').length &&
                !$(e.target).closest('#boochat-connect-popup').length) {
                // Uncomment below if you want chat to close when clicking outside
                // closeChat();
            }
        });
    });
    
})(jQuery);
