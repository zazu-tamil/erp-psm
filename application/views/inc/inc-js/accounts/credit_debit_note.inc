<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
var base_url = '<?php echo base_url(); ?>';
$(document).ready(function() {
    if ($('.select2').length > 0) {
        $('.select2').select2();
    }

    if ($('#party_type').val() && $('#tender_enquiry_id').val()) {
        loadInvoices();
    }

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

    $(".srch_enq_id").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: base_url + "tender/tender_enquiry_id_search",
                type: "POST",
                data: { search: request.term },
                dataType: "json",
                success: function (data) {
                    response(data);
                },
            });
        },
        minLength: 1,
        select: function (event, ui) {
            $("#tender_enquiry_id").val(ui.item.tender_enquiry_id);
            $(this).val(ui.item.label);
            
            if (ui.item.company_id) {
                $('#company_id').val(ui.item.company_id);
            }
            
            var currentType = $('#party_type').val();
            // Auto-select Customer only if party type is not set to Supplier
            if (currentType !== 'Supplier') {
                if (ui.item.customer_id) {
                    $('#party_type').val('Customer').trigger('change');
                    $('#customer_id').val(ui.item.customer_id);
                }
                loadInvoices();
            } else {
                // Try to auto-select unique supplier
                $.ajax({
                    url: base_url + 'credit-debit-note-get-enquiry-supplier',
                    type: 'POST',
                    dataType: 'json',
                    data: { enquiry_id: ui.item.tender_enquiry_id },
                    success: function(res) {
                        if (res.status === 'success' && res.vendor_id) {
                            $('#supplier_id').val(res.vendor_id);
                        }
                        loadInvoices();
                    },
                    error: function() {
                        loadInvoices();
                    }
                });
            }
            
            return false;
        }
    });

    $('#customer_id, #supplier_id').on('change', function() {
        loadInvoices();
    });

    function loadInvoices() {
        var pType = $('#party_type').val();
        var pId = (pType == 'Customer') ? $('#customer_id').val() : $('#supplier_id').val();
        var enqId = $('#tender_enquiry_id').val();

        if (pType && enqId) {
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

            // Auto fetch items
            var pType = $('#party_type').val();
            $.ajax({
                url: base_url + 'credit-debit-note-get-invoice-items',
                type: 'POST',
                dataType: 'json',
                data: {
                    party_type: pType,
                    invoice_id: opt.val()
                },
                success: function(res) {
                    if (res.status == 'success' && res.data.length > 0) {
                        $('#itemTable tbody').empty();
                        $.each(res.data, function(i, item) {
                            addRow(item);
                        });
                        calculateAmounts();
                    }
                }
            });

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
        var uom = data.uom || '';
        
        var qty = parseFloat(data.qty) || 0;
        var rate = parseFloat(data.rate) || 0;
        var tPercent = parseFloat(data.tax_percent) || 0;
        var baseAmt = qty * rate;
        var calcTAmt = (baseAmt * tPercent) / 100;
        
        var tAmt = data.tax_amount !== undefined ? data.tax_amount : calcTAmt.toFixed(2);
        var lTotal = data.line_total !== undefined ? data.line_total : (baseAmt + calcTAmt).toFixed(2);
        
        qty = data.qty || '';
        rate = data.rate || '';
        tPercent = data.tax_percent || '0';

        var tr = '<tr>' +
            '<td><i class="fa fa-arrows"></i></td>' +
            '<td><input type="text" name="item_code[]" class="form-control input-sm" required value="' + code + '"></td>' +
            '<td><input type="text" name="description[]" class="form-control input-sm" required value="' + desc + '"></td>' +
            '<td><input type="text" name="hsn_sac[]" class="form-control input-sm" value="' + hsn + '"></td>' +
            '<td><input type="number" name="qty[]" class="form-control input-sm calc-item" step="any" min="0" required value="' + qty + '"></td>' +
            '<td><input type="text" name="uom[]" class="form-control input-sm" required value="' + uom + '"></td>' +
            '<td><input type="number" name="rate[]" class="form-control input-sm calc-item" step="any" min="0" required value="' + rate + '"></td>' +
            '<td><input type="number" name="tax_percent[]" class="form-control input-sm calc-item" step="any" min="0" value="' + tPercent + '"></td>' +
            '<td><input type="text" name="item_tax_amount[]" class="form-control input-sm item_tax_amount" readonly value="' + tAmt + '"></td>' +
            '<td><input type="text" name="line_total[]" class="form-control input-sm line_total" readonly value="' + lTotal + '"></td>' +
            '<td><button type="button" class="btn btn-danger btn-xs btnRemoveItem"><i class="fa fa-trash"></i></button></td>' +
            '</tr>';
            
        var $tr = $(tr);

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
        var taxPercent = parseFloat(tr.find('input[name="tax_percent[]"]').val()) || 0;

        var baseAmount = qty * rate;
        var taxAmt = (baseAmount * taxPercent) / 100;
        var lineTotal = baseAmount + taxAmt;

        tr.find('input[name="item_tax_amount[]"]').val(taxAmt.toFixed(2));
        tr.find('input[name="line_total[]"]').val(lineTotal.toFixed(2));

        calculateAmounts();
    });

    $(document).on('keyup change', '.calc-total, #adjustment_amount', function() {
        calculateAmounts();
    });

    function calculateAmounts() {
        var subTotal = 0;
        var totalTax = 0;

        $('#itemTable tbody tr').each(function() {
            var qty = parseFloat($(this).find('input[name="qty[]"]').val()) || 0;
            var rate = parseFloat($(this).find('input[name="rate[]"]').val()) || 0;
            var taxPercent = parseFloat($(this).find('input[name="tax_percent[]"]').val()) || 0;

            var baseAmount = qty * rate;
            var taxAmt = (baseAmount * taxPercent) / 100;

            subTotal += baseAmount;
            totalTax += taxAmt;
        });

        $('#subtotal').val(subTotal.toFixed(2));
        $('#taxable_amount').val(subTotal.toFixed(2));
        $('#tax_amount').val(totalTax.toFixed(2));

        var roundOff = parseFloat($('#roundoff').val()) || 0;
        var totalAmount = subTotal + totalTax + roundOff;

        $('#total_amount').val(totalAmount.toFixed(2));
        calculateAdjustments();
    }

    function calculateAdjustments() {
        var totalAmount = parseFloat($('#total_amount').val()) || 0;
        var adjust = parseFloat($('#adjustment_amount').val()) || 0;
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: res.msg,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = base_url + 'credit-debit-note-list';
                    });
                } else {
                    Swal.fire('Error!', res.msg, 'error');
                    submitBtn.html(btnHtml).prop('disabled', false);
                }
            },
            error: function() {
                Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                submitBtn.html(btnHtml).prop('disabled', false);
            }
        });
    });
});

function deleteNote(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You will not be able to recover this Credit / Debit Note!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'credit-debit-note-delete/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.status == 'success') {
                        Swal.fire('Deleted!', res.msg, 'success').then(function() {
                            window.location.href = base_url + 'credit-debit-note-list';
                        });
                    } else {
                        Swal.fire('Error!', res.msg, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                }
            });
        }
    });
}
</script>
