<!-- Select2 -->
<script src="<?php echo base_url() ?>asset/bower_components/select2/dist/js/select2.full.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/select2/dist/css/select2.min.css">
<!-- Toastr -->
<link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/toastr/toastr.min.css">
<script src="<?php echo base_url() ?>asset/bower_components/toastr/toastr.min.js"></script>
<!-- SweetAlert -->
<script src="<?php echo base_url() ?>asset/plugins/sweetalert/sweetalert.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2();

        $('#tender_enquiry_id').on('change', function() {
            var tender_id = $(this).val();
            
            // Only clear and fetch if we are not in the middle of editing and setting values
            if(!window.isEditing) {
                $('#vendor_id').html('<option value="">Select Vendor</option>').trigger('change.select2');
                $('#vendor_po_id').html('<option value="">Select PO</option>').trigger('change.select2');
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
                        
                        if(window.isEditing && window.editVendor) {
                            $('#vendor_id').val(window.editVendor).trigger('change');
                            window.editVendor = null;
                        }
                    }
                });
            }
        });

        $('#vendor_id').on('change', function() {
            var tender_id = $('#tender_enquiry_id').val();
            var vendor_id = $(this).val();
            
            if(!window.isEditing) {
                $('#vendor_po_id').html('<option value="">Select PO</option>').trigger('change.select2');
            }

            if (tender_id && vendor_id) {
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
                        
                        if(window.isEditing && window.editPo) {
                            $('#vendor_po_id').val(window.editPo).trigger('change.select2');
                            window.editPo = null;
                            window.isEditing = false;
                        }
                    }
                });
            }
        });

        function handleAcTypeChange(type) {
            if (type === 'Bank') {
                $('#bank_div').show();
                $('#bank_id').prop('required', true);
                $('#cash_category_div').hide();
                $('#cash_category_id').val('').trigger('change.select2').prop('required', false);
            } else if (type === 'Cash') {
                $('#bank_div').hide();
                $('#bank_id').val('').trigger('change.select2').prop('required', false);
                $('#cash_category_div').show();
                $('#cash_category_id').prop('required', true);
            } else {
                $('#bank_div').hide();
                $('#bank_id').val('').trigger('change.select2').prop('required', false);
                $('#cash_category_div').hide();
                $('#cash_category_id').val('').trigger('change.select2').prop('required', false);
            }
        }

        $('#ac_type_opt').on('change', function() {
            handleAcTypeChange($(this).val());
        });

        $('#btnAdd').on('click', function() {
            $('#frmAdd')[0].reset();
            $('#mode').val('Add');
            $('#adv_payment_id').val('');
            $('.select2').val('').trigger('change.select2');
            handleAcTypeChange('');
            window.isEditing = false;
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
            
            window.isEditing = true;
            window.editVendor = vendor;
            window.editPo = po;

            $('#tender_enquiry_id').val(tender).trigger('change');
            
            $('#ac_type_opt').val($(this).data('ac')).trigger('change');
            if (bank) {
                $('#bank_id').val(bank).trigger('change.select2');
            }
            if (cash) {
                $('#cash_category_id').val(cash).trigger('change.select2');
            }
            
            $('#adv_payment_date').val($(this).data('date'));
            $('#adv_payment_amt').val($(this).data('amt'));
            $('#status').val($(this).data('status')).trigger('change.select2');

            $('#addModal').modal('show');
        });

        $('#btnCancel').on('click', function() {
            $('#frmAdd')[0].reset();
            $('#mode').val('Add');
            $('#adv_payment_id').val('');
            $('.select2').val('').trigger('change.select2');
            handleAcTypeChange('');
            window.isEditing = false;
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
