<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Supplier Reports</a></li>
        <li class="active"><?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">

    <!-- KPI Summary Row -->
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-money"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Paid (Pending Goods)</span>
                    <span class="info-box-number"><?php echo number_format($kpi_total_paid, 3); ?></span>
                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                    <span class="progress-description">Disbursed with missing inward</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Pending Qty</span>
                    <span class="info-box-number"><?php echo number_format($kpi_total_pending_qty, 2); ?></span>
                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                    <span class="progress-description">Units yet to be received</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Inward Not Entered</span>
                    <span class="info-box-number"><?php echo $kpi_not_entered_count; ?></span>
                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                    <span class="progress-description">0% Material received</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-orange">
                <span class="info-box-icon"><i class="fa fa-truck"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Partial Inward</span>
                    <span class="info-box-number"><?php echo $kpi_partial_count; ?></span>
                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                    <span class="progress-description">Partially delivered</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title text-white"><i class="fa fa-filter"></i> Search Filter</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form method="post" action="" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="srch_from_date">Payment From Date</label>
                        <input type="date" name="srch_from_date" id="srch_from_date" class="form-control"
                            value="<?php echo set_value('srch_from_date', $srch_from_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_to_date">Payment To Date</label>
                        <input type="date" name="srch_to_date" id="srch_to_date" class="form-control"
                            value="<?php echo set_value('srch_to_date', $srch_to_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="vendor_id">Vendor Name</label>
                        <?php echo form_dropdown('vendor_id', $vendor_opt, set_value('vendor_id', $vendor_id), 'id="vendor_id" class="form-control"'); ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="payment_type">Payment Type</label>
                        <?php 
                        $pay_type_opts = [
                            '' => 'All Payments',
                            'Advance Payment' => 'Advance Payment Only',
                            'Bill Payment' => 'Bill/Invoice Payment Only'
                        ];
                        echo form_dropdown('payment_type', $pay_type_opts, set_value('payment_type', $payment_type), 'id="payment_type" class="form-control"'); 
                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="inward_status">Inward Status</label>
                        <?php 
                        $inward_status_opts = [
                            '' => 'All Pending (Not Entered + Partial)',
                            'Inward Not Entered' => 'Inward Not Entered (0% Received)',
                            'Partial Inward' => 'Partial Inward (Partially Received)'
                        ];
                        echo form_dropdown('inward_status', $inward_status_opts, set_value('inward_status', $inward_status), 'id="inward_status" class="form-control"'); 
                        ?>
                    </div>

                    <div class="form-group col-md-6" style="margin-top: 25px;">
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show Report</button>
                        <a href="<?php echo site_url('vendor-paid-material-pending-inward-report?reset=1'); ?>" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</a>
                        <button type="submit" name="export_excel" value="1" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Export Excel (Server-side)</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Box -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-list"></i> Vendor Paid Material Pending Inward Records</h3>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped" id="tbl-data">
                <thead class="bg-gray">
                    <tr style="font-weight:bold;">
                        <th style="width: 40px;">S.No</th>
                        <th>Payment Date</th>
                        <th>Payment Type</th>
                        <th>Vendor Name</th>
                        <th>PO No & Date</th>
                        <th>Invoice No & Date</th>
                        <th class="text-right">Paid Amount</th>
                        <th class="text-right">Ordered Qty</th>
                        <th class="text-right">Inward Qty</th>
                        <th class="text-right">Pending Qty</th>
                        <th class="text-center">Inward Status</th>
                        <th class="text-center">Aging (Days)</th>
                        <th class="text-center" style="width: 90px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $sum_paid_amount = 0;
                $sum_ordered_qty = 0;
                $sum_inward_qty = 0;
                $sum_pending_qty = 0;

                if (!empty($record_list)):
                    foreach ($record_list as $i => $row):
                        $sum_paid_amount += $row['paid_amount'];
                        $sum_ordered_qty += $row['ordered_qty'];
                        $sum_inward_qty += $row['inward_qty'];
                        $sum_pending_qty += $row['pending_qty'];

                        // Calculate days elapsed since payment
                        $aging_days = 0;
                        if (!empty($row['payment_date'])) {
                            $pay_ts = strtotime($row['payment_date']);
                            $now_ts = strtotime(date('Y-m-d'));
                            $aging_days = max(0, floor(($now_ts - $pay_ts) / (60 * 60 * 24)));
                        }
                ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo !empty($row['payment_date']) ? date('d/m/Y', strtotime($row['payment_date'])) : '-'; ?></td>
                        <td>
                            <?php if ($row['payment_type'] == 'Advance Payment'): ?>
                                <span class="label label-info"><i class="fa fa-bolt"></i> Advance</span>
                            <?php else: ?>
                                <span class="label label-primary"><i class="fa fa-file-text-o"></i> Bill Payment</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($row['vendor_name']); ?></strong></td>
                        <td>
                            <?php if (!empty($row['po_no'])): ?>
                                <?php echo htmlspecialchars($row['po_no']); ?>
                                <?php if (!empty($row['po_date'])): ?>
                                    <br><small class="text-muted"><i class="fa fa-calendar"></i> <?php echo date('d/m/Y', strtotime($row['po_date'])); ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">Direct / None</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['invoice_no'])): ?>
                                <?php echo htmlspecialchars($row['invoice_no']); ?>
                                <?php if (!empty($row['invoice_date'])): ?>
                                    <br><small class="text-muted"><i class="fa fa-calendar"></i> <?php echo date('d/m/Y', strtotime($row['invoice_date'])); ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td align="right" style="font-weight: bold; color: #0073b7;">
                            <?php echo number_format($row['paid_amount'], 3); ?>
                        </td>
                        <td align="right"><?php echo number_format($row['ordered_qty'], 2); ?></td>
                        <td align="right" style="color: green;"><?php echo number_format($row['inward_qty'], 2); ?></td>
                        <td align="right" style="font-weight: bold; color: red;">
                            <?php echo number_format($row['pending_qty'], 2); ?>
                        </td>
                        <td class="text-center">
                            <?php 
                            if ($row['inward_status'] == 'Inward Not Entered') {
                                echo '<span class="label label-danger"><i class="fa fa-times-circle"></i> Not Entered</span>';
                            } elseif ($row['inward_status'] == 'Partial Inward') {
                                echo '<span class="label label-warning"><i class="fa fa-hourglass-half"></i> Partial Inward</span>';
                            } else {
                                echo '<span class="label label-default">' . htmlspecialchars($row['inward_status']) . '</span>';
                            }
                            ?>
                        </td>
                        <td class="text-center">
                            <?php if ($aging_days > 30): ?>
                                <span class="badge bg-red"><?php echo $aging_days; ?> d</span>
                            <?php elseif ($aging_days > 15): ?>
                                <span class="badge bg-yellow"><?php echo $aging_days; ?> d</span>
                            <?php else: ?>
                                <span class="badge bg-blue"><?php echo $aging_days; ?> d</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if (!empty($row['vendor_po_id']) || !empty($row['invoice_id'])): ?>
                                <button type="button" class="btn btn-info btn-xs btn-view-items"
                                    data-po-id="<?php echo (int)($row['vendor_po_id'] ?? 0); ?>"
                                    data-invoice-id="<?php echo (int)($row['invoice_id'] ?? 0); ?>"
                                    data-payment-type="<?php echo htmlspecialchars($row['payment_type']); ?>">
                                    <i class="fa fa-eye"></i> Items
                                </button>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php 
                    endforeach;
                endif; 
                ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background-color: #f3f4f5; font-size: 14px;">
                        <td colspan="6" class="text-right">TOTAL:</td>
                        <td align="right" style="color: #0073b7;"><?php echo number_format($sum_paid_amount, 3); ?></td>
                        <td align="right"><?php echo number_format($sum_ordered_qty, 2); ?></td>
                        <td align="right" style="color: green;"><?php echo number_format($sum_inward_qty, 2); ?></td>
                        <td align="right" style="color: red;"><?php echo number_format($sum_pending_qty, 2); ?></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</section>

<!-- Material Inward Item Comparison Modal -->
<div class="modal fade" id="itemsModal" tabindex="-1" role="dialog" aria-labelledby="itemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="itemsModalLabel" style="color: white;">
                    <i class="fa fa-cubes"></i> Ordered Material vs Inward Received Details
                </h4>
            </div>
            <div class="modal-body" id="itemsModalBody">
                <!-- Loaded dynamically via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>

