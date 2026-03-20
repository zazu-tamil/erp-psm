<?php include_once(VIEWPATH . 'inc/header.php'); 
// echo '<pre>';
// print_r($header);
// print_r($merged_items);
// echo '</pre>';
?>

<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Tender</a></li>
        <li class="active">Edit <?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-pencil"></i> Edit Tender PO</h3>
        </div>

        <form method="post" action="" id="frmedit" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Edit" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Purchase Order Details</legend>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="srch_company_id">Company</label>
                            <?php echo form_dropdown('srch_company_id',   $company_opt, set_value('srch_company_id', $header['company_id']), 'id="srch_company_id" class="form-control readonly-select"'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_customer_id">Customer</label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select'] + $customer_opt, set_value('srch_customer_id', $header['customer_id']), 'id="srch_customer_id" class="form-control readonly-select"'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_tender_enquiry_id">Tender Enquiry No</label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select'] + $tender_enquiry_opt, set_value('srch_tender_enquiry_id', $header['tender_enquiry_id']), 'id="srch_tender_enquiry_id" class="form-control readonly-select" '); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="tender_quotation_id">Quotation No</label>
                            <?php echo form_dropdown('tender_quotation_id', ['' => 'Select Quotation'] + $quotation_opt, set_value('tender_quotation_id', $header['tender_quotation_id']), 'id="srch_quotation_no" class="form-control readonly-select" '); ?>
                            <input type="hidden" name="tender_quotation_id" id="tender_quotation_id"
                                value="<?php echo htmlspecialchars($header['tender_quotation_id']); ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Customer PO No</label>
                            <input type="text" name="customer_po_no" id="customer_po_no" class="form-control"
                                value="<?php echo htmlspecialchars($header['customer_po_no']); ?>"
                                placeholder="e.g., CUST-PO-2025-001">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Our PO No</label>
                            <input type="text" name="our_po_no" id="our_po_no" class="form-control"
                                value="<?php echo htmlspecialchars($header['our_po_no']); ?>"
                                placeholder="e.g., CUST-PO-2025-001">
                        </div>

                        <div class="form-group col-md-3">
                            <label>PO Date</label>
                            <input type="date" name="po_date" id="po_date" class="form-control"
                                value="<?php echo htmlspecialchars($header['po_date']); ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>PO Received Date</label>
                            <input type="date" name="po_received_date" id="po_received_date" class="form-control"
                                value="<?php echo htmlspecialchars($header['po_received_date']); ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Delivery Date</label>
                            <input type="date" name="delivery_date" id="delivery_date" class="form-control"
                                value="<?php echo htmlspecialchars($header['delivery_date']); ?>">
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="currency_id">Currency</label>
                                <?php 
                                    echo form_dropdown(
                                        'currency_id',
                                        ['' => ' Currency'] + $currency_opt, 
                                        set_value('currency_id', $header['currency_id']),
                                        'id="currency_id" class="form-control" required'
                                    );
                                ?>
                            </div>
                        </div>

                        <div class="form-group col-md-3">
                            <label>PO Status</label><br>
                            <label class="radio-inline">
                                <input type="radio" name="po_status" value="Open"
                                    <?php echo ($header['po_status'] == 'Open') ? 'checked' : ''; ?>>
                                Open
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="po_status" value="In Progress"
                                    <?php echo ($header['po_status'] == 'In Progress') ? 'checked' : ''; ?>>
                                In Progress
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="po_status" value="Completed"
                                    <?php echo ($header['po_status'] == 'Completed') ? 'checked' : ''; ?>>
                                Completed
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="po_status" value="Cancelled"
                                    <?php echo ($header['po_status'] == 'Cancelled') ? 'checked' : ''; ?>>
                                Cancelled
                            </label>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Status</label><br>
                            <label class="radio-inline">
                                <input type="radio" name="status" value="Active"
                                    <?php echo ($header['status'] == 'Active') ? 'checked' : ''; ?>>
                                Active
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="status" value="Inactive"
                                    <?php echo ($header['status'] == 'Inactive') ? 'checked' : ''; ?>>
                                Inactive
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="remarks">Notes</label>
                            <textarea name="remarks" class="form-control" id="editor2" placeholder="Enter your remarks"
                                rows="5"><?php echo htmlspecialchars($header['remarks']); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Terms & Conditions</label>
                                <textarea id="editor1" name="terms" class="form-control custom-textarea"
                                    placeholder="Enter PO terms"
                                    required><?php echo htmlspecialchars($header['terms']); ?></textarea>
                            </div>
                        </div>
                    </div>

                </fieldset>

                <fieldset class="mt-4">
                    <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="btnExport">Click Here - Export as Excel File</label> <br>
                            <button id="btnExport" type="button" class="btn btn-success"
                                value="Tender-PO-<?php echo htmlspecialchars($header['customer_po_no']); ?>">Export
                                Excel & Download</button>
                        </div>
                        <div class="col-md-4 form-group ">
                            <label for="excelFile">Choose Excel File to Import</label>
                            <input type="file" class="form-control" id="excelFile" accept=".xls,.xlsx"
                                placeholder="Choose Excel File to Import">
                        </div>
                        <div class="col-md-4 form-group ">
                            <i class="text-red">Note: <br>Don't change <b class="text-info">[ tender_po_item_id ,
                                    tender_quotation_item_id ] </b> column its software referance Ids in excel file
                                Whlie importing</i>
                        </div>
                    </div>

                    <div id="item_container">
                        <?php if (!empty($merged_items)): ?>
                        <?php foreach ($merged_items as $i => $row): ?>
                        <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">

                            <h5 class="text-primary mb-3">
                                Item <?php echo $i + 1; ?>

                            </h5>

                            <div class="row">

                                <div class="col-md-1 d-flex align-items-center justify-content-center">
                                    <input type="checkbox" class="form-check-input item-check" name="selected_items[]"
                                        value="<?php echo $i; ?>" <?php if (!empty($row['tender_po_item_id'])): ?>
                                        checked <?php endif; ?>>

                                    <input type="hidden" name="tender_quotation_item_id[]"
                                        value="<?php echo $row['tender_quotation_item_id']; ?>">
                                    <input type="hidden" name="tender_po_item_id[]>"
                                        value="<?php echo $row['tender_po_item_id']; ?>">

                                </div>

                                <!-- Category + Item -->
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Item Code</label>
                                        <input type="text" class="form-control item_code" name="item_code[]"
                                            value="<?php echo htmlspecialchars($row['item_code']); ?>" readonly>
                                    </div>
                                </div>

                                <!-- Item Description -->
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Item Description</label>
                                        <textarea name="item_desc[]" class="form-control item_desc"
                                            rows="3"><?php echo htmlspecialchars($row['item_desc']); ?></textarea>
                                    </div>
                                </div>

                                <!-- UOM / Qty / Rate / VAT / Amount -->
                                <div class="col-md-4">
                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>UOM</label>
                                                <input type="text" name="uom[]" class="form-control uom"
                                                    value="<?php echo htmlspecialchars($row['uom']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Qty</label>
                                                <input type="number" step="any" name="qty[]"
                                                    class="form-control qty-input"
                                                    value="<?php echo htmlspecialchars($row['qty']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Rate</label>
                                                <input type="number" step="any" name="rate[]"
                                                    class="form-control rate-input" value="<?php echo $row['rate']; ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>VAT %</label>
                                                <input type="number" step="any" name="gst[]"
                                                    class="form-control vat-dropdown"
                                                    value="<?php echo $row['vat']; ?>">

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Amount WO Tax</label>
                                                <input type="number" step="any" name="amount_wo_tax[]"
                                                    class="form-control amountwotx"
                                                    value="<?php echo number_format(($row['rate'] * $row['qty']), 3, '.', ''); ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Amount</label>
                                                <input type="number" step="any" name="amount[]"
                                                    class="form-control amount-input"
                                                    value="<?php echo number_format($row['amount'], 3, '.', ''); ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                    </div> <!-- row -->
                                </div> <!-- col-md-4 -->

                            </div> <!-- row -->


                        </div> <!-- item-card -->
                        <?php endforeach; ?>

                        <?php else: ?>
                        <div class="alert alert-warning">No items in the selected quotation.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Total Section -->

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
                                        <span class="text-primary"><span id="total_amount_wo_tax">0.000</span></span>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-md-3 text-right">
                                <div class="total-box shadow-sm">
                                    <h5 class="mb-0">
                                        <i class="fa fa-calculator text-success me-2"></i>
                                        <strong>Total TAX Amount :</strong>
                                        <span class="text-primary text-bold"><span id="total_tax_amount">0.000</span></span>
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

                <div id="div_addt_chrg">
                    <fieldset class="mt-4 pt-4">
                        <legend class="text-light-blue"><i class="fa fa-list"></i> Addtional Charges (If any)</legend>
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>#</th>
                                <th>Addt.Charges Type</th>
                                <th>Addt.Charges Amt</th>
                                <th>VAT %</th>
                                <th>VAT Amt</th>
                                <th>Total Amt</th>
                            </tr>
                            <tbody id="tb_addt_chrg_list">
                                <?php foreach($addt_charges_list as $key => $row): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="chk_tender_quote_addtchrg_id"
                                            id="chk_tender_quote_addtchrg_id_<?php echo $row['tender_quote_addtchrg_id']; ?>"
                                            name="chk_tender_quote_addtchrg_id[]"
                                            value="<?php echo $row['tender_quote_addtchrg_id']; ?>"
                                            <?php echo ($row['tender_po_addtchrg_id'] != '') ? 'checked' : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="hidden" class="form-control addt_charges_type_id"
                                            name="addt_charges_type_id[<?php echo $row['tender_quote_addtchrg_id']; ?>]"
                                            value="<?php echo $row['addt_charges_type_id']; ?>">
                                        <input type="hidden" class="form-control tender_po_addtchrg_id"
                                            name="tender_po_addtchrg_id[<?php echo $row['tender_quote_addtchrg_id']; ?>]"
                                            value="<?php echo $row['tender_po_addtchrg_id']; ?>">
                                        <label for="chk_tender_quote_addtchrg_id_<?php echo $row['tender_quote_addtchrg_id']; ?>"><?php echo $row['addt_charges_type_name']; ?></label>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control addt_charges_amt"
                                            name="addt_charges_amt[<?php echo $row['tender_quote_addtchrg_id']; ?>]"
                                            value="<?php echo $row['addt_charges_amt']; ?>">
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control addt_charges_vat"
                                            name="addt_charges_vat[<?php echo $row['tender_quote_addtchrg_id']; ?>]"
                                            value="<?php echo $row['addt_charges_vat']; ?>">
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control addt_charges_vat_amt"
                                            name="addt_charges_vat_amt[<?php echo $row['tender_quote_addtchrg_id']; ?>]"
                                            value="<?php echo $row['addt_charges_vat_amt']; ?>" readonly>

                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control addt_charges_tot_amt"
                                            name="addt_charges_tot_amt[<?php echo $row['tender_quote_addtchrg_id']; ?>]"
                                            value="<?php echo $row['addt_charges_tot_amt']; ?>" readonly>
                                    </td>
                                </tr>
                                <?php endforeach; ?>

                            </tbody>

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

            <div class="box-footer">
                <div class="pull-left">
                    <a href="<?php echo site_url('customer-tender-po-list'); ?>" class="btn btn-default"><i
                            class="fa fa-arrow-left"></i> Back To List</a>
                </div>
                <div class="pull-right">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update</button>
                </div>
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>