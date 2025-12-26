<?php include_once(VIEWPATH . 'inc/header.php');
// echo '<pre>';
//  print_r($items);
// echo '</pre>'; 
?>

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
            <h3 class="box-title"><i class="fa fa-plus-circle"></i> Add Vendor PO</h3>
            <a href="<?php echo site_url('vendor-po-list'); ?>" class="btn btn-warning pull-right">
                <i class="fa fa-arrow-left"></i> Back To List
            </a>
        </div>

        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Edit" />
                <input type="hidden" name="vendor_po_id" value="<?php echo $header['vendor_po_id']; ?>" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Po Details</legend>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Company <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_company_id', ['' => 'Select Company'] + $company_opt, set_value('srch_company_id', $header['company_id']), 'id="srch_company_id" class="form-control" required disabled'); ?>
                            <input type="hidden" name="srch_company_id" id="srch_company_id" value="<?php echo  $header['company_id']; ?>" required="true">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Customer <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('srch_customer_id', $header['customer_id']), 'id="srch_customer_id" class="form-control" required disabled'); ?>
                             <input type="hidden" name="srch_customer_id" id="srch_customer_id" value="<?php echo  $header['customer_id']; ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Tender Enquiry No <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select Enquiry'] + $tender_enquiry_opt, set_value('srch_tender_enquiry_id', $header['tender_enquiry_id']), 'id="srch_tender_enquiry_id" class="form-control" required disabled' ); ?>
                                <input type="hidden" name="srch_tender_enquiry_id" id="srch_tender_enquiry_id" value="<?php echo  $header['tender_enquiry_id']; ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Vendor Name <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_vendor_id', ['' => 'Select Vendor'] + $vendor_opt, set_value('srch_vendor_id', $header['vendor_id']), 'id="srch_vendor_id" class="form-control"  required disabled'); ?>
                            <input type="hidden" name="srch_vendor_id" id="srch_vendor_id" value="<?php echo  $header['vendor_id']; ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Vendor Quotation No <span style="color:red;">*</span>
                                <span data-toggle="tooltip" title="" class=""
                                    data-original-title="Only when Vendor Quotation Status is Confirmed then select the Quotation No. Otherwise leave it blank.">
                                    <i class="text-sm text-info fa fa-info-circle"></i>
                                </span>
                            </label>
                            <?php echo form_dropdown('srch_vendor_quote_id', ['' => 'Select Enquiry No']  + $vendor_quote_opt, set_value('srch_vendor_quote_id', $header['vendor_quote_id']), 'id="srch_vendor_quote_id" class="form-control" required disabled'); ?>
                            <input type="hidden" name="srch_vendor_quote_id" id="srch_vendor_quote_id" value="<?php echo  $header['vendor_quote_id']; ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Contact Person</label>
                            <?php echo form_dropdown('srch_vendor_contact_person_id', ['' => 'Select Contact'] + $vendor_contact_opt, set_value('srch_vendor_contact_person_id', $header['vendor_contact_person_id']), 'id="srch_vendor_contact_id" class="form-control"'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>PO Date</label>
                            <input type="date" name="po_date" id="po_date" class="form-control"
                                value="<?php echo set_value('po_date', $header['po_date']); ?>">
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="delivery_date">Delivery Date</label>
                             <input type="date" name="delivery_date" id="delivery_date" class="form-control"
                                value="<?php echo set_value('delivery_date', date('Y-m-d', strtotime($header['delivery_date']))); ?>">

                                    
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label>PO Status <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('po_status', ['' => 'Select Status'] + $po_status_opt, set_value('po_status', $header['po_status']), 'id="po_status" class="form-control" required="true"'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Transport Charges</label>
                            <input type="number" step="0.01" name="transport_charges" id="transport_charges"
                                class="form-control" value="<?php echo set_value('transport_charges', $header['transport_charges']); ?>"
                                placeholder="0.00">
                        </div> 
                        <div class="form-group col-md-4">
                            <label>Other Charges</label>
                            <input type="number" step="0.01" name="other_charges" id="other_charges"
                                class="form-control" value="<?php echo set_value('other_charges', $header['other_charges']); ?>"
                                placeholder="0.00">
                        </div>
                        <!-- Po Number -->
                         <div class="form-group col-md-2">
                            <label>Po Number <span style="color:red;">*</span></label>
                            <input type="text" name="po_no" id="po_no" class="form-control" placeholder="Enter PO Number"
                                value="<?php echo set_value('po_no', $header['po_no']); ?>" required="true">
                         </div>
                        <div class="form-group col-md-2">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active" <?php echo set_radio('status', 'Active', $header['status'] == 'Active'); ?>> Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Inactive" <?php echo set_radio('status', 'Inactive', $header['status'] == 'Inactive'); ?>> Inactive</label>
                        </div>
                    </div> 
                    <div class="row">
                      <div class="col-md-6">
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea id="editor1" name="remarks" class="form-control"
                                    placeholder="Enter notes"><?php echo set_value('remarks', $header['remarks']); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Terms & Conditions</label>
                                <textarea id="editor2" name="terms" class="form-control"
                                    placeholder="Enter terms"><?php echo set_value('terms', $header['terms']); ?></textarea>
                            </div>
                        </div>
                    </div>


                </fieldset>

                 <fieldset class="mt-4">
                    <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th style="width:5%;">Select</th>
                                <th style="width:10%;">Item Code</th>
                                <th style="width:30%;">Description</th>
                                <th style="width:10%;">UOM & Qty</th> 
                                <th style="width:10%;">Rate</th>
                                <th style="width:10%;">VAT %</th>
                                <th style="width:10%;">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="item_container">
                            <?php if (!empty($items)): ?>
                                <?php $index = 0;
                                foreach ($items as $item): ?>
                                    <tr class="item-row">
                                        <td>
                                            <input type="checkbox" class="form-check-input item-check" name="selected_items[]"
                                                value="<?php echo $index; ?>" checked>
                                        </td>

                                        <td>
                                            <input type="text" class="form-control item_code-input" name="item_code[<?php echo $index; ?>]"
                                                value="<?php echo htmlspecialchars($item['item_code']); ?>" readonly>

                                            <input type="hidden" name="vendor_rate_enquiry_item_id[<?php echo $index; ?>]"
                                                value="<?php echo $item['vendor_rate_enquiry_item_id']; ?>">
                                                <input type="hidden" name="vendor_po_item_id[<?php echo $index; ?>]"
                                                value="<?php echo $item['vendor_po_item_id']; ?>">
                                            
                                        </td>

                                        <td>
                                            <textarea name="item_desc[<?php echo $index; ?>]" class="form-control" rows="2"
                                                ><?php echo htmlspecialchars($item['item_desc']); ?></textarea>
                                        </td>

                                        <td>
                                            <input type="text" name="uom[<?php echo $index; ?>]" class="form-control"
                                                value="<?php echo $item['uom']; ?>" >
                                            <br>
                                            <input type="number" step="0.01" name="qty[<?php echo $index; ?>]"
                                                class="form-control qty-input" value="<?php echo $item['qty']; ?>" >
                                        </td>

                                        <td>
                                            <input type="number" step="0.01" name="rate[<?php echo $index; ?>]"  class="form-control rate-input" value="<?php echo $item['rate']; ?>">
                                        </td>

                                        <td>
                                            <input type="number" step="0.01" name="gst[<?php echo $index; ?>]"  class="form-control vat" value="<?php echo $item['vat']; ?>">
                                        </td>

                                        <td>
                                            <input type="number" step="0.01" name="amount[<?php echo $index; ?>]"
                                                class="form-control amount-input" value="<?php echo $item['amount']; ?>"
                                                readonly>
                                        </td>

                                    </tr>
                                    <?php $index++; endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                         <thead>
                            <tr>
                                <th colspan="6" class="text-right">Total</th>
                                <th class="text-right">
                                    <span class="value"> <span id="total_amount">0.00</span></span>
                                </th>
                            </tr>
                        </thead>
                    </table>
                </fieldset>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('vendor-po-list'); ?>" class="btn btn-warning pull-left">
                    <i class="fa fa-arrow-left"></i> Back To List
                </a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</section> 
<?php include_once(VIEWPATH . 'inc/footer.php'); ?>