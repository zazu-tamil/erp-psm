<link rel="stylesheet" href="<?= base_url() ?>asset/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<script src="<?= base_url() ?>asset/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>asset/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 dropdowns if available
    if ($.fn.select2) {
        $('.select2').select2({
            allowClear: false
        });
    }

    // Initialize Monthly Statement DataTable
    if ($('#tblItemInwardOutward').length) {
        $('#tblItemInwardOutward').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [[1, "asc"]],
            "columnDefs": [
                { "orderable": false, "targets": [0, 9] }
            ],
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Quick filter items..."
            }
        });
    }

    // Initialize Yearly Trend DataTable if present
    if ($('#tblYearlyTrend').length) {
        $('#tblYearlyTrend').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pageLength": 25,
            "order": [[0, "asc"]]
        });
    }

    // Connect Export Excel button to submit form with export=excel
    $('#btnExportExcel').on('click', function(e) {
        e.preventDefault();
        var form = $('#frmReportSearch');
        var originalAction = form.attr('action');
        var exportUrl = '<?php echo site_url("item-inward-outward-report"); ?>?export_excel=1';
        
        // Temporarily change action or append export flag
        var hiddenExport = $('<input>').attr({
            type: 'hidden',
            name: 'export_excel',
            value: '1',
            id: 'temp_export_excel'
        });
        form.append(hiddenExport);
        form.submit();
        $('#temp_export_excel').remove();
    });
});

/**
 * Show Modal with detailed inward & outward transactions for an item
 */
function showItemVouchers(itemCode, filterType) {
    if (!itemCode) return;

    $('#modalItemCode').text(itemCode);
    $('#modalItemDetails').modal('show');
    $('#modalLoading').show();
    $('#modalContent').hide();

    // Reset card visibility
    if (filterType === 'inward') {
        $('#cardInwards').removeClass('col-md-6').addClass('col-md-12').show();
        $('#cardOutwards').hide();
    } else if (filterType === 'outward') {
        $('#cardOutwards').removeClass('col-md-6').addClass('col-md-12').show();
        $('#cardInwards').hide();
    } else {
        $('#cardInwards').removeClass('col-md-12').addClass('col-md-6').show();
        $('#cardOutwards').removeClass('col-md-12').addClass('col-md-6').show();
    }

    var selectedMonth = $('#srch_month').val() || '<?php echo date("Y-m"); ?>';

    $.ajax({
        url: '<?php echo site_url("item-inward-outward-details-ajax"); ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            item_code: itemCode,
            month: selectedMonth
        },
        success: function(res) {
            $('#modalLoading').hide();
            $('#modalContent').show();

            if (res && res.status) {
                $('#modalMonth').text('Period: ' + res.month);

                // Populate Inwards
                var inHtml = '';
                var inCount = (res.inwards && res.inwards.length) ? res.inwards.length : 0;
                $('#inwardCount').text(inCount);

                if (inCount > 0) {
                    var totalInQty = 0;
                    var totalInAmt = 0;
                    $.each(res.inwards, function(i, row) {
                        var qty = parseFloat(row.qty) || 0;
                        var rate = parseFloat(row.rate) || 0;
                        var amt = parseFloat(row.amount) || 0;
                        totalInQty += qty;
                        totalInAmt += amt;

                        inHtml += '<tr>' +
                            '<td>' + (row.inward_date || '-') + '</td>' +
                            '<td><strong>' + (row.inward_no || '-') + '</strong></td>' +
                            '<td>' + (row.vendor_name || '-') + '</td>' +
                            '<td class="text-right" style="font-weight:bold; color:#059669;">' + qty.toFixed(2) + ' ' + (row.uom || '') + '</td>' +
                            '<td class="text-right">' + rate.toFixed(2) + '</td>' +
                            '<td class="text-right">' + amt.toFixed(2) + '</td>' +
                            '</tr>';
                    });
                    inHtml += '<tr style="font-weight:bold; background:#e2e8f0;">' +
                        '<td colspan="3" class="text-right">Total Inward:</td>' +
                        '<td class="text-right" style="color:#059669;">' + totalInQty.toFixed(2) + '</td>' +
                        '<td></td>' +
                        '<td class="text-right">' + totalInAmt.toFixed(2) + '</td>' +
                        '</tr>';
                } else {
                    inHtml = '<tr><td colspan="6" class="text-center text-muted" style="padding:20px;">No inward vouchers in this month for this item.</td></tr>';
                }
                $('#inwardRows').html(inHtml);

                // Populate Outwards
                var outHtml = '';
                var outCount = (res.outwards && res.outwards.length) ? res.outwards.length : 0;
                $('#outwardCount').text(outCount);

                if (outCount > 0) {
                    var totalOutQty = 0;
                    $.each(res.outwards, function(i, row) {
                        var qty = parseFloat(row.qty) || 0;
                        totalOutQty += qty;

                        outHtml += '<tr>' +
                            '<td>' + (row.dc_date || '-') + '</td>' +
                            '<td><strong>' + (row.dc_no || '-') + '</strong></td>' +
                            '<td>' + (row.customer_name || '-') + '</td>' +
                            '<td class="text-right" style="font-weight:bold; color:#dc2626;">' + qty.toFixed(2) + '</td>' +
                            '<td class="text-center"><span class="label label-default">' + (row.uom || '') + '</span></td>' +
                            '</tr>';
                    });
                    outHtml += '<tr style="font-weight:bold; background:#e2e8f0;">' +
                        '<td colspan="3" class="text-right">Total Outward:</td>' +
                        '<td class="text-right" style="color:#dc2626;">' + totalOutQty.toFixed(2) + '</td>' +
                        '<td></td>' +
                        '</tr>';
                } else {
                    outHtml = '<tr><td colspan="5" class="text-center text-muted" style="padding:20px;">No delivery challans in this month for this item.</td></tr>';
                }
                $('#outwardRows').html(outHtml);

            } else {
                $('#inwardRows').html('<tr><td colspan="6" class="text-center text-danger">Failed to fetch transactions.</td></tr>');
                $('#outwardRows').html('<tr><td colspan="5" class="text-center text-danger">Failed to fetch transactions.</td></tr>');
            }
        },
        error: function() {
            $('#modalLoading').hide();
            $('#modalContent').show();
            $('#inwardRows').html('<tr><td colspan="6" class="text-center text-danger">Network error fetching vouchers.</td></tr>');
            $('#outwardRows').html('<tr><td colspan="5" class="text-center text-danger">Network error fetching vouchers.</td></tr>');
        }
    });
}
</script>
