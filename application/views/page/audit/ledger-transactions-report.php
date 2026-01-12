<?php include_once(VIEWPATH . '/inc/header.php'); ?>

<section class="content-header no-print">
    <h1>
        <i class="fa fa-balance-scale"></i> <?php echo htmlspecialchars($title); ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Report</a></li>
        <li class="active"><?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">

    <!-- ================= FILTER ================= -->
    <div class="box box-primary no-print">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-filter"></i> Filter Options</h3>
        </div>
        <div class="box-body">
            <form method="post">
                <div class="row">

                    <div class="col-md-3">
                        <label>From Date</label>
                        <input type="date" name="srch_from_date" class="form-control"
                            value="<?php echo htmlspecialchars($srch_from_date ?? ''); ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label>To Date</label>
                        <input type="date" name="srch_to_date" class="form-control"
                            value="<?php echo htmlspecialchars($srch_to_date ?? ''); ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label>Ledger Account</label>
                        <?php
                        echo form_dropdown(
                            'srch_ledger_id',
                            $ledger_opt ?? [],
                            $srch_ledger_id ?? '',
                            'class="form-control" required'
                        );
                        ?>
                    </div>

                    <div class="col-md-3" style="margin-top:25px;">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-search"></i> Show Report
                        </button>
                        <button type="button" class="btn btn-default" onclick="window.print();">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <!-- ================= REPORT ================= -->
    <?php if (!empty($srch_ledger_id)): ?>



        <!-- PRINT HEADER -->
        <div class="print-header text-center">
            <h2 class="company-name">AL HILLO TRADING CO W.L.L</h2>
            <h3 class="report-title">LEDGER TRANSACTIONS REPORT</h3>

            <p><strong>Ledger:</strong> <?php echo htmlspecialchars($ledger_name); ?></p>
            <p>
                <strong>Period:</strong>
                <?php echo date('d M Y', strtotime($srch_from_date)); ?>
                to
                <?php echo date('d M Y', strtotime($srch_to_date)); ?>
            </p>
        </div>

        <?php
        // ================= OPENING BALANCE =================
        $opening_amount = 0;

        if (!empty($ledger_list['opening_bal'])) {
            preg_match('/([\d\.]+)\s*(Dr|Cr)/', $ledger_list['opening_bal'], $m);
            $opening_amount = ($m[2] == 'Dr') ? $m[1] : -$m[1];
        }

        $running_balance = $opening_amount;
        $total_debit = 0;
        $total_credit = 0;
        ?>

        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Ledger Transactions</h3>
            </div>

            <div class="box-body">
                <table class="table table-bordered table-striped ledger-table">
                    <thead>
                        <tr class="bg-gray">
                            <th width="15%">Date</th>
                            <th width="18%">Voucher No</th>
                            <th width="25%">Type</th>
                            <th class="text-right" width="14%">Debit</th>
                            <th class="text-right" width="14%">Credit</th>
                            <th class="text-right" width="14%">Balance</th>
                        </tr>
                    </thead>

                    <tbody>
                        <!-- OPENING BALANCE -->
                        <tr class="bg-info">
                            <td colspan="5"><strong>Opening Balance</strong></td>
                            <td class="text-right">
                                    <strong>
                                    <?php echo !empty($ledger_list['opening_bal']) ? $ledger_list['opening_bal'] : '0 Dr'; ?>
                                </strong>
                            </td>
                        </tr>

                        <!-- TRANSACTIONS -->
                        <?php foreach ($ledger_transactions as $row): ?>
                            <?php
                            $running_balance += $row['debit'];
                            $running_balance -= $row['credit'];

                            $total_debit += $row['debit'];
                            $total_credit += $row['credit'];

                            if ($running_balance >= 0) {
                                $bal_display = number_format($running_balance, 3) . ' Dr';
                            } else {
                                $bal_display = number_format(abs($running_balance), 3) . ' Cr';
                            }
                            ?>
                            <tr>
                                <td><?php echo date('d-m-Y', strtotime($row['voucher_date'])); ?></td>
                                <td><?php echo $row['narration']; ?></td>
                                <td><?php echo $row['voucher_type']; ?></td>
                                <td class="text-right"><?php echo number_format($row['debit'], 3); ?></td>
                                <td class="text-right"><?php echo number_format($row['credit'], 3); ?></td>
                                <td class="text-right"><?php echo $bal_display; ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <!-- TOTALS -->
                        <tr class="bg-warning">
                            <td colspan="3"><strong>Total</strong></td>
                            <td class="text-right"><strong><?php echo number_format($total_debit, 3); ?></strong></td>
                            <td class="text-right"><strong><?php echo number_format($total_credit, 3); ?></strong></td>
                            <td></td>
                        </tr>

                        <!-- CLOSING BALANCE -->
                        <tr class="bg-success">
                            <td colspan="5"><strong>Closing Balance</strong></td>
                            <td class="text-right">
                                <strong>
                                    <?php
                                    if ($running_balance >= 0)
                                        echo number_format($running_balance, 3) . ' Dr';
                                    else
                                        echo number_format(abs($running_balance), 3) . ' Cr';
                                    ?>
                                </strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


    <?php else: ?>

        <div class="box box-warning">
            <div class="box-body text-center text-muted" style="padding:40px;">
                <i class="fa fa-info-circle fa-3x"></i>
                <p style="margin-top:15px;font-size:16px;">
                    Please select a ledger account and date range to view the report.
                </p>
            </div>
        </div>

    <?php endif; ?>


</section>

<!-- ================= PRINT CSS ================= -->
<style>
    /* Screen */
    .ledger-table {
        width: 100%;
        table-layout: fixed;
    }

    .print-header {
        display: none;
    }

    /* Print */
    @media print {

        @page {
            size: A4;
            margin: 12mm;
        }

        body {
            font-size: 11px;
            color: #000;
        }

        .no-print {
            display: none !important;
        }

        .print-header {
            display: block;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .report-title {
            font-size: 14px;
            margin: 5px 0;
        }

        table {
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        tr {
            page-break-inside: avoid;
        }

        th,
        td {
            padding: 6px !important;
            border: 1px solid #000 !important;
        }

        .bg-gray {
            background: #f0f0f0 !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>