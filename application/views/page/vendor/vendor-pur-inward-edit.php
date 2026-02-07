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
                <input type="hidden" name="vendor_pur_inward_id" value="<?php echo $header['vendor_pur_inward_id']; ?>" />
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
                            <label>Vendor PO No <span style="color:red;">*</span>
                                <span data-toggle="tooltip" title="" class=""
                                    data-original-title="Only when Vendor PO Status is Confirmed then select the PO No. Otherwise leave it blank.">
                                    <i class="text-sm text-info fa fa-info-circle"></i>
                                </span>
                            </label>
                            <?php echo form_dropdown('srch_vendor_po_id', ['' => 'Select PO No'] + $vendor_po_opt, set_value('srch_vendor_po_id' , $header['vendor_po_id']), 'id="srch_vendor_po_id" class="form-control" required disabled'); ?>
                            <input type="hidden" name="srch_vendor_po_id" id="srch_vendor_po_id" value="<?php echo  $header['vendor_po_id']; ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Contact Person</label>
                            <?php echo form_dropdown('srch_vendor_contact_person_id', ['' => 'Select Contact'] + $vendor_contact_opt, set_value('srch_vendor_contact_person_id', $header['vendor_contact_person_id']), 'id="srch_vendor_contact_id" class="form-control"'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Inward Date</label>
                            <input type="date" name="inward_date" id="inward_date" class="form-control"
                                value="<?php echo set_value('inward_date', $header['inward_date']); ?>">
                        </div>

                       <div class="form-group col-md-4">
                            <label>Upload Quotation Document</label>

                            <div class="input-group">
                                <input type="file" class="form-control" id="dc_upload" name="dc_upload">

                                <?php if (!empty($header['dc_upload'])): ?>
                                    <span class="input-group-btn">
                                        <a href="<?php echo base_url($header['dc_upload']); ?>" 
                                        target="_blank"
                                        class="btn btn-info"
                                        style="margin-left:5px;">
                                            View Document
                                        </a>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

   
                         
                         <div class="form-group col-md-2">
                            <label>Inward Number <span style="color:red;">*</span></label>
                            <input type="text" name="inward_no" id="inward_no" class="form-control" placeholder="Enter Inward Number"
                                value="<?php echo set_value('inward_no', $header['inward_no']); ?>" required="true">
                         </div>
                        <div class="form-group col-md-2">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active" <?php echo set_radio('status', 'Active', $header['status'] == 'Active'); ?>> Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Inactive" <?php echo set_radio('status', 'Inactive', $header['status'] == 'Inactive'); ?>> Inactive</label>
                        </div>
                    </div> 
                    <div class="row">
                      <div class="col-md-12">
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea id="editor1" name="remarks" class="form-control"
                                    placeholder="Enter notes"><?php echo set_value('remarks', $header['remarks']); ?></textarea>
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
                                <th style="width:20%;">Item Code</th>
                                <th style="width:30%;">Description</th>
                                <th style="width:5%;">UOM</th>
                                <th style="width:10%;">Qty</th> 
                            </tr>
                        </thead>
                        <tbody id="item_container">
                            <?php if (!empty($items)): ?>
                                <?php $index = 0;
                                foreach ($items as $item): ?>
                                    <tr class="item-row">
                                        <td>
                                            <input type="checkbox" class="form-check-input item-check" name="selected_items[]"
                                                value="<?php echo $index; ?>" checked> <small class="badge badge-secondary">Item<?php echo $index + 1; ?></small>
                                        </td>

                                        <td>
                                            <input type="text" class="form-control"
                                                value="<?php echo htmlspecialchars($item['item_code']); ?>" readonly>

                                            <input type="hidden" name="vendor_po_item_id[<?php echo $index; ?>]"
                                                value="<?php echo $item['vendor_po_item_id']; ?>">
                                            <input type="hidden" name="category_id[<?php echo $index; ?>]"
                                                value="<?php echo $item['category_id']; ?>">
                                            <input type="hidden" name="item_id[<?php echo $index; ?>]"
                                                value="<?php echo $item['item_id']; ?>">
                                        </td>

                                        <td>
                                            <textarea name="item_desc[<?php echo $index; ?>]" class="form-control" rows="2"
                                                readonly><?php echo htmlspecialchars($item['item_desc']); ?></textarea>
                                        </td>

                                        <td>
                                            <input type="text" name="uom[<?php echo $index; ?>]" class="form-control"
                                                value="<?php echo $item['uom']; ?>" readonly>
                                        </td>

                                        <td>
                                            <input type="number" step="any" name="qty[<?php echo $index; ?>]"
                                                class="form-control qty-input" value="<?php echo $item['qty']; ?>">
                                        </td> 

                                    </tr>
                                    <?php $index++; endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </fieldset>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('vendor-pur-inward-list'); ?>" class="btn btn-warning pull-left">
                    <i class="fa fa-arrow-left"></i> Back To List
                </a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</section> 
<?php include_once(VIEWPATH . 'inc/footer.php'); ?>