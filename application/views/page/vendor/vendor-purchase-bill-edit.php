<?php include_once(VIEWPATH . 'inc/header.php'); ?>

<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i>Vendor</a></li>
        <li class="active"> <?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-edit"></i> Edit Vendor Purchase Bill Entry
            </h3>

            <a href="<?php echo site_url('vendor-purchase-bill-list'); ?>" class="btn btn-warning pull-right">
                <i class="fa fa-arrow-left"></i> Back To List
            </a>
        </div>

        <form method="post" action="" id="frmedit" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Edit" />
                <input type="hidden" name="vendor_purchase_invoice_id"
                    value="<?php echo $header['vendor_purchase_invoice_id']; ?>" />

                <fieldset class="tender-inward">
                    <legend class="text-light-blue">
                        <i class="fa fa-file-text-o"></i> Purchase Bill Details
                    </legend>

                    <div
                        style="border:1px solid #ddd; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:5px;">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="srch_enq_id">Search Enquiry No</label>
                                <input type="text" name="srch_enq_id" class="form-control srch_enq_id" value=""
                                    placeholder="Search Enquiry No" readonly />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Company <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_company_id', ['' => 'Select Company'] + $company_opt, $header['company_id'], 'id="srch_company_id" class="form-control" disabled required'); ?>

                            <input type="hidden" name="srch_company_id" value="<?php echo $header['company_id']; ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Customer <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select Customer'] + $customer_opt, $header['customer_id'], 'id="srch_customer_id" class="form-control" disabled required'); ?>
                            <input type="hidden" name="srch_customer_id" value="<?php echo $header['customer_id']; ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Tender Enquiry No <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select Enquiry'] +$tender_enquiry_opt , $header['tender_enquiry_id'], 'id="srch_tender_enquiry_id" class="form-control readonly" required readonly'); ?>
                             
                        </div>

                        <div class="form-group col-md-3">
                            <label>Vendor Name <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_vendor_id', ['' => 'Select Vendor'] + $vendor_opt, $header['vendor_id'], 'id="srch_vendor_id" class="form-control" disabled required'); ?>
                            <input type="hidden" name="srch_vendor_id" value="<?php echo $header['vendor_id']; ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Vendor PO No <span style="color:red;">*</span>
                                <span data-toggle="tooltip" title="" class=""
                                    data-original-title="Only when Vendor PO Status is Confirmed then select the PO No. Otherwise leave it blank.">
                                    <i class="text-sm text-info fa fa-info-circle"></i>
                                </span>
                            </label>
                            <?php echo form_dropdown('srch_vendor_po_id',$vendor_po_opt, $header['vendor_po_id'], 'id="srch_vendor_po_id" class="form-control readonly" disabled required'); ?>
                            <input type="hidden" name="srch_vendor_po_id" id="srch_vendor_po_id"
                                value="<?php echo  $header['vendor_po_id']; ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Contact Person</label>
                            <?php echo form_dropdown('srch_vendor_contact_person_id', ['' => 'Select Contact'] + $vendor_contact_opt, $header['vendor_contact_person_id'], 'id="srch_vendor_contact_person_id" class="form-control readonly" readonly'); ?>
                            <input type="hidden" name="srch_vendor_contact_person_id"
                                id="srch_vendor_contact_person_id_hidden"
                                value="<?php echo $header['vendor_contact_person_id']; ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Check Door Delivery</label>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"
                                        name="door_delivery"
                                        id="door_delivery"
                                        value="Door Delivery"
                                        <?= (!empty($header['door_delivery']) && $header['door_delivery'] == 'Door Delivery') ? 'checked' : ''; ?>>
                                    Door Delivery
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-2">
                            <label>Invoice Date</label>
                            <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                                value="<?php echo $header['invoice_date']; ?>" required="true">
                        </div>

                        <div class="form-group col-md-2">
                            <label>Invoice No</label>
                            <input type="text" name="invoice_no" id="invoice_no" class="form-control"
                                value="<?php echo htmlspecialchars($header['invoice_no']); ?>"
                                placeholder="Enter Invoice No" required="true">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Entry Date <i class="text-danger text-sm">[VAT Filing Date]</i></label>
                            <input type="date" name="entry_date" id="entry_date" class="form-control"
                                value="<?php echo $header['entry_date']; ?>" required="true">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Upload Purchase Bill Document</label>
                            <input type="file" name="purchase_bill_upload" id="purchase_bill_upload"
                                class="form-control">
                            <?php if (!empty($header['purchase_bill_upload'])): ?>
                            <small class="text-info">
                                Current: <a href="<?php echo base_url($header['purchase_bill_upload']); ?>"
                                    target="_blank">View File</a>
                            </small>
                            <?php endif; ?>
                        </div>

                        <div class="form-group col-md-2">
                            <label>Status</label><br>
                            <label class="radio-inline">
                                <input type="radio" name="status" value="Active"
                                    <?php echo ($header['status'] == 'Active') ? 'checked' : ''; ?>> Active
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="status" value="Inactive"
                                    <?php echo ($header['status'] == 'Inactive') ? 'checked' : ''; ?>> Inactive
                            </label>
                        </div>


                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset
                                style="border:1px solid #081979; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:2px;">
                                <legend class="text-info">For Purchase NBR - VAT Filing </legend>

                                <div class="form-group col-md-6">
                                    <label>VAT Payer Purchase</label>
                                    <?php echo form_dropdown('vat_payer_purchase_grp', $vat_payer_purchase_opt, $header['vat_payer_purchase_grp'], 'id="vat_payer_purchase_grp" class="form-control" required'); ?>
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Declaration Number</label>
                                    <input type="text" name="declaration_no" id="declaration_no" class="form-control"
                                        value="<?php echo htmlspecialchars($header['declaration_no']); ?>">
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Declaration Date</label>
                                    <input type="date" name="declaration_date" id="declaration_date"
                                        class="form-control" value="<?php echo $header['declaration_date']; ?>">
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea id="editor2" name="remarks" class="form-control"
                                    placeholder="Enter remarks"><?php echo $header['remarks']; ?></textarea>
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
                                        <th style="width:40%;">Item Code & Description</th>
                                        <th style="width:10%;">UOM & Qty</th>
                                        <th style="width:10%;">Rate & Conversion</th>
                                        <th style="width:10%;">Amount & Duty %</th>
                                        <th style="width:8%;">VAT %</th>
                                        <th style="width:11%;">In BHD <br>Amt (W/O Tax) & <br> Amt (With Tax)</th>
                                    </tr>
                                </thead>
                                <tbody id="item_container">
                                    <?php if (!empty($all_po_items)): ?>
                                    <?php foreach ($all_po_items as $idx => $po_item): ?>
                                    <?php 
                                                // Check if this PO item is already saved
                                                $is_checked = isset($saved_item_ids[$po_item['vendor_po_item_id']]);
                                                $saved_data = $is_checked ? $saved_item_ids[$po_item['vendor_po_item_id']] : [];
                                                
                                                // Use saved values if available, otherwise PO values
                                                $item_rate = $is_checked ? $saved_data['rate'] : $po_item['rate'];
                                                $item_gst = $is_checked ? $saved_data['gst'] : $po_item['gst'];
                                                $item_qty = $is_checked ? $saved_data['qty'] : $po_item['qty'];
                                                $item_amount = $is_checked ? $saved_data['amount'] : $po_item['amount'];
                                                $item_conversion_rate = $is_checked ? $saved_data['conversion_rate'] : 1;
                                                $item_duty = $is_checked ? $saved_data['duty'] : 0;
                                                $item_act_amt = ($item_qty * $item_rate * $item_conversion_rate); // Base amount without tax
                                                $amountwotx = $item_act_amt + ($item_act_amt * $item_duty / 100); // Amount without tax but with duty   
                                            ?>
                                    <tr class="item-row">
                                        <td>
                                            <input type="checkbox" class="item-check" name="selected_items[]"
                                                value="<?php echo $idx; ?>" <?php echo $is_checked ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                value="<?php echo htmlspecialchars($po_item['item_code']); ?>" readonly>
                                            <input type="hidden" name="vendor_purchase_invoice_item_id[]"
                                                value="<?php echo $is_checked ? $saved_data['vendor_purchase_invoice_item_id'] : ''; ?>">
                                            <input type="hidden" name="vendor_po_item_id[]"
                                                value="<?php echo $po_item['vendor_po_item_id']; ?>">
                                            <input type="hidden" name="category_id[]"
                                                value="<?php echo $po_item['category_id']; ?>">
                                            <input type="hidden" name="item_id[]"
                                                value="<?php echo $po_item['item_id']; ?>">
                                            <input type="hidden" name="item_code[]"
                                                value="<?php echo htmlspecialchars($po_item['item_code']); ?>">
                                            <i class="text-sm">Desc</i>
                                            <textarea class="form-control" rows="2" name="item_desc[]"
                                                readonly><?php echo htmlspecialchars($po_item['item_desc']); ?></textarea>
                                        </td>
                                        <td>
                                            <input type="text" name="uom[]" class="form-control"
                                                value="<?php echo htmlspecialchars($po_item['uom']); ?>" readonly>
                                            <i class="text-sm">Qty</i>
                                            <input type="number" step="any" name="qty[]" class="form-control qty-input"
                                                value="<?php echo $item_qty; ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="any" name="rate[]"
                                                class="form-control rate-input" value="<?php echo $item_rate; ?>">
                                            <i class="text-sm">Conversion Rate</i>
                                            <input type="number" step="any" name="conversion_rate[]"
                                                class="form-control conversion_rate"
                                                value="<?php echo $item_conversion_rate; ?>">
                                        </td>
                                        <td>
                                            <input type="number" step="any" name="act_amt[]"
                                                class="form-control act_amt" value="<?php echo $item_act_amt; ?>"
                                                readonly>
                                            <i class="text-sm">Duty %</i>
                                            <input type="number" step="any" name="duty[]" class="form-control duty"
                                                value="<?php echo $item_duty; ?>">
                                        </td>
                                        <td>
                                            <input type="number" step="any" name="gst[]" class="form-control gst-input"
                                                value="<?php echo $item_gst; ?>">

                                        </td>
                                        <td>
                                            <input type="number" step="any" name="amountwotx[]"
                                                class="form-control amountwotx" value="<?php echo $amountwotx; ?>"
                                                readonly>
                                            <i class="text-sm">With Tax</i>
                                            <input type="number" step="any" name="amount[]"
                                                class="form-control amount-input" value="<?php echo $item_amount; ?>"
                                                readonly>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <!-- <div class="tot_amt text-right">
                                <label>Total Bill Amount</label>
                                <div id="total_amount_display"><?php echo number_format($header['total_amount'], 3); ?>
                                </div>
                                <input type="hidden" name="total_amount" id="total_amount"
                                    value="<?php echo $header['total_amount']; ?>">

                                <label>Total GST</label>
                                <div id="total_gst_amount_display">
                                    <?php echo number_format($header['tax_amount'], 3); ?></div>
                                <input type="hidden" name="tax_amount" id="total_gst_amount"
                                    value="<?php echo $header['tax_amount']; ?>">
                            </div> -->

                           <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fix_theamount_total">Fix The Amount</label>

                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"
                                                name="fix_theamount_total"
                                                id="fix_theamount_total"
                                                value="1"
                                                <?php if($header['fix_theamount_total'] == 1){ echo 'checked'; } ?>>

                                            Fix The Amount
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <!-- Total With Tax -->
                            <div class="col-md-3 pull-right">
                                <div class="form-group total-box shadow-sm">
                                    <label>
                                        <i class="fa fa-calculator text-success"></i>
                                        Total Amount With Tax
                                    </label>
                                    <input type="number"
                                        name="total_amount" step="any"
                                        id="total_amount"
                                        class="form-control text-right font-weight-bold" value="<?php echo $header['total_amount'];?>" readonly>
                                </div>
                            </div>
                             <div class="col-md-3 pull-right">
                                <div class="form-group total-box shadow-sm">
                                    <label>
                                        <i class="fa fa-calculator text-success"></i>
                                        Total VAT Amount
                                    </label>
                                    <input type="number"
                                        name="total_vat_amount" step="any"
                                        id="total_vat_amount"
                                        class="form-control text-right" value="<?php echo $header['tax_amount'];?>" readonly>
                                </div>
                            </div> 
                            <!-- Total Amount WO Tax -->
                            <div class="col-md-3 pull-right">
                                <div class="form-group total-box shadow-sm">
                                    <label>
                                        <i class="fa fa-calculator text-success"></i>
                                        Total Amount WO Tax
                                    </label>
                                    <input type="number" step="any"
                                        name="total_amount_wo_tax"
                                        id="total_amount_wo_tax"
                                        class="form-control text-right" value="<?php echo $header['total_amount_wo_tax'];?>" readonly> 
                                </div>
                            </div>

                            <!-- Total VAT -->
                           
                        </div>
                        </fieldset>
                    </div>
                </div>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('vendor-purchase-bill-list'); ?>" class="btn btn-warning pull-left">
                    <i class="fa fa-arrow-left"></i> Back To List
                </a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<style>
