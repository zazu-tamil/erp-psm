<?php include_once(VIEWPATH . 'inc/header.php');


// echo'<pre>';
// print_r($header);
// echo'</pre>';
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
            <h3 class="box-title"><i class="fa fa-pencil"></i> Edit Tender Quotation</h3>
            <div class="pull-right"><a href="<?php echo site_url('tender-quotation-list'); ?>" class="btn btn-default"><i
                        class="fa fa-arrow-left"></i> Back To List</a></div>
        </div>

        <form method="post" action="" id="frmedit" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Edit" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Tender Quotation</legend>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="srch_company_id">Company</label>
                            <?php echo form_dropdown('srch_company_id', ['' => 'Select'] + $company_opt, set_value('srch_company_id', $header['company_id']), 'id="srch_company_id" class="form-control"'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_customer_id">Customer</label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select'] + $customer_opt, set_value('srch_customer_id', $header['customer_id']), 'id="srch_customer_id" class="form-control"'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_tender_enquiry_id">Tender Enquiry No</label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select'] + $tender_enquiry_opt, set_value('srch_tender_enquiry_id', $header['tender_enquiry_id']), 'id="srch_tender_enquiry_id" class="form-control" disabled'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Quotation No</label>
                            <input type="text" name="quotation_no" id="quotation_no" class="form-control"
                                value="<?php echo htmlspecialchars($header['quotation_no']); ?>"
                                placeholder="e.g., TEN-2025-001">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Tender Ref No</label>
                            <input type="text" name="tender_ref_no" id="tender_ref_no" class="form-control"
                                value="<?php echo htmlspecialchars($header['tender_ref_no']); ?>"
                                placeholder="e.g., TEN-2025-001">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Quotation Date</label>
                            <input type="date" name="quote_date" id="quote_date" class="form-control"
                                value="<?php echo htmlspecialchars($header['quote_date']); ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Quotation Status</label><br>

                            <label class="radio-inline">
                                <input type="radio" name="quotation_status" value="Open"
                                    <?php echo ($header['quotation_status'] == 'Open') ? 'checked' : ''; ?>>
                                Open
                            </label>

                            <label class="radio-inline">
                                <input type="radio" name="quotation_status" value="Quoted"
                                    <?php echo ($header['quotation_status'] == 'Quoted') ? 'checked' : ''; ?>>
                                Quoted
                            </label>

                            <label class="radio-inline">
                                <input type="radio" name="quotation_status" value="Won"
                                    <?php echo ($header['quotation_status'] == 'Won') ? 'checked' : ''; ?>>
                                Won
                            </label>

                            <label class="radio-inline">
                                <input type="radio" name="quotation_status" value="On Hold"
                                    <?php echo ($header['quotation_status'] == 'On Hold') ? 'checked' : ''; ?>>
                                On Hold
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
                        <div class="form-group col-md-3">
                            <label for="transport_charges">Transport Charges</label>
                           <input type="number" step="0.01" name="transport_charges" id="transport_charges"
                                class="form-control"
                                placeholder="Enter transport charges"
                                value="<?php echo $header['transport_charges']; ?>">

                        </div>
                        <div class="form-group col-md-3">
                            <label for="other_charges">Other Charges</label>
                            <input type="number" step="0.01" name="other_charges" id="other_charges"
                                class="form-control" placeholder="Enter other charges"
                                value="<?php echo number_format($header['other_charges'], 2); ?>">
                                
                        </div>
                        <div class="form-group col-md-3">
                            <label for="currency_id">Currency Symbol <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('currency_id', ['' => 'Select Currency'] + $currency_opt, set_value('currency_id' , $header['currency_id']), 'id="currency_id" class="form-control" required '); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="remarks">Note</label>
                            <textarea name="remarks" class="form-control" id="editor2_edit_modal" rows="7"
                                placeholder="Enter your remarks"><?php echo htmlspecialchars($header['remarks']); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Terms</label>
                                <textarea id="editor1_edit_modal" name="terms" class="form-control custom-textarea"
                                    placeholder="Enter quotation terms"><?php echo htmlspecialchars($header['terms']); ?></textarea>

                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="mt-4">
                    <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>
                    <div class="row">
                        <div class="col-md-4 form-group">
                                <label for="btnExport">Click Here - Export as Excel File</label> <br>
                                <button id="btnExport" type="button" class="btn btn-success" value="<?php echo htmlspecialchars($header['quotation_no']); ?>">Export Excel & Download</button>
                        </div>
                        <div class="col-md-4 form-group ">
                            <label for="excelFile">Choose Excel File to Import</label>
                            <input type="file" class="form-control" id="excelFile" accept=".xls,.xlsx"
                                placeholder="Choose Excel File to Import">
                        </div> 
                        <div class="col-md-4 form-group ">
                        <i class="text-red">Note: <br>Don't change <b class="text-info">[ tender_quotation_item_id , tender_enquiry_item_id ] </b> column its software referance Ids in excel file Whlie importing</i>
                        </div>     
                    </div>

                    <div id="item_container">
                        <?php if (!empty($items)): ?>
                        <?php foreach ($items as $i => $row): ?>
                        <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
                            <h5 class="text-primary mb-3">Item Details <?php echo $i + 1; ?></h5>
                            <div class="row">
                                <div class="col-md-1 d-flex align-items-center justify-content-center">
                                    <input type="checkbox" class="form-check-input item-check" name="selected_items[]"
                                        value="<?php echo $i; ?>" checked>
                                         <input type="hidden" name="tender_quotation_item_id[]" class="tender_quotation_item_id" value="<?php echo htmlspecialchars($row['tender_quotation_item_id']); ?>">
                                         <input type="hidden" name="tender_enquiry_item_id[]" class="tender_enquiry_item_id" value="<?php echo htmlspecialchars($row['tender_enquiry_item_id']); ?>">
                                        
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Item Code</label>
                                        <input type="text" class="form-control" name="item_code[]" value="<?php echo htmlspecialchars($row['item_code']); ?>" readonly>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Item Description</label>
                                        <textarea name="item_desc[]" class="form-control desc-textarea"
                                            rows="3"><?php echo htmlspecialchars($row['item_desc']); ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>UOM</label>
                                                <input type="text" name="uom[]" class="form-control"
                                                    value="<?php echo htmlspecialchars($row['uom']); ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Quantity</label>
                                                <input type="number" step="0.01" name="qty[]"
                                                    class="form-control qty-input"
                                                    value="<?php echo htmlspecialchars($row['qty']); ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Rate</label>
                                                <input type="number" step="0.01" name="rate[]"
                                                    class="form-control rate-input"
                                                    value="<?php echo htmlspecialchars($row['rate']); ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>VAT %</label>
                                               <input type="text" name="gst[]" class="form-control vat-dropdown" value="<?php echo htmlspecialchars($row['vat']); ?>" >
                                                 
                                            </div>
                                        </div>


                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Amount</label>
                                                <input type="number" step="0.01" name="amount[]"
                                                    class="form-control amount-input"
                                                    value="<?php echo htmlspecialchars($row['amount']); ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                           
                             
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <div class="alert alert-warning">No items found for this quotation.</div>
                        <?php endif; ?>
                    </div>


                    <!-- Total Display Box -->
                    <div class="total-section mt-5 mb-4">
                        <div class="total-card shadow-lg">
                            <div class="total-content d-flex align-items-center justify-content-between">
                                <div class="total-icon">
                                    <i class="fa fa-calculator text-success"></i>
                                </div>
                                <div class="total-text text-end">
                                    <span class="label">Total Amount:</span>
                                    <span class="value">BHD <span id="total_amount">0.00</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-6"><a href="<?php echo site_url('tender-quotation-list'); ?>" class="btn btn-default"><i
                        class="fa fa-arrow-left"></i> Back To List</a></div>
                    <div class="col-md-6 text-right"><button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update</button></div>
                </div> 
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>