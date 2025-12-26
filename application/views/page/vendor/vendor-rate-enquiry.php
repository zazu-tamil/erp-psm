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
             <a href="<?php echo site_url('vendor-rate-enquiry-list'); ?>" class="btn btn-warning pull-right"><i
                        class="fa fa-arrow-left"></i> Back To List</a>
        </div>

        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Add" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i>Vendor Rate Enquiry</legend>
                    <div style="border:1px solid #ddd; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:5px;">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="srch_enq_id">Search Enquiry No</label>
                                <input type="text" name="srch_enq_id" class="form-control srch_enq_id" value="" placeholder="Search Enquiry No" />
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Customer <span class="text-red">*</span></label>
                            <div class="form-group">
                                <?php echo form_dropdown('srch_customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('srch_customer_id'), 'id="srch_customer_id" class="form-control" required'); ?>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="srch_tender_enquiry_id">Tender Enquiry No</label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select Enquiry']  , set_value('srch_tender_enquiry_id'), 'id="srch_tender_enquiry_id" class="form-control"'); ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="srch_vendor_id">Vendor Name <span class="text-red">*</span></label>
                            <div class="input-group">
                                <?php echo form_dropdown('srch_vendor_id', ['' => 'Select'] + $vendor_opt, set_value('srch_vendor_id'), 'id="srch_vendor_id" class="form-control" required'); ?>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-info" data-toggle="modal"
                                        data-target="#add_vendor">Add New</button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="srch_vendor_contact_id">Contact Person</label>
                            <div class="input-group">
                                <?php echo form_dropdown('srch_vendor_contact_id', ['' => 'Select'] + $vendor_contact_opt, set_value('srch_vendor_contact_id'), 'id="srch_vendor_contact_id" class="form-control "'); ?>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-info" data-toggle="modal"
                                        data-target="#add_vendor_contact_pereson">Add New</button>
                                </span>
                            </div>
                        </div>
                        <!-- <div class="form-group col-md-4">
                            <label>Enquiry No</label>
                            <input type="text" name="enquiry_no" id="enquiry_no" class="form-control"
                                placeholder="e.g., TEN-2025-001" value="<?php echo set_value('enquiry_no'); ?>">
                        </div> -->
                        <div class="form-group col-md-4">
                            <label>Enquiry Date <span class="text-red">*</span></label>
                            <input type="date" name="enquiry_date" id="enquiry_date" class="form-control"
                                value="<?php echo set_value('enquiry_date', date('Y-m-d')); ?>" required>
                        </div>
                        <!-- <div class="form-group col-md-4">
                            <label>Opening Date & Time</label>
                            <input type="datetime-local" name="opening_date" id="opening_date" class="form-control"
                                value="<?php echo set_value('opening_date'); ?>">
                        </div> -->
 

                        <div class="form-group col-md-4">
                            <label>Closing Date & Time</label>
                            <input type="datetime-local" name="closing_date" id="closing_date" class="form-control"
                                value="<?php echo set_value('closing_date'); ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Enquiry Status <span class="text-red">*</span></label><br>
                            <?php echo form_dropdown('vendor_rate_enquiry_status', ['' => 'Select'] + $vendor_RFQ_opt,'', 'id="vendor_rate_enquiry_status" class="form-control" required="true"'); ?> 
                        </div>
                        <div class="form-group col-md-4">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active" checked>
                                Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="InActive">
                                InActive</label>
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
                         
                    </div>


                    <div id="item_container"></div>
                </fieldset>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('vendor-rate-enquiry-list'); ?>" class="btn btn-warning pull-left"><i
                        class="fa fa-arrow-left"></i> Back To List</a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</section>

<div class="modal fade" id="add_vendor" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" action="" id="frmadd_Vendor" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Add Vendor</h3>
                    <input type="hidden" name="mode" value="Add Vendor" />
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Vendor Name</label>
                            <input class="form-control" type="text" name="vendor_name" id="vendor_name"
                                placeholder="Vendor Name">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Country <span class="text-danger">*</span></label>
                                <?php echo form_dropdown('country', ['' => 'Select Country'] + $country_opt, set_value('country'), 'id="country" class="form-control"'); ?>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>VAT No</label>
                            <input class="form-control" type="text" name="gst" id="gst" placeholder="VAT No">
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" name="address" id="address" placeholder="Address"
                                    required="true" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Remarks</label>
                            <textarea name="remarks" id="remarks" rows="4" class="form-control"></textarea>
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
                    <input type="submit" name="btn_add_vendor" value="Save" class="btn btn-primary" />
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="add_vendor_contact_pereson">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="" id="frmadd_contact_person" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Add Contact Person</h3>
                    <!-- CHANGED: mode value from 'Add' to 'Add Contact Person' -->
                    <input type="hidden" name="mode" value="Add Contact Person" />
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Vendor <span class="text-danger">*</span></label>
                            <?php echo form_dropdown('vendor_id', $vendor_opt, '', 'id="contact_vendor_id" class="form-control" required'); ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Contact Person Name <span class="text-danger">*</span></label>
                            <input type="text" name="contact_person_name" id="contact_person_name" class="form-control"
                                placeholder="Name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Department</label>
                            <input type="text" name="department" id="contact_department" class="form-control"
                                placeholder="Department">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Designation</label>
                            <input type="text" name="designation" id="contact_designation" class="form-control"
                                placeholder="Designation">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Mobile</label>
                            <input type="text" name="mobile" id="contact_mobile" class="form-control"
                                placeholder="Mobile">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Email</label>
                            <input type="email" name="email" id="contact_email" class="form-control"
                                placeholder="Email">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Status</label><br>
                            <label><input type="radio" name="status" value="Active" checked> Active</label>
                            <label class="ml-3"><input type="radio" name="status" value="InActive"> InActive</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" id="contact_address" class="form-control" rows="3"
                            placeholder="Full Address"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <input type="button" name="btn_add_contact" value="Save" class="btn btn-primary" />
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>