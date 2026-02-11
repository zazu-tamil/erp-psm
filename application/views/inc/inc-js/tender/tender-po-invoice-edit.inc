<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
<style>
  .total-wrapper {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    width: 100%;
  }
  .total-box {
    background: linear-gradient(135deg, #f6fff9, #e8f9f0);
    border: 2px solid #b5e0c6;
    border-radius: 12px;
    padding: 12px 24px;
    font-family: "Segoe UI", sans-serif;
    font-size: 1.15rem;
    color: #333;
    min-width: 280px;
    text-align: right;
    transition: all 0.3s ease-in-out;
  }
  .total-box:hover {
    background: #e3f7ec;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
  }
  .total-box i {
    font-size: 1.3rem;
    vertical-align: middle;
  }
  #total_amount,
  #total_amount_wo_tax {
    font-size: 1.4rem;
    font-weight: 700;
    margin-left: 5px;
  }
  @media (max-width: 768px) {
    .total-wrapper {
      justify-content: center;
    }
    .total-box {
      text-align: center;
      min-width: 220px;
    }
  }
</style>
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
                  .text(row.customer_name),
              );
            });
            $customerDropdown.prop("disabled", false);
          } else {
            $customerDropdown.html(
              '<option value="">No customers found</option>',
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
                  .text(row.display),
              );
            });
          } else {
            $enquiryDropdown.html(
              '<option value="">No enquiries found</option>',
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
  });

  $(function () {
    CKEDITOR.replace("editor1", {
      height: 80,
      extraPlugins: "justify,colorbutton,font,table",
      removeButtons: "",
      toolbar: [
        {
          name: "clipboard",
          items: [
            "Cut",
            "Copy",
            "Paste",
            "PasteText",
            "PasteFromWord",
            "-",
            "Undo",
            "Redo",
          ],
        },
        {
          name: "basicstyles",
          items: [
            "Bold",
            "Italic",
            "Underline",
            "Strike",
            "RemoveFormat",
            "CopyFormatting",
          ],
        },
        {
          name: "paragraph",
          items: [
            "NumberedList",
            "BulletedList",
            "-",
            "Outdent",
            "Indent",
            "-",
            "Blockquote",
            "JustifyLeft",
            "JustifyCenter",
            "JustifyRight",
            "JustifyBlock",
          ],
        },
        { name: "links", items: ["Link", "Unlink", "Anchor"] },
        {
          name: "insert",
          items: ["Image", "Table", "HorizontalRule", "SpecialChar"],
        },
        { name: "styles", items: ["Format", "Font", "FontSize"] },
        { name: "colors", items: ["TextColor", "BGColor"] },
        { name: "tools", items: ["Maximize", "ShowBlocks"] },
      ],
    });
    CKEDITOR.replace("editor2", {
      height: 80,
      extraPlugins: "justify,colorbutton,font,table",
      removeButtons: "",
      toolbar: [
        {
          name: "clipboard",
          items: [
            "Cut",
            "Copy",
            "Paste",
            "PasteText",
            "PasteFromWord",
            "-",
            "Undo",
            "Redo",
          ],
        },
        {
          name: "basicstyles",
          items: [
            "Bold",
            "Italic",
            "Underline",
            "Strike",
            "RemoveFormat",
            "CopyFormatting",
          ],
        },
        {
          name: "paragraph",
          items: [
            "NumberedList",
            "BulletedList",
            "-",
            "Outdent",
            "Indent",
            "-",
            "Blockquote",
            "JustifyLeft",
            "JustifyCenter",
            "JustifyRight",
            "JustifyBlock",
          ],
        },
        { name: "links", items: ["Link", "Unlink", "Anchor"] },
        {
          name: "insert",
          items: ["Image", "Table", "HorizontalRule", "SpecialChar"],
        },
        { name: "styles", items: ["Format", "Font", "FontSize"] },
        { name: "colors", items: ["TextColor", "BGColor"] },
        { name: "tools", items: ["Maximize", "ShowBlocks"] },
      ],
    });
  });
</script>
<script>
  $(document).ready(function () {
    function calculateItemAmount(row) {
      let qty = parseFloat(row.find(".qty-input").val()) || 0;
      let rate = parseFloat(row.find(".rate-input").val()) || 0;
      let gst = parseFloat(row.find(".gst-select").val()) || 0;

      let base_amount = qty * rate;
      let gst_amount = (base_amount * gst) / 100;
      let total = base_amount + gst_amount;

      // Set values
      row.find(".amount-input").val(total.toFixed(3));

      // Create hidden GST field if not exists
      if (row.find(".gst-amount-input").length === 0) {
        row.append('<input type="hidden" class="gst-amount-input" value="0">');
      }

      row.find(".gst-amount-input").val(gst_amount.toFixed(3));
    }

    /* ===============================
     TOTAL CALCULATION
  =============================== */
    function calculateTotals() {
      let total_amount = 0; // WITH GST
      let total_wo_tax = 0; // WITHOUT GST
      let total_gst_only = 0; // GST ONLY

      $(".item-row").each(function () {
        if ($(this).find(".item-check").is(":checked")) {
          const amount = parseFloat($(this).find(".amount-input").val()) || 0;
          const gst_amt =
            parseFloat($(this).find(".gst-amount-input").val()) || 0;

          total_amount += amount;
          total_gst_only += gst_amt;
          total_wo_tax += amount - gst_amt;
        }
      });

      // Display
      $("#total_amount").text(total_amount.toFixed(3));
      $("#total_amount_wo_tax").text(total_wo_tax.toFixed(3));

      // Hidden fields
      $(".total_amount_hidden").val(total_amount.toFixed(3));
      $(".total_without_tax_hidden").val(total_wo_tax.toFixed(3));
      $(".gst_amount_only_hidden").val(total_gst_only.toFixed(3));
    }

    // Trigger on change
    $(document).on(
      "input change",
      ".rate-input, .gst-select, .qty-input, .item-check",
      function () {
        let row = $(this).closest(".item-row");
        calculateItemAmount(row);
        calculateTotals();
      },
    );

    // Initial Load
    $(".item-row").each(function () {
      calculateItemAmount($(this));
    });

    calculateTotals();
  });
</script>
