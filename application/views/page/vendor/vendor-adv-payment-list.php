<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Vendor</a></li>
        <li class="active">Vendor Advance Payment List</li>
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
                    <h4 class="modal-title" id="addModalLabel">Add / Edit Advance Payment</h4>
                </div>
                <form method="post" action="" id="frmAdd">
                    <div class="modal-body">
                        <input type="hidden" name="mode" id="mode" value="Add">
                        <input type="hidden" name="adv_payment_id" id="adv_payment_id" value="">
                        
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Tender Enquiry ID</label>
                                <?php echo form_dropdown('tender_enquiry_id', $tender_enquiry_opt, '', 'id="tender_enquiry_id" class="form-control select2" style="width:100%" required'); ?>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label>Vendor</label>
                                <?php echo form_dropdown('vendor_id', $vendor_opt, '', 'id="vendor_id" class="form-control select2" style="width:100%" required'); ?>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label>Vendor PO</label>
                                <select name="vendor_po_id" id="vendor_po_id" class="form-control select2" style="width:100%">
                                    <option value="">Select PO</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Account Type</label>
                                <?php echo form_dropdown('ac_type_opt', $ac_type_opt_list, '', 'id="ac_type_opt" class="form-control select2" style="width:100%" required'); ?>
                            </div>
                        
                            <div class="form-group col-md-6">
                                <label for="adv_payment_date">Advance Payment Date</label>
                                <input type="date" name="adv_payment_date" id="adv_payment_date" class="form-control" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="adv_payment_amt">Payment Amount</label>
                                <input type="number" step="0.01" name="adv_payment_amt" id="adv_payment_amt" class="form-control" placeholder="0.00" required>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label>Status</label>
                                <select name="status" id="status" class="form-control select2" style="width:100%">
                                    <option value="">Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
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
                        <label>Vendor</label>
                        <?php echo form_dropdown('srch_vendor_id', $vendor_opt, isset($srch_vendor_id) ? $srch_vendor_id : '', 'class="form-control select2" style="width:100%"'); ?>
                    </div>
                    <div class="col-md-2 mt-4 text-right">
                        <label>&nbsp;</label><br>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                        <a href="<?php echo site_url('vendor-adv-payment/clear_filter'); ?>" class="btn btn-danger"><i class="fa fa-times"></i> Clear</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- List Table -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Vendor Advance Payments</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-success btn-sm" id="btnAdd">
                    <i class="fa fa-plus"></i> Add Advance Payment
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
                        <th class="text-center">S.No</th>
                        <th>Payment Date</th>
                        <th>Tender Enquiry No</th>
                        <th>Vendor Name</th>
                        <th>PO No</th>
                        <th>A/C Type</th>
                        <th class="text-right">Amount</th>
                        <th>Status </th>
                        <th class="text-center" colspan="2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)): ?>
                        <?php foreach ($record_list as $j => $row): ?>
                            <tr>
                                <td class="text-center"><?php echo ($j + 1 + $sno); ?></td>
                                <td><?php echo $row['adv_payment_date'] ? date('d-m-Y', strtotime($row['adv_payment_date'])) : '-'; ?></td>
                                <td>
                                    <small class="label label-success"><?php echo $row['tender_info']; ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($row['vendor_name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['po_no'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['ac_type_opt'] ?? '-'); ?></td>
                                <td class="text-right"><?php echo number_format($row['adv_payment_amt'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['status'] ?? '-'); ?></td>

                                <td class="text-center">
                                    <button class="btn btn-primary btn-xs edit_record" 
                                            data-id="<?php echo $row['adv_payment_id']; ?>"
                                            data-tender="<?php echo $row['tender_enquiry_id']; ?>"
                                            data-vendor="<?php echo $row['vendor_id']; ?>"
                                            data-po="<?php echo $row['vendor_po_id']; ?>"
                                            data-ac="<?php echo $row['ac_type_opt']; ?>"
                                            data-date="<?php echo $row['adv_payment_date']; ?>"
                                            data-amt="<?php echo $row['adv_payment_amt']; ?>"
                                            data-status="<?php echo $row['status']; ?>"
                                            title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </td>
                                <td class="text-center">
                                    <button value="<?php echo $row['adv_payment_id']; ?>"
                                        class="del_record btn btn-danger btn-xs" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-danger">No records found.</td>
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
