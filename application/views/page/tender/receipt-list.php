<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<section class="content-header">
    <h1>Receipt List</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Accounts</a></li>
        <li class="active">Receipt List</li>
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
            <form method="post" action="<?php echo site_url('receipt-list') ?>" id="frmsearch">
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

                    <div class="form-group col-md-3">
                        <label>Customer</label>
                        <?php echo form_dropdown('srch_customer_id', $customer_opt, set_value('srch_customer_id', $srch_customer_id), 'id="srch_customer_id" class="form-control"'); ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Payment Mode</label>
                        <?php echo form_dropdown('srch_payment_mode', $payment_mode_opt, set_value('srch_payment_mode', $srch_payment_mode), 'id="srch_payment_mode" class="form-control"'); ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Status</label>
                        <?php echo form_dropdown('srch_status', $status_opt, set_value('srch_status', $srch_status), 'id="srch_status" class="form-control"'); ?>
                    </div>

                    <div class="form-group col-md-2 text-left">
                        <br />
                        <button class="btn btn-success" name="btn_show" value="Show"><i class="fa fa-search"></i>
                            Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="box box-info">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#add_modal">
                <span class="fa fa-plus-circle"></span> Add New
            </button>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Receipt No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Payment Mode</th>
                        <th>Customer Ledger</th>
                        <th>Bank Ledger</th>
                        <th class="text-right">Amount</th>
                        <th>Voucher</th>
                        <th>Narration</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)) { ?>
                        <?php foreach ($record_list as $j => $ls) { ?>
                            <tr>
                                <td class="text-center"><?php echo ($j + 1 + $sno); ?></td>
                                <td class="text-center"><?php echo $ls['receipt_no']; ?></td>
                                <td><?php echo !empty($ls['receipt_date']) ? date('d-m-Y', strtotime($ls['receipt_date'])) : ''; ?></td>
                                <td><?php echo $ls['customer_name']; ?></td>
                                <td><?php echo $ls['payment_mode']; ?></td>
                                <td><?php echo $ls['customer_ledger_name']; ?></td>
                                <td><?php echo $ls['bank_ledger_name']; ?></td>
                                <td class="text-right"><?php echo number_format((float) $ls['amount'], 3); ?></td>
                                <td>
                                    <?php
                                    if (!empty($ls['voucher_id'])) {
                                        echo $ls['voucher_id'];
                                        if (!empty($ls['voucher_type'])) {
                                            echo "<br><span class='label label-info'>" . $ls['voucher_type'] . "</span>";
                                        }
                                        if (!empty($ls['voucher_date'])) {
                                            echo "<br><span class='text-muted'>" . date('d-m-Y', strtotime($ls['voucher_date'])) . "</span>";
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?php echo $ls['narration']; ?></td>
                                <td><?php echo $ls['status']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">No records found</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <?php if (!empty($pagination)) { ?>
                <div class="clearfix">
                    <?php echo $pagination; ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="add_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form method="post" action="<?php echo site_url('receipt-list'); ?>" id="frmadd">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title" id="scrollmodalLabel"><strong>Add Receipt</strong></h3>
                        <input type="hidden" name="mode" value="Add" />
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Receipt No</label>
                                <input class="form-control" type="text" name="receipt_no" id="receipt_no" value=""
                                    placeholder="Receipt No" required="true">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Receipt Date</label>
                                <input class="form-control" type="date" name="receipt_date" id="receipt_date"
                                    value="<?php echo date('Y-m-d'); ?>" required="true">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Customer</label>
                                <?php echo form_dropdown('customer_id', $customer_add_opt, set_value('customer_id'), 'id="customer_id" class="form-control" required="true"'); ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Voucher</label>
                                <?php echo form_dropdown('voucher_id', $voucher_opt, set_value('voucher_id'), 'id="voucher_id" class="form-control" required="true"'); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Payment Mode</label>
                                <select name="payment_mode" id="payment_mode" class="form-control" required="true">
                                    <option value="">Select</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6" id="bank_ledger_wrap" style="display:none;">
                                <label>Bank Ledger</label>
                                <?php echo form_dropdown('bank_ledger_id', $bank_ledger_opt, set_value('bank_ledger_id'), 'id="bank_ledger_id" class="form-control"'); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Amount</label>
                                <input class="form-control text-right" type="number" step="0.001" name="amount" id="amount"
                                    value="" required="true">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Narration</label>
                                <input class="form-control" type="text" name="narration" id="narration" value=""
                                    placeholder="Narration">
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
</section>
<?php include_once(VIEWPATH . '/inc/footer.php'); ?>

