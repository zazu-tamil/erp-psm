<?php
// If Excel Export requested, output clean formatted Excel table
if (!empty($is_excel)) {
    ?>
    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 11pt; }
            th { background-color: #1f497d; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; padding: 6px; }
            td { border: 1px solid #d9d9d9; padding: 5px; vertical-align: middle; }
            .category-header { background-color: #2c3e50; color: #ffffff; font-weight: bold; font-size: 12pt; }
            .group-header { background-color: #dce6f1; color: #1f497d; font-weight: bold; }
            .group-total { background-color: #f2f2f2; font-weight: bold; }
            .grand-total { background-color: #1f497d; color: #ffffff; font-weight: bold; font-size: 12pt; border: 2px solid #000000; }
            .text-right { text-align: right; }
            .text-center { text-align: center; }
            .text-left { text-align: left; }
        </style>
    </head>
    <body>
        <table>
            <tr>
                <td colspan="7" class="text-center" style="font-size: 16pt; font-weight: bold;"><?php echo defined('COMPANY') ? COMPANY : 'ERP SYSTEM'; ?></td>
            </tr>
            <tr>
                <td colspan="7" class="text-center" style="font-size: 13pt; font-weight: bold;">TRIAL BALANCE (GENERAL LEDGER REPORT)</td>
            </tr>
            <tr>
                <td colspan="7" class="text-center" style="font-size: 10pt; color: #555555;">
                    Period: <?php echo date('d-M-Y', strtotime($srch_from_date)); ?> to <?php echo date('d-M-Y', strtotime($srch_to_date)); ?>
                    <?php if (!empty($srch_ac_type)) echo " | Account Group: " . $srch_ac_type; ?>
                </td>
            </tr>
            <tr><td colspan="7"></td></tr>
            <thead>
                <tr>
                    <th rowspan="2" class="text-left">Particulars</th>
                    <th colspan="2">Opening Balance</th>
                    <th colspan="2">Period Transactions</th>
                    <th colspan="2">Closing Balance</th>
                </tr>
                <tr>
                    <th>Debit (BHD)</th>
                    <th>Credit (BHD)</th>
                    <th>Debit (BHD)</th>
                    <th>Credit (BHD)</th>
                    <th>Debit (BHD)</th>
                    <th>Credit (BHD)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $g_op_dr = $g_op_cr = $g_pr_dr = $g_pr_cr = $g_cl_dr = $g_cl_cr = 0.0;
                $cat_grouped = [];
                foreach ($record_list as $row) {
                    $cat = !empty($row['account_category']) ? $row['account_category'] : 'GENERAL';
                    $head = !empty($row['account_head_name']) ? $row['account_head_name'] : 'General Accounts';
                    $cat_grouped[$cat][$head][] = $row;
                    $g_op_dr += (float)$row['opening_debit'];
                    $g_op_cr += (float)$row['opening_credit'];
                    $g_pr_dr += (float)$row['period_debit'];
                    $g_pr_cr += (float)$row['period_credit'];
                    $g_cl_dr += (float)$row['closing_debit'];
                    $g_cl_cr += (float)$row['closing_credit'];
                }

                foreach ($cat_grouped as $cat_name => $heads):
                ?>
                    <tr class="category-header">
                        <td colspan="7" style="background-color: #2c3e50; color: #ffffff; font-weight: bold;"><?php echo strtoupper(htmlspecialchars($cat_name)); ?></td>
                    </tr>
                    <?php foreach ($heads as $head_name => $items):
                        $h_op_dr = $h_op_cr = $h_pr_dr = $h_pr_cr = $h_cl_dr = $h_cl_cr = 0.0;
                    ?>
                        <tr class="group-header">
                            <td colspan="7" style="background-color: #dce6f1; color: #1f497d; font-weight: bold; padding-left: 15px;">
                                <?php echo htmlspecialchars($head_name); ?>
                            </td>
                        </tr>
                        <?php foreach ($items as $r):
                            $h_op_dr += (float)$r['opening_debit'];
                            $h_op_cr += (float)$r['opening_credit'];
                            $h_pr_dr += (float)$r['period_debit'];
                            $h_pr_cr += (float)$r['period_credit'];
                            $h_cl_dr += (float)$r['closing_debit'];
                            $h_cl_cr += (float)$r['closing_credit'];
                        ?>
                            <tr>
                                <td style="padding-left: 30px;"><?php echo htmlspecialchars($r['sub_account_head_name']); ?></td>
                                <td class="text-right"><?php echo ($r['opening_debit'] > 0) ? number_format($r['opening_debit'], 3) : '-'; ?></td>
                                <td class="text-right"><?php echo ($r['opening_credit'] > 0) ? number_format($r['opening_credit'], 3) : '-'; ?></td>
                                <td class="text-right"><?php echo ($r['period_debit'] > 0) ? number_format($r['period_debit'], 3) : '-'; ?></td>
                                <td class="text-right"><?php echo ($r['period_credit'] > 0) ? number_format($r['period_credit'], 3) : '-'; ?></td>
                                <td class="text-right"><?php echo ($r['closing_debit'] > 0) ? number_format($r['closing_debit'], 3) : '-'; ?></td>
                                <td class="text-right"><?php echo ($r['closing_credit'] > 0) ? number_format($r['closing_credit'], 3) : '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="group-total" style="background-color: #f2f2f2; font-weight: bold;">
                            <td style="padding-left: 20px;">Subtotal : <?php echo htmlspecialchars($head_name); ?></td>
                            <td class="text-right"><?php echo ($h_op_dr > 0) ? number_format($h_op_dr, 3) : '-'; ?></td>
                            <td class="text-right"><?php echo ($h_op_cr > 0) ? number_format($h_op_cr, 3) : '-'; ?></td>
                            <td class="text-right"><?php echo ($h_pr_dr > 0) ? number_format($h_pr_dr, 3) : '-'; ?></td>
                            <td class="text-right"><?php echo ($h_pr_cr > 0) ? number_format($h_pr_cr, 3) : '-'; ?></td>
                            <td class="text-right"><?php echo ($h_cl_dr > 0) ? number_format($h_cl_dr, 3) : '-'; ?></td>
                            <td class="text-right"><?php echo ($h_cl_cr > 0) ? number_format($h_cl_cr, 3) : '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <tr class="grand-total" style="background-color: #1f497d; color: #ffffff; font-weight: bold;">
                    <td>GRAND TOTAL</td>
                    <td class="text-right"><?php echo number_format($g_op_dr, 3); ?></td>
                    <td class="text-right"><?php echo number_format($g_op_cr, 3); ?></td>
                    <td class="text-right"><?php echo number_format($g_pr_dr, 3); ?></td>
                    <td class="text-right"><?php echo number_format($g_pr_cr, 3); ?></td>
                    <td class="text-right"><?php echo number_format($g_cl_dr, 3); ?></td>
                    <td class="text-right"><?php echo number_format($g_cl_cr, 3); ?></td>
                </tr>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit;
}
?>

<?php include_once(VIEWPATH . '/inc/header.php'); ?>

<?php
// Pre-calculate totals for KPIs and structured display
$grand_op_dr = 0.0; $grand_op_cr = 0.0;
$grand_pr_dr = 0.0; $grand_pr_cr = 0.0;
$grand_cl_dr = 0.0; $grand_cl_cr = 0.0;

$cat_grouped = [];
$non_zero_count = 0;

foreach ($record_list as $row) {
    $cat = !empty($row['account_category']) ? $row['account_category'] : 'GENERAL';
    $head = !empty($row['account_head_name']) ? $row['account_head_name'] : 'General Accounts';
    
    $cat_grouped[$cat][$head][] = $row;

    $od = (float)$row['opening_debit'];
    $oc = (float)$row['opening_credit'];
    $pd = (float)$row['period_debit'];
    $pc = (float)$row['period_credit'];
    $cd = (float)$row['closing_debit'];
    $cc = (float)$row['closing_credit'];

    $grand_op_dr += $od;
    $grand_op_cr += $oc;
    $grand_pr_dr += $pd;
    $grand_pr_cr += $pc;
    $grand_cl_dr += $cd;
    $grand_cl_cr += $cc;

    if ($od > 0.0001 || $oc > 0.0001 || $pd > 0.0001 || $pc > 0.0001 || $cd > 0.0001 || $cc > 0.0001) {
        $non_zero_count++;
    }
}

$cl_diff = abs($grand_cl_dr - $grand_cl_cr);
$is_balanced = ($cl_diff < 0.01);
?>

<section class="content-header">
    <h1>
        <i class="fa fa-balance-scale"></i> <?php echo htmlspecialchars($title); ?>
        <small>General Ledger Double-Entry Summary</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo site_url('admin'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#"><i class="fa fa-book"></i> Accounts</a></li>
        <li class="active">Trial Balance</li>
    </ol>
</section>

<section class="content">

    <!-- SEARCH FILTER & TOOLBAR CARD -->
    <div class="box box-primary tb-filter-box no-print">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-filter"></i> Report Options & Date Range</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('account-trial-balance'); ?>" id="frmsearch">
                <input type="hidden" name="export_excel" id="export_excel" value="0">
                <input type="hidden" name="view_mode" id="view_mode" value="<?php echo htmlspecialchars($view_mode); ?>">
                <input type="hidden" name="col_mode" id="col_mode" value="<?php echo htmlspecialchars($col_mode); ?>">

                <!-- Filter Controls Row -->
                <div class="row">
                    <div class="form-group col-md-3 col-sm-6">
                        <label><i class="fa fa-calendar"></i> From Date</label>
                        <input type="date" class="form-control" id="srch_from_date" name="srch_from_date"
                            value="<?php echo $srch_from_date; ?>" required>
                    </div>
                    <div class="form-group col-md-3 col-sm-6">
                        <label><i class="fa fa-calendar"></i> To Date</label>
                        <input type="date" class="form-control" id="srch_to_date" name="srch_to_date"
                            value="<?php echo $srch_to_date; ?>" required>
                    </div>
                    <div class="form-group col-md-3 col-sm-6">
                        <label><i class="fa fa-university"></i> Account Filter</label>
                        <?php echo form_dropdown('srch_ac_type', $ac_type_opt, set_value('srch_ac_type', $srch_ac_type), ' id="srch_ac_type" class="form-control" '); ?>
                    </div>
                    <div class="form-group col-md-3 col-sm-6 text-right" style="margin-top:25px;">
                        <button type="submit" class="btn btn-success" onclick="$('#export_excel').val('0');">
                            <i class="fa fa-search"></i> Generate
                        </button>
                        <button type="button" onclick="window.print()" class="btn btn-primary">
                            <i class="fa fa-print"></i> Print
                        </button>
                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-file-excel-o"></i> Excel <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="javascript:void(0)" onclick="triggerServerExcel()"><i class="fa fa-download text-green"></i> Server-side (.xls)</a></li>
                            <li><a href="javascript:void(0)" onclick="exportToExcel()"><i class="fa fa-table text-blue"></i> Instant Client (.xls)</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Presets & Display Switches Row -->
                <div class="row tb-toolbar-row">
                    <div class="col-md-6 col-sm-12">
                        <span class="toolbar-label"><i class="fa fa-clock-o"></i> Quick Dates:</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-default btn-preset" data-preset="month">This Month</button>
                            <button type="button" class="btn btn-default btn-preset" data-preset="quarter">This Quarter</button>
                            <button type="button" class="btn btn-default btn-preset" data-preset="current_fy">Current FY</button>
                            <button type="button" class="btn btn-default btn-preset" data-preset="prev_fy">Previous FY</button>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right tb-toggles-container">
                        <!-- Column Switcher -->
                        <div class="btn-group btn-group-sm tb-segmented-btn" role="group">
                            <button type="button" class="btn <?php echo ($col_mode == '6col') ? 'btn-primary' : 'btn-default'; ?>" id="btnCol6" title="Opening, Movement, Closing">6-Column</button>
                            <button type="button" class="btn <?php echo ($col_mode == '2col') ? 'btn-primary' : 'btn-default'; ?>" id="btnCol2" title="Closing Balance Only">2-Column</button>
                        </div>

                        <!-- View Mode Switcher -->
                        <div class="btn-group btn-group-sm tb-segmented-btn" role="group" style="margin-left: 10px;">
                            <button type="button" class="btn <?php echo ($view_mode == 'detailed') ? 'btn-primary' : 'btn-default'; ?>" id="btnViewDetailed" title="Show All Sub-accounts"><i class="fa fa-list"></i> Detailed</button>
                            <button type="button" class="btn <?php echo ($view_mode == 'summary') ? 'btn-primary' : 'btn-default'; ?>" id="btnViewSummary" title="Show Groups Only"><i class="fa fa-compress"></i> Summary</button>
                        </div>

                        <!-- Hide Zero Balances -->
                        <label class="checkbox-inline tb-hide-zero-label" style="margin-left: 12px;">
                            <input type="checkbox" id="chkHideZero" name="hide_zero" value="1" <?php echo ($hide_zero == '1') ? 'checked' : ''; ?>>
                            Hide Zero Balances
                        </label>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- EXECUTIVE KPI SUMMARY CARDS -->
    <div class="row tb-kpi-row no-print">
        <!-- KPI 1: Opening Balances -->
        <div class="col-md-3 col-sm-6">
            <div class="info-box tb-kpi-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-hourglass-start"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">OPENING BALANCE</span>
                    <span class="info-box-number">
                        Dr: <?php echo number_format($grand_op_dr, 3); ?>
                    </span>
                    <span class="tb-kpi-subtext text-muted">
                        Cr: <?php echo number_format($grand_op_cr, 3); ?> BHD
                    </span>
                </div>
            </div>
        </div>

        <!-- KPI 2: Period Transactions -->
        <div class="col-md-3 col-sm-6">
            <div class="info-box tb-kpi-box">
                <span class="info-box-icon bg-purple"><i class="fa fa-exchange"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">PERIOD MOVEMENTS</span>
                    <span class="info-box-number">
                        In: <?php echo number_format($grand_pr_dr, 3); ?>
                    </span>
                    <span class="tb-kpi-subtext text-muted">
                        Out: <?php echo number_format($grand_pr_cr, 3); ?> BHD
                    </span>
                </div>
            </div>
        </div>

        <!-- KPI 3: Closing Balances -->
        <div class="col-md-3 col-sm-6">
            <div class="info-box tb-kpi-box">
                <span class="info-box-icon bg-blue"><i class="fa fa-hourglass-end"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">CLOSING BALANCE</span>
                    <span class="info-box-number">
                        Dr: <?php echo number_format($grand_cl_dr, 3); ?>
                    </span>
                    <span class="tb-kpi-subtext text-muted">
                        Cr: <?php echo number_format($grand_cl_cr, 3); ?> BHD
                    </span>
                </div>
            </div>
        </div>

        <!-- KPI 4: Balancing Status -->
        <div class="col-md-3 col-sm-6">
            <div class="info-box tb-kpi-box <?php echo $is_balanced ? 'tb-kpi-balanced' : 'tb-kpi-variance'; ?>">
                <span class="info-box-icon <?php echo $is_balanced ? 'bg-green' : 'bg-red'; ?>">
                    <i class="fa <?php echo $is_balanced ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">STATUS</span>
                    <?php if ($is_balanced): ?>
                        <span class="info-box-number text-success" style="font-size: 16px;">
                            <i class="fa fa-shield"></i> BALANCED
                        </span>
                        <span class="tb-kpi-subtext text-muted">Variance: 0.000 BHD</span>
                    <?php else: ?>
                        <span class="info-box-number text-danger" style="font-size: 15px;">
                            DIFF: <?php echo number_format($cl_diff, 3); ?>
                        </span>
                        <span class="tb-kpi-subtext text-danger">Reconciliation Required</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN TRIAL BALANCE REPORT TABLE CARD -->
    <div class="box box-info tb-report-card">
        <!-- Print Header -->
        <div class="box-header with-border print-header text-center">
            <h2 class="company-name"><?php echo defined('COMPANY') ? COMPANY : 'ERP SYSTEM'; ?></h2>
            <h3 class="report-title">TRIAL BALANCE (GENERAL LEDGER REPORT)</h3>
            <p class="report-period">
                Period: <strong><?php echo date('d-M-Y', strtotime($srch_from_date)); ?></strong>
                to <strong><?php echo date('d-M-Y', strtotime($srch_to_date)); ?></strong>
                <?php if (!empty($srch_ac_type)): ?>
                    | Group: <strong><?php echo htmlspecialchars($srch_ac_type); ?></strong>
                <?php endif; ?>
            </p>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-bordered trial-table <?php echo ($col_mode == '2col') ? 'mode-2col' : 'mode-6col'; ?>" id="trialBalanceTable">
                <thead>
                    <tr>
                        <th rowspan="2" class="particulars-col">Account Title / Particulars</th>
                        <th colspan="2" class="text-center col-op">Opening Balance</th>
                        <th colspan="2" class="text-center col-pr">Period Movement</th>
                        <th colspan="2" class="text-center col-cl">Closing Balance</th>
                    </tr>
                    <tr>
                        <th class="text-right amount-col col-op sub-header-dr">Debit (BHD)</th>
                        <th class="text-right amount-col col-op sub-header-cr">Credit (BHD)</th>
                        <th class="text-right amount-col col-pr sub-header-dr">Debit (BHD)</th>
                        <th class="text-right amount-col col-pr sub-header-cr">Credit (BHD)</th>
                        <th class="text-right amount-col col-cl sub-header-dr">Debit (BHD)</th>
                        <th class="text-right amount-col col-cl sub-header-cr">Credit (BHD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cat_grouped)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding: 30px;">
                                <i class="fa fa-info-circle fa-2x"></i><br>
                                No transactions or accounts found for the specified period.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cat_grouped as $cat_name => $heads): 
                            $c_op_dr = $c_op_cr = $c_pr_dr = $c_pr_cr = $c_cl_dr = $c_cl_cr = 0.0;
                            foreach ($heads as $items) {
                                foreach ($items as $r) {
                                    $c_op_dr += (float)$r['opening_debit'];
                                    $c_op_cr += (float)$r['opening_credit'];
                                    $c_pr_dr += (float)$r['period_debit'];
                                    $c_pr_cr += (float)$r['period_credit'];
                                    $c_cl_dr += (float)$r['closing_debit'];
                                    $c_cl_cr += (float)$r['closing_credit'];
                                }
                            }
                        ?>
                            <!-- Category Header (ASSETS, LIABILITIES, etc.) -->
                            <tr class="category-header-row" data-category="<?php echo htmlspecialchars($cat_name); ?>">
                                <td colspan="7">
                                    <i class="fa fa-folder-open text-aqua"></i>
                                    <strong><?php echo strtoupper(htmlspecialchars($cat_name)); ?></strong>
                                </td>
                            </tr>

                            <?php foreach ($heads as $head_name => $items): 
                                $h_op_dr = $h_op_cr = $h_pr_dr = $h_pr_cr = $h_cl_dr = $h_cl_cr = 0.0;
                                foreach ($items as $r) {
                                    $h_op_dr += (float)$r['opening_debit'];
                                    $h_op_cr += (float)$r['opening_credit'];
                                    $h_pr_dr += (float)$r['period_debit'];
                                    $h_pr_cr += (float)$r['period_credit'];
                                    $h_cl_dr += (float)$r['closing_debit'];
                                    $h_cl_cr += (float)$r['closing_credit'];
                                }
                                $head_slug = md5($cat_name . '_' . $head_name);
                            ?>
                                <!-- Group Header Row (Clickable Accordion) -->
                                <tr class="group-header-row" data-target-group="<?php echo $head_slug; ?>">
                                    <td class="group-title-cell">
                                        <i class="fa fa-angle-down group-toggle-icon"></i>
                                        <strong><?php echo htmlspecialchars($head_name); ?></strong>
                                        <span class="badge bg-gray count-badge"><?php echo count($items); ?></span>
                                    </td>
                                    <td class="text-right group-summary-cell col-op">
                                        <strong><?php echo ($h_op_dr > 0) ? number_format($h_op_dr, 3) : '-'; ?></strong>
                                    </td>
                                    <td class="text-right group-summary-cell col-op">
                                        <strong><?php echo ($h_op_cr > 0) ? number_format($h_op_cr, 3) : '-'; ?></strong>
                                    </td>
                                    <td class="text-right group-summary-cell col-pr">
                                        <strong><?php echo ($h_pr_dr > 0) ? number_format($h_pr_dr, 3) : '-'; ?></strong>
                                    </td>
                                    <td class="text-right group-summary-cell col-pr">
                                        <strong><?php echo ($h_pr_cr > 0) ? number_format($h_pr_cr, 3) : '-'; ?></strong>
                                    </td>
                                    <td class="text-right group-summary-cell col-cl">
                                        <strong><?php echo ($h_cl_dr > 0) ? number_format($h_cl_dr, 3) : '-'; ?></strong>
                                    </td>
                                    <td class="text-right group-summary-cell col-cl">
                                        <strong><?php echo ($h_cl_cr > 0) ? number_format($h_cl_cr, 3) : '-'; ?></strong>
                                    </td>
                                </tr>

                                <!-- Sub-account / Ledger Item Rows -->
                                <?php foreach ($items as $row): 
                                    $r_op_dr = (float)$row['opening_debit'];
                                    $r_op_cr = (float)$row['opening_credit'];
                                    $r_pr_dr = (float)$row['period_debit'];
                                    $r_pr_cr = (float)$row['period_credit'];
                                    $r_cl_dr = (float)$row['closing_debit'];
                                    $r_cl_cr = (float)$row['closing_credit'];

                                    $is_zero = ($r_op_dr == 0 && $r_op_cr == 0 && $r_pr_dr == 0 && $r_pr_cr == 0 && $r_cl_dr == 0 && $r_cl_cr == 0);
                                ?>
                                    <tr class="ledger-row group-item-<?php echo $head_slug; ?> <?php echo $is_zero ? 'zero-bal-row' : ''; ?>">
                                        <td class="ledger-cell">
                                            <?php if (!empty($row['drilldown_url'])): ?>
                                                <a href="<?php echo $row['drilldown_url']; ?>" class="tb-drilldown-link" target="_blank" title="View Detailed Account Statement / Ledger">
                                                    <?php echo htmlspecialchars($row['sub_account_head_name']); ?>
                                                    <i class="fa fa-external-link drilldown-icon text-muted"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="ledger-name"><?php echo htmlspecialchars($row['sub_account_head_name']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right amount-cell col-op">
                                            <?php echo ($r_op_dr > 0) ? number_format($r_op_dr, 3) : '-'; ?>
                                        </td>
                                        <td class="text-right amount-cell col-op">
                                            <?php echo ($r_op_cr > 0) ? number_format($r_op_cr, 3) : '-'; ?>
                                        </td>
                                        <td class="text-right amount-cell col-pr">
                                            <?php echo ($r_pr_dr > 0) ? number_format($r_pr_dr, 3) : '-'; ?>
                                        </td>
                                        <td class="text-right amount-cell col-pr">
                                            <?php echo ($r_pr_cr > 0) ? number_format($r_pr_cr, 3) : '-'; ?>
                                        </td>
                                        <td class="text-right amount-cell col-cl">
                                            <?php echo ($r_cl_dr > 0) ? number_format($r_cl_dr, 3) : '-'; ?>
                                        </td>
                                        <td class="text-right amount-cell col-cl">
                                            <?php echo ($r_cl_cr > 0) ? number_format($r_cl_cr, 3) : '-'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <!-- Subtotal Row per Account Head -->
                                <tr class="head-subtotal-row group-subtotal-<?php echo $head_slug; ?>">
                                    <td class="subtotal-label">Subtotal: <?php echo htmlspecialchars($head_name); ?></td>
                                    <td class="text-right col-op"><?php echo ($h_op_dr > 0) ? number_format($h_op_dr, 3) : '-'; ?></td>
                                    <td class="text-right col-op"><?php echo ($h_op_cr > 0) ? number_format($h_op_cr, 3) : '-'; ?></td>
                                    <td class="text-right col-pr"><?php echo ($h_pr_dr > 0) ? number_format($h_pr_dr, 3) : '-'; ?></td>
                                    <td class="text-right col-pr"><?php echo ($h_pr_cr > 0) ? number_format($h_pr_cr, 3) : '-'; ?></td>
                                    <td class="text-right col-cl"><?php echo ($h_cl_dr > 0) ? number_format($h_cl_dr, 3) : '-'; ?></td>
                                    <td class="text-right col-cl"><?php echo ($h_cl_cr > 0) ? number_format($h_cl_cr, 3) : '-'; ?></td>
                                </tr>
                            <?php endforeach; ?>

                            <!-- Major Category Total Row (Assets Total, Liabilities Total, etc.) -->
                            <tr class="category-total-row">
                                <td class="cat-total-label"><strong>TOTAL <?php echo strtoupper(htmlspecialchars($cat_name)); ?></strong></td>
                                <td class="text-right col-op"><strong><?php echo ($c_op_dr > 0) ? number_format($c_op_dr, 3) : '-'; ?></strong></td>
                                <td class="text-right col-op"><strong><?php echo ($c_op_cr > 0) ? number_format($c_op_cr, 3) : '-'; ?></strong></td>
                                <td class="text-right col-pr"><strong><?php echo ($c_pr_dr > 0) ? number_format($c_pr_dr, 3) : '-'; ?></strong></td>
                                <td class="text-right col-pr"><strong><?php echo ($c_pr_cr > 0) ? number_format($c_pr_cr, 3) : '-'; ?></strong></td>
                                <td class="text-right col-cl"><strong><?php echo ($c_cl_dr > 0) ? number_format($c_cl_dr, 3) : '-'; ?></strong></td>
                                <td class="text-right col-cl"><strong><?php echo ($c_cl_cr > 0) ? number_format($c_cl_cr, 3) : '-'; ?></strong></td>
                            </tr>
                        <?php endforeach; ?>

                        <!-- GRAND TOTAL ROW -->
                        <tr class="grand-total-row">
                            <td class="grand-total-label"><strong>GRAND TOTAL</strong></td>
                            <td class="text-right col-op"><strong><?php echo number_format($grand_op_dr, 3); ?></strong></td>
                            <td class="text-right col-op"><strong><?php echo number_format($grand_op_cr, 3); ?></strong></td>
                            <td class="text-right col-pr"><strong><?php echo number_format($grand_pr_dr, 3); ?></strong></td>
                            <td class="text-right col-pr"><strong><?php echo number_format($grand_pr_cr, 3); ?></strong></td>
                            <td class="text-right col-cl"><strong><?php echo number_format($grand_cl_dr, 3); ?></strong></td>
                            <td class="text-right col-cl"><strong><?php echo number_format($grand_cl_cr, 3); ?></strong></td>
                        </tr>

                        <!-- DIFFERENCE ROW (Displayed when variance > 0.001) -->
                        <?php if ($cl_diff > 0.001): ?>
                            <tr class="difference-alert-row">
                                <td class="diff-label">
                                    <i class="fa fa-exclamation-triangle text-danger"></i>
                                    <strong>RECONCILIATION DIFFERENCE</strong>
                                </td>
                                <td class="text-right col-op">-</td>
                                <td class="text-right col-op">-</td>
                                <td class="text-right col-pr">-</td>
                                <td class="text-right col-pr">-</td>
                                <td class="text-right col-cl diff-amount">
                                    <strong><?php echo ($grand_cl_dr < $grand_cl_cr) ? number_format($cl_diff, 3) : '-'; ?></strong>
                                </td>
                                <td class="text-right col-cl diff-amount">
                                    <strong><?php echo ($grand_cl_cr < $grand_cl_dr) ? number_format($cl_diff, 3) : '-'; ?></strong>
                                </td>
                            </tr>
                        <?php endif; ?>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="box-footer no-print">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <p class="text-muted" style="margin: 5px 0;">
                        <i class="fa fa-info-circle text-blue"></i>
                        Showing <strong><?php echo $non_zero_count; ?></strong> active accounts with movements (out of <?php echo count($record_list); ?> total).
                    </p>
                </div>
                <div class="col-md-6 col-sm-6 text-right">
                    <p class="text-muted" style="margin: 5px 0;">
                        <i class="fa fa-clock-o"></i>
                        Report Generated: <strong><?php echo date('d-M-Y, h:i A'); ?></strong>
                    </p>
                </div>
            </div>
        </div>
    </div>

</section>

<!-- ENHANCED STYLING FOR TRIAL BALANCE -->
<style>
    /* Filter card enhancements */
    .tb-filter-box {
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }
    .tb-toolbar-row {
        margin-top: 10px;
        padding-top: 12px;
        border-top: 1px dashed #e4e7ea;
    }
    .toolbar-label {
        font-weight: 600;
        font-size: 12px;
        color: #555;
        margin-right: 6px;
    }
    .btn-preset {
        font-size: 11px;
        padding: 4px 9px;
    }
    .tb-segmented-btn .btn {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
    }
    .tb-hide-zero-label {
        font-size: 12px;
        font-weight: 600;
        color: #444;
        cursor: pointer;
    }

    /* Executive KPI box enhancements */
    .tb-kpi-row {
        margin-bottom: 5px;
    }
    .tb-kpi-box {
        border-radius: 6px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .tb-kpi-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
    }
    .tb-kpi-box .info-box-icon {
        border-top-left-radius: 6px;
        border-bottom-left-radius: 6px;
    }
    .tb-kpi-box .info-box-number {
        font-size: 16px;
        font-weight: bold;
        font-family: 'Consolas', 'Courier New', monospace;
    }
    .tb-kpi-subtext {
        font-size: 11px;
        display: block;
        margin-top: 2px;
    }
    .tb-kpi-balanced {
        border-right: 4px solid #00a65a;
    }
    .tb-kpi-variance {
        border-right: 4px solid #dd4b39;
    }

    /* Main Table Design */
    .tb-report-card {
        border-radius: 6px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }
    .trial-table {
        font-size: 12px;
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 0;
    }
    .trial-table th {
        background: #2b3e50 !important;
        color: #ffffff !important;
        text-align: center;
        padding: 8px 6px;
        font-weight: 600;
        border: 1px solid #1a252f !important;
        vertical-align: middle !important;
    }
    .trial-table th.sub-header-dr {
        background: #34495e !important;
    }
    .trial-table th.sub-header-cr {
        background: #3a536b !important;
    }
    .trial-table td {
        padding: 6px 10px;
        border: 1px solid #e1e6eb !important;
        vertical-align: middle;
    }

    /* Monospace alignment for numbers */
    .trial-table .text-right {
        text-align: right;
        font-family: 'Consolas', 'Courier New', monospace;
        font-size: 12px;
    }

    /* Category Headers (ASSETS, LIABILITIES, etc.) */
    .category-header-row {
        background: #1e2b37 !important;
        color: #ffffff !important;
        font-size: 13px;
        font-weight: bold;
    }
    .category-header-row td {
        padding: 8px 12px;
        border-top: 2px solid #0f171d !important;
        border-bottom: 2px solid #0f171d !important;
        letter-spacing: 0.5px;
    }

    /* Group Headers (Account Head) */
    .group-header-row {
        background: #ebf3f9 !important;
        color: #1f3a52;
        font-size: 12.5px;
        cursor: pointer;
        user-select: none;
    }
    .group-header-row:hover {
        background: #dbe9f4 !important;
    }
    .group-header-row td {
        padding: 7px 10px;
        border-top: 1.5px solid #b8d4e9 !important;
        border-bottom: 1.5px solid #b8d4e9 !important;
    }
    .group-toggle-icon {
        margin-right: 6px;
        color: #3c8dbc;
        font-size: 14px;
        transition: transform 0.2s ease;
    }
    .count-badge {
        font-size: 10px;
        margin-left: 6px;
        padding: 2px 6px;
        border-radius: 10px;
    }

    /* Sub-account / Ledger Item Rows */
    .ledger-row {
        background: #ffffff;
    }
    .ledger-row:hover {
        background: #f7fafc !important;
    }
    .ledger-cell {
        padding-left: 28px !important;
        position: relative;
    }
    .ledger-cell:before {
        content: "•";
        position: absolute;
        left: 15px;
        color: #3c8dbc;
        font-size: 16px;
        top: 4px;
    }
    .tb-drilldown-link {
        color: #2c3e50;
        text-decoration: none;
        display: inline-block;
    }
    .tb-drilldown-link:hover {
        color: #0073b7;
        text-decoration: underline;
    }
    .drilldown-icon {
        font-size: 10px;
        margin-left: 4px;
        opacity: 0.7;
    }

    /* Subtotal & Total Rows */
    .head-subtotal-row {
        background: #f9fbfd;
        font-weight: 600;
        color: #475569;
        font-size: 11.5px;
        border-bottom: 1px dashed #cbd5e1 !important;
    }
    .head-subtotal-row td {
        padding: 5px 10px;
    }
    .subtotal-label {
        padding-left: 22px !important;
        font-style: italic;
    }
    .category-total-row {
        background: #e2e8f0 !important;
        font-weight: bold;
        color: #1e293b;
        font-size: 12.5px;
    }
    .category-total-row td {
        padding: 8px 10px;
        border-top: 1.5px solid #94a3b8 !important;
        border-bottom: 1.5px solid #94a3b8 !important;
    }

    /* Grand Total Row */
    .grand-total-row {
        background: #0f172a !important;
        color: #ffffff !important;
        font-size: 14px;
        font-weight: bold;
    }
    .grand-total-row td {
        padding: 12px 10px;
        border: 2px solid #0f172a !important;
    }
    .grand-total-label {
        letter-spacing: 0.5px;
    }

    /* Difference Row */
    .difference-alert-row {
        background: #fff1f2 !important;
        color: #b91c1c !important;
        font-size: 13px;
        font-weight: bold;
    }
    .difference-alert-row td {
        padding: 10px;
        border: 2px solid #f87171 !important;
    }
    .diff-amount {
        color: #dc2626 !important;
        font-family: 'Consolas', monospace;
    }

    /* 2-Column Mode Handling */
    .trial-table.mode-2col .col-op,
    .trial-table.mode-2col .col-pr {
        display: none !important;
    }

    /* Zero Balances Hide */
    .trial-table.hide-zero-active .zero-bal-row {
        display: none !important;
    }

    /* Summary Mode: Hide Ledger Rows & Head Subtotals */
    .trial-table.mode-summary .ledger-row,
    .trial-table.mode-summary .head-subtotal-row {
        display: none !important;
    }
    .trial-table.mode-summary .group-toggle-icon {
        display: none;
    }

    /* Print Header & Media Rules */
    .print-header {
        display: none;
    }

    @media print {
        .no-print,
        .main-header,
        .main-sidebar,
        .breadcrumb,
        .content-header,
        .tb-filter-box,
        .tb-kpi-row,
        .drilldown-icon {
            display: none !important;
        }

        .content-wrapper, .content {
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
        }

        .print-header {
            display: block !important;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333333;
        }
        .print-header h2 {
            margin: 0 0 5px 0;
            font-size: 20px;
            font-weight: bold;
        }
        .print-header h3 {
            margin: 0 0 5px 0;
            font-size: 15px;
            font-weight: bold;
        }

        .box {
            border: none !important;
            box-shadow: none !important;
        }

        .trial-table {
            font-size: 10px !important;
        }
        .trial-table th {
            background: #333333 !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            padding: 5px 4px !important;
        }
        .category-header-row {
            background: #555555 !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .group-header-row {
            background: #eaeaea !important;
            color: #000000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .grand-total-row {
            background: #222222 !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>
