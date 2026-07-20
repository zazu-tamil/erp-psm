<?php include_once(VIEWPATH . 'inc/header.php'); ?>

<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-money"></i> Transactions</a></li>
        <li class="active"><?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">
    <form id="frmCreditDebitNote" method="post" enctype="multipart/form-data">
        <?php if(isset($edit_data)): ?>
            <input type="hidden" name="note_id" value="<?php echo $edit_data['credit_debit_note_id']; ?>">
        <?php endif; ?>
        <!-- 1. Document Details -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><span class="badge bg-blue">1</span> Document Details</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Transaction Type <span class="text-danger">*</span></label><br>
                        <label class="radio-inline"><input type="radio" name="note_type" value="Credit" <?php echo (isset($edit_data) && $edit_data['note_type'] == 'Credit') ? 'checked' : (isset($edit_data) ? '' : 'checked'); ?>> Credit Note</label>
                        <label class="radio-inline"><input type="radio" name="note_type" value="Debit" <?php echo (isset($edit_data) && $edit_data['note_type'] == 'Debit') ? 'checked' : ''; ?>> Debit Note</label>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Note No. <span class="text-danger">*</span></label>
                        <input type="text" name="note_no" class="form-control" readonly placeholder="Auto Generated" value="<?php echo isset($edit_data) ? htmlspecialchars($edit_data['note_no']) : ''; ?>">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" name="note_date" class="form-control" value="<?php echo isset($edit_data) ? $edit_data['note_date'] : date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Company / Branch <span class="text-danger">*</span></label>
                        <?php echo form_dropdown('company_id', $company_opt, isset($edit_data) ? $edit_data['company_id'] : '', 'id="company_id" class="form-control" required'); ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Party Type <span class="text-danger">*</span></label>
                        <select name="party_type" id="party_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="Customer" <?php echo (isset($edit_data) && $edit_data['party_type'] == 'Customer') ? 'selected' : ''; ?>>Customer</option>
                            <option value="Supplier" <?php echo (isset($edit_data) && $edit_data['party_type'] == 'Supplier') ? 'selected' : ''; ?>>Supplier</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group" id="customer_div" style="display:<?php echo (isset($edit_data) && $edit_data['party_type'] == 'Customer') ? 'block' : 'none'; ?>;">
                        <label>Customer <span class="text-danger">*</span></label>
                        <?php echo form_dropdown('customer_id', $customer_opt, isset($edit_data) ? $edit_data['customer_id'] : '', 'id="customer_id" class="form-control"'); ?>
                    </div>
                    <div class="col-md-3 form-group" id="supplier_div" style="display:<?php echo (isset($edit_data) && $edit_data['party_type'] == 'Supplier') ? 'block' : 'none'; ?>;">
                        <label>Supplier <span class="text-danger">*</span></label>
                        <?php echo form_dropdown('supplier_id', $supplier_opt, isset($edit_data) ? $edit_data['supplier_id'] : '', 'id="supplier_id" class="form-control"'); ?>
                    </div>
                    
                    <div class="col-md-3 form-group">
                        <label>Search Enquiry No <span class="text-danger">*</span></label>
                        <input type="text" name="srch_enq_id" id="srch_enq_id" class="form-control srch_enq_id" placeholder="Search Enquiry No" value="<?php echo isset($edit_data) ? htmlspecialchars($edit_data['tender_no']) : ''; ?>">
                        <input type="hidden" name="tender_enquiry_id" id="tender_enquiry_id" value="<?php echo isset($edit_data) ? $edit_data['tender_enquiry_id'] : ''; ?>">
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Reference Invoice <span class="text-danger">*</span></label>
                        <select name="reference_invoice_id" id="reference_invoice_id" class="form-control" required data-selected-on-load="<?php echo isset($edit_data) ? $edit_data['reference_invoice_id'] : ''; ?>">
                            <option value="">Select Invoice</option>
                        </select>
                        <input type="hidden" name="reference_invoice_no" id="reference_invoice_no" value="<?php echo isset($edit_data) ? htmlspecialchars($edit_data['reference_invoice_no']) : ''; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Currency <span class="text-danger">*</span></label>
                        <?php echo form_dropdown('currency_id', $currency_opt, isset($edit_data) ? $edit_data['currency_id'] : '17', 'class="form-control" required'); ?>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Exchange Rate</label>
                        <input type="number" name="exchange_rate" class="form-control" step="0.000001" value="<?php echo isset($edit_data) ? $edit_data['exchange_rate'] : '1.000000'; ?>">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Reason <span class="text-danger">*</span></label>
                        <select name="reason" class="form-control" required>
                            <option value="">Select Reason</option>
                            <option value="Sales Return" <?php echo (isset($edit_data) && $edit_data['reason'] == 'Sales Return') ? 'selected' : ''; ?>>Sales Return</option>
                            <option value="Post Sale Discount" <?php echo (isset($edit_data) && $edit_data['reason'] == 'Post Sale Discount') ? 'selected' : ''; ?>>Post Sale Discount</option>
                            <option value="Deficiency in Services" <?php echo (isset($edit_data) && $edit_data['reason'] == 'Deficiency in Services') ? 'selected' : ''; ?>>Deficiency in Services</option>
                            <option value="Correction in Invoice" <?php echo (isset($edit_data) && $edit_data['reason'] == 'Correction in Invoice') ? 'selected' : ''; ?>>Correction in Invoice</option>
                            <option value="Change in POS" <?php echo (isset($edit_data) && $edit_data['reason'] == 'Change in POS') ? 'selected' : ''; ?>>Change in POS</option>
                            <option value="Other" <?php echo (isset($edit_data) && $edit_data['reason'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="1"><?php echo isset($edit_data) ? htmlspecialchars($edit_data['remarks']) : ''; ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Item Details -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><span class="badge bg-blue">2</span> Item Details</h3>
                <div class="pull-right">
                    <button type="button" class="btn btn-primary btn-sm" id="btnAddItem"><i class="fa fa-plus"></i> Add Item</button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="itemTable">
                        <thead>
                            <tr class="bg-gray">
                                <th>#</th>
                                <th>Item Code</th>
                                <th>Description</th>
                                <th>HSN/SAC</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Rate</th>
                                <th>VAT %</th>
                                <th>VAT Amt</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic rows go here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="9" class="text-right">Sub Total</th>
                                <th class="text-right"><input type="text" name="subtotal" id="subtotal" class="form-control text-right" readonly value="<?php echo isset($edit_data) ? $edit_data['subtotal'] : '0.00'; ?>"></th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="9" class="text-right">Taxable Amount</th>
                                <th class="text-right"><input type="text" name="taxable_amount" id="taxable_amount" class="form-control text-right" readonly value="<?php echo isset($edit_data) ? $edit_data['taxable_amount'] : '0.00'; ?>"></th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="9" class="text-right">Total VAT Amount</th>
                                <th class="text-right"><input type="text" name="tax_amount" id="tax_amount" class="form-control text-right" readonly value="<?php echo isset($edit_data) ? $edit_data['tax_amount'] : '0.00'; ?>"></th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="9" class="text-right">Round Off</th>
                                <th class="text-right"><input type="number" name="roundoff" id="roundoff" class="form-control text-right calc-total" step="0.01" value="<?php echo isset($edit_data) ? $edit_data['roundoff'] : '0.00'; ?>"></th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="9" class="text-right text-primary" style="font-size:18px;">Total Amount</th>
                                <th class="text-right"><input type="text" name="total_amount" id="total_amount" class="form-control text-right text-bold text-primary" readonly value="<?php echo isset($edit_data) ? $edit_data['total_amount'] : '0.00'; ?>"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- 3. Adjustments -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><span class="badge bg-blue">3</span> Adjustments</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group row">
                            <label class="col-sm-6 control-label">Outstanding Invoice Amount</label>
                            <div class="col-sm-6">
                                <input type="number" name="outstanding_invoice_amount" id="outstanding_invoice_amount" class="form-control text-right" step="0.01" readonly value="<?php echo isset($edit_adjustments['invoice_amount']) ? $edit_adjustments['invoice_amount'] : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-6 control-label">Adjust Amount <span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" name="adjustment_amount" id="adjustment_amount" class="form-control text-right" step="0.01" value="<?php echo isset($edit_data) ? $edit_data['adjustment_amount'] : '0.00'; ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-6 control-label">Remaining Credit</label>
                            <div class="col-sm-6">
                                <input type="number" name="remaining_amount" id="remaining_amount" class="form-control text-right" step="0.01" readonly value="<?php echo isset($edit_data) ? $edit_data['remaining_amount'] : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Attachments & Notes -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><span class="badge bg-blue">4</span> Attachments & Notes</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label>Upload File</label>
                            <input type="file" name="attachment_file" class="form-control" accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx">
                            <?php if (isset($edit_attachments) && count($edit_attachments) > 0): ?>
                                <small>Currently attached: <?php echo htmlspecialchars($edit_attachments[0]['file_name']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Internal Notes</label>
                            <textarea name="internal_notes" class="form-control" rows="3" placeholder="Max 250 characters"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box-footer text-right">
            <?php if(isset($edit_data)): ?>
                <button type="button" class="btn btn-danger pull-left" onclick="deleteNote(<?php echo $edit_data['credit_debit_note_id']; ?>)"><i class="fa fa-trash"></i> Delete</button>
            <?php endif; ?>
            <input type="hidden" name="status" id="doc_status" value="Draft">
            <a href="javascript:history.back();" class="btn btn-default">Cancel</a>
            <button type="submit" class="btn btn-warning" onclick="$('#doc_status').val('Draft');"><i class="fa fa-save"></i> Save Draft</button>
            <button type="submit" class="btn btn-primary" onclick="$('#doc_status').val('Approved');"><i class="fa fa-check"></i> Save & Approve</button>
            <button type="submit" class="btn btn-success" onclick="$('#doc_status').val('Posted');"><i class="fa fa-send"></i> Post</button>
        </div>
    </form>
</section>

<script>
    var taxOptionsHtml = '';
    <?php foreach($tax_opt as $val => $text): ?>
        taxOptionsHtml += '<option value="<?php echo htmlspecialchars($val); ?>"><?php echo htmlspecialchars($text); ?></option>';
    <?php endforeach; ?>

    var initialEditItems = [];
    <?php if (isset($edit_items) && !empty($edit_items)): ?>
        initialEditItems = <?php echo json_encode($edit_items); ?>;
    <?php endif; ?>
</script>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
