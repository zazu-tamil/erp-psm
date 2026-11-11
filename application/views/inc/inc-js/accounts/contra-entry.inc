<!-- Select2 -->
<script src="<?php echo base_url() ?>asset/bower_components/select2/dist/js/select2.full.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/select2/dist/css/select2.min.css">
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<!-- bootstrap datepicker -->
<script src="<?php echo base_url() ?>asset/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script>
jQuery(function($) {
    if ($.fn.select2) {
        $(".select2").select2({ width: "100%" });
    }

    // Toggle fields based on account type selection (Add Form)
    function handleAddFromToggle() {
        var val = $("#add_from_ac_type").val();
        if (val === 'Bank') {
            $(".add_from_bank_wrapper").show();
            $("#add_from_bank_id").prop('required', true);
            $(".add_from_cash_wrapper").hide();
            $("#add_from_cash_category_id").val('').trigger('change.select2').prop('required', false);
            $(".add_from_pay_type_wrapper").show();
            var payType = $("input[name='from_pay_type']:checked", "#add_modal").val();
            if (payType === 'Cheque') {
                $(".add_from_cheque_wrapper").show();
                $("#add_from_cheque_date, #add_from_cheque_no, #add_from_cheque_bank").prop('required', true);
            } else {
                $(".add_from_cheque_wrapper").hide();
                $("#add_from_cheque_date, #add_from_cheque_no, #add_from_cheque_bank").prop('required', false);
            }
        } else if (val === 'Cash') {
            $(".add_from_bank_wrapper").hide();
            $("#add_from_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".add_from_cash_wrapper").show();
            $("#add_from_cash_category_id").prop('required', true);
            $(".add_from_pay_type_wrapper").hide();
            $(".add_from_cheque_wrapper").hide();
            $("#add_from_pay_type_online").prop('checked', true);
            $("#add_from_cheque_date, #add_from_cheque_no, #add_from_cheque_bank").val('').prop('required', false);
        } else {
            $(".add_from_bank_wrapper").hide();
            $("#add_from_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".add_from_cash_wrapper").hide();
            $("#add_from_cash_category_id").val('').trigger('change.select2').prop('required', false);
            $(".add_from_pay_type_wrapper").hide();
            $(".add_from_cheque_wrapper").hide();
            $("#add_from_pay_type_online").prop('checked', true);
            $("#add_from_cheque_date, #add_from_cheque_no, #add_from_cheque_bank").val('').prop('required', false);
        }
    }

    $("#add_from_ac_type").change(handleAddFromToggle);

    $("input[name='from_pay_type']", "#add_modal").change(function() {
        if ($("#add_from_ac_type").val() === 'Bank' && $(this).val() === 'Cheque') {
            $(".add_from_cheque_wrapper").show();
            $("#add_from_cheque_date, #add_from_cheque_no, #add_from_cheque_bank").prop('required', true);
        } else {
            $(".add_from_cheque_wrapper").hide();
            $("#add_from_cheque_date, #add_from_cheque_no, #add_from_cheque_bank").prop('required', false);
        }
    });

    function handleAddToToggle() {
        var val = $("#add_to_ac_type").val();
        if (val === 'Bank') {
            $(".add_to_bank_wrapper").show();
            $("#add_to_bank_id").prop('required', true);
            $(".add_to_cash_wrapper").hide();
            $("#add_to_cash_category_id").val('').trigger('change.select2').prop('required', false);
            $(".add_to_pay_type_wrapper").show();
            var payType = $("input[name='to_pay_type']:checked", "#add_modal").val();
            if (payType === 'Cheque') {
                $(".add_to_cheque_wrapper").show();
                $("#add_to_cheque_date, #add_to_cheque_no, #add_to_cheque_bank").prop('required', true);
            } else {
                $(".add_to_cheque_wrapper").hide();
                $("#add_to_cheque_date, #add_to_cheque_no, #add_to_cheque_bank").prop('required', false);
            }
        } else if (val === 'Cash') {
            $(".add_to_bank_wrapper").hide();
            $("#add_to_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".add_to_cash_wrapper").show();
            $("#add_to_cash_category_id").prop('required', true);
            $(".add_to_pay_type_wrapper").hide();
            $(".add_to_cheque_wrapper").hide();
            $("#add_to_pay_type_online").prop('checked', true);
            $("#add_to_cheque_date, #add_to_cheque_no, #add_to_cheque_bank").val('').prop('required', false);
        } else {
            $(".add_to_bank_wrapper").hide();
            $("#add_to_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".add_to_cash_wrapper").hide();
            $("#add_to_cash_category_id").val('').trigger('change.select2').prop('required', false);
            $(".add_to_pay_type_wrapper").hide();
            $(".add_to_cheque_wrapper").hide();
            $("#add_to_pay_type_online").prop('checked', true);
            $("#add_to_cheque_date, #add_to_cheque_no, #add_to_cheque_bank").val('').prop('required', false);
        }
    }

    $("#add_to_ac_type").change(handleAddToToggle);

    $('#add_modal').on('show.bs.modal', function() {
        handleAddFromToggle();
        handleAddToToggle();
    });

    $("input[name='to_pay_type']", "#add_modal").change(function() {
        if ($("#add_to_ac_type").val() === 'Bank' && $(this).val() === 'Cheque') {
            $(".add_to_cheque_wrapper").show();
            $("#add_to_cheque_date, #add_to_cheque_no, #add_to_cheque_bank").prop('required', true);
        } else {
            $(".add_to_cheque_wrapper").hide();
            $("#add_to_cheque_date, #add_to_cheque_no, #add_to_cheque_bank").prop('required', false);
        }
    });

    // Toggle fields based on account type selection (Edit Form)
    function handleEditFromToggle() {
        var val = $("#edit_from_ac_type").val();
        if (val === 'Bank') {
            $(".edit_from_bank_wrapper").show();
            $("#edit_from_bank_id").prop('required', true);
            $(".edit_from_cash_wrapper").hide();
            $("#edit_from_cash_category_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_from_pay_type_wrapper").show();
            var payType = $("input[name='from_pay_type']:checked", "#edit_modal").val();
            if (payType === 'Cheque') {
                $(".edit_from_cheque_wrapper").show();
                $("#edit_from_cheque_date, #edit_from_cheque_no, #edit_from_cheque_bank").prop('required', true);
            } else {
                $(".edit_from_cheque_wrapper").hide();
                $("#edit_from_cheque_date, #edit_from_cheque_no, #edit_from_cheque_bank").prop('required', false);
            }
        } else if (val === 'Cash') {
            $(".edit_from_bank_wrapper").hide();
            $("#edit_from_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_from_cash_wrapper").show();
            $("#edit_from_cash_category_id").prop('required', true);
            $(".edit_from_pay_type_wrapper").hide();
            $(".edit_from_cheque_wrapper").hide();
            $("#edit_from_pay_type_online").prop('checked', true);
            $("#edit_from_cheque_date, #edit_from_cheque_no, #edit_from_cheque_bank").val('').prop('required', false);
        } else {
            $(".edit_from_bank_wrapper").hide();
            $("#edit_from_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_from_cash_wrapper").hide();
            $("#edit_from_cash_category_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_from_pay_type_wrapper").hide();
            $(".edit_from_cheque_wrapper").hide();
            $("#edit_from_pay_type_online").prop('checked', true);
            $("#edit_from_cheque_date, #edit_from_cheque_no, #edit_from_cheque_bank").val('').prop('required', false);
        }
    }

    $("#edit_from_ac_type").change(handleEditFromToggle);

    $("input[name='from_pay_type']", "#edit_modal").change(function() {
        if ($("#edit_from_ac_type").val() === 'Bank' && $(this).val() === 'Cheque') {
            $(".edit_from_cheque_wrapper").show();
            $("#edit_from_cheque_date, #edit_from_cheque_no, #edit_from_cheque_bank").prop('required', true);
        } else {
            $(".edit_from_cheque_wrapper").hide();
            $("#edit_from_cheque_date, #edit_from_cheque_no, #edit_from_cheque_bank").prop('required', false);
        }
    });

    function handleEditToToggle() {
        var val = $("#edit_to_ac_type").val();
        if (val === 'Bank') {
            $(".edit_to_bank_wrapper").show();
            $("#edit_to_bank_id").prop('required', true);
            $(".edit_to_cash_wrapper").hide();
            $("#edit_to_cash_category_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_to_pay_type_wrapper").show();
            var payType = $("input[name='to_pay_type']:checked", "#edit_modal").val();
            if (payType === 'Cheque') {
                $(".edit_to_cheque_wrapper").show();
                $("#edit_to_cheque_date, #edit_to_cheque_no, #edit_to_cheque_bank").prop('required', true);
            } else {
                $(".edit_to_cheque_wrapper").hide();
                $("#edit_to_cheque_date, #edit_to_cheque_no, #edit_to_cheque_bank").prop('required', false);
            }
        } else if (val === 'Cash') {
            $(".edit_to_bank_wrapper").hide();
            $("#edit_to_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_to_cash_wrapper").show();
            $("#edit_to_cash_category_id").prop('required', true);
            $(".edit_to_pay_type_wrapper").hide();
            $(".edit_to_cheque_wrapper").hide();
            $("#edit_to_pay_type_online").prop('checked', true);
            $("#edit_to_cheque_date, #edit_to_cheque_no, #edit_to_cheque_bank").val('').prop('required', false);
        } else {
            $(".edit_to_bank_wrapper").hide();
            $("#edit_to_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_to_cash_wrapper").hide();
            $("#edit_to_cash_category_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_to_pay_type_wrapper").hide();
            $(".edit_to_cheque_wrapper").hide();
            $("#edit_to_pay_type_online").prop('checked', true);
            $("#edit_to_cheque_date, #edit_to_cheque_no, #edit_to_cheque_bank").val('').prop('required', false);
        }
    }

    $("#edit_to_ac_type").change(handleEditToToggle);

    $("input[name='to_pay_type']", "#edit_modal").change(function() {
        if ($("#edit_to_ac_type").val() === 'Bank' && $(this).val() === 'Cheque') {
            $(".edit_to_cheque_wrapper").show();
            $("#edit_to_cheque_date, #edit_to_cheque_no, #edit_to_cheque_bank").prop('required', true);
        } else {
            $(".edit_to_cheque_wrapper").hide();
            $("#edit_to_cheque_date, #edit_to_cheque_no, #edit_to_cheque_bank").prop('required', false);
        }
    });

    // Populate Edit Modal
    $(".btn-edit").click(function() {
        var id = $(this).data('id');
        var date = $(this).data('date');
        var amount = $(this).data('amount');

        var from_type = $(this).data('from-type');
        var from_bank = $(this).data('from-bank');
        var from_cash = $(this).data('from-cash');
        var from_pay_type = $(this).data('from-pay-type') || 'Online';
        var from_cheque_date = $(this).data('from-cheque-date') || '';
        var from_cheque_no = $(this).data('from-cheque-no') || '';
        var from_cheque_bank = $(this).data('from-cheque-bank') || '';
        var from_remarks = $(this).data('from-remarks');

        var to_type = $(this).data('to-type');
        var to_bank = $(this).data('to-bank');
        var to_cash = $(this).data('to-cash');
        var to_pay_type = $(this).data('to-pay-type') || 'Online';
        var to_cheque_date = $(this).data('to-cheque-date') || '';
        var to_cheque_no = $(this).data('to-cheque-no') || '';
        var to_cheque_bank = $(this).data('to-cheque-bank') || '';
        var to_remarks = $(this).data('to-remarks');

        $("#edit_contra_entry_id").val(id);
        $("#edit_entry_date").val(date);
        $("#edit_amount").val(amount);

        // FROM SIDE
        $("#edit_from_ac_type").val(from_type);
        if (from_type === 'Bank') {
            $(".edit_from_bank_wrapper").show();
            $("#edit_from_bank_id").val(from_bank).trigger('change.select2').prop('required', true);
            $(".edit_from_cash_wrapper").hide();
            $("#edit_from_cash_category_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_from_pay_type_wrapper").show();
            $("input[name='from_pay_type'][value='" + from_pay_type + "']", "#edit_modal").prop('checked', true);
            if (from_pay_type === 'Cheque') {
                $(".edit_from_cheque_wrapper").show();
                $("#edit_from_cheque_date").val(from_cheque_date).prop('required', true);
                $("#edit_from_cheque_no").val(from_cheque_no).prop('required', true);
                $("#edit_from_cheque_bank").val(from_cheque_bank).prop('required', true);
            } else {
                $(".edit_from_cheque_wrapper").hide();
                $("#edit_from_cheque_date, #edit_from_cheque_no, #edit_from_cheque_bank").val('').prop('required', false);
            }
        } else if (from_type === 'Cash') {
            $(".edit_from_bank_wrapper").hide();
            $("#edit_from_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_from_cash_wrapper").show();
            $("#edit_from_cash_category_id").val(from_cash).trigger('change.select2').prop('required', true);
            $(".edit_from_pay_type_wrapper").hide();
            $(".edit_from_cheque_wrapper").hide();
            $("#edit_from_pay_type_online").prop('checked', true);
            $("#edit_from_cheque_date, #edit_from_cheque_no, #edit_from_cheque_bank").val('').prop('required', false);
        } else {
            $(".edit_from_bank_wrapper").hide();
            $(".edit_from_cash_wrapper").hide();
            $(".edit_from_pay_type_wrapper").hide();
            $(".edit_from_cheque_wrapper").hide();
        }
        $("#edit_from_remarks").val(from_remarks);

        // TO SIDE
        $("#edit_to_ac_type").val(to_type);
        if (to_type === 'Bank') {
            $(".edit_to_bank_wrapper").show();
            $("#edit_to_bank_id").val(to_bank).trigger('change.select2').prop('required', true);
            $(".edit_to_cash_wrapper").hide();
            $("#edit_to_cash_category_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_to_pay_type_wrapper").show();
            $("input[name='to_pay_type'][value='" + to_pay_type + "']", "#edit_modal").prop('checked', true);
            if (to_pay_type === 'Cheque') {
                $(".edit_to_cheque_wrapper").show();
                $("#edit_to_cheque_date").val(to_cheque_date).prop('required', true);
                $("#edit_to_cheque_no").val(to_cheque_no).prop('required', true);
                $("#edit_to_cheque_bank").val(to_cheque_bank).prop('required', true);
            } else {
                $(".edit_to_cheque_wrapper").hide();
                $("#edit_to_cheque_date, #edit_to_cheque_no, #edit_to_cheque_bank").val('').prop('required', false);
            }
        } else if (to_type === 'Cash') {
            $(".edit_to_bank_wrapper").hide();
            $("#edit_to_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_to_cash_wrapper").show();
            $("#edit_to_cash_category_id").val(to_cash).trigger('change.select2').prop('required', true);
            $(".edit_to_pay_type_wrapper").hide();
            $(".edit_to_cheque_wrapper").hide();
            $("#edit_to_pay_type_online").prop('checked', true);
            $("#edit_to_cheque_date, #edit_to_cheque_no, #edit_to_cheque_bank").val('').prop('required', false);
        } else {
            $(".edit_to_bank_wrapper").hide();
            $(".edit_to_cash_wrapper").hide();
            $(".edit_to_pay_type_wrapper").hide();
            $(".edit_to_cheque_wrapper").hide();
        }
        $("#edit_to_remarks").val(to_remarks);

        $("#edit_modal").modal('show');
    });

    // Delete Trigger
    $(".btn-delete").click(function() {
        var id = $(this).data('id');
        $("#delete_contra_entry_id").val(id);
        $("#delete_modal").modal('show');
    });
});
</script>
