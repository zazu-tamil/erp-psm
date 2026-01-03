<?php include_once(VIEWPATH . '/inc/header.php'); ?>

<section class="content-header">
    <h1>
        <i class="fa fa-line-chart"></i> <?php echo htmlspecialchars($title); ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Report</a></li>
        <li class="active"><?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">

    <!-- FILTER -->
    <div class="filter-card no-print">
        <div class="filter-header">
            <h3><i class="fa fa-filter"></i> Filter Options</h3>
        </div>
        <div class="filter-body">
            <form method="post">
                <div class="row">
                    <div class="col-md-3">
                        <label>From Date</label>
                        <input type="date" name="srch_from_date" class="form-control"
                               value="<?php echo $srch_from_date; ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label>To Date</label>
                        <input type="date" name="srch_to_date" class="form-control"
                               value="<?php echo $srch_to_date; ?>" required>
                    </div>
                    <br>
                    <div class="col-md-6 filter-actions">
                        <button class="btn btn-generate">
                            <i class="fa fa-search"></i> Generate Report
                        </button>
                        <button type="button" onclick="window.print()" class="btn btn-print">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- REPORT -->
    <div class="report-card">

        <!-- SCREEN VIEW: Card/Grid Layout (Hidden on Print) -->
        <div class="report-content screen-view">

            <!-- TITLE -->
            <div class="report-header">
                <div class="company-logo">
                    <i class="fa fa-building-o"></i>
                </div>
                <h2>PROFIT & LOSS STATEMENT</h2>
                <p class="period-text">
                    For the period from
                    <span class="date-highlight"><?php echo date('d M Y', strtotime($srch_from_date)); ?></span>
                    to
                    <span class="date-highlight"><?php echo date('d M Y', strtotime($srch_to_date)); ?></span>
                </p>
            </div>

            <?php
            $income = [];
            $expense = [];
            $income_total = 0;
            $expense_total = 0;

            if (!empty($record_list)) {
                foreach ($record_list as $row) {
                    if ($row['nature'] == 'Income') {
                        $income[] = $row;
                        $income_total += abs($row['net_amount']);
                    }
                    if ($row['nature'] == 'Expense') {
                        $expense[] = $row;
                        $expense_total += abs($row['net_amount']);
                    }
                }
            }

            $max = max(count($income), count($expense));
            ?>

            <div class="pl-grid">
                <!-- INCOME SECTION -->
                <div class="pl-section income-section">
                    <div class="section-header">
                        <i class="fa fa-arrow-up"></i> INCOME
                    </div>
                    <div class="section-body">
                        <?php if (!empty($income)): ?>
                            <?php foreach ($income as $item): ?>
                                <div class="pl-item">
                                    <span class="item-name"><?php echo htmlspecialchars($item['ledger_name']); ?></span>
                                    <span class="item-amount"> <?php echo number_format(abs($item['net_amount']), 3); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-data">No income records</div>
                        <?php endif; ?>
                    </div>
                    <div class="section-total">
                        <span>Total Income</span>
                        <span class="total-amount"> <?php echo number_format($income_total, 3); ?></span>
                    </div>
                </div>

                <!-- EXPENSE SECTION -->
                <div class="pl-section expense-section">
                    <div class="section-header">
                        <i class="fa fa-arrow-down"></i> EXPENSES
                    </div>
                    <div class="section-body">
                        <?php if (!empty($expense)): ?>
                            <?php foreach ($expense as $item): ?>
                                <div class="pl-item">
                                    <span class="item-name"><?php echo htmlspecialchars($item['ledger_name']); ?></span>
                                    <span class="item-amount"> <?php echo number_format(abs($item['net_amount']), 3); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-data">No expense records</div>
                        <?php endif; ?>
                    </div>
                    <div class="section-total">
                        <span>Total Expenses</span>
                        <span class="total-amount"> <?php echo number_format($expense_total, 3); ?></span>
                    </div>
                </div>
            </div>

            <!-- NET RESULT -->
            <?php
            $net_profit = $income_total - $expense_total;
            $is_profit = $net_profit >= 0;
            ?>
            <div class="net-result <?php echo $is_profit ? 'profit' : 'loss'; ?>">
                <div class="result-icon">
                    <i class="fa fa-<?php echo $is_profit ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                </div>
                <div class="result-text">
                    <h3>NET <?php echo $is_profit ? 'PROFIT' : 'LOSS'; ?></h3>
                    <p class="result-amount"> <?php echo number_format(abs($net_profit), 3); ?></p>
                </div>
            </div>

        </div>

        <!-- PRINT VIEW: Table Layout (Only visible when printing) -->
        <div class="report-content print-view">

            <div class="print-header">
                <h2 style="text-align:center; margin-bottom:10px;">PROFIT & LOSS STATEMENT</h2>
                <p style="text-align:center; color:#555;">
                    For the period from <?php echo date('d M Y', strtotime($srch_from_date)); ?>
                    to <?php echo date('d M Y', strtotime($srch_to_date)); ?>
                </p>
            </div>

            <table class="print-table" style="width:100%; border-collapse:collapse; margin-top:30px;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:10px; border-bottom:3px double #000; font-size:14px;">PARTICULARS</th>
                        <th style="text-align:right; padding:10px; border-bottom:3px double #000; font-size:14px;">AMOUNT ()</th>
                        <th style="text-align:left; padding:10px; border-bottom:3px double #000; font-size:14px;">PARTICULARS</th>
                        <th style="text-align:right; padding:10px; border-bottom:3px double #000; font-size:14px;">AMOUNT ()</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < $max || $i < 1; $i++): ?>
                        <tr>
                            <!-- Income Side -->
                            <td style="padding:8px 10px; vertical-align:top;">
                                <?php if (isset($income[$i])): ?>
                                    <?php echo htmlspecialchars($income[$i]['ledger_name']); ?>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right; padding:8px 10px; vertical-align:top;">
                                <?php if (isset($income[$i])): ?>
                                    <?php echo number_format(abs($income[$i]['net_amount']), 3); ?>
                                <?php endif; ?>
                            </td>

                            <!-- Expense Side -->
                            <td style="padding:8px 10px; vertical-align:top;">
                                <?php if (isset($expense[$i])): ?>
                                    <?php echo htmlspecialchars($expense[$i]['ledger_name']); ?>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right; padding:8px 10px; vertical-align:top;">
                                <?php if (isset($expense[$i])): ?>
                                    <?php echo number_format(abs($expense[$i]['net_amount']), 3); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endfor; ?>

                    <!-- Total Rows -->
                    <tr>
                        <td style="padding:10px; font-weight:bold; border-top:2px solid #000;">Total Income</td>
                        <td style="text-align:right; padding:10px; font-weight:bold; border-top:2px solid #000;">
                            <?php echo number_format($income_total, 3); ?>
                        </td>
                        <td style="padding:10px; font-weight:bold; border-top:2px solid #000;">Total Expenses</td>
                        <td style="text-align:right; padding:10px; font-weight:bold; border-top:2px solid #000;">
                            <?php echo number_format($expense_total, 3); ?>
                        </td>
                    </tr>

                    <!-- Net Profit/Loss Row -->
                    <tr>
                        <td colspan="3" style="padding:15px 10px; text-align:center; font-weight:bold; font-size:16px;">
                            NET <?php echo $is_profit ? 'PROFIT' : 'LOSS'; ?>
                        </td>
                        <td style="text-align:right; padding:15px 10px; font-weight:bold; font-size:16px; 
                                   color:<?php echo $is_profit ? '#11998e' : '#eb3349'; ?>;">
                            <?php echo number_format(abs($net_profit), 3); ?>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

