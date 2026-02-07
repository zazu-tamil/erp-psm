<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Master</a></li>
        <li class="active"><?php echo $title; ?></li>
    </ol>
</section>

<section class="content">
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <!-- SEARCH FILTER BOX -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="srch_from_date">From Date</label>
                        <input type="date" name="srch_from_date" id="srch_from_date" class="form-control"
                            value="<?php echo set_value('srch_from_date', $srch_from_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_to_date">To Date</label>
                        <input type="date" name="srch_to_date" id="srch_to_date" class="form-control"
                            value="<?php echo set_value('srch_to_date', $srch_to_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label>Voucher Narration</label>
                        <?php echo form_dropdown('srch_voucher_narration_id', $voucher_narration_opt, set_value('srch_voucher_narration_id', $srch_voucher_narration_id), 'id="srch_voucher_narration_id" class="form-control"'); ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Voucher Type</label>
                        <?php echo form_dropdown('srch_voucher_type', $voucher_type_opt, set_value('srch_voucher_type', $srch_voucher_type), 'id="srch_voucher_type" class="form-control"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- VOUCHERS LIST BOX -->
    <div class="box box-success">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#add_modal">
                <span class="fa fa-plus-circle"></span> Add New Voucher
            </button>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped" id="company_list">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th>Voucher Date</th>
                        <th>Voucher Type</th>
                        <th>Voucher Narration</th>
                        <th>Status</th>
                        <th colspan="2" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)): ?>
                        <?php foreach ($record_list as $j => $ls) { ?>
                            <tr class="mb-3">
                                <td class="text-center"><?php echo ($sno + $j); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($ls['voucher_date'])); ?></td>
                                <td><?php echo $ls['voucher_type']; ?></td>
                                <td><?php echo $ls['narration']; ?></td>
                                <td>
                                    <span class="label label-<?php echo ($ls['status'] == 'Active') ? 'success' : 'danger'; ?>">
                                        <?php echo $ls['status']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button data-toggle="modal" data-target="#edit_modal"
                                        value="<?php echo $ls['voucher_id']; ?>" class="btn_edit_voucher btn btn-primary btn-xs"
                                        title="Edit"><i class="fa fa-edit"></i></button>
                                </td>
                                <td class="text-center">
                                    <button value="<?php echo $ls['voucher_id']; ?>" class="del_record btn btn-danger btn-xs"
                                        title="Delete"><i class="fa fa-remove"></i></button>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- ADD MODAL -->
            <div class="modal fade" id="add_modal" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?= site_url('vouchers-list'); ?>" id="frmadd">
                            <input type="hidden" name="mode" value="Add Voucher">

                            <div class="modal-header bg-primary">
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                <h4 class="modal-title"><strong>Voucher Entry</strong></h4>
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Voucher Date <span class="text-danger">*</span></label>
                                        <input type="date" name="voucher_date" id="add_voucher_date"
                                            class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Voucher Type <span class="text-danger">*</span></label>
                                        <?= form_dropdown('voucher_type', array('' => 'Select Voucher Type') + $voucher_type_opt, '', 'id="add_voucher_type" class="form-control" required'); ?>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Status</label>
                                        <select name="status" id="add_status" class="form-control">
                                            <option value="Active" selected>Active</option>
                                            <option value="InActive">InActive</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Narration</label>
                                        <textarea name="narration" id="add_narration" class="form-control" rows="2"
                                            placeholder="Enter voucher narration..." required></textarea>
                                    </div>
                                </div>

                                <hr>

                                <h4 class="bg-prim">
                                    <strong>Voucher Entries</strong>
                                    <button type="button" class="btn btn-success btn-sm pull-right" id="addRow">
                                        <i class="fa fa-plus"></i> Add Row
                                    </button>
                                </h4>
                                <p>&nbsp;</p>

                                <table class="table table-bordered" id="addVoucherTable">
                                    <thead>
                                        <tr class="bg-light-blue">
                                            <th>Ledger Account <span class="text-danger">*</span></th>
                                            <th width="20%">Debit (₹)</th>
                                            <th width="20%">Credit (₹)</th>
                                            <th class="text-center" width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <?= form_dropdown('ledger_id[]', array('' => 'Select Ledger Account') + $ledger_accounts_list_opt, '', 'class="form-control ledger-select" required'); ?>
                                            </td>
                                            <td>
                                                <input type="number" step="any" name="debit[]"
                                                    class="form-control debit" placeholder="0.00" value="0.00">
                                            </td>
                                            <td>
                                                <input type="number" step="any" name="credit[]"
                                                    class="form-control credit" placeholder="0.00" value="0.00">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm removeRow">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-gray">
                                            <td class="text-right"><strong>Totals:</strong></td>
                                            <td class="text-right">
                                                <strong>₹ <span id="addTotalDebit">0.00</span></strong>
                                            </td>
                                            <td class="text-right">
                                                <strong>₹ <span id="addTotalCredit">0.00</span></strong>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr id="balanceWarning" style="display:none;">
                                            <td colspan="4" class="text-center text-danger">
                                                <strong><i class="fa fa-warning"></i> Debit and Credit must be
                                                    equal!</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" id="saveVoucherBtn">
                                    <i class="fa fa-save"></i> Save Voucher
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- EDIT MODAL -->
            <div class="modal fade" id="edit_modal" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo site_url('vouchers-list'); ?>" id="frmedit">
                            <input type="hidden" name="mode" value="Edit Voucher" />
                            <input type="hidden" name="voucher_id" id="edit_voucher_id" value="" />

                            <div class="modal-header bg-primary">
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                <h4 class="modal-title"><strong>Edit Voucher</strong></h4>
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Voucher Date <span class="text-danger">*</span></label>
                                        <input type="date" name="voucher_date" id="voucher_date" class="form-control"
                                            required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Voucher Type <span class="text-danger">*</span></label>
                                        <?= form_dropdown('voucher_type', array('' => 'Select Voucher Type') + $voucher_type_opt, '', 'id="voucher_type" class="form-control" required'); ?>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="Active">Active</option>
                                            <option value="InActive">InActive</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Narration</label>
                                        <textarea name="narration" id="narration" class="form-control"
                                            rows="2"></textarea>
                                    </div>
                                </div>

                                <hr>

                                <h4 class="bg-prim">
                                    <strong>Voucher Entries</strong>
                                    <button type="button" class="btn btn-success btn-sm pull-right" id="editAddRow">
                                        <i class="fa fa-plus"></i> Add Row
                                    </button>
                                </h4>
                                <p>&nbsp;</p>

                                <table class="table table-bordered" id="editVoucherTable">
                                    <thead>
                                        <tr class="bg-light-blue">
                                            <th>Ledger Account <span class="text-danger">*</span></th>
                                            <th width="20%">Debit (₹)</th>
                                            <th width="20%">Credit (₹)</th>
                                            <th class="text-center" width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="editTableBody">

                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-gray">
                                            <td class="text-right"><strong>Totals:</strong></td>
                                            <td class="text-right">
                                                <strong>₹ <span id="editTotalDebit">0.00</span></strong>
                                            </td>
                                            <td class="text-right">
                                                <strong>₹ <span id="editTotalCredit">0.00</span></strong>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr id="editBalanceWarning" style="display:none;">
                                            <td colspan="4" class="text-center text-danger">
                                                <strong><i class="fa fa-warning"></i> Debit and Credit must be
                                                    equal!</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" id="updateVoucherBtn">
                                    <i class="fa fa-save"></i> Update Voucher
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
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

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>