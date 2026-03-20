
<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<!-- jQuery UI JS -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

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

                // ✅ Case 1: Has checkbox → include only if checked
                if ($chk.length > 0) {
                    if ($chk.is(":checked")) {
                        grand_total += value;
                    }
                }
                // ✅ Case 2: No checkbox → Fully Paid row → ALWAYS include
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

        $(".add_srch_enq_id").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
                    type: "POST",
                    data: { search: request.term },
                    dataType: "json",
                    success: function (data) {
                        response(data);
                    },
                    error: function () {
                        response([]);
                    },
                });
            },
            minLength: 1,
            select: function (event, ui) {
                $(this).val(ui.item.label);
                $("#add_customer_id").val(ui.item.customer_id);
                add_load_tender_enq(ui.item.tender_enquiry_id);
                add_load_invoice_list(ui.item.customer_id, ui.item.tender_enquiry_id);
                return false;
            },
        });

        $("#add_customer_id").on("change", function () {
            add_load_tender_enq("");
            add_load_invoice_list($(this).val());
        });

        $("#add_tender_enquiry_id").on("change", function () {
            add_load_invoice_list($("#add_customer_id").val(), $(this).val());
        });

        function add_load_tender_enq(t_enq_id) {
            var customer_id = $("#add_customer_id").val();
            var $dd = $("#add_tender_enquiry_id");

            $dd
                .html('<option value="">Select Enquiry</option>')
                .prop("disabled", true);
            $("#add_item_container").html("");
            if (!customer_id) return;

            $.ajax({
                url: "<?php echo site_url('vendor/get_vendor_rate_enquiries_by_customer'); ?>",
                type: "POST",
                data: { customer_id: customer_id },
                dataType: "json",
                success: function (res) {
                    if (res && res.length > 0) {
                        $dd.prop("disabled", false);
                        $.each(res, function (i, row) {
                            $dd.append(
                                $("<option>", {
                                    value: row.tender_enquiry_id,
                                    text: row.display,
                                }),
                            );
                        });
                        if (t_enq_id) $dd.val(t_enq_id).trigger("change");
                    } else {
                        $dd.html('<option value="">No enquiries found</option>');
                    }
                },
                error: function () {
                    alert("Error loading enquiries.");
                },
            });
        }
        function add_load_invoice_list(customer_id, tender_enquiry_id) {

            var $container = $("#add_item_container");
            $container.html("");
            if (!customer_id) return;

            $.ajax({
                url: "<?php echo site_url('payment/get_customer_invoice_list_ajax'); ?>",
                type: "POST",
                data: { customer_id: customer_id, tender_enquiry_id: tender_enquiry_id },
                dataType: "json",
                success: function (record_list) {

                    console.log(record_list);

                    $container.html("");

                    if (!record_list || record_list.length === 0) {
                        $container.html(
                            '<tr><td colspan="5" class="text-center text-muted">No invoices found.</td></tr>'
                        );
                        calculateGrandTotal("add");
                        return;
                    }

                    $.each(record_list, function (i, row) {

                        var amount = parseFloat(row.total_amount) || 0;
                        var balance_amount = parseFloat(row.balance_amount) || 0;

                        var checkbox = "";
                        var input_box = "";
                        var balance_label = "";

                        // ✅ Fully Paid
                        if (balance_amount === 0) {

                            checkbox = '<span class="label label-success">Paid</span>';

                            balance_label = '<label class="label label-success">Paid</label>';

                            input_box = '<input type="text" value="0.000"' +
                                ' class="form-control invoice-amount-input text-right" readonly>';

                        } else {

                            checkbox = '<input type="checkbox" class="select-item"' +
                                ' name="selected_items[]"' +
                                ' data-amount="' + balance_amount + '"' +
                                ' value="' + i + '">';

                            balance_label = '<label class="label label-danger">Bal: ' +
                                format3(balance_amount) +
                                '</label>';

                            // ✅ FIXED HERE
                            input_box = '<input type="text" name="inv_amount[' + i + ']"  ' +
                                ' class="form-control invoice-amount-input text-right">';
                        }

                        $container.append(
                            "<tr>" +

                            '<td class="text-center">' + checkbox + "</td>" +

                            "<td>" + row.invoice_date +
                            '<input type="hidden" name="tender_enq_invoice_id[' + i + ']" value="' +
                            row.tender_enq_invoice_id + '">' +
                            "</td>" +

                            "<td>" + row.invoice_no + "</td>" +

                            '<td class="text-right">' +
                            format3(amount) +
                            "<br>" + balance_label +
                            "</td>" +

                            "<td>" + input_box + "</td>" +

                            "</tr>"
                        );
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

                    // Customer → load enquiry dropdown → load invoice list
                    $("#edit_customer_id").val(data.customer_id);
                    edit_load_tender_enq(data.tender_enquiry_id);
                    edit_load_invoice_list(data.tender_receipt_id, data.customer_id, data.tender_enquiry_id);
                },
                error: function () {
                    alert("Error loading record.");
                },
            });
        });

        $(".edit_srch_enq_id").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
                    type: "POST",
                    data: { search: request.term },
                    dataType: "json",
                    success: function (data) {
                        response(data);
                    },
                    error: function () {
                        response([]);
                    },
                });
            },
            minLength: 1,
            select: function (event, ui) {
                $(this).val(ui.item.label);
                $("#edit_customer_id").val(ui.item.customer_id);
                edit_load_tender_enq(ui.item.tender_enquiry_id);
                edit_load_invoice_list(
                    $("#edit_tender_receipt_id").val(),
                    ui.item.customer_id, ui.item.tender_enquiry_id
                );
                return false;
            },
        });

        $("#edit_customer_id").on("change", function () {
            edit_load_tender_enq("");
            var tender_receipt_id = $("#edit_tender_receipt_id").val();
            var tender_enquiry_id = $("#edit_tender_enquiry_id").val();
            if ($(this).val()) {
                edit_load_invoice_list(tender_receipt_id, $(this).val(), tender_enquiry_id);
            } else {
                $("#edit_item_container").html("");
                calculateGrandTotal("edit");
            }
        });

        $("#edit_tender_enquiry_id").on("change", function () {
            var tender_receipt_id = $("#edit_tender_receipt_id").val();
            edit_load_invoice_list(tender_receipt_id, $("#edit_customer_id").val(), $(this).val());

            if ($(this).val()) {
                edit_load_invoice_list(tender_receipt_id, $("#edit_customer_id").val(), $(this).val());
            } else {
                $("#edit_item_container").html("");
                calculateGrandTotal("edit");
            }

        })

        function edit_load_tender_enq(t_enq_id) {
            var customer_id = $("#edit_customer_id").val();
            var $dd = $("#edit_tender_enquiry_id");

            $dd
                .html('<option value="">Select Enquiry</option>')
                .prop("disabled", true);
            if (!customer_id) return;

            $.ajax({
                url: "<?php echo site_url('vendor/get_vendor_rate_enquiries_by_customer'); ?>",
                type: "POST",
                data: { customer_id: customer_id },
                dataType: "json",
                success: function (res) {
                    if (res && res.length > 0) {
                        $dd.prop("disabled", false);
                        $.each(res, function (i, row) {
                            $dd.append(
                                $("<option>", {
                                    value: row.tender_enquiry_id,
                                    text: row.display,
                                }),
                            );
                        });
                        if (t_enq_id) $dd.val(t_enq_id);
                    } else {
                        $dd.html('<option value="">No enquiries found</option>');
                    }
                },
                error: function () {
                    alert("Error loading enquiries.");
                },
            });
        }
        function edit_load_invoice_list(tender_receipt_id, customer_id, tender_enquiry_id) {

            var $container = $("#edit_item_container");
            $container.html("");

            if (!customer_id) return;

            $.ajax({
                url: "<?php echo site_url('payment/load_receipt_customer_invoice_data'); ?>",
                type: "POST",
                data: {
                    tender_receipt_id: tender_receipt_id,
                    customer_id: customer_id,
                    tender_enquiry_id: tender_enquiry_id
                },
                dataType: "json",

                success: function (record_list) {

                    $container.html("");

                    if (!record_list || record_list.length === 0) {
                        $container.html('<tr><td colspan="5" class="text-center">No invoices found</td></tr>');
                        return;
                    }

                    $.each(record_list, function (i, row) {

                        var total_amount = parseFloat(row.total_amount) || 0;
                        var paid_amount = parseFloat(row.paid_amount) || 0;
                        var balance_amount = parseFloat(row.balance_amount) || 0;

                        // ✅ IMPORTANT FIX
                        var current_paid = parseFloat(row.current_paid_amount) || 0;

                        var inv_amount = current_paid; // ✅ use current receipt only

                        var receipt_inv_id = row.tender_receipt_invoice_id;

                        var isPaid = row.payment_status === "Paid";

                        // ✅ checkbox based on CURRENT receipt
                        var isChecked = current_paid > 0 ? "checked" : "";

                        var checkbox = "";
                        var input_box = "";
                        var balance_label = "";

                        // ✅ FULLY PAID (ALL RECEIPTS)
                        if (isPaid) {

                            checkbox =
                                '<input type="checkbox" checked disabled>' +
                                '<input type="hidden" name="selected_items[]" value="' + i + '">';

                            balance_label = '<label class="label label-success">Paid</label>';

                            input_box =
                                '<input type="text" name="inv_amount[' + i + ']" ' +
                                'value="' + format3(inv_amount) + '" ' +
                                'class="form-control invoice-amount-input text-right" >';

                        } else {

                            checkbox =
                                '<input type="checkbox" class="select-item" ' +
                                'name="selected_items[]" ' +
                                'data-amount="' + balance_amount + '" ' +
                                'value="' + i + '" ' + isChecked + '>';

                            balance_label =
                                '<label class="label label-danger">Bal: ' + format3(balance_amount) + '</label>';

                            var readonly = isChecked ? "" : "readonly";

                            input_box =
                                '<input type="text" name="inv_amount[' + i + ']" ' +
                                'value="' + format3(inv_amount) + '" ' +
                                'class="form-control invoice-amount-input text-right" ' + readonly + '>';
                        }

                        $container.append(
                            "<tr>" +

                            '<td class="text-center">' + checkbox + '</td>' +

                            "<td>" + row.invoice_date +

                            '<input type="hidden" name="tender_enq_invoice_id[' + i + ']" value="' + row.tender_enq_invoice_id + '">' +
                            '<input type="hidden" name="tender_receipt_invoice_id[' + i + ']" value="' + receipt_inv_id + '">' +

                            "</td>" +

                            "<td>" + row.invoice_no + "</td>" +

                            '<td class="text-right">' +
                            format3(total_amount) +
                            "<br>" + balance_label +
                            "</td>" +

                            "<td>" + input_box + "</td>" +

                            "</tr>"
                        );
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
    });
</script>