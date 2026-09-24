<!-- DataTables -->
<link rel="stylesheet" href="<?php echo base_url('asset/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css'); ?>">
<script src="<?php echo base_url('asset/bower_components/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('asset/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js'); ?>"></script>

<!-- Select2 -->
<link rel="stylesheet" href="<?php echo base_url('asset/bower_components/select2/dist/css/select2.min.css'); ?>">
<script src="<?php echo base_url('asset/bower_components/select2/dist/js/select2.full.min.js'); ?>"></script>

<script>
$(document).ready(function () {
    // Initialize Select2 on dropdowns
    $('.select2').select2({
        width: '100%'
    });

    // Initialize DataTable
    if ($.fn.DataTable.isDataTable('#tbl_supplier_invoice')) {
        $('#tbl_supplier_invoice').DataTable().destroy();
    }

    var table = $('#tbl_supplier_invoice').DataTable({
        responsive: true,
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[1, 'asc']], // Default sort: Date ASC (chronological)
        columnDefs: [
            { orderable: false, targets: [0] }, // S.No non-orderable
            { type: 'date', targets: [1] }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search bills...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ bills",
            infoEmpty: "Showing 0 to 0 of 0 bills",
            infoFiltered: "(filtered from _MAX_ total bills)"
        }
    });

    // Auto-renumber S.No on search, sort, and pagination
    table.on('order.dt search.dt page.dt', function () {
        var info = table.page.info();
        table.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw(false);

    // Export Excel Handler
    $('#btnExportExcel').on('click', function (e) {
        e.preventDefault();
        $('#export_excel').val('1');
        $('#frmSupplierInvoice').submit();
        setTimeout(function () {
            $('#export_excel').val('0');
        }, 1000);
    });
});
</script>
