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
                        <input type="date" name="srch_to_date" class="form-control" value="<?php echo $srch_to_date; ?>"
                            required>
                    </div>
                    <br>
                    <div class="col-md-6 filter-actions">
                        <button class="btn btn-generate">
                            <i class="fa fa-search"></i> Generate Report
                        </button>
                        <!-- <button type="button" onclick="window.print()" class="btn btn-print">
                            <i class="fa fa-print"></i> Print
                        </button> -->
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
            // Prevent PHP 8.1+ deprecation warnings if variables are null
            $sales = (float) ($sales ?? 0);
            $purchases = (float) ($purchases ?? 0);

            // Group other income by parent head
            $grouped_other_income = [];
            foreach ($other_income as $row) {
                $head = $row['inc_type'];
                if (!isset($grouped_other_income[$head])) {
                    $grouped_other_income[$head] = [
                        'sub_items' => [],
                        'subtotal' => 0
                    ];
                }
                $grouped_other_income[$head]['sub_items'][] = [
                    'name' => $row['sub_typ'],
                    'amount' => $row['inc_amt']
                ];
                $grouped_other_income[$head]['subtotal'] += $row['inc_amt'];
            }

            // Calculate indirect expenses subtotal
            $indirect_total = 0;
            foreach ($indirect_expenses as $row) {
                $indirect_total += $row['exp_amt'];
            }

            // Calculate Grand Totals
            $income_total = $sales;
            foreach ($grouped_other_income as $head) {
                $income_total += $head['subtotal'];
            }

            $expense_total = $purchases + $indirect_total;

            // Generate Flat list of rows for the side-by-side PRINT layout
            $income_rows = [];
            // Sales
            $income_rows[] = [
                'text' => 'Sales',
                'amount' => number_format($sales, 3),
                'is_head' => true,
                'is_sub' => false
            ];
            // Other Income heads and sub-items
            foreach ($grouped_other_income as $head_name => $head_data) {
                $income_rows[] = [
                    'text' => $head_name,
                    'amount' => number_format($head_data['subtotal'], 3),
                    'is_head' => true,
                    'is_sub' => false
                ];
                foreach ($head_data['sub_items'] as $sub_item) {
                    $income_rows[] = [
                        'text' => $sub_item['name'],
                        'amount' => number_format($sub_item['amount'], 3),
                        'is_head' => false,
                        'is_sub' => true
                    ];
                }
            }

            $expense_rows = [];
            // Purchases
            $expense_rows[] = [
                'text' => 'Purchases',
                'amount' => number_format($purchases, 3),
                'is_head' => true,
                'is_sub' => false
            ];
            // Indirect Expenses and sub-items
            $expense_rows[] = [
                'text' => 'Indirect Expenses',
                'amount' => number_format($indirect_total, 3),
                'is_head' => true,
                'is_sub' => false
            ];
            foreach ($indirect_expenses as $row) {
                $expense_rows[] = [
                    'text' => ucwords(strtolower($row['exp_type'])),
                    'amount' => number_format($row['exp_amt'], 3),
                    'is_head' => false,
                    'is_sub' => true
                ];
            }

            $max = max(count($income_rows), count($expense_rows));
            ?>

            <div class="pl-grid">


                <!-- EXPENSE SECTION -->
                <div class="pl-section expense-section">
                    <div class="section-header">
                        <i class="fa fa-arrow-down"></i> EXPENSES
                    </div>
                    <div class="section-body">
                        <!-- Purchases -->
                        <div class="pl-group">
                            <div class="pl-item pl-head">
                                <span class="item-name">Purchases</span>
                                <span class="item-amount"> <?php echo number_format($purchases, 3); ?></span>
                            </div>
                        </div>

                        <!-- Indirect Expenses -->
                        <div class="pl-group">
                            <div class="pl-item pl-head">
                                <span class="item-name">Indirect Expenses</span>
                                <span class="item-amount"> <?php echo number_format($indirect_total, 3); ?></span>
                            </div>
                            <?php if (!empty($indirect_expenses)): ?>
                                <div class="pl-sub-items">
                                    <?php foreach ($indirect_expenses as $expense): ?>
                                        <div class="pl-item pl-sub-item">
                                            <span
                                                class="item-name text-capitalize"><?php echo strtolower($expense['exp_type']); ?></span>
                                            <span class="item-amount">
                                                <?php echo number_format($expense['exp_amt'], 3); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="section-total">
                        <span>Total Expenses</span>
                        <span class="total-amount"> <?php echo number_format($expense_total, 3); ?></span>
                    </div>
                </div>
                <!-- INCOME SECTION -->
                <div class="pl-section income-section">
                    <div class="section-header">
                        <i class="fa fa-arrow-up"></i> INCOME
                    </div>
                    <div class="section-body">
                        <!-- Sales -->
                        <div class="pl-group">
                            <div class="pl-item pl-head">
                                <span class="item-name">Sales</span>
                                <span class="item-amount"> <?php echo number_format($sales, 3); ?></span>
                            </div>
                        </div>

                        <!-- Other Income -->
                        <?php foreach ($grouped_other_income as $head_name => $head_data): ?>
                            <div class="pl-group">
                                <div class="pl-item pl-head">
                                    <span class="item-name"><?php echo htmlspecialchars($head_name); ?></span>
                                    <span class="item-amount">
                                        <?php echo number_format($head_data['subtotal'], 3); ?></span>
                                </div>
                                <div class="pl-sub-items">
                                    <?php foreach ($head_data['sub_items'] as $sub_item): ?>
                                        <div class="pl-item pl-sub-item">
                                            <span class="item-name"><?php echo htmlspecialchars($sub_item['name']); ?></span>
                                            <span class="item-amount">
                                                <?php echo number_format($sub_item['amount'], 3); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="section-total">
                        <span>Total Income</span>
                        <span class="total-amount"> <?php echo number_format($income_total, 3); ?></span>
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
                        <th style="text-align:left; padding:10px; border-bottom:3px double #000; font-size:14px;">
                            PARTICULARS (EXPENSES)</th>
                        <th style="text-align:right; padding:10px; border-bottom:3px double #000; font-size:14px;">
                            AMOUNT ()</th>
                        <th style="text-align:left; padding:10px; border-bottom:3px double #000; font-size:14px;">
                            PARTICULARS (INCOME)</th>
                        <th style="text-align:right; padding:10px; border-bottom:3px double #000; font-size:14px;">
                            AMOUNT ()</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < $max; $i++):
                        $inc_row = isset($income_rows[$i]) ? $income_rows[$i] : null;
                        $exp_row = isset($expense_rows[$i]) ? $expense_rows[$i] : null;
                        ?>
                        <tr>
                            <!-- Expense Side -->
                            <td
                                style="padding:8px 10px; vertical-align:top; <?php if ($exp_row) {
                                    if ($exp_row['is_head'])
                                        echo 'font-weight:bold;';
                                    if ($exp_row['is_sub'])
                                        echo 'padding-left:25px; font-style:italic; color:#555;';
                                } ?>">
                                <?php if ($exp_row): ?>
                                    <?php echo htmlspecialchars($exp_row['text']); ?>
                                <?php endif; ?>
                            </td>
                            <td
                                style="text-align:right; padding:8px 10px; vertical-align:top; <?php if ($exp_row && $exp_row['is_head'])
                                    echo 'font-weight:bold;'; ?>">
                                <?php if ($exp_row): ?>
                                    <?php echo $exp_row['amount']; ?>
                                <?php endif; ?>
                            </td>

                            <!-- Income Side -->
                            <td
                                style="padding:8px 10px; vertical-align:top; <?php if ($inc_row) {
                                    if ($inc_row['is_head'])
                                        echo 'font-weight:bold;';
                                    if ($inc_row['is_sub'])
                                        echo 'padding-left:25px; font-style:italic; color:#555;';
                                } ?>">
                                <?php if ($inc_row): ?>
                                    <?php echo htmlspecialchars($inc_row['text']); ?>
                                <?php endif; ?>
                            </td>
                            <td
                                style="text-align:right; padding:8px 10px; vertical-align:top; <?php if ($inc_row && $inc_row['is_head'])
                                    echo 'font-weight:bold;'; ?>">
                                <?php if ($inc_row): ?>
                                    <?php echo $inc_row['amount']; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endfor; ?>

                    <!-- Total Rows -->
                    <tr>
                        <td style="padding:10px; font-weight:bold; border-top:2px solid #000;">Total Expenses</td>
                        <td style="text-align:right; padding:10px; font-weight:bold; border-top:2px solid #000;">
                            <?php echo number_format($expense_total, 3); ?>
                        </td>
                        <td style="padding:10px; font-weight:bold; border-top:2px solid #000;">Total Income</td>
                        <td style="text-align:right; padding:10px; font-weight:bold; border-top:2px solid #000;">
                            <?php echo number_format($income_total, 3); ?>
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

        .no-print,
        .screen-view {
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
        .net-result.profit,
        .net-result.loss {
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        display: flex;
        flex-direction: column;
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
        flex: 1;
    }

    .pl-group {
        margin-bottom: 15px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: all 0.3s;
    }

    .pl-group:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .pl-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        background: white;
        margin-bottom: 0;
        border-radius: 0;
        box-shadow: none;
        transition: all 0.3s;
    }

    .pl-item.pl-head {
        font-weight: 700;
        background: #ffffff;
        border-left: 4px solid #667eea;
    }

    .income-section .pl-item.pl-head {
        border-left-color: #11998e;
    }

    .expense-section .pl-item.pl-head {
        border-left-color: #eb3349;
    }

    .pl-sub-items {
        background: #fafafa;
        border-top: 1px solid #f0f0f0;
    }

    .pl-item.pl-sub-item {
        padding-left: 35px;
        font-size: 13px;
        color: #555;
        background: transparent;
        border-bottom: 1px solid #f3f3f3;
    }

    .pl-item.pl-sub-item:last-child {
        border-bottom: none;
    }

    .item-name {
        font-size: 14px;
        color: #333;
        font-weight: 500;
    }

    .pl-item.pl-head .item-name {
        font-weight: 700;
        color: #1a1a1a;
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