   

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
            <h3 class="box-title"><i class="fa fa-plus-circle"></i> Add Tender Quotation</h3>
        </div>

        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Add" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Tender Quotation</legend>

                    <div class="row">

                        <div class="form-group col-md-3">
                            <label for="srch_company_id">Company</label>
                            <?php echo form_dropdown('srch_company_id', ['' => 'Select'] + $company_opt, set_value('srch_company_id'), 'id="srch_company_id" class="form-control "'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="srch_customer_id">Customer</label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select'] + $customer_opt, set_value('srch_customer_id'), 'id="srch_customer_id" class="form-control "'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="srch_tender_enquiry_id">Tender Enquiry No</label>
                            <?php echo form_dropdown('srch_tender_enquiry_id',['' => 'Select'] + $tender_enquiry_opt,set_value('srch_tender_enquiry_id'),'id="srch_tender_enquiry_id" class="form-control"'); ?>
                        </div> 
                        <div class="form-group col-md-3">
                            <label>Quotation No</label>
                            <input type="text" name="quotation_no" id="quotation_no" class="form-control" placeholder="e.g., TEN-2025-001" value="<?php echo set_value('quotation_no'); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Tender Ref No</label>
                            <input type="text" name="tender_ref_no" id="tender_ref_no" class="form-control" placeholder="e.g., TEN-2025-001" value="<?php echo set_value('tender_ref_no'); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" class="form-control" id="remarks" placeholder="Enter your remarks " rows="1"></textarea>
                        </div>
                   
                        <div class="form-group col-md-3">
                            <label>Quotation Date</label>
                            <input type="date" name="quote_date" id="quote_date" class="form-control" value="<?php echo set_value('quote_date', date('Y-m-d')); ?>">
                        </div> 
                        <div class="form-group col-md-3">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Confirmed" > Confirmed</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Pending" checked> Pending</label>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="mt-4">
                    <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>
                    <div id="item_container"></div>
                </fieldset>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('vendor-rate-enquiry-list'); ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back To List</a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
 