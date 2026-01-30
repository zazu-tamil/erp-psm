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
                            <?php echo form_dropdown('company_id',$company_opt, set_value('company_id'), 'id="srch_company_id" class="form-control select2" required'); ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Customer <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <?php echo form_dropdown('customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('customer_id'), 'id="srch_customer_id" class="form-control" required'); ?>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-info" data-toggle="modal"
                                        data-target="#add_customer">Add New</button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Customer Contact Person </label>
                            <div class="input-group">
                                <?php
                                echo form_dropdown(
                                    'customer_contact_id',
                                    ['' => 'Select Contact Person'],
                                    set_value('customer_contact_id'),
                                    'id="srch_customer_contact_id" class="form-control" '
                                );
                                ?>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-info" data-toggle="modal"
                                        data-target="#add_customer_contact_id">Add New</button>
                                </span>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label>RFQ No</label>
                            <input type="text" name="enquiry_no" class="form-control" placeholder="e.g., TEN-2025-001"
                                value="<?php echo set_value('enquiry_no'); ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Tender Name</label>
                            <input type="text" name="tender_name" class="form-control" placeholder="tender_name"
                                value="<?php echo set_value('tender_name'); ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Enquiry Date</label>
                            <input type="date" name="enquiry_date" class="form-control"
                                value="<?php echo set_value('enquiry_date', date('Y-m-d')); ?>">
                        </div>
                        <!-- <div class="form-group col-md-4">
                            <label>Opening Date & Time</label>
                            <input type="datetime-local" name="opening_date" class="form-control"
                                value="<?php echo set_value('opening_date'); ?>">
                        </div> -->
                        <div class="form-group col-md-4">
                            <label>Closing Date & Time</label>
                            <input type="datetime-local" name="closing_date" class="form-control"
                                value="<?php echo set_value('closing_date'); ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Tender Enquiry Status <span class="text-danger">*</span></label>
                            <?php echo form_dropdown('tender_status', ['' => 'Select'] + $tender_status_opt, set_value('tender_status', 'Active'), 'class="form-control" required'); ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Tender Document</label>
                            <input type="file" name="tender_document" id="tender_document" class="form-control">
                            <small class="text-muted">All file types allowed (Max 10MB)</small>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Status</label> <br>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="status" value="Active" checked="true" /> Active
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="status" value="InActive" /> InActive
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- Item Details - DIV + GRID -->
                <fieldset class="mt-4">

                    <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>

                    <div class="row">
                        <div class="col-md-3 form-group">

                            <label for="">Search Item & Load Item</label> <br>
                            <button type="button" class="btn btn-info" data-toggle="modal"
                                data-target="#select_item_modal">
                                <i class="fa fa-search"></i> Search Item & Load Item
                            </button>


                        </div>
                        <div class="col-md-5 form-group ">
                            <label for="">Download Sample Excel File To Import</label><br>
                            <a href="<?php echo base_url('asset/tender-enquiry-items-sample.xlsx'); ?>"
                                class="btn btn-warning" download>
                                <i class="fa fa-download"></i> Download Sample Excel File To Import
                            </a>
                        </div>
                        <div class="col-md-4 form-group ">
                            <label for="excelFile">Choose Excel File to Import</label>
                            <input type="file" class="form-control" id="excelFile" accept=".xls,.xlsx"
                                placeholder="Choose Excel File to Import">
                        </div>
                    </div>



                    <div class="item-details-container">

                        <!-- Header -->
                        <div class="grid-header">
                            <div style="width:10%;">Item Code </div>
                            <div>Description</div>
                            <div>UOM</div>
                            <div>Qty</div>
                            <div class="text-center">Action</div>
                        </div>

                        <!-- Dynamic Rows -->
                        <div id="item_rows"></div>
                        <div id="item_rows_add">
                            
                        </div>

                        <button type="button" class="btn btn-primary" id="add_more_added" style="margin-top:10px;">
                            <i class="fa fa-plus"></i> Add More Item
                        </button>

                    </div>


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


<div class="modal fade" id="add_customer_contact_id" role="dialog" aria-labelledby="scrollmodalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" action="" id="frmadd_Customer_Contact" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Add Customer Contact</h3>
                    <input type="hidden" name="mode" value="Add Customer Contact" />
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Customer Name<span class="text-danger">*</span></label>
                            <select id="customer_id_two" name="customer_id" class="form-control" required="true">
                                <option value="">Select Customer</option>
                                <?php foreach ($customer_opt as $key => $val) { ?>
                                    <option value="<?= $key ?>"><?= $val ?></option>
                                <?php } ?>
                            </select>

                        </div>


                        <div class="form-group col-md-6">
                            <label>Contact Person Name <span class="text-danger">*</span></label>
                            <input type="text" name="contact_person_name" id="contact_person_name" class="form-control"
                                placeholder="Name" required="true">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Department</label>
                            <input type="text" name="department" class="form-control" placeholder="Department">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Designation</label>
                            <input type="text" name="designation" class="form-control" placeholder="Designation">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Mobile</label>
                            <input type="text" name="mobile" class="form-control" placeholder="Mobile">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Status</label><br>
                            <label><input type="radio" name="status" value="Active" checked> Active</label>
                            <label class="ml-3"><input type="radio" name="status" value="InActive"> InActive</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Full Address"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <input type="submit" name="btn_add_customer_contact" value="Save" class="btn btn-primary" />
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .text-danger {
        color: red !important;
    }
</style>

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
                            <label>Customer Name <span class="text-danger">*</span></label>
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
                                <label for="address">Address</label>
                                <textarea class="form-control" name="address" id="address" placeholder="Address"
                                    rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Country</label>
                                <?php echo form_dropdown('country', ['' => 'Select Country'] + $country_opt, set_value('country'), 'id="country" class="form-control"'); ?>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Mobile</label>
                            <input class="form-control" type="text" name="mobile" id="mobile" placeholder="Mobile">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Alternate Mobile</label>
                            <input class="form-control" type="text" name="mobile_alt" id="mobile_alt"
                                placeholder="Alternate Mobile">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input class="form-control" type="email" name="email" id="email" placeholder="Email">
                        </div>
                    </div>


                    <div class="row mt-3">
                        <div class="form-group col-md-6">
                            <label>Status</label><br>
                            <label><input type="radio" name="status" value="Active" checked> Active</label>
                            <label class="ml-3"><input type="radio" name="status" value="InActive">
                                InActive</label>
                        </div>

                        <div class="form-group col-md-6">
                            <label>VAT No</label>
                            <input class="form-control" type="text" name="gst" id="gst" placeholder="VAT No">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="form-group col-md-12">
                            <label>Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="3"
                                placeholder="Enter your remarks"></textarea>
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
<div class="modal fade" id="add_category" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="" id="frmadd_Category" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Add Category</h3>
                    <input type="hidden" name="mode" value="Add Category" />
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Category Name<span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="category_name" id="category_name" value=""
                                placeholder="Category Name" required="true">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Status</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="status" value="Active" checked="true" />
                                    Active
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="status" value="InActive" /> InActive
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <input type="submit" name="btn_add_category" value="Save" class="btn btn-primary" />
                </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade" id="select_item_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" action="" id="frm_item_modal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Items List</h3>
                    <input type="hidden" name="mode" value="Select items" />
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="item_search_modal">Search Item Code</label>
                            <input type="text" id="item_search_modal" class="form-control"
                                placeholder="Search Item Code">
                        </div>
                        <div class="col-md-6">
                            <label for="item_desc_modal">Search Item Desc</label>
                            <input type="text" id="item_desc_modal" class="form-control" placeholder="Search Item Desc">
                        </div>
                    </div>
                    <table class="table table-bordered  table-striped mt-3" id="item_search_table_modal">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th width="50%">Item Desc</th>
                                <th>UOM</th>
                                <th>Table</th>
                                <th>Date</th>
                                <th>Select</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn_cancel" data-dismiss="modal">Cancel</button>
                    <!-- <input type="button" name="btn_add_brand" value="Save" class="btn btn-primary" /> -->
                </div>
            </form>
        </div>
    </div>
</div>




<?php include_once(VIEWPATH . 'inc/footer.php'); ?>