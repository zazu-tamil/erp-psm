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
            <h3 class="box-title"><i class="fa fa-plus-circle"></i> Add Tender Enquiry</h3>
        </div>

        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Add" />

                <!-- Tender Enquiry Details -->
                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Tender Enquiry Details</legend>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="srch_company_id">Company</label>
                            <?php echo form_dropdown('company_id', ['' => 'Select'] + $company_opt, set_value('company_id'), 'id="srch_company_id" class="form-control select2"'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="srch_customer_id">Customer</label>
                            <?php echo form_dropdown('customer_id', ['' => 'Select'] + $customer_opt, set_value('customer_id'), 'id="srch_customer_id" class="form-control select2"'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Enquiry No</label>
                            <input type="text" name="enquiry_no" id="enquiry_no" class="form-control" placeholder="e.g., TEN-2025-001" value="<?php echo set_value('enquiry_no'); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Enquiry Date</label>
                            <input type="date" name="enquiry_date" id="enquiry_date" class="form-control" value="<?php echo set_value('enquiry_date', date('Y-m-d')); ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Opening Date & Time</label>
                            <input type="datetime-local" name="opening_date" id="opening_date" class="form-control" value="<?php echo set_value('opening_date'); ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Closing Date & Time</label>
                            <input type="datetime-local" name="closing_date" id="closing_date" class="form-control" value="<?php echo set_value('closing_date'); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Status</label>
                            <?php echo form_dropdown('status', ['' => 'Select'] + $status_opt, set_value('status'), 'id="status" class="form-control select2"'); ?>
                        </div>
                    </div>
                </fieldset>

                <!-- Item Details (DIV GRID) -->
                <fieldset class="mt-4">
                    <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>

                    <div id="item_container">
                        <!-- Rows will be injected here -->
                    </div>

                    <button type="button" class="btn btn-primary mt-3" id="add_more">
                        <i class="fa fa-plus"></i> Add More
                    </button>
                </fieldset>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('tender-enquiry-list'); ?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Back To List
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Save
                </button>
            </div>
        </form>
    </div>
</section>



<?php include_once(VIEWPATH . 'inc/footer.php'); ?>

