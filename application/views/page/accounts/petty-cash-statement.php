<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<style>
    /* Modern Metric Cards Styling */
    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 16px 20px;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
        border-left: 5px solid #3c8dbc;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
    }
    .stat-card.opening { border-left-color: #f39c12; }
    .stat-card.purchases { border-left-color: #00a65a; }
    .stat-card.paid { border-left-color: #dd4b39; }
    .stat-card.closing { border-left-color: #605ca8; }
    
    .stat-card .label-text {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        color: #777d8c;
        margin-bottom: 6px;
        display: block;
    }
    .stat-card .value-text {
        font-size: 22px;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1.2;
    }
    .stat-card .icon-bg {
        position: absolute;
        right: 15px;
        bottom: 10px;
        font-size: 38px;
        color: rgba(0, 0, 0, 0.05);
        pointer-events: none;
    }
    @media print {
        @page {
            size: landscape;
            margin: 10mm;
        }
        body {
            font-size: 11px;
        }
        .table > thead > tr > th, 
        .table > tbody > tr > th, 
        .table > tfoot > tr > th, 
        .table > thead > tr > td, 
        .table > tbody > tr > td, 
        .table > tfoot > tr > td {
            padding: 4px !important;
            font-size: 11px !important;
        }
        .badge, .label {
            border: 1px solid #000;
            color: #000 !important;
            background: transparent !important;
        }
    }
</style>
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
                                   value="<?php echo set_value('srch_from_date', $srch_from_date); ?>" 
                                   min="<?php echo !empty($op_date) ? $op_date : ''; ?>" required="true">
                        </div>
                        <?php if (!empty($op_date)) { ?>
                            <small class="text-muted"><i class="fa fa-info-circle text-info"></i> Opening: <?php echo date('d-m-Y', strtotime($op_date)); ?></small>
                        <?php } ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label>To Date</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="date" class="form-control pull-right" id="srch_to_date" name="srch_to_date" 
                                   value="<?php echo set_value('srch_to_date', $srch_to_date); ?>" 
                                   min="<?php echo !empty($op_date) ? $op_date : ''; ?>" required="true">
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

    <!-- Pre-calculate period statistics -->
    <?php
    $tot_in_calc = 0.000;
    $tot_out_calc = 0.000;
    if (!empty($records)) {
        foreach ($records as $r) {
            $tot_in_calc += (float)$r['amount_in'];
            $tot_out_calc += (float)$r['amount_out'];
        }
    }
    $closing_balance_calc = $opening_balance + $tot_in_calc - $tot_out_calc;
    ?>

    <!-- Modern Summary Statistics Grid -->
    <div class="row no-print" style="margin-bottom: 5px;">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card opening">
                <span class="label-text"><i class="fa fa-folder-open text-warning"></i> Opening Balance</span>
                <div class="value-text text-warning"><?php echo number_format($opening_balance, 3); ?> <small style="font-size: 13px; font-weight: normal; color: #777;">BHD</small></div>
                <i class="fa fa-folder-open icon-bg"></i>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card purchases">
                <span class="label-text"><i class="fa fa-arrow-circle-down text-green"></i> Total Cash In (Inward)</span>
                <div class="value-text text-green"><?php echo number_format($tot_in_calc, 3); ?> <small style="font-size: 13px; font-weight: normal; color: #777;">BHD</small></div>
                <i class="fa fa-arrow-down icon-bg"></i>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card paid">
                <span class="label-text"><i class="fa fa-arrow-circle-up text-red"></i> Total Cash Out (Outward)</span>
                <div class="value-text text-red"><?php echo number_format($tot_out_calc, 3); ?> <small style="font-size: 13px; font-weight: normal; color: #777;">BHD</small></div>
                <i class="fa fa-arrow-up icon-bg"></i>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card closing">
                <span class="label-text"><i class="fa fa-balance-scale text-purple"></i> Closing Balance</span>
                <div class="value-text" style="color: #605ca8;">
                    <?php echo number_format($closing_balance_calc, 3); ?> 
                    <small style="font-size: 12px; font-weight: bold; padding: 2px 6px; border-radius: 4px; background: <?php echo $closing_balance_calc >= 0 ? '#e8f5e9; color: #2e7d32;' : '#ffebee; color: #c62828;'; ?>">
                        <?php echo $closing_balance_calc >= 0 ? 'DR' : 'CR'; ?>
                    </small>
                </div>
                <i class="fa fa-balance-scale icon-bg"></i>
            </div>
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
                        <td colspan="3">
                            <span class="label bg-orange" style="font-size: 11px; margin-right: 5px;">
                                <i class="fa fa-folder-open"></i>
                            </span>
                            <strong>Opening Balance (as of <?php echo date('d-m-Y', strtotime($srch_from_date)); ?>)</strong>
                        </td>
                        <td class="text-right">-</td>
                        <td class="text-right">-</td>
                        <td class="text-right text-bold" style="font-size: 14px;">
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
                                <?php
                                $type_badge = 'label-default';
                                if ($row['tr_type'] == 'Customer Receipt') $type_badge = 'label-success';
                                elseif ($row['tr_type'] == 'Customer Advance') $type_badge = 'label-info';
                                elseif ($row['tr_type'] == 'Supplier Payment') $type_badge = 'label-danger';
                                elseif ($row['tr_type'] == 'Supplier Advance') $type_badge = 'label-warning';
                                elseif ($row['tr_type'] == 'Contra Inward') $type_badge = 'bg-purple';
                                elseif ($row['tr_type'] == 'Contra Outward') $type_badge = 'bg-maroon';
                                ?>
                                <span class="label <?php echo $type_badge; ?>" style="font-size: 11px;">
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
