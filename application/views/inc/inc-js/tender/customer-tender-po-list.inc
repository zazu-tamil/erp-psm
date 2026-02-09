<script>
  jQuery(function ($) {
    /* ================================================================
       1. COMPANY CHANGE - Load ALL Customers (no filtering by company)
       ================================================================ */
    $("#srch_company_id").on("change", function () {
      const company_id = $(this).val();
      const $customerDropdown = $("#srch_customer_id");
      const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $quotationDropdown = $("#srch_quotation_no");
      const $container = $("#item_container");

      // Reset dependent dropdowns
      $enquiryDropdown.html('<option value="">Select Enquiry</option>');
      $quotationDropdown.html('<option value="">Select Quotation</option>');
      $container.html("");

      if (!company_id) {
        $customerDropdown.html('<option value="">Select Customer</option>');
        return;
      }

      // Load ALL customers (not filtered by company)
      $.ajax({
        url: "<?php echo site_url('vendor/get_all_customers'); ?>",
        type: "POST",
        dataType: "json",
        success: function (res) {
          $customerDropdown.html('<option value="">Select Customer</option>');
          if (res.length > 0) {
            $.each(res, function (i, row) {
              $customerDropdown.append(
                $("<option></option>")
                  .attr("value", row.customer_id)
                  .text(row.customer_name)
              );
            });
            $customerDropdown.prop("disabled", false);
          } else {
            $customerDropdown.html(
              '<option value="">No customers found</option>'
            );
          }
        },
        error: function () {
          alert("Error loading customers");
        },
      });
    });

 
 
    */

    // Initialize Select2 (make sure this runs first)
    $(document).ready(function () {
      $(".select2").select2({
        placeholder: "Select an option",
        allowClear: true,
      });

      // Trigger change on page load if there's already a value (for filters persistence)
      if ($("#srch_tender_enquiry_id").val()) {
        $("#srch_tender_enquiry_id").trigger("change");
      }
    });

    // Correct way to handle Select2 change event
    $(document).on("change", "#srch_tender_enquiry_id", function () {
      const tenderId = $(this).val();

      // Clear dependent dropdowns
      $("#srch_tender_quotation_id")
        .empty()
        .append('<option value="">Select Quotation</option>')
        .trigger("change");

      if (!tenderId) {
        $("#item_rows").empty();
        addEmptyRow();
        return;
      }

      $.ajax({
        url: "<?php echo site_url('tender/get_data'); ?>",
        type: "POST",
        data: {
          tbl: "get-quotation-with-enquiry",
          id: tenderId,
        },
        dataType: "json",
        success: function (data) {
          const $quotation = $("#srch_tender_quotation_id");
          $quotation
            .empty()
            .append('<option value="">Select Quotation No</option>');

          if (data && data.length > 0) {
            $.each(data, function (i, item) {
              $quotation.append(
                `<option value="${item.tender_quotation_id}">${item.quotation_no}</option>`
              );
            });
          } else {
            $quotation.append('<option value="">No Quotations Found</option>');
          }

          // Re-initialize Select2 after dynamically adding options
          $quotation.trigger("change");

          // Clear items if needed
          $("#item_rows").empty();
          addEmptyRow();
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error: ", error);
          alert("Failed to load quotations. Please try again.");
        },
      });
    });

    /* ================================================================
       DELETE PO RECORD
       ================================================================ */
    $(".del_po").click(function () {
      if (confirm("Are you sure you want to delete this Customer Tender PO?")) {
        var id = $(this).val();

        $.post(
          "<?php echo site_url('delete-record'); ?>",
          {
            tbl: "customer_tender_po_info",
            id: id,
          },
          function (res) {
            alert(res);
            location.reload();
          }
        ).fail(function () {
          alert("Error deleting record. Please try again.");
        });
      }
    });

    /* ================================================================
       SEARCH FILTER - Optional: Auto-submit on select change
       ================================================================ */
    $("#srch_company_id, #srch_customer_id, #srch_po_status").on(
      "change",
      function () {
        // Uncomment line below to auto-submit filter
        // $('#frmsearch').submit();
      }
    );
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
