<?php include_once(VIEWPATH . 'inc/header.php'); ?>

<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Tender</a></li>
        <li class="active">Add <?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-plus-circle"></i> Invoice Generator</h3>
        </div>

        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Add" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Invoice Details</legend>

                    <div
                        style="border:1px solid #ddd; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:5px;">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="srch_enq_id">Search Enquiry No</label>
                                <input type="text" name="srch_enq_id" class="form-control srch_enq_id" value=""
                                    placeholder="Search Enquiry No" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="srch_company_id">Company <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_company_id', $company_opt, set_value('srch_company_id'), 'id="srch_company_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_customer_id">Customer <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('srch_customer_id'), 'id="srch_customer_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_tender_enquiry_id">Tender Enquiry No <span
                                    style="color:red;">*</span></label>
                            <?php echo form_dropdown(
                                'srch_tender_enquiry_id',
                                ['' => 'Select Enquiry'],
                                set_value('srch_tender_enquiry_id'),
                                'id="srch_tender_enquiry_id" class="form-control" required'
                            ); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_tender_po_id">Tender PO No <span style="color:red;">*</span></label>
                            <?php echo form_dropdown(
                                'srch_tender_po_id',
                                ['' => 'Select PO'],
                                set_value('srch_tender_po_id'),
                                'id="srch_tender_po_id" class="form-control" required'
                            ); ?>
                        </div>

                        <div class="form-group col-md-12">
                            <fieldset
                                style="border:1px solid #081979; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:2px;">
                                <legend class="text-info">DC List </legend>
                                <div id="dc_list_container"></div>
                            </fieldset>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Invoice Date</label>
                            <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                                value="<?php echo set_value('invoice_date', date('Y-m-d')); ?>">
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="currency_id">Currency</label>
                                <?php
                                echo form_dropdown(
                                    'currency_id',
                                    ['' => 'Select Currency'] + $currency_opt,
                                    set_value('currency_id', $default_currency_id),
                                    'id="currency_id" class="form-control" required'
                                );
                                ?>
                            </div>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Invoice Status</label><br>
                            <label class="radio-inline"><input type="radio" name="invoice_status" value="Pending"
                                    checked>Pending</label>
                            <label class="radio-inline"><input type="radio" name="invoice_status"
                                    value="Payment Paid">Payment Paid</label>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active" checked>
                                Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Inactive">
                                Inactive</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset
                                style="border:1px solid #081979; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:2px;">
                                <legend class="text-info">For Sales NBR - VAT Filing </legend>

                                <div class="form-group col-md-6">
                                    <label>VAT Payer Sales</label>
                                    <?php echo form_dropdown('vat_payer_sales_grp', $vat_payer_sales_opt, set_value('vat_payer_sales_grp'), 'id="vat_payer_sales_grp" class="form-control"'); ?>
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Declaration Number</label>
                                    <input type="text" name="declaration_no" id="declaration_no" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Declaration Date</label>
                                    <input type="date" name="declaration_date" id="declaration_date"
                                        class="form-control">
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="remarks">Notes</label>
                            <textarea name="remarks" class="form-control" id="editor2" placeholder="Enter your remarks"
                                rows="5"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Terms & Conditions Invoice</label>
                                <textarea id="editor1" name="terms" class="form-control custom-textarea"
                                    placeholder="Enter Invoice terms" required></textarea>
                            </div>
                        </div>
                    </div>

                </fieldset>

                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="mt-4">
                            <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>
                            <table class="table table-bordered table-sm table-responsive">
                                <thead>
                                    <tr>
                                        <th style="width:5%;">✔</th>
                                        <th style="width:10%;">Item Code</th>
                                        <th style="width:40%;">Description</th>
                                        <th style="width:10%;">UOM & Qty</th>
                                        <th style="width:10%;">Rate</th>
                                        <th style="width:10%;">VAT %</th>
                                        <th style="width:10%;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="item_container"></tbody>
                            </table>

                            <div
                                style="border-radius:10px; padding:30px; background-color:#f8f9fa; border:2px solid blue; margin-top:30px;margin-bottom:30px;">
                                <div class="row">
                                    <div class="col-md-3 text-right ">
                                        <h3 class="text-red">Total Excl Addt Charges</h3>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <div class="total-box shadow-sm">
                                            <h5 class="mb-0">
                                                <i class="fa fa-calculator text-success me-2"></i>
                                                <strong>Total Amount WO Tax:</strong>
                                                <span class="text-primary"><span
                                                        id="total_amount_wo_tax">0.000</span></span>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <div class="total-box shadow-sm">
                                            <h5 class="mb-0">
                                                <i class="fa fa-calculator text-success me-2"></i>
                                                <strong>Total TAX Amount :</strong>
                                                <span class="text-primary text-bold"><span
                                                        id="total_tax_amount">0.000</span></span>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-right ">
                                        <div class="total-box shadow-sm">
                                            <h5 class="mb-0">
                                                <i class="fa fa-calculator text-success me-2"></i>
                                                <strong>Total Amount With Tax:</strong>
                                                <span class="text-primary"><span id="total_amount">0.000</span></span>
                                            </h5>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </fieldset>

                        <div id="div_addt_chrg" class="hide">
                            <fieldset class="mt-4 pt-4">
                                <legend class="text-light-blue"><i class="fa fa-list"></i> Addtional Charges (If any)
                                </legend>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>#</th>
                                        <th>Addt.Charges Type</th>
                                        <th>Addt.Charges Amt</th>
                                        <th>VAT %</th>
                                        <th>VAT Amt</th>
                                        <th>Total Amt</th>
                                    </tr>
                                    <tbody id="tb_addt_chrg_list"></tbody>

                                </table>
                            </fieldset>

                            <div
                                style="border-radius:10px; padding:30px; background-color:#f8f9fa; border:2px solid blue; margin-top:30px;margin-bottom:30px;">
                                <div class="row">
                                    <div class="col-md-3 text-right ">
                                        <h3 class="text-red">Total Inc Addt Charges</h3>
                                    </div>

                                    <div class="col-md-3 text-right">
                                        <div class="total-box shadow-sm">
                                            <h5 class="mb-0">
                                                <i class="fa fa-calculator text-success me-2"></i>
                                                <strong>Total Amount WO Tax :</strong>
                                                <span class="text-primary text-bold"><span
                                                        id="total_amount_wo_tax_addt">0.000</span></span>
                                                <br><i>Inc Addt Charges</i>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <div class="total-box shadow-sm">
                                            <h5 class="mb-0">
                                                <i class="fa fa-calculator text-success me-2"></i>
                                                <strong>Total Tax Amount :</strong>
                                                <span class="text-primary text-bold"><span
                                                        id="total_tax_amount_addt">0.000</span></span>
                                                <br><i>Inc Addt Charges</i>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-right ">
                                        <div class="total-box shadow-sm">
                                            <h5 class="mb-0">
                                                <i class="fa fa-calculator text-success me-2"></i>
                                                <strong>Total Amount With Tax :</strong>
                                                <span class="text-primary text-bold"><span
                                                        id="total_amount_addt">0.000</span></span>
                                                <br><i>Inc Addt Charges</i>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <div class="row">
                    <div class="text-left col-md-6">
                        <input type="hidden" name="total_amount" id="total_amount_hidden" value="">
                        <input type="hidden" name="tax_amount" id="tax_amount_hidden" value="">
                        <a href="<?php echo site_url('customer-tender-po-list'); ?>" class="btn btn-default"><i
                                class="fa fa-arrow-left"></i> Back To List</a>
                    </div>
                    <div class="text-right col-md-6">
                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
