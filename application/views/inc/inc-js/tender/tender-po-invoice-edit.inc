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
<style>
  .total-section {
    display: flex;
    justify-content: flex-end;
    width: 100%;
  }
  .total-card {
    background: linear-gradient(135deg, #eafaf3, #dff8ec);
    border: 2px solid #aee0c0;
    border-radius: 16px;
    padding: 18px 28px;
    min-width: 320px;
    transition: all 0.3s ease-in-out;
  }
  .total-card:hover {
    background: #e1f7ec;
    transform: translateY(-3px);
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
  }
  .total-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .total-icon i {
    font-size: 1.8rem;
    background: #d2f0dc;
    padding: 10px;
    border-radius: 50%;
    color: #2e865f;
  }
  .total-text .label {
    font-size: 1.1rem;
    color: #444;
    font-weight: 600;
    margin-right: 6px;
  }
  .total-text .value {
    font-size: 1.6rem;
    font-weight: 700;
    color: #007a4d;
  }
  #total_amount {
    font-weight: 800;
  }

  @media (max-width: 768px) {
    .total-section {
      justify-content: center;
    }
    .total-card {
      text-align: center;
      min-width: 240px;
      padding: 14px 18px;
    }
    .total-icon i {
      font-size: 1.5rem;
      padding: 8px;
    }
  }
</style>
<script>
$(document).ready(function () {

    function calculateItemAmount(row) {

        let qty   = parseFloat(row.find(".qty-input").val())   || 0;
        let rate  = parseFloat(row.find(".rate-input").val())  || 0;
        let gst   = parseFloat(row.find(".gst-select").val())  || 0;

        let base_amount = qty * rate;
        let gst_amount  = (base_amount * gst) / 100;
        let total       = base_amount + gst_amount;

        row.find(".amount-input").val(total.toFixed(2));
    }

    function calculateTotal() {
        let total = 0;
        $(".amount-input").each(function () {
            total += parseFloat($(this).val()) || 0;
        });
        $("#total_amount").text(total.toFixed(2));
    }

    // Trigger recalculation on Rate or GST change
    $(document).on("input change", ".rate-input, .gst-select", function () {
        let row = $(this).closest(".item-row");
        calculateItemAmount(row);
        calculateTotal();
    });

    // Initial calculation on page load
    $(".item-row").each(function () {
        calculateItemAmount($(this));
    });
    calculateTotal();

});
</script>