</section>

<!-- Your existing <style> block remains same until @media print -->

<style>
/* ... [ALL YOUR EXISTING STYLES REMAIN UNCHANGED] ... */

/* Print Styles - Enhanced for Table View */
@media print {
    .no-print, .screen-view {
        display: none !important;
    }

    .print-view {
        display: block !important;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 12pt;
        line-height: 1.4;
        color: #000;
        background: white;
    }

    .report-card {
        box-shadow: none;
        border: none;
        padding: 0;
        margin: 0;
    }

    .report-content {
        padding: 20px !important;
    }

    /* Force background colors to print */
    .net-result.profit, .net-result.loss {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Ensure table prints cleanly */
    .print-table {
        page-break-inside: avoid;
    }

    @page {
        margin: 1cm;
    }
}
</style>

 <style>
    .print-view {
        display: none;
    }

    @media print {
        .print-view {
            display: block;
        }
    }
</style>
<style>
/* Filter Card */
.filter-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 25px;
    overflow: hidden;
}

.filter-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px 25px;
    color: white;
}

.filter-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.filter-body {
    padding: 25px;
}

.filter-body label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 14px;
}

.filter-body .form-control {
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 10px 15px;
    font-size: 14px;
    transition: all 0.3s;
}

.filter-body .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filter-actions {
    display: flex;
    align-items: flex-end;
    gap: 10px;
}

