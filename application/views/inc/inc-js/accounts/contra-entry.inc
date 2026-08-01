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
    $("#add_from_ac_type").change(function() {
        var val = $(this).val();
        if (val === 'Bank') {
            $(".add_from_bank_wrapper").show();
            $("#add_from_bank_id").prop('required', true);
            $(".add_from_cash_wrapper").hide();
            $("#add_from_cash_category_id").val('').trigger('change.select2').prop('required', false);
        } else if (val === 'Cash') {
            $(".add_from_bank_wrapper").hide();
            $("#add_from_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".add_from_cash_wrapper").show();
            $("#add_from_cash_category_id").prop('required', true);
        } else {
            $(".add_from_bank_wrapper").hide();
            $("#add_from_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".add_from_cash_wrapper").hide();
            $("#add_from_cash_category_id").val('').trigger('change.select2').prop('required', false);
        }
    });

    $("#add_to_ac_type").change(function() {
        var val = $(this).val();
        if (val === 'Bank') {
            $(".add_to_bank_wrapper").show();
            $("#add_to_bank_id").prop('required', true);
            $(".add_to_cash_wrapper").hide();
            $("#add_to_cash_category_id").val('').trigger('change.select2').prop('required', false);
        } else if (val === 'Cash') {
            $(".add_to_bank_wrapper").hide();
            $("#add_to_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".add_to_cash_wrapper").show();
            $("#add_to_cash_category_id").prop('required', true);
        } else {
            $(".add_to_bank_wrapper").hide();
            $("#add_to_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".add_to_cash_wrapper").hide();
            $("#add_to_cash_category_id").val('').trigger('change.select2').prop('required', false);
        }
    });

    // Toggle fields based on account type selection (Edit Form)
    $("#edit_from_ac_type").change(function() {
        var val = $(this).val();
        if (val === 'Bank') {
            $(".edit_from_bank_wrapper").show();
            $("#edit_from_bank_id").prop('required', true);
            $(".edit_from_cash_wrapper").hide();
            $("#edit_from_cash_category_id").val('').trigger('change.select2').prop('required', false);
        } else if (val === 'Cash') {
            $(".edit_from_bank_wrapper").hide();
            $("#edit_from_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_from_cash_wrapper").show();
            $("#edit_from_cash_category_id").prop('required', true);
        } else {
            $(".edit_from_bank_wrapper").hide();
            $("#edit_from_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_from_cash_wrapper").hide();
            $("#edit_from_cash_category_id").val('').trigger('change.select2').prop('required', false);
        }
    });

    $("#edit_to_ac_type").change(function() {
        var val = $(this).val();
        if (val === 'Bank') {
            $(".edit_to_bank_wrapper").show();
            $("#edit_to_bank_id").prop('required', true);
            $(".edit_to_cash_wrapper").hide();
            $("#edit_to_cash_category_id").val('').trigger('change.select2').prop('required', false);
        } else if (val === 'Cash') {
            $(".edit_to_bank_wrapper").hide();
            $("#edit_to_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_to_cash_wrapper").show();
            $("#edit_to_cash_category_id").prop('required', true);
        } else {
            $(".edit_to_bank_wrapper").hide();
            $("#edit_to_bank_id").val('').trigger('change.select2').prop('required', false);
            $(".edit_to_cash_wrapper").hide();
            $("#edit_to_cash_category_id").val('').trigger('change.select2').prop('required', false);
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
        var from_remarks = $(this).data('from-remarks');

        var to_type = $(this).data('to-type');
        var to_bank = $(this).data('to-bank');
        var to_cash = $(this).data('to-cash');
        var to_remarks = $(this).data('to-remarks');

        $("#edit_contra_entry_id").val(id);
        $("#edit_entry_date").val(date);
        $("#edit_amount").val(amount);

        $("#edit_from_ac_type").val(from_type).trigger('change');
        if (from_type === 'Bank') {
            $("#edit_from_bank_id").val(from_bank).trigger('change.select2');
        } else if (from_type === 'Cash') {
            $("#edit_from_cash_category_id").val(from_cash).trigger('change.select2');
        }
        $("#edit_from_remarks").val(from_remarks);

        $("#edit_to_ac_type").val(to_type).trigger('change');
        if (to_type === 'Bank') {
            $("#edit_to_bank_id").val(to_bank).trigger('change.select2');
        } else if (to_type === 'Cash') {
            $("#edit_to_cash_category_id").val(to_cash).trigger('change.select2');
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
