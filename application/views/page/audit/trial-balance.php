<?php include_once(VIEWPATH . '/inc/header.php'); ?>

<section class="content-header">
    <h1>
        <i class="fa fa-balance-scale"></i> <?php echo htmlspecialchars($title); ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Report</a></li>
        <li class="active"><?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>
 
<section class="content">

    <!-- FILTER -->
    <div class="box box-primary no-print">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-filter"></i> Filter Options</h3>
        </div>
        <div class="box-body">
            <form method="post">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fa fa-calendar"></i> From Date</label>
                            <input type="date" name="srch_from_date" class="form-control"
                                value="<?php echo $srch_from_date; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fa fa-calendar"></i> To Date</label>
                            <input type="date" name="srch_to_date" class="form-control"
                                value="<?php echo $srch_to_date; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="margin-top:25px;">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-search"></i> Generate Report
                            </button>
                            <button type="button" onclick="window.print()" class="btn btn-primary">
                                <i class="fa fa-print"></i> Print Report
                            </button>
                            <button type="button" onclick="exportToExcel()" class="btn btn-info">
                                <i class="fa fa-file-excel-o"></i> Export Excel
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- REPORT -->
    <div class="box box-success">
        <div class="box-header with-border print-header">
            <div class="text-center">
                <h2 class="company-name">Your Company Name</h2>
                <h3 class="report-title">TRIAL BALANCE</h3>
                <p class="report-period">
                    Period: <strong><?php echo date('d M Y', strtotime($srch_from_date)); ?></strong> 
                    to <strong><?php echo date('d M Y', strtotime($srch_to_date)); ?></strong>
                </p>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered trial-table" id="trialBalanceTable">
                    <thead>
                        <tr>
                            <th rowspan="2" class="particulars-col">Particulars</th>
                            <th colspan="2" class="text-center balance-header">Closing Balance</th>
                        </tr>
                        <tr>
                            <th class="text-right amount-col">Debit </th>
                            <th class="text-right amount-col">Credit </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $current_group = '';
                        $grp_debit = $grp_credit = 0;
                        $total_debit = $total_credit = 0;

                        foreach ($record_list as $row):

                            if ($current_group != $row['group_name']) {

                                if ($current_group != '') { ?>
                                    <tr class="group-total">
                                        <td><strong><?php echo $current_group; ?></strong></td>
                                        <td class="text-right"><strong><?php echo number_format($grp_debit, 3); ?></strong></td>
                                        <td class="text-right"><strong><?php echo number_format($grp_credit, 3); ?></strong></td>
                                    </tr>
                                    <?php
                                    $grp_debit = $grp_credit = 0;
                                }

                                $current_group = $row['group_name']; ?>
                                <tr class="group-header">
                                    <td colspan="3"><strong><?php echo strtoupper($current_group); ?></strong></td>
                                </tr>
                            <?php } ?>

                            <tr class="ledger-row">
                                <td class="ledger"><?php echo $row['ledger_name']; ?></td>
                                <td class="text-right">
                                    <?php echo ($row['closing_debit'] > 0) ? number_format($row['closing_debit'], 3) : '-'; ?>
                                </td>
                                <td class="text-right">
                                    <?php echo ($row['closing_credit'] > 0) ? number_format($row['closing_credit'], 3) : '-'; ?>
                                </td>
                            </tr>

                            <?php
                            $grp_debit += $row['closing_debit'];
                            $grp_credit += $row['closing_credit'];

                            $total_debit += $row['closing_debit'];
                            $total_credit += $row['closing_credit'];

                        endforeach;
                        ?>

                        <!-- LAST GROUP TOTAL -->
                        <?php if ($current_group != '') { ?>
                        <tr class="group-total">
                            <td><strong><?php echo $current_group; ?></strong></td>
                            <td class="text-right"><strong><?php echo number_format($grp_debit, 3); ?></strong></td>
                            <td class="text-right"><strong><?php echo number_format($grp_credit, 3); ?></strong></td>
                        </tr>
                        <?php } ?>

                        <!-- GRAND TOTAL -->
                        <tr class="grand-total">
                            <td><strong>GRAND TOTAL</strong></td>
                            <td class="text-right"><strong><?php echo number_format($total_debit, 3); ?></strong></td>
                            <td class="text-right"><strong><?php echo number_format($total_credit, 3); ?></strong></td>
                        </tr>

                        <!-- DIFFERENCE (if any) -->
                        <?php 
                        $difference = abs($total_debit - $total_credit);
                        if ($difference > 0.01) { ?>
                        <tr class="difference-row">
                            <td><strong>DIFFERENCE</strong></td>
                            <td class="text-right" style="color: #d9534f;">
                                <strong><?php echo ($total_debit < $total_credit) ? number_format($difference, 3) : '-'; ?></strong>
                            </td>
                            <td class="text-right" style="color: #d9534f;">
                                <strong><?php echo ($total_credit < $total_debit) ? number_format($difference, 3) : '-'; ?></strong>
                            </td>
                        </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer no-print">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted">
                        <i class="fa fa-info-circle"></i> 
                        Total Records: <strong><?php echo count($record_list); ?></strong>
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
    /* Enhanced Table Styling */
    .trial-table {
        font-size: 14px;
        border-collapse: collapse;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .trial-table th {
        background: linear-gradient(to bottom, #3c8dbc 0%, #367fa9 100%);
        color: white;
        text-align: center;
        padding: 12px 8px;
        font-weight: 600;
        border: 1px solid #2e6da4;
    }

    .trial-table td {
        padding: 10px 12px;
        border: 1px solid #575757ff !important;
    }

    .particulars-col {
        min-width: 300px;
    }

    .amount-col {
        min-width: 150px;
    }

    .balance-header {
        background: linear-gradient(to bottom, #5bc0de 0%, #46b8da 100%);
        border: 1px solid #575757ff;
    }

    .group-header {
        background: linear-gradient(to right, #ffffffff 0%, #ffffffff 100%);
        color: #000000;
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .group-header td {
        padding: 10px 15px;
        border: 1px solid #d87a0a;
    }

    .group-total {
        background: #f9f9f9;
        border-top: 2px solid #575757ff !important;
        border-bottom: 2px solid #575757ff !important;
    }

    .group-total td {
        font-weight: 600;
        color: #333;
    }

    .grand-total {
        background: linear-gradient(to right, #222222 0%, #333333 100%);
        color: white;
        font-size: 16px;
        font-weight: bold;
    }

    .grand-total td {
        padding: 15px 12px;
        border: 2px solid #575757ff;
    }

    .difference-row {
        background: #fcf8e3;
        border: 2px solid #d9534f;
    }

    .difference-row td {
        font-weight: 600;
    }

    .ledger {
        padding-left: 35px;
        position: relative;
    }

    .ledger:before {
        content: "→";
        position: absolute;
        left: 15px;
        color: #999;
    }

    .ledger-row:hover {
        background: #f5f5f5;
        transition: background 0.2s;
    }

    .text-right {
        text-align: right;
        font-family: 'Courier New', monospace;
    }

    /* Print Header Styling */
    .print-header {
        display: none;
    }

    .company-name {
        margin: 0;
        color: #333;
        font-size: 24px;
        font-weight: bold;
    }

    .report-title {
        margin: 5px 0;
        color: #3c8dbc;
        font-size: 20px;
    }

    .report-period {
        margin: 5px 0;
        color: #666;
        font-size: 13px;
    }

    /* Print Styles */
    @media print {
        body {
            margin: 0;
            padding: 0;
        }

        .content-header,
        .no-print {
            display: none !important;
        }

        .print-header {
            display: block !important;
            margin-bottom: 20px;
            padding: 20px;
            border-bottom: 3px solid #333;
        }

        .box {
            border: none;
            box-shadow: none;
            margin: 0;
        }

        .trial-table {
            width: 100%;
            page-break-inside: auto;
        }

        .trial-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .trial-table thead {
            display: table-header-group;
        }

        .trial-table th {
            background: #333 !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .group-header {
            background: #ddd !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .grand-total {
            background: #333 !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .group-total {
            background: #f0f0f0 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @page {
            margin: 1cm;
            size: A4 portrait;
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .trial-table {
            font-size: 12px;
        }

        .particulars-col {
            min-width: 200px;
        }

        .amount-col {
            min-width: 100px;
        }
    }
</style>



<?php include_once(VIEWPATH . '/inc/footer.php'); ?>
<script>
// Export to Excel Function
function exportToExcel() {
    var table = document.getElementById('trialBalanceTable');
    var html = table.outerHTML;
    
    // Create a download link
    var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
    var link = document.createElement('a');
    link.href = url;
    link.download = 'Trial_Balance_<?php echo date("Y-m-d"); ?>.xls';
    link.click();
}

// Auto-print on load (optional - remove if not needed)
// window.onload = function() {
//     if (window.location.search.indexOf('auto_print=1') > -1) {
//         window.print();
//     }
// }
</script>