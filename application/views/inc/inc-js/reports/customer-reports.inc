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
    // Initialize Select2 on the customer selection dropdown
    $("#stmt_customer_select").select2({
      width: '100%'
    });

    // Check if initial customer has opening balance
    function checkCustomerOpening(customer_id, isInitialLoad) {
      if (!customer_id) {
        // No customer selected (All Customers / initial state)
        $("#div-from-date, #div-to-date, #div-submit").hide();
        $(".opening-balance-warning").remove();
        return;
      }

      $.ajax({
        url: "<?php echo site_url('get-customer-opening-balance'); ?>",
        type: "post",
        data: { customer_id: customer_id },
        dataType: "json",
        success: function (res) {
          $(".opening-balance-warning").remove();
          
          if (res.status === 'success') {
            if (res.exists) {
              // Opening balance exists! Enable and show date inputs
              $("#div-from-date, #div-to-date, #div-submit").fadeIn();
              
              // Constrain From and To Date fields
              $("#from_date, #to_date").attr('min', res.opening_date);
              
              // Adjust dates if they are prior to opening date
              let currentFrom = $("#from_date").val();
              if (!currentFrom || currentFrom < res.opening_date) {
                $("#from_date").val(res.opening_date);
              }
              
              let currentTo = $("#to_date").val();
              if (!currentTo || currentTo < res.opening_date) {
                $("#to_date").val(res.opening_date);
              }
            } else {
              // Opening balance is empty! Hide date inputs and alert user
              $("#div-from-date, #div-to-date, #div-submit").hide();
              
              //alert("Customer opening balance is empty. Please enter the opening balance first!");
              
              // Display premium alert card at the top
              let alertHtml = `
                <div class="alert alert-warning alert-dismissible opening-balance-warning" style="border-radius: 8px; margin-bottom: 20px;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> Attention!</h4>
                    Customer opening balance is empty. Please enter the opening balance first!
                </div>
              `;
              $("#stmt-report-container").prepend(alertHtml);
            }
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", xhr.responseText);
        }
      });
    }

    // Trigger on drop-down change
    $("#stmt_customer_select").on("change", function () {
      let val = $(this).val();
      checkCustomerOpening(val, false);
    });

    // Run once on load to establish correct state
    let initialCustomer = $("#stmt_customer_select").val();
    if (initialCustomer) {
      // Just set attributes but don't force-hide on page-load if it was validated by PHP
      $.ajax({
        url: "<?php echo site_url('get-customer-opening-balance'); ?>",
        type: "post",
        data: { customer_id: initialCustomer },
        dataType: "json",
        success: function (res) {
          if (res.status === 'success' && res.exists) {
            $("#from_date, #to_date").attr('min', res.opening_date);
          }
        }
      });
    } else {
      $("#div-from-date, #div-to-date, #div-submit").hide();
    }

    // Handle Export Excel button click
    $(document).on('click', '#btn-export-excel', function (e) {
      e.preventDefault();
      $('#export_excel').val('1');
      $('#report-filter-form').submit();
      // Reset back to 0 so subsequent standard filters do not trigger excel export
      setTimeout(function () {
        $('#export_excel').val('0');
      }, 500);
    });
  });
</script>
