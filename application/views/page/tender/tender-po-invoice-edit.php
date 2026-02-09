<?php include_once(VIEWPATH . 'inc/header.php');

// echo '<pre>';
// print_r($header);
// echo '</pre>';
?>

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
                <input type="hidden" name="mode" value="Edit" />
                <input type="hidden" name="tender_enq_invoice_id"
                    value="<?php echo $header['tender_enq_invoice_id']; ?>" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Invoice Details</legend>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="srch_company_id">Company <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_company_id', ['' => 'Select Company'] + $company_opt, set_value('srch_company_id', $header['company_id']), 'id="srch_company_id" class="form-control" required disabled'); ?>
                            <input type="hidden" name="srch_company_id" id="srch_company_id"
                                value="<?php echo $header['company_id']; ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_customer_id">Customer <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('srch_customer_id', $header['customer_id']), 'id="srch_customer_id" class="form-control" required     disabled'); ?>
                            <input type="hidden" name="srch_customer_id" id="srch_customer_id"
                                value="<?php echo $header['customer_id']; ?>">
                        </div>


                        <div class="form-group col-md-3">
                            <label for="srch_tender_enquiry_id">Tender Enquiry No <span
                                    style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select Customer'] + $tender_enquiry_opt, set_value('srch_tender_enquiry_id', $header['tender_enquiry_id']), 'id="srch_tender_enquiry_id" class="form-control" required disabled'); ?>
                        </div>
                        <input type="hidden" name="srch_tender_enquiry_id" id="tender_enquiry_id"
                            value="<?php echo $header['tender_enquiry_id']; ?>">

                        <!-- <div class="form-group col-md-3">
                            <label for="srch_tender_enquiry_id">Tender Enquiry No <span
                                    style="color:red;">*</span></label>
                            <?php echo form_dropdown(
                                'srch_tender_enquiry_id',
                                ['' => 'Select Enquiry'] + $tender_enquiry_opt,
                                set_value('srch_tender_enquiry_id', $header['tender_enquiry_id']),
                                'id="srch_tender_enquiry_id" class="form-control" required'
                            ); ?>
                          
                        </div> -->

                        <div class="form-group col-md-3">
                            <label for="srch_tender_po_id">Tender PO No <span style="color:red;">*</span></label>
                            <?php echo form_dropdown(
                                'srch_tender_po_id',
                                ['' => 'Select PO'] + $tender_po_opt,
                                set_value('srch_tender_po_id', $header['tender_po_id']),
                                'id="srch_tender_po_id" class="form-control" disabled required'
                            ); ?>
                            <input type="hidden" name="srch_tender_po_id" id="srch_tender_po_id"
                                value="<?php echo $header['tender_po_id']; ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Invoice Date</label>
                            <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                                value="<?php echo set_value('invoice_date', date('Y-m-d', strtotime($header['invoice_date']))); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Invoice No</label>
                            <input type="text" name="invoice_no" id="invoice_no" class="form-control"
                                value="<?php echo set_value('invoice_no', $header['invoice_no']); ?>"
                                placeholder="Enter your invoice date">
                        </div>
                        <div class="col-md-3">
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
                            <label>Invoice Status</label><br>
                            <label class="radio-inline"><input type="radio" name="invoice_status" value="Pending" <?php echo set_value('invoice_status', $header['invoice_status']) == 'Pending' ? 'checked' : ''; ?>>Pending</label>
                            <label class="radio-inline"><input type="radio" name="invoice_status" value="Payment Paid"
                                    <?php echo set_value('invoice_status', $header['invoice_status']) == 'Payment Paid' ? 'checked' : ''; ?>>Payment Paid</label>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active" <?php echo set_value('status', $header['status']) == 'Active' ? 'checked' : ''; ?>>
                                Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Inactive" <?php echo set_value('status', $header['status']) == 'Inactive' ? 'checked' : ''; ?>>
                                Inactive</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="remarks">Notes</label>
                            <textarea name="remarks" class="form-control" id="editor2" placeholder="Enter your remarks"
                                rows="5"><?php echo nl2br($header['remarks']); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Terms & Conditions</label>
                                <textarea id="editor1" name="terms" class="form-control custom-textarea"
                                    placeholder="Enter PO terms"
                                    required><?php echo set_value('terms', $header['terms']); ?></textarea>
                            </div>
                        </div>
                    </div>

                </fieldset>
                <fieldset class="mt-4">
                    <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>
                    <table class="table table-bordered table-sm">
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
                        <tbody id="item_container">
                            <?php if (!empty($item_list)): ?>
                                <?php $index = 0;
                                foreach ($item_list as $item): ?>
                                    <tr class="item-row">
                                        <td>
                                            <input type="checkbox" class="form-check-input item-check" name="selected_items[]"
                                                value="<?php echo $index; ?>" checked>
                                            <input type="hidden" name="tender_enq_invoice_item_id[<?php echo $index; ?>]"
                                                value="<?php echo $item['tender_enq_invoice_item_id']; ?>">
                                        </td>

                                        <td>
                                            <input type="text" class="form-control"
                                                value="<?php echo htmlspecialchars($item['item_code']); ?>" readonly>

                                            <input type="hidden" name="tender_po_item_id[<?php echo $index; ?>]"
                                                value="<?php echo $item['tender_po_item_id']; ?>">
                                        </td>

                                        <td>
                                            <textarea name="item_desc[<?php echo $index; ?>]" class="form-control" rows="2"
                                                readonly><?php echo htmlspecialchars($item['item_desc']); ?></textarea>
                                        </td>

                                        <td>
                                            <input type="text" name="uom[<?php echo $index; ?>]" class="form-control"
                                                value="<?php echo $item['uom']; ?>" readonly>
                                            <br>
                                            <input type="number" step="any" name="qty[<?php echo $index; ?>]"
                                                class="form-control qty-input" value="<?php echo $item['qty']; ?>" readonly>
                                        </td>

                                        <td>
                                            <input type="number" step="any" name="rate[<?php echo $index; ?>]"
                                                class="form-control rate-input" value="<?php echo number_format($item[' '], 3); ?>">
                                        </td>

                                        <td>
                                            <input type="number" step="any" name="gst[<?php echo $index; ?>]"
                                                class="form-control gst-select" value="<?php echo $item['gst']; ?>">
                                        </td>

                                        <td>
                                            <input type="number" step="any" name="amount[<?php echo $index; ?>]"
                                                class="form-control amount-input" value="<?php echo $item['amount']; ?>"
                                                readonly>
                                        </td>

                                    </tr>
                                    <?php $index++; endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </fieldset>
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
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('customer-tender-po-list'); ?>" class="btn btn-default"><i
                        class="fa fa-arrow-left"></i> Back To List</a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php');



?>