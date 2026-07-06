
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
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {

        // ============================================================
        // AMOUNT DISTRIBUTION LOGIC
        // ============================================================
        function distributeAmount(prefix, startFromRow = null) {
            var overallAmount = parseFloat($("#" + prefix + "_grand_total_amount").val()) || 0;
            var remaining = overallAmount;

            // Handle without bill if checked
            if ($("#" + prefix + "_is_without_bill").is(":checked")) {
                var without_bill_val = parseFloat($("#" + prefix + "_without_bill_amount").val()) || 0;
                remaining = parseFloat((remaining - without_bill_val).toFixed(3));
            }

            var lockSubsequent = false;
            var totalAllocated = 0;

            // Iterate over all rows in the table
            $("#" + prefix + "_item_container tr").each(function () {
                var $row = $(this);
                var $chk = $row.find(".select-item");
                var $input = $row.find(".invoice-amount-input");

                if ($chk.length === 0) return; // skip row if no checkbox

                if ($chk.is(":checked")) {
                    var maxAmount = parseFloat($chk.attr("data-amount")) || 0;
                    var payNow = 0;

                    // If we are before or at the edited row, we preserve the current input value (capped)
                    if (startFromRow !== null) {
                        if (lockSubsequent) {
                            // We are AFTER the edited row, so we distribute the remaining amount
                            if (remaining > 0) {
                                if (remaining >= maxAmount) {
                                    payNow = maxAmount;
                                    remaining = parseFloat((remaining - maxAmount).toFixed(3));
                                } else {
                                    payNow = remaining;
                                    remaining = 0;
                                }
                            } else {
                                payNow = 0;
                            }
                            $input.val(format3(payNow));
                        } else {
                            // We are BEFORE or AT the edited row
                            var currentVal = parseFloat($input.val()) || 0;
                            // Cap currentVal to maxAmount and remaining
                            if (currentVal < 0) currentVal = 0;
                            if (currentVal > maxAmount) currentVal = maxAmount;
                            if (currentVal > remaining) currentVal = remaining;
                            
                            payNow = currentVal;
                            $input.val(format3(payNow));
                            remaining = parseFloat((remaining - payNow).toFixed(3));
                        }
                    } else {
                        // Standard oldest-first distribution
                        if (remaining > 0) {
                            if (remaining >= maxAmount) {
                                payNow = maxAmount;
                                remaining = parseFloat((remaining - maxAmount).toFixed(3));
                            } else {
                                payNow = remaining;
                                remaining = 0;
                            }
                        } else {
                            payNow = 0;
                        }
                        $input.val(format3(payNow));
                    }

                    $input.prop("readonly", false);
                    totalAllocated = parseFloat((totalAllocated + payNow).toFixed(3));
                } else {
                    $input.prop("readonly", true).val("0.000");
                }

                // If this is the startFromRow, we lock subsequent rows to distribute remaining amount
                if (startFromRow !== null && $row.is(startFromRow)) {
                    lockSubsequent = true;
                }
            });

            // Update the allocated total
            $("#" + prefix + "_alloc_total").text(format3(totalAllocated));
            $("#" + prefix + "_alloc_tfoot").show();
            
            // Show allocation info
            var msg = "Allocated " + format3(totalAllocated) + " across checked invoice(s)";
            var remainingUnallocated = parseFloat((overallAmount - totalAllocated).toFixed(3));
            if (remainingUnallocated > 0) {
                msg += ". <span class='text-danger'>Unallocated Balance: " + format3(remainingUnallocated) + "</span>";
                $("#" + prefix + "_allocate_msg").html(msg);
                $("#" + prefix + "_allocate_info").slideDown(200);
            } else {
                $("#" + prefix + "_allocate_info").hide();
            }
        }

        function updateAllocatedFooter(prefix) {
            var overallAmount = parseFloat($("#" + prefix + "_grand_total_amount").val()) || 0;
            var totalAllocated = 0;

            $("#" + prefix + "_item_container tr").each(function () {
                var $row = $(this);
                var $chk = $row.find(".select-item");
                var $input = $row.find(".invoice-amount-input");

                if ($chk.length > 0 && $chk.is(":checked")) {
                    var val = parseFloat($input.val()) || 0;
                    totalAllocated += val;
                    $input.prop("readonly", false); // make sure it's writable
                } else if ($chk.length > 0) {
                    $input.prop("readonly", true); // make sure it's read-only
                }
            });

            if ($("#" + prefix + "_is_without_bill").is(":checked")) {
                var without_bill_val = parseFloat($("#" + prefix + "_without_bill_amount").val()) || 0;
                totalAllocated += without_bill_val;
            }

            totalAllocated = parseFloat(totalAllocated.toFixed(3));
            $("#" + prefix + "_alloc_total").text(format3(totalAllocated));
            $("#" + prefix + "_alloc_tfoot").show();

            var remainingUnallocated = parseFloat((overallAmount - totalAllocated).toFixed(3));
            if (remainingUnallocated > 0) {
                var msg = "Allocated " + format3(totalAllocated) + " across checked invoice(s)";
                msg += ". <span class='text-danger'>Unallocated Balance: " + format3(remainingUnallocated) + "</span>";
                $("#" + prefix + "_allocate_msg").html(msg);
                $("#" + prefix + "_allocate_info").slideDown(200);
            } else {
                $("#" + prefix + "_allocate_info").hide();
            }
        }

        function autoAllocate(prefix) {
            var overallAmount = parseFloat($("#" + prefix + "_grand_total_amount").val()) || 0;
            if (overallAmount <= 0) {
                alert("Please enter a valid receipt amount before auto-allocating.");
                return;
            }

            // Check all checkboxes first
            $("#" + prefix + "_item_container .select-item").prop("checked", true);

            // Distribute
            distributeAmount(prefix);

            // Uncheck checkboxes that ended up with 0 pay amount
            $("#" + prefix + "_item_container tr").each(function () {
                var $row = $(this);
                var $chk = $row.find(".select-item");
                var $input = $row.find(".invoice-amount-input");
                if ($chk.length > 0) {
                    var val = parseFloat($input.val()) || 0;
                    if (val <= 0) {
                        $chk.prop("checked", false);
                        $input.prop("readonly", true).val("0.000");
                    }
                }
            });
            
            // Show toastr
            toastr.success("Receipt allocated successfully!");
        }

        // Wire up Auto Allocate buttons
        $("#add_auto_allocate_btn").on("click", function () {
            autoAllocate("add");
        });
        $("#edit_auto_allocate_btn").on("click", function () {
            autoAllocate("edit");
        });

        // Redistribute payment when overall amount is manually changed
        $("#add_grand_total_amount, #edit_grand_total_amount").on("input", function () {
            var prefix = $(this).attr("id").startsWith("add") ? "add" : "edit";
            distributeAmount(prefix);
        });

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


        function format3(val) {
            return parseFloat(val || 0).toFixed(3);
        }

        function toggleBank(prefix) {
            var mode = $("#" + prefix + "_receipt_mode").val();
            if (mode === "Bank") {
                $("#" + prefix + "_bank_div").show();
                $("#" + prefix + "_receipt_type_container").show();
            } else {
                $("#" + prefix + "_bank_div").hide();
                $("#" + prefix + "_bank_id").val("");
                $("#" + prefix + "_receipt_type_container").hide();
                // Uncheck radios and hide details
                $('input[name="receipt_type"]').prop('checked', false);
                $('#' + prefix + '_receipt_type_div').hide();
                $('#' + prefix + '_cheque_no').val('');
                $('#' + prefix + '_cheque_date').val('');
                $('#' + prefix + '_cheque_bank').val('');
            }
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
            var prefix =
                $(this).closest(".modal").attr("id") === "add_modal" ? "add" : "edit";

            if ($(this).is(":checked")) {
                $input.prop("readonly", false);

                // Compute remaining amount before this row
                var overallAmount = parseFloat($("#" + prefix + "_grand_total_amount").val()) || 0;
                var allocatedBefore = 0;

                // Deduct without bill amount if checked
                if ($("#" + prefix + "_is_without_bill").is(":checked")) {
                    allocatedBefore += parseFloat($("#" + prefix + "_without_bill_amount").val()) || 0;
                }

                var currentRowIndex = $row.index();
                $("#" + prefix + "_item_container tr").each(function () {
                    var $r = $(this);
                    if ($r.index() < currentRowIndex) {
                        var $c = $r.find(".select-item");
                        if ($c.length > 0 && $c.is(":checked")) {
                            allocatedBefore += parseFloat($r.find(".invoice-amount-input").val()) || 0;
                        }
                    }
                });

                var maxAmount = parseFloat($(this).attr("data-amount")) || 0;
                var remaining = Math.max(0, overallAmount - allocatedBefore);
                var initialVal = Math.min(maxAmount, remaining);
                $input.val(format3(initialVal));
            } else {
                $input.val("0.000").prop("readonly", true);
            }

            distributeAmount(prefix, $row);
        });

        $(document).on("input", ".invoice-amount-input", function () {
            var $row = $(this).closest("tr");
            var prefix =
                $(this).closest(".modal").attr("id") === "add_modal" ? "add" : "edit";
            distributeAmount(prefix, $row);
        });

        $(document).on("blur", ".invoice-amount-input", function () {
            var $row = $(this).closest("tr");
            var prefix =
                $(this).closest(".modal").attr("id") === "add_modal" ? "add" : "edit";
            $(this).val(format3($(this).val()));
            distributeAmount(prefix, $row);
        });

        // Without Bill Checkbox and Input handlers
        $(document).on("change", "#add_is_without_bill, #edit_is_without_bill", function () {
            var prefix = $(this).attr("id").startsWith("add") ? "add" : "edit";
            var $input = $("#" + prefix + "_without_bill_amount");
            if ($(this).is(":checked")) {
                $input.show().focus();
            } else {
                $input.val("0.000").hide();
            }
            distributeAmount(prefix);
        });

        $(document).on("input change blur", "#add_without_bill_amount, #edit_without_bill_amount", function (e) {
            var prefix = $(this).attr("id").startsWith("add") ? "add" : "edit";
            if (e.type === "blur") {
                $(this).val(format3($(this).val()));
            }
            distributeAmount(prefix);
        });

        $("#add_select_all_items").on("change", function () {
            var isChecked = $(this).is(":checked");
            $("#add_item_container .select-item").prop("checked", isChecked);

            $("#add_item_container tr").each(function() {
                var $chk = $(this).find(".select-item");
                var $input = $(this).find(".invoice-amount-input");
                if ($chk.length > 0) {
                    if (isChecked) {
                        $input.prop("readonly", false);
                    } else {
                        $input.val("0.000").prop("readonly", true);
                    }
                }
            });
            distributeAmount("add");
        });

        $("#edit_select_all_items").on("change", function () {
            var isChecked = $(this).is(":checked");
            $("#edit_item_container .select-item").prop("checked", isChecked);

            $("#edit_item_container tr").each(function() {
                var $chk = $(this).find(".select-item");
                var $input = $(this).find(".invoice-amount-input");
                if ($chk.length > 0) {
                    if (isChecked) {
                        $input.prop("readonly", false);
                    } else {
                        $input.val("0.000").prop("readonly", true);
                    }
                }
            });
            distributeAmount("edit");
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
            $("#add_modal").data("initial_load", true);
            $("#frmadd")[0].reset();
            $("#add_item_container").html("");
            $("#add_tender_enquiry_id")
                .html('<option value="">Select Enquiry</option>')
                .prop("disabled", true);
            $("#add_grand_total_amount").val("");
            $(".add_srch_enq_id").val("");
            toggleBank("add");
            // Reset without bill elements
            $("#add_is_without_bill").prop("checked", false);
            $("#add_without_bill_amount").val("0.000").hide();
            // Reset balance panel
            $('#add_customer_balance_panel').hide();
            $('#add_bal_customer_name').text('');
            $('#add_bal_current').text('0.000');
            $('#add_bal_current_label').html('');
            // Reset allocation panel
            $('#add_allocate_info').hide();
            $('#add_alloc_tfoot').hide();
            $('#add_alloc_total').text('0.000');
        });

        // ============================================================
        // Customer Balance Summary — inside Add Modal only (current balance)
        // ============================================================
        function loadAddModalCustomerBalance(customer_id) {
            if (!customer_id) {
                $('#add_customer_balance_panel').hide();
                return;
            }
            var payment_date = $('#add_receipt_date').val();
            $.ajax({
                url: "<?php echo site_url('get-customer-balance-summary'); ?>",
                type: "POST",
                data: { 
                    customer_id: customer_id,
                    payment_date: payment_date
                },
                dataType: "json",
                success: function (res) {
                    if (!res || res.status !== 'success') {
                        $('#add_customer_balance_panel').hide();
                        return;
                    }

                    // Customer name
                    var cname = (typeof customerOpt !== 'undefined' && customerOpt[customer_id])
                        ? customerOpt[customer_id] : 'Customer #' + customer_id;
                    $('#add_bal_customer_name').text(cname);

                    // Current Balance
                    var bal = parseFloat(res.current_balance.replace(/,/g, ''));
                    $('#add_bal_current').text(res.current_balance);

                    if (bal > 0) {
                        $('#add_bal_current').css('color', '#c62828');
                        $('#add_bal_current_label').html('<span class="label label-danger">Receivable from Customer</span>');
                    } else if (bal < 0) {
                        $('#add_bal_current').css('color', '#2e7d32');
                        $('#add_bal_current_label').html('<span class="label label-success">Advance/Overpaid</span>');
                    } else {
                        $('#add_bal_current').css('color', '#555');
                        $('#add_bal_current_label').html('<span class="label label-default">Settled</span>');
                    }

                    $('#add_customer_balance_panel').slideDown(200);
                },
                error: function () {
                    $('#add_customer_balance_panel').hide();
                }
            });
        }

        $("#add_customer_id").on("change", function () {
            var customer_id = $(this).val();
            // Reset allocation panel when customer changes
            $('#add_allocate_info').hide();
            $('#add_alloc_tfoot').hide();
            add_load_invoice_list(customer_id);
            loadAddModalCustomerBalance(customer_id);
        });

        // Recalculate balance when receipt date changes
        $('#add_receipt_date').on('change', function () {
            var customer_id = $('#add_customer_id').val();
            if (customer_id) {
                loadAddModalCustomerBalance(customer_id);
            }
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
                success: function (record_list) {                    if (!record_list || record_list.length === 0) {
                        $container.html(
                            '<tr><td colspan="6" class="text-center text-muted">No invoices found.</td></tr>'
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
                            '<input type="hidden" name="bill_type[' + i + ']" value="' +
                            row.bill_type + '">' +
                            "</td>" +
                            "<td>" + row.invoice_no + "</td>" +
                            "<td>" + row.tender_details + "</td>" +
                            "<td>" + row.bill_type + "</td>" +
                            '<td class="text-right">' + format3(amount) + "<br>" + balance_label + "</td>" +
                            "<td>" + input_box + "</td>" +
                            "</tr>"
                        );
                    });

                    $(tableId).DataTable({
                        paging: false,
                        searching: true,
                        ordering: true,
                        info: true,
                        autoWidth: false,
                        destroy: true // extra safety
                    });

                    distributeAmount("add");
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

                    $("#edit_modal").data("initial_load", true);
                    $("#edit_grand_total_amount").val(format3(data.amount));

                    if (data.is_without_bill == 1) {
                        $("#edit_is_without_bill").prop("checked", true);
                        $("#edit_without_bill_amount").val(format3(data.without_bill_amount)).show();
                    } else {
                        $("#edit_is_without_bill").prop("checked", false);
                        $("#edit_without_bill_amount").val("0.000").hide();
                    }

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
                                    'data-amount="' + (balance_amount + current_paid) + '" ' +
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
                                '<input type="hidden" name="bill_type[' + i + ']" value="' + row.bill_type + '">' +
                                "</td>" +

                                "<td>" + row.invoice_no + "</td>" +
                                "<td>" + row.tender_details + "</td>" +
                                "<td>" + row.bill_type + "</td>" +

                                '<td class="text-right">' +
                                format3(total_amount) + "<br>" + balance_label +
                                "</td>" +

                                "<td>" + input_box + "</td>" +
                                "</tr>"
                            );
                        });
                    }

                    $(tableId).DataTable({
                        paging: false,
                        searching: true,
                        ordering: true,
                        info: true,
                        autoWidth: false,
                        destroy: true
                    });

                    updateAllocatedFooter("edit");
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
    });
</script>