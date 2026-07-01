<?php include_once(VIEWPATH . '/inc/header.php'); ?>

<section class="content-header">
    <h1>
        <i class="fa fa-balance-scale"></i> <?php echo htmlspecialchars($title); ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Reports</a></li>
        <li class="active">Account Trial Balance</li>
    </ol>
</section>

<section class="content">

    <!-- SEARCH FILTER -->
    <div class="box box-primary no-print">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-filter"></i> Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('account-trial-balance') ?>" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label><i class="fa fa-calendar"></i> From Date</label>
                        <input type="date" class="form-control" id="srch_from_date" name="srch_from_date"
                            value="<?php echo $srch_from_date; ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label><i class="fa fa-calendar"></i> To Date</label>
                        <input type="date" class="form-control" id="srch_to_date" name="srch_to_date"
                            value="<?php echo $srch_to_date; ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label><i class="fa fa-university"></i> Account Group (Cash/Bank)</label>
                        <?php echo form_dropdown('srch_ac_type', array('' => 'All') + $ac_type_opt, set_value('srch_ac_type', $srch_ac_type), ' id="srch_ac_type" class="form-control" '); ?>
                    </div>
                    <div class="form-group col-md-3" style="margin-top:25px;">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-search"></i> Show Report
                        </button>
                        <button type="button" onclick="window.print()" class="btn btn-primary">
                            <i class="fa fa-print"></i> Print
                        </button>
                        <button type="button" onclick="exportToExcel()" class="btn btn-info">
                            <i class="fa fa-file-excel-o"></i> Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- REPORT TABLE -->
    <div class="box box-info">
        <div class="box-header with-border print-header text-center">
            <h2 class="company-name"><?php echo defined('COMPANY') ? COMPANY : 'Your Company Name'; ?></h2>
            <h3 class="report-title">TRIAL BALANCE (ACCOUNTS BOOK)</h3>
            <p class="report-period">
                Period: <strong><?php echo date('d-M-Y', strtotime($srch_from_date)); ?></strong>
                to <strong><?php echo date('d-M-Y', strtotime($srch_to_date)); ?></strong>
                <?php if (!empty($srch_ac_type)) {
                    echo " | Group: <strong>" . $srch_ac_type . "</strong>";
                } ?>
            </p>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-bordered trial-table" id="trialBalanceTable">
                <thead>
                    <tr>
                        <th rowspan="2" class="particulars-col">Particulars</th>
                        <th colspan="2" class="text-center balance-header">Closing Balance</th>
                    </tr>
                    <tr>
                        <th class="text-right amount-col header-debit">Debit</th>
                        <th class="text-right amount-col header-credit">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $current_group = '';

                    // Group totals
                    $grp_cl_dr = $grp_cl_cr = 0;

                    // Grand totals
                    $grand_cl_dr = $grand_cl_cr = 0;

                    // Temporary array to store items grouped by account head
                    $grouped_records = [];
                    foreach ($record_list as $row) {
                        $grouped_records[$row['account_head_name']][] = $row;
                    }

                    foreach ($grouped_records as $group_name => $items):
                        ?>
                        <!-- Group Header -->
                        <tr class="group-header">
                            <td colspan="3"><strong><?php echo strtoupper(htmlspecialchars($group_name)); ?></strong></td>
                        </tr>

                        <?php
                        $grp_cl_dr = $grp_cl_cr = 0;

                        foreach ($items as $row):
                            $grp_cl_dr += $row['closing_debit'];
                            $grp_cl_cr += $row['closing_credit'];
                            ?>
                            <!-- Ledger Subaccount Row -->
                            <tr class="ledger-row">
                                <td class="ledger"><?php echo htmlspecialchars($row['sub_account_head_name']); ?></td>
                                <td class="text-right amount-cell">
                                    <?php echo ($row['closing_debit'] > 0) ? number_format($row['closing_debit'], 3) : '-'; ?>
                                </td>
                                <td class="text-right amount-cell">
                                    <?php echo ($row['closing_credit'] > 0) ? number_format($row['closing_credit'], 3) : '-'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <!-- Group Total Row -->
                        <tr class="group-total">
                            <td><strong>Total : <?php echo htmlspecialchars($group_name); ?></strong></td>
                            <td class="text-right">
                                <strong><?php echo ($grp_cl_dr > 0) ? number_format($grp_cl_dr, 3) : '-'; ?></strong></td>
                            <td class="text-right">
                                <strong><?php echo ($grp_cl_cr > 0) ? number_format($grp_cl_cr, 3) : '-'; ?></strong></td>
                        </tr>

                        <?php
                        $grand_cl_dr += $grp_cl_dr;
                        $grand_cl_cr += $grp_cl_cr;
                    endforeach;
                    ?>

                    <!-- GRAND TOTAL -->
                    <tr class="grand-total">
                        <td><strong>GRAND TOTAL</strong></td>
                        <td class="text-right"><strong><?php echo number_format($grand_cl_dr, 3); ?></strong></td>
                        <td class="text-right"><strong><?php echo number_format($grand_cl_cr, 3); ?></strong></td>
                    </tr>

                    <!-- DIFFERENCE (if any) -->
                    <?php
                    $diff_cl = abs($grand_cl_dr - $grand_cl_cr);

                    if ($diff_cl > 0.01):
                        ?>
                        <tr class="difference-row">
                            <td><strong>DIFFERENCE</strong></td>

                            <!-- Closing Difference -->
                            <td class="text-right" style="color: #d9534f;">
                                <strong><?php echo ($grand_cl_dr < $grand_cl_cr) ? number_format($diff_cl, 3) : '-'; ?></strong>
                            </td>
                            <td class="text-right" style="color: #d9534f;">
                                <strong><?php echo ($grand_cl_cr < $grand_cl_dr) ? number_format($diff_cl, 3) : '-'; ?></strong>
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>

        <div class="box-footer no-print">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted">
                        <i class="fa fa-info-circle"></i>
                        Total Accounts Reported: <strong><?php echo count($record_list); ?></strong>
                    </p>
                </div>
                <div class="col-md-6 text-right">
                    <p class="text-muted">
                        <i class="fa fa-clock-o"></i>
                        Generated on: <strong><?php echo date('d M Y, h:i A'); ?></strong>
                    </p>
                </div>
            </div>
        </div>
    </div>

