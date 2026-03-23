<?php include_once(VIEWPATH . '/inc/header.php');

// echo '<pre>';
// print_r($_POST);
// echo '</pre>'; 

?>

<section class="content-header">
    <h1><?php echo $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Master</a></li>
        <li class="active"><?php echo $title; ?></li>
    </ol>
</section>
<style>
    .pointer {
        cursor: pointer !important;
    }
</style>
<!-- Main content -->
<section class="content">

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <!-- Search Filter Box -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('customer-invoice-receipt'); ?>" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>Customer</label>
                        <?php echo form_dropdown('srch_customer_id', ['' => 'All'] + $customer_opt, $srch_customer_id, 'id="srch_customer_id" class="form-control select2"'); ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="srch_enquiry_no">Our Enquiry No</label>
                        <input type="text" name="srch_enquiry_no" id="srch_enquiry_no" class="form-control"
                            value="<?php echo set_value('srch_enquiry_no', $srch_enquiry_no); ?>"
                            placeholder="Search the Our Enquiry No">
                        <input type="hidden" name="tender_enquiry_id_value_id" class="tender_enquiry_id_value_id"
                            value="<?php echo set_value('tender_enquiry_id_value_id', $tender_enquiry_id_value_id); ?>">

                    </div>
                    <div class="form-group col-md-3 text-left">
                        <br>
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show</button>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main List Box -->
    <div class="box box-success">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#add_modal">
                <span class="fa fa-plus-circle"></span> Add New
            </button>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered" id="company_list">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th>Receipt No</th>
                        <th>Date</th>
                        <th>Receipt Mode</th>
                        <th class="text-right">Amount</th>
                        <th colspan="2" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($record_list as $j => $ls): ?>
                        <tr>
                            <td class="text-center"><?php echo ($j + 1); ?></td>
                            <td><?php echo $ls['receipt_no'] ?? ''; ?></td>
                            <td><?php echo $ls['receipt_date'] ?? ''; ?></td>
                            <td>
                                <?php if ($ls['receipt_mode'] == 'Bank'): ?>
                                    <span class="label label-success"><?php echo $ls['receipt_mode']; ?></span>
                                    <br>(<?php echo $ls['bank_name'] ?? ''; ?>)
                                <?php elseif ($ls['receipt_mode'] == 'Cash'): ?>
                                    <span class="label label-success"><?php echo $ls['receipt_mode']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right"><?php echo number_format((float) ($ls['amount'] ?? 0), 3); ?></td>
                            <td class="text-center">
                                <button type="button" data-toggle="modal" data-target="#edit_modal"
                                    data-id="<?php echo $ls['tender_receipt_id'] ?? ''; ?>"
                                    data-customer-id="<?php echo $ls['customer_id'] ?? ''; ?>"
                                    class="edit_record btn btn-primary btn-xs" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>
                            <td class="text-center">
                                <button type="button" value="<?php echo $ls['tender_receipt_id'] ?? ''; ?>"
                                    class="del_record btn btn-danger btn-xs" title="Delete">
                                    <i class="fa fa-remove"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($record_list)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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


    <!-- ===================== ADD MODAL ===================== -->
    <div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="post" action="<?php echo site_url('customer-invoice-receipt'); ?>" id="frmadd"
                    enctype="multipart/form-data">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title" id="addModalLabel"><strong>Add Customer Receipt</strong></h3>
                        <input type="hidden" name="mode" value="Add" />
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Receipt Date <span class="text-red">*</span></label>
                                <input type="date" name="receipt_date" id="add_receipt_date" class="form-control"
                                    required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Customer <span class="text-red">*</span></label>
                                <?php echo form_dropdown('customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('customer_id'), 'id="add_customer_id" class="form-control" required="true"'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Receipt Mode <span class="text-red">*</span></label>
                                <?php echo form_dropdown('receipt_mode', ['' => 'Select Receipt Mode', 'Cash' => 'Cash', 'Bank' => 'Bank'], set_value('receipt_mode'), 'id="add_receipt_mode" class="form-control" required="true"'); ?>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Receipt Type</label><br>
                                <label class="radio-inline">
                                    <input type="radio" name="receipt_type" value="Online"> Online
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="receipt_type" value="Cheque">Cheque
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6" id="add_bank_div" style="display:none;">
                                <label>Select Bank <span class="text-red">*</span></label>
                                <?php echo form_dropdown('bank_id', ['' => 'Select Bank'] + $bank_opt, set_value('bank_id'), 'id="add_bank_id" class="form-control"'); ?>
                            </div>
                            <div id="add_receipt_type_div" style="display:none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cheque Date <span class="text-red">*</span></label>
                                        <input type="date" name="cheque_date" id="add_cheque_date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cheque No <span class="text-red">*</span></label>
                                        <input type="text" name="cheque_no" id="add_cheque_no" class="form-control"
                                            placeholder="Ex: 0001 ">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cheque Bank <span class="text-red">*</span></label>
                                        <input type="text" name="cheque_bank" id="add_cheque_bank" class="form-control"
                                            placeholder="Ex: SBI">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea name="remarks" id="add_remarks" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Status</label><br>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="Active" checked> Active
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="InActive"> InActive
                                </label>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Amount <span class="text-red">*</span></label>
                                <input type="text" name="amount" id="add_grand_total_amount"
                                    class="form-control text-right" readonly>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="customer_invoice_receipt_list_table">
                                <thead class="bg-info">
                                    <tr>
                                        <th class="text-center" style="width:40px;">
                                            <input type="checkbox" id="add_select_all_items">
                                        </th>
                                        <th>Date</th>
                                        <th>Invoice No</th>
                                        <th>Enquiry No</th>
                                        <th class="text-right">Invoice Amount</th>
                                        <th>Pay Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="add_item_container"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fa fa-times"></i> Cancel
                        </button>
                        <input type="submit" name="Save" value="Save" class="btn btn-primary" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="post" action="<?php echo site_url('customer-invoice-receipt'); ?>" id="frmedit"
                    enctype="multipart/form-data">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title" id="editModalLabel"><strong>Edit Customer Receipt</strong></h3>
                        <input type="hidden" name="mode" value="Edit" />
                        <input type="hidden" name="tender_receipt_id" id="edit_tender_receipt_id" value="" />
                    </div>
                    <div class="modal-body">


                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Receipt Date <span class="text-red">*</span></label>
                                <input type="date" name="receipt_date" id="edit_receipt_date" class="form-control"
                                    required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Customer <span class="text-red">*</span></label>
                                <?php echo form_dropdown('customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('customer_id'), 'id="edit_customer_id" class="form-control" required="true"'); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Receipt Mode <span class="text-red">*</span></label>
                                <?php echo form_dropdown('receipt_mode', ['' => 'Select Receipt Mode', 'Cash' => 'Cash', 'Bank' => 'Bank'], set_value('receipt_mode'), 'id="edit_receipt_mode" class="form-control" required="true"'); ?>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Receipt Type</label><br>
                                <label class="radio-inline">
                                    <input type="radio" name="receipt_type" value="Online"> Online
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="receipt_type" value="Cheque">Cheque
                                </label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6" id="edit_bank_div" style="display:none;">
                                <label>Select Bank <span class="text-red">*</span></label>
                                <?php echo form_dropdown('bank_id', ['' => 'Select Bank'] + $bank_opt, set_value('bank_id'), 'id="edit_bank_id" class="form-control"'); ?>
                            </div>
                            <div id="edit_receipt_type_div" style="display:none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cheque Date <span class="text-red">*</span></label>
                                        <input type="date" name="cheque_date" id="edit_cheque_date"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cheque No <span class="text-red">*</span></label>
                                        <input type="text" name="cheque_no" id="edit_cheque_no" class="form-control"
                                            placeholder="Ex: 0001 ">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cheque Bank <span class="text-red">*</span></label>
                                        <input type="text" name="cheque_bank" id="edit_cheque_bank" class="form-control"
                                            placeholder="Ex: SBI">
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea name="remarks" id="edit_remarks" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="form-group col-md-4">
                                <label>Status</label><br>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="Active" checked> Active
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="InActive"> InActive
                                </label>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Amount <span class="text-red">*</span></label>
                                <input type="text" name="amount" id="edit_grand_total_amount"
                                    class="form-control text-right" readonly>
                            </div>
                        </div>

                        <br>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped"
                                id="edit_customer_invoice_receipt_list_table">
                                <thead class="bg-info">
                                    <tr>
                                        <th class="text-center">
                                            <input type="checkbox" id="edit_select_all_items">
                                        </th>
                                        <th>Date</th>
                                        <th>Invoice No</th>
                                        <th>Enquiry No</th>
                                        <th class="text-right">Invoice Amount</th>
                                        <th>Pay Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="edit_item_container"></tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fa fa-times"></i> Cancel
                        </button>
                        <input type="submit" name="Update" value="Update" class="btn btn-primary" />
                    </div>
                </form>
            </div>
        </div>
    </div>

</section>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>

<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<!-- jQuery UI JS -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet"
    href="<?php echo base_url() ?>asset/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<script src="<?php echo base_url() ?>asset/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>asset/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<style>
    .ui-autocomplete {
        z-index: 9999999 !important;
    }

    #add_modal,
    #edit_modal {
        z-index: 1050 !important;
    }

    .modal-backdrop {
        z-index: 1040 !important;
    }

    .invoice-amount-input {
        width: 120px;
        min-width: 100px;
    }
</style>

<script>
    $(document).ready(function () {

        // On change of receipt type
        $('input[name="receipt_type"]').on('change', function () {

            if ($(this).val() === 'Cheque') {
                $('#add_receipt_type_div').show();   
            } else {
                $('#add_receipt_type_div').hide();   
                $('#add_cheque_no').val('');
                $('#add_cheque_date').val('');
                $('#add_cheque_bank').val('');
            }

        });

        // On change of receipt type
        $('input[name="receipt_type"]').on('change', function () {

            if ($(this).val() === 'Cheque') {
                $('#edit_receipt_type_div').show();   
            } else {
                $('#edit_receipt_type_div').hide();    
                $('#edit_cheque_no').val('');
                $('#edit_cheque_date').val('');
                $('#edit_cheque_bank').val('');
            }

        });


        $(document).on("input change", ".invoice-amount-input, .select-item", function () {
            calculateGrandTotal("edit");
        });

        function format3(val) {
            return parseFloat(val || 0).toFixed(3);
        }

        function toggleBank(prefix) {
            var mode = $("#" + prefix + "_receipt_mode").val();
            if (mode === "Bank") {
                $("#" + prefix + "_bank_div").show();
            } else {
                $("#" + prefix + "_bank_div").hide();
                $("#" + prefix + "_bank_id").val("");
            }
        }

        function calculateGrandTotal(prefix) {

            var grand_total = 0;

            $("#" + prefix + "_item_container tr").each(function () {

                var $row = $(this);
                var $chk = $row.find(".select-item");
                var $input = $row.find(".invoice-amount-input");

                var value = parseFloat($input.val()) || 0;

                
                if ($chk.length > 0) {
                    if ($chk.is(":checked")) {
                        grand_total += value;
                    }
                }
              
                else {
                    grand_total += value;
                }

            });

            $("#" + prefix + "_grand_total_amount").val(format3(grand_total));
        }

        $("#add_receipt_mode").on("change", function () {
            toggleBank("add");
        });

        $("#edit_receipt_mode").on("change", function () {
            toggleBank("edit");
        });

        $(document).on("change", ".select-item", function () {
            var $row = $(this).closest("tr");
            var $input = $row.find(".invoice-amount-input");
            var amount = parseFloat($(this).data("amount")) || 0;
            var prefix =
                $(this).closest(".modal").attr("id") === "add_modal" ? "add" : "edit";

            if ($(this).is(":checked")) {
                $input.prop("readonly", false).val(format3(amount));
            } else {
                $input.val("0.000");
            }

            calculateGrandTotal(prefix);
        });

        $(document).on("blur", ".invoice-amount-input", function () {
            var prefix =
                $(this).closest(".modal").attr("id") === "add_modal" ? "add" : "edit";
            $(this).val(format3($(this).val()));
            calculateGrandTotal(prefix);
        });

        $("#add_select_all_items").on("change", function () {
            $("#add_item_container .select-item")
                .prop("checked", $(this).is(":checked"))
                .trigger("change");
        });

        $("#edit_select_all_items").on("change", function () {
            $("#edit_item_container .select-item")
                .prop("checked", $(this).is(":checked"))
                .trigger("change");
        });

        $(document).on("click", ".del_record", function () {
            if (!confirm("Are you sure you want to delete this record?")) return;

            var id = $(this).val();

            $.ajax({
                url: "<?php echo site_url('payment/delete_record'); ?>",
                type: "POST",
                data: { tbl: "customer_invoice_receipt_info", id: id },
                success: function (res) {
                    alert(res);
                    location.reload();
                },
                error: function () {
                    alert("Error deleting record.");
                },
            });
        });

        $("#add_modal").on("show.bs.modal", function () {
            $("#frmadd")[0].reset();
            $("#add_item_container").html("");
            $("#add_tender_enquiry_id")
                .html('<option value="">Select Enquiry</option>')
                .prop("disabled", true);
            $("#add_grand_total_amount").val("");
            $(".add_srch_enq_id").val("");
            toggleBank("add");
        });


        $("#add_customer_id").on("change", function () {
            var customer_id = $(this).val();
            add_load_invoice_list(customer_id);
        });


        function add_load_invoice_list(customer_id) {

            var tableId = "#customer_invoice_receipt_list_table";
            var $container = $("#add_item_container");

            // ✅ Destroy DataTable BEFORE updating
            if ($.fn.DataTable.isDataTable(tableId)) {
                $(tableId).DataTable().clear().destroy();
            }

            $container.html("");

            if (!customer_id) return;

            $.ajax({
                url: "<?php echo site_url('payment/get_customer_invoice_list_ajax'); ?>",
                type: "POST",
                data: { customer_id: customer_id },
                dataType: "json",
                success: function (record_list) {

                    if (!record_list || record_list.length === 0) {
                        $container.html(
                            '<tr><td colspan="5" class="text-center text-muted">No invoices found.</td></tr>'
                        );
                        return;
                    }

                    $.each(record_list, function (i, row) {

                        var amount = parseFloat(row.total_amount) || 0;
                        var balance_amount = parseFloat(row.balance_amount) || 0;

                        var checkbox = "";
                        var input_box = "";
                        var balance_label = "";

                        if (balance_amount === 0) {

                            checkbox = '<span class="label label-success">Paid</span>';

                            balance_label = '<label class="label label-success">Paid</label>';

                            input_box = '<input type="text" value="0.000" class="form-control text-right" readonly>';

                        } else {

                            checkbox = '<input type="checkbox" class="select-item"' +
                                ' name="selected_items[]"' +
                                ' data-amount="' + balance_amount + '"' +
                                ' value="' + i + '">';

                            balance_label = '<label class="label label-danger">Bal: ' +
                                format3(balance_amount) +
                                '</label>';

                         
                            input_box = '<input type="text" name="inv_amount[' + i + ']"  ' +
                                ' class="form-control invoice-amount-input text-right">';
                        }

                        $container.append(
                            "<tr>" +
                            '<td class="text-center">' + checkbox + "</td>" +
                            "<td>" + row.invoice_date +
                            '<input type="hidden" name="tender_enq_invoice_id[' + i + ']" value="' +
                            row.tender_enq_invoice_id + '">' +
                            '<input type="hidden" name="tender_enquiry_id[' + i + ']" value="' +
                            row.tender_enquiry_id + '">' +
                            "</td>" +
                            "<td>" + row.invoice_no + "</td>" +
                            "<td>" + row.tender_details + "</td>" +
                            '<td class="text-right">' + format3(amount) + "<br>" + balance_label + "</td>" +
                            "<td>" + input_box + "</td>" +
                            "</tr>"
                        );
                    });

                
                    $(tableId).DataTable({
                        paging: true,
                        searching: true,
                        ordering: true,
                        info: true,
                        autoWidth: false,
                        destroy: true // extra safety
                    });

                    calculateGrandTotal("add");
                },
                error: function () {
                    alert("Error loading invoice list.");
                }
            });
        }


        $(document).on("click", ".edit_record", function () {
            var tender_receipt_id = $(this).data("id");

            // Reset first
            $("#frmedit")[0].reset();
            $("#edit_item_container").html("");
            $("#edit_tender_enquiry_id")
                .html('<option value="">Select Enquiry</option>')
                .prop("disabled", true);
            $("#edit_grand_total_amount").val("");
            $(".edit_srch_enq_id").val("");
            toggleBank("edit");

            $.ajax({
                url: "<?php echo site_url('payment/get_data'); ?>",
                type: "POST",
                data: {
                    tbl: "tender_receipt_info_invoice_list",
                    tender_receipt_id: tender_receipt_id,
                },
                dataType: "json",
                success: function (res) {
                    if (!res || res.length === 0) {
                        alert("No data found.");
                        return;
                    }

                    var data = res[0];

                    $("#edit_tender_receipt_id").val(data.tender_receipt_id);
                    $("#edit_receipt_date").val(data.receipt_date);
                    $("#edit_remarks").val(data.remarks);
                    $("#edit_receipt_mode").val(data.receipt_mode).trigger("change"); // also toggles bank

                    if (data.bank_id) {
                        $("#edit_bank_id").val(data.bank_id);
                    }

                    // Status radio
                    $('#frmedit input[name="status"]')
                        .filter('[value="' + data.status + '"]')
                        .prop("checked", true);

                    // Set receipt type radio
                    $('#frmedit input[name="receipt_type"]')
                        .filter('[value="' + data.receipt_type + '"]')
                        .prop("checked", true);

                    // Trigger change to auto show/hide
                    $('#frmedit input[name="receipt_type"]:checked').trigger('change');

                    // If Cheque → set values
                    if (data.receipt_type === 'Cheque') {
                        $('#edit_cheque_no').val(data.cheque_no);
                        $('#edit_cheque_date').val(data.cheque_date);
                        $('#edit_cheque_bank').val(data.cheque_bank);
                    }

                    // Customer → load enquiry dropdown → load invoice list
                    $("#edit_customer_id").val(data.customer_id).trigger("change");
                    edit_load_invoice_list(data.tender_receipt_id, data.customer_id);
                },
                error: function () {
                    alert("Error loading record.");
                },
            });
        });
    
        function toggleReceiptType(type, mode = 'add') {

            if (type === 'Cheque') {
                $('#' + mode + '_receipt_type_div').show();
            } else {
                $('#' + mode + '_receipt_type_div').hide();
                $('#' + mode + '_cheque_no').val('');
                $('#' + mode + '_cheque_date').val('');
                $('#' + mode + '_cheque_bank').val('');
            }
        }

        // Add form
        $('input[name="receipt_type"]').on('change', function () {
            toggleReceiptType($(this).val(), 'add');
        });

        // Edit form
        $('#frmedit input[name="receipt_type"]').on('change', function () {
            toggleReceiptType($(this).val(), 'edit');
        });


        $("#edit_customer_id").on("change", function () {
            var tender_receipt_id = $("#edit_tender_receipt_id").val();
            var customer_id = $(this).val();
            if (customer_id) {
                edit_load_invoice_list(tender_receipt_id, customer_id);
            } else {
                $("# #edit_item_container").html("");
                calculateGrandTotal("edit");
            }
        });

        function edit_load_invoice_list(tender_receipt_id, customer_id) {

            var $container = $("#customer_invoice_receipt_list_table #edit_item_container");
            $container.html("");

            if (!customer_id) return;

            $.ajax({
                url: "<?php echo site_url('payment/load_receipt_customer_invoice_data'); ?>",
                type: "POST",
                data: {
                    tender_receipt_id: tender_receipt_id,
                    customer_id: customer_id
                },
                dataType: "json",

                success: function (record_list) {

                    var tableId = "#edit_customer_invoice_receipt_list_table";

                    if ($.fn.DataTable.isDataTable(tableId)) {
                        $(tableId).DataTable().destroy();
                    }

                    var $container = $("#edit_item_container");
                    $container.html("");

                    if (!record_list || record_list.length === 0) {
                        $container.html('<tr><td colspan="6" class="text-center">No invoices found</td></tr>');
                    } else {

                        $.each(record_list, function (i, row) {

                            var tender_receipt_invoice_id = row.tender_receipt_invoice_id;

                            var total_amount = parseFloat(row.total_amount) || 0;
                            var balance_amount = parseFloat(row.balance) || 0;

                            // ✅ FIXED (use correct field)
                            var current_paid = parseFloat(row.current_paid_amount) || 0;

                            var checkbox = '';

                            // ✅ Checkbox logic
                            if (tender_receipt_invoice_id && tender_receipt_invoice_id != 0) {

                                checkbox =
                                    '<input type="checkbox" class="select-item" ' +
                                    'name="selected_items[]" ' +
                                    'data-amount="' + balance_amount + '" ' +
                                    'value="' + i + '" checked>';

                            } else {

                                checkbox =
                                    '<input type="checkbox" class="select-item" ' +
                                    'name="selected_items[]" ' +
                                    'data-amount="' + balance_amount + '" ' +
                                    'value="' + i + '">';
                            }

                            var input_box = '';

                            // ✅ Input logic
                            if (tender_receipt_invoice_id && tender_receipt_invoice_id != 0) {

                                input_box =
                                    '<input type="text" class="form-control invoice-amount-input text-right" ' +
                                    'name="inv_amount[' + i + ']" ' +
                                    'value="' + format3(current_paid) + '">';

                            } else {

                                input_box =
                                    '<input type="text" class="form-control invoice-amount-input text-right" ' +
                                    'name="inv_amount[' + i + ']" ' +
                                    'value="0.000" readonly>';
                            }

                            // ✅ Balance label
                            var balance_label = '<span class="text-danger">Bal: ' + format3(balance_amount) + '</span>';

                            $container.append(
                                "<tr>" +
                                '<td class="text-center">' + checkbox + '</td>' +

                                "<td>" + row.invoice_date +
                                '<input type="hidden" name="tender_enq_invoice_id[' + i + ']" value="' + row.tender_enq_invoice_id + '">' +
                                '<input type="hidden" name="tender_enquiry_id[' + i + ']" value="' + row.tender_enquiry_id + '">' +
                                '<input type="hidden" name="tender_receipt_invoice_id[' + i + ']" value="' + (tender_receipt_invoice_id ? tender_receipt_invoice_id : '') + '">' +
                                "</td>" +

                                "<td>" + row.invoice_no + "</td>" +
                                "<td>" + row.tender_details + "</td>" +

                                '<td class="text-right">' +
                                format3(total_amount) + "<br>" + balance_label +
                                "</td>" +

                                "<td>" + input_box + "</td>" +
                                "</tr>"
                            );
                        });
                    }

                    $(tableId).DataTable({
                        paging: true,
                        searching: true,
                        ordering: true,
                        info: true,
                        autoWidth: false,
                        destroy: true
                    });

                    calculateGrandTotal("edit");
                }
            });
        }

        $("#srch_enquiry_no").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url('payment/tender_enquiry_id_search'); ?>",
                    type: "POST",
                    data: { search: request.term },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        response(data);
                    },
                });
            },

            minLength: 1,

            select: function (event, ui) {
                console.log(ui);
                // $("#srch_company_id").val(ui.item.company_id);
                $(".tender_enquiry_id_value_id").val(ui.item.tender_enquiry_id);
            },
        });

        $(document).on('change', '.select-item', function () {

            var row = $(this).closest('tr');
            var input = row.find('.invoice-amount-input');
            var balance = parseFloat($(this).data('amount')) || 0;

            if ($(this).is(':checked')) {
                input.prop('readonly', false).val(format3(balance)); // auto fill
            } else {
                input.prop('readonly', true).val('0.000');
            }

            calculateGrandTotal("edit");
        });
    });
</script>