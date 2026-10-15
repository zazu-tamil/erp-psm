<!-- Select2 -->
<link rel="stylesheet" href="<?php echo base_url('asset/bower_components/select2/dist/css/select2.min.css'); ?>" />
<script src="<?php echo base_url('asset/bower_components/select2/dist/js/select2.full.min.js'); ?>"></script>

<!-- DataTables (Local - Fast Loading) -->
<link rel="stylesheet" href="<?php echo base_url('asset/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css'); ?>" />
<script src="<?php echo base_url('asset/bower_components/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('asset/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js'); ?>"></script>

<style>
  /* FULL RESPONSIVE WIDTH */
  .select2-container {
    width: 100% !important;
  }

  /* ADMINLTE INPUT MATCH */
  .select2-container--default .select2-selection--single {
    height: 34px !important;
    padding: 6px 12px !important;
    border: 1px solid #d2d6de !important;
    border-radius: 0 !important;
    background-color: #fff !important;
  }

  .select2-selection__rendered {
    line-height: 22px !important;
  }

  .select2-selection__arrow {
    height: 32px !important;
  }

  @media (max-width: 768px) {
    .select2-container--default .select2-selection--single {
      height: 37px !important;
      padding: 8px 12px !important;
    }
  }

  .select2-container .select2-selection--single .select2-selection__rendered {
    padding-left: 0 !important;
    padding-right: 0 !important;
    height: auto !important;
  }

  .dataTables_wrapper {
    margin-top: 10px;
  }
</style>

<script>
  $(document).ready(function () {
    // Initialize Select2
    if ($.fn.select2) {
      $('.select2').select2({
        width: '100%'
      });
    }

    // Initialize DataTable on Vendor Multi Customer Table
    if ($.fn.DataTable) {
      if ($.fn.DataTable.isDataTable('#vendor_multi_invoice_table')) {
        $('#vendor_multi_invoice_table').DataTable().destroy();
      }

      var table = $('#vendor_multi_invoice_table').DataTable({
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[1, 'desc']], // Default sort by Invoice Date DESC
        columnDefs: [
          { orderable: false, targets: [0, 8] }, // Disable sorting on S.No and Action
          { searchable: false, targets: [8] }     // Disable search on Action
        ],
        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search bills...",
          lengthMenu: "Show _MENU_ entries",
          info: "Showing _START_ to _END_ of _TOTAL_ entries",
          infoEmpty: "Showing 0 to 0 of 0 entries",
          infoFiltered: "(filtered from _MAX_ total entries)",
          zeroRecords: "No matching records found"
        }
      });

      // Renumber S.No on search, sort, and pagination
      table.on('order.dt search.dt page.dt', function () {
        var info = table.page.info();
        table.column(0, { search: 'applied', order: 'current', page: 'current' }).nodes().each(function (cell, i) {
          cell.innerHTML = info.start + i + 1;
        });
      });
    }

    // Delete record action
    $(document).on('click', '.del_record', function () {
      if (confirm("Are you sure you want to delete this purchase bill?")) {
        const id = $(this).val();
        $.post(
          "<?php echo site_url('delete-record'); ?>",
          {
            tbl: "vendor_purchase_multiple_invoice_info",
            id: id,
          },
          function (res) {
            alert(res);
            location.reload();
          }
        ).fail(function () {
          alert("Error deleting record.");
        });
      }
    });
  });
</script>
