<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<section class="content-header">
    <h1>Contra Entry List</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Accounts</a></li>
        <li class="active">Contra Entry List</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Search Filter Box -->
    <div class="box box-info no-print">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('contra-entry') ?>" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>From Date</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="date" class="form-control pull-right" id="srch_from_date" name="srch_from_date"
                                value="<?php echo set_value('srch_from_date', $srch_from_date); ?>">
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label>To Date</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="date" class="form-control pull-right" id="srch_to_date" name="srch_to_date"
                                value="<?php echo set_value('srch_to_date', $srch_to_date); ?>">
                        </div>
                    </div>
                    <div class="form-group col-md-2 text-left">
                        <br />
                        <button class="btn btn-success" name="btn_show" value="Show"><i class="fa fa-search"></i> Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Record Box -->
    <div class="box box-info">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#add_modal"><span class="fa fa-plus-circle"></span> Add New Contra Entry</button>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped" id="contra-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">S.No</th>
                        <th style="width: 100px;">Entry Date</th>
                        <th>From Account</th>
                        <th>To Account</th>
                        <th class="text-right" style="width: 130px;">Amount (BHD)</th>
                        <th>Remarks</th>
                        <th class="text-center" style="width: 120px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($record_list)) { ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No contra entries found.</td>
                        </tr>
                    <?php } else { 
                        foreach ($record_list as $i => $row) {
                            $from_name = ($row['from_ac_type'] === 'Bank') ? $row['from_bank_name'] : 'Cash ('.$row['from_cash_category_name'].')';
                            $to_name = ($row['to_ac_type'] === 'Bank') ? $row['to_bank_name'] : 'Cash ('.$row['to_cash_category_name'].')';
                    ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($row['entry_date'])); ?></td>
                            <td>
                                <span class="label <?php echo $row['from_ac_type'] === 'Bank' ? 'bg-blue' : 'bg-green'; ?>" style="font-size: 11px; margin-right: 5px;"><?php echo $row['from_ac_type']; ?></span>
                                <strong><?php echo htmlspecialchars($from_name); ?></strong>
                            </td>
                            <td>
                                <span class="label <?php echo $row['to_ac_type'] === 'Bank' ? 'bg-blue' : 'bg-green'; ?>" style="font-size: 11px; margin-right: 5px;"><?php echo $row['to_ac_type']; ?></span>
                                <strong><?php echo htmlspecialchars($to_name); ?></strong>
                            </td>
                            <td class="text-right text-bold"><?php echo number_format($row['amount'], 3); ?></td>
                            <td>
                                <?php if (!empty($row['from_remarks'])) { ?>
                                    <small class="text-muted" style="display:block;">From: <?php echo htmlspecialchars($row['from_remarks']); ?></small>
                                <?php } ?>
                                <?php if (!empty($row['to_remarks'])) { ?>
                                    <small class="text-muted" style="display:block;">To: <?php echo htmlspecialchars($row['to_remarks']); ?></small>
                                <?php } ?>
                                <?php if (empty($row['from_remarks']) && empty($row['to_remarks'])) { echo '-'; } ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-primary btn-xs btn-edit" 
                                    data-id="<?php echo $row['contra_entry_id']; ?>"
                                    data-date="<?php echo $row['entry_date']; ?>"
                                    data-from-type="<?php echo $row['from_ac_type']; ?>"
                                    data-from-bank="<?php echo $row['from_bank_id']; ?>"
                                    data-from-cash="<?php echo $row['from_cash_category_id']; ?>"
                                    data-from-remarks="<?php echo htmlspecialchars($row['from_remarks']); ?>"
                                    data-to-type="<?php echo $row['to_ac_type']; ?>"
                                    data-to-bank="<?php echo $row['to_bank_id']; ?>"
                                    data-to-cash="<?php echo $row['to_cash_category_id']; ?>"
                                    data-to-remarks="<?php echo htmlspecialchars($row['to_remarks']); ?>"
                                    data-amount="<?php echo $row['amount']; ?>"><span class="fa fa-pencil"></span></button>
                                <button type="button" class="btn btn-danger btn-xs btn-delete" 
                                    data-id="<?php echo $row['contra_entry_id']; ?>"><span class="fa fa-trash"></span></button>
                            </td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Add Modal -->
<div class="modal fade" id="add_modal" role="dialog" aria-labelledby="addModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <form method="post" action="<?php echo site_url('contra-entry'); ?>">
            <input type="hidden" name="mode" value="Add">
            <div class="modal-content" style="border-radius: 8px;">
                <div class="modal-header bg-success" style="border-radius: 8px 8px 0 0;">
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-white" id="addModalLabel" style="font-weight:600;"><span class="fa fa-plus-circle"></span> Add New Contra Entry</h4>
                </div>
                <div class="modal-body" style="padding: 25px;">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Date <span class="text-red">*</span></label>
                            <input type="date" class="form-control" name="entry_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Amount (BHD) <span class="text-red">*</span></label>
                            <input type="number" step="0.001" class="form-control" name="amount" placeholder="0.000" required min="0.001">
                        </div>
                    </div>

                    <div class="row">
                        <!-- From Account Details -->
                        <div class="col-md-6" style="border-right: 1px solid #eee; padding-right: 20px;">
                            <h4 style="font-weight: 600; border-bottom: 2px solid #5a5ce8; padding-bottom: 8px; color: #5a5ce8; margin-bottom: 15px;"><i class="fa fa-sign-out"></i> FROM DETAILS (Source)</h4>
                            <div class="form-group">
                                <label>Account Group <span class="text-red">*</span></label>
                                <select class="form-control" name="from_ac_type" id="add_from_ac_type" required>
                                    <option value="">Select Group</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank</option>
                                </select>
                            </div>
                            <div class="form-group add_from_bank_wrapper" style="display: none;">
                                <label>Bank Account <span class="text-red">*</span></label>
                                <select class="form-control select2" name="from_bank_id" id="add_from_bank_id" style="width: 100%;">
                                    <option value="">Select Bank</option>
                                    <?php foreach ($bank_list as $bank) { ?>
                                        <option value="<?php echo $bank['bank_id']; ?>"><?php echo htmlspecialchars($bank['bank_name'].' ('.$bank['branch'].')'); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group add_from_cash_wrapper" style="display: none;">
                                <label>Cash Category <span class="text-red">*</span></label>
                                <select class="form-control select2" name="from_cash_category_id" id="add_from_cash_category_id" style="width: 100%;">
                                    <option value="">Select Cash Category</option>
                                    <?php foreach ($cash_categories as $cc) { ?>
                                        <option value="<?php echo $cc['cash_category_id']; ?>"><?php echo htmlspecialchars($cc['category_name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea class="form-control" name="from_remarks" rows="2" placeholder="Source remarks"></textarea>
                            </div>
                        </div>

                        <!-- To Account Details -->
                        <div class="col-md-6" style="padding-left: 20px;">
                            <h4 style="font-weight: 600; border-bottom: 2px solid #1fc27d; padding-bottom: 8px; color: #1fc27d; margin-bottom: 15px;"><i class="fa fa-sign-in"></i> TO DETAILS (Destination)</h4>
                            <div class="form-group">
                                <label>Account Group <span class="text-red">*</span></label>
                                <select class="form-control" name="to_ac_type" id="add_to_ac_type" required>
                                    <option value="">Select Group</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank</option>
                                </select>
                            </div>
                            <div class="form-group add_to_bank_wrapper" style="display: none;">
                                <label>Bank Account <span class="text-red">*</span></label>
                                <select class="form-control select2" name="to_bank_id" id="add_to_bank_id" style="width: 100%;">
                                    <option value="">Select Bank</option>
                                    <?php foreach ($bank_list as $bank) { ?>
                                        <option value="<?php echo $bank['bank_id']; ?>"><?php echo htmlspecialchars($bank['bank_name'].' ('.$bank['branch'].')'); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group add_to_cash_wrapper" style="display: none;">
                                <label>Cash Category <span class="text-red">*</span></label>
                                <select class="form-control select2" name="to_cash_category_id" id="add_to_cash_category_id" style="width: 100%;">
                                    <option value="">Select Cash Category</option>
                                    <?php foreach ($cash_categories as $cc) { ?>
                                        <option value="<?php echo $cc['cash_category_id']; ?>"><?php echo htmlspecialchars($cc['category_name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea class="form-control" name="to_remarks" rows="2" placeholder="Destination remarks"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: #fcfcfc; border-top: 1px solid #eee; border-radius: 0 0 8px 8px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-times"></span> Close</button>
                    <button type="submit" class="btn btn-success"><span class="fa fa-save"></span> Save Entry</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="edit_modal" role="dialog" aria-labelledby="editModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <form method="post" action="<?php echo site_url('contra-entry'); ?>">
            <input type="hidden" name="mode" value="Edit">
            <input type="hidden" name="contra_entry_id" id="edit_contra_entry_id">
            <div class="modal-content" style="border-radius: 8px;">
                <div class="modal-header bg-primary" style="border-radius: 8px 8px 0 0;">
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-white" id="editModalLabel" style="font-weight:600;"><span class="fa fa-pencil-square-o"></span> Edit Contra Entry</h4>
                </div>
                <div class="modal-body" style="padding: 25px;">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Date <span class="text-red">*</span></label>
                            <input type="date" class="form-control" name="entry_date" id="edit_entry_date" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Amount (BHD) <span class="text-red">*</span></label>
                            <input type="number" step="0.001" class="form-control" name="amount" id="edit_amount" required min="0.001">
                        </div>
                    </div>

                    <div class="row">
                        <!-- From Account Details -->
                        <div class="col-md-6" style="border-right: 1px solid #eee; padding-right: 20px;">
                            <h4 style="font-weight: 600; border-bottom: 2px solid #5a5ce8; padding-bottom: 8px; color: #5a5ce8; margin-bottom: 15px;"><i class="fa fa-sign-out"></i> FROM DETAILS (Source)</h4>
                            <div class="form-group">
                                <label>Account Group <span class="text-red">*</span></label>
                                <select class="form-control" name="from_ac_type" id="edit_from_ac_type" required>
                                    <option value="">Select Group</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank</option>
                                </select>
                            </div>
                            <div class="form-group edit_from_bank_wrapper" style="display: none;">
                                <label>Bank Account <span class="text-red">*</span></label>
                                <select class="form-control select2" name="from_bank_id" id="edit_from_bank_id" style="width: 100%;">
                                    <option value="">Select Bank</option>
                                    <?php foreach ($bank_list as $bank) { ?>
                                        <option value="<?php echo $bank['bank_id']; ?>"><?php echo htmlspecialchars($bank['bank_name'].' ('.$bank['branch'].')'); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group edit_from_cash_wrapper" style="display: none;">
                                <label>Cash Category <span class="text-red">*</span></label>
                                <select class="form-control select2" name="from_cash_category_id" id="edit_from_cash_category_id" style="width: 100%;">
                                    <option value="">Select Cash Category</option>
                                    <?php foreach ($cash_categories as $cc) { ?>
                                        <option value="<?php echo $cc['cash_category_id']; ?>"><?php echo htmlspecialchars($cc['category_name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea class="form-control" name="from_remarks" id="edit_from_remarks" rows="2" placeholder="Source remarks"></textarea>
                            </div>
                        </div>

                        <!-- To Account Details -->
                        <div class="col-md-6" style="padding-left: 20px;">
                            <h4 style="font-weight: 600; border-bottom: 2px solid #1fc27d; padding-bottom: 8px; color: #1fc27d; margin-bottom: 15px;"><i class="fa fa-sign-in"></i> TO DETAILS (Destination)</h4>
                            <div class="form-group">
                                <label>Account Group <span class="text-red">*</span></label>
                                <select class="form-control" name="to_ac_type" id="edit_to_ac_type" required>
                                    <option value="">Select Group</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank</option>
                                </select>
                            </div>
                            <div class="form-group edit_to_bank_wrapper" style="display: none;">
                                <label>Bank Account <span class="text-red">*</span></label>
                                <select class="form-control select2" name="to_bank_id" id="edit_to_bank_id" style="width: 100%;">
                                    <option value="">Select Bank</option>
                                    <?php foreach ($bank_list as $bank) { ?>
                                        <option value="<?php echo $bank['bank_id']; ?>"><?php echo htmlspecialchars($bank['bank_name'].' ('.$bank['branch'].')'); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group edit_to_cash_wrapper" style="display: none;">
                                <label>Cash Category <span class="text-red">*</span></label>
                                <select class="form-control select2" name="to_cash_category_id" id="edit_to_cash_category_id" style="width: 100%;">
                                    <option value="">Select Cash Category</option>
                                    <?php foreach ($cash_categories as $cc) { ?>
                                        <option value="<?php echo $cc['cash_category_id']; ?>"><?php echo htmlspecialchars($cc['category_name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea class="form-control" name="to_remarks" id="edit_to_remarks" rows="2" placeholder="Destination remarks"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: #fcfcfc; border-top: 1px solid #eee; border-radius: 0 0 8px 8px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-times"></span> Close</button>
                    <button type="submit" class="btn btn-primary"><span class="fa fa-save"></span> Update Entry</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
    <div class="modal-dialog" role="document">
        <form method="post" action="<?php echo site_url('contra-entry'); ?>">
            <input type="hidden" name="mode" value="Delete">
            <input type="hidden" name="contra_entry_id" id="delete_contra_entry_id">
            <div class="modal-content" style="border-radius: 8px;">
                <div class="modal-header bg-danger" style="border-radius: 8px 8px 0 0;">
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-white" id="deleteModalLabel" style="font-weight:600;"><span class="fa fa-trash"></span> Delete Contra Entry</h4>
                </div>
                <div class="modal-body" style="padding: 20px 25px; font-size: 15px;">
                    Are you sure you want to delete this contra entry? This action cannot be undone.
                </div>
                <div class="modal-footer" style="background: #fcfcfc; border-top: 1px solid #eee; border-radius: 0 0 8px 8px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><span class="fa fa-trash"></span> Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>
