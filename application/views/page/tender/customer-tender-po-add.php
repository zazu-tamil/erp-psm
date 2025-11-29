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
            <h3 class="box-title"><i class="fa fa-plus-circle"></i> Add  Tender PO</h3>
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
                            <label for="srch_tender_enquiry_id">Tender Enquiry No <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select Enquiry'], set_value('srch_tender_enquiry_id'), 'id="srch_tender_enquiry_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_quotation_no">Quotation No <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_quotation_no', ['' => 'Select Quotation'], set_value('srch_quotation_no'), 'id="srch_quotation_no" class="form-control" required'); ?>
                        </div>

                        <!-- <div class="form-group col-md-3">
                            <label>Our PO No</label>
                            <input type="text" name="our_po_no" id="our_po_no" class="form-control"
                                placeholder="e.g., PO-2025-001" value="<?php echo set_value('our_po_no'); ?>">
                        </div> -->

                        <div class="form-group col-md-3">
                            <label>Customer PO No</label>
                            <input type="text" name="customer_po_no" id="customer_po_no" class="form-control"
                                placeholder="e.g., CUST-PO-2025-001" value="<?php echo set_value('customer_po_no'); ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>PO Date</label>
                            <input type="date" name="po_date" id="po_date" class="form-control"
                                value="<?php echo set_value('po_date', date('Y-m-d')); ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>PO Received Date</label>
                            <input type="date" name="po_received_date" id="po_received_date" class="form-control"
                                value="<?php echo set_value('po_received_date'); ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Delivery Date</label>
                            <input type="date" name="delivery_date" id="delivery_date" class="form-control"
                                value="<?php echo set_value('delivery_date'); ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>PO Status</label><br>
                            <label class="radio-inline"><input type="radio" name="po_status" value="Open">Open</label>
                            <label class="radio-inline"><input type="radio" name="po_status" value="In Progress"> In Progress</label>
                            <label class="radio-inline"><input type="radio" name="po_status" value="Completed"> Completed</label>
                            <label class="radio-inline"><input type="radio" name="po_status" value="Cancelled"> Cancelled</label>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active" checked> Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Inactive"> Inactive</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="remarks">Notes</label>
                            <textarea name="remarks" class="form-control" id="editor2" placeholder="Enter your remarks" rows="5"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Terms & Conditions</label>
                                <textarea id="editor1" name="terms" class="form-control custom-textarea" placeholder="Enter PO terms" required></textarea>
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
                <a href="<?php echo site_url('customer-tender-po-list'); ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back To List</a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>