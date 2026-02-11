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
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-plus-circle"></i> Add Vendor Purchase Bill Entry
            </h3>


            <a href="<?php echo site_url('vendor-pur-inward-list'); ?>" class="btn btn-warning pull-right">
                <i class="fa fa-arrow-left"></i> Back To List
            </a>
        </div>


        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Add" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue">
                        <i class="fa fa-file-text-o"></i> Purchase Bill Details

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
                            <?php echo form_dropdown('srch_company_id',  $company_opt, set_value('srch_company_id'), 'id="srch_company_id" class="form-control" required'); ?>
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
                            <label>Vendor PO No <span style="color:red;">*</span>
                                <span data-toggle="tooltip" title="" class=""
                                    data-original-title="Only when Vendor PO Status is Confirmed then select the PO No. Otherwise leave it blank.">
                                    <i class="text-sm text-info fa fa-info-circle"></i>
                                </span>
                            </label>
                            <?php echo form_dropdown('srch_vendor_po_id', ['' => 'Select PO No'], set_value('srch_vendor_po_id'), 'id="srch_vendor_po_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Contact Person</label>
                            <?php echo form_dropdown('srch_vendor_contact_person_id', ['' => 'Select Contact'] + $vendor_contact_opt, set_value('srch_vendor_contact_person_id'), 'id="srch_vendor_contact_id" class="form-control"'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Invoice Date</label>
                            <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                                value="<?php echo set_value('invoice_date', date('Y-m-d')); ?>" required="true">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Invoice No </label>
                            <input type="text" name="invoice_no" id="invoice_no" class="form-control"
                                placeholder="Enter Invoice No" required="true">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Upload Purchase Bill Document</label>
                            <input type="file" name="purchase_bill_upload" id="purchase_bill_upload"
                                class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active"
                                    <?php echo set_radio('status', 'Active', TRUE); ?>> Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Inactive"
                                    <?php echo set_radio('status', 'Inactive'); ?>> Inactive</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset 
                                style="border:1px solid #081979; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:2px;">
                                <legend class="text-info">For Purchase NBR - VAT Filing </legend>

                                <div class="form-group col-md-6">
                                    <label>VAT Payer Purchase</label>
                                    <?php echo form_dropdown('vat_payer_purchase_grp', $vat_payer_purchase_opt, set_value('vat_payer_purchase_grp'), 'id="vat_payer_purchase_grp" class="form-control"'); ?>
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Declaration Number</label>
                                    <input type="text" name="declaration_no" id="declaration_no" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Declaration Date</label>
                                    <input type="date" name="declaration_date" id="declaration_date"
                                        class="form-control">
                                </div>

                            </fieldset>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea id="editor2" name="remarks" class="form-control"
                                    placeholder="Enter remarks"><?php echo set_value('remarks'); ?></textarea>
                            </div>
                        </div>
                    </div>

                </fieldset>
                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="mt-4">
                            <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>
                            <table class="table table-bordered table-sm table-responsive">
                                <thead>
                                    <tr>
                                        <th style="width:5%;">✔</th>
                                        <th style="width:10%;">Item Code</th>
                                        <th style="width:40%;">Description</th>
                                        <th style="width:10%;">UOM & Qty</th>
                                        <th style="width:10%;">Rate</th>
                                        <th style="width:10%;">VAT %</th>
                                        <th style="width:10%;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="item_container"></tbody>
                            </table>

                            <div class="tot_amt text-right">
                                <label>Total Bill Amount</label>
                                <div id="total_amount_display">0.00</div>
                                <input type="hidden" name="total_amount" id="total_amount">

                                <label>Total GST</label>
                                <div id="total_gst_amount_display">0.00</div>
                                <input type="hidden" name="total_gst_amount" id="total_gst_amount">
                            </div>


                        </fieldset>
                    </div>
                </div>

            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url(''); ?>" class="btn btn-warning pull-left">
                    <i class="fa fa-arrow-left"></i> Back To List
                </a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</section>




<?php include_once(VIEWPATH . 'inc/footer.php'); ?>