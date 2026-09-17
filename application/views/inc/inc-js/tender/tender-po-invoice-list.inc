<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>

<!-- DataTables -->
<link rel="stylesheet" href="<?php echo base_url('asset/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css'); ?>">
<script src="<?php echo base_url('asset/bower_components/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('asset/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js'); ?>"></script>

<script>
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#tbl_tender_invoice')) {
      $('#tbl_tender_invoice').DataTable().destroy();
    }

    var table = $('#tbl_tender_invoice').DataTable({
      responsive: true,
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      pageLength: 25,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
      order: [[1, 'desc']], // Default sort order by Invoice Date DESC
      columnDefs: [
        { orderable: false, targets: [0, 7] }, // Disable sorting on S.No and Action
        { searchable: false, targets: [7] }    // Disable search on Action
      ],
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search invoices...",
        lengthMenu: "Show _MENU_ entries",
        info: "Showing _START_ to _END_ of _TOTAL_ entries",
        infoEmpty: "Showing 0 to 0 of 0 entries",
        infoFiltered: "(filtered from _MAX_ total entries)"
      }
    });

    // Automatically renumber S.No on search, sort, and pagination
    table.on('order.dt search.dt page.dt', function () {
      var info = table.page.info();
      table.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
        cell.innerHTML = info.start + i + 1;
      });
    });

    $(document).on("click", ".del_record", function () {
      if (confirm("Are you sure you want to delete this tender invoice?")) {
        const id = $(this).val();
        //alert(id);
        $.post(
          "<?php echo site_url('delete-record'); ?>",
          {
            tbl: "tender_enq_invoice_info",
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
<link
  href="<?php echo base_url() ?>asset/bower_components/select2/dist/css/select2.min.css"
  rel="stylesheet"
/>
<script src="<?php echo base_url() ?>asset/bower_components/select2/dist/js/select2.full.min.js"></script>

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

  /* TEXT ALIGN FIX */
  .select2-selection__rendered {
    line-height: 22px !important;
  }

  /* DROPDOWN ARROW */
  .select2-selection__arrow {
    height: 32px !important;
  }

  /* MOBILE TOUCH FIX */
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
    /* margin-top: -4px; */
  }
</style>
<script>
  $(document).ready(function () {
    $(".select2").select2({
      width: "100%",
    });
  });
</script>


<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    jQuery(function ($) {
        $("#srch_enquiry_no").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
                    type: "POST",
                    data: { search: request.term },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        response(data);
                    },
                });
            },

            minLength: 1,

            select: function (event, ui) {
                console.log(ui);
                // $("#srch_company_id").val(ui.item.company_id);
                // $("#srch_customer_id").val(ui.item.customer_id).change();
            },
        });

        $("#srch_tender_enquiry_id").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url('tender/tender_srch_rfq_no'); ?>",
                    type: "POST",
                    data: { search: request.term },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        response(data);
                    },
                });
            },

            minLength: 1,

            select: function (event, ui) {
                console.log(ui);
                // $("#srch_company_id").val(ui.item.company_id);
                // $("#srch_customer_id").val(ui.item.customer_id).change();
            },
        });
        $("#srch_tender_invoice_no").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url('tender/srch_tender_invoice_no'); ?>",
                    type: "POST",
                    data: { search: request.term },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        response(data);
                    },
                });
            },

            minLength: 1,

            select: function (event, ui) {
                console.log(ui);
                // $("#srch_company_id").val(ui.item.company_id);
                // $("#srch_customer_id").val(ui.item.customer_id).change();
            },
        });
    });
</script>