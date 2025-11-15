<script>
$(document).ready(function () {

  /* --------------------------------------------------------------
     1. Load items when Tender Enquiry changes
     -------------------------------------------------------------- */
  $("#srch_tender_enquiry_id").on("change", function () {
    const tender_enquiry_id = $(this).val();
    const $container = $("#item_container");
    $container.html("");

    if (!tender_enquiry_id) return;

    $.ajax({
      url: "<?php echo site_url('vendor/get_data'); ?>",
      type: "POST",
      data: { tbl: "get-tender-enquiry-item-list-rate", id: tender_enquiry_id },
      dataType: "json",
      success: function (res) {
        if (res.length > 0) {
          $.each(res, function (i, row) {
            /* ---- hidden fields that will be sent ONLY for checked rows ---- */
            const hiddenFields = `
              <input type="hidden" name="tender_enquiry_item_id[]" value="${row.tender_enquiry_item_id}">
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
                      <label>Category Name</label>
                      <input type="text" class="form-control" value="${row.category_name}" readonly>
                    </div>
                    <div class="form-group">
                      <label>Item Name</label>
                      <input type="text" class="form-control" value="${row.item_name}" readonly>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Item Description</label>
                      <textarea name="item_desc[]" class="form-control desc-textarea" rows="3">${row.item_desc}</textarea>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>UOM</label>
                          <input type="text" name="uom[]" class="form-control" value="${row.uom}" readonly>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Quantity</label>
                          <input type="number" step="0.01" name="qty[]" class="form-control qty-input" value="${row.qty}" readonly>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Rate</label>
                          <input type="number" step="0.01" name="rate[]" class="form-control rate-input" value="${row.rate ?? 0}">
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

          /* ---- Total Row ---- */
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
        } else {
          $container.html('<div class="alert alert-warning">No items found for this tender enquiry.</div>');
        }
      }
    });
  });

  /* --------------------------------------------------------------
     2. Amount calculation (rate / gst change)
     -------------------------------------------------------------- */
  $(document).on("input change", ".rate-input, .vat-dropdown", function () {
    const $row = $(this).closest(".item-card");
    const qty  = parseFloat($row.find(".qty-input").val()) || 0;
    const rate = parseFloat($row.find(".rate-input").val()) || 0;
    const gst  = parseFloat($row.find(".vat-dropdown").val()) || 0;

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

  /* --------------------------------------------------------------
     3. Remove hidden fields if unchecked
     -------------------------------------------------------------- */
  $(document).on("change", ".item-check", function () {
    const $card = $(this).closest(".item-card");
    if (!$(this).is(":checked")) {
      $card.find("input[type=hidden][name^='tender_enquiry_item_id'], " +
                 "input[type=hidden][name^='category_id'], " +
                 "input[type=hidden][name^='item_id']").remove();
    }
  });

});
</script>
