<?php include_once(VIEWPATH . 'inc/header.php'); 

// echo '<pre>';
// print_r($header);
// echo '</pre>';
?>

<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Tender</a></li>
        <li class="active">Edit Vendor Quotation</li>
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
            <h3 class="box-title"><i class="fa fa-edit"></i> Edit Vendor Quotation</h3>
            <a href="<?php echo site_url('vendor-quotation-list'); ?>" class="btn btn-warning pull-right">
                <i class="fa fa-arrow-left"></i> Back To List
            </a>
        </div>

        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Edit" />
                <input type="hidden" name="vendor_quote_id" value="<?php echo $header['vendor_quote_id']; ?>" />

                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Quotation Details</legend>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Company <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_company_id', $company_opt, $header['company_id'], 'id="srch_company_id" class="form-control readonly-select" required'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Customer <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_customer_id', $customer_opt, $header['customer_id'], 'id="srch_customer_id" class="form-control readonly-select" required readonly'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Tender Enquiry No <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', $tender_enquiry_opt, $header['tender_enquiry_id'], 'id="srch_tender_enquiry_id" class="form-control readonly-selects" required readonly'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Vendor Name <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_vendor_id', $vendor_opt, $header['vendor_id'], 'id="srch_vendor_id" class="form-control readonly-select" required readonly'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Vendor Rate Enquiry No <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_vendor_rate_enquiry_id', $vendor_rate_enquiry_opt, $header['vendor_rate_enquiry_id'], 'id="srch_vendor_rate_enquiry_id" class="form-control readonly-select" required disabled'); ?>
                            <input type="hidden" name="vendor_rate_enquiry_id" value="<?php echo $header['vendor_rate_enquiry_id']; ?>" id="vendor_rate_enquiry_id">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Contact Person</label>
                            <?php echo form_dropdown('srch_vendor_contact_person_id', $vendor_contact_opt, $header['vendor_contact_person_id'], 'id="srch_vendor_contact_id" class="form-control readonly-select" readonly'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Quotation Date <span style="color:red;">*</span></label>
                            <input type="date" name="quote_date" id="quote_date" class="form-control"
                                value="<?php echo set_value('quote_date', $header['quote_date']); ?>" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Quotation No <span style="color:red;">*</span></label>
                            <input type="text" name="quote_no" id="quote_no" class="form-control"
                                value="<?php echo set_value('quote_no', $header['quote_no']); ?>"
                                placeholder="Enter Quotation No" required="true">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Quotation Status <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('quote_status', $quotation_status_opt, $header['quote_status'], 'id="quote_status" class="form-control" required'); ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Upload Quotation Document</label>

                            <div class="input-group">

                                <input type="file" class="form-control" id="quote_doc_upload" name="quote_doc_upload">
                                <span class="input-group-btn">
                                    <span class="btn btn-info btn-file">
                                        <?php if (!empty($header['quote_doc_upload'])): ?>
                                        <a href="<?php echo base_url($header['quote_doc_upload']); ?>" target="_blank"
                                            style="margin-top:6px; color:#fff;">
                                            View Document
                                        </a>
                                        <?php endif; ?>
                                    </span>
                                </span>
                            </div>


                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="currency_id">Currency</label>
                                <?php 
                                    echo form_dropdown(
                                        'currency_id',
                                        ['' => 'Select Currency'] + $currency_opt, 
                                        set_value('currency_id', $header['currency_id']),
                                        'id="currency_id" class="form-control" required'
                                    );
                                ?>  
                            </div>
                        </div>


                        <div class="form-group col-md-4">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active"
                                    <?php echo ($header['status'] == 'Active') ? 'checked' : ''; ?>> Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Inactive"
                                    <?php echo ($header['status'] == 'Inactive') ? 'checked' : ''; ?>> Inactive</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea id="editor1" name="remarks" class="form-control"
                                    placeholder="Enter remarks"><?php echo htmlspecialchars($header['remarks']); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Terms & Conditions</label>
                                <textarea id="editor2" name="terms" class="form-control"
                                    placeholder="Enter terms"><?php echo htmlspecialchars($header['terms']); ?></textarea>
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
                                <button id="btnExport" type="button" class="btn btn-success" value="Vendor-quote-<?php echo $header['quote_no']; ?>" >Export
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
                                 <th class="text-center" style="width:5%;">✔ <br><input type="checkbox" name="selectall" id="selectall"></th>
                                <th style="width:10%;">Item Code</th>
                                <th style="width:35%;">Description</th>
                                <th style="width:10%;">UOM & Qty</th>
                                <th style="width:10%;">Rate</th>
                                <th style="width:8%;">VAT %</th>
                                <th style="width:11%;">Amt (W/O Tax)</th>
                                <th style="width:11%;">Amt (With Tax)</th>
                            </tr>
                        </thead>
                        <tbody id="item_container">
                            <?php if (!empty($items)): ?>
                            <?php $index = 0;
                                foreach ($items as $item): ?>
                            <tr class="item-row item-card">
                                <td>
                                    <input type="checkbox" class="form-check-input item-check" name="selected_items[]"
                                        value="<?php echo $index; ?>" <?php echo (!empty($item['vendor_quote_item_id'])) ? 'checked' : ''; ?>>
                                    <input type="hidden" name="vendor_rate_enquiry_item_id[<?php echo $index; ?>]" value="<?php echo $item['vendor_rate_enquiry_item_id']; ?>">
                                    <input type="hidden" name="vendor_quote_item_id[<?php echo $index; ?>]" value="<?php echo $item['vendor_quote_item_id']; ?>">
                                </td>

                                <td>
                                    <input type="text" class="form-control item_code" name="item_code[<?php echo $index; ?>]"  value="<?php echo htmlspecialchars($item['item_code']); ?>">
                                </td>
                                <td>
                                    <textarea name="item_desc[<?php echo $index; ?>]" class="form-control" rows="2"  ><?php echo htmlspecialchars($item['item_desc']); ?></textarea>
                                </td>

                                <td>
                                    <input type="text" name="uom[<?php echo $index; ?>]" class="form-control uom" value="<?php echo $item['uom']; ?>" >
                                    <br>
                                    <input type="number" step="any" name="qty[<?php echo $index; ?>]"  class="form-control qty" value="<?php echo $item['qty']; ?>" >
                                </td>

                                <td>
                                    <input type="number" step="any" name="rate[<?php echo $index; ?>]" class="form-control rate" value="<?php echo $item['rate']; ?>">
                                </td>

                                <td>
                                    <input type="number" step="any" name="gst[<?php echo $index; ?>]" class="form-control vat" value="<?php echo $item['vat']; ?>">
                                   
                                </td>

                                <td>
                                 <input type="number" step="any" name="amount_wo_tax[]" class="form-control amountwotx"
                                                            value="<?php echo number_format(($item['rate'] * $item['qty']), 3, '.', ''); ?>" readonly>
                                </td>
                                <td>
                                     <input type="number" step="any" name="amount[]" class="form-control amounttx amount"
                                                            value="<?php echo number_format($item['amount'], 3, '.', ''); ?>" readonly>
                                </td>

                            </tr>
                            <?php $index++; endforeach; ?>
                            <?php endif; ?>
                        </tbody> 
                    </table> 
                    <div class="row">   
                        <div class="col-md-3 pull-right ">
                            <div class="total-box shadow-sm">
                                <h5 class="mb-0">
                                    <i class="fa fa-calculator text-success me-2"></i>
                                    <strong>Total Amount With Tax:</strong>
                                    <span class="text-primary"><span id="total_amount">0.000</span></span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-md-3 pull-right">
                            <div class="total-box shadow-sm">
                                <h5 class="mb-0">
                                    <i class="fa fa-calculator text-success me-2"></i>
                                    <strong>Total Amount WO Tax:</strong>
                                    <span class="text-primary"><span id="total_amount_wo_tax">0.000</span></span>
                                </h5>
                            </div>
                        </div> 
                    </div>
                </fieldset>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('vendor-quotation-list'); ?>" class="btn btn-warning pull-left">
                    <i class="fa fa-arrow-left"></i> Back To List
                </a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update Quotation</button>
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>