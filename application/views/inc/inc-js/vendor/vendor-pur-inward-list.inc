<script>
  $(document).ready(function () {
    // === COMPANY → LOAD CUSTOMERS & VENDORS ===
    $("#srch_company_id").on("change", function () {
      const company_id = $(this).val();

      const $customer = $("#srch_customer_id");
      const $vendor = $("#srch_vendor_id");

      // Reset dropdowns
      $customer.html('<option value="">Loading...</option>');
      $vendor.html('<option value="">Loading...</option>');

      // If no company selected → show "All"
      if (!company_id) {
        $customer.html('<option value="">All</option>');
        $vendor.html('<option value="">All</option>');
        return;
      }

      // === LOAD CUSTOMERS ===
      $.post(
        "<?php echo site_url('vendor/get_all_customers'); ?>",
        { company_id: company_id },
        function (res) {
          $customer.html('<option value="">All</option>');
          if (Array.isArray(res)) {
            $.each(res, function (i, row) {
              $customer.append(
                `<option value="${row.customer_id}">${row.customer_name}</option>`
              );
            });
          }
        },
        "json"
      );

      // === LOAD VENDORS ===
      $.post(
        "<?php echo site_url('vendor/get_all_vendors'); ?>",
        { company_id: company_id },
        function (res) {
          $vendor.html('<option value="">All</option>');
          if (Array.isArray(res)) {
            $.each(res, function (i, row) {
              $vendor.append(
                `<option value="${row.vendor_id}">${row.vendor_name}</option>`
              );
            });
          }
        },
        "json"
      );
    });

    // === CUSTOMER → LOAD TENDER ENQUIRIES ===
    $("#srch_customer_id").on("change", function () {
      const customer_id = $(this).val();
      const company_id = $("#srch_company_id").val();
      const $enquiry = $("#srch_tender_enquiry_id");

      if (!customer_id || !company_id) {
        $enquiry.html('<option value="">All</option>').prop("disabled", true);
        return;
      }

      $.post(
        "<?php echo site_url('vendor/get_tender_enquiries_by_customer'); ?>",
        {
          company_id: company_id,
          customer_id: customer_id,
        },
        function (res) {
          $enquiry.html('<option value="">All</option>');
          if (Array.isArray(res) && res.length > 0) {
            $.each(res, function (i, row) {
              $enquiry.append(
                `<option value="${row.tender_enquiry_id}">${row.display}</option>`
              );
            });
            $enquiry.prop("disabled", false);
          }
        },
        "json"
      );
    });

    // === TENDER ENQUIRY → LOAD VENDORS ===
    $("#srch_tender_enquiry_id").on("change", function () {
      const tender_id = $(this).val();
      const $vendor = $("#srch_vendor_id");

      $vendor.html('<option value="">Loading...</option>');

      if (!tender_id) {
        $vendor.html('<option value="">All</option>');
        return;
      }

      $.post(
        "<?php echo site_url('vendor/get_vendor_list_purchase_inward'); ?>",
        { srch_tender_enquiry_id: tender_id },
        function (res) {
          $vendor.html('<option value="">All</option>');
          if (Array.isArray(res) && res.length > 0) {
            $.each(res, function (i, row) {
              $vendor.append(
                `<option value="${row.vendor_id}">${row.vendor_name}</option>`
              );
            });
          }
        },
        "json"
      );
    });
  });
</script>
