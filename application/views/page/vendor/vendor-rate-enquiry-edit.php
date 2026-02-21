<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Tender</a></li>
        <li class="active">Edit <?php echo htmlspecialchars($title); ?></li>
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
            <h3 class="box-title">Edit Vendor Rate Enquiry</h3>
            <a href="<?php echo site_url('vendor-rate-enquiry-list'); ?>" class="btn btn-warning pull-right"> <i
                    class="fa fa-arrow-left"></i> Back to list</a>
        </div>
        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Edit" />
                <input type="hidden" name="old_tender_enquiry_id" id="old_tender_enquiry_id"
                    value="<?php echo $main['tender_enquiry_id']; ?>" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue">Vendor Rate Enquiry</legend>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="srch_customer_id">Customer <span class="text-red">*</span></label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select'] + $customer_opt, $main['customer_id'], 'id="srch_customer_id" class="form-control readonly-select" required '); ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="srch_tender_enquiry_id">Tender Enquiry No</label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select'] + $tender_enquiry_opt, $main['tender_enquiry_id'], 'id="srch_tender_enquiry_id" class="form-control readonly-select" '); ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="srch_vendor_id">Vendor Name <span class="text-red">*</span></label>
                            <?php echo form_dropdown('srch_vendor_id', ['' => 'Select'] + $vendor_opt, $main['vendor_id'], 'id="srch_vendor_id" class="form-control" required'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="srch_vendor_contact_id">Contact Person</label>
                            <?php echo form_dropdown('srch_vendor_contact_id', ['' => 'Select'] + $vendor_contact_opt, $main['vendor_contact_person_id'], 'id="srch_vendor_contact_id" class="form-control"'); ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Enquiry No</label>
                            <input type="text" name="enquiry_no" id="enquiry_no" class="form-control"
                                value="<?php echo $main['enquiry_no']; ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Enquiry Date <span class="text-red">*</span></label>
                            <input type="date" name="enquiry_date" id="enquiry_date" class="form-control"
                                value="<?php echo date('Y-m-d', strtotime($main['enquiry_date'])); ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <!-- <div class="form-group col-md-4">
                            <label>Opening Date & Time</label>
                            <input type="datetime-local" name="opening_date" id="opening_date" class="form-control"
                                value="<?php //echo date('Y-m-d\TH:i', strtotime($main['opening_date'])); ?>">
                        </div> -->
                        <div class="form-group col-md-4">
                            <label>Closing Date & Time</label>
                            <input type="datetime-local" name="closing_date" id="closing_date" class="form-control"
                                value="<?php echo date('Y-m-d\TH:i', strtotime($main['closing_date'])); ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Enquiry Status <span class="text-red">*</span></label><br>
                            <?php echo form_dropdown('vendor_rate_enquiry_status', ['' => 'Select'] + $vendor_RFQ_opt, $main['vendor_rate_enquiry_status'], 'id="vendor_rate_enquiry_status" class="form-control" required="true"'); ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Status</label><br>
                            <label class="radio-inline">
                                <input type="radio" name="status" value="Active"
                                    <?php echo ($main['status'] == 'Active') ? 'checked' : ''; ?>> Active
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="status" value="InActive"
                                    <?php echo ($main['status'] == 'InActive') ? 'checked' : ''; ?>> InActive
                            </label>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="mt-4">
                    <legend class="text-light-blue">Item Details</legend>
                    <div style="border:2px solid green; padding:10px; margin-bottom:15px; border-radius:10px; ">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="btnExport">Click Here - Export as Excel File</label> <br>
                                <button id="btnExport" type="button" class="btn btn-success"
                                    value="Vendor-ENQ-<?php echo htmlspecialchars($main['enquiry_no']); ?>">Export Excel
                                    & Download</button>
                            </div>
                            <div class="col-md-4 form-group ">
                                <label for="excelFile">Choose Excel File to Import</label>
                                <input type="file" class="form-control" id="excelFile" accept=".xls,.xlsx"
                                    placeholder="Choose Excel File to Import">
                            </div>
                            <!-- <div class="col-md-4 form-group ">
                            <i class="text-red">Note: <br>Don't change <b class="text-info">[ tender_po_item_id , tender_quotation_item_id ] </b> column its software referance Ids in excel file Whlie importing</i>
                            </div>      -->
                        </div>
                    </div>

                    <div id="item_container">
                        <?php foreach ($item_rows as $i => $row): ?>
                        <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
                            <h5 class="text-primary mb-3">Item Details <?php echo $i + 1; ?></h5>


                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group" style="margin-top: 25px;">
                                        <div class="form-check">
                                            <input type="checkbox"name="selected_items[]"value="<?= $i ?>"<?= !empty($row['vendor_rate_enquiry_item_id']) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="check_<?php echo $i; ?>"
                                                style="margin-left: 5px;">Select</label>
                                        </div>
                                        <input type="hidden" name="vendor_rate_enquiry_item_id[]" value="<?php echo $row['vendor_rate_enquiry_item_id']; ?>" id="item_<?php echo $row['vendor_rate_enquiry_item_id']; ?>">
                                        <input type="hidden" name="tender_enquiry_item_id[]"  value="<?php echo $row['tender_enquiry_item_id']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Item Code</label>
                                        <input type="text" class="form-control item_code" name="item_code[]"  value="<?php echo $row['item_code']; ?>">
                                    </div>
                                </div>
                                <!-- <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Category Name</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php //echo $row['category_name']; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Item Name</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php //echo $row['item_name']; ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Item Description</label>
                                        <textarea name="item_desc[]" class="form-control desc-textarea"
                                            rows="3"><?php echo $row['item_desc']; ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>UOM</label>
                                                <input type="text" name="uom[]" class="form-control"
                                                    value="<?php echo $row['uom']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Quantity</label>
                                                <input type="number" step="any" name="qty[]"
                                                    class="form-control qty-input" value="<?php echo $row['qty']; ?>">
                                            </div>
                                        </div>

                                        <!-- <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Rate</label>
                                                    <input type="number" step="any" name="rate[]"
                                                        class="form-control rate-input" value="<?php echo $row['rate']; ?>">
                                                </div>
                                            </div> -->
                                        <!-- <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>VAT %</label>
                                                    <select name="gst[]" class="form-control vat-dropdown">
                                                        <option value="">Select</option>
                                                        <?php /*if (isset($gst_opt)): ?>
                                                            <?php foreach ($gst_opt as $gst_id => $gst_pct): ?>
                                                                <option value="<?php echo $gst_pct; ?>" <?php echo ($row['gst'] == $gst_pct) ? 'selected' : ''; ?>>
                                                                    <?php echo $gst_pct; ?>%</option>
                                                            <?php endforeach; ?>
                                                        <?php endif; */?>
                                                    </select>
                                                </div>
                                            </div> -->
                                        <!-- <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Amount (Qty × Rate × VAT %)</label>
                                                    <input type="number" step="any" name="amount[]"
                                                        class="form-control amount-input"
                                                        value="<?php //echo $row['amount']; ?>" readonly>
                                                </div>
                                            </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-info" style="background-color:#f0f8ff; border: 2px solid #007bff;">
                                <div class="row">
                                    <div class="col-md-10 text-right">
                                        <h4 style="margin: 0;"><strong>Grand Total:</strong></h4>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" step="any" id="grand_total" class="form-control"
                                            value="0.00" readonly
                                            style="font-weight: bold; font-size: 16px; text-align: center;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </fieldset>
            </div>
            <div class="box-footer text-right">

                <a href="<?php echo site_url('vendor-rate-enquiry-list'); ?>" class="btn btn-warning pull-left"> <i
                        class="fa fa-arrow-left"></i> Back to list</a>
                <button type="submit" class="btn btn-success">
                    Update
                </button>
            </div>
        </form>
    </div>
</section>
<?php include_once(VIEWPATH . 'inc/footer.php'); ?>