.btn {
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 14px;
}

.btn-generate {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-generate:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-print {
    background: #f8f9fa;
    color: #333;
    border: 2px solid #e0e0e0;
}

.btn-print:hover {
    background: #e9ecef;
    border-color: #ccc;
}

/* Report Card */
.report-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
}

.report-content {
    padding: 40px;
}

/* Report Header */
.report-header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 3px solid #f0f0f0;
}

.company-logo {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: white;
    font-size: 36px;
}

.report-header h2 {
    margin: 0 0 15px;
    font-size: 32px;
    font-weight: 700;
    color: #1a1a1a;
    letter-spacing: 1px;
}

.period-text {
    font-size: 15px;
    color: #666;
    margin: 0;
}

.date-highlight {
    font-weight: 700;
    color: #667eea;
}

/* P&L Grid */
.pl-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.pl-section {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.section-header {
    padding: 18px 20px;
    font-weight: 700;
    font-size: 16px;
    text-align: center;
    color: white;
}

.income-section .section-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.expense-section .section-header {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

.section-body {
    background: #fafafa;
    padding: 20px;
    min-height: 200px;
}

.pl-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    background: white;
    margin-bottom: 10px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    transition: all 0.3s;
}

.pl-item:hover {
    transform: translateX(5px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.item-name {
    font-size: 14px;
    color: #333;
    font-weight: 500;
}

.item-amount {
    font-size: 15px;
    font-weight: 700;
    color: #1a1a1a;
}

.no-data {
    text-align: center;
    color: #999;
    padding: 40px;
    font-style: italic;
}

.section-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 20px;
    background: #fff;
    border-top: 3px solid #e0e0e0;
    font-weight: 700;
    font-size: 16px;
}

.income-section .section-total {
    color: #11998e;
}

.expense-section .section-total {
    color: #eb3349;
}

.total-amount {
    font-size: 18px;
}

/* Net Result */
.net-result {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 25px;
    padding: 30px;
    border-radius: 12px;
    margin-top: 30px;
}

.net-result.profit {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.net-result.loss {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
    color: white;
}

.result-icon {
    font-size: 48px;
}

.result-text h3 {
    margin: 0 0 8px;
    font-size: 20px;
    font-weight: 600;
    letter-spacing: 2px;
}

.result-amount {
    margin: 0;
    font-size: 36px;
    font-weight: 700;
}

/* Print Styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        font-size: 12px;
    }
    
    .report-card {
        box-shadow: none;
    }
    
    .report-content {
        padding: 20px;
    }
    
    .pl-item:hover {
        transform: none;
    }
    
    .net-result {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .pl-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .filter-actions {
        margin-top: 15px;
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn {
        width: 100%;
    }
    
    .report-content {
        padding: 20px;
    }
    
    .result-text h3 {
        font-size: 18px;
    }
    
    .result-amount {
        font-size: 28px;
    }
}
</style>
<?php include_once(VIEWPATH . '/inc/footer.php'); ?>