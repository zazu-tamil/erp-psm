<script>
$(document).ready(function() {
    // Show/hide party selection
    $('#party_type').on('change', function() {
        var pType = $(this).val();
        if (pType == 'Customer') {
            $('#customer_div').show();
            $('#supplier_div').hide();
            $('#customer_id').prop('required', true);
            $('#supplier_id').prop('required', false);
        } else if (pType == 'Supplier') {
            $('#customer_div').hide();
            $('#supplier_div').show();
            $('#customer_id').prop('required', false);
            $('#supplier_id').prop('required', true);
        } else {
            $('#customer_div').hide();
            $('#supplier_div').hide();
            $('#customer_id').prop('required', false);
            $('#supplier_id').prop('required', false);
        }
        loadInvoices();
    });

    $('#customer_id, #supplier_id, #tender_enquiry_id').on('change', function() {
        loadInvoices();
    });

    function loadInvoices() {
        var pType = $('#party_type').val();
        var pId = (pType == 'Customer') ? $('#customer_id').val() : $('#supplier_id').val();
        var enqId = $('#tender_enquiry_id').val();

        if (pType && pId && enqId) {
            $.ajax({
                url: base_url + 'credit-debit-note-get-invoices',
                type: 'POST',
                dataType: 'json',
                data: {
                    party_type: pType,
                    party_id: pId,
                    enquiry_id: enqId
                },
                success: function(res) {
                    var html = '<option value="">Select Invoice</option>';
                    if (res.status == 'success' && res.data.length > 0) {
                        $.each(res.data, function(i, v) {
                            html += '<option value="'+v.invoice_id+'" data-no="'+v.invoice_no+'" data-amt="'+v.total_amount+'">'+v.invoice_no+' ('+v.invoice_date+') - Amt: '+v.total_amount+'</option>';
                        });
                    }
                    $('#reference_invoice_id').html(html);
                    
                    // Auto select for edit mode
                    var autoSel = $('#reference_invoice_id').attr('data-selected-on-load');
                    if (autoSel) {
                        $('#reference_invoice_id').val(autoSel);
                        $('#reference_invoice_id').removeAttr('data-selected-on-load');
                    }
                }
            });
        } else {
            $('#reference_invoice_id').html('<option value="">Select Invoice</option>');
        }
    }

    $('#reference_invoice_id').on('change', function() {
        var opt = $(this).find(':selected');
        if (opt.val()) {
            $('#reference_invoice_no').val(opt.data('no'));
            var amt = parseFloat(opt.data('amt')) || 0;
            $('#outstanding_invoice_amount').val(amt.toFixed(2));
        } else {
            $('#reference_invoice_no').val('');
            $('#outstanding_invoice_amount').val('0.00');
        }
        calculateAmounts();
    });

    // Dynamic Item Table
    var itemIndex = 0;
    
    function addRow(data) {
        data = data || {};
        var code = data.item_code || '';
        var desc = data.description || '';
        var hsn = data.hsn_sac || '';
        var qty = data.qty || '';
        var uom = data.uom || '';
        var rate = data.rate || '';
        var dPercent = data.discount_percent || '0';
        var dAmt = data.discount_amount || '0.00';
        var tPercent = data.tax_percent || '0';
        var tAmt = data.tax_amount || '0.00';
        var lTotal = data.line_total || '0.00';

        var taxSelect = '<select name="tax_percent[]" class="form-control input-sm calc-item">';
        taxSelect += taxOptionsHtml;
        taxSelect += '</select>';

        var tr = '<tr>' +
            '<td><i class="fa fa-arrows"></i></td>' +
            '<td><input type="text" name="item_code[]" class="form-control input-sm" required value="' + code + '"></td>' +
            '<td><input type="text" name="description[]" class="form-control input-sm" required value="' + desc + '"></td>' +
            '<td><input type="text" name="hsn_sac[]" class="form-control input-sm" value="' + hsn + '"></td>' +
            '<td><input type="number" name="qty[]" class="form-control input-sm calc-item" step="0.001" min="0" required value="' + qty + '"></td>' +
            '<td><input type="text" name="uom[]" class="form-control input-sm" required value="' + uom + '"></td>' +
            '<td><input type="number" name="rate[]" class="form-control input-sm calc-item" step="0.01" min="0" required value="' + rate + '"></td>' +
            '<td><input type="number" name="discount_percent[]" class="form-control input-sm calc-item" step="0.01" min="0" value="' + dPercent + '"></td>' +
            '<td><input type="text" name="item_discount_amount[]" class="form-control input-sm item_discount_amount" readonly value="' + dAmt + '"></td>' +
            '<td>' + taxSelect + '</td>' +
            '<td><input type="text" name="item_tax_amount[]" class="form-control input-sm item_tax_amount" readonly value="' + tAmt + '"></td>' +
            '<td><input type="text" name="line_total[]" class="form-control input-sm line_total" readonly value="' + lTotal + '"></td>' +
            '<td><button type="button" class="btn btn-danger btn-xs btnRemoveItem"><i class="fa fa-trash"></i></button></td>' +
            '</tr>';
            
        var $tr = $(tr);
        if (data.tax_percent !== undefined) {
            $tr.find('select[name="tax_percent[]"]').val(parseFloat(data.tax_percent));
        }
        $('#itemTable tbody').append($tr);
    }

    $('#btnAddItem').on('click', function() {
        addRow();
    });

    // Default first row or load edit items
    if (typeof initialEditItems !== 'undefined' && initialEditItems.length > 0) {
        $.each(initialEditItems, function(idx, item) {
            addRow(item);
        });
        
        // Also setup invoice selection logic if needed
        var refInvId = $('#reference_invoice_id').attr('data-selected');
        if (refInvId) {
            // we will need to re-select it after loadInvoices finishes
            $('#reference_invoice_id').attr('data-selected-on-load', refInvId);
        }
    } else {
        addRow();
    }

    $(document).on('click', '.btnRemoveItem', function() {
        $(this).closest('tr').remove();
        calculateAmounts();
    });

    $(document).on('keyup change', '.calc-item', function() {
        var tr = $(this).closest('tr');
        var qty = parseFloat(tr.find('input[name="qty[]"]').val()) || 0;
        var rate = parseFloat(tr.find('input[name="rate[]"]').val()) || 0;
        var discPercent = parseFloat(tr.find('input[name="discount_percent[]"]').val()) || 0;
        var taxPercent = parseFloat(tr.find('select[name="tax_percent[]"]').val()) || 0;

        var baseAmount = qty * rate;
        var discAmt = (baseAmount * discPercent) / 100;
        var taxable = baseAmount - discAmt;
        var taxAmt = (taxable * taxPercent) / 100;
        var lineTotal = taxable + taxAmt;

        tr.find('input[name="item_discount_amount[]"]').val(discAmt.toFixed(2));
        tr.find('input[name="item_tax_amount[]"]').val(taxAmt.toFixed(2));
        tr.find('input[name="line_total[]"]').val(lineTotal.toFixed(2));

        calculateAmounts();
    });

    $(document).on('keyup change', '.calc-total, #adjustment_amount', function() {
        calculateAmounts();
    });

    function calculateAmounts() {
        var subTotal = 0;
        var totalDisc = 0;
        var totalTax = 0;
        
        $('#itemTable tbody tr').each(function() {
            var qty = parseFloat($(this).find('input[name="qty[]"]').val()) || 0;
            var rate = parseFloat($(this).find('input[name="rate[]"]').val()) || 0;
            var dAmt = parseFloat($(this).find('input[name="item_discount_amount[]"]').val()) || 0;
            var tAmt = parseFloat($(this).find('input[name="item_tax_amount[]"]').val()) || 0;
            
            var baseAmount = qty * rate;
            subTotal += baseAmount;
            totalDisc += dAmt;
            totalTax += tAmt;
        });

        var taxableAmount = subTotal - totalDisc;
        var roundOff = parseFloat($('#roundoff').val()) || 0;
        var totalAmount = taxableAmount + totalTax + roundOff;

        $('#subtotal').val(subTotal.toFixed(2));
        $('#discount_amount').val(totalDisc.toFixed(2));
        $('#taxable_amount').val(taxableAmount.toFixed(2));
        $('#tax_amount').val(totalTax.toFixed(2));
        $('#total_amount').val(totalAmount.toFixed(2));

        // Adjustments logic
        // In this implementation, Adjust Amount defaults to 0. If user types it, it calculates Remaining Credit.
        var outstanding = parseFloat($('#outstanding_invoice_amount').val()) || 0;
        var adjust = parseFloat($('#adjustment_amount').val()) || 0;
        // Remaining Credit = Total Amount - Adjust Amount
        var remaining = totalAmount - adjust;
        $('#remaining_amount').val(remaining.toFixed(2));
    }

    // Form Submit
    $('#frmCreditDebitNote').on('submit', function(e) {
        e.preventDefault();
        
        // Ensure at least one item
        if($('#itemTable tbody tr').length == 0) {
            alert('Please add at least one item.');
            return false;
        }

        var formData = new FormData(this);
        var submitBtn = $(document.activeElement);
        var btnHtml = submitBtn.html();
        submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

        $.ajax({
            url: base_url + 'credit-debit-note-save',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                if (res.status == 'success') {
                    alert(res.msg);
                    window.location.href = base_url + 'credit-debit-note-list'; // Redirect to list page
                } else {
                    alert(res.msg);
                    submitBtn.html(btnHtml).prop('disabled', false);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                submitBtn.html(btnHtml).prop('disabled', false);
            }
        });
    });
});
</script>
