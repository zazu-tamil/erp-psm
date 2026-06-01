<link href="<?php echo base_url() ?>asset/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
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
  }
</style>

<script>
  jQuery(function ($) {
    // Initialize Select2 for search filter (not in a modal, no dropdownParent needed)
    $("#vendor_select_filter").select2({
      width: '100%'
    });

    // Initialize Select2 with dropdownParent to avoid focus conflicts in Bootstrap Modals
    $("#vendor_select_add").select2({
      dropdownParent: $('#add_modal'),
      width: '100%'
    });

    $("#vendor_select_edit").select2({
      dropdownParent: $('#edit_modal'),
      width: '100%'
    });

    // Reset select2 value on add modal show to keep it clean
    $('#add_modal').on('show.bs.modal', function () {
      $("#vendor_select_add").val('').trigger('change');
    });

    // Edit Record AJAX
    $(document).on("click", ".edit_record", function () {
      let id = $(this).val();
      $.ajax({
        url: "<?php echo site_url('get-data');?>",
        type: "post",
        data: { tbl: "vendor_opening_balance_info", id: id },
        dataType: "json",
        success: function (d) {
          console.log(d); // Debugging
          $("#edit_modal #opening_id").val(d.opening_id); 
          $("#edit_modal #vendor_select_edit").val(d.vendor_id).trigger("change"); 
          $("#edit_modal #opening_date").val(d.opening_date); 
          $("#edit_modal #balance_type").val(d.balance_type); 
          $("#edit_modal #opening_amount").val(d.opening_amount); 
          $("#edit_modal #remarks").val(d.remarks); 

          $("#edit_modal").modal("show");
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", xhr.responseText);
        },
      });
    });

    // Delete Record AJAX
    $(document).on("click", ".del_record", function () {
      let id = $(this).val();
      if (confirm("Are you sure you want to delete this opening balance?")) {
        $.ajax({
          url: "<?php echo site_url('delete-record');?>",
          type: "post",
          data: { tbl: "vendor_opening_balance_info", id: id },
          success: function (d) {
            alert(d);
            location.reload();
          },
        });
      }
    });
  });
</script>
