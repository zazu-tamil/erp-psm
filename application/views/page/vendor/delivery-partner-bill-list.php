<?php include_once(VIEWPATH . '/inc/header.php'); 
//print_r($record_list);
?>

<section class="content-header">
    <h1><?php echo $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Master</a></li>
        <li class="active"><?php echo $title; ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="" id="frmsearch">
                <div class="row">

                    <div class="form-group col-md-3">
                        <label>Customer</label>
                        <div class="form-group">
                            <?php echo form_dropdown('srch_customer_id', ['' => 'All'] + $customer_opt, $srch_customer_id, 'id="srch_customer_id" class="form-control select2" '); ?>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Supplier</label>
                        <?php echo form_dropdown('srch_vendor_id', ['' => 'All'] + $vendor_opt, $srch_vendor_id, 'id="srch_vendor_id" class="form-control srch_vendor_id select2" style="width:100%"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <label for="">OR</label>
                    </div>
                </div>
                <div class="row">

                    <div class="form-group col-md-3">
                        <label for="srch_invoice_no">Invoice No</label>
                        <input type="text" name="srch_invoice_no" id="srch_invoice_no" class="form-control"
                            value="<?php echo set_value('srch_invoice_no', $srch_invoice_no); ?>"
                            placeholder="Search the Our Invoice No">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="srch_enquiry_no">Our Enquiry No</label>
                        <input type="text" name="srch_enquiry_no" id="srch_enquiry_no"
                            class="form-control srch_enquiry_no"
                            value="<?php echo set_value('srch_enquiry_no', $srch_enquiry_no); ?>"
                            placeholder="Search the Our Enquiry No">
                    </div>

                    <div class="form-group col-md-3 text-left">
                        <br>
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="box box-success">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#add_modal">
                <span class="fa fa-plus-circle"></span> Add New
            </button>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped table-responsive" id="company_list">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th>Sub A/C Head</th>
                        <th>Deliver Partner</th>
                        <th>Customer</th>
                        <th>Our Enquiry No</th>
                        <th>Invoice No</th>
                        <th>Amt W/O DP</th> 
                        <th>Amt With Tax/DP</th>
                        <th colspan="2" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($record_list as $j => $ls) {
                        ?>
                    <tr class="mb-3">
                        <td class="text-center"><?php echo ($j + 1); ?></td>
                        <td><?php echo htmlspecialchars($ls['sub_account_head_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($ls['vendor_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($ls['customer_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($ls['tender_info'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($ls['invoice_no'] ?? ''); ?></td>
                        <td class="text-right">
                            <?php echo number_format((float) ($ls['tot_amt_wo_dp'] ?? 0), 3); ?>
                        </td>
                        <td class="text-right">
                            <?php echo number_format((float) ($ls['g_total'] ?? 0), 3); ?>
                        </td>
                        <td class="text-center">
                            <button data-toggle="modal" data-target="#edit_modal"
                                value="<?php echo $ls['dp_bill_id'] ?? ''; ?>"
                                class="edit_record btn btn-primary btn-xs" title="Edit">
                                <i class="fa fa-edit"></i>
                            </button>
                        </td>
                        <td class="text-center">
                            <button value="<?php echo $ls['dp_bill_id'] ?? ''; ?>" class="del_record btn btn-danger btn-xs"
                                title="Delete">
                                <i class="fa fa-remove"></i>
                            </button>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>

           
            <div class="modal fade" id="add_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel"><strong>Add Delivery Partner Bill
                                        </strong></h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>
                            <div class="modal-body">
                                <div
                                    style="border:1px solid #ddd; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:5px;">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="srch_enq_id">Search Enquiry No</label>
                                            <input type="text" name="srch_enq_id" class="form-control srch_enq_id"
                                                value="" placeholder="Search Enquiry No" />
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="sub_account_head_id">A/c Sub Head</label>
                                            <?php echo form_dropdown('sub_account_head_id', $ac_sub_head_opt, set_value('sub_account_head_id'), 'id="sub_account_head_id" class="form-control" required'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Customer <span class="text-red">*</span></label>
                                        <?php echo form_dropdown('customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('customer_id'), 'id="srch_customer_id" class="form-control" required'); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="tender_enquiry_id">Tender Enquiry No</label>
                                        <?php echo form_dropdown('tender_enquiry_id', ['' => 'Select Enquiry'], set_value('tender_enquiry_id'), 'id="srch_tender_enquiry_id" class="form-control" required'); ?>
                                    </div> 
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="vendor_id">Supplier Name <span class="text-red">*</span></label>
                                        <div class="input-group">
                                            <?php echo form_dropdown('vendor_id', ['' => 'Select'] + $vendor_opt, set_value('vendor_id'), 'id="vendor_id" class="form-control srch_vendor_id" required'); ?>
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-info" id="btn_open_add_vendor" value="add_modal">Add
                                                    New</button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Invoice Date</label>
                                        <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                                            value="<?php echo set_value('invoice_date'); ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Invoice No <span class="text-red">*</span></label>
                                        <input type="text" name="invoice_no" id="invoice_no" class="form-control"
                                            placeholder="Invoice No" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Entry Date</label>
                                        <input type="date" name="inv_entry_date" id="inv_entry_date" class="form-control"
                                            value="<?php echo set_value('inv_entry_date'); ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>VAT Payer Sales / Purchase Group</label>
                                        <?php echo form_dropdown('vat_payer_purchase_grp', $vat_payer_purchase_opt, set_value('vat_payer_purchase_grp'), 'id="vat_payer_purchase_grp" class="form-control"'); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label>Custom Stamp Fee</label>
                                        <input type="number" step="any" name="custom_stamp_fee"
                                            id="custom_stamp_fee" class="form-control"
                                            placeholder="Custom Stamp Fee" value="" >
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Custom Bill Amount</label>
                                        <input type="number" step="any" name="custom_bill_amt"
                                            id="custom_bill_amt" class="form-control"
                                            placeholder="Custom Bill Amount" value="" >
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Custom Duty</label>
                                        <input type="number" step="any" name="custom_duty" id="custom_duty"
                                            class="form-control" placeholder="Custom Duty">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Custom VAT Amount</label>
                                        <input type="number" step="any" name="custom_vat_amt"
                                            id="custom_vat_amt" class="form-control"
                                            placeholder="Custom VAT Amount">
                                    </div> 
                                    
                                    
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label>Total Amount <i class="text-sm">W/O DP Chrg & VAT</i> </label>
                                        <input type="number" step="any" name="tot_amt_wo_dp"
                                            id="tot_amt_wo_dp" class="form-control"
                                            placeholder="Total Amount W/O DP">
                                    </div>
                                   <div class="form-group col-md-3">
                                        <label>DP Charges</label>
                                        <input type="number" step="any" name="dp_charges"
                                            id="dp_charges" class="form-control"
                                            placeholder="DP Charges" value="0">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>DP VAT %</label>
                                        <input type="number" step="any" name="dp_vat" id="dp_vat"
                                            class="form-control" placeholder="DP VAT">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>DP VAT Amt</label>
                                        <input type="number" step="any" name="dp_vat_amt" id="dp_vat_amt"
                                            class="form-control" placeholder="DP VAT Amount" readonly>
                                    </div>
                                     <div class="form-group col-md-3">
                                        <label>Grand Total</label>
                                        <input type="number" step="any" name="g_total"
                                            id="g_total" class="form-control"
                                            placeholder="Grand Total Amount">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Status</label><br>
                                        <label class="radio-inline"><input type="radio" name="status" value="Active"
                                                checked> Active</label>
                                        <label class="radio-inline"><input type="radio" name="status" value="InActive">
                                            InActive</label>
                                    </div> 
                                </div>  
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <input type="submit" name="Save" value="Save" class="btn btn-primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="edit_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form method="post" action="" id="frmedit"
                            enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel"><strong>Edit Delivery Partner Bill</strong>
                                </h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="dp_bill_id" id="dp_bill_id" value="" />
                            </div>
                             
                            <div class="modal-body">
                                <div
                                    style="border:1px solid #ddd; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:5px;">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="srch_enq_id">Search Enquiry No</label>
                                            <input type="text" name="srch_enq_id" class="form-control srch_enq_id"
                                                value="" placeholder="Search Enquiry No" />
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="sub_account_head_id">A/c Sub Head</label>
                                            <?php echo form_dropdown('sub_account_head_id', $ac_sub_head_opt, set_value('sub_account_head_id'), 'id="sub_account_head_id" class="form-control" required'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Customer <span class="text-red">*</span></label>
                                        <?php echo form_dropdown('customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('customer_id'), 'id="srch_customer_id" class="form-control" required'); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="tender_enquiry_id">Tender Enquiry No</label>
                                        <?php echo form_dropdown('tender_enquiry_id', ['' => 'Select Enquiry'], set_value('tender_enquiry_id'), 'id="srch_tender_enquiry_id" class="form-control" required'); ?>
                                    </div> 
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="vendor_id">Supplier Name <span class="text-red">*</span></label>
                                        <div class="input-group1">
                                            <?php echo form_dropdown('vendor_id', ['' => 'Select'] + $vendor_opt, set_value('vendor_id'), 'id="vendor_id" class="form-control srch_vendor_id" required'); ?>
                                           
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Invoice Date</label>
                                        <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                                            value="<?php echo set_value('invoice_date'); ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Invoice No <span class="text-red">*</span></label>
                                        <input type="text" name="invoice_no" id="invoice_no" class="form-control"
                                            placeholder="Invoice No" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Entry Date</label>
                                        <input type="date" name="inv_entry_date" id="inv_entry_date" class="form-control"
                                            value="<?php echo set_value('inv_entry_date'); ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>VAT Payer Sales / Purchase Group</label>
                                        <?php echo form_dropdown('vat_payer_purchase_grp', $vat_payer_purchase_opt, set_value('vat_payer_purchase_grp'), 'id="vat_payer_purchase_grp" class="form-control"'); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label>Custom Stamp Fee</label>
                                        <input type="number" step="any" name="custom_stamp_fee"
                                            id="custom_stamp_fee" class="form-control"
                                            placeholder="Custom Stamp Fee" value="" >
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Custom Bill Amount</label>
                                        <input type="number" step="any" name="custom_bill_amt"
                                            id="custom_bill_amt" class="form-control"
                                            placeholder="Custom Bill Amount" value="" >
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Custom Duty</label>
                                        <input type="number" step="any" name="custom_duty" id="custom_duty"
                                            class="form-control" placeholder="Custom Duty">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Custom VAT Amount</label>
                                        <input type="number" step="any" name="custom_vat_amt"
                                            id="custom_vat_amt" class="form-control"
                                            placeholder="Custom VAT Amount">
                                    </div> 
                                    
                                    
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label>Total Amount <i class="text-sm">W/O DP Chrg & VAT</i> </label>
                                        <input type="number" step="any" name="tot_amt_wo_dp"
                                            id="tot_amt_wo_dp" class="form-control"
                                            placeholder="Total Amount W/O DP">
                                    </div>
                                   <div class="form-group col-md-3">
                                        <label>DP Charges</label>
                                        <input type="number" step="any" name="dp_charges"
                                            id="dp_charges" class="form-control"
                                            placeholder="DP Charges" value="0">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>DP VAT %</label>
                                        <input type="number" step="any" name="dp_vat" id="dp_vat"
                                            class="form-control" placeholder="DP VAT">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>DP VAT Amt</label>
                                        <input type="number" step="any" name="dp_vat_amt" id="dp_vat_amt"
                                            class="form-control" placeholder="DP VAT Amount" readonly>
                                    </div>
                                     <div class="form-group col-md-3">
                                        <label>Grand Total</label>
                                        <input type="number" step="any" name="g_total"
                                            id="g_total" class="form-control"
                                            placeholder="Grand Total Amount">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Status</label><br>
                                        <label class="radio-inline"><input type="radio" name="status" value="Active"
                                                checked> Active</label>
                                        <label class="radio-inline"><input type="radio" name="status" value="InActive">
                                            InActive</label>
                                    </div> 
                                </div>  
                                 
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <input type="submit" name="Save" value="Update" class="btn btn-primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Add Vendor Modal (Child / Nested) -->
            <div class="modal fade" id="add_vendor" tabindex="-1" role="dialog" aria-labelledby="addVendorLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form method="post" action="" id="frmadd_Vendor" enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" id="btn_close_vendor_modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="addVendorLabel">Add Supplier</h3>
                                <input type="hidden" name="mode" value="Add Vendor" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Supplier Name</label>
                                        <input class="form-control" type="text" name="vendor_name" id="vendor_name"
                                            placeholder="Supplier Name">
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
                                        <input class="form-control" type="text" name="mobile" id="mobile"
                                            placeholder="Mobile">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Alternate Mobile</label>
                                        <input class="form-control" type="text" name="mobile_alt" id="mobile_alt"
                                            placeholder="Alternate Mobile">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Email</label>
                                        <input class="form-control" type="email" name="email" id="email"
                                            placeholder="Email">
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
                                        <input class="form-control" type="text" name="gst" id="gst"
                                            placeholder="VAT No">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control" name="address" id="address"
                                                placeholder="Address" required="true" rows="4"></textarea>
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
                                <button type="button" class="btn btn-secondary"
                                    id="btn_cancel_vendor_modal">Cancel</button>
                                <input type="submit" name="btn_add_vendor" value="Save" class="btn btn-primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div><!-- /.box-body -->

        <div class="box-footer">
            <div class="form-group col-sm-6">
                <label>Total Records : <?php echo $total_records; ?></label>
            </div>
            <div class="form-group col-sm-6">
                <?php echo $pagination; ?>
            </div>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>
