<?php include_once(VIEWPATH . 'inc/header.php'); 
// echo '<pre>';
// print_r($_FILES);
// echo '</pre>'; 
?>

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
            <h3 class="box-title"><i class="fa fa-plus-circle"></i> Add Vendor Quotation</h3>
            <a href="<?php echo site_url('vendor-quotation-list'); ?>" class="btn btn-warning pull-right">
                <i class="fa fa-arrow-left"></i> Back To List
            </a>
        </div>

        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Add" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Quotation Details</legend>

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
                            <label>Company <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_company_id',   $company_opt, set_value('srch_company_id'), 'id="srch_company_id" class="form-control" required'); ?>
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
                            <label>Vendor Rate Enquiry No <span style="color:red;">*</span>
                                <span data-toggle="tooltip" title="" class=""
                                    data-original-title="Only when Vendor Rate Enquiry Status is Quotation Received then select the Enquiry No. Otherwise leave it blank.">
                                    <i class="text-sm text-info fa fa-info-circle"></i>
                                </span>
                            </label>
                            <?php echo form_dropdown('srch_vendor_rate_enquiry_id', ['' => 'Select Enquiry No'], set_value('srch_vendor_rate_enquiry_id'), 'id="srch_vendor_rate_enquiry_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Contact Person</label>
                            <?php echo form_dropdown('srch_vendor_contact_person_id', ['' => 'Select Contact'] + $vendor_contact_opt, set_value('srch_vendor_contact_person_id'), 'id="srch_vendor_contact_id" class="form-control"'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Quotation Date</label>
                            <input type="date" name="quote_date" id="quote_date" class="form-control"
                                value="<?php echo set_value('quote_date', date('Y-m-d')); ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Quotation No <span style="color:red;">*</span></label>
                            <input type="text" name="quote_no" id="quote_no" class="form-control"
                                placeholder="Enter Quotation No" required="true">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Quotation Status <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('quote_status', ['' => 'Select Status'] + $quotation_status_opt, set_value('quote_status'), 'id="quote_status" class="form-control" required="true"'); ?>
                        </div>

                        <!-- <div class="form-group col-md-4">
                            <label>Handling Charges</label>
                            <input type="number" step="0.01" name="handling_charges" id="handling_charges"
                                class="form-control" value="<?php echo set_value('handling_charges'); ?>"
                                placeholder="0.00">
                        </div> -->
                        <!-- image upload  only--->
                        <div class="form-group col-md-4">
                            <label>Upload Quotation Document</label>
                            <input type="file" name="quote_doc_upload" id="quote_doc_upload" class="form-control">
                        </div>

                        <div class="col-md-4">
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

                        <div class="form-group col-md-4">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active"
                                    <?php echo set_radio('status', 'Active', TRUE); ?>> Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Inactive"
                                    <?php echo set_radio('status', 'Inactive'); ?>> Inactive</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea id="editor1" name="remarks" class="form-control"
                                    placeholder="Enter notes"><?php echo set_value('remarks'); ?></textarea>
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

                    <div class="cls_export"
                        style="border:2px solid green; padding:10px; margin-bottom:15px; border-radius:10px; ">
                        <div class="row">
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
                    </div>




                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th style="width:5%;">✔</th>
                                <th style="width:5%;">Item Code</th>
                                <th style="width:30%;">Description</th>
                                <th style="width:15%;" class="text-right">UOM & QTY</th>
                                <th style="width:5%;"  class="text-right">Rate</th>
                                <th style="width:5%;"  class="text-right">VAT %</th>
                                <th style="width:10%;" class="text-right">Amount</th>
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
                <a href="<?php echo site_url('vendor-quotation-list'); ?>" class="btn btn-warning pull-left">
                    <i class="fa fa-arrow-left"></i> Back To List
                </a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</section>




<?php include_once(VIEWPATH . 'inc/footer.php'); ?>