.text-light-blue {
    color: #3c8dbc !important;
}

fieldset {
    border: 1px solid #d2d6de;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

legend {
    font-size: 16px;
    font-weight: bold;
    width: auto;
    padding: 0 12px;
}

.tot_amt {
    background: #f9f9f9;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-top: 10px;
}

.tot_amt label {
    font-weight: bold;
    margin-right: 10px;
}

.total-box {
    background: linear-gradient(135deg, #f6fff9, #e8f9f0);
    border: 2px solid #b5e0c6;
    border-radius: 12px;
    padding: 12px 24px;
    min-width: 280px;
    text-align: right;
    font-size: 1.2rem;
    font-weight: bold;
}

.tot_amt div {
    display: inline-block;
    font-size: 1.2em;
    color: #3c8dbc;
    font-weight: bold;
    margin-right: 20px;
}

.readonly {
    pointer-events: none;
}
</style>

<script>
$(document).ready(function () {

/* =====================================================
   INITIAL TOTAL LOAD
=====================================================*/
setTimeout(function () {
    $(".item-row").each(function () {
        calculateRow($(this));
    });
    calculateTotalAmount();
    toggleFixAmount(); // ✅ AUTO LOAD FIX AMOUNT
}, 500);


/* =====================================================
   AUTOCOMPLETE ENQUIRY
=====================================================*/
$(".srch_enq_id").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
            type: "POST",
            data: { search: request.term },
            dataType: "json",
            success: response
        });
    },
    minLength: 1,
    select: function (event, ui) {
        $("#srch_company_id").val(ui.item.company_id);
        $("#srch_customer_id")
            .val(ui.item.customer_id)
            .trigger("change");

        load_tender_enq(ui.item.tender_enquiry_id);
    }
});


