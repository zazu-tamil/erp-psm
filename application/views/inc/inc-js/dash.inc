<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // 1. Data passed from Controller
    const months = <?php echo isset($chart_months) ? json_encode($chart_months) : '[]'; ?>;
    const sales = <?php echo isset($chart_sales) ? json_encode($chart_sales) : '[]'; ?>;
    const purchases = <?php echo isset($chart_purchases) ? json_encode($chart_purchases) : '[]'; ?>;
    const tenderStatusData = <?php echo isset($tender_status_stats) ? json_encode($tender_status_stats) : '[]'; ?>;

    // 2. Sales vs Purchases Area/Line Chart
    const lineCanvas = document.getElementById('salesPurchasesChart');
    if (lineCanvas) {
        const ctxLine = lineCanvas.getContext('2d');

        // Create gradients
        const gradientSales = ctxLine.createLinearGradient(0, 0, 0, 300);
        gradientSales.addColorStop(0, 'rgba(16, 185, 129, 0.22)');
        gradientSales.addColorStop(1, 'rgba(16, 185, 129, 0.01)');

        const gradientPurchases = ctxLine.createLinearGradient(0, 0, 0, 300);
        gradientPurchases.addColorStop(0, 'rgba(239, 68, 68, 0.18)');
        gradientPurchases.addColorStop(1, 'rgba(239, 68, 68, 0.01)');

        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Sales Revenue (Invoiced)',
                        data: sales,
                        borderColor: '#10b981',
                        borderWidth: 2.5,
                        backgroundColor: gradientSales,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 3.5,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Purchases & Bills',
                        data: purchases,
                        borderColor: '#ef4444',
                        borderWidth: 2.5,
                        backgroundColor: gradientPurchases,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 3.5,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 15,
                            font: {
                                family: "'Inter', sans-serif",
                                size: 12,
                                weight: '600'
                            },
                            color: '#475569'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        titleFont: {
                            family: "'Inter', sans-serif",
                            size: 12,
                            weight: '700'
                        },
                        bodyFont: {
                            family: "'Inter', sans-serif",
                            size: 12
                        },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'BD ' + Number(context.parsed.y).toLocaleString('en-US', {
                                        minimumFractionDigits: 3,
                                        maximumFractionDigits: 3
                                    });
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: "'Inter', sans-serif",
                                size: 11
                            },
                            color: '#64748b'
                        }
                    },
                    y: {
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            font: {
                                family: "'Inter', sans-serif",
                                size: 11
                            },
                            color: '#64748b',
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return (value / 1000).toFixed(0) + 'K';
                                }
                                return value;
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. Tender Status Pipeline Doughnut Chart
    const doughnutCanvas = document.getElementById('tenderStatusChart');
    if (doughnutCanvas) {
        const ctxDoughnut = doughnutCanvas.getContext('2d');
        
        const statusLabels = [];
        const statusCounts = [];
        const statusColors = [];
        
        const colorMap = {
            'Open': '#3b82f6',     // Blue
            'Quoted': '#8b5cf6',   // Purple
            'Won': '#10b981',      // Emerald Green
            'Lost': '#ef4444',     // Red
            'On Hold': '#f59e0b'   // Amber
        };

        tenderStatusData.forEach(item => {
            statusLabels.push(item.tender_status);
            statusCounts.push(parseInt(item.count));
            statusColors.push(colorMap[item.tender_status] || '#94a3b8');
        });

        if (statusLabels.length === 0) {
            statusLabels.push('No Active Tenders');
            statusCounts.push(1);
            statusColors.push('#e2e8f0');
        }

        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: statusColors,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 12,
                            font: {
                                family: "'Inter', sans-serif",
                                size: 11,
                                weight: '600'
                            },
                            color: '#475569'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const val = context.raw;
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${val} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 4. Initialize Recent Tenders DataTable (Top 15 Fast Load)
    if ($('#recentTendersTable').length) {
        $('#recentTendersTable').DataTable({
            "order": [[1, "desc"]],
            "pageLength": 15,
            "lengthMenu": [5, 10, 15, 25],
            "language": {
                "search": "",
                "searchPlaceholder": "Search Enquiry No or Client...",
                "lengthMenu": "Show _MENU_ entries"
            },
            "paging": false,          // Top 15 are loaded, fast scan without pagination clutter
            "info": false,
            "columnDefs": [
                { "targets": 0, "width": "140px" },
                { "targets": 1, "width": "90px" },
                { "targets": 2, "width": "180px" },
                { "targets": 3, "width": "160px" },
                { "targets": 4, "width": "80px" },
                { "targets": 5, "width": "70px", "orderable": false }
            ]
        });
    }

    // 5. Initialize Modals with DataTables on Demand
    $('#closingTendersModal').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#closingTendersTable')) {
            $('#closingTendersTable').DataTable({ 
                "order": [[3, "asc"]],
                "pageLength": 10
            });
        }
    });

    $('#enquiriesTodayModal').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#enquiriesTodayTable')) {
            $('#enquiriesTodayTable').DataTable({ 
                "order": [[0, "asc"]],
                "pageLength": 10
            });
        }
    });

    $('#quotationsTodayModal').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#quotationsTodayTable')) {
            $('#quotationsTodayTable').DataTable({ 
                "order": [[0, "asc"]],
                "pageLength": 10
            });
        }
    });

    $('#vendorInquiriesTodayModal').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#vendorInquiriesTodayTable')) {
            $('#vendorInquiriesTodayTable').DataTable({ 
                "order": [[0, "asc"]],
                "pageLength": 10
            });
        }
    });
});
</script>
