<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<section class="content-header">
    <h1>Inward List</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Accounts</a></li>
        <li class="active">Inward List</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('inward-list') ?>" id="frmsearch">
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
                        <!-- /.input group -->
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
                        <!-- /.input group -->
                    </div>

                    <div class="form-group col-md-2 text-left">
                        <br />
                        <button class="btn btn-success" name="btn_show" value="Show'"><i class="fa fa-search"></i>
                            Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="box box-info">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#add_modal"><span
                    class="fa fa-plus-circle"></span> Add New </button>

        </div>
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>R.No</th>
                        <th>Inward Date</th>
                        <th>Company / Project</th>
                        <th>Account Head</th>
                        <th>Sub Account Head</th>
                        <th>Inward From</th>
                        <th>Amount</th>
                        <th>Remarks</th>
                        <th colspan="3" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($record_list as $j => $ls) { ?>
                        <tr>
                            <td class="text-center"><?php echo ($j + 1 + $sno); ?></td>
                            <td class="text-center"><?php echo $ls['prefix'] . $ls['vno']; ?><br /><i
                                    class="label label-info"><?php echo $ls['voucher_type_name'] ?></i></td>
                            <td><?php echo date('d-m-Y', strtotime($ls['inward_date'])) ?><br /><?php echo $ls['ac_type'] ?>
                            </td>
                            <td>
                                <?php echo $ls['company_name'] ?><br />
                                <span class="text-muted">
                                    <?php echo $ls['project_id'] ?>:
                                    <?php echo $ls['customer_name'] ?>:
                                    <?php echo ($ls['customer_branch_name'] != '' ? $ls['customer_branch_name'] : '-') ?><br />
                                    <?php echo $ls['work_name'] ?>
                                </span>
                            </td>
                            <td><?php echo $ls['account_head_name'] ?></td>
                            <td><?php echo $ls['sub_account_head_name'] ?></td>
                            <td><?php echo $ls['in_from'] ?></td>
                            <td><?php echo $ls['amount'] ?></td>
                            <td><?php echo $ls['remarks'] ?></td>
                            <td class="text-center">
                                <?php if (($this->session->userdata(SS_PFIX . 'user_type') == "Admin") || (($this->session->userdata(SS_PFIX . 'user_type') != 'Admin') && ($ls['days'] <= EDIT_ALLOW_DAYS) && ($this->session->userdata('cr_edit_flg') == '1'))) { ?>
                                    <button data-toggle="modal" data-target="#edit_modal"
                                        value="<?php echo $ls['cash_inward_id'] ?>" class="edit_record btn btn-primary btn-xs"
                                        title="Edit"><i class="fa fa-edit"></i></button>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo site_url('print-receipt/' . $ls['cash_inward_id']); ?>" target="_blank"
                                    class="btn btn-success btn-xs" title="Print Receipt"><i class="fa fa-print"></i></a>
                            </td>
                            <td class="text-center">
                                <?php if (($this->session->userdata('cr_user_type') == "Admin") || (($this->session->userdata('cr_user_type') != 'Admin') && ($ls['days'] <= EDIT_ALLOW_DAYS))) { ?>
                                    <button value="<?php echo $ls['cash_inward_id'] ?>" class="del_record btn btn-danger btn-xs"
                                        title="Delete"><i class="fa fa-remove"></i></button>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="modal fade" id="add_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <form method="post" action="" id="frmadd">
                            <div class="modal-header">

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel">Add Cash Inward Info</h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Date</label>
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input class="form-control datepicker" type="text" name="inward_date"
                                                id="inward_date" value="<?php echo date('Y-m-d'); ?>" required="true">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Company</label>
                                        <?php echo form_dropdown('company_id', array('' => 'Select') + $company_name_opt, set_value('company_id'), 'id="company_id" class="form-control" required'); ?>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Project</label>
                                        <?php echo form_dropdown('project_id', array('' => 'Select'), set_value('project_id'), 'id="project_id" class="form-control" required'); ?>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Account Group</label>
                                        <?php echo form_dropdown('ac_type', array('' => 'Select') + $ac_type_opt, set_value('ac_type'), ' id="ac_type" class="form-control" required="true"'); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Account Head</label>
                                        <?php echo form_dropdown('account_head_id', array('' => 'Select') + $account_head_opt, set_value('account_head_id'), ' id="account_head_id" class="form-control" required="true"'); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Sub-Account Head</label>
                                        <?php echo form_dropdown('sub_account_head_id', array('' => 'Select'), set_value('sub_account_head_id'), ' id="sub_account_head_id" class="form-control" required="true"'); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Inward From</label>
                                        <?php echo form_dropdown('sub_account_headlvl3_id', array('' => 'Select'), set_value('sub_account_headlvl3_id'), ' id="sub_account_headlvl3_id" class="form-control" '); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Amount</label>
                                        <input class="form-control text-right" type="number" step="any" name="amount"
                                            id="amount" value="">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Voucher Type</label>
                                        <?php echo form_dropdown('voucher_type_id', array('' => 'Select') + $voucher_type_opt, set_value('voucher_type_id'), ' id="voucher_type_id" class="form-control" required="true"'); ?>
                                    </div>
                                    <div class="form-group col-md-6 hide">
                                        <label>Status</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" value="Active" checked="true" />
                                                Active
                                            </label>
                                        </div>

                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Remarks</label>
                                        <?php echo form_textarea('remarks', '', 'class="form-control" id="remarks"') ?>
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

            <div class="modal fade" id="edit_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <form method="post" action="" id="frmedit">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel">Edit Cash Inward Info</h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="cash_inward_id" id="cash_inward_id" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Date</label>
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input class="form-control datepicker" type="text" name="inward_date"
                                                id="inward_date" value="<?php echo date('Y-m-d'); ?>" required="true">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Company</label>
                                        <?php echo form_dropdown('company_id', array('' => 'Select') + $company_name_opt, set_value('company_id'), 'id="company_id" class="form-control" required'); ?>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Project</label>
                                        <?php echo form_dropdown('project_id', array('' => 'Select') + $project_opt, set_value('project_id'), 'id="project_id" class="form-control" required'); ?>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Account Group</label>
                                        <?php echo form_dropdown('ac_type', array('' => 'Select') + $ac_type_opt, set_value('ac_type'), ' id="ac_type" class="form-control" required="true"'); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Account Head</label>
                                        <?php echo form_dropdown('account_head_id', array('' => 'Select') + $account_head_opt, set_value('account_head_id'), ' id="account_head_id" class="form-control" required="true"'); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Sub-Account Head</label>
                                        <?php echo form_dropdown('sub_account_head_id', array('' => 'Select'), set_value('sub_account_head_id'), ' id="sub_account_head_id" class="form-control" required="true"'); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Inward From</label>
                                        <?php echo form_dropdown('sub_account_headlvl3_id', array('' => 'Select'), set_value('sub_account_headlvl3_id'), ' id="sub_account_headlvl3_id" class="form-control" '); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Amount</label>
                                        <input class="form-control text-right" type="number" step="any" name="amount"
                                            id="amount" value="">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Voucher Type</label>
                                        <?php echo form_dropdown('voucher_type_id', array('' => 'Select') + $voucher_type_opt, set_value('voucher_type_id'), ' id="voucher_type_id" class="form-control" required="true"'); ?>
                                    </div>
                                    <div class="form-group col-md-6 hide">
                                        <label>Status</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" value="Active" checked="true" />
                                                Active
                                            </label>
                                        </div>

                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Remarks</label>
                                        <?php echo form_textarea('remarks', '', 'class="form-control" id="remarks"') ?>
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


        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <div class="form-group col-sm-6">
                <label>Total Records : <?php echo $total_records; ?></label>
            </div>
            <div class="form-group col-sm-6">
                <?php echo $pagination; ?>
            </div>
        </div>
        <!-- /.box-footer-->
    </div>
    <!-- /.box -->

</section>
<!-- /.content -->
<?php include_once(VIEWPATH . 'inc/footer.php'); ?>