<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<section class="content-header">
    <h1>Opening Balance List</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Accounts</a></li> 
        <li class="active">Opening Balance List</li>
    </ol>
</section>

<!-- Main content -->
<section class="content"> 
    <div class="box box-info">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#add_modal">
                <span class="fa fa-plus-circle"></span> Add New Opening Balance
            </button>
        </div>
        <div class="box-body table-responsive"> 
            <table class="table table-hover table-bordered table-striped" id="opening_balance_list">
                <thead>
                    <tr>
                        <th style="width: 50px;">S.No</th>
                        <th style="width: 100px;">Date</th>  
                        <th>Account Group</th>  
                        <th>Account / Category Name</th>  
                        <th class="text-right" style="width: 140px;">Amount (BHD)</th>  
                        <th>Remarks</th>
                        <th class="text-center" style="width: 60px;">Edit</th>  
                        <th class="text-center" style="width: 60px;">Delete</th>  
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)) { ?>
                        <?php foreach ($record_list as $j => $ls) { 
                            $account_name = '-';
                            if ($ls['ac_type'] == 'Bank') {
                                $account_name = !empty($ls['bank_name']) ? $ls['bank_name'] . (!empty($ls['branch']) ? ' (' . $ls['branch'] . ')' : '') : 'All Banks';
                            } else if ($ls['ac_type'] == 'Cash') {
                                $account_name = !empty($ls['cash_category_name']) ? $ls['cash_category_name'] : 'All Cash';
                            }
                        ?> 
                        <tr> 
                            <td class="text-center"><?php echo ($j + 1); ?></td> 
                            <td><?php echo !empty($ls['opening_date']) ? date('d-m-Y', strtotime($ls['opening_date'])) : '-'; ?></td>   
                            <td>
                                <span class="label <?php echo $ls['ac_type'] === 'Bank' ? 'bg-blue' : 'bg-green'; ?>" style="font-size: 11px;">
                                    <?php echo $ls['ac_type']; ?>
                                </span>
                            </td>   
                            <td><strong><?php echo htmlspecialchars($account_name); ?></strong></td>
                            <td class="text-right text-bold"><?php echo number_format((float) $ls['amount'], 3); ?></td>    
                            <td><?php echo !empty($ls['remarks']) ? htmlspecialchars($ls['remarks']) : '-'; ?></td>
                            <td class="text-center">
                                <button data-toggle="modal" data-target="#edit_modal" value="<?php echo $ls['opening_balance_id']; ?>" class="edit_record btn btn-primary btn-xs" title="Edit"><i class="fa fa-edit"></i></button>
                            </td>                                  
                            <td class="text-center">
                                <button value="<?php echo $ls['opening_balance_id']; ?>" class="del_record btn btn-danger btn-xs" title="Delete"><i class="fa fa-remove"></i></button>
                            </td>                                      
                        </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="add_modal" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form method="post" action="<?php echo site_url('opening-balance-list'); ?>" id="frmadd">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="addModalLabel"><strong>Add Opening Balance</strong></h4>
                        <input type="hidden" name="mode" value="Add" />
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Date <span class="text-red">*</span></label>
                            <input class="form-control" type="date" name="opening_date" id="add_opening_date" value="<?php echo date('Y-m-d'); ?>" required>                                             
                        </div> 
                        <div class="form-group">
                            <label>Account Group <span class="text-red">*</span></label>
                            <select class="form-control" name="ac_type" id="add_ac_type" required>
                                <option value="">Select Account Group</option>
                                <option value="Bank">Bank</option>
                                <option value="Cash">Cash</option>
                            </select>
                        </div>
                        <div class="form-group" id="add_bank_wrapper" style="display: none;">
                            <label>Bank Account <span class="text-red">*</span></label>
                            <?php echo form_dropdown('bank_id', $bank_opt, '', 'id="add_bank_id" class="form-control" style="width: 100%;"'); ?>
                        </div>
                        <div class="form-group" id="add_cash_wrapper" style="display: none;">
                            <label>Cash Category <span class="text-red">*</span></label>
                            <?php echo form_dropdown('cash_category_id', $cash_category_opt, '', 'id="add_cash_category_id" class="form-control" style="width: 100%;"'); ?>
                        </div>
                        <div class="form-group">
                            <label>Amount (BHD)</label>
                            <input class="form-control text-right" type="number" step="any" name="amount" id="add_amount" value="0.000" placeholder="0.000">                                             
                        </div>  
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea class="form-control" name="remarks" id="add_remarks" rows="2" placeholder="Optional remarks"></textarea>
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
    <div class="modal fade" id="edit_modal" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form method="post" action="<?php echo site_url('opening-balance-list'); ?>" id="frmedit">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="editModalLabel"><strong>Edit Opening Balance</strong></h4>
                        <input type="hidden" name="mode" value="Edit" />
                        <input type="hidden" name="opening_balance_id" id="edit_opening_balance_id" />
                    </div>
                    <div class="modal-body"> 
                        <div class="form-group">
                            <label>Date <span class="text-red">*</span></label>
                            <input class="form-control" type="date" name="opening_date" id="edit_opening_date" value="" required>                                             
                        </div> 
                        <div class="form-group">
                            <label>Account Group <span class="text-red">*</span></label>
                            <select class="form-control" name="ac_type" id="edit_ac_type" required>
                                <option value="">Select Account Group</option>
                                <option value="Bank">Bank</option>
                                <option value="Cash">Cash</option>
                            </select>
                        </div>
                        <div class="form-group" id="edit_bank_wrapper" style="display: none;">
                            <label>Bank Account <span class="text-red">*</span></label>
                            <?php echo form_dropdown('bank_id', $bank_opt, '', 'id="edit_bank_id" class="form-control" style="width: 100%;"'); ?>
                        </div>
                        <div class="form-group" id="edit_cash_wrapper" style="display: none;">
                            <label>Cash Category <span class="text-red">*</span></label>
                            <?php echo form_dropdown('cash_category_id', $cash_category_opt, '', 'id="edit_cash_category_id" class="form-control" style="width: 100%;"'); ?>
                        </div>
                        <div class="form-group">
                            <label>Amount (BHD)</label>
                            <input class="form-control text-right" type="number" step="any" name="amount" id="edit_amount" placeholder="0.000">                                             
                        </div> 
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea class="form-control" name="remarks" id="edit_remarks" rows="2" placeholder="Optional remarks"></textarea>
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
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
