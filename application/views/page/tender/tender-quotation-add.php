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
            <div class="pull-right"><a href="<?php echo site_url('tender-quotation-list'); ?>"
                    class="btn btn-default"><i class="fa fa-arrow-left"></i> Back To List</a></div>
        </div>

        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">

                <input type="hidden" name="mode" value="Add" />

                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Tender Quotation</legend>
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
                            <?php echo form_dropdown('srch_company_id', ['' => 'Select Company'] + $company_opt, set_value('srch_company_id'), 'id="srch_company_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_customer_id">Customer <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('srch_customer_id'), 'id="srch_customer_id" class="form-control" required '); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="srch_tender_enquiry_id">Tender Enquiry No <span
                                    style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select Enquiry'], set_value('srch_tender_enquiry_id'), 'id="srch_tender_enquiry_id" class="form-control" required'); ?>
                        </div>

                        <!-- <div class="form-group col-md-3">
                            <label>Quotation No</label>
                            <input type="text" name="quotation_no" id="quotation_no" class="form-control"
                                placeholder="e.g., TEN-2025-001" value="<?php echo set_value('quotation_no'); ?>">
                        </div> -->

                        <div class="form-group col-md-3">
                            <label>Tender Ref No</label>
                            <input type="text" name="tender_ref_no" id="tender_ref_no" class="form-control"
                                placeholder="e.g., TEN-2025-001" value="<?php echo set_value('tender_ref_no'); ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Quotation Date</label>
                            <input type="date" name="quote_date" id="quote_date" class="form-control"
                                value="<?php echo set_value('quote_date', date('Y-m-d')); ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Quotation Status</label><br>
                            <label class="radio-inline"><input type="radio" name="quotation_status" value="Open"
                                    checked>Open</label>
                            <label class="radio-inline"><input type="radio" name="quotation_status" value="Quoted">
                                Quoted</label>
                            <label class="radio-inline"><input type="radio" name="quotation_status" value="Won">
                                Won</label>
                            <label class="radio-inline"><input type="radio" name="quotation_status" value="Lost">
                                Lost</label>
                            <label class="radio-inline"><input type="radio" name="quotation_status" value="On Hold"> On
                                Hold</label>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active" checked>
                                Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Inactive">
                                Inactive</label>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="transport_charges">Transport Charges</label>
                            <input type="number" step="0.01" name="transport_charges" id="transport_charges"
                                class="form-control" placeholder="Enter transport charges"
                                value="<?php echo set_value('transport_charges', '0.00'); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="other_charges">Other Charges</label>
                            <input type="number" step="0.01" name="other_charges" id="other_charges"
                                class="form-control" placeholder="Enter other charges"
                                value="<?php echo set_value('other_charges', '0.00'); ?>">
                        </div>

                        <div class="col-md-3">
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

                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="remarks">Note</label>
                            <textarea name="remarks" class="form-control" id="editor2" placeholder="Enter your remarks"
                                rows="8"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Quotation Terms</label>
                                <textarea id="editor1" name="terms" class="form-control custom-textarea"
                                    placeholder="Enter quotation terms" required></textarea>
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
                            <i class="text-red">Note: <br>Don't change <b class="text-info">[ tender_enquiry_item_id ,
                                    enquiry_no ] </b> column its software referance Ids in excel file Whlie
                                importing</i>
                        </div>
                    </div>
                    <div id="item_container"></div>
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

    <div class="box-footer">
        <div class="row">
            <div class="col-md-6 text-left">
                <a href="<?php echo site_url('tender-quotation-list'); ?>" class="btn btn-default"><i
                        class="fa fa-arrow-left"></i> Back To List</a>
            </div>
            <div class="col-md-6 text-right"><button type="submit" class="btn btn-success"><i class="fa fa-save"></i>
                    Save</button></div>
        </div>
    </div>
    </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>