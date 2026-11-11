<!-- Select2 -->
<script src="<?php echo base_url() ?>asset/bower_components/select2/dist/js/select2.full.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/select2/dist/css/select2.min.css">
<!-- Toastr -->
<link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/toastr/toastr.min.css">
<script src="<?php echo base_url() ?>asset/bower_components/toastr/toastr.min.js"></script>
<!-- SweetAlert -->
<script src="<?php echo base_url() ?>asset/plugins/sweetalert/sweetalert.min.js"></script>

<style>
    .select2-results__option[aria-selected="true"] {
        display: none !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3c8dbc;
        border-color: #367fa9;
        color: #fff;
        padding: 1px 10px;
        margin-top: 4px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff !important;
        margin-right: 5px;
    }
</style>

<script>
    $(document).ready(function() {
        var allCustomersHtml = $('#customer_id').html();
        window.isEditing = false;

        // Initialize Select2
        $('.select2').each(function() {
            var $this = $(this);
            var isMulti = $this.prop('multiple');
            var config = {
                width: '100%',
                closeOnSelect: isMulti ? true : true,
                placeholder: $this.data('placeholder') || 'Select Option'
            };
            if ($this.closest('#addModal').length > 0) {
                config.dropdownParent = $('#addModal');
            }
            $this.select2(config);
        });

        // Account Type Toggle
        function handleAcTypeChange(val) {
            if (val === 'Bank') {
                $('#bank_div').show();
                $('#bank_id').prop('required', true);
                $('#bank_pay_type_div').show();
                var payType = $('input[name="pay_type"]:checked').val() || 'Online';
                if (payType === 'Cheque') {
                    $('#cheque_fields_div').show();
                    $('#cheque_date, #cheque_no, #cheque_bank').prop('required', true);
                } else {
                    $('#cheque_fields_div').hide();
                    $('#cheque_date, #cheque_no, #cheque_bank').prop('required', false);
                }
                $('#cash_category_div').hide();
                $('#cash_category_id').val('').trigger('change.select2').prop('required', false);
            } else if (val === 'Cash') {
                $('#bank_div').hide();
                $('#bank_id').val('').trigger('change.select2').prop('required', false);
                $('#bank_pay_type_div').hide();
                $('#cheque_fields_div').hide();
                $('#pay_type_online').prop('checked', true);
                $('#cheque_date, #cheque_no, #cheque_bank').val('').prop('required', false);
                $('#cash_category_div').show();
                $('#cash_category_id').prop('required', true);
            } else {
                $('#bank_div').hide();
                $('#bank_id').val('').trigger('change.select2').prop('required', false);
                $('#bank_pay_type_div').hide();
                $('#cheque_fields_div').hide();
                $('#pay_type_online').prop('checked', true);
                $('#cheque_date, #cheque_no, #cheque_bank').val('').prop('required', false);
                $('#cash_category_div').hide();
                $('#cash_category_id').val('').trigger('change.select2').prop('required', false);
            }
        }

        $('#ac_type_opt').on('change', function() {
            handleAcTypeChange($(this).val());
        });

        $('input[name="pay_type"]').on('change', function() {
            if ($('#ac_type_opt').val() === 'Bank' && $(this).val() === 'Cheque') {
                $('#cheque_fields_div').show();
                $('#cheque_date, #cheque_no, #cheque_bank').prop('required', true);
            } else {
                $('#cheque_fields_div').hide();
                $('#cheque_date, #cheque_no, #cheque_bank').prop('required', false);
            }
        });

        function fetchInvoices(tender_id, customer_id, po_id, selected_ids, callback) {
            if (!customer_id) {
                $('#invoice_ids').html('').trigger('change');
                if (typeof callback === 'function') callback();
                return;
            }

            var selArr = [];
            if (Array.isArray(selected_ids)) {
                selArr = selected_ids.map(String).filter(Boolean);
            } else if (selected_ids) {
                selArr = String(selected_ids).split(',').map(function(s) { return s.trim(); }).filter(Boolean);
            }

            $.ajax({
                url: '<?php echo site_url("payment/get_customer_invoices_ajax"); ?>',
                type: 'POST',
                data: {
                    tender_enquiry_id: tender_id,
                    customer_id: customer_id,
                    tender_po_id: po_id,
                    selected_invoices: selArr.join(',')
                },
                dataType: 'json',
                success: function(response) {
                    var html = '';
                    $.each(response, function(i, item) {
                        var isSelected = selArr.indexOf(String(item.tender_enq_invoice_id)) !== -1;
                        var label = item.invoice_no;
                        if (item.invoice_date) {
                            label += ' (' + item.invoice_date;
                            if (item.total_amount) {
                                label += ' - Amt: ' + parseFloat(item.total_amount).toFixed(3);
                            }
                            label += ')';
                        }
                        html += '<option value="' + item.tender_enq_invoice_id + '" ' + (isSelected ? 'selected="selected"' : '') + '>' + label + '</option>';
                    });

                    $('#invoice_ids').html(html);
                    $('#invoice_ids').val(selArr).trigger('change');
                    $('#invoice_ids').select2('close');

                    if (typeof callback === 'function') {
                        callback();
                    }
                },
                error: function() {
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            });
        }

        $('#tender_enquiry_id').on('change', function() {
            var tender_id = $(this).val();

            if (!window.isEditing) {
                $('#tender_po_id').html('<option value="">Select PO</option>').trigger('change.select2');
                $('#invoice_ids').html('').trigger('change');
            }

            if (tender_id) {
                $.ajax({
                    url: '<?php echo site_url("payment/get_customer_by_tender_id_ajax"); ?>',
                    type: 'POST',
                    data: {tender_enquiry_id: tender_id},
                    dataType: 'json',
                    success: function(cust) {
                        if (cust && cust.customer_id) {
                            if (!window.isEditing) {
                                $('#customer_id').val(cust.customer_id).trigger('change');
                            }
                        }
                    }
                });
            }
        });

        $('#customer_id').on('change', function() {
            var tender_id = $('#tender_enquiry_id').val();
            var customer_id = $(this).val();

            if (!window.isEditing) {
                $('#tender_po_id').html('<option value="">Select PO</option>').trigger('change');
                $('#invoice_ids').html('').trigger('change');
            }

            if (customer_id) {
                $.ajax({
                    url: '<?php echo site_url("payment/get_customer_po_by_customer_ajax"); ?>',
                    type: 'POST',
                    data: {
                        tender_enquiry_id: tender_id,
                        customer_id: customer_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        var html = '<option value="">Select PO</option>';
                        $.each(response, function(i, item) {
                            html += '<option value="' + item.tender_po_id + '">' + item.po_no + '</option>';
                        });
                        $('#tender_po_id').html(html);

                        if (window.isEditing && window.editPo) {
                            $('#tender_po_id').val(window.editPo).trigger('change.select2');
                            window.editPo = null;
                        } else if (!window.isEditing) {
                            $('#tender_po_id').val('').trigger('change.select2');
                        }

                        fetchInvoices(tender_id, customer_id, $('#tender_po_id').val(), window.editInvoices);
                    }
                });
            }
        });

        $('#tender_po_id').on('change', function() {
            var tender_id = $('#tender_enquiry_id').val();
            var customer_id = $('#customer_id').val();
            var po_id = $(this).val();

            if (!window.isEditing) {
                fetchInvoices(tender_id, customer_id, po_id, []);
            }
        });

        // Add Button
        $('#btnAdd').on('click', function() {
            window.isEditing = false;
            window.editCustomer = null;
            window.editPo = null;
            window.editInvoices = null;

            $('#mode').val('Add');
            $('#adv_payment_id').val('');
            $('#addModalLabel').text('Add Customer Advance Payment');
            $('#frmAdd')[0].reset();

            $('#tender_enquiry_id').val('').trigger('change.select2');
            $('#customer_id').html(allCustomersHtml).val('').trigger('change.select2');
            $('#tender_po_id').html('<option value="">Select PO</option>').val('').trigger('change.select2');
            $('#invoice_ids').html('').val([]).trigger('change.select2');

            $('#ac_type_opt').val('Bank').trigger('change');
            $('#bank_id').val('').trigger('change.select2');
            $('#cash_category_id').val('').trigger('change.select2');
            $('#pay_type_online').prop('checked', true);
            $('#cheque_date, #cheque_no, #cheque_bank').val('').prop('required', false);
            $('#cheque_fields_div').hide();
            $('#bank_pay_type_div').show();
            $('#status').val('Active').trigger('change.select2');
            $('#remarks').val('');

            $('#addModal').modal('show');
        });

        // Edit Button
        $(document).on('click', '.edit_record', function() {
            window.isEditing = true;

            var id = $(this).data('id');
            var tender_id = $(this).data('tender');
            var customer_id = $(this).data('customer');
            var po_id = $(this).data('po');
            var invoices = $(this).data('invoices');
            var ac_type = $(this).data('ac');
            var bank_id = $(this).data('bank');
            var cash_id = $(this).data('cash');
            var pay_type = $(this).data('pay-type') || 'Online';
            var cheque_date = $(this).data('cheque-date') || '';
            var cheque_no = $(this).data('cheque-no') || '';
            var cheque_bank = $(this).data('cheque-bank') || '';
            var date = $(this).data('date');
            var amt = $(this).data('amt');
            var remarks = $(this).data('remarks');
            var status = $(this).data('status');

            $('#mode').val('Edit');
            $('#adv_payment_id').val(id);
            $('#addModalLabel').text('Edit Customer Advance Payment');

            window.editCustomer = customer_id;
            window.editPo = po_id;
            window.editInvoices = invoices;

            $('#tender_enquiry_id').val(tender_id).trigger('change.select2');
            $('#customer_id').val(customer_id).trigger('change.select2');

            // Trigger cascading PO and invoices
            $.ajax({
                url: '<?php echo site_url("payment/get_customer_po_by_customer_ajax"); ?>',
                type: 'POST',
                data: {
                    tender_enquiry_id: tender_id,
                    customer_id: customer_id
                },
                dataType: 'json',
                success: function(response) {
                    var html = '<option value="">Select PO</option>';
                    $.each(response, function(i, item) {
                        html += '<option value="' + item.tender_po_id + '">' + item.po_no + '</option>';
                    });
                    $('#tender_po_id').html(html);
                    $('#tender_po_id').val(po_id).trigger('change.select2');

                    fetchInvoices(tender_id, customer_id, po_id, invoices, function() {
                        window.isEditing = false;
                    });
                }
            });

            $('#ac_type_opt').val(ac_type).trigger('change');
            if (ac_type === 'Bank') {
                $('#bank_id').val(bank_id).trigger('change.select2');
                $('#bank_pay_type_div').show();
                $("input[name='pay_type'][value='" + pay_type + "']").prop('checked', true);
                if (pay_type === 'Cheque') {
                    $('#cheque_fields_div').show();
                    $('#cheque_date').val(cheque_date).prop('required', true);
                    $('#cheque_no').val(cheque_no).prop('required', true);
                    $('#cheque_bank').val(cheque_bank).prop('required', true);
                } else {
                    $('#cheque_fields_div').hide();
                    $('#cheque_date, #cheque_no, #cheque_bank').val('').prop('required', false);
                }
            } else if (ac_type === 'Cash') {
                $('#cash_category_id').val(cash_id).trigger('change.select2');
                $('#bank_pay_type_div, #cheque_fields_div').hide();
                $('#pay_type_online').prop('checked', true);
                $('#cheque_date, #cheque_no, #cheque_bank').val('').prop('required', false);
            } else {
                $('#bank_pay_type_div, #cheque_fields_div').hide();
                $('#pay_type_online').prop('checked', true);
                $('#cheque_date, #cheque_no, #cheque_bank').val('').prop('required', false);
            }

            $('#adv_payment_date').val(date);
            $('#adv_payment_amt').val(amt);
            $('#remarks').val(remarks);
            $('#status').val(status).trigger('change.select2');

            $('#addModal').modal('show');
        });

        $('#btnCancel').on('click', function() {
            $('#frmAdd')[0].reset();
            $('#mode').val('Add');
            $('#adv_payment_id').val('');
            $('#customer_id').html(allCustomersHtml);
            $('#addModal .select2').val('').trigger('change.select2');
            $('#invoice_ids').html('').val([]).trigger('change.select2');
            $('#pay_type_online').prop('checked', true);
            $('#cheque_date, #cheque_no, #cheque_bank').val('').prop('required', false);
            $('#cheque_fields_div').hide();
        });

        // Delete confirmation
        $(document).on('click', '.del_record', function() {
            var del_id = $(this).val();
            swal({
                title: "Are you sure?",
                text: "Do you really want to delete this Customer Advance Payment?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then(function(willDelete) {
                if (willDelete) {
                    $.ajax({
                        url: '<?php echo site_url("customer-adv-payment"); ?>',
                        type: 'POST',
                        data: {
                            mode: 'Delete',
                            del_id: del_id
                        },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status) {
                                swal("Deleted!", "Customer Advance Payment has been deleted.", "success")
                                .then(function() {
                                    location.reload();
                                });
                            }
                        }
                    });
                }
            });
        });
    });
</script>
