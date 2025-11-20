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
            <h3 class="box-title"><i class="fa fa-plus-circle"></i> Add Vendor PO </h3>
            <a href="<?php echo site_url('vendor-po-list'); ?>" class="btn btn-warning pull-right"><i
                        class="fa fa-arrow-left"></i> Back To List</a>
        </div>

        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Add" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Purchase Order Details</legend>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="srch_company_id">Company <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_company_id', ['' => 'Select Company'] + $company_opt, set_value('srch_company_id'), 'id="srch_company_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_customer_id">Customer <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select Customer'], set_value('srch_customer_id'), 'id="srch_customer_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_tender_enquiry_id">Tender Enquiry No <span
                                    style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select Enquiry'], set_value('srch_tender_enquiry_id'), 'id="srch_tender_enquiry_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_vendor_rate_enquiry_id">Vendor Rate Enquiry No <span
                                    style="color:red;">*</span></label>
                            <?php echo form_dropdown('vendor_rate_enquiry_id', ['' => 'Select Enquiry No'], set_value('srch_vendor_rate_enquiry_id'), 'id="srch_vendor_rate_enquiry_id" class="form-control" required'); ?>
                        </div>                        
                        <div class="form-group col-md-3">
                            <label for="srch_vendor_id">Vendor Name <span class="text-red">*</span></label>
                            
                                <?php echo form_dropdown('srch_vendor_id', ['' => 'Select'] + $vendor_opt, set_value('srch_vendor_id'), 'id="srch_vendor_id" class="form-control" required'); ?> 
                           
                        </div>
                        <div class="form-group col-md-3">
                            <label for="srch_vendor_contact_id">Contact Person</label>
                           
                                <?php echo form_dropdown('srch_vendor_contact_person_id', ['' => 'Select'] + $vendor_contact_opt, set_value('srch_vendor_contact_id'), 'id="srch_vendor_contact_id" class="form-control "'); ?> 
                        </div>

                        <!-- <div class="form-group col-md-3">
                            <label>Our PO No</label>
                            <input type="text" name="our_po_no" id="our_po_no" class="form-control"
                                placeholder="e.g., PO-2025-001" value="<?php echo set_value('our_po_no'); ?>">
                        </div> -->
<!-- 
                        <div class="form-group col-md-3">
                            <label>PO No</label>
                            <input type="text" name="po_no" id="po_no" class="form-control"
                                placeholder="e.g., CUST-PO-2025-001" value="<?php echo set_value('po_no'); ?>">
                        </div> -->

                        <div class="form-group col-md-3">
                            <label>PO Date</label>
                            <input type="date" name="po_date" id="po_date" class="form-control"
                                value="<?php echo set_value('po_date', date('Y-m-d')); ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Opening Date</label>
                            <input type="date" name="opening_date" id="opening_date" class="form-control"
                                value="<?php echo set_value('opening_date', date('Y-m-d')); ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>PO Closing Date</label>
                            <input type="date" name="closing_date" id="closing_date" class="form-control"
                                value="<?php echo set_value('closing_date'); ?>">
                        </div> 

                        <div class="form-group col-md-3">
                            <label>PO Status <span style="color:red;">*</span></label><br>
                            <?php echo form_dropdown('po_status', ['' => 'Select Po Status'] + $tender_status_opt, set_value('po_status'), 'id="po_status" class="form-control" required'); ?> 
                        </div>

                        <div class="form-group col-md-3">
                            <label>Handling Charges</label><br>
                            <input type="number" step="0.01" name="handling_charges" id="handling_charges" class="form-control" value="" placeholder="100.00">
                            
                        </div>
                        <div class="form-group col-md-2">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active" checked>
                                Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Inactive">
                                Inactive</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" class="form-control" id="remarks" placeholder="Enter your remarks"
                                rows="5"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>PO Terms & Conditions</label>
                                <textarea id="editor1" name="terms" class="form-control custom-textarea"
                                    placeholder="Enter PO terms" required></textarea>
                            </div>
                        </div>
                    </div>

                </fieldset>

                <fieldset class="mt-4">
                    <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>
                    <div id="item_container"></div>
                </fieldset>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('vendor-po-list'); ?>" class="btn btn-warning pull-left"><i
                        class="fa fa-arrow-left"></i> Back To List</a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>