</section>

<style>
    /* Premium Aesthetic Styling */
    .trial-table {
        font-size: 13px;
        border-collapse: collapse;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        width: 100%;
        margin-bottom: 20px;
    }

    .trial-table th {
        background: #3c8dbc;
        color: white;
        text-align: center;
        padding: 10px 6px;
        font-weight: 600;
        border: 1px solid #575757ff !important;
    }

    .trial-table td {
        padding: 8px 10px;
        border: 1px solid #ddd !important;
    }

    .particulars-col {
        min-width: 250px;
    }

    .amount-col {
        min-width: 110px;
    }

    .balance-header {
        background: #2e6da4 !important;
        border: 1px solid #575757ff !important;
    }

    .header-debit {
        background: #3a7ebd !important;
    }

    .header-credit {
        background: #4b89c2 !important;
    }

    .group-header {
        background: #eef2f5 !important;
        color: #1a252f;
        font-size: 13px;
        font-weight: bold;
    }

    .group-header td {
        padding: 8px 12px;
        border-top: 1.5px solid #ccc !important;
        border-bottom: 1.5px solid #ccc !important;
    }

    .group-total {
        background: #f7f9fa;
        border-top: 1px solid #ccc !important;
        border-bottom: 1px solid #ccc !important;
    }

    .group-total td {
        font-weight: bold;
        color: #2c3e50;
    }

    .grand-total {
        background: #2c3e50 !important;
        color: white;
        font-size: 14px;
        font-weight: bold;
    }

    .grand-total td {
        padding: 12px 10px;
        border: 2px solid #2c3e50 !important;
    }

    .difference-row {
        background: #fcf8e3;
        border: 1.5px solid #d9534f;
    }

    .difference-row td {
        font-weight: bold;
    }

    .ledger {
        padding-left: 25px;
        position: relative;
    }

    .ledger:before {
        content: "•";
        position: absolute;
        left: 12px;
        color: #3c8dbc;
        font-size: 16px;
        top: 6px;
    }

    .ledger-row:hover {
        background: #f5f8fa;
        transition: background 0.15s ease-in-out;
    }

    .text-right {
        text-align: right;
        font-family: 'Courier New', monospace;
    }

    .amount-cell {
        color: #333;
    }

    /* Print styling */
    .print-header {
        display: none;
    }

    @media print {

        .no-print,
        .main-header,
        .main-sidebar,
        .breadcrumb,
        .content-header {
            display: none !important;
        }

        .content-wrapper {
            margin-left: 0 !important;
        }

        .print-header {
            display: block !important;
            margin-bottom: 15px;
            padding: 10px;
            border-bottom: 2px solid #333;
        }

        .box {
            border: none !important;
            box-shadow: none !important;
        }

        .trial-table th {
            background: #444 !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .group-header {
            background: #eee !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .grand-total {
            background: #222 !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

    }
</style>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>

<script>
    // Excel export function
    function exportToExcel() {
        var table = document.getElementById('trialBalanceTable');
        var html = table.outerHTML;

        var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        var link = document.createElement('a');
        link.href = url;
        link.download = 'Account_Trial_Balance_<?php echo date("Y-m-d"); ?>.xls';
        link.click();
    }
</script>