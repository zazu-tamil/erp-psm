<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>

<script>
  $(document).ready(function () {
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
      $enquiryDropdown
        .html('<option value="">Select Enquiry</option>')
        .prop("disabled", true);
      $quotationDropdown
        .html('<option value="">Select Quotation</option>')
        .prop("disabled", true);
      $container.html("");

      if (!company_id) {
        $customerDropdown
          .html('<option value="">Select Customer</option>')
          .prop("disabled", false);
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

    /* ================================================================
       2. CUSTOMER CHANGE - Load Tender Enquiries (filtered by company + customer)
       ================================================================ */

    $("#srch_customer_id").on("change", function () {
      const customer_id = $(this).val();
      const company_id = $("#srch_company_id").val();
      const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $quotationDropdown = $("#srch_quotation_no");
      const $container = $("#item_container");

      // Reset dependent dropdowns and items
      $enquiryDropdown
        .html('<option value="">Select Enquiry</option>')
        .prop("disabled", true);
      $quotationDropdown
        .html('<option value="">Select Quotation</option>')
        .prop("disabled", true);
      $container.html("");

      if (!customer_id || !company_id) return;

      $.ajax({
        url: "<?php echo site_url('tender/get_tender_enquiries_by_customer'); ?>",
        type: "POST",
        data: {
          company_id: company_id,
          customer_id: customer_id,
        },
        dataType: "json",
        success: function (res) {
          if (res.length > 0) {
            $enquiryDropdown.prop("disabled", false);
            $.each(res, function (i, row) {
              $enquiryDropdown.append(
                $("<option></option>")
                  .attr("value", row.tender_enquiry_id)
                  .text(row.display)
              );
            });
          } else {
            $enquiryDropdown.html(
              '<option value="">No enquiries found</option>'
            );
          }
        },
        error: function () {
          alert("Error loading enquiries");
        },
      });
    });

    $("#srch_tender_enquiry_id").on("change", function () {
      const tender_enquiry_id = $(this).val();
      const $po = $("#srch_tender_po_id");

      $po.html('<option value="">Loading...</option>');

      if (!tender_enquiry_id) {
        $po.html('<option value="">Select Tender PO</option>');
        return;
      }

      $.ajax({
        url: "<?= site_url('tender/get_tender_invoice_po_no_load'); ?>",
        type: "POST",
        data: { srch_tender_enquiry_id: tender_enquiry_id },
        dataType: "json",
        success: function (res) {
          console.log(res);

          let html = '<option value="">Select Tender PO</option>';

          if (res.length > 0) {
            $.each(res, function (i, row) {
              html += `<option value="${row.tender_po_id}">${row.customer_po_no}</option>`;
            });
          } else {
            html = '<option value="">No PO Found</option>';
          }

          $po.html(html);
        },
      });
    });


    $(".del_record").click(function () {
      if (confirm("Are you sure you want to delete this tender enquiry?")) {
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
