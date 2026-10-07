<?php include_once(VIEWPATH . 'inc/header.php'); ?>

<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Vendor</a></li>
        <li class="active"><?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<style>
    .customer-block {
        border: 2px solid #3c8dbc;
        border-radius: 8px;
        margin-bottom: 15px;
        overflow: hidden;
    }
    .customer-block-header {
        background: linear-gradient(135deg, #004b8d, #3c8dbc);
        color: #fff;
        padding: 8px 14px;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .customer-block-header .select-all-btn {
        font-size: 11px;
        padding: 2px 10px;
        cursor: pointer;
    }
    .item-modal-table th { background: #f4f6f9; font-size: 12px; }
    .item-modal-table td { font-size: 12px; vertical-align: middle; }
    .selected-items-table th { background: #004b8d; color: #fff; font-size: 12px; }
    .selected-items-table td { font-size: 12px; vertical-align: middle; }
    .badge-customer {
        background: #e8f0fe;
        color: #1a56db;
        border: 1px solid #a4cafe;
        border-radius: 12px;
        padding: 2px 10px;
        font-size: 11px;
        font-weight: 600;
    }
    .total-box { background: linear-gradient(135deg, #f6fff9, #e8f9f0); border: 2px solid #b5e0c6; border-radius: 8px; padding: 10px 16px; }
    .enquiry-tag { background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 2px 8px; font-size: 11px; }
    .no-results-msg { color: #888; text-align: center; padding: 20px; }
    .ui-autocomplete { z-index: 9999 !important; }
</style>

<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-edit"></i> Edit Vendor Purchase Bill – Multiple Customers</h3>
            <a href="<?php echo site_url('vendor-purchase-bill-multiple-customer-list'); ?>" class="btn btn-warning pull-right">
                <i class="fa fa-arrow-left"></i> Back To List
            </a>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible" style="margin:10px;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible" style="margin:10px;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Edit">
                <input type="hidden" name="vendor_purchase_multiple_invoice_id" value="<?php echo $record['vendor_purchase_multiple_invoice_id']; ?>">

                <!-- === BILL HEADER === -->
                <fieldset style="border:1px solid #3c8dbc; padding:15px; border-radius:6px; margin-bottom:20px;">
                    <legend style="color:#3c8dbc; font-weight:700; font-size:15px; width:auto; padding:0 12px;">
                        <i class="fa fa-file-text-o"></i> Purchase Bill Details
                    </legend>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Company <span class="text-danger">*</span></label>
                            <?php echo form_dropdown('srch_company_id', $company_opt, set_value('srch_company_id', $record['company_id']), 'id="srch_company_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Vendor <span class="text-danger">*</span></label>
                            <select name="srch_vendor_id" id="srch_vendor_id" class="form-control select2" required>
                                <option value="">Select Vendor</option>
                                <?php foreach ($vendor_opt as $vid => $vname): ?>
                                    <option value="<?php echo $vid; ?>" <?php if($record['vendor_id'] == $vid) echo 'selected'; ?>><?php echo htmlspecialchars($vname); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Contact Person</label>
                            <?php echo form_dropdown('srch_vendor_contact_person_id', $vendor_contact_opt, set_value('srch_vendor_contact_person_id', $record['vendor_contact_person_id']), 'id="srch_vendor_contact_id" class="form-control"'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                                value="<?php echo set_value('invoice_date', $record['invoice_date']); ?>" required>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Invoice No <span class="text-danger">*</span></label>
                            <input type="text" name="invoice_no" id="invoice_no" class="form-control" placeholder="Invoice No" required value="<?php echo htmlspecialchars($record['invoice_no']); ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Entry Date <small class="text-danger">[VAT Date]</small></label>
                            <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?php echo set_value('entry_date', $record['entry_date']); ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Upload Bill Document</label>
                            <input type="file" name="purchase_bill_upload" id="purchase_bill_upload" class="form-control">
                            <input type="hidden" name="old_purchase_bill_upload" value="<?php echo $record['purchase_bill_upload']; ?>">
                            <?php if(!empty($record['purchase_bill_upload'])): ?>
                                <small><a href="<?php echo base_url($record['purchase_bill_upload']); ?>" target="_blank">View Uploaded Document</a></small>
                            <?php endif; ?>
                        </div>

                        <div class="form-group col-md-2">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active" <?php echo ($record['status'] == 'Active') ? 'checked' : ''; ?>> Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Inactive" <?php echo ($record['status'] == 'Inactive') ? 'checked' : ''; ?>> Inactive</label>
                        </div>
                    </div>

                    <!-- VAT Filing -->
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset style="border:1px solid #081979; padding:10px; border-radius:4px; background:#f9f9f9;">
                                <legend class="text-info" style="font-size:13px; width:auto; padding:0 10px;">For Purchase NBR - VAT Filing</legend>
                                <div class="col-md-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="only_accounting_entry" id="only_accounting_entry" value="1" <?php if($record['only_accounting_entry'] == 1) echo 'checked'; ?>>
                                            Only Accounting Entry (Not VAT Filing)
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>VAT Payer Purchase Category</label>
                                    <?php echo form_dropdown('vat_payer_purchase_grp', $vat_payer_purchase_opt, set_value('vat_payer_purchase_grp', $record['vat_payer_purchase_grp']), 'id="vat_payer_purchase_grp" class="form-control"'); ?>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea id="editor2" name="remarks" class="form-control" rows="2" placeholder="Remarks..."><?php echo set_value('remarks', $record['remarks']); ?></textarea>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- === ITEM DETAILS === -->
                <fieldset style="border:1px solid #3c8dbc; padding:15px; border-radius:6px; margin-bottom:20px;">
                    <legend style="color:#3c8dbc; font-weight:700; font-size:15px; width:auto; padding:0 12px;">
                        <i class="fa fa-list"></i> Item Details
                    </legend>

                    <div class="text-right" style="margin-bottom:10px;">
                        <button type="button" class="btn btn-primary btn-sm" id="btn_open_item_modal">
                            <i class="fa fa-plus"></i> Add Items from Enquiry
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="selected_items_table">
                            <thead>
                                <tr class="selected-items-table">
                                    <th style="width:5%;">#</th>
                                    <th style="width:40%;">Item Code & Description</th>
                                    <th style="width:10%;">UOM & Qty</th>
                                    <th style="width:10%;">Rate & Conversion</th>
                                    <th style="width:10%;">Amount</th>
                                    <th style="width:8%;">VAT %</th>
                                    <th style="width:11%;">In BHD <br>Amt (W/O Tax) & <br> Amt (With Tax)</th>
                                </tr>
                            </thead>
                            <tbody id="item_container">
                                <tr id="no_items_row">
                                    <td colspan="12" class="no-results-msg"><i class="fa fa-info-circle"></i> No items added yet. Click "Add Items" to search and add.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </fieldset>

                <!-- === TOTAL EXCL ADDT CHARGES === -->
                <fieldset style="border:1px solid #081979; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:2px;">
                    <legend>Total Amount Excluding Additional Charges</legend>
                    <div class="row">
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-3">
                            <div class="form-group total-box shadow-sm">
                                <label>
                                    <i class="fa fa-calculator text-success"></i>
                                    Total Amount WO Tax
                                </label>
                                <span style="display:none;" id="total_amount_wo_tax"></span>
                                <input type="number" step="any" name="total_amount_wo_tax"
                                    id="hidden_total_amount_wo_tax" class="form-control text-right" value="<?php echo $record['total_amount_wo_tax']; ?>"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group total-box shadow-sm">
                                <label>
                                    <i class="fa fa-calculator text-success"></i>
                                    Total VAT Amount
                                </label>
                                <span style="display:none;" id="total_tax_amount"></span>
                                <input type="number" step="any" name="total_vat_amount" id="hidden_total_vat_amount"
                                    class="form-control text-right" value="<?php echo $record['tax_amount']; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group total-box shadow-sm">
                                <label>
                                    <i class="fa fa-calculator text-success"></i>
                                    Total Amount With Tax
                                </label>
                                <span style="display:none;" id="total_amount"></span>
                                <input type="number" step="any" name="total_amount" id="hidden_total_amount"
                                    class="form-control text-right font-weight-bold" value="<?php echo $record['total_amount']; ?>" readonly>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- === ADDITIONAL CHARGES === -->
                <div id="div_addt_chrg">
                    <fieldset style="border:1px solid #081979; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:2px;">
                        <legend class="text-light-blue"><i class="fa fa-list"></i> Additional Charges (If any)</legend>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Addt.Charges Type</th>
                                    <th>Addt.Charges Amt</th>
                                    <th>Conversion Rate</th>
                                    <th>Conversion Amt</th>
                                    <th>VAT %</th>
                                    <th>VAT Amt</th>
                                    <th>Total Amt</th>
                                </tr>
                            </thead>
                            <tbody id="tb_addt_chrg_list">
                                <?php if (!empty($addt_charges_list)) {
                                    foreach ($addt_charges_list as $charge) {
                                        $id = $charge['addt_charges_type_id'];
                                        $is_checked = isset($saved_addt_charges[$id]) ? 'checked' : '';
                                        $saved_chg = isset($saved_addt_charges[$id]) ? $saved_addt_charges[$id] : [];
                                        $amt = isset($saved_chg['addt_charges_amt']) ? $saved_chg['addt_charges_amt'] : '0.000';
                                        $c_rate = isset($saved_chg['conversion_rate']) ? $saved_chg['conversion_rate'] : '1.00000';
                                        $c_amt = isset($saved_chg['conversion_amt']) ? $saved_chg['conversion_amt'] : '0.000';
                                        $vat = isset($saved_chg['addt_charges_vat']) ? $saved_chg['addt_charges_vat'] : '0.00';
                                        $vat_amt = isset($saved_chg['addt_charges_vat_amt']) ? $saved_chg['addt_charges_vat_amt'] : '0.000';
                                        $tot = isset($saved_chg['addt_charges_tot_amt']) ? $saved_chg['addt_charges_tot_amt'] : '0.000';
                                        $readonly = $is_checked ? '' : 'readonly';
                                        $saved_id = isset($saved_chg['vendor_purchase_multiple_invoice_addtchrg_id']) ? $saved_chg['vendor_purchase_multiple_invoice_addtchrg_id'] : '';
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="chk_addt_charge" name="chk_addt_charges_type_id[]" value="<?php echo $id; ?>" <?php echo $is_checked; ?>>
                                        <input type="hidden" name="addt_charges_type_id[<?php echo $id; ?>]" value="<?php echo $id; ?>">
                                        <input type="hidden" name="vendor_purchase_multiple_invoice_addtchrg_id[<?php echo $id; ?>]" value="<?php echo $saved_id; ?>">
                                    </td>
                                    <td><?php echo $charge['addt_charges_type_name']; ?></td>
                                    <td>
                                        <input type="number" step="any" class="form-control addt_charges_amt" name="addt_charges_amt[<?php echo $id; ?>]" value="<?php echo $amt; ?>" <?php echo $readonly; ?>>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control addt_charges_conversion_rate" name="addt_charges_conversion_rate[<?php echo $id; ?>]" value="<?php echo $c_rate; ?>" <?php echo $readonly; ?>>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control addt_charges_conversion_amt" name="addt_charges_conversion_amt[<?php echo $id; ?>]" value="<?php echo $c_amt; ?>" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control addt_charges_vat" name="addt_charges_vat[<?php echo $id; ?>]" value="<?php echo $vat; ?>" <?php echo $readonly; ?>>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control addt_charges_vat_amt" name="addt_charges_vat_amt[<?php echo $id; ?>]" value="<?php echo $vat_amt; ?>" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control addt_charges_tot_amt" name="addt_charges_tot_amt[<?php echo $id; ?>]" value="<?php echo $tot; ?>" readonly>
                                    </td>
                                </tr>
                                <?php } } ?>
                            </tbody>
                        </table>
                    </fieldset>

                    <fieldset style="border:1px solid #081979; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:2px;">
                        <legend>Total Amount Including Additional Charges</legend>
                        <div class="row">
                            <div class="col-md-3">
                                <h4 class="text-red" style="margin-top: 25px;">Total Inc Addt Charges</h4>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="fix_theamount_total" id="fix_theamount_total" value="1" <?php if($record['fix_theamount_total'] == 1) echo 'checked'; ?>> 
                                        Fix Total Amount <i class="text-sm text-info">(manually)</i>
                                    </label>
                                </div>    
                            </div>
                            <div class="col-md-3">
                                <div class="form-group total-box shadow-sm">
                                    <label>Total Amount WO Tax</label>
                                    <span style="display:none;" id="total_amount_wo_tax_addt"></span>
                                    <input type="number" step="any" name="total_amount_wo_tax_inc_addl"
                                        id="hidden_total_amount_wo_tax_inc_addl" class="form-control text-right" value="<?php echo $record['total_amount_wo_tax_inc_addl']; ?>"
                                        <?php if($record['fix_theamount_total'] != 1) echo 'readonly'; ?>>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group total-box shadow-sm">
                                    <label>Total Tax Amount</label>
                                    <span style="display:none;" id="total_tax_amount_addt"></span>
                                    <input type="number" step="any" name="total_tax_amount_inc_addl"
                                        id="hidden_total_tax_amount_inc_addl" class="form-control text-right" value="<?php echo $record['total_tax_amount_inc_addl']; ?>"
                                        <?php if($record['fix_theamount_total'] != 1) echo 'readonly'; ?>>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group total-box shadow-sm">
                                    <label>Total Amount With Tax</label>
                                    <span style="display:none;" id="total_amount_addt"></span>
                                    <input type="number" step="any" name="total_amount_inc_addl"
                                        id="hidden_total_amount_inc_addl" class="form-control text-right" value="<?php echo $record['total_amount_inc_addl']; ?>"
                                        <?php if($record['fix_theamount_total'] != 1) echo 'readonly'; ?>>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>

            </div><!-- /.box-body -->

            <div class="box-footer text-right">
                <a href="<?php echo site_url('vendor-purchase-bill-multiple-customer-list'); ?>" class="btn btn-warning pull-left">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
                <button type="submit" class="btn btn-success" id="btn_save">
                    <i class="fa fa-save"></i> Update Bill
                </button>
            </div>
        </form>
    </div>
</section>

<!-- ========== ITEM SEARCH MODAL ========== -->
<div class="modal fade" id="itemSearchModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width:95%; max-width:1200px;" role="document">
        <div class="modal-content">

            <div class="modal-header" style="background:#004b8d; color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-search"></i> Search & Add Items by Enquiry</h4>
            </div>

            <div class="modal-body">

                <!-- Search Row -->
                <div class="row" style="margin-bottom:15px; background:#f4f6f9; padding:12px; border-radius:6px;">
                    <div class="col-md-6">
                        <label><b>Search by Enquiry No / PO No / Tender Name</b></label>
                        <div class="input-group">
                            <input type="text" id="modal_enquiry_search" class="form-control" placeholder="Type enquiry no, po no or tender name...">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary" id="btn_search_items">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding-top:22px;">
                        <button type="button" class="btn btn-success btn-sm" id="btn_select_all_modal">
                            <i class="fa fa-check-square-o"></i> Select All
                        </button>
                        <button type="button" class="btn btn-default btn-sm" id="btn_deselect_all_modal">
                            <i class="fa fa-square-o"></i> Deselect All
                        </button>
                        <span id="modal_selected_count" class="label label-info" style="font-size:13px; margin-left:10px;">0 selected</span>
                    </div>
                </div>

                <!-- Results Area -->
                <div id="modal_results_area">
                    <div class="no-results-msg">
                        <i class="fa fa-info-circle text-info fa-2x"></i><br>
                        Select a vendor and search for enquiry numbers to load items.
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-success" id="btn_add_selected_items">
                    <i class="fa fa-plus"></i> Add Selected Items
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    var preloaded_items = <?php echo json_encode($items); ?>;
</script>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
