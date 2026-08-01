<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Decode data from PHP
    const months = <?php echo isset($chart_months) ? json_encode($chart_months) : '[]'; ?>;
    const sales = <?php echo isset($chart_sales) ? json_encode($chart_sales) : '[]'; ?>;
    const purchases = <?php echo isset($chart_purchases) ? json_encode($chart_purchases) : '[]'; ?>;
    
    const tenderStatusData = <?php echo isset($tender_status_stats) ? json_encode($tender_status_stats) : '[]'; ?>;

    // 1. Sales vs Purchases Line Chart
    const lineCanvas = document.getElementById('salesPurchasesChart');
    if (lineCanvas) {
        const ctxLine = lineCanvas.getContext('2d');
        
        // Use flat solid fills
        const salesBg = 'rgba(16, 185, 129, 0.05)';
        const purchasesBg = 'rgba(239, 68, 68, 0.05)';

        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Sales (Customer Invoices)',
                        data: sales,
                        borderColor: '#10b981',
                        borderWidth: 3,
                        backgroundColor: salesBg,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Purchases (Supplier Bills)',
                        data: purchases,
                        borderColor: '#ef4444',
                        borderWidth: 3,
                        backgroundColor: purchasesBg,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            font: {
                                family: "'Source Sans Pro', sans-serif",
                                size: 12,
                                weight: '600'
                            },
                            color: '#4b5563'
                        }
                    },
                    tooltip: {
                        padding: 10,
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        titleFont: {
                            family: "'Source Sans Pro', sans-serif",
                            size: 12,
                            weight: '700'
                        },
                        bodyFont: {
                            family: "'Source Sans Pro', sans-serif",
                            size: 12
                        },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-BH', { style: 'currency', currency: 'BHD', maximumFractionDigits: 3 }).format(context.parsed.y);
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
                                family: "'Source Sans Pro', sans-serif",
                                size: 11
                            },
                            color: '#6b7280'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(243, 244, 246, 0.8)'
                        },
                        ticks: {
                            font: {
                                family: "'Source Sans Pro', sans-serif",
                                size: 11
                            },
                            color: '#6b7280',
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

    // 2. Tender Status Doughnut Chart
    const doughnutCanvas = document.getElementById('tenderStatusChart');
    if (doughnutCanvas) {
        const ctxDoughnut = doughnutCanvas.getContext('2d');
        
        // Prepare status data
        const statusLabels = [];
        const statusCounts = [];
        const statusColors = [];
        
        const colorMap = {
            'Open': '#3b82f6',     // Blue
            'Quoted': '#00c5ff',   // Cyan (Zazu)
            'Won': '#10b981',      // Emerald Green
            'Lost': '#ef4444',     // Red
            'On Hold': '#f59e0b'   // Amber
        };

        tenderStatusData.forEach(item => {
            statusLabels.push(item.tender_status);
            statusCounts.push(parseInt(item.count));
            statusColors.push(colorMap[item.tender_status] || '#9ca3af');
        });

        // If no data, show a placeholder segment
        if (statusLabels.length === 0) {
            statusLabels.push('No Active Tenders');
            statusCounts.push(1);
            statusColors.push('#e5e7eb');
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
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 10,
                            font: {
                                family: "'Source Sans Pro', sans-serif",
                                size: 12,
                                weight: '600'
                            },
                            color: '#4b5563'
                        }
                    },
                    tooltip: {
                        padding: 10,
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
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

    // 3. Initialize Recent Tenders DataTable
    if ($('#recentTendersTable').length) {
        $('#recentTendersTable').DataTable({
            "order": [[1, "desc"]],
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "language": {
                "search": "",
                "searchPlaceholder": "Search Enquiry No or Client...",
                "lengthMenu": "Show _MENU_ entries"
            },
            "responsive": false,
            "scrollX": true,           // KEY FIX: enforces column widths, adds horizontal scroll
            "autoWidth": false,        // prevent DataTables from auto-shrinking columns
            "pagingType": "simple_numbers",
            "dom": '<"row align-items-center"<"col-sm-6"l><"col-sm-6"f>>rt<"row align-items-center mt-2"<"col-sm-5"i><"col-sm-7"p>>',
            "columnDefs": [
                { "targets": 0, "width": "160px" },                               // Enquiry No
                { "targets": 1, "width": "95px",  "className": "col-date" },      // Date
                { "targets": 2, "width": "180px" },                               // Tender Name
                { "targets": 3, "width": "150px" },                               // Customer
                { "targets": 4, "width": "130px" },                               // Company
                { "targets": 5, "width": "90px",  "orderable": false }            // Status
            ]
        });
    }
});
</script>
