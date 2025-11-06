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
                            <label>Company <span class="text-danger">*</span></label>
                            <?php echo form_dropdown('company_id', ['' => 'Select Company'] + $company_opt, set_value('company_id'), 'id="srch_company_id" class="form-control select2" required'); ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Customer</label>
                            <div class="input-group">
                                <?php echo form_dropdown('customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('customer_id'), 'id="srch_customer_id" class="form-control"'); ?>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-info" data-toggle="modal"
                                        data-target="#add_customer">Add New</button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Enquiry No</label>
                            <input type="text" name="enquiry_no" class="form-control" placeholder="e.g., TEN-2025-001"
                                value="<?php echo set_value('enquiry_no'); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Enquiry Date</label>
                            <input type="date" name="enquiry_date" class="form-control"
                                value="<?php echo set_value('enquiry_date', date('Y-m-d')); ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Opening Date & Time</label>
                            <input type="datetime-local" name="opening_date" class="form-control"
                                value="<?php echo set_value('opening_date'); ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Closing Date & Time</label>
                            <input type="datetime-local" name="closing_date" class="form-control"
                                value="<?php echo set_value('closing_date'); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Status</label>
                            <?php echo form_dropdown('status', ['' => 'Select'] + $status_opt, set_value('status', 'Active'), 'class="form-control select2"'); ?>
                        </div>
                    </div>
                </fieldset>

                <!-- Item Details - DIV + GRID -->
                <fieldset class="mt-4">
                    <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>

                    <div class="item-details-container">
                        <!-- Header -->
                        <div class="grid-header">
                            <div>Category / Item</div>
                            <div>Description</div>
                            <div>UOM</div>
                            <div>Qty</div>
                            <div class="text-center">Action</div>
                        </div>

                        <!-- Dynamic Rows -->
                        <div id="item_rows"></div>
                    </div>

                    <button type="button" class="btn btn-primary mt-" id="add_more" style="margin-top: 10px;">
                        <i class="fa fa-plus"></i> Add More Item
                    </button>
                </fieldset>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('tender-enquiry-list'); ?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Save Enquiry
                </button>
            </div>
        </form>
    </div>
</section>


<div class="modal fade" id="add_customer" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" action="" id="frmadd_Customer" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Add Customer</h3>
                    <input type="hidden" name="mode" value="Add Customer" />
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Customer Name</label>
                            <input class="form-control" type="text" name="customer_name" id="customer_name"
                                placeholder="Customer Name">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Contact Name</label>
                            <input class="form-control" type="text" name="contact_name" id="contact_name"
                                placeholder="Contact Person">
                        </div>
                        <div class="form-group col-md-4">
                            <label>CR No</label>
                            <input class="form-control" type="text" name="crno" id="crno"
                                placeholder="Commercial Registration No">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Country</label>
                                <?php echo form_dropdown('country', ['' => 'Select Country'] + $country_opt, set_value('country'), 'id="country" class="form-control" required'); ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" name="address" id="address" placeholder="Address"
                                    required="true" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Mobile</label>
                            <input class="form-control" type="text" name="mobile" id="mobile" placeholder="Mobile">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Alternate Mobile</label>
                            <input class="form-control" type="text" name="mobile_alt" id="mobile_alt"
                                placeholder="Alternate Mobile">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Email</label>
                            <input class="form-control" type="email" name="email" id="email" placeholder="Email">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>VAT No</label>
                            <input class="form-control" type="text" name="gst" id="gst" placeholder="VAT No">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Remarks</label>
                            <input type="text" name="remarks" id="remarks" class="form-control"
                                placeholder="Enter remarks">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Latitude</label>
                            <input class="form-control" type="text" name="latitude" id="latitude"
                                placeholder="Latitude">
                        </div>
                        <div class="col-md-6">
                            <label>Longitude</label>
                            <input class="form-control" type="text" name="longitude" id="longitude"
                                placeholder="Longitude">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Google Map Location</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="google_map_location"
                                        id="google_map_location" placeholder="Google Map Location URL">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-primary" id="get-location-btn">
                                            <i class="fa fa-map-marker"></i> Capture Location
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row mt-3">
                        <div class="form-group col-md-6">
                            <label>Status</label><br>
                            <label><input type="radio" name="status" value="Active" checked> Active</label>
                            <label class="ml-3"><input type="radio" name="status" value="InActive">
                                InActive</label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <input type="submit" name="btn_add_customer" value="Save" class="btn btn-primary" />
                </div>
            </form>
        </div>
    </div>
</div>



<?php include_once(VIEWPATH . 'inc/footer.php'); ?>