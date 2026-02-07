<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Master</a></li>
        <li class="active"><?php echo $title; ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="srch_from_date">From Date</label>
                        <input type="date" name="srch_from_date" id="srch_from_date" class="form-control"
                            value="<?php echo set_value('srch_from_date', $srch_from_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_to_date">To Date</label>
                        <input type="date" name="srch_to_date" id="srch_to_date" class="form-control"
                            value="<?php echo set_value('srch_to_date', $srch_to_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="voucher_type">Voucher Type</label>
                        <?php
                        echo form_dropdown(
                            'voucher_type',
                            $voucher_type_opt,
                            set_value('voucher_type', $voucher_type),
                            'class="form-control" id="voucher_type" required'
                        );
                        ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Select Voucher</label>
                        <?php
                        echo form_dropdown(
                            'srch_voucher_narration_id',
                            $voucher_list_opt ?? ['' => '-- Select Voucher --'],
                            $srch_voucher_narration_id ?? '',
                            'id="srch_voucher_narration_id" class="form-control"'
                        );
                        ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Select Ledger Account</label>
                        <?php echo form_dropdown('srch_ledger_account_id', $ledger_accounts_list_opt, set_value('srch_ledger_account_id', $srch_ledger_account_id), 'id="srch_ledger_account_id" class="form-control"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="box box-success">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#add_modal">
                <span class="fa fa-plus-circle"></span> Add New
            </button>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped table-responsive" id="company_list">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th>Voucher Date</th>
                        <th>Voucher Type</th>
                        <th>Ledger Name</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th colspan="2" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($record_list as $j => $ls) {
                        ?>
                        <tr class="mb-3">
                            <td class="text-center"><?php echo ($j + 1); ?></td>
                            <td><?php echo date('d-m-Y', strtotime($ls['voucher_date'])) ?></td>
                            <td><?php echo $ls['narration'] ?> <br>
                                <i class="label label-info"><?php echo $ls['voucher_type'] ?></i>
                            </td>
                            <td><?php echo $ls['ledger_name'] ?></td>
                            <td><?php echo number_format($ls['debit'], 2) ?></td>
                            <td><?php echo number_format($ls['credit'], 2) ?></td>

                            <td class="text-center">
                                <button data-toggle="modal" data-target="#edit_modal" value="<?php echo $ls['entry_id'] ?>"
                                    class="edit_record btn btn-primary btn-xs" title="Edit"><i
                                        class="fa fa-edit"></i></button>
                            </td>
                            <td class="text-center">
                                <button value="<?php echo $ls['entry_id'] ?>" class="del_record btn btn-danger btn-xs"
                                    title="Delete"><i class="fa fa-remove"></i></button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>

            <!-- Add Voucher Entry Modal -->
            <div class="modal fade" id="add_modal" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">

                        <form method="post" action="<?php echo site_url('voucher-entries-list'); ?>" id="frmadd"
                            enctype="multipart/form-data">

                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                                <h4 class="modal-title"><strong>Add Voucher Entry</strong></h4>
                                <input type="hidden" name="mode" value="Add">
                            </div>

                            <div class="modal-body">
                                <div class="row">

                                    <div class="form-group col-md-12">
                                        <label for="voucher_type">Voucher Type</label>
                                        <?php
                                        echo form_dropdown(
                                            'voucher_type',
                                            $voucher_type_opt,
                                            $voucher_type ?? '',
                                            'class="form-control" id="voucher_type" required'
                                        );
                                        ?>
                                    </div>

                                    <!-- Voucher Type -->
                                    <div class="form-group col-md-12">
                                        <label>Voucher</label>
                                        <div class="input-group">
                                            <?php
                                            echo form_dropdown(
                                                'voucher_id',
                                                ['' => 'Select Voucher'],
                                                set_value('voucher_id'),
                                                'id="srch_voucher_id" class="form-control" '
                                            );
                                            ?>
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-info" data-toggle="modal"
                                                    data-target="#add_voucher_id">Add New</button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Ledger</label>
                                        <?php
                                        echo form_dropdown(
                                            'ledger_id',
                                            $ledger_accounts_list_opt,
                                            '',
                                            'class="form-control" id="ledger_id" required'
                                        );
                                        ?>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="payment_type">Payment Type</label>
                                        <select name="payment_type" id="payment_type" class="form-control">
                                            <option value="Debit">Debit</option>
                                            <option value="Credit">Credit</option>
                                        </select>
                                    </div>

                                    <!-- Debit -->
                                    <div class="form-group col-md-12" id="debit_div">
                                        <label>Debit</label>
                                        <input type="number" step="any" name="debit" class="form-control" id="debit"
                                            placeholder="0.00">
                                    </div>

                                    <!-- Credit -->
                                    <div class="form-group col-md-12" id="credit_div">
                                        <label>Credit</label>
                                        <input type="number" step="any" name="credit" class="form-control" id="credit"
                                            placeholder="0.00">
                                    </div>

                                    <!-- Status -->
                                    <div class="form-group col-md-12">
                                        <label>Status</label><br>
                                        <label class="radio-inline">
                                            <input type="radio" name="status" value="Active" checked> Active
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="status" value="InActive"> InActive
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
                
          

            <div class="modal fade" id="edit_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo site_url('voucher-entries-list'); ?>" id="frmedit"
                            class="form-material">
                            <div class="modal-header">

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel">Edit Account Group </h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="entry_id" id="entry_id" value="" />
                            </div>
                            <div class="modal-body">
                                <div class="row">

                                    <!-- Voucher Type -->
                                    <div class="form-group col-md-12">
                                        <label>Voucher</label>
                                        <?php
                                        echo form_dropdown(
                                            'voucher_id',
                                            $voucher_list_opt,
                                            '',
                                            'class="form-control" id="voucher_id" required="true"'
                                        );
                                        ?>
                                    </div>

                                    <!-- Ledger -->
                                    <div class="form-group col-md-12">
                                        <label>Ledger</label>
                                        <?php
                                        echo form_dropdown(
                                            'ledger_id',
                                            $ledger_accounts_list_opt,
                                            '',
                                            'class="form-control" id="ledger_id" required="true"'
                                        );
                                        ?>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="payment_type">Payment Type</label>
                                        <select name="payment_type" id="payment_type" class="form-control">
                                            <option value="Debit">Debit</option>
                                            <option value="Credit">Credit</option>
                                        </select>
                                    </div>

                                    <!-- Debit -->
                                    <div class="form-group col-md-12" id="debit_div">
                                        <label>Debit</label>
                                        <input type="number" step="any" name="debit" class="form-control" id="debit"
                                            placeholder="0.00">
                                    </div>

                                    <!-- Credit -->
                                    <div class="form-group col-md-12" id="credit_div">
                                        <label>Credit</label>
                                        <input type="number" step="any" name="credit" class="form-control" id="credit"
                                            placeholder="0.00">
                                    </div>

                                    <!-- Status -->
                                    <div class="form-group col-md-12">
                                        <label>Status</label><br>
                                        <label class="radio-inline">
                                            <input type="radio" name="status" value="Active" checked> Active
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="status" value="InActive"> InActive
                                        </label>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <input type="submit" name="Save" value="Update" class="btn btn-primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="add_voucher_id" class="add_voucher_modal" role="dialog" aria-labelledby="scrollmodalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form method="post" action="" id="frmadd_voucher" enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title">Add Voucher</h3>
                                <input type="hidden" name="mode" value="Add Voucher" />
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Voucher Date</label>
                                        <input type="date" name="voucher_date" class="form-control" id="voucher_date"
                                            required value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Voucher Type</label>
                                        <select name="voucher_type" class="form-control" id="voucher_type">
                                            <option value="Journal">Journal</option>
                                            <option value="Payment">Payment</option>
                                            <option value="Receipt">Receipt</option>
                                            <option value="Contra">Contra</option>
                                            <option value="Sales">Sales</option>
                                            <option value="Purchase">Purchase</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Narration</label>
                                        <textarea name="narration" class="form-control" rows="5"
                                            placeholder="Enter your narration" id="narration"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Status</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" value="Active" checked="true" />
                                                Active
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" value="InActive" /> InActive
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <input type="submit" name="btn_add_voucher" value="Save" class="btn btn-primary" />

                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
        <div class="box-footer">
            <div class="form-group col-sm-6">
                <label>Total Records : <?php echo $total_records; ?></label>
            </div>
            <div class="form-group col-sm-6">
                <?php echo $pagination; ?>
            </div>
        </div>

    </div>

</section>
<!-- /.content -->
<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
<script>
$(document).ready(function () {
    var voucherType = $("#add_modal #voucher_type").val(); // source
    $(".add_voucher_modal #voucher_type").val(voucherType).trigger('change'); // target
});
</script>


<script>

    $("#add_modal #voucher_type").change(function () {
        const voucher_type = $(this).val();
        $.post(
            "<?php echo site_url('audit/get_data'); ?>",
            {
                tbl: "voucher_type_narration_load",
                id: voucher_type,
            },
            function (data) {
                console.log(data);
                const $cust = $("#add_modal #srch_voucher_id");
                $cust.empty().append('<option value="">Select Narration</option>');
                $.each(data, function (i, c) {
                    $cust.append(
                        `<option value="${c.voucher_id}">${c.narration}</option>`
                    );
                });
            },
            "json"
        );
    });

    $("#voucher_type").change(function () {
        const voucher_type = $(this).val();
        $.post(
            "<?php echo site_url('audit/get_data'); ?>",
            {
                tbl: "voucher_type_narration_load",
                id: voucher_type,
            },
            function (data) {
                console.log(data);
                const $cust = $("#srch_voucher_narration_id");
                $cust.empty().append('<option value="">Select Narration</option>');
                $.each(data, function (i, c) {
                    $cust.append(
                        `<option value="${c.voucher_id}">${c.narration}</option>`
                    );
                });
            },
            "json"
        );
    });
    $("#srch_voucher_narration_id").change(function () {
        const group_id = $(this).val();
        $.post(
            "<?php echo site_url('audit/get_data'); ?>",
            {
                tbl: "voucher_ledger_list_load",
                id: group_id,
            },
            function (data) {
                console.log(data);
                const $cust = $("#srch_ledger_account_id");
                $cust.empty().append('<option value="">Select Ledger</option>');
                $.each(data, function (i, c) {
                    $cust.append(
                        `<option value="${c.ledger_id}">${c.ledger_name}</option>`
                    );
                });
            },
            "json"
        );
    });

    $(document).on("submit", "#frmadd_voucher", function (e) {
        e.preventDefault();

        let btn = $('input[name="btn_add_voucher"]');
        btn.val("Saving...").prop("disabled", true);

        let formData = $(this).serialize();

        let voucher_date = $('#voucher_date').val().trim();
        if (voucher_date === "") {
            alert("Voucher Date is required.");
            $('#voucher_date').focus();
            enableButton();
            return false;
        }

        $.ajax({
            url: "<?php echo site_url('audit/ajax_add_master_inline'); ?>",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {

                    // Append new one voucher to dropdown
                    $("#srch_voucher_id")
                        .append(new Option(response.name, response.id, true, true));
                    // Append new two voucher to dropdown
                    $("#srch_voucher_narration_id")
                        .append(new Option(response.name, response.id, true, true));
                    // Reset form
                    $("#frmadd_voucher")[0].reset();

                    // Close modal
                    $("#add_voucher_id").modal("hide");

                    // Success message
                    $("#zazualert").html(`
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        ${response.message}
                    </div>
                `);
                } else {
                    alert("Failed to add voucher");
                }
            },
            error: function (xhr) {
                alert("Server Error");
            },
            complete: function () {
                enableButton();
            }
        });

        function enableButton() {
            btn.val("Save").prop("disabled", false);
        }
    });
</script>