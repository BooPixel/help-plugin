(function($) {
    'use strict';
    
    let chart = null;
    
    $(document).ready(function() {
        // Definir datas padrão (últimos 30 dias)
        const today = new Date();
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(today.getDate() - 30);
        
        $('#date-from').val(thirtyDaysAgo.toISOString().split('T')[0]);
        $('#date-to').val(today.toISOString().split('T')[0]);
        
        // Carregar estatísticas ao carregar a página
        loadStatistics();
        
        // Carregar estatísticas ao clicar no botão
        $('#load-statistics').on('click', function() {
            loadStatistics();
        });
    });
    
    function loadStatistics() {
        const dateFrom = $('#date-from').val();
        const dateTo = $('#date-to').val();
        
        if (!dateFrom || !dateTo) {
            alert('Por favor, selecione as datas inicial e final.');
            return;
        }
        
        if (new Date(dateFrom) > new Date(dateTo)) {
            alert('A data inicial deve ser anterior à data final.');
            return;
        }
        
        // Mostrar loading
        $('#load-statistics').prop('disabled', true).text('Carregando...');
        
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
                    // Atualizar resumo rápido
                    $('#stats-1day').text(response.data.summary['1day'] || 0);
                    $('#stats-7days').text(response.data.summary['7days'] || 0);
                    $('#stats-30days').text(response.data.summary['30days'] || 0);
                    
                    // Atualizar gráfico
                    updateChart(response.data.chart);
                } else {
                    alert('Erro ao carregar estatísticas: ' + (response.data.message || 'Erro desconhecido'));
                }
            },
            error: function() {
                alert('Erro ao conectar com o servidor. Tente novamente.');
            },
            complete: function() {
                $('#load-statistics').prop('disabled', false).text('Carregar Estatísticas');
            }
        });
    }
    
    function updateChart(chartData) {
        const ctx = document.getElementById('interactions-chart');
        
        if (!ctx) {
            return;
        }
        
        // Destruir gráfico anterior se existir
        if (chart) {
            chart.destroy();
        }
        
        // Criar novo gráfico
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Interações',
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

