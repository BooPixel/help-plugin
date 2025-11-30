/**
 * Sessions page JavaScript
 *
 * @package BooPixel_AI_Chat_For_N8n
 */

(function($) {
    'use strict';
    
    let currentPage = 1;
    let perPage = 20;
    
    $(document).ready(function() {
        loadSessions();
        
        // Refresh button
        $('#refresh-sessions').on('click', function() {
            currentPage = 1;
            loadSessions();
        });
        
        // Per page change
        $('#sessions-per-page').on('change', function() {
            perPage = parseInt($(this).val(), 10);
            currentPage = 1;
            loadSessions();
        });
    });
    
    function loadSessions() {
        const container = $('#sessions-container');
        container.html('<p style="text-align: center; color: #646970; padding: 20px;"><span class="spinner is-active" style="float: none; margin: 0 10px 0 0;"></span>' + boopixelAiChatForN8nSessions.loadingText + '</p>');
        
        $.ajax({
            url: boopixelAiChatForN8nSessions.ajax_url,
            type: 'POST',
            data: {
                action: 'boochat_connect_get_sessions',
                nonce: boopixelAiChatForN8nSessions.nonce,
                page: currentPage,
                per_page: perPage
            },
            success: function(response) {
                if (response.success) {
                    renderSessions(response.data.sessions);
                    renderPagination(response.data);
                } else {
                    showError(response.data.message || boopixelAiChatForN8nSessions.errorLoadingText);
                }
            },
            error: function(xhr, status, error) {
                showError(boopixelAiChatForN8nSessions.errorLoadingText + error);
            }
        });
    }
    
    function renderSessions(sessions) {
        const container = $('#sessions-container');
        
        if (!sessions || sessions.length === 0) {
            container.html('<p style="text-align: center; color: #646970; padding: 20px;">' + boopixelAiChatForN8nSessions.noSessionsText + '</p>');
            return;
        }
        
        let html = '<table class="boochat-connect-sessions-table">';
        html += '<thead>';
        html += '<tr>';
        html += '<th>' + 'Session ID' + '</th>';
        html += '<th>' + 'First Interaction' + '</th>';
        html += '<th>' + 'Last Interaction' + '</th>';
        html += '<th>' + 'Messages' + '</th>';
        html += '<th>' + 'User' + '</th>';
        html += '<th>' + 'Bot' + '</th>';
        html += '<th>' + 'Actions' + '</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';
        
        sessions.forEach(function(session) {
            const firstDate = new Date(session.first_interaction);
            const lastDate = new Date(session.last_interaction);
            
            html += '<tr>';
            html += '<td><span class="boochat-connect-session-id">' + escapeHtml(session.session_id.substring(0, 20)) + '...</span></td>';
            html += '<td><span class="boochat-connect-session-meta">' + formatDate(firstDate) + '</span></td>';
            html += '<td><span class="boochat-connect-session-meta">' + formatDate(lastDate) + '</span></td>';
            html += '<td>' + session.message_count + '</td>';
            html += '<td>' + session.user_messages + '</td>';
            html += '<td>' + session.robot_messages + '</td>';
            html += '<td>';
            html += '<div class="boochat-connect-dropdown">';
            html += '<button type="button" class="boochat-connect-dropdown-toggle" data-session-id="' + escapeHtml(session.session_id) + '">';
            html += '<span class="boochat-connect-dropdown-icon">⋯</span>';
            html += '</button>';
            html += '<div class="boochat-connect-dropdown-menu">';
            html += '<a href="#" class="boochat-connect-view-session" data-session-id="' + escapeHtml(session.session_id) + '">' + 'View' + '</a>';
            html += '<a href="#" class="boochat-connect-export-json" data-session-id="' + escapeHtml(session.session_id) + '">' + (boopixelAiChatForN8nSessions.exportJsonText || 'Export JSON') + '</a>';
            html += '<a href="#" class="boochat-connect-export-csv" data-session-id="' + escapeHtml(session.session_id) + '">' + (boopixelAiChatForN8nSessions.exportCsvText || 'Export CSV') + '</a>';
            html += '</div>';
            html += '</div>';
            html += '</td>';
            html += '</tr>';
        });
        
        html += '</tbody>';
        html += '</table>';
        
        container.html(html);
        
        // Add click handler for dropdown toggle
        $('.boochat-connect-dropdown-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const dropdown = $(this).closest('.boochat-connect-dropdown');
            const menu = dropdown.find('.boochat-connect-dropdown-menu');
            
            // Close other dropdowns
            $('.boochat-connect-dropdown').not(dropdown).removeClass('active');
            
            // Check if dropdown should appear below instead of above
            const toggleButton = $(this);
            const buttonOffset = toggleButton.offset();
            const buttonHeight = toggleButton.outerHeight();
            const menuHeight = menu.outerHeight();
            const windowScrollTop = $(window).scrollTop();
            const spaceAbove = buttonOffset.top - windowScrollTop;
            
            // If not enough space above, show below
            if (spaceAbove < menuHeight + 10) {
                menu.addClass('dropdown-below');
            } else {
                menu.removeClass('dropdown-below');
            }
            
            // Toggle current dropdown
            dropdown.toggleClass('active');
        });
        
        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.boochat-connect-dropdown').length) {
                $('.boochat-connect-dropdown').removeClass('active');
            }
        });
        
        // Add click handler for view session
        $('.boochat-connect-view-session').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const sessionId = $(this).data('session-id');
            $('.boochat-connect-dropdown').removeClass('active');
            viewSession(sessionId);
        });
        
        // Add click handler for export JSON
        $('.boochat-connect-export-json').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const sessionId = $(this).data('session-id');
            $('.boochat-connect-dropdown').removeClass('active');
            exportSession(sessionId, 'json');
        });
        
        // Add click handler for export CSV
        $('.boochat-connect-export-csv').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const sessionId = $(this).data('session-id');
            $('.boochat-connect-dropdown').removeClass('active');
            exportSession(sessionId, 'csv');
        });
    }
    
    function renderPagination(data) {
        const pagination = $('#sessions-pagination');
        
        if (data.total_pages <= 1) {
            pagination.hide();
            return;
        }
        
        pagination.show();
        
        let html = '<div class="tablenav-pages">';
        html += '<span class="displaying-num">' + data.total + ' ' + 'items' + '</span>';
        html += '<span class="pagination-links">';
        
        // Previous button
        if (data.page > 1) {
            html += '<a class="button" href="#" data-page="' + (data.page - 1) + '">‹</a>';
        } else {
            html += '<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>';
        }
        
        html += '<span class="paging-input">';
        html += '<span class="tablenav-paging-text">';
        html += data.page + ' ' + 'of' + ' <span class="total-pages">' + data.total_pages + '</span>';
        html += '</span>';
        html += '</span>';
        
        // Next button
        if (data.page < data.total_pages) {
            html += '<a class="button" href="#" data-page="' + (data.page + 1) + '">›</a>';
        } else {
            html += '<span class="tablenav-pages-navspan" aria-hidden="true">›</span>';
        }
        
        html += '</span>';
        html += '</div>';
        
        pagination.html(html);
        
        // Add click handlers
        pagination.find('a[data-page]').on('click', function(e) {
            e.preventDefault();
            currentPage = parseInt($(this).data('page'), 10);
            loadSessions();
        });
    }
    
    function viewSession(sessionId) {
        // Show modal with loading state
        showModal(sessionId);
        
        // Fetch session messages via AJAX
        $.ajax({
            url: boopixelAiChatForN8nSessions.ajax_url,
            type: 'POST',
            data: {
                action: 'boochat_connect_get_session_details',
                nonce: boopixelAiChatForN8nSessions.nonce,
                session_id: sessionId
            },
            success: function(response) {
                if (response.success) {
                    renderMessagesInModal(response.data.messages, response.data.session_id);
                } else {
                    showModalError(response.data.message || 'Error loading session details.');
                }
            },
            error: function(xhr, status, error) {
                showModalError('Error loading session details: ' + error);
            }
        });
    }
    
    function showModal(sessionId) {
        // Create modal if it doesn't exist
        if ($('#boochat-connect-session-modal').length === 0) {
            $('body').append(createModalHTML());
        }
        
        const modal = $('#boochat-connect-session-modal');
        const modalContent = modal.find('.boochat-connect-modal-content');
        
        // Set session ID in header title
        modal.find('.boochat-connect-modal-session-id').text(sessionId);
        modal.find('.boochat-connect-modal-session-id').attr('title', sessionId);
        
        // Show loading state
        modalContent.html('<div style="text-align: center; padding: 40px;"><span class="spinner is-active" style="float: none; margin: 0 10px 0 0;"></span>Loading messages...</div>');
        
        // Show modal
        modal.fadeIn(200);
    }
    
    function createModalHTML() {
        return '<div id="boochat-connect-session-modal" class="boochat-connect-modal" style="display: none;">' +
            '<div class="boochat-connect-modal-overlay"></div>' +
            '<div class="boochat-connect-modal-container">' +
            '<div class="boochat-connect-modal-header">' +
            '<h2>Session Messages: <span class="boochat-connect-modal-session-id"></span></h2>' +
            '<button type="button" class="boochat-connect-modal-close" aria-label="Close">&times;</button>' +
            '</div>' +
            '<div class="boochat-connect-modal-content"></div>' +
            '</div>' +
            '</div>';
    }
    
    function renderMessagesInModal(messages, sessionId) {
        const modalContent = $('#boochat-connect-session-modal .boochat-connect-modal-content');
        
        if (!messages || messages.length === 0) {
            modalContent.html('<div style="text-align: center; padding: 40px; color: #646970;">No messages found for this session.</div>');
            return;
        }
        
        let html = '<div class="boochat-connect-messages-container">';
        
        messages.forEach(function(message) {
            const messageDate = new Date(message.interaction_date);
            const isUser = message.message_type === 'user';
            const messageClass = isUser ? 'boochat-connect-message-user' : 'boochat-connect-message-robot';
            
            html += '<div class="boochat-connect-message ' + messageClass + '">';
            html += '<div class="boochat-connect-message-header">';
            html += '<span class="boochat-connect-message-type">' + (isUser ? 'User' : 'Bot') + '</span>';
            html += '<span class="boochat-connect-message-date">' + formatDate(messageDate) + '</span>';
            html += '</div>';
            html += '<div class="boochat-connect-message-content">' + escapeHtml(message.message || '') + '</div>';
            html += '</div>';
        });
        
        html += '</div>';
        
        modalContent.html(html);
    }
    
    function showModalError(message) {
        const modalContent = $('#boochat-connect-session-modal .boochat-connect-modal-content');
        modalContent.html('<div class="notice notice-error"><p>' + escapeHtml(message) + '</p></div>');
    }
    
    // Close modal handlers
    $(document).on('click', '.boochat-connect-modal-close, .boochat-connect-modal-overlay', function() {
        $('#boochat-connect-session-modal').hide();
    });
    
    // Close modal on ESC key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#boochat-connect-session-modal').is(':visible')) {
            $('#boochat-connect-session-modal').hide();
    }
    });
    
    function showError(message) {
        const container = $('#sessions-container');
        container.html('<div class="notice notice-error"><p>' + escapeHtml(message) + '</p></div>');
    }
    
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
    }
    
    function exportSession(sessionId, format) {
        // Fetch session messages via AJAX
        $.ajax({
            url: boopixelAiChatForN8nSessions.ajax_url,
            type: 'POST',
            data: {
                action: 'boochat_connect_export_session',
                nonce: boopixelAiChatForN8nSessions.nonce,
                session_id: sessionId,
                format: format
            },
            success: function(response) {
                if (response.success) {
                    downloadFile(response.data.content, response.data.filename, response.data.mime_type);
                } else {
                    alert(response.data.message || 'Error exporting session.');
                }
            },
            error: function(xhr, status, error) {
                alert('Error exporting session: ' + error);
            }
        });
    }
    
    function downloadFile(content, filename, mimeType) {
        // Create a blob with the content
        const blob = new Blob([content], { type: mimeType });
        
        // Create a temporary link element
        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = filename;
        
        // Append to body, click, and remove
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Clean up the URL object
        window.URL.revokeObjectURL(link.href);
    }
    
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
})(jQuery);