/* =====================================================
   LOAD TENDER ENQUIRY
=====================================================*/
function load_tender_enq(t_enq_id="") {

    const customer_id = $("#srch_customer_id").val();
    const company_id  = $("#srch_company_id").val();
    const $drop = $("#srch_tender_enquiry_id");

    $drop.html('<option value="">Select Enquiry</option>')
         .prop("disabled", true);

    if(!customer_id || !company_id) return;

    $.post(
        "<?php echo site_url('vendor/get_tender_enquiries_by_customer');?>",
        {company_id,customer_id},
        function(res){

            $drop.prop("disabled",false);

            $.each(res,function(i,row){
                $drop.append(
                    `<option value="${row.tender_enquiry_id}">
                        ${row.display}
                     </option>`
                );
            });

            if(t_enq_id){
                $drop.val(t_enq_id).trigger("change");
            }
        },
        "json"
    );
}


/* =====================================================
   ROW INPUT CHANGE
=====================================================*/
$(document).on(
"input change",
".rate-input,.gst-input,.qty-input,.item-check,.conversion_rate,.duty",
function () {

    const $row = $(this).closest(".item-row");

    calculateRow($row);
    calculateTotalAmount();
});


/* =====================================================
   FIX AMOUNT CHECKBOX
=====================================================*/
$(document).on("change","#fix_theamount_total",function(){
    toggleFixAmount();
});


