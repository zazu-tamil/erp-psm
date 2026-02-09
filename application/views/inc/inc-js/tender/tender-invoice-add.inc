<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>

<link
  rel="stylesheet"
  href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"
/>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
  $(document).ready(function () {
    $(".srch_enq_id").autocomplete({
      source: function (request, response) {
        $.ajax({
          url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
          type: "POST",
          data: { search: request.term },
          dataType: "json",
          success: function (data) {
            //console.log(data);
            response(data);
          },
        });
      },

      minLength: 1,

      select: function (event, ui) {
        console.log(ui);
        $("#srch_company_id").val(ui.item.company_id);
        $("#srch_customer_id").val(ui.item.customer_id).change();
        //$("#srch_tender_enquiry_id").val(ui.item.tender_enquiry_id);
        load_tender_enq(ui.item.tender_enquiry_id);
      },
    });

    function load_tender_enq(t_enq_id = "") {
      const customer_id = $("#srch_customer_id").val(); // <-- IMPORTANT
      const company_id = $("#srch_company_id").val();
      const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $container = $("#item_container");

      // Reset
      $enquiryDropdown
        .html('<option value="">Select Enquiry</option>')
        .prop("disabled", true);

      $container.html("");

      if (!customer_id || !company_id) return;

      $.ajax({
        url: "<?php echo site_url('vendor/get_tender_enquiries_by_customer'); ?>",
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
                $("<option>", {
                  value: row.tender_enquiry_id,
                  text: row.display,
                })
              );
            });

            // ✅ SET SELECTED VALUE HERE
            if (t_enq_id) {
              $enquiryDropdown.val(t_enq_id).trigger("change");
            }
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
    }

    $("#srch_customer_id").on("change", function () {
      load_tender_enq("");
    });

    /* ================================================================
       1. COMPANY CHANGE - Load ALL Customers (no filtering by company)
       ================================================================ */
    /* $("#srch_company_id").on("change", function () {
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
    });*/

    /* ================================================================
       2. CUSTOMER CHANGE - Load Tender Enquiries (filtered by company + customer)
       ================================================================ */

    $("#srch_customer_id1").on("change", function () {
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
<script>
  $(document).ready(function () {
    $("#srch_tender_po_id").on("change", function () {
      const tender_po_id = $(this).val();
      const $container = $("#item_container");
      $container.empty();

      if (!tender_po_id) return;

      $.ajax({
        url: "<?php echo site_url('tender/get_tender_po_invoice_load_items'); ?>",
        type: "POST",
        data: { tender_po_id: tender_po_id },
        dataType: "json",

        success: function (res) {
          if (!res || res.length === 0) {
            $container.html(`
                        <tr>
                            <td colspan="7" class="text-center text-danger">
                                No items found
                            </td>
                        </tr>
                    `);
            return;
          }

          $.each(res, function (i, row) {
            const html = `
                    <tr class="item-row">

                        <td>
                            <input type="checkbox"
                                   class="item-check"
                                   name="selected_items[]"
                                   value="${i}"
                                   checked>
                        </td>

                        <td>
                            <input type="text" class="form-control"
                                   value="${row.item_code || ""}" readonly>

                            <input type="hidden"
                                   name="tender_po_item_id[]"
                                   value="${row.tender_po_item_id}">
                        </td>

                        <td>
                            <textarea class="form-control" rows="2"
                                      name="item_desc[]" readonly>${
                                        row.item_desc || ""
                                      }</textarea>
                        </td>

                        <td>
                            <input type="text" class="form-control"
                                   name="uom[]" value="${
                                     row.uom || ""
                                   }" readonly>

                            <input type="number"
                                   name="qty[]"
                                   class="form-control qty-input"
                                   value="${row.qty}" readonly>
                        </td>

                        <td>
                            <input type="number"
                                   name="rate[]"
                                   class="form-control rate-input"
                                   value="${row.rate}">
                        </td>

                        <td>
                            <input type="number"
                                   name="gst[]"
                                   class="form-control gst-input"
                                   value="${row.gst}">
                            <input type="hidden"
                                   name="gst_amount[]"
                                   class="gst-amount-input">
                        </td>

                        <td>
                            <input type="number"
                                   name="amount[]"
                                   class="form-control amount-input"
                                   readonly>
                        </td>
                    </tr>
                    `;

            const $row = $(html);
            $container.append($row);
            calculateRow($row);
          });

          calculateTotals();
        },

        error: function () {
          alert("Error loading items");
        },
      });
    });

    /* ===============================
       RECALCULATE ON CHANGE
    =============================== */
    $(document).on(
      "input change",
      ".rate-input, .gst-input, .item-check",
      function () {
        const $row = $(this).closest(".item-row");
        calculateRow($row);
        calculateTotals();
      }
    );

    /* ===============================
       ROW CALCULATION
    =============================== */
    function calculateRow($row) {
      if (!$row.find(".item-check").is(":checked")) {
        $row.find(".gst-amount-input").val("0.00");
        $row.find(".amount-input").val("0.00");
        return;
      }

      const qty = parseFloat($row.find(".qty-input").val()) || 0;
      const rate = parseFloat($row.find(".rate-input").val()) || 0;
      const gst = parseFloat($row.find(".gst-input").val()) || 0;

      const base = qty * rate;
      const gst_amt = (base * gst) / 100;
      const total = base + gst_amt;

      $row.find(".gst-amount-input").val(gst_amt.toFixed(3));
      $row.find(".amount-input").val(total.toFixed(3));
    }

    /* ===============================
       TOTAL CALCULATION
    =============================== */
    function calculateTotals() {
      let total_amount = 0;
      let total_gst = 0;

      $(".item-row").each(function () {
        if ($(this).find(".item-check").is(":checked")) {
          total_amount += parseFloat($(this).find(".amount-input").val()) || 0;
          total_gst += parseFloat($(this).find(".gst-amount-input").val()) || 0;
        }
      });

      $("#total_amount_display").text(total_amount.toFixed(3));
      $("#total_amount").val(total_amount.toFixed(3));

      $("#total_gst_amount_display").text(total_gst.toFixed(3));
      $("#total_gst_amount").val(total_gst.toFixed(3));
    }

    /* ===============================
       FORM VALIDATION
    =============================== */
    $("form").on("submit", function () {
      if ($(".item-check:checked").length === 0) {
        alert("Please select at least one item");
        return false;
      }
    });
  });
</script>
