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
            <h3 class="box-title"><i class="fa fa-plus-circle"></i> Add Tender PO</h3>
        </div>

        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Add" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Purchase Order Details</legend>

                    <div
                        style="border:1px solid #ddd; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:5px;">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="srch_enq_id">Search Enquiry No</label>
                                <input type="text" name="srch_enq_id" class="form-control srch_enq_id" value=""
                                    placeholder="Search Enquiry No" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="srch_company_id">Company <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_company_id',  $company_opt, set_value('srch_company_id'), 'id="srch_company_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_customer_id">Customer <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('srch_customer_id'), 'id="srch_customer_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_tender_enquiry_id">Tender Enquiry No <span style="color:red;">*</span> <i
                                    class="text-red text-sm text-right"> [ Note: Tender Enq Status : Won ] </i> </label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select Enquiry'], set_value('srch_tender_enquiry_id'), 'id="srch_tender_enquiry_id" class="form-control" required'); ?>

                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_quotation_no">Quotation No <span style="color:red;">*</span> <i
                                    class="text-red text-sm text-right"> [ Note: Quotation Status : Won ] </i></label>
                            <?php echo form_dropdown('srch_quotation_no', ['' => 'Select Quotation'], set_value('srch_quotation_no'), 'id="srch_quotation_no" class="form-control" required'); ?>
                        </div>

                        <!-- <div class="form-group col-md-3">
                            <label>Our PO No</label>
                            <input type="text" name="our_po_no" id="our_po_no" class="form-control"
                                placeholder="e.g., PO-2025-001" value="<?php //echo set_value('our_po_no'); ?>">
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
                            <label class="radio-inline"><input type="radio" name="po_status" value="Open"
                                    checked>Open</label>
                            <label class="radio-inline"><input type="radio" name="po_status" value="In Progress"> In
                                Progress</label>
                            <label class="radio-inline"><input type="radio" name="po_status" value="Completed">
                                Completed</label>
                            <label class="radio-inline"><input type="radio" name="po_status" value="Cancelled">
                                Cancelled</label>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="currency_id">Currency</label>
                                <?php
                                echo form_dropdown(
                                    'currency_id',
                                    ['' => 'Select Currency'] + $currency_opt,
                                    set_value('currency_id', $default_currency_id),
                                    'id="currency_id" class="form-control" required'
                                );
                                ?>
                            </div>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active" checked>
                                Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Inactive">
                                Inactive</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="remarks">Notes</label>
                            <textarea name="remarks" class="form-control" id="editor2" placeholder="Enter your remarks"
                                rows="5"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Terms & Conditions</label>
                                <textarea id="editor1" name="terms" class="form-control custom-textarea"
                                    placeholder="Enter PO terms" required></textarea>
                            </div>
                        </div>
                    </div>

                </fieldset>

                <fieldset class="mt-4">
                    <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>

                    <div class="row cls_export ">
                        <div class="col-md-4 form-group">
                            <label for="btnExport">Click Here - Export as Excel File</label> <br>
                            <button id="btnExport" type="button" class="btn btn-success" value="" data-xls="">Export
                                Excel & Download</button>
                        </div>
                        <div class="col-md-4 form-group ">
                            <label for="excelFile">Choose Excel File to Import</label>
                            <input type="file" class="form-control" id="excelFile" accept=".xls,.xlsx"
                                placeholder="Choose Excel File to Import">
                        </div>
                        <div class="col-md-4 form-group ">
                            <i class="text-red">Note: <br>Don't change <b class="text-info">[ tender_quotation_item_id ]
                                </b> column its software referance Ids in excel file while
                                importing</i>
                        </div>
                    </div>

                    <div id="item_container"></div>
                    <div class="total-wrapper mt-4 mb-4">
                        <div class="total-box shadow-sm">
                            <h5 class="mb-0">
                                <i class="fa fa-calculator text-success me-2"></i>
                                <strong>Total Amount:</strong>
                                <span class="text-primary"><span id="total_amount">0.00</span></span>
                            </h5>
                        </div>
                    </div>

                </fieldset>
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



/*
 <div class="col-md-3">
                     <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" class="form-control" value="${row.category_name || ''}" readonly>
                      </div>
                      <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" class="form-control" value="${row.item_name || ''}" readonly>
                      </div>
                    </div>*/
?>