/* =====================================================
   FIX AMOUNT FUNCTION (AUTO + CHANGE)
=====================================================*/
function toggleFixAmount(){

    if($("#fix_theamount_total").is(":checked")){

        $("#total_amount").data("fixed",true);
        $("#total_vat_amount").data("fixed",true);
        $("#total_amount_wo_tax").data("fixed",true);

        $("#total_amount,#total_vat_amount,#total_amount_wo_tax")
            .prop("readonly",false)
            .css("background","#fff");

    }else{

        $("#total_amount").data("fixed",false);
        $("#total_vat_amount").data("fixed",false);
        $("#total_amount_wo_tax").data("fixed",false);

        $("#total_amount,#total_vat_amount,#total_amount_wo_tax")
            .prop("readonly",true)
            .css("background","#eee");

        calculateTotalAmount();
    }
}


/* =====================================================
   ROW CALCULATION
=====================================================*/
function calculateRow($row){

    const qty   = parseFloat($row.find(".qty-input").val())||0;
    const rate  = parseFloat($row.find(".rate-input").val())||0;
    const gst   = parseFloat($row.find(".gst-input").val())||0;
    const cRate = parseFloat($row.find(".conversion_rate").val())||1;
    const duty  = parseFloat($row.find(".duty").val())||0;

    let ac_amt = qty*rate*cRate;

    let amountWithoutTax =
        ac_amt + ((ac_amt*duty)/100);

    let amountWithTax =
        amountWithoutTax +
        (amountWithoutTax*gst/100);

    let gstAmount =
        amountWithTax-amountWithoutTax;

    $row.find(".act_amt")
        .val(ac_amt.toFixed(3));

    $row.find(".amountwotx")
        .val(amountWithoutTax.toFixed(3));

    $row.find(".gst-amount-input")
        .val(gstAmount.toFixed(3));

    $row.find(".amount-input")
        .val(amountWithTax.toFixed(3));
}


/* =====================================================
   TOTAL CALCULATION
=====================================================*/
function calculateTotalAmount(){

    if($("#fix_theamount_total").is(":checked")){
        return;
    }

    let total=0;
    let total_wot=0;

    $(".item-row").each(function(){

        if($(this).find(".item-check").is(":checked")){

            total+=parseFloat(
                $(this).find(".amount-input").val()
            )||0;

            total_wot+=parseFloat(
                $(this).find(".amountwotx").val()
            )||0;
        }
    });

    let totalVat = total-total_wot;

    $("#total_amount").val(total.toFixed(3));
    $("#total_amount_wo_tax").val(total_wot.toFixed(3));
    $("#total_vat_amount").val(totalVat.toFixed(3));

    $(".total_amount_hidden")
        .val(total.toFixed(3));

    $(".total_amount_wo_tax_hidden")
        .val(total_wot.toFixed(3));

    $(".total_vat_amount_hidden")
        .val(totalVat.toFixed(3));
}


/* =====================================================
   FORM VALIDATION
=====================================================*/
$("form").on("submit",function(){

    if($(".item-check:checked").length===0){
        alert("Please select at least one item");
        return false;
    }
});

});
</script>