
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

        // ============================================================
        // AUTO-ALLOCATE: Oldest-first payment distribution
        // ============================================================
        function autoAllocatePayment(prefix) {
            var totalPayment = parseFloat($("#" + prefix + "_grand_total_amount").val()) || 0;

            if (totalPayment <= 0) {
                alert("Please enter a valid payment amount before auto-allocating.");
                return;
            }

            var remaining = totalPayment;
            var totalAllocated = 0;
            var fullyPaidCount = 0;
            var partialCount = 0;

            // Uncheck all rows first, reset amounts
            $("#" + prefix + "_item_container tr").each(function () {
                var $row = $(this);
                var $chk = $row.find(".select-item");
                var $input = $row.find(".invoice-amount-input");
                if ($chk.length > 0) {
                    $chk.prop("checked", false);
                    $input.prop("readonly", true).val("0.000");
                }
            });

            // Now allocate oldest-first (rows are already in invoice_date ASC order)
            $("#" + prefix + "_item_container tr").each(function () {
                if (remaining <= 0) return false; // break

                var $row = $(this);
                var $chk = $row.find(".select-item");
                var $input = $row.find(".invoice-amount-input");

                if ($chk.length === 0) return; // skip non-selectable rows

                // balance for this bill (stored in data-amount)
                var billBalance = parseFloat($chk.data("amount")) || 0;
                if (billBalance <= 0) return;

                var payNow = 0;

                if (remaining >= billBalance) {
                    // Full payment of this bill
                    payNow = billBalance;
                    remaining = parseFloat((remaining - billBalance).toFixed(3));
                    fullyPaidCount++;
                } else {
                    // Partial payment — remaining covers only part of this bill
                    payNow = remaining;
                    remaining = 0;
                    partialCount++;
                }

                $chk.prop("checked", true);
                $input.prop("readonly", false).val(parseFloat(payNow).toFixed(3));
                totalAllocated = parseFloat((totalAllocated + payNow).toFixed(3));
            });

            // Update grand total to match exactly what was allocated
            // (it should already equal totalPayment if enough bills exist)
            // Show summary footer
            $("#" + prefix + "_alloc_total").text(parseFloat(totalAllocated).toFixed(3));
            $("#" + prefix + "_alloc_tfoot").show();

            // Build info message
            var msg = "Auto-allocated " + parseFloat(totalAllocated).toFixed(3) +
                " across " + (fullyPaidCount + partialCount) + " bill(s)";
            if (fullyPaidCount > 0) msg += " ("+fullyPaidCount+" fully paid)";
            if (partialCount > 0)   msg += " ("+partialCount+" partial)";
            if (remaining > 0) {
                msg += ". <span class='text-danger'>Unallocated Balance: " +
                    parseFloat(remaining).toFixed(3) + "</span>";

                // SweetAlert for Unallocated Balance
                Swal.fire({
                    icon: 'warning',
                    title: 'Unallocated Balance!',
                    html: 'Payment amount exceeds the total outstanding bills.<br><br><b>Unallocated Balance: ' + parseFloat(remaining).toFixed(3) + '</b> will be left.',
                    confirmButtonColor: '#f9a825'
                });
            } else {
                // Toastr success notification
                toastr.success("Payment allocated successfully!");
            }
            $("#" + prefix + "_allocate_msg").html(msg);
            $("#" + prefix + "_allocate_info").slideDown(200);

            // Recalculate grand total from checked rows to stay consistent
            calculateGrandTotal(prefix);
        }

        // Wire up Auto Allocate buttons
        $("#add_auto_allocate_btn").on("click", function () {
            autoAllocatePayment("add");
        });
        $("#edit_auto_allocate_btn").on("click", function () {
            autoAllocatePayment("edit");
        });

        // Hide allocation info when amount is manually changed
        $("#add_grand_total_amount, #edit_grand_total_amount").on("input", function () {
            var prefix = $(this).attr("id").startsWith("add") ? "add" : "edit";
            $("#" + prefix + "_allocate_info").hide();
            $("#" + prefix + "_alloc_tfoot").hide();
        });


        function loadAddModalVendorBalance(vendor_id) {
            if (!vendor_id) {
                $('#add_vendor_balance_panel').hide();
                return;
            }
            var payment_date = $('#add_payment_date').val();
            $.ajax({
                url: "<?php echo site_url('get-vendor-balance-summary'); ?>",
                type: "POST",
                data: { 
                    vendor_id: vendor_id,
                    payment_date: payment_date
                },
                dataType: "json",
                success: function (res) {
                    if (!res || res.status !== 'success') {
                        $('#add_vendor_balance_panel').hide();
                        return;
                    }

                    // Vendor name
                    var vname = (typeof vendorOpt !== 'undefined' && vendorOpt[vendor_id])
                        ? vendorOpt[vendor_id] : 'Vendor #' + vendor_id;
                    $('#add_bal_vendor_name').text(vname);

                    // Current Balance
                    var bal = parseFloat(res.current_balance.replace(/,/g, ''));
                    $('#add_bal_current').text(res.current_balance);

                    if (bal > 0) {
                        $('#add_bal_current').css('color', '#c62828');
                        $('#add_bal_current_label').html('<span class="label label-danger">Payable to Vendor</span>');
                    } else if (bal < 0) {
                        $('#add_bal_current').css('color', '#2e7d32');
                        $('#add_bal_current_label').html('<span class="label label-success">Advance/Overpaid</span>');
                    } else {
                        $('#add_bal_current').css('color', '#555');
                        $('#add_bal_current_label').html('<span class="label label-default">Settled</span>');
                    }

                    $('#add_vendor_balance_panel').slideDown(200);
                },
                error: function () {
                    $('#add_vendor_balance_panel').hide();
                }
            });
        }


        // Fire when vendor is selected in Add modal
        $('#add_vendor_id').on('change', function () {
            loadAddModalVendorBalance($(this).val());
        });

        // Recalculate balance when payment date changes
        $('#add_payment_date').on('change', function () {
            var vendor_id = $('#add_vendor_id').val();
            if (vendor_id) {
                loadAddModalVendorBalance(vendor_id);
            }
        });



        // On change of receipt type
        $('input[name="payment_type"]').on('change', function () {

            if ($(this).val() === 'Cheque') {
                $('#add_payment_type_div').show();
            } else {
                $('#add_payment_type_div').hide();
                $('#add_cheque_no').val('');
                $('#add_cheque_date').val('');
                $('#add_cheque_bank').val('');
            }

        });

        // On change of receipt type
        $('input[name="payment_type"]').on('change', function () {

            if ($(this).val() === 'Cheque') {
                $('#edit_payment_type_div').show();
            } else {
                $('#edit_payment_type_div').hide();
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
            var mode = $("#" + prefix + "_payment_mode").val();
            if (mode === "Bank") {
                $("#" + prefix + "_bank_div").show();
                $("#" + prefix + "_payment_type_container").show();
            } else {
                $("#" + prefix + "_bank_div").hide();
                $("#" + prefix + "_bank_id").val("");
                $("#" + prefix + "_payment_type_container").hide();
                // Uncheck radios and hide details
                $('input[name="payment_type"]').prop('checked', false);
                $('#' + prefix + '_payment_type_div').hide();
                $('#' + prefix + '_cheque_no').val('');
                $('#' + prefix + '_cheque_date').val('');
                $('#' + prefix + '_cheque_bank').val('');
            }
        }

        function calculateGrandTotal(prefix) {

            var grand_total = 0;
            var any_checked = false;

            $("#" + prefix + "_item_container tr").each(function () {

                var $row = $(this);
                var $chk = $row.find(".select-item");
                var $input = $row.find(".invoice-amount-input");

                var value = parseFloat($input.val()) || 0;


                if ($chk.length > 0) {
                    if ($chk.is(":checked")) {
                        grand_total += value;
                        any_checked = true;
                    }
                }

                else {
                    // Do not add if no select-item
                }

            });

            // Add without bill amount if checkbox is checked
            if ($("#" + prefix + "_is_without_bill").is(":checked")) {
                var without_bill_val = parseFloat($("#" + prefix + "_without_bill_amount").val()) || 0;
                grand_total += without_bill_val;
                any_checked = true;
            }

            var modal = $("#" + prefix + "_modal");
            var is_initial = modal.data("initial_load");

            if (is_initial) {
                if (any_checked) {
                    $("#" + prefix + "_grand_total_amount").val(format3(grand_total));
                }
                modal.data("initial_load", false);
            } else {
                $("#" + prefix + "_grand_total_amount").val(format3(grand_total));
            }
        }

        $("#add_payment_mode").on("change", function () {
            toggleBank("add");
        });

        $("#edit_payment_mode").on("change", function () {
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

        // Without Bill Checkbox and Input handlers
        $(document).on("change", "#add_is_without_bill, #edit_is_without_bill", function () {
            var prefix = $(this).attr("id").startsWith("add") ? "add" : "edit";
            var $input = $("#" + prefix + "_without_bill_amount");
            if ($(this).is(":checked")) {
                $input.show().focus();
            } else {
                $input.val("0.000").hide();
            }
            calculateGrandTotal(prefix);
        });

        $(document).on("input change blur", "#add_without_bill_amount, #edit_without_bill_amount", function (e) {
            var prefix = $(this).attr("id").startsWith("add") ? "add" : "edit";
            if (e.type === "blur") {
                $(this).val(format3($(this).val()));
            }
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
                data: { tbl: "vendor_payment_info_list", id: id },
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
            $('#add_vendor_balance_panel').hide();
            $('#add_bal_vendor_name').text('');
            $('#add_bal_current').text('0.000');
            $('#add_bal_current_label').html('');
            // Reset allocation panel
            $('#add_allocate_info').hide();
            $('#add_alloc_tfoot').hide();
            $('#add_alloc_total').text('0.000');
        });


        $("#add_vendor_id").on("change", function () {
            var vendor_id = $(this).val();
            // Reset allocation panel when vendor changes
            $('#add_allocate_info').hide();
            $('#add_alloc_tfoot').hide();
            add_load_vendor_payment_list(vendor_id);

        });


        function add_load_vendor_payment_list(vendor_id) {

            var tableId = "#add_vendor_payment_list_table";
            var $container = $("#add_item_container");

            // ✅ Destroy DataTable BEFORE updating
            if ($.fn.DataTable.isDataTable(tableId)) {
                $(tableId).DataTable().clear().destroy();
            }

            $container.html("");

            if (!vendor_id) return;

            $.ajax({
                url: "<?php echo site_url('payment/get_vendor_payment_blil_list'); ?>",
                type: "POST",
                data: { vendor_id: vendor_id },
                dataType: "json",
                success: function (record_list) {

                    if (!record_list || record_list.length === 0) {
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


                            input_box = '<input type="text" name="bill_amount[' + i + ']"  ' +
                                ' class="form-control invoice-amount-input text-right">';
                        }

                        $container.append(
                            "<tr>" +
                            '<td class="text-center">' + checkbox + "</td>" +
                            "<td>" + row.bill_date +
                            '<input type="hidden" name="bill_id[' + i + ']" value="' +
                            row.bill_id + '">' +
                            '<input type="hidden" name="bill_type[' + i + ']" value="' +
                            row.bill_type + '">' +
                            "</td>" +
                            "<td>" + row.bill_no + "</td>" +
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

                    calculateGrandTotal("add");
                },
                error: function () {
                    alert("Error loading invoice list.");
                }
            });
        }


        $(document).on("click", ".edit_record", function () {
            var vendor_payment_id = $(this).data("id");

           
            $("#frmedit")[0].reset();
            $("#edit_item_container").html(""); 

            toggleBank("edit");

            $.ajax({
                url: "<?php echo site_url('payment/get_data'); ?>",
                type: "POST",
                data: {
                    tbl: "vendor_payment_list_info_ajax",
                    vendor_payment_id: vendor_payment_id ,
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

                    $("#edit_vendor_payment_id").val(data.vendor_payment_id);
                    $("#edit_payment_date").val(data.payment_date);
                    $("#edit_remarks").val(data.remarks);
                    $("#edit_payment_mode").val(data.payment_mode).trigger("change"); // also toggles bank

                    if (data.bank_id) {
                        $("#edit_bank_id").val(data.bank_id);
                    }

                    // Status radio
                    $('#frmedit input[name="status"]')
                        .filter('[value="' + data.status + '"]')
                        .prop("checked", true);

                    // Set receipt type radio
                    $('#frmedit input[name="payment_type"]')
                        .filter('[value="' + data.payment_type + '"]')
                        .prop("checked", true);

                    // Trigger change to auto show/hide
                    $('#frmedit input[name="payment_type"]:checked').trigger('change');

                    // If Cheque → set values
                    if (data.receipt_type === 'Cheque') {
                        $('#edit_cheque_no').val(data.cheque_no);
                        $('#edit_cheque_date').val(data.cheque_date);
                        $('#edit_cheque_bank').val(data.cheque_bank);
                    }

                    // Customer → load enquiry dropdown → load invoice list
                    $("#edit_vendor_id").val(data.vendor_id).trigger("change");
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


        $("#edit_vendor_id").on("change", function () {
            var vendor_payment_id = $("#edit_vendor_payment_id").val();
            var vendor_id = $(this).val();
            edit_load_vendor_payment_bill_list(vendor_payment_id, vendor_id);
          
        });

        function edit_load_vendor_payment_bill_list(vendor_payment_id, vendor_id) {

            var $container = $("#customer_invoice_receipt_list_table #edit_item_container");
            $container.html("");

            if (!vendor_payment_id) return;

            $.ajax({
                url: "<?php echo site_url('payment/load_vendor_payment_bill_edit_list_ajax'); ?>",
                type: "POST",
                data: {
                    vendor_payment_id: vendor_payment_id,
                    vendor_id: vendor_id 
                },
                dataType: "json",

                success: function (record_list) {

                    var tableId = "#eidt_vendor_payment_list";

                    if ($.fn.DataTable.isDataTable(tableId)) {
                        $(tableId).DataTable().destroy();
                    }

                    var $container = $("#edit_item_container");
                    $container.html("");

                    if (!record_list || record_list.length === 0) {
                        $container.html('<tr><td colspan="6" class="text-center">No invoices found</td></tr>');
                    } else {

                        $.each(record_list, function (i, row) {

                            var vendor_payment_bill_id = row.vendor_payment_bill_id;

                            var total_amount = parseFloat(row.total_amount) || 0;
                            var balance_amount = parseFloat(row.balance_amount) || 0;

                        
                            var current_paid = parseFloat(row.current_paid_amount) || 0;

                            var checkbox = '';

                         
                            if (vendor_payment_bill_id && vendor_payment_bill_id != 0) {

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
                            if (vendor_payment_bill_id && vendor_payment_bill_id != 0) {

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

                                "<td>" + row.bill_date +
                                '<input type="hidden" name="bill_id[' + i + ']" value="' + row.bill_id + '">' +
                                '<input type="hidden" name="bill_type[' + i + ']" value="' + row.bill_type + '">' +
                                '<input type="hidden" name="vendor_payment_bill_id[' + i + ']" value="' + (vendor_payment_bill_id ? vendor_payment_bill_id : '') + '">' +
                                "</td>" +

                                "<td>" + row.bill_no + "</td>" +
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

                    calculateGrandTotal("edit");
                }
            });
        }

    });
</script>