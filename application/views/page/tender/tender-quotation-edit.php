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
                        <div class="form-group col-md-6">
                            <label for="remarks">Remarks</label>
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
                    <div id="item_container">
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $i => $row): ?>
                                <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
                                    <h5 class="text-primary mb-3">Item Details <?php echo $i + 1; ?></h5>
                                    <div class="row">
                                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                                            <input type="checkbox" class="form-check-input item-check" name="selected_items[]"
                                                value="<?php echo $i; ?>" checked>
                                        </div>

                                        <div class="col-md-3"> 
                                            <div class="form-group">
                                                <label>Item Code</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($row['item_code']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
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
                                                            value="<?php echo htmlspecialchars($row['uom']); ?>" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Quantity</label>
                                                        <input type="number" step="0.01" name="qty[]"
                                                            class="form-control qty-input"
                                                            value="<?php echo htmlspecialchars($row['qty']); ?>" readonly>
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
                                                        <?php
                                                        echo form_dropdown(
                                                            'gst[]',
                                                            ['' => 'Select VAT']+$gst_opt,
                                                            set_value('gst[]', isset($row['gst']) ? $row['gst'] : ''),
                                                            'class="form-control vat-dropdown"'
                                                        );
                                                        ?>
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

                                    <input type="hidden" name="tender_enquiry_item_id[]"
                                        value="<?php echo htmlspecialchars($row['tender_enquiry_item_id']); ?>">
                                    <input type="hidden" name="category_id[]"
                                        value="<?php echo htmlspecialchars($row['category_id']); ?>">
                                    <input type="hidden" name="item_id[]"
                                        value="<?php echo htmlspecialchars($row['item_id']); ?>">
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
                                    <span class="value">₹ <span id="total_amount">0.00</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('tender-quotation-list'); ?>" class="btn btn-default"><i
                        class="fa fa-arrow-left"></i> Back To List</a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>