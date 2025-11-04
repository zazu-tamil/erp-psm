   

<?php include_once(VIEWPATH . 'inc/header.php'); ?>

<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Vendor</a></li>
        <li class="active">Add <?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-plus-circle"></i> Add Vendor Rate Enquiry</h3>
        </div>

        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Add" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i>Vendor Rate Enquiry</legend>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="srch_customer_id">Customer</label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select'] + $customer_opt, set_value('srch_customer_id'), 'id="srch_customer_id" class="form-control "'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="srch_tender_enquiry_id">Tender Enquiry No</label>
                            <?php echo form_dropdown('srch_tender_enquiry_id',['' => 'Select'] + $tender_enquiry_opt,set_value('srch_tender_enquiry_id'),'id="srch_tender_enquiry_id" class="form-control"'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="srch_vendor_id">Vendor Name</label>
                            <?php echo form_dropdown('srch_vendor_id', ['' => 'Select'] + $vendor_opt, set_value('srch_vendor_id'), 'id="srch_vendor_id" class="form-control "'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Enquiry No</label>
                            <input type="text" name="enquiry_no" id="enquiry_no" class="form-control" placeholder="e.g., TEN-2025-001" value="<?php echo set_value('enquiry_no'); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label>Enquiry Date</label>
                            <input type="date" name="enquiry_date" id="enquiry_date" class="form-control" value="<?php echo set_value('enquiry_date', date('Y-m-d')); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Opening Date & Time</label>
                            <input type="datetime-local" name="opening_date" id="opening_date" class="form-control" value="<?php echo set_value('opening_date'); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Closing Date & Time</label>
                            <input type="datetime-local" name="closing_date" id="closing_date" class="form-control" value="<?php echo set_value('closing_date'); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active" checked> Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="InActive"> InActive</label>
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

