<!-- Select2 -->
<script src="<?php echo base_url() ?>asset/bower_components/select2/dist/js/select2.full.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/select2/dist/css/select2.min.css">
<!-- Toastr -->
<link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/toastr/toastr.min.css">
<script src="<?php echo base_url() ?>asset/bower_components/toastr/toastr.min.js"></script>
<!-- SweetAlert -->
<script src="<?php echo base_url() ?>asset/plugins/sweetalert/sweetalert.min.js"></script>

<style>
    /* Hide already selected options in Select2 dropdown results */
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
        // Initialize Select2 with modal parent and closeOnSelect for multiple select
        $('.select2').each(function() {
            var $this = $(this);
            var isMulti = $this.prop('multiple');
            $this.select2({
                width: '100%',
                dropdownParent: $('#addModal'),
                closeOnSelect: isMulti ? true : true,
                placeholder: $this.data('placeholder') || 'Select Option'
            });
        });

        function fetchInvoices(tender_id, vendor_id, po_id, selected_ids, callback) {
            if (!vendor_id) {
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
                url: '<?php echo site_url("vendor/get_vendor_invoices_ajax"); ?>',
                type: 'POST',
                data: {
                    tender_enquiry_id: tender_id,
                    vendor_id: vendor_id,
                    vendor_po_id: po_id,
                    selected_invoices: selArr.join(',')
                },
                dataType: 'json',
                success: function(response) {
                    var html = '';
                    $.each(response, function(i, item) {
                        var isSelected = selArr.indexOf(String(item.vendor_purchase_invoice_id)) !== -1;
                        var label = item.invoice_no;
                        if (item.invoice_date) {
                            label += ' (' + item.invoice_date;
                            if (item.total_amount) {
                                label += ' - Amt: ' + parseFloat(item.total_amount).toFixed(2);
                            }
                            label += ')';
                        }
                        html += '<option value="' + item.vendor_purchase_invoice_id + '" ' + (isSelected ? 'selected="selected"' : '') + '>' + label + '</option>';
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
                $('#vendor_id').html('<option value="">Select Vendor</option>').trigger('change');
                $('#vendor_po_id').html('<option value="">Select PO</option>').trigger('change');
                $('#invoice_ids').html('').trigger('change');
            }

            if (tender_id) {
                $.ajax({
                    url: '<?php echo site_url("vendor/get_vendor_by_tender_id_ajax"); ?>',
                    type: 'POST',
                    data: {tender_enquiry_id: tender_id},
                    dataType: 'json',
                    success: function(response) {
                        var html = '<option value="">Select Vendor</option>';
                        $.each(response, function(i, item) {
                            html += '<option value="'+item.vendor_id+'">'+item.vendor_name+'</option>';
                        });
                        $('#vendor_id').html(html);
                        
                        if (window.isEditing && window.editVendor) {
                            var v = window.editVendor;
                            window.editVendor = null;
                            $('#vendor_id').val(v).trigger('change');
                        } else if (!window.isEditing) {
                            $('#vendor_id').val('').trigger('change');
                        }
                    }
                });
            }
        });

        $('#vendor_id').on('change', function() {
            var tender_id = $('#tender_enquiry_id').val();
            var vendor_id = $(this).val();
            
            if (!window.isEditing) {
                $('#vendor_po_id').html('<option value="">Select PO</option>').trigger('change');
                $('#invoice_ids').html('').trigger('change');
            }

            if (vendor_id) {
                $.ajax({
                    url: '<?php echo site_url("vendor/get_vendor_po_by_vendor_ajax"); ?>',
                    type: 'POST',
                    data: {tender_enquiry_id: tender_id, vendor_id: vendor_id},
                    dataType: 'json',
                    success: function(response) {
                        var html = '<option value="">Select PO</option>';
                        $.each(response, function(i, item) {
                            html += '<option value="'+item.vendor_po_id+'">'+item.po_no+'</option>';
                        });
                        $('#vendor_po_id').html(html);
                        
                        if (window.isEditing) {
                            var p = window.editPo || '';
                            window.editPo = null;
                            $('#vendor_po_id').val(p).trigger('change.select2');

                            var savedInvoices = window.editInvoices || [];
                            window.editInvoices = null;
                            fetchInvoices(tender_id, vendor_id, p, savedInvoices, function() {
                                window.isEditing = false;
                            });
                        } else {
                            $('#vendor_po_id').val('').trigger('change.select2');
                            fetchInvoices(tender_id, vendor_id, '', []);
                        }
                    }
                });
            }
        });

        $('#vendor_po_id').on('change', function() {
            var tender_id = $('#tender_enquiry_id').val();
            var vendor_id = $('#vendor_id').val();
            var po_id = $(this).val();

            if (vendor_id && !window.isEditing) {
                var currentSelected = $('#invoice_ids').val() || [];
                fetchInvoices(tender_id, vendor_id, po_id, currentSelected);
            }
        });

        function handleAcTypeChange(type) {
            if (type === 'Bank') {
                $('#bank_div').show();
                $('#bank_id').prop('required', true);
                $('#cash_category_div').hide();
                $('#cash_category_id').val('').trigger('change').prop('required', false);
            } else if (type === 'Cash') {
                $('#bank_div').hide();
                $('#bank_id').val('').trigger('change').prop('required', false);
                $('#cash_category_div').show();
                $('#cash_category_id').prop('required', true);
            } else {
                $('#bank_div').hide();
                $('#bank_id').val('').trigger('change').prop('required', false);
                $('#cash_category_div').hide();
                $('#cash_category_id').val('').trigger('change').prop('required', false);
            }
        }

        $('#ac_type_opt').on('change', function() {
            handleAcTypeChange($(this).val());
        });

        $('#btnAdd').on('click', function() {
            $('#frmAdd')[0].reset();
            $('#mode').val('Add');
            $('#adv_payment_id').val('');
            $('.select2').val('').trigger('change');
            $('#invoice_ids').html('').val(null).trigger('change');
            $('#invoice_ids').select2('close');
            handleAcTypeChange('');
            window.isEditing = false;
            window.editVendor = null;
            window.editPo = null;
            window.editInvoices = null;
            $('#addModal').modal('show');
        });

        $('.edit_record').on('click', function() {
            $('#mode').val('Edit');
            $('#adv_payment_id').val($(this).data('id'));
            
            var tender = $(this).data('tender');
            var vendor = $(this).data('vendor');
            var po = $(this).data('po');
            var bank = $(this).data('bank');
            var cash = $(this).data('cash');
            var invoices = $(this).data('invoices');
            
            window.isEditing = true;
            window.editVendor = vendor;
            window.editPo = po;
            window.editInvoices = invoices ? String(invoices).split(',').map(function(s) { return s.trim(); }) : [];

            $('#tender_enquiry_id').val(tender).trigger('change');
            
            $('#ac_type_opt').val($(this).data('ac')).trigger('change');
            if (bank) {
                $('#bank_id').val(bank).trigger('change');
            }
            if (cash) {
                $('#cash_category_id').val(cash).trigger('change');
            }
            
            $('#adv_payment_date').val($(this).data('date'));
            $('#adv_payment_amt').val($(this).data('amt'));
            $('#status').val($(this).data('status')).trigger('change');

            if (!tender && vendor) {
                $('#vendor_id').val(vendor).trigger('change');
            }

            $('#addModal').modal('show');
        });

        $('#btnCancel').on('click', function() {
            $('#frmAdd')[0].reset();
            $('#mode').val('Add');
            $('#adv_payment_id').val('');
            $('.select2').val('').trigger('change');
            $('#invoice_ids').html('').val(null).trigger('change');
            $('#invoice_ids').select2('close');
            handleAcTypeChange('');
            window.isEditing = false;
            window.editVendor = null;
            window.editPo = null;
            window.editInvoices = null;
        });

        $('.del_record').on('click', function() {
            var id = $(this).val();
            swal({
                title: "Are you sure?",
                text: "You want to delete this record?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: '<?php echo site_url("vendor/vendor_adv_payment_list"); ?>',
                        type: 'POST',
                        data: {mode: 'Delete', del_id: id},
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                swal("Deleted!", "Record has been deleted.", "success")
                                .then(() => {
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
