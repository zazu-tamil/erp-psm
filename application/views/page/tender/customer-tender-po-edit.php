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

        <form method="post" action="" id    ="frmedit" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Edit" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Purchase Order Details</legend>

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
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select'] + $tender_enquiry_opt, set_value('srch_tender_enquiry_id', $header['tender_enquiry_id']), 'id="srch_tender_enquiry_id" class="form-control"'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="tender_quotation_id">Quotation No</label>
                            <?php echo form_dropdown('tender_quotation_id', ['' => 'Select Quotation'], set_value('tender_quotation_id', $header['tender_quotation_id']), 'id="tender_quotation_id" class="form-control"'); ?>
                            <input type="hidden" name="tender_quotation_id" id="tender_quotation_id" value="<?php echo htmlspecialchars($header['tender_quotation_id']); ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Customer PO No</label>
                            <input type="text" name="customer_po_no" id="customer_po_no" class="form-control"
                                value="<?php echo htmlspecialchars($header['customer_po_no']); ?>"
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
                            <textarea name="remarks" class="form-control" id="editor2" placeholder="Enter your remarks" rows="5"><?php echo htmlspecialchars($header['remarks']); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Terms & Conditions</label>
                                <textarea id="editor1" name="terms" class="form-control custom-textarea" placeholder="Enter PO terms" required><?php echo htmlspecialchars($header['terms']); ?></textarea>
                            </div>
                        </div>
                    </div>

                </fieldset>

                <fieldset class="mt-4">
                    <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>

                    <div id="item_container">
                        <?php if (!empty($merged_items)): ?>
                            <?php foreach ($merged_items as $i => $row): ?>
                                <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">

                                    <h5 class="text-primary mb-3">
                                        Item <?php echo $i + 1; ?>
                                        <?php if ($row['saved']): ?> 
                                            <span class="badge bg-success">Saved</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">New</span>
                                        <?php endif; ?>
                                    </h5>

                                    <div class="row">

                                        <!-- Checkbox -->
                                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                                            <input type="checkbox" class="form-check-input item-check" 
                                                name="selected_items[]" value="<?php echo $i; ?>"
                                                <?php echo $row['saved'] ? 'checked' : ''; ?>>
                                        </div>

                                        <!-- Category + Item -->
                                        <div class="col-md-3"> 
                                            <div class="form-group">
                                                <label>Item Code</label>
                                                <input type="text" class="form-control" 
                                                    value="<?php echo htmlspecialchars($row['item_code']); ?>" readonly>
                                            </div>
                                        </div>

                                        <!-- Item Description -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Item Description</label>
                                                <textarea name="item_desc[]" class="form-control" rows="3"><?php echo htmlspecialchars($row['item_desc']); ?></textarea>
                                            </div>
                                        </div>

                                        <!-- UOM / Qty / Rate / VAT / Amount -->
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
                                                        <label>Qty</label>
                                                        <input type="number" step="0.01" name="qty[]" class="form-control qty-input"
                                                            value="<?php echo htmlspecialchars($row['qty']); ?>" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Rate</label>
                                                        <input type="number" step="0.01" name="rate[]" class="form-control rate-input"
                                                            value="<?php echo $row['rate']; ?>">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>VAT %</label>
                                                        <select name="gst[]" class="form-control vat-dropdown">
                                                            <option value="">Select</option>
                                                            <?php foreach($gst_opt as $gid => $pct): ?>
                                                                <option value="<?= $pct; ?>" 
                                                                    <?php echo (abs($row['gst'] - $pct) < 0.01) ? 'selected' : ''; ?>>
                                                                    <?= $pct; ?>%
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Amount</label>
                                                        <input type="number" step="0.01" name="amount[]" class="form-control amount-input"
                                                            value="<?php echo number_format($row['amount'], 2, '.', ''); ?>" readonly>
                                                    </div>
                                                </div>

                                            </div> <!-- row -->
                                        </div> <!-- col-md-4 -->

                                    </div> <!-- row -->

                                    <!-- Hidden fields -->
                                    <input type="hidden" name="tender_quotation_item_id[]" value="<?php echo $row['tender_quotation_item_id']; ?>">
                                    <input type="hidden" name="category_id[]" value="<?php echo $row['category_id']; ?>">
                                    <input type="hidden" name="item_id[]" value="<?php echo $row['item_id']; ?>">

                                    <?php if ($row['saved'] && $row['tender_po_item_id']): ?>
                                        <input type="hidden" name="tender_po_item_id_<?php echo $i; ?>" 
                                            value="<?php echo $row['tender_po_item_id']; ?>">
                                    <?php endif; ?>

                                </div> <!-- item-card -->
                            <?php endforeach; ?>

                        <?php else: ?>
                            <div class="alert alert-warning">No items in the selected quotation.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Total Section -->
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
                <a href="<?php echo site_url('customer-tender-po-list'); ?>" class="btn btn-default"><i
                        class="fa fa-arrow-left"></i> Back To List</a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>

