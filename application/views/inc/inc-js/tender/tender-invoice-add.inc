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
            }
        });

    });

    /* ================================================================
       4. QUOTATION CHANGE - Load Items from Selected Quotation
       ================================================================ */
    $("#srch_tender_po_id").on("change", function () {
      const tender_po_id = $(this).val();
      const $container = $("#item_container");
      $container.html("");

      if (!tender_po_id) return;

      $.ajax({
        url: "<?php echo site_url('tender/get_tender_po_invoice_load_items'); ?>",
        type: "POST",
        data: {
          tender_po_id: tender_po_id,
        },
        dataType: "json",
        success: function (res) {
          if (res.length > 0) {
            $.each(res, function (i, row) {
              const hiddenFields = `
                <input type="hidden" name="tender_po_item_id[]" value="${row.tender_po_item_id}">
                <input type="hidden" name="category_id[]" value="${row.category_id}">
                <input type="hidden" name="item_id[]" value="${row.item_id}">
              `;

              const itemHtml = `
                <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
                  <h5 class="text-primary mb-3">Item Details ${i + 1}</h5>
                  <div class="row">
                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                      <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="${i}">
                    </div>

                    <div class="col-md-3"> 
                      <div class="form-group">
                        <label>Item Code</label>
                        <input type="text" class="form-control" value="${
                          row.item_code || ""
                        }" readonly>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Item Description</label>
                        <textarea name="item_desc[]" class="form-control desc-textarea" rows="3">${
                          row.item_desc || ""
                        }</textarea>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>UOM</label>
                            <input type="text" name="uom[]" class="form-control" value="${
                              row.uom || ""
                            }" readonly>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" step="0.01" name="qty[]" class="form-control qty-input" value="${
                              row.qty || 0
                            }" readonly>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Rate</label>
                            <input type="number" step="0.01" name="rate[]" class="form-control rate-input" value="${
                              row.rate || 0
                            }">
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <label>VAT %</label>
                            <select name="gst[]" class="form-control vat-dropdown">
                              <option value="">Select</option>
                              <?php foreach($gst_opt as $gid => $pct): ?>
                                <option value="<?= $pct; ?>"><?= $pct; ?>%</option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group">
                            <label>Amount</label>
                            <input type="number" step="0.01" name="amount[]" class="form-control amount-input" value="0.00" readonly>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  ${hiddenFields}
                </div>
              `;
              $container.append(itemHtml);
            });

            // Add total row
            $container.append(`
              <div class="total-wrapper mt-4 mb-4">
                <div class="total-box shadow-sm">
                  <h5 class="mb-0">
                    <i class="fa fa-calculator text-success me-2"></i>
                    <strong>Total Amount:</strong>
                    <span class="text-primary">₹ <span id="total_amount">0.00</span></span>
                  </h5>
                </div>
              </div>

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
                #total_amount {
                  font-size: 1.4rem;
                  font-weight: 700;
                  margin-left: 5px;
                }
                @media (max-width: 768px) {
                  .total-wrapper { justify-content: center; }
                  .total-box { text-align: center; min-width: 220px; }
                }
              </style>
            `);

            // Calculate totals on load
            calculateTotalAmount();
          } else {
            $container.html(
              '<div class="alert alert-warning">No items found for this quotation.</div>'
            );
          }
        },
        error: function () {
          alert("Error loading quotation items");
        },
      });
    });

    /* ================================================================
       5. Amount Calculation (Rate / VAT change)
       ================================================================ */
    $(document).on("input change", ".rate-input, .vat-dropdown", function () {
      const $row = $(this).closest(".item-card");
      const qty = parseFloat($row.find(".qty-input").val()) || 0;
      const rate = parseFloat($row.find(".rate-input").val()) || 0;
      const gst = parseFloat($row.find(".vat-dropdown").val()) || 0;

      const amount = qty * rate * (1 + gst / 100);
      $row.find(".amount-input").val(amount.toFixed(2));

      calculateTotalAmount();
    });

    function calculateTotalAmount() {
      let total = 0;
      $(".amount-input").each(function () {
        total += parseFloat($(this).val()) || 0;
      });
      $("#total_amount").text(total.toFixed(2));
    }

    /* ================================================================
       6. Checkbox Change - Add/Remove Hidden Fields
       ================================================================ */
    $(document).on("change", ".item-check", function () {
      const $card = $(this).closest(".item-card");
      if (!$(this).is(":checked")) {
        $card
          .find(
            "input[type=hidden][name^='tender_quotation_item_id'], " +
              "input[type=hidden][name^='category_id'], " +
              "input[type=hidden][name^='item_id']"
          )
          .remove();
      }
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
