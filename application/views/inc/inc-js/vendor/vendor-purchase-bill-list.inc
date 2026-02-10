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

    $(".del_record").click(function () {
      if (confirm("Are you sure you want to delete this tender enquiry?")) {
        const id = $(this).val();
        //alert(id);
        $.post(
          "<?php echo site_url('delete-record'); ?>",
          {
            tbl: "vendor_purchase_invoice_info",
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

<link
  rel="stylesheet"
  href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"
/>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
  jQuery(function ($) {
    $("#srch_customer_rfq_no").autocomplete({
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
    $("#srch_invoice_no").autocomplete({
      source: function (request, response) {
        $.ajax({
          url: "<?php echo base_url('vendor/vendor_srch_invoice_no'); ?>",
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
