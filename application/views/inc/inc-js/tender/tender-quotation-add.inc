
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
                            <input type="hidden" name="category_id[]"          value="${row.category_id}">
                            <input type="hidden" name="item_id[]"              value="${row.item_id}">
                        `;

                        const itemHtml = `
                            <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
                                <h5 class="text-primary mb-3">Item Details ${i + 1}</h5>
                                <div class="row">
                                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                                        <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="${i}">
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group"><label>Category Name</label>
                                            <input type="text" class="form-control" value="${row.category_name}" readonly>
                                        </div>
                                        <div class="form-group"><label>Item Name</label>
                                            <input type="text" class="form-control" value="${row.item_name}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group"><label>Item Description</label>
                                            <textarea name="item_desc[]" class="form-control desc-textarea" rows="3">${row.item_desc}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-6"><div class="form-group"><label>UOM</label>
                                                <input type="text" name="uom[]" class="form-control" value="${row.uom}" readonly></div></div>
                                            <div class="col-md-6"><div class="form-group"><label>Quantity</label>
                                                <input type="number" step="0.01" name="qty[]" class="form-control qty-input" value="${row.qty}" readonly></div></div>

                                            <div class="col-md-6"><div class="form-group"><label>Rate</label>
                                                <input type="number" step="0.01" name="rate[]" class="form-control rate-input" value="${row.rate ?? 0}"></div></div>
                                            <div class="col-md-6"><div class="form-group"><label>VAT %</label>
                                                <select name="gst[]" class="form-control vat-dropdown">
                                                    <option value="">Select</option>
                                                    <?php foreach($gst_opt as $gid => $pct): ?>
                                                        <option value="<?= $pct; ?>"><?= $pct; ?>%</option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div></div>

                                            <div class="col-md-12"><div class="form-group"><label>Amount</label>
                                                <input type="number" step="0.01" name="amount[]" class="form-control amount-input" value="0.00" readonly></div></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- hidden fields (index = ${i}) -->
                                ${hiddenFields}
                            </div>`;
                        $container.append(itemHtml);
                    });

                    /* ---- total row (unchanged) ---- */
                    $container.append(` ... your total-box HTML ... `);
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
        const $row   = $(this).closest(".item-card");
        const qty    = parseFloat($row.find(".qty-input").val()) || 0;
        const rate   = parseFloat($row.find(".rate-input").val()) || 0;
        const gst    = parseFloat($row.find(".vat-dropdown").val()) || 0;

        const amount = qty * rate * (1 + gst/100);
        $row.find(".amount-input").val(amount.toFixed(2));

        calculateTotalAmount();
    });

    function calculateTotalAmount() {
        let total = 0;
        $(".amount-input").each(function () { total += parseFloat($(this).val()) || 0; });
        $("#total_amount").text(total.toFixed(2));
    }

    /* --------------------------------------------------------------
       3. **IMPORTANT** – when a checkbox is unchecked we *remove* its
           hidden fields so they are never posted.
       -------------------------------------------------------------- */
    $(document).on("change", ".item-check", function () {
        const $card = $(this).closest(".item-card");
        if (!$(this).is(":checked")) {
            // remove hidden fields that belong to this row
            $card.find("input[type=hidden][name^='tender_enquiry_item_id']," +
                       "input[type=hidden][name^='category_id']," +
                       "input[type=hidden][name^='item_id']").remove();
        } else {
            // if they were removed before, re-add them (safety)
            const idx = $(this).val();   // the original i index
            const row = <?php echo json_encode($res ?? []); ?>[idx]; // NOT needed – we keep them in DOM
            // (the hidden fields are already present when the row is built)
        }
    });
});
</script>