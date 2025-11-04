<script>
$(document).ready(function () {
    const tender_enquiry_id = $("#old_tender_enquiry_id").val();
    
    // Initialize grand total on page load
    calculateGrandTotal();
    
    // Initialize with saved items if tender enquiry is the same
    if (tender_enquiry_id) {
        // Items are already loaded from PHP, no AJAX call needed on initial load
    }

    // Handle tender enquiry change
    $("#srch_tender_enquiry_id").on("change", function () {
        const tender_enquiry_id = $(this).val();
        const old_tender_enquiry_id = $("#old_tender_enquiry_id").val();
        
        const selectedText = $(this).find("option:selected").text();
        const parts = selectedText.split(' -> ');
        
        if (parts.length >= 4) {
            const customerName = parts[3].trim();
            let customer_id = '';
            
            // Find customer ID from available options
            <?php foreach ($customer_opt as $id => $name): ?>
            if ("<?php echo $name; ?>" === customerName) {
                customer_id = '<?php echo $id; ?>';
            }
            <?php endforeach; ?>
            
            $("#srch_customer_id").val(customer_id);
        }
        
        // Only load new items if tender enquiry changed
        if (tender_enquiry_id && tender_enquiry_id != old_tender_enquiry_id) {
            loadItems(tender_enquiry_id);
        }
    });

    function loadItems(tender_enquiry_id) {
        $.ajax({
            url: '<?php echo site_url("vendor/get_data"); ?>',
            type: "POST",
            data: { tbl: "get-tender-enquiry-item-list-rate", id: tender_enquiry_id },
            dataType: "json",
            success: function (res) {
                $("#item_container").empty();
                
                if (res.length > 0) {
                    $.each(res, function (i, row) {
                        const itemHtml = `
                            <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
                                <h5 class="text-primary mb-3">Item Details ${i + 1}</h5>
                                <input type="hidden" name="vendor_rate_enquiry_item_id[]" value="">
                                <input type="hidden" name="tender_enquiry_item_id[]" value="${row.tender_enquiry_item_id}">
                                <input type="hidden" name="category_id[]" value="${row.category_id}">
                                <input type="hidden" name="item_id[]" value="${row.item_id}">
                                <div class="row">
                                    <div class="col-md-1">
                                        <div class="form-group" style="margin-top: 25px;">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="${i}" id="check_${i}" checked>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Category Name</label>
                                                    <input type="text" class="form-control" value="${row.category_name}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Item Name</label>
                                                    <input type="text" class="form-control" value="${row.item_name}" readonly>
                                                </div>
                                            </div>
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
                                                        <?php if(isset($gst_opt)): ?>
                                                            <?php foreach($gst_opt as $gst_id => $gst_pct): ?>
                                                                <option value="<?php echo $gst_pct; ?>"><?php echo $gst_pct; ?>%</option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Amount (Qty × Rate × VAT %)</label>
                                                    <input type="number" step="0.01" name="amount[]" class="form-control amount-input" value="0.00" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $("#item_container").append(itemHtml);
                    });
                } else {
                    $("#item_container").html('<div class="alert alert-warning">No items found.</div>');
                }
                
                calculateGrandTotal();
            },
            error: function() {
                alert('Error loading items.');
            }
        });
    }

    // Calculate amount when rate or VAT changes
    $(document).on("change", ".rate-input, .vat-dropdown", function() {
        const card = $(this).closest(".item-card");
        calculateAmount(card);
        calculateGrandTotal();
    });

    function calculateAmount(card) {
        const rateInput = card.find(".rate-input");
        const qtyInput = card.find(".qty-input");
        const vatDropdown = card.find(".vat-dropdown");
        const amountInput = card.find(".amount-input");

        const rate = parseFloat(rateInput.val()) || 0;
        const qty = parseFloat(qtyInput.val()) || 0;
        const vat = parseFloat(vatDropdown.val()) || 0;

        const baseAmount = qty * rate;
        const vatAmount = (baseAmount * vat) / 100;
        const finalAmount = baseAmount + vatAmount;

        amountInput.val(finalAmount.toFixed(2));
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        
        $(".item-card").each(function() {
            const isChecked = $(this).find(".item-check").is(":checked");
            if (isChecked) {
                const amount = parseFloat($(this).find(".amount-input").val()) || 0;
                grandTotal += amount;
            }
        });
        
        $("#grand_total").val(grandTotal.toFixed(2));
    }

    // Initialize - set opacity based on checkbox state on page load
    $(".item-card").each(function() {
        const isChecked = $(this).find(".item-check").is(":checked");
        if (!isChecked) {
            $(this).css("opacity", "0.6");
        } else {
            $(this).css("opacity", "1");
        }
    });

    // Handle checkbox change - toggle opacity and update grand total
    $(document).on("change", ".item-check", function() {
        const card = $(this).closest(".item-card");
        const isChecked = $(this).is(":checked");
        
        if (!isChecked) {
            card.css("opacity", "0.6");
        } else {
            card.css("opacity", "1");
        }
        
        calculateGrandTotal();
    });

    // Form submission - send all items with their checkbox state
    $("#frmadd").on("submit", function (e) {
        let hasChecked = false;
        
        $(".item-card").each(function () {
            const isChecked = $(this).find(".item-check").is(":checked");
            if (isChecked) {
                hasChecked = true;
            }
        });
        
        if (!hasChecked) {
            alert("Please select at least one item.");
            e.preventDefault();
            return false;
        }
    });
});
</script>