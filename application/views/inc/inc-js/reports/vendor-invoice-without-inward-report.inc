<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" />

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tbl-data').DataTable({
        "dom": "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "pageLength": 10,
        "ordering": true,
        "buttons": [
            {
                extend: 'excelHtml5',
                text: 'Export Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    modifier: { page: 'current' },
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                },
                customize: function (xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    
                    // Add borders to all cells
                    $('row c', sheet).attr('s', '25');

                    // Header coloring (dark blue)
                    $('row:first c', sheet).attr('s', '2'); // '2' is usually a bold style, maybe with a background. 
                    // To do it properly with DataTables excelHtml5, '32' is a default bold with border and background in some DataTables builds.
                    // We'll use style 32 for headers if it's available, otherwise fallback.
                    $('row[r="1"] c', sheet).attr('s', '32');
                }
            },
            {
                extend: 'pdfHtml5',
                text: 'Export PDF',
                className: 'btn btn-danger btn-sm',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    modifier: { page: 'current' },
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                },
                customize: function (doc) {
                    // Proper alignment and proper font size
                    doc.defaultStyle.fontSize = 8;
                    doc.styles.tableHeader.fontSize = 9;
                    doc.styles.tableHeader.alignment = 'left';
                    
                    // Borders and layout
                    var objLayout = {};
                    objLayout['hLineWidth'] = function(i) { return 0.5; };
                    objLayout['vLineWidth'] = function(i) { return 0.5; };
                    objLayout['hLineColor'] = function(i) { return '#aaa'; };
                    objLayout['vLineColor'] = function(i) { return '#aaa'; };
                    objLayout['paddingLeft'] = function(i) { return 4; };
                    objLayout['paddingRight'] = function(i) { return 4; };
                    doc.content[1].layout = objLayout;
                }
            }
        ]
    });

    // Handle View Items click
    $('#tbl-data').on('click', '.btn-view-items', function() {
        var invoiceId = $(this).data('invoice-id');
        var poId = $(this).data('po-id');
        
        $('#itemsModal').modal('show');
        $('#itemsModalBody').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
        
        $.ajax({
            url: '<?= site_url("get-invoice-inward-item-comparison") ?>',
            type: 'POST',
            data: {
                invoice_id: invoiceId,
                po_id: poId,
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            success: function(response) {
                $('#itemsModalBody').html(response);
            },
            error: function() {
                $('#itemsModalBody').html('<p class="text-danger">Failed to load item comparison.</p>');
            }
        });
    });
});
</script>
