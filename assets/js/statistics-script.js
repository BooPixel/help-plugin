(function($) {
    'use strict';
    
    let chart = null;
    
    function initStatistics() {
        if (typeof boochatConnectStats === 'undefined') {
            console.error('Statistics configuration not found');
            return;
        }
        
        // Wait for DOM to be ready
        if (!$('#date-from').length || !$('#date-to').length) {
            setTimeout(initStatistics, 100);
            return;
        }
        
        // Set default dates to today
        const today = new Date().toISOString().split('T')[0];
        const dateFrom = $('#date-from').val();
        const dateTo = $('#date-to').val();
        
        // Only set if not already set
        if (!dateFrom) {
            $('#date-from').val(today);
        }
        if (!dateTo) {
            $('#date-to').val(today);
        }
        
        // Load statistics on page load (only if dates are set)
        const finalDateFrom = $('#date-from').val();
        const finalDateTo = $('#date-to').val();
        if (finalDateFrom && finalDateTo) {
            loadStatistics();
        }
        
        // Load statistics on button click
        $('#load-statistics').on('click', function(e) {
            e.preventDefault();
            loadStatistics();
        });
    }
    
    function showMessage(message, type) {
        type = type || 'error';
        const messageClass = type === 'error' ? 'notice-error' : 'notice-success';
        const $message = $('<div>')
            .addClass('notice ' + messageClass + ' is-dismissible')
            .css({
                margin: '10px 0',
                padding: '10px 15px'
            })
            .html('<p>' + message + '</p>');
        
        // Remove existing messages
        $('.boochat-connect-statistics-message').remove();
        
        // Insert message before the date filters
        $('.boochat-connect-statistics-filters').before($message.addClass('boochat-connect-statistics-message'));
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $message.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    function loadStatistics() {
        if (typeof boochatConnectStats === 'undefined') {
            showMessage('Error: Statistics configuration not loaded. Please refresh the page.', 'error');
            return;
        }
        
        const dateFrom = $('#date-from').val();
        const dateTo = $('#date-to').val();
        
        if (!dateFrom || !dateTo) {
            showMessage(boochatConnectStats.selectDatesText || 'Please select start and end dates.', 'error');
            return;
        }
        
        if (new Date(dateFrom) > new Date(dateTo)) {
            showMessage(boochatConnectStats.invalidDateRangeText || 'Start date must be before end date.', 'error');
            return;
        }
        
        const $button = $('#load-statistics');
        $button.prop('disabled', true).text(boochatConnectStats.loadingText || 'Loading...');
        
        $.ajax({
            url: boochatConnectStats.ajaxUrl,
            type: 'POST',
            data: {
                action: 'boochat_connect_get_statistics',
                nonce: boochatConnectStats.nonce,
                date_from: dateFrom,
                date_to: dateTo
            },
            success: function(response) {
                if (response.success) {
                    // Update summary
                    $('#stats-1day').text(response.data.summary['1day'] || 0);
                    $('#stats-7days').text(response.data.summary['7days'] || 0);
                    $('#stats-30days').text(response.data.summary['30days'] || 0);
                    
                    // Update chart
                    if (response.data.chart && response.data.chart.labels) {
                        updateChart(response.data.chart);
                    }
                    
                    // Update calendar
                    if (response.data.calendar) {
                        updateCalendar(response.data.calendar);
                    }
                    
                    // Show success message if data loaded
                    if (response.data.summary || response.data.chart) {
                        showMessage('Statistics loaded successfully.', 'success');
                    }
                } else {
                    let errorMessage = boochatConnectStats.errorLoadingText + (response.data.message || '');
                    showMessage(errorMessage, 'error');
                }
            },
            error: function(xhr, status, error) {
                showMessage(boochatConnectStats.errorConnectingText || 'Error connecting to server. Please try again.', 'error');
            },
            complete: function() {
                $button.prop('disabled', false).text(boochatConnectStats.loadStatisticsText);
            }
        });
    }
    
    function updateChart(chartData) {
        const ctx = document.getElementById('interactions-chart');
        if (!ctx || typeof Chart === 'undefined') {
            return;
        }
        
        if (chart) {
            chart.destroy();
        }
        
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: 'Interactions',
                    data: chartData.data || [],
                    borderColor: '#2271b1',
                    backgroundColor: 'rgba(34, 113, 177, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    
    function updateCalendar(calendarData) {
        const container = document.getElementById('calendar-container');
        if (!container) {
            return;
        }
        
        container.innerHTML = '';
        
        if (!calendarData || Object.keys(calendarData).length === 0) {
            container.innerHTML = '<p style="text-align: center; color: #646970; padding: 20px;">No interaction data available</p>';
            return;
        }
        
        const maxCount = Math.max(...Object.values(calendarData), 1);
        const today = new Date();
        const days = [];
        
        // Generate last 365 days
        for (let i = 0; i < 365; i++) {
            const date = new Date(today);
            date.setDate(date.getDate() - i);
            const dateStr = date.toISOString().split('T')[0];
            days.push({
                date: date,
                dateStr: dateStr,
                count: calendarData[dateStr] || 0
            });
        }
        
        // Group into weeks
        const weeks = [];
        let currentWeek = [];
        
        days.forEach((day, index) => {
            const dayOfWeek = day.date.getDay();
            
            if (dayOfWeek === 0 && currentWeek.length > 0) {
                while (currentWeek.length < 7) {
                    currentWeek.push(null);
                }
                weeks.push(currentWeek);
                currentWeek = [];
            }
            
            currentWeek.push(day);
            
            if (index === days.length - 1) {
                while (currentWeek.length < 7) {
                    currentWeek.push(null);
                }
                weeks.push(currentWeek);
            }
        });
        
        // Render calendar
        const calendarHTML = $('<div>').css({
            display: 'flex',
            gap: '3px',
            alignItems: 'flex-start'
        });
        
        weeks.forEach(function(week) {
            const weekColumn = $('<div>').css({
                display: 'flex',
                flexDirection: 'column',
                gap: '3px'
            });
            
            week.forEach(function(day) {
                const dayElement = $('<div>').css({
                    width: '12px',
                    height: '12px',
                    borderRadius: '2px',
                    border: '1px solid #ddd'
                });
                
                if (day === null) {
                    dayElement.css({
                        background: 'transparent',
                        border: 'none'
                    });
                } else {
                    const intensity = day.count / maxCount;
                    let color = '#ebedf0';
                    
                    if (day.count > 0) {
                        if (intensity <= 0.2) {
                            color = '#c6e48b';
                        } else if (intensity <= 0.4) {
                            color = '#7bc96f';
                        } else if (intensity <= 0.6) {
                            color = '#239a3b';
                        } else {
                            color = '#196127';
                        }
                    }
                    
                    dayElement.css('background', color);
                    dayElement.attr('title', day.dateStr + ': ' + day.count + ' interaction' + (day.count !== 1 ? 's' : ''));
                    dayElement.css('cursor', 'pointer');
                }
                
                weekColumn.append(dayElement);
            });
            
            calendarHTML.append(weekColumn);
        });
        
        container.appendChild(calendarHTML[0]);
    }
    
    // Wait for Chart.js and DOM to be ready
    function waitForChartAndInit() {
        if (typeof Chart === 'undefined') {
            setTimeout(waitForChartAndInit, 100);
            return;
        }
        
        if (typeof boochatConnectStats === 'undefined') {
            setTimeout(waitForChartAndInit, 100);
            return;
        }
        
        if (typeof jQuery === 'undefined') {
            setTimeout(waitForChartAndInit, 100);
            return;
        }
        
        $(document).ready(function() {
            // Small delay to ensure all DOM elements are ready
            setTimeout(function() {
                initStatistics();
            }, 100);
        });
    }
    
    waitForChartAndInit();
    
})(jQuery);
