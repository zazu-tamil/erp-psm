<?php include_once(VIEWPATH . 'inc/header.php');
// echo '<pre>';
// print_r($_FILES);
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
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible auto-hide">
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible auto-hide">
            <?php echo $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-plus-circle"></i> Add Vendor PO
            </h3>


            <a href="<?php echo site_url('vendor-po-list'); ?>" class="btn btn-warning pull-right">
                <i class="fa fa-arrow-left"></i> Back To List
            </a>
        </div>


        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Add" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue">
                        <i class="fa fa-file-text-o"></i> PO Details
                        &nbsp;&nbsp;&nbsp;
                        <small style="color:red !important;">
                            PO can be generated only when the Vendor Quotation status is <strong>Confirmed</strong>.
                        </small>
                    </legend>

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
                        <div class="form-group col-md-4">
                            <label>Company <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_company_id', ['' => 'Select Company'] + $company_opt, set_value('srch_company_id'), 'id="srch_company_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Customer <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('srch_customer_id'), 'id="srch_customer_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Tender Enquiry No <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select Enquiry'], set_value('srch_tender_enquiry_id'), 'id="srch_tender_enquiry_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Vendor Name <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_vendor_id', ['' => 'Select Vendor'], set_value('srch_vendor_id'), 'id="srch_vendor_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Vendor Quotation No <span style="color:red;">*</span>
                                <span data-toggle="tooltip" title="" class=""
                                    data-original-title="Only when Vendor Quotation Status is Confirmed then select the Quotation No. Otherwise leave it blank.">
                                    <i class="text-sm text-info fa fa-info-circle"></i>
                                </span>
                            </label>
                            <?php echo form_dropdown('srch_vendor_quote_id', ['' => 'Select Enquiry No'], set_value('srch_vendor_quote_id'), 'id="srch_vendor_quote_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Contact Person</label>
                            <?php echo form_dropdown('srch_vendor_contact_person_id', ['' => 'Select Contact'] + $vendor_contact_opt, set_value('srch_vendor_contact_person_id'), 'id="srch_vendor_contact_id" class="form-control"'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>PO Date</label>
                            <input type="date" name="po_date" id="po_date" class="form-control"
                                value="<?php echo set_value('po_date', date('Y-m-d')); ?>">
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="delivery_date">Delivery Date</label>
                                <input type="date" name="delivery_date" id="delivery_date" class="form-control"
                                    value="<?php echo set_value('delivery_date'); ?>">
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label>PO Status <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('po_status', ['' => 'Select Status'] + $po_status_opt, set_value('po_status'), 'id="po_status" class="form-control" required="true"'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Transport Charges</label>
                            <input type="number" step="0.01" name="transport_charges" id="transport_charges"
                                class="form-control" value="<?php echo set_value('transport_charges'); ?>"
                                placeholder="0.00">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Other Charges</label>
                            <input type="number" step="0.01" name="other_charges" id="other_charges"
                                class="form-control" value="<?php echo set_value('other_charges'); ?>"
                                placeholder="0.00">
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="currency_id">Select Currency</label>
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


                        <div class="form-group col-md-4">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active" <?php echo set_radio('status', 'Active', TRUE); ?>> Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Inactive" <?php echo set_radio('status', 'Inactive'); ?>> Inactive</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea id="editor1" name="remarks" class="form-control"
                                    placeholder="Enter remarks"><?php echo set_value('remarks'); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Terms & Conditions</label>
                                <textarea id="editor2" name="terms" class="form-control"
                                    placeholder="Enter terms"><?php echo set_value('terms'); ?></textarea>
                            </div>
                        </div>
                    </div>



                </fieldset>

                <fieldset class="mt-4">
                    <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th style="width:5%;">✔</th>
                                <th style="width:10%;">Item Code</th>
                                <th style="width:40%;">Description</th>
                                <th style="width:10%;">UOM & Qty </th>
                                <th style="width:10%;">Rate</th>
                                <th style="width:10%;">VAT %</th>
                                <th style="width:10%;">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="item_container"></tbody>
                        <thead>
                            <tr>
                                <th colspan="6" class="text-right">Total</th>
                                <th class="text-right">
                                    <span class="value"> <span id="total_amount">0.00</span></span>
                                </th>
                            </tr>
                        </thead>
                    </table>

                </fieldset>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('vendor-po-list'); ?>" class="btn btn-warning pull-left">
                    <i class="fa fa-arrow-left"></i> Back To List
                </a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</section>




<?php include_once(VIEWPATH . 'inc/footer.php'); ?>