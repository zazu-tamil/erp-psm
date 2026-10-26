<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-folder-open"></i> Customer</a></li>
        <li class="active">Customer Advance Payment List</li>
    </ol>
</section>

<section class="content">
    <?php if ($this->session->flashdata('success')) { ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php } ?>
    
    <!-- Modal -->
    <div class="modal fade" id="addModal" role="dialog" aria-labelledby="addModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="addModalLabel">Add / Edit Customer Advance Payment</h4>
                </div>
                <form method="post" action="" id="frmAdd">
                    <div class="modal-body">
                        <input type="hidden" name="mode" id="mode" value="Add">
                        <input type="hidden" name="adv_payment_id" id="adv_payment_id" value="">
                        
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Tender Enquiry ID</label>
                                <?php echo form_dropdown('tender_enquiry_id', $tender_enquiry_opt, '', 'id="tender_enquiry_id" class="form-control select2" style="width:100%"'); ?>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label>Customer <span class="text-red">*</span></label>
                                <?php echo form_dropdown('customer_id', $customer_opt, '', 'id="customer_id" class="form-control select2" style="width:100%" required'); ?>
                            </div>
                            <div class="clearfix"></div>

                            <div class="form-group col-md-6">
                                <label>Customer PO</label>
                                <select name="tender_po_id" id="tender_po_id" class="form-control select2" style="width:100%">
                                    <option value="">Select PO</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Mapped Invoices (Multiple)</label>
                                <select name="invoice_ids[]" id="invoice_ids" class="form-control select2" multiple="multiple" style="width:100%" data-placeholder="Select Invoices">
                                </select>
                            </div>
                            <div class="clearfix"></div>

                            <div class="form-group col-md-6">
                                <label>Account Type <span class="text-red">*</span></label>
                                <?php echo form_dropdown('ac_type_opt', $ac_type_opt_list, 'Bank', 'id="ac_type_opt" class="form-control select2" style="width:100%" required'); ?>
                            </div>

                            <div class="form-group col-md-6" id="bank_div">
                                <label>Select Bank <span class="text-red">*</span></label>
                                <?php echo form_dropdown('bank_id', ['' => 'Select Bank'] + $bank_opt, '', 'id="bank_id" class="form-control select2" style="width:100%"'); ?>
                            </div>

                            <div class="form-group col-md-6" id="cash_category_div" style="display:none;">
                                <label>Cash Category <span class="text-red">*</span></label>
                                <?php echo form_dropdown('cash_category_id', ['' => 'Select Cash Category'] + $cash_categories_opt, '', 'id="cash_category_id" class="form-control select2" style="width:100%"'); ?>
                            </div>
                            <div class="clearfix"></div>

                            <div class="form-group col-md-6">
                                <label for="adv_payment_date">Advance Payment Date <span class="text-red">*</span></label>
                                <input type="date" name="adv_payment_date" id="adv_payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="adv_payment_amt">Payment Amount <span class="text-red">*</span></label>
                                <input type="number" step="0.001" name="adv_payment_amt" id="adv_payment_amt" class="form-control" placeholder="0.000" required>
                            </div>
                            <div class="clearfix"></div>

                            <div class="form-group col-md-6">
                                <label for="remarks">Remarks</label>
                                <input type="text" name="remarks" id="remarks" class="form-control" placeholder="Enter remarks / notes">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Status</label>
                                <select name="status" id="status" class="form-control select2" style="width:100%">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" id="btnCancel">Close</button>
                        <button type="submit" class="btn btn-primary" id="btnSave"><i class="fa fa-save"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Filter</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form method="post" action="">
                <div class="row">
                    <div class="col-md-3">
                        <label>From Date</label>
                        <input type="date" name="srch_from_date" class="form-control" value="<?php echo isset($srch_from_date) ? $srch_from_date : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label>To Date</label>
                        <input type="date" name="srch_to_date" class="form-control" value="<?php echo isset($srch_to_date) ? $srch_to_date : ''; ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Customer</label>
                        <?php echo form_dropdown('srch_customer_id', $customer_opt, isset($srch_customer_id) ? $srch_customer_id : '', 'class="form-control select2" style="width:100%"'); ?>
                    </div>
                    <div class="col-md-2 mt-4 text-right">
                        <label>&nbsp;</label><br>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                        <a href="<?php echo site_url('customer-adv-payment/clear_filter'); ?>" class="btn btn-danger"><i class="fa fa-times"></i> Clear</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- List Table -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Customer Advance Payments</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-success btn-sm" id="btnAdd">
                    <i class="fa fa-plus"></i> Add Customer Advance
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 45px;">S.No</th>
                        <th>Payment Date</th>
                        <th>Tender Enquiry No</th>
                        <th>Customer Name</th>
                        <th>PO No</th>
                        <th>Mapped Invoices</th>
                        <th>A/C Type</th>
                        <th class="text-right">Amount</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th class="text-center" colspan="2" style="width: 80px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)): ?>
                        <?php foreach ($record_list as $j => $row): ?>
                            <tr>
                                <td class="text-center"><?php echo ($j + 1 + $sno); ?></td>
                                <td><?php echo $row['adv_payment_date'] ? date('d-m-Y', strtotime($row['adv_payment_date'])) : '-'; ?></td>
                                <td>
                                    <?php if (!empty($row['tender_info'])): ?>
                                        <small class="label label-success"><?php echo $row['tender_info']; ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($row['customer_name'] ?? '-'); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['po_no'] ?? '-'); ?></td>
                                <td>
                                    <?php 
                                        $display_invoices = !empty($row['invoice_no']) ? $row['invoice_no'] : '';
                                        if (!empty($display_invoices)): 
                                            $inv_arr = explode(',', $display_invoices);
                                            foreach ($inv_arr as $inv_item):
                                                $inv_item = trim($inv_item);
                                                if (!empty($inv_item)):
                                    ?>
                                        <span class="label label-primary" style="display:inline-block; margin: 1px 2px; font-size: 85%;"><?php echo htmlspecialchars($inv_item); ?></span>
                                    <?php 
                                                endif;
                                            endforeach;
                                        else: 
                                    ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                     <span class="label <?php echo ($row['ac_type_opt'] == 'Cash') ? 'label-warning' : 'label-info'; ?>">
                                         <?php echo htmlspecialchars($row['ac_type_opt'] ?? '-'); ?>
                                     </span>
                                     <?php if ($row['ac_type_opt'] == 'Bank' && !empty($row['bank_name'])): ?>
                                         <br><small class="text-muted"><?php echo htmlspecialchars($row['bank_name']); ?></small>
                                     <?php elseif ($row['ac_type_opt'] == 'Cash' && !empty($row['category_name'])): ?>
                                         <br><small class="text-muted"><?php echo htmlspecialchars($row['category_name']); ?></small>
                                     <?php endif; ?>
                                 </td>
                                <td class="text-right" style="font-weight: 700; color: #00a65a;"><?php echo number_format($row['adv_payment_amt'], 3); ?></td>
                                <td><small><?php echo htmlspecialchars($row['remarks'] ?? '-'); ?></small></td>
                                <td>
                                    <span class="label label-<?php echo ($row['status'] == 'Active') ? 'success' : 'danger'; ?>">
                                        <?php echo htmlspecialchars($row['status'] ?? '-'); ?>
                                    </span>
                                </td>

                                <td class="text-center" style="width: 35px;">
                                     <button class="btn btn-primary btn-xs edit_record" 
                                             data-id="<?php echo $row['adv_payment_id']; ?>"
                                             data-tender="<?php echo $row['tender_enquiry_id'] ?? ''; ?>"
                                             data-customer="<?php echo $row['customer_id']; ?>"
                                             data-po="<?php echo $row['tender_po_id'] ?? ''; ?>"
                                             data-invoices="<?php echo htmlspecialchars($row['invoice_ids'] ?? ''); ?>"
                                             data-ac="<?php echo $row['ac_type_opt']; ?>"
                                             data-bank="<?php echo $row['bank_id'] ?? ''; ?>"
                                             data-cash="<?php echo $row['cash_category_id'] ?? ''; ?>"
                                             data-date="<?php echo $row['adv_payment_date']; ?>"
                                             data-amt="<?php echo $row['adv_payment_amt']; ?>"
                                             data-remarks="<?php echo htmlspecialchars($row['remarks'] ?? ''); ?>"
                                             data-status="<?php echo $row['status']; ?>"
                                             title="Edit">
                                         <i class="fa fa-edit"></i>
                                     </button>
                                </td>
                                <td class="text-center" style="width: 35px;">
                                    <button value="<?php echo $row['adv_payment_id']; ?>"
                                        class="del_record btn btn-danger btn-xs" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12" class="text-center text-danger">No customer advance records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="box-footer">
            <div class="form-group col-sm-6">
                <label>Total Records: <?php echo $total_records; ?></label>
            </div>
            <div class="form-group col-sm-6 text-right">
                <?php echo $pagination; ?>
            </div>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
