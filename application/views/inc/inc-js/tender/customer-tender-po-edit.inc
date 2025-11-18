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

<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>

<script>
  $(document).ready(function () {
    calculateTotalAmount();

    const existingCompanyId = $("#srch_company_id").val();
    const existingCustomerId = $("#srch_customer_id").val();
    const existingEnquiryId = $("#srch_tender_enquiry_id").val();
    const existingQuotationId = $("#srch_quotation_no").val();

    if (existingCompanyId && existingCustomerId && existingEnquiryId) {
      loadQuotations(existingCompanyId, existingCustomerId, existingEnquiryId, existingQuotationId);
    }

    $("#srch_company_id").on("change", function () {
      const company_id = $(this).val();
      const $customerDropdown = $("#srch_customer_id");
      const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $quotationDropdown = $("#srch_quotation_no");
      const $container = $("#item_container");

      $enquiryDropdown.html('<option value="">Select Enquiry</option>').prop('disabled', true);
      $quotationDropdown.html('<option value="">Select Quotation</option>').prop('disabled', true);
      $container.html('');

      if (!company_id) {
        $customerDropdown.html('<option value="">Select Customer</option>').prop('disabled', false);
        return;
      }

      $.ajax({
        url: "<?php echo site_url('vendor/get_all_customers'); ?>",
        type: "POST",
        dataType: "json",
        success: function (res) {
          $customerDropdown.html('<option value="">Select Customer</option>');
          if (res.length > 0) {
            $.each(res, function (i, row) {
              $customerDropdown.append(
                $('<option></option>')
                  .attr('value', row.customer_id)
                  .text(row.customer_name)
              );
            });
            $customerDropdown.prop('disabled', false);
          } else {
            $customerDropdown.html('<option value="">No customers found</option>');
          }
        },
        error: function () {
          alert('Error loading customers');
        }
      });
    });

    $("#srch_customer_id").on("change", function () {
      const customer_id = $(this).val();
      const company_id = $("#srch_company_id").val();
      const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $quotationDropdown = $("#srch_quotation_no");
      const $container = $("#item_container");

      $enquiryDropdown.html('<option value="">Select Enquiry</option>').prop('disabled', true);
      $quotationDropdown.html('<option value="">Select Quotation</option>').prop('disabled', true);
      $container.html('');

      if (!customer_id || !company_id) return;

      $.ajax({
        url: "<?php echo site_url('tender/get_tender_enquiries_by_customer'); ?>",
        type: "POST",
        data: { 
          company_id: company_id,
          customer_id: customer_id 
        },
        dataType: "json",
        success: function (res) {
          if (res.length > 0) {
            $enquiryDropdown.prop('disabled', false);
            $.each(res, function (i, row) {
              $enquiryDropdown.append(
                $('<option></option>')
                  .attr('value', row.tender_enquiry_id)
                  .text(row.display)
              );
            });
          } else {
            $enquiryDropdown.html('<option value="">No enquiries found</option>');
          }
        },
        error: function () {
          alert('Error loading enquiries');
        }
      });
    });

    $("#srch_tender_enquiry_id").on("change", function () {
      const company_id = $("#srch_company_id").val();
      const customer_id = $("#srch_customer_id").val();
      const tender_enquiry_id = $(this).val();

      if (!company_id || !customer_id || !tender_enquiry_id) return;

      loadQuotations(company_id, customer_id, tender_enquiry_id, null);
    });

    function loadQuotations(company_id, customer_id, tender_enquiry_id, preSelectQuotationId) {
      const $quotationDropdown = $("#srch_quotation_no");

      $.ajax({
        url: "<?php echo site_url('tender/get_quotations_by_customer'); ?>",
        type: "POST",
        data: {
          company_id: company_id,
          customer_id: customer_id,
          tender_enquiry_id: tender_enquiry_id
        },
        dataType: "json",
        success: function (res) {
          $quotationDropdown.html('<option value="">Select Quotation</option>');
          
          if (res.length > 0) {
            $quotationDropdown.prop('disabled', false);
            $.each(res, function (i, row) {
              const isSelected = preSelectQuotationId && (row.tender_quotation_id == preSelectQuotationId) ? 'selected' : '';
              $quotationDropdown.append(
                $('<option></option>')
                  .attr('value', row.tender_quotation_id)
                  .attr('selected', isSelected)
                  .text(row.display)
              );
            });

            if (preSelectQuotationId) {
              $quotationDropdown.trigger('change');
            }
          } else {
            $quotationDropdown.html('<option value="">No quotations with "Won" status found</option>');
            $quotationDropdown.prop('disabled', true);
          }
        },
        error: function (xhr, status, error) {
          alert('Error loading quotations');
        }
      });
    }

    $("#srch_quotation_no").on("change", function () {
      const tender_quotation_id = $(this).val();
      const $container = $("#item_container");
      $container.html("");

      if (!tender_quotation_id) return;

      $.ajax({
        url: "<?php echo site_url('tender/get_quotation_items'); ?>",
        type: "POST",
        data: {
          tender_quotation_id: tender_quotation_id
        },
        dataType: "json",
        success: function (res) {
          if (res.length > 0) {
            $.each(res, function (i, row) {
              const hiddenFields = `
                <input type="hidden" name="tender_quotation_item_id[]" value="${row.tender_quotation_item_id}">
                <input type="hidden" name="category_id[]" value="${row.category_id}">
                <input type="hidden" name="item_id[]" value="${row.item_id}">
              `;

              let gstOptions = '<option value="">Select</option>';
              <?php foreach($gst_opt as $gid => $pct): ?>
                const gstValue = parseFloat('<?= $pct; ?>');
                const rowGst = parseFloat(row.gst);
                const isSelected = (gstValue === rowGst) ? 'selected' : '';
                gstOptions += `<option value="${gstValue}" ${isSelected}><?= $pct; ?>%</option>`;
              <?php endforeach; ?>

              const initialAmount = parseFloat(row.qty || 0) * parseFloat(row.rate || 0) * (1 + parseFloat(row.gst || 0) / 100);

              const itemHtml = `
                <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
                  <h5 class="text-primary mb-3">Item Details ${i + 1}</h5>
                  <div class="row">
                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                      <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="${i}" checked>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" class="form-control" value="${row.category_name || ''}" readonly>
                      </div>
                      <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" class="form-control" value="${row.item_name || ''}" readonly>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Item Description</label>
                        <textarea name="item_desc[]" class="form-control" rows="3">${row.item_desc || ''}</textarea>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>UOM</label>
                            <input type="text" name="uom[]" class="form-control" value="${row.uom || ''}" readonly>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" step="0.01" name="qty[]" class="form-control qty-input" value="${row.qty || 0}" readonly>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Rate</label>
                            <input type="number" step="0.01" name="rate[]" class="form-control rate-input" value="${row.rate || 0}">
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <label>VAT %</label>
                            <select name="gst[]" class="form-control vat-dropdown">
                              ${gstOptions}
                            </select>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group">
                            <label>Amount</label>
                            <input type="number" step="0.01" name="amount[]" class="form-control amount-input" value="${initialAmount.toFixed(2)}" readonly>
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

            calculateTotalAmount();
          } else {
            $container.html('<div class="alert alert-warning">No items found for this quotation.</div>');
          }
        },
        error: function (xhr, status, error) {
          alert('Error loading quotation items');
        }
      });
    });

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
        const value = parseFloat($(this).val()) || 0;
        total += value;
      });

      const $total = $("#total_amount");
      $total.fadeOut(100, function () {
        $(this).text(total.toFixed(2)).fadeIn(200);
      });
    }

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
    CKEDITOR.replace("editor1_edit", {
      height: 100,
      extraPlugins: "justify,colorbutton,font,table",
      removeButtons: "",
      toolbar: [
        {
          name: "clipboard",
          items: ["Cut", "Copy", "Paste", "PasteText", "PasteFromWord", "-", "Undo", "Redo"],
        },
        {
          name: "basicstyles",
          items: ["Bold", "Italic", "Underline", "Strike", "RemoveFormat", "CopyFormatting"],
        },
        {
          name: "paragraph",
          items: [
            "NumberedList", "BulletedList", "-", "Outdent", "Indent", "-",
            "Blockquote", "JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyBlock",
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