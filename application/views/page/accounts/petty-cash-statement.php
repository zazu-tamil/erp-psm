<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<section class="content-header">
    <h1>Cash Statement</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Accounts</a></li>
        <li class="active">Cash Statement</li>
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
            <form method="post" action="<?php echo site_url('petty-cash-statement') ?>" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>From Date</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="date" class="form-control pull-right" id="srch_from_date" name="srch_from_date" 
                                   value="<?php echo set_value('srch_from_date', $srch_from_date); ?>" required="true">
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label>To Date</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="date" class="form-control pull-right" id="srch_to_date" name="srch_to_date" 
                                   value="<?php echo set_value('srch_to_date', $srch_to_date); ?>" required="true">
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Cash Category</label>
                        <?php echo form_dropdown('srch_cash_category_id', $cash_categories_opt, set_value('srch_cash_category_id', $srch_cash_category_id), 'id="srch_cash_category_id" class="form-control select2"'); ?>
                    </div>
                    <div class="form-group col-md-2 text-left">
                        <br />
                        <button class="btn btn-success" name="btn_show" value="Show"><i class="fa fa-search"></i> Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Box -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h4 class="box-title" style="display:inline-block; font-size:18px;">
                Statement Period: [ <?php echo date('d-m-Y', strtotime($srch_from_date)); ?> to <?php echo date('d-m-Y', strtotime($srch_to_date)); ?> ]
            </h4>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-primary btn-sm btnexp no-print" style="margin-right: 5px;">
                    <i class="fa fa-file-excel-o"></i> Export Excel
                </button>
                <button type="button" class="btn btn-default btn-sm no-print" onclick="window.print();">
                    <i class="fa fa-print"></i> Print Report
                </button>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-striped table-bordered table-hover" id="statement-table">
                <thead>
                    <tr class="bg-gray-light">
                        <th style="width: 50px;">#</th>
                        <th style="width: 100px;">Date</th>
                        <th style="width: 120px;">V.No</th>
                        <th style="width: 150px;">Transaction Type</th>
                        <th style="width: 150px;">Cash Category</th>
                        <th>Particulars / Remarks</th>
                        <th class="text-right" style="width: 130px;">Cash In (Inward)</th>
                        <th class="text-right" style="width: 130px;">Cash Out (Outward)</th>
                        <th class="text-right" style="width: 140px;">Running Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Opening Balance Row -->
                    <tr class="text-bold bg-warning" style="background-color: #fcf8e3 !important;">
                        <td>1</td>
                        <td><?php echo date('d-m-Y', strtotime($srch_from_date)); ?></td>
                        <td>-</td>
                        <td colspan="3"><strong>Opening Balance</strong></td>
                        <td class="text-right">-</td>
                        <td class="text-right">-</td>
                        <td class="text-right text-bold">
                            <?php echo number_format($opening_balance, 3); ?>
                        </td>
                    </tr>

                    <?php 
                    $running_balance = $opening_balance;
                    $tot_in = 0;
                    $tot_out = 0;
                    $serial = 2;

                    if (!empty($records)) {
                        foreach ($records as $row) {
                            $running_balance += ((float)$row['amount_in'] - (float)$row['amount_out']);
                            $tot_in += (float)$row['amount_in'];
                            $tot_out += (float)$row['amount_out'];
                            
                            $party_remarks = "";
                            if (!empty($row['party_name'])) {
                                $party_remarks .= "<strong>" . htmlspecialchars($row['party_name']) . "</strong>";
                            }
                            if (!empty($row['remarks'])) {
                                if (!empty($party_remarks)) {
                                    $party_remarks .= " - ";
                                }
                                $party_remarks .= htmlspecialchars($row['remarks']);
                            }
                            if (empty($party_remarks)) {
                                $party_remarks = "-";
                            }
                    ?>
                        <tr>
                            <td><?php echo $serial++; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($row['tr_date'])); ?></td>
                            <td><?php echo !empty($row['ref_no']) ? htmlspecialchars($row['ref_no']) : '-'; ?></td>
                            <td>
                                <span class="label label-default" style="font-size: 11px;">
                                    <?php echo htmlspecialchars($row['tr_type']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-green">Cash</span>
                                <?php if (!empty($row['cash_category_name'])) { ?>
                                    <br><small class="text-bold" style="display:block;"><?php echo htmlspecialchars($row['cash_category_name']); ?></small>
                                <?php } ?>
                            </td>
                            <td><?php echo $party_remarks; ?></td>
                            <td class="text-right text-green">
                                <?php echo (float)$row['amount_in'] > 0 ? number_format((float)$row['amount_in'], 3) : '-'; ?>
                            </td>
                            <td class="text-right text-red">
                                <?php echo (float)$row['amount_out'] > 0 ? number_format((float)$row['amount_out'], 3) : '-'; ?>
                            </td>
                            <td class="text-right text-bold">
                                <?php echo number_format($running_balance, 3); ?>
                            </td>
                        </tr>
                    <?php 
                        } 
                    } else { 
                    ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">No transactions found for the selected period and account filter.</td>
                        </tr>
                    <?php 
                    } 
                    ?>
                </tbody>
                <tfoot>
                    <tr class="bg-gray-light text-bold" style="font-size: 15px; border-top: 2px solid #ddd;">
                        <td colspan="6" class="text-right">Total Transacted:</td>
                        <td class="text-right text-green"><?php echo number_format($tot_in, 3); ?></td>
                        <td class="text-right text-red"><?php echo number_format($tot_out, 3); ?></td>
                        <td class="text-right">-</td>
                    </tr>
                    <tr class="bg-warning text-bold" style="font-size: 16px; background-color: #fcf8e3 !important;">
                        <td colspan="6" class="text-right">Closing Balance:</td>
                        <td colspan="2" class="text-center">
                            <?php 
                            if ($running_balance >= 0) {
                                echo '<span class="text-success text-bold">DR (Positive)</span>';
                            } else {
                                echo '<span class="text-danger text-bold">CR (Negative)</span>';
                            }
                            ?>
                        </td>
                        <td class="text-right text-bold" style="border-bottom: 3px double #333;">
                            <?php echo number_format($running_balance, 3); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>
