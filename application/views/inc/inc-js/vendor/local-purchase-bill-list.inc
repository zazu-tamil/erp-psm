<style>
.ui-autocomplete {
    z-index: 9999999 !important;
}

/* Parent modals */
#add_modal,
#edit_modal {
    z-index: 1050 !important;
}

/* Child modal */
#add_vendor {
    z-index: 1080 !important;
}

/* Parent backdrop */
.modal-backdrop {
    z-index: 1040 !important;
}

/* Child backdrop */
.modal-backdrop.child-backdrop {
    z-index: 1070 !important;
}
</style>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
$(function() {

    var parentModal = null;

    /* OPEN CHILD MODAL */
    $('#btn_open_add_vendor').click(function() {

        parentModal = $('#add_modal');

        $('#add_vendor').modal({
            backdrop: false,
            keyboard: false,
            show: true
        });

        $('<div class="modal-backdrop fade in child-backdrop"></div>').appendTo(document.body);
    });

    function closeVendorModal() {
        $('#add_vendor').modal('hide');
    }

    $('#btn_close_vendor_modal,#btn_cancel_vendor_modal').click(function() {
        closeVendorModal();
    });

    $('#add_vendor').on('hidden.bs.modal', function() {

        $('.modal-backdrop.child-backdrop').remove();

        if (parentModal && parentModal.hasClass('in')) {
            $('body').addClass('modal-open');
        }

        parentModal = null;
    });




    $('#frmadd_Vendor').submit(function(e) {

        e.preventDefault();

        var vendor_name = $('#vendor_name').val().trim();

        if (vendor_name == '') {
            alert('Vendor Name is required');
            $('#vendor_name').focus();
            return false;
        }

        $.ajax({

            url: "<?php echo base_url('vendor/ajax_add_master_inline'); ?>",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",

            success: function(res) {

                if (res.status == 'success') {

                    var newOption = new Option(res.name, res.id, true, true);

                    $('.srch_vendor_id').append(newOption).trigger('change');

                    $('#frmadd_Vendor')[0].reset();

                    closeVendorModal();

                    $('#zazualert').html(
                        '<div class="alert alert-success alert-dismissible">' +
                        '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                        '<strong>Success!</strong> ' + res.message +
                        '</div>'
                    );

                } else {
                    alert(res.message);
                }

            },

            error: function() {
                alert('Server Error');
            }

        });

    });


    $('#srch_enquiry_no').autocomplete({


        source: function(request, response) {

            $.ajax({

                url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
                type: "POST",
                dataType: "json",
                data: {
                    search: request.term
                },

                success: function(data) {
                    response(data);
                }

            });

        },

        minLength: 1,

        select: function(event, ui) {
            $("#srch_enquiry_no").val(ui.item.value);
        }

    });

    $('#add_modal .srch_enq_id').autocomplete({

        appendTo: "#add_modal",

        source: function(request, response) {

            $.ajax({

                url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
                type: "POST",
                dataType: "json",
                data: {
                    search: request.term
                },

                success: function(data) {
                    response(data);
                }

            });

        },

        minLength: 1,

        select: function(event, ui) {

            $('#add_modal #srch_customer_id')
                .val(ui.item.customer_id)
                .trigger('change');

            load_tender_enq(ui.item.tender_enquiry_id);

        }

    });

    function load_tender_enq(t_enq_id) {

        t_enq_id = t_enq_id || '';

        var customer_id = $('#add_modal #srch_customer_id').val();

        var dd = $('#add_modal #srch_tender_enquiry_id');

        dd.html('<option value="">Select Enquiry</option>');
        dd.prop('disabled', true);

        if (!customer_id) return;

        $.ajax({

            url: "<?php echo site_url('vendor/get_vendor_rate_enquiries_by_customer'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                customer_id: customer_id
            },

            success: function(res) {

                if (res.length > 0) {

                    dd.prop('disabled', false);

                    $.each(res, function(i, row) {

                        dd.append(
                            $('<option>', {
                                value: row.tender_enquiry_id,
                                text: row.display
                            })
                        );

                    });

                    if (t_enq_id != '') {
                        dd.val(t_enq_id).trigger('change');
                    }

                } else {
                    dd.html('<option value="">No enquiries found</option>');
                }

            },

            error: function() {
                alert('Error loading enquiries');
            }

        });

    }

    $('#edit_modal .srch_enq_id').autocomplete({

        appendTo: "#edit_modal",

        source: function(request, response) {

            $.ajax({

                url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
                type: "POST",
                dataType: "json",
                data: {
                    search: request.term
                },

                success: function(data) {
                    response(data);
                }

            });

        },

        minLength: 1,

        select: function(event, ui) {

            $('#edit_modal #srch_customer_id')
                .val(ui.item.customer_id)
                .trigger('change');

            edit_load_tender_enq(ui.item.tender_enquiry_id);

        }

    });


    function edit_load_tender_enq(t_enq_id) {

        t_enq_id = t_enq_id || '';

        var customer_id = $('#edit_modal #srch_customer_id').val();

        var dd = $('#edit_modal #srch_tender_enquiry_id');

        dd.html('<option value="">Select Enquiry</option>');
        dd.prop('disabled', true);

        if (!customer_id) return;

        $.ajax({

            url: "<?php echo site_url('vendor/get_vendor_rate_enquiries_by_customer'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                customer_id: customer_id
            },

            success: function(res) {

                if (res.length > 0) {

                    dd.prop('disabled', false);

                    $.each(res, function(i, row) {

                        dd.append(
                            $('<option>', {
                                value: row.tender_enquiry_id,
                                text: row.display
                            })
                        );

                    });

                    if (t_enq_id != '') {
                        dd.val(t_enq_id).trigger('change');
                    }

                } else {
                    dd.html('<option value="">No enquiries found</option>');
                }

            },

            error: function() {
                alert('Error loading enquiries');
            }

        });

    }



    $(document).on('click', '.edit_record', function() {

        var id = $(this).val();

        $.ajax({

            url: "<?php echo site_url('vendor/get_data'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                tbl: "local_purchase_bill_info",
                id: id
            },

            success: function(res_ar) {
                //console.log(res_ar);
                let res = res_ar[0];
                $("#edit_modal #local_purchase_bill_id").val(res.local_purchase_bill_id);
                $("#edit_modal #sub_account_head_id").val(res.sub_account_head_id);
                $("#edit_modal #srch_customer_id").val(res.customer_id);
                edit_load_tender_enq(res.tender_enquiry_id);
                $("#edit_modal #tender_enquiry_id").val(res.tender_enquiry_id);
                $("#edit_modal #vendor_id").val(res.vendor_id);
                $("#edit_modal #invoice_date").val(res.invoice_date);
                $("#edit_modal #invoice_no").val(res.invoice_no);
                $("#edit_modal #inv_entry_date").val(res.inv_entry_date);
                $("#edit_modal #vat_payer_purchase_grp").val(res.vat_payer_purchase_grp);
                $("#edit_modal #tot_amt_wo_tax").val(res.tot_amt_wo_tax);
                $("#edit_modal #vat").val(res.vat);
                $("#edit_modal #vat_amt").val(res.vat_amt);
                $("#edit_modal #tot_amt_with_tax").val(res.tot_amt_with_tax);
                $("#edit_modal #tot_amt_with_tax").val(res.tot_amt_with_tax);
                $(`#edit_modal input[name="status"][value="${res.status}"]`).prop('checked', true);
            } 

        });

    });



    function calculateWithTax(prefix) {

        prefix = prefix || '';

        var base = parseFloat($('#' + prefix + ' #tot_amt_wo_tax').val()) || 0;

        var rate = parseFloat($('#' + prefix + ' #vat').val()) || 0;

        var total = base + (base * rate / 100);
        var vat_amt = (base * rate / 100); 
        $('#' + prefix + ' #vat_amt').val(vat_amt.toFixed(3));
        $('#' + prefix + ' #tot_amt_with_tax').val(total.toFixed(3));

    }


    $('#add_modal #vat,#add_modal #tot_amt_wo_tax').on('input', function() {
        calculateWithTax('add_modal');
    });

    $('#edit_modal #srch_customer_id').on('change', function() {  
        edit_load_tender_enq(0);
    });
    $('#edit_modal #vat, #edit_modal #tot_amt_wo_tax').on('input', function() {  
        calculateWithTax('edit_modal');
    });


    
    $(".del_record").click(function () {
      if (confirm("Are you sure you want to delete this bill ?")) {
        const id = $(this).val();
        //alert(id);
        $.post(
          "<?php echo site_url('vendor/delete_record'); ?>",
          {
            tbl: "local_purchase_bill_info",
            id: id,
          },
          function (res) {
            alert(res);
            location.reload();
          }
        ).fail(function () {
          alert("Error deleting record.");
        });
      }
    });




});
</script>