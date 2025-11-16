(function($) {
    'use strict';
    
    let chart = null;
    
    $(document).ready(function() {
        // Set default dates (last 30 days)
        const today = new Date();
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(today.getDate() - 30);
        
        $('#date-from').val(thirtyDaysAgo.toISOString().split('T')[0]);
        $('#date-to').val(today.toISOString().split('T')[0]);
        
        // Load statistics on page load
        loadStatistics();
        
        // Load statistics on button click
        $('#load-statistics').on('click', function() {
            loadStatistics();
        });
    });
    
    function loadStatistics() {
        const dateFrom = $('#date-from').val();
        const dateTo = $('#date-to').val();
        
        if (!dateFrom || !dateTo) {
            alert('Please select start and end dates.');
            return;
        }
        
        if (new Date(dateFrom) > new Date(dateTo)) {
            alert('Start date must be before end date.');
            return;
        }
        
        // Show loading
        $('#load-statistics').prop('disabled', true).text('Loading...');
        
        $.ajax({
            url: helpPluginStats.ajaxUrl,
            type: 'POST',
            data: {
                action: 'help_plugin_get_statistics',
                nonce: helpPluginStats.nonce,
                date_from: dateFrom,
                date_to: dateTo
            },
            success: function(response) {
                if (response.success) {
                    // Update quick summary
                    $('#stats-1day').text(response.data.summary['1day'] || 0);
                    $('#stats-7days').text(response.data.summary['7days'] || 0);
                    $('#stats-30days').text(response.data.summary['30days'] || 0);
                    
                    // Update chart
                    updateChart(response.data.chart);
                } else {
                    alert('Error loading statistics: ' + (response.data.message || 'Unknown error'));
                }
            },
            error: function() {
                alert('Error connecting to server. Please try again.');
            },
            complete: function() {
                $('#load-statistics').prop('disabled', false).text('Load Statistics');
            }
        });
    }
    
    function updateChart(chartData) {
        const ctx = document.getElementById('interactions-chart');
        
        if (!ctx) {
            return;
        }
        
        // Destroy previous chart if exists
        if (chart) {
            chart.destroy();
        }
        
        // Create new chart
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Interactions',
                    data: chartData.data,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    }
    
})(jQuery);

