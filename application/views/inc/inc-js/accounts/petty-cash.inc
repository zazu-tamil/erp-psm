<script>
$(document).ready(function() {
    // Initialize Select2 if available
    if($.fn.select2) {
        $('.select2').select2({ width: '100%' });
    }

    $('#account_head_id').change(function() {
        var ah_id = $(this).val();
        if(ah_id) {
            $.post('<?= site_url("pettycash/get_sub_accounts") ?>', {account_head_id: ah_id}, function(data) {
                $('#category_id').html(data);
                if($.fn.select2) $('#category_id').trigger('change.select2');
            });
        } else {
            $('#category_id').html('<option value="">Select Sub Account</option>');
            if($.fn.select2) $('#category_id').trigger('change.select2');
        }
    });

    $('#edit_account_head_id').change(function(e, isInit) {
        if(isInit) return; // Skip if triggered programmatically during modal init
        var ah_id = $(this).val();
        if(ah_id) {
            $.post('<?= site_url("pettycash/get_sub_accounts") ?>', {account_head_id: ah_id}, function(data) {
                $('#edit_category_id').html(data);
                if($.fn.select2) $('#edit_category_id').trigger('change.select2');
            });
        } else {
            $('#edit_category_id').html('<option value="">Select Sub Account</option>');
            if($.fn.select2) $('#edit_category_id').trigger('change.select2');
        }
    });

    $('.btn-edit').click(function() {
        var id = $(this).data('id');
        var date = $(this).data('date');
        var type = $(this).data('type');
        var account_head = $(this).data('account-head');
        var category = $(this).data('category');
        var amount = $(this).data('amount');
        var remarks = $(this).data('remarks');

        $('#edit_id').val(id);
        $('#edit_type').val(type);
        $('#edit_date').val(date);
        $('#edit_amount').val(amount);
        $('#edit_remarks').val(remarks);

        if(type === 'Outward') {
            $('#edit_account_head_group').show();
            $('#edit_category_group').show();
            
            $('#edit_account_head_id').val(account_head).trigger('change', [true]);
            
            $.post('<?= site_url("pettycash/get_sub_accounts") ?>', {account_head_id: account_head}, function(data) {
                $('#edit_category_id').html(data);
                $('#edit_category_id').val(category);
                if($.fn.select2) $('#edit_category_id').trigger('change.select2');
            });

            $('#edit_account_head_id').prop('required', true);
            $('#edit_category_id').prop('required', true);
        } else {
            $('#edit_account_head_group').hide();
            $('#edit_category_group').hide();
            $('#edit_account_head_id').val('').trigger('change', [true]);
            $('#edit_category_id').html('<option value="">Select Sub Account</option>');
            $('#edit_category_id').val('').trigger('change.select2');
            $('#edit_account_head_id').prop('required', false);
            $('#edit_category_id').prop('required', false);
        }

        $('#editTransactionModal').modal('show');
    });

    $('.btn-delete').click(function() {
        var id = $(this).data('id');
        if(confirm('Are you sure you want to delete this transaction? This action cannot be undone.')) {
            window.location.href = '<?= site_url("pettycash/delete_transaction/") ?>' + id;
        }
    });

    // Initialize Chart.js Donut Chart
    if(typeof chartData !== 'undefined' && chartData.length > 0) {
        var ctx = document.getElementById('expenseChart').getContext('2d');
        var expenseChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartLabels,
                datasets: [{
                    data: chartData,
                    backgroundColor: chartColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed !== null) {
                                    label += new Intl.NumberFormat('en-US', { minimumFractionDigits: 3 }).format(context.parsed) + ' BHD';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Generate Custom Legend
        var legendHtml = '';
        for (var i = 0; i < chartLabels.length; i++) {
            if(i > 4) break; // Limit to 5 legend items
            var percent = ((chartData[i] / totalOutward) * 100).toFixed(1);
            var amountFmt = new Intl.NumberFormat('en-US', { minimumFractionDigits: 3 }).format(chartData[i]);
            var color = chartColors[i % chartColors.length];
            
            legendHtml += '<div class="legend-item">';
            legendHtml += '<div class="legend-label"><span class="legend-color-box" style="background:' + color + ';"></span>' + chartLabels[i] + '</div>';
            legendHtml += '<div class="legend-values"><span class="legend-percent">' + percent + '%</span><span class="legend-amount">' + amountFmt + ' BHD</span></div>';
            legendHtml += '</div>';
        }
        $('#chart-legend').html(legendHtml);
    } else {
        $('#expenseChart').replaceWith('<div style="color:#94a3b8; text-align:center; padding-top:40px;">No expenses this month</div>');
    }
});
</script>