<script>
$(document).ready(function() {


    // Enable / Disable on checkbox change
    $(document).on("change", ".chk_tender_po_addtchrg_id", function() {

        //alert('CHK Box');

        let row = $(this).closest('tr');

        if ($(this).is(':checked')) {
            row.find(
                    '.addt_charges_amt, .addt_charges_vat, .addt_charges_vat_amt, .addt_charges_tot_amt'
                )
                .prop('disabled', false);
        } else {
            row.find(
                    '.addt_charges_amt, .addt_charges_vat, .addt_charges_vat_amt, .addt_charges_tot_amt'
                )
                .prop('disabled', true);
            // .val('0'); // clear values
        }

        calculateTotalAmount_addt();
    });

    $('#tb_addt_chrg_list .chk_tender_po_addtchrg_id').change();


    // Calculation on change
    $(document).on('input', '.addt_charges_amt, .addt_charges_vat', function() {

        let row = $(this).closest('tr');

        let amt = parseFloat(row.find('.addt_charges_amt').val()) || 0;
        let vat = parseFloat(row.find('.addt_charges_vat').val()) || 0;

        let vat_amt = (amt * vat) / 100;
        let total = amt + vat_amt;

        row.find('.addt_charges_vat_amt').val(vat_amt.toFixed(2));
        row.find('.addt_charges_tot_amt').val(total.toFixed(2));

        calculateTotalAmount_addt();
    });


    function calculateTotalAmount_addt() {
        let total_addt_amt_wo_tax = 0;
        $(".addt_charges_amt").each(function() {
            if ($(this).closest('tr').find('.chk_tender_po_addtchrg_id').is(':checked')) {
                total_addt_amt_wo_tax += parseFloat($(this).val()) || 0;
            }
        });
        //alert("TEST" + $("#total_amount_wo_tax").text());
        total_addt_amt_wo_tax = parseFloat($("#total_amount_wo_tax").text()) + parseFloat(
            total_addt_amt_wo_tax);
        $("#total_amount_wo_tax_addt").text(total_addt_amt_wo_tax.toFixed(3));


        let total_addt_amt_w_tax = 0;
        $(".addt_charges_tot_amt").each(function() {
            if ($(this).closest('tr').find('.chk_tender_po_addtchrg_id').is(':checked')) {
                total_addt_amt_w_tax += parseFloat($(this).val()) || 0;
            }
        });
        total_addt_amt_w_tax = parseFloat($("#total_amount").text()) + parseFloat(total_addt_amt_w_tax);
        $("#total_amount_addt").text(total_addt_amt_w_tax.toFixed(3));


        $("#total_tax_amount_addt").text((total_addt_amt_w_tax - total_addt_amt_wo_tax).toFixed(3));


        $("#total_amount_hidden").val(total_addt_amt_w_tax.toFixed(3)); 
        $("#tax_amount_hidden").val((total_addt_amt_w_tax - total_addt_amt_wo_tax).toFixed(3));

    }

    const $container = $("#item_container");

    /* ===============================
       LOAD DC LIST WHEN PO CHANGES
    =============================== */
    $("#srch_tender_po_id").on("change", function() {

        const tender_po_id = $(this).val();

        $("#dc_list_container").html("");
        $container.html("");

        if (!tender_po_id) return;

        // ---- Load DC list ----
        $.ajax({
            url: "<?php echo site_url('tender/get_tender_po_DC_list'); ?>",
            type: "POST",
            data: {
                tender_po_id: tender_po_id
            },
            dataType: "json",
            success: function(res) {
                console.log(res);

                //alert(res['addt_chrg_list'].length);
                //div_addt_chrg
                if (res['addt_chrg_list'].length > 0) {
                    $("#div_addt_chrg").removeClass("hide");
                    $('#tb_addt_chrg_list').html('');
                    let ctnt_html = ``;
                    $.each(res['addt_chrg_list'], function(i, row) {
                        // let amtwotx = (row.qty * row.rate).toFixed(3);
                        ctnt_html += `
                        <tr> 
                            <td>
                                <input type="checkbox" class="chk_tender_po_addtchrg_id"
                                        id="chk_tender_po_addtchrg_id_${row.tender_po_addtchrg_id}"
                                        name="chk_tender_po_addtchrg_id[]"
                                        value="${row.tender_po_addtchrg_id}" checked> 
                            </td>
                            <td>  
                            <input type="hidden" class="form-control addt_charges_type_id" name="addt_charges_type_id[${row.tender_quote_addtchrg_id}]" value="${row.addt_charges_type_id}">
                            ${row.addt_charges_type_name}
                            </td>
                            <td><input type="number" step="any" class="form-control addt_charges_amt" name="addt_charges_amt[${row.tender_quote_addtchrg_id}]" value="${row.addt_charges_amt}"></td>
                            <td><input type="number" step="any" class="form-control addt_charges_vat" name="addt_charges_vat[${row.tender_quote_addtchrg_id}]" value="${row.addt_charges_vat}">    </td>
                            <td><input type="number" step="any" class="form-control addt_charges_vat_amt" name="addt_charges_vat_amt[${row.tender_quote_addtchrg_id}]" value="${row.addt_charges_vat_amt}" readonly></td>
                            <td><input type="number" step="any" class="form-control addt_charges_tot_amt" name="addt_charges_tot_amt[${row.tender_quote_addtchrg_id}]" value="${row.addt_charges_tot_amt}" readonly></td>
                        </tr>
                        `;

                    })
                    $('#tb_addt_chrg_list').html(ctnt_html);

                } else {
                    $("#div_addt_chrg").addClass("hide");
                }




                let html = "";

                if (res['dc_list'].length > 0) {

                    $.each(res['dc_list'], function(i, row) {
                        html += `
                            <div class="col-md-3">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"
                                            class="dc_list"
                                            name="dc_id[]"
                                            value="${row.tender_dc_id}">
                                            DC No: ${row.dc_no}
                                            [ ${row.dc_date} ]
                                            ( ${row.total_items} )
                                    </label>
                                </div>
                            </div>`;
                    });

                } else {
                    html = `<div class="col-md-12 text-danger">No DC Found</div>`;
                }

                $("#dc_list_container").html(html);
            }
        });

        // ---- Load currency ----
        $.ajax({
            url: "<?php echo site_url('tender/get_tender_po_currency_id'); ?>",
            type: "POST",
            data: {
                tender_po_id: tender_po_id
            },
            dataType: "json",
            success: function(res) {
                if (res.length > 0) {
                    $("#currency_id").val(res[0].currency_id).trigger("change");
                }
            }
        });

    });

    /* ===============================
       LOAD ITEMS WHEN DC SELECTED
    =============================== */
    $(document).on("change", ".dc_list", function() {

        let dc_ids = [];

        $(".dc_list:checked").each(function() {
            dc_ids.push($(this).val());
        });

        loadItemsByDC(dc_ids);
    });

    function loadItemsByDC(dc_ids) {

        $container.html("");

        if (dc_ids.length === 0) {
            $container.html(`
                <tr>
                    <td colspan="7" class="text-danger text-center">
                        Select DC to load items
                    </td>
                </tr>`);
            return;
        }

        $.ajax({
            url: "<?php echo site_url('tender/get_tender_po_invoice_load_items_dc_id'); ?>",
            type: "POST",
            data: {
                dc_id: dc_ids,
                tender_po_id: $("#srch_tender_po_id").val()
            },
            dataType: "json",

            success: function(res) {

                if (!res || res.length === 0) {
                    $container.html(`
                        <tr>
                            <td colspan="7" class="text-danger text-center">
                                No items found
                            </td>
                        </tr>`);
                    return;
                }

                $.each(res, function(i, row) {

                    const html = `
                    <tr class="item-row">

                        <td>
                            <input type="checkbox"
                                class="item-check"
                                name="selected_items[${i}]"    
                                value="${i}"
                                checked>
                        </td>

                        <td>
                            <input type="text"
                                class="form-control"
                                name="item_code[${i}]"
                                value="${row.item_code || ''}"
                                readonly>

                            <input type="hidden"
                                name="tender_po_item_id[${i}]"
                                value="${row.tender_po_item_id || 0}">
                        </td>

                        <td>
                            <textarea class="form-control"
                                name="item_desc[${i}]"
                                rows="2"
                                readonly>${row.item_desc || ''}</textarea>
                        </td>

                        <td>
                            <input type="text"
                                class="form-control"
                                name="uom[${i}]"
                                value="${row.uom || ''}"
                                readonly>

                            <input type="number"
                                step="any"
                                class="form-control qty-input"
                                name="qty[${i}]"
                                value="${row.del_qty || 0}"
                                readonly>
                        </td>

                        <td>
                            <input type="number"
                                step="any"
                                class="form-control rate-input"
                                name="rate[${i}]"
                                value="${row.rate || 0}">
                        </td>

                        <td>
                            <input type="number"
                                step="any"
                                class="form-control gst-input"
                                name="gst[${i}]"
                                value="${row.gst || 0}">

                            <input type="hidden"
                                class="gst-amount-input"
                                name="gst_amount[${i}]">
                        </td>

                        <td>
                            <input type="number"
                                step="any"
                                class="form-control amount-input"
                                name="amount[${i}]"
                                readonly>
                        </td>

                    </tr>`;

                    const $row = $(html);
                    $container.append($row);

                    calculateRow($row);
                });

                calculateTotals();
            }
        });
    }

    /* ===============================
       CALCULATIONS
    =============================== */
    $(document).on("input change",
        ".rate-input,.gst-input,.item-check",
        function() {
            const $row = $(this).closest(".item-row");
            calculateRow($row);
            calculateTotals();
        });

    function calculateRow($row) {

        if (!$row.find(".item-check").is(":checked")) {
            $row.find(".amount-input").val("0.000");
            $row.find(".gst-amount-input").val("0.000");
            return;
        }

        const qty = parseFloat($row.find(".qty-input").val()) || 0;
        const rate = parseFloat($row.find(".rate-input").val()) || 0;
        const gst = parseFloat($row.find(".gst-input").val()) || 0;

        const base = qty * rate;
        const gstAmt = (base * gst) / 100;
        const total = base + gstAmt;

        $row.find(".gst-amount-input").val(gstAmt.toFixed(3));
        $row.find(".amount-input").val(total.toFixed(3));
    }

    function calculateTotals() {

        let total = 0;
        let gstOnly = 0;
        let withoutTax = 0;

        $(".item-row").each(function() {

            if ($(this).find(".item-check").is(":checked")) {

                const amt = parseFloat($(this).find(".amount-input").val()) || 0;
                const gst = parseFloat($(this).find(".gst-amount-input").val()) || 0;

                total += amt;
                gstOnly += gst;
                withoutTax += (amt - gst);
            }
        });

        $("#total_amount").text(total.toFixed(3));
        $("#total_amount_wo_tax").text(withoutTax.toFixed(3));

        $("#total_amount_hidden").val(total.toFixed(3)); 
        $("#tax_amount_hidden").val(gstOnly.toFixed(3));

        $("#total_tax_amount").text((total - withoutTax).toFixed(3));

        calculateTotalAmount_addt();
    }

});
</script>
<script>
$(document).ready(function() {
    var editorConfig = {
        height: 120,
        extraPlugins: "justify,colorbutton,font,table",
    };

    CKEDITOR.replace("editor1", editorConfig);
    CKEDITOR.replace("editor2", editorConfig);

    CKEDITOR.instances.editor1.on("instanceReady", function() {
        let companyId = $("#srch_company_id").val();

        if (companyId) {
            load_terms_and_conditions(companyId);
        }
    });
});

function load_terms_and_conditions(companyId) {
    $.ajax({
        url: "<?php echo site_url('get-data'); ?>",
        type: "POST",
        dataType: "json",
        data: {
            tbl: "get-company-terms-and-conditions-list",
            id: companyId,
        },
        success: function(res) {
            console.log("Response:", res);

            if (res && res.length > 0) {
                CKEDITOR.instances.editor1.setData(res[0].invoice_terms || "");
            } else {
                CKEDITOR.instances.editor1.setData("");
            }
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        },
    });
}
</script>