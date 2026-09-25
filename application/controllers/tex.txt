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
                    <div class="col-md-6 filter-actions">
                        <button type="submit" class="btn btn-generate">
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

    <?php
    // --- Data Extraction & Calculation ---
    $sales_wo_tax = (float) (is_array($sales_summary ?? null) ? ($sales_summary['total_sales_wo_tax'] ?? 0) : ($sales ?? 0));
    $sales_tax = (float) (is_array($sales_summary ?? null) ? ($sales_summary['total_sales_tax'] ?? 0) : 0);
    $sales_with_tax = (float) (is_array($sales_summary ?? null) ? ($sales_summary['total_sales_with_tax'] ?? $sales_wo_tax) : $sales_wo_tax);

    $purchases_wo_tax = (float) (is_array($purchases_summary ?? null) ? ($purchases_summary['total_purchases_wo_tax'] ?? 0) : ($purchases ?? 0));
    $purchases_tax = (float) (is_array($purchases_summary ?? null) ? ($purchases_summary['total_purchases_tax'] ?? 0) : 0);
    $purchases_with_tax = (float) (is_array($purchases_summary ?? null) ? ($purchases_summary['total_purchases_with_tax'] ?? $purchases_wo_tax) : $purchases_wo_tax);

    // Group other income by parent head
    $grouped_other_income = [];
    $other_income_total_wo_tax = 0;
    $other_income_total_tax = 0;
    $other_income_total_with_tax = 0;

    if (!empty($other_income) && is_array($other_income)) {
        foreach ($other_income as $row) {
            $head = $row['inc_type'] ?? 'Other Income';
            $amt_wo_tax = (float) ($row['inc_amt_wo_tax'] ?? $row['inc_amt'] ?? 0);
            $tax = (float) ($row['tax_amt'] ?? 0);
            $amt_with_tax = (float) ($row['inc_amt_with_tax'] ?? $row['inc_amt'] ?? 0);

            if (!isset($grouped_other_income[$head])) {
                $grouped_other_income[$head] = [
                    'sub_items' => [],
                    'subtotal_wo_tax' => 0,
                    'subtotal_tax' => 0,
                    'subtotal_with_tax' => 0,
                ];
            }
            $grouped_other_income[$head]['sub_items'][] = [
                'name' => $row['sub_typ'] ?? '',
                'amount_wo_tax' => $amt_wo_tax,
                'tax_amount' => $tax,
                'amount_with_tax' => $amt_with_tax
            ];
            $grouped_other_income[$head]['subtotal_wo_tax'] += $amt_wo_tax;
            $grouped_other_income[$head]['subtotal_tax'] += $tax;
            $grouped_other_income[$head]['subtotal_with_tax'] += $amt_with_tax;

            $other_income_total_wo_tax += $amt_wo_tax;
            $other_income_total_tax += $tax;
            $other_income_total_with_tax += $amt_with_tax;
        }
    }

    // Calculate indirect expenses subtotals
    $indirect_total_wo_tax = 0;
    $indirect_total_tax = 0;
    $indirect_total_with_tax = 0;

    if (!empty($indirect_expenses) && is_array($indirect_expenses)) {
        foreach ($indirect_expenses as $row) {
            $indirect_total_wo_tax += (float) ($row['exp_amt_wo_tax'] ?? $row['exp_amt'] ?? 0);
            $indirect_total_tax += (float) ($row['tax_amt'] ?? 0);
            $indirect_total_with_tax += (float) ($row['exp_amt_with_tax'] ?? $row['exp_amt'] ?? 0);
        }
    }

    // Grand Totals:
    // Income
    $income_total_wo_tax = $sales_wo_tax + $other_income_total_wo_tax;
    $income_total_tax = $sales_tax + $other_income_total_tax;
    $income_total_with_tax = $sales_with_tax + $other_income_total_with_tax;

    // Expenses
    $expense_total_wo_tax = $purchases_wo_tax + $indirect_total_wo_tax;
    $expense_total_tax = $purchases_tax + $indirect_total_tax;
    $expense_total_with_tax = $purchases_with_tax + $indirect_total_with_tax;

    // Net Results
    $net_profit_wo_tax = $income_total_wo_tax - $expense_total_wo_tax;
    $is_profit_wo_tax = $net_profit_wo_tax >= 0;

    $net_profit_with_tax = $income_total_with_tax - $expense_total_with_tax;
    $is_profit_with_tax = $net_profit_with_tax >= 0;

    $net_tax_diff = $income_total_tax - $expense_total_tax;

    // Build Flat list of rows for the comparative table & print layout
    $income_table_rows = [];
    $income_table_rows[] = [
        'text' => 'Sales',
        'wo_tax' => $sales_wo_tax,
        'tax' => $sales_tax,
        'with_tax' => $sales_with_tax,
        'is_head' => true,
        'is_sub' => false
    ];
    foreach ($grouped_other_income as $head_name => $head_data) {
        $income_table_rows[] = [
            'text' => $head_name,
            'wo_tax' => $head_data['subtotal_wo_tax'],
            'tax' => $head_data['subtotal_tax'],
            'with_tax' => $head_data['subtotal_with_tax'],
            'is_head' => true,
            'is_sub' => false
        ];
        foreach ($head_data['sub_items'] as $sub_item) {
            $income_table_rows[] = [
                'text' => $sub_item['name'],
                'wo_tax' => $sub_item['amount_wo_tax'],
                'tax' => $sub_item['tax_amount'],
                'with_tax' => $sub_item['amount_with_tax'],
                'is_head' => false,
                'is_sub' => true
            ];
        }
    }

    $expense_table_rows = [];
    $expense_table_rows[] = [
        'text' => 'Purchases',
        'wo_tax' => $purchases_wo_tax,
        'tax' => $purchases_tax,
        'with_tax' => $purchases_with_tax,
        'is_head' => true,
        'is_sub' => false
    ];
    $expense_table_rows[] = [
        'text' => 'Indirect Expenses',
        'wo_tax' => $indirect_total_wo_tax,
        'tax' => $indirect_total_tax,
        'with_tax' => $indirect_total_with_tax,
        'is_head' => true,
        'is_sub' => false
    ];
    if (!empty($indirect_expenses) && is_array($indirect_expenses)) {
        foreach ($indirect_expenses as $row) {
            $expense_table_rows[] = [
                'text' => ucwords(strtolower($row['exp_type'])),
                'wo_tax' => (float) ($row['exp_amt_wo_tax'] ?? $row['exp_amt'] ?? 0),
                'tax' => (float) ($row['tax_amt'] ?? 0),
                'with_tax' => (float) ($row['exp_amt_with_tax'] ?? $row['exp_amt'] ?? 0),
                'is_head' => false,
                'is_sub' => true
            ];
        }
    }

    $max_table_rows = max(count($income_table_rows), count($expense_table_rows));
    ?>

    <!-- EXECUTIVE KPI SUMMARY CARDS -->
    <div class="kpi-grid no-print">
        <!-- KPI 1: Net Result Without Tax -->
        <div class="kpi-card <?php echo $is_profit_wo_tax ? 'kpi-profit' : 'kpi-loss'; ?>">
            <div class="kpi-icon">
                <i class="fa fa-<?php echo $is_profit_wo_tax ? 'arrow-circle-up' : 'arrow-circle-down'; ?>"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-header-row">
                    <span class="kpi-title">NET <?php echo $is_profit_wo_tax ? 'PROFIT' : 'LOSS'; ?> (WITHOUT
                        TAX)</span>
                    <span class="kpi-tag kpi-tag-wo">Excl. Tax</span>
                </div>
                <h3 class="kpi-value"><?php echo number_format(abs($net_profit_wo_tax), 3); ?> <small>BHD</small></h3>
                <p class="kpi-subtext">Income: <?php echo number_format($income_total_wo_tax, 3); ?> | Expense:
                    <?php echo number_format($expense_total_wo_tax, 3); ?></p>
            </div>
        </div>

        <!-- KPI 2: Net Result With Tax -->
        <div class="kpi-card <?php echo $is_profit_with_tax ? 'kpi-profit' : 'kpi-loss'; ?>">
            <div class="kpi-icon">
                <i class="fa fa-<?php echo $is_profit_with_tax ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-header-row">
                    <span class="kpi-title">NET <?php echo $is_profit_with_tax ? 'PROFIT' : 'LOSS'; ?> (WITH TAX)</span>
                    <span class="kpi-tag kpi-tag-with">Incl. Tax</span>
                </div>
                <h3 class="kpi-value"><?php echo number_format(abs($net_profit_with_tax), 3); ?> <small>BHD</small></h3>
                <p class="kpi-subtext">Income: <?php echo number_format($income_total_with_tax, 3); ?> | Expense:
                    <?php echo number_format($expense_total_with_tax, 3); ?></p>
            </div>
        </div>

        <!-- KPI 3: Tax / VAT Summary -->
        <div class="kpi-card kpi-tax">
            <div class="kpi-icon">
                <i class="fa fa-percent"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-header-row">
                    <span class="kpi-title">NET VAT BALANCE</span>
                    <span class="kpi-tag kpi-tag-tax"><?php echo $net_tax_diff >= 0 ? 'Payable' : 'Credit'; ?></span>
                </div>
                <h3 class="kpi-value"><?php echo number_format(abs($net_tax_diff), 3); ?> <small>BHD</small></h3>
                <p class="kpi-subtext">Output VAT: <?php echo number_format($income_total_tax, 3); ?> | Input VAT:
                    <?php echo number_format($expense_total_tax, 3); ?></p>
            </div>
        </div>
    </div>

    <!-- REPORT CARD -->
    <div class="report-card">

        <!-- VIEW SWITCHER TABS (Screen Only) -->
        <div class="report-toolbar no-print">
            <div class="report-toolbar-left">
                <span class="toolbar-label"><i class="fa fa-eye"></i> View Mode:</span>
                <div class="btn-group pl-view-switcher" role="group">
                    <button type="button" class="btn pl-tab-btn active" data-target="view-split">
                        <i class="fa fa-columns"></i> Split View (Side-by-Side)
                    </button>
                    <button type="button" class="btn pl-tab-btn" data-target="view-wo-tax">
                        <i class="fa fa-file-text-o"></i> Without Tax Only
                    </button>
                    <button type="button" class="btn pl-tab-btn" data-target="view-with-tax">
                        <i class="fa fa-shield"></i> With Tax Only
                    </button>
                    <button type="button" class="btn pl-tab-btn" data-target="view-table">
                        <i class="fa fa-table"></i> Comparative Table
                    </button>
                </div>
            </div>
            <div class="report-toolbar-right">
                <button type="button" onclick="window.print()" class="btn btn-print-quick">
                    <i class="fa fa-print"></i> Print Statement
                </button>
            </div>
        </div>

        <!-- REPORT HEADER (Screen & Print) -->
        <div class="report-header">
            <div class="company-logo no-print">
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

        <!-- ============================================================== -->
        <!-- TAB 1: SPLIT VIEW (SIDE-BY-SIDE: WITHOUT TAX vs WITH TAX)     -->
        <!-- ============================================================== -->
        <div id="view-split" class="pl-view-panel active screen-view">
            <div class="split-comparison-container">

                <!-- LEFT SIDE: WITHOUT TAX STATEMENT -->
                <div class="split-column split-column-wo-tax">
                    <div class="split-column-header">
                        <div class="split-column-title">
                            <i class="fa fa-file-text-o"></i> STATEMENT WITHOUT TAX
                        </div>
                        <span class="split-pill split-pill-wo">Base / Taxable Value</span>
                    </div>

                    <div class="pl-grid-split">
                        <!-- EXPENSE SECTION (WITHOUT TAX) -->
                        <div class="pl-section expense-section">
                            <div class="section-header">
                                <i class="fa fa-arrow-down"></i> EXPENSES (EXCL. TAX)
                            </div>
                            <div class="section-body">
                                <!-- Purchases -->
                                <div class="pl-group">
                                    <div class="pl-item pl-head">
                                        <span class="item-name">Purchases</span>
                                        <span
                                            class="item-amount"><?php echo number_format($purchases_wo_tax, 3); ?></span>
                                    </div>
                                </div>

                                <!-- Indirect Expenses -->
                                <div class="pl-group">
                                    <div class="pl-item pl-head">
                                        <span class="item-name">Indirect Expenses</span>
                                        <span
                                            class="item-amount"><?php echo number_format($indirect_total_wo_tax, 3); ?></span>
                                    </div>
                                    <?php if (!empty($indirect_expenses)): ?>
                                        <div class="pl-sub-items">
                                            <?php foreach ($indirect_expenses as $expense): ?>
                                                <div class="pl-item pl-sub-item">
                                                    <span
                                                        class="item-name text-capitalize"><?php echo strtolower($expense['exp_type']); ?></span>
                                                    <span class="item-amount">
                                                        <?php echo number_format((float) ($expense['exp_amt_wo_tax'] ?? $expense['exp_amt'] ?? 0), 3); ?>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="section-total">
                                <span>Total Expenses (Without Tax)</span>
                                <span class="total-amount"><?php echo number_format($expense_total_wo_tax, 3); ?></span>
                            </div>
                        </div>

                        <!-- INCOME SECTION (WITHOUT TAX) -->
                        <div class="pl-section income-section">
                            <div class="section-header">
                                <i class="fa fa-arrow-up"></i> INCOME (EXCL. TAX)
                            </div>
                            <div class="section-body">
                                <!-- Sales -->
                                <div class="pl-group">
                                    <div class="pl-item pl-head">
                                        <span class="item-name">Sales</span>
                                        <span class="item-amount"><?php echo number_format($sales_wo_tax, 3); ?></span>
                                    </div>
                                </div>

                                <!-- Other Income -->
                                <?php if (!empty($grouped_other_income)): ?>
                                    <?php foreach ($grouped_other_income as $head_name => $head_data): ?>
                                        <div class="pl-group">
                                            <div class="pl-item pl-head">
                                                <span class="item-name"><?php echo htmlspecialchars($head_name); ?></span>
                                                <span class="item-amount">
                                                    <?php echo number_format($head_data['subtotal_wo_tax'], 3); ?>
                                                </span>
                                            </div>
                                            <div class="pl-sub-items">
                                                <?php foreach ($head_data['sub_items'] as $sub_item): ?>
                                                    <div class="pl-item pl-sub-item">
                                                        <span
                                                            class="item-name"><?php echo htmlspecialchars($sub_item['name']); ?></span>
                                                        <span class="item-amount">
                                                            <?php echo number_format($sub_item['amount_wo_tax'], 3); ?>
                                                        </span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="pl-empty-text">No other income recorded in this period</div>
                                <?php endif; ?>
                            </div>
                            <div class="section-total">
                                <span>Total Income (Without Tax)</span>
                                <span class="total-amount"><?php echo number_format($income_total_wo_tax, 3); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- NET RESULT (WITHOUT TAX) -->
                    <div class="net-result <?php echo $is_profit_wo_tax ? 'profit' : 'loss'; ?>">
                        <div class="result-icon">
                            <i
                                class="fa fa-<?php echo $is_profit_wo_tax ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                        </div>
                        <div class="result-text">
                            <h3>NET <?php echo $is_profit_wo_tax ? 'PROFIT' : 'LOSS'; ?> (WITHOUT TAX)</h3>
                            <p class="result-amount"><?php echo number_format(abs($net_profit_wo_tax), 3); ?> <span
                                    class="currency">BHD</span></p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE: WITH TAX STATEMENT -->
                <div class="split-column split-column-with-tax">
                    <div class="split-column-header">
                        <div class="split-column-title">
                            <i class="fa fa-shield"></i> STATEMENT WITH TAX
                        </div>
                        <span class="split-pill split-pill-with">Gross / Inclusive Value</span>
                    </div>

                    <div class="pl-grid-split">
                        <!-- EXPENSE SECTION (WITH TAX) -->
                        <div class="pl-section expense-section">
                            <div class="section-header">
                                <i class="fa fa-arrow-down"></i> EXPENSES (INCL. TAX)
                            </div>
                            <div class="section-body">
                                <!-- Purchases -->
                                <div class="pl-group">
                                    <div class="pl-item pl-head">
                                        <span class="item-name">Purchases</span>
                                        <span
                                            class="item-amount"><?php echo number_format($purchases_wo_tax, 3); ?></span>
                                    </div>
                                </div>

                                <!-- Indirect Expenses -->
                                <div class="pl-group">
                                    <div class="pl-item pl-head">
                                        <span class="item-name">Indirect Expenses</span>
                                        <span
                                            class="item-amount"><?php echo number_format($indirect_total_wo_tax, 3); ?></span>
                                    </div>
                                    <?php if (!empty($indirect_expenses)): ?>
                                        <div class="pl-sub-items">
                                            <?php foreach ($indirect_expenses as $expense): ?>
                                                <div class="pl-item pl-sub-item">
                                                    <span
                                                        class="item-name text-capitalize"><?php echo strtolower($expense['exp_type']); ?></span>
                                                    <span class="item-amount">
                                                        <?php echo number_format((float) ($expense['exp_amt_wo_tax'] ?? $expense['exp_amt'] ?? 0), 3); ?>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="section-total" style="font-size: 13px; color: #64748b; border-top: 1px dashed #e2e8f0; padding-top: 10px;">
                                <span>Total Expenses (Without Tax)</span>
                                <span class="total-amount"><?php echo number_format($expense_total_wo_tax, 3); ?></span>
                            </div>
                            <div class="section-total"
                                style="font-size: 13px; color: #64748b; padding-top: 5px; margin-top: -5px;">
                                <span>Total Tax Amount</span>
                                <span class="total-amount"><?php echo number_format($expense_total_tax, 3); ?></span>
                            </div>
                            <div class="section-total">
                                <span>Total Expenses (With Tax)</span>
                                <span
                                    class="total-amount"><?php echo number_format($expense_total_with_tax, 3); ?></span>
                            </div>
                        </div>

                        <!-- INCOME SECTION (WITH TAX) -->
                        <div class="pl-section income-section">
                            <div class="section-header">
                                <i class="fa fa-arrow-up"></i> INCOME (INCL. TAX)
                            </div>
                            <div class="section-body">
                                <!-- Sales -->
                                <div class="pl-group">
                                    <div class="pl-item pl-head">
                                        <span class="item-name">Sales</span>
                                        <span
                                            class="item-amount"><?php echo number_format($sales_wo_tax, 3); ?></span>
                                    </div>
                                </div>

                                <!-- Other Income -->
                                <?php if (!empty($grouped_other_income)): ?>
                                    <?php foreach ($grouped_other_income as $head_name => $head_data): ?>
                                        <div class="pl-group">
                                            <div class="pl-item pl-head">
                                                <span class="item-name"><?php echo htmlspecialchars($head_name); ?></span>
                                                <span class="item-amount">
                                                    <?php echo number_format($head_data['subtotal_wo_tax'], 3); ?>
                                                </span>
                                            </div>
                                            <div class="pl-sub-items">
                                                <?php foreach ($head_data['sub_items'] as $sub_item): ?>
                                                    <div class="pl-item pl-sub-item">
                                                        <span
                                                            class="item-name"><?php echo htmlspecialchars($sub_item['name']); ?></span>
                                                        <span class="item-amount">
                                                            <?php echo number_format($sub_item['amount_wo_tax'], 3); ?>
                                                        </span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="pl-empty-text">No other income recorded in this period</div>
                                <?php endif; ?>
                            </div>
                            <div class="section-total" style="font-size: 13px; color: #64748b; border-top: 1px dashed #e2e8f0; padding-top: 10px;">
                                <span>Total Income (Without Tax)</span>
                                <span class="total-amount"><?php echo number_format($income_total_wo_tax, 3); ?></span>
                            </div>
                            <div class="section-total"
                                style="font-size: 13px; color: #64748b; padding-top: 5px; margin-top: -5px;">
                                <span>Total Tax Amount</span>
                                <span class="total-amount"><?php echo number_format($income_total_tax, 3); ?></span>
                            </div>
                            <div class="section-total">
                                <span>Total Income (With Tax)</span>
                                <span
                                    class="total-amount"><?php echo number_format($income_total_with_tax, 3); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- NET RESULT (WITH TAX) -->
                    <div class="net-result <?php echo $is_profit_with_tax ? 'profit' : 'loss'; ?>">
                        <div class="result-icon">
                            <i
                                class="fa fa-<?php echo $is_profit_with_tax ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                        </div>
                        <div class="result-text">
                            <h3>NET <?php echo $is_profit_with_tax ? 'PROFIT' : 'LOSS'; ?> (WITH TAX)</h3>
                            <p class="result-amount"><?php echo number_format(abs($net_profit_with_tax), 3); ?> <span
                                    class="currency">BHD</span></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ============================================================== -->
        <!-- TAB 2: WITHOUT TAX FULL VIEW                                   -->
        <!-- ============================================================== -->
        <div id="view-wo-tax" class="pl-view-panel screen-view">
            <div class="single-view-banner wo-tax-banner">
                <i class="fa fa-info-circle"></i> Showing Profit & Loss Statement based on <strong>Without Tax</strong>
                (Base / Taxable Values)
            </div>
            <div class="pl-grid">
                <!-- EXPENSE SECTION -->
                <div class="pl-section expense-section">
                    <div class="section-header">
                        <i class="fa fa-arrow-down"></i> EXPENSES (WITHOUT TAX)
                    </div>
                    <div class="section-body">
                        <!-- Purchases -->
                        <div class="pl-group">
                            <div class="pl-item pl-head">
                                <span class="item-name">Purchases</span>
                                <span class="item-amount"><?php echo number_format($purchases_wo_tax, 3); ?></span>
                            </div>
                        </div>

                        <!-- Indirect Expenses -->
                        <div class="pl-group">
                            <div class="pl-item pl-head">
                                <span class="item-name">Indirect Expenses</span>
                                <span class="item-amount"><?php echo number_format($indirect_total_wo_tax, 3); ?></span>
                            </div>
                            <?php if (!empty($indirect_expenses)): ?>
                                <div class="pl-sub-items">
                                    <?php foreach ($indirect_expenses as $expense): ?>
                                        <div class="pl-item pl-sub-item">
                                            <span
                                                class="item-name text-capitalize"><?php echo strtolower($expense['exp_type']); ?></span>
                                            <span class="item-amount">
                                                <?php echo number_format((float) ($expense['exp_amt_wo_tax'] ?? $expense['exp_amt'] ?? 0), 3); ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="section-total">
                        <span>Total Expenses (Without Tax)</span>
                        <span class="total-amount"><?php echo number_format($expense_total_wo_tax, 3); ?></span>
                    </div>
                </div>

                <!-- INCOME SECTION -->
                <div class="pl-section income-section">
                    <div class="section-header">
                        <i class="fa fa-arrow-up"></i> INCOME (WITHOUT TAX)
                    </div>
                    <div class="section-body">
                        <!-- Sales -->
                        <div class="pl-group">
                            <div class="pl-item pl-head">
                                <span class="item-name">Sales</span>
                                <span class="item-amount"><?php echo number_format($sales_wo_tax, 3); ?></span>
                            </div>
                        </div>

                        <!-- Other Income -->
                        <?php if (!empty($grouped_other_income)): ?>
                            <?php foreach ($grouped_other_income as $head_name => $head_data): ?>
                                <div class="pl-group">
                                    <div class="pl-item pl-head">
                                        <span class="item-name"><?php echo htmlspecialchars($head_name); ?></span>
                                        <span class="item-amount">
                                            <?php echo number_format($head_data['subtotal_wo_tax'], 3); ?>
                                        </span>
                                    </div>
                                    <div class="pl-sub-items">
                                        <?php foreach ($head_data['sub_items'] as $sub_item): ?>
                                            <div class="pl-item pl-sub-item">
                                                <span class="item-name"><?php echo htmlspecialchars($sub_item['name']); ?></span>
                                                <span class="item-amount">
                                                    <?php echo number_format($sub_item['amount_wo_tax'], 3); ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="section-total">
                        <span>Total Income (Without Tax)</span>
                        <span class="total-amount"><?php echo number_format($income_total_wo_tax, 3); ?></span>
                    </div>
                </div>
            </div>

            <!-- NET RESULT -->
            <div class="net-result <?php echo $is_profit_wo_tax ? 'profit' : 'loss'; ?>">
                <div class="result-icon">
                    <i class="fa fa-<?php echo $is_profit_wo_tax ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                </div>
                <div class="result-text">
                    <h3>NET <?php echo $is_profit_wo_tax ? 'PROFIT' : 'LOSS'; ?> (WITHOUT TAX)</h3>
                    <p class="result-amount"><?php echo number_format(abs($net_profit_wo_tax), 3); ?> BHD</p>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- TAB 3: WITH TAX FULL VIEW                                      -->
        <!-- ============================================================== -->
        <div id="view-with-tax" class="pl-view-panel screen-view">
            <div class="single-view-banner with-tax-banner">
                <i class="fa fa-info-circle"></i> Showing Profit & Loss Statement based on <strong>With Tax</strong>
                (Gross / Inclusive Values)
            </div>
            <div class="pl-grid">
                <!-- EXPENSE SECTION -->
                <div class="pl-section expense-section">
                    <div class="section-header">
                        <i class="fa fa-arrow-down"></i> EXPENSES (INCL. TAX)
                    </div>
                    <div class="section-body">
                        <!-- Purchases -->
                        <div class="pl-group">
                            <div class="pl-item pl-head">
                                <span class="item-name">Purchases</span>
                                <span class="item-amount"><?php echo number_format($purchases_wo_tax, 3); ?></span>
                            </div>
                        </div>

                        <!-- Indirect Expenses -->
                        <div class="pl-group">
                            <div class="pl-item pl-head">
                                <span class="item-name">Indirect Expenses</span>
                                <span
                                    class="item-amount"><?php echo number_format($indirect_total_wo_tax, 3); ?></span>
                            </div>
                            <?php if (!empty($indirect_expenses)): ?>
                                <div class="pl-sub-items">
                                    <?php foreach ($indirect_expenses as $expense): ?>
                                        <div class="pl-item pl-sub-item">
                                            <span
                                                class="item-name text-capitalize"><?php echo strtolower($expense['exp_type']); ?></span>
                                            <span class="item-amount">
                                                <?php echo number_format((float) ($expense['exp_amt_wo_tax'] ?? $expense['exp_amt'] ?? 0), 3); ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="section-total" style="font-size: 13px; color: #64748b; border-top: 1px dashed #e2e8f0; padding-top: 10px;">
                        <span>Total Expenses (Without Tax)</span>
                        <span class="total-amount"><?php echo number_format($expense_total_wo_tax, 3); ?></span>
                    </div>
                    <div class="section-total"
                        style="font-size: 13px; color: #64748b; padding-top: 5px; margin-top: -5px;">
                        <span>Total Tax Amount</span>
                        <span class="total-amount"><?php echo number_format($expense_total_tax, 3); ?></span>
                    </div>
                    <div class="section-total">
                        <span>Total Expenses (With Tax)</span>
                        <span class="total-amount"><?php echo number_format($expense_total_with_tax, 3); ?></span>
                    </div>
                </div>

                <!-- INCOME SECTION -->
                <div class="pl-section income-section">
                    <div class="section-header">
                        <i class="fa fa-arrow-up"></i> INCOME (INCL. TAX)
                    </div>
                    <div class="section-body">
                        <!-- Sales -->
                        <div class="pl-group">
                            <div class="pl-item pl-head">
                                <span class="item-name">Sales</span>
                                <span class="item-amount"><?php echo number_format($sales_wo_tax, 3); ?></span>
                            </div>
                        </div>

                        <!-- Other Income -->
                        <?php if (!empty($grouped_other_income)): ?>
                            <?php foreach ($grouped_other_income as $head_name => $head_data): ?>
                                <div class="pl-group">
                                    <div class="pl-item pl-head">
                                        <span class="item-name"><?php echo htmlspecialchars($head_name); ?></span>
                                        <span class="item-amount">
                                            <?php echo number_format($head_data['subtotal_wo_tax'], 3); ?>
                                        </span>
                                    </div>
                                    <div class="pl-sub-items">
                                        <?php foreach ($head_data['sub_items'] as $sub_item): ?>
                                            <div class="pl-item pl-sub-item">
                                                <span class="item-name"><?php echo htmlspecialchars($sub_item['name']); ?></span>
                                                <span class="item-amount">
                                                    <?php echo number_format($sub_item['amount_wo_tax'], 3); ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="section-total" style="font-size: 13px; color: #64748b; border-top: 1px dashed #e2e8f0; padding-top: 10px;">
                        <span>Total Income (Without Tax)</span>
                        <span class="total-amount"><?php echo number_format($income_total_wo_tax, 3); ?></span>
                    </div>
                    <div class="section-total"
                        style="font-size: 13px; color: #64748b; padding-top: 5px; margin-top: -5px;">
                        <span>Total Tax Amount</span>
                        <span class="total-amount"><?php echo number_format($income_total_tax, 3); ?></span>
                    </div>
                    <div class="section-total">
                        <span>Total Income (With Tax)</span>
                        <span class="total-amount"><?php echo number_format($income_total_with_tax, 3); ?></span>
                    </div>
                </div>
            </div>

            <!-- NET RESULT -->
            <div class="net-result <?php echo $is_profit_with_tax ? 'profit' : 'loss'; ?>">
                <div class="result-icon">
                    <i class="fa fa-<?php echo $is_profit_with_tax ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                </div>
                <div class="result-text">
                    <h3>NET <?php echo $is_profit_with_tax ? 'PROFIT' : 'LOSS'; ?> (WITH TAX)</h3>
                    <p class="result-amount"><?php echo number_format(abs($net_profit_with_tax), 3); ?> BHD</p>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- TAB 4: COMPARATIVE TABLE VIEW (SCREEN)                         -->
        <!-- ============================================================== -->
        <div id="view-table" class="pl-view-panel screen-view">
            <div class="table-responsive">
                <table class="table table-bordered table-hover pl-comparison-table">
                    <thead>
                        <tr class="table-main-header">
                            <th colspan="4" class="text-center expense-col-header">EXPENSES</th>
                            <th colspan="4" class="text-center income-col-header">INCOME</th>
                        </tr>
                        <tr class="table-sub-header">
                            <th>Particulars</th>
                            <th class="text-right">Without Tax</th>
                            <th class="text-right">VAT / Tax</th>
                            <th class="text-right">With Tax</th>

                            <th>Particulars</th>
                            <th class="text-right">Without Tax</th>
                            <th class="text-right">VAT / Tax</th>
                            <th class="text-right">With Tax</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 0; $i < $max_table_rows; $i++):
                            $exp_row = $expense_table_rows[$i] ?? null;
                            $inc_row = $income_table_rows[$i] ?? null;
                            ?>
                            <tr>
                                <!-- Expense Side -->
                                <td
                                    class="<?php echo ($exp_row && $exp_row['is_head']) ? 'cell-head' : ''; ?> <?php echo ($exp_row && $exp_row['is_sub']) ? 'cell-sub' : ''; ?>">
                                    <?php echo $exp_row ? htmlspecialchars($exp_row['text']) : ''; ?>
                                </td>
                                <td class="text-right <?php echo ($exp_row && $exp_row['is_head']) ? 'cell-head' : ''; ?>">
                                    <?php echo $exp_row ? number_format($exp_row['wo_tax'], 3) : ''; ?>
                                </td>
                                <td
                                    class="text-right <?php echo ($exp_row && $exp_row['is_head']) ? 'cell-head text-muted' : 'text-muted'; ?>">
                                    <?php echo $exp_row ? number_format($exp_row['tax'], 3) : ''; ?>
                                </td>
                                <td class="text-right <?php echo ($exp_row && $exp_row['is_head']) ? 'cell-head' : ''; ?>">
                                    <?php echo $exp_row ? number_format($exp_row['with_tax'], 3) : ''; ?>
                                </td>

                                <!-- Income Side -->
                                <td
                                    class="<?php echo ($inc_row && $inc_row['is_head']) ? 'cell-head' : ''; ?> <?php echo ($inc_row && $inc_row['is_sub']) ? 'cell-sub' : ''; ?>">
                                    <?php echo $inc_row ? htmlspecialchars($inc_row['text']) : ''; ?>
                                </td>
                                <td class="text-right <?php echo ($inc_row && $inc_row['is_head']) ? 'cell-head' : ''; ?>">
                                    <?php echo $inc_row ? number_format($inc_row['wo_tax'], 3) : ''; ?>
                                </td>
                                <td
                                    class="text-right <?php echo ($inc_row && $inc_row['is_head']) ? 'cell-head text-muted' : 'text-muted'; ?>">
                                    <?php echo $inc_row ? number_format($inc_row['tax'], 3) : ''; ?>
                                </td>
                                <td class="text-right <?php echo ($inc_row && $inc_row['is_head']) ? 'cell-head' : ''; ?>">
                                    <?php echo $inc_row ? number_format($inc_row['with_tax'], 3) : ''; ?>
                                </td>
                            </tr>
                        <?php endfor; ?>

                        <!-- Grand Total Row -->
                        <tr class="table-total-row">
                            <td><strong>Total Expenses</strong></td>
                            <td class="text-right">
                                <strong><?php echo number_format($expense_total_wo_tax, 3); ?></strong></td>
                            <td class="text-right"><strong><?php echo number_format($expense_total_tax, 3); ?></strong>
                            </td>
                            <td class="text-right">
                                <strong><?php echo number_format($expense_total_with_tax, 3); ?></strong></td>

                            <td><strong>Total Income</strong></td>
                            <td class="text-right">
                                <strong><?php echo number_format($income_total_wo_tax, 3); ?></strong></td>
                            <td class="text-right"><strong><?php echo number_format($income_total_tax, 3); ?></strong>
                            </td>
                            <td class="text-right">
                                <strong><?php echo number_format($income_total_with_tax, 3); ?></strong></td>
                        </tr>

                        <!-- Net Profit / Loss Row -->
                        <tr class="table-net-row">
                            <td colspan="4" class="text-center font-bold">
                                NET <?php echo $is_profit_wo_tax ? 'PROFIT' : 'LOSS'; ?> (WITHOUT TAX):
                                <span class="<?php echo $is_profit_wo_tax ? 'text-success' : 'text-danger'; ?>"
                                    style="font-size:16px; margin-left:8px;">
                                    <?php echo number_format(abs($net_profit_wo_tax), 3); ?> BHD
                                </span>
                            </td>
                            <td colspan="4" class="text-center font-bold">
                                NET <?php echo $is_profit_with_tax ? 'PROFIT' : 'LOSS'; ?> (WITH TAX):
                                <span class="<?php echo $is_profit_with_tax ? 'text-success' : 'text-danger'; ?>"
                                    style="font-size:16px; margin-left:8px;">
                                    <?php echo number_format(abs($net_profit_with_tax), 3); ?> BHD
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- PRINT VIEW: Table Layout (Visible Only When Printing)          -->
        <!-- ============================================================== -->
        <div class="report-content print-view">

            <div class="print-header">
                <h2 style="text-align:center; margin:0 0 5px 0;">PROFIT & LOSS STATEMENT</h2>
                <h4 style="text-align:center; margin:0 0 10px 0; color:#555;">(WITH TAX & WITHOUT TAX COMPARISON)</h4>
                <p style="text-align:center; color:#555; margin:0 0 20px 0;">
                    For the period from <?php echo date('d M Y', strtotime($srch_from_date)); ?>
                    to <?php echo date('d M Y', strtotime($srch_to_date)); ?>
                </p>
            </div>

            <!-- Print KPI Summary -->
            <table style="width:100%; border-collapse:collapse; margin-bottom:20px; border:1px solid #ccc;">
                <tr style="background:#f5f5f5;">
                    <th style="padding:8px; border:1px solid #ccc; text-align:center;">Metric</th>
                    <th style="padding:8px; border:1px solid #ccc; text-align:center;">Without Tax (BHD)</th>
                    <th style="padding:8px; border:1px solid #ccc; text-align:center;">VAT / Tax (BHD)</th>
                    <th style="padding:8px; border:1px solid #ccc; text-align:center;">With Tax (BHD)</th>
                </tr>
                <tr>
                    <td style="padding:6px 8px; border:1px solid #ccc; font-weight:bold;">Total Income</td>
                    <td style="padding:6px 8px; border:1px solid #ccc; text-align:right;">
                        <?php echo number_format($income_total_wo_tax, 3); ?></td>
                    <td style="padding:6px 8px; border:1px solid #ccc; text-align:right;">
                        <?php echo number_format($income_total_tax, 3); ?></td>
                    <td style="padding:6px 8px; border:1px solid #ccc; text-align:right; font-weight:bold;">
                        <?php echo number_format($income_total_with_tax, 3); ?></td>
                </tr>
                <tr>
                    <td style="padding:6px 8px; border:1px solid #ccc; font-weight:bold;">Total Expenses</td>
                    <td style="padding:6px 8px; border:1px solid #ccc; text-align:right;">
                        <?php echo number_format($expense_total_wo_tax, 3); ?></td>
                    <td style="padding:6px 8px; border:1px solid #ccc; text-align:right;">
                        <?php echo number_format($expense_total_tax, 3); ?></td>
                    <td style="padding:6px 8px; border:1px solid #ccc; text-align:right; font-weight:bold;">
                        <?php echo number_format($expense_total_with_tax, 3); ?></td>
                </tr>
                <tr style="background:<?php echo $is_profit_wo_tax ? '#e8f5e9' : '#ffebee'; ?>;">
                    <td style="padding:8px; border:1px solid #ccc; font-weight:bold; font-size:13px;">NET
                        <?php echo $is_profit_wo_tax ? 'PROFIT' : 'LOSS'; ?></td>
                    <td style="padding:8px; border:1px solid #ccc; text-align:right; font-weight:bold; font-size:13px;">
                        <?php echo number_format(abs($net_profit_wo_tax), 3); ?></td>
                    <td style="padding:8px; border:1px solid #ccc; text-align:right; font-weight:bold; font-size:13px;">
                        <?php echo number_format($net_tax_diff, 3); ?></td>
                    <td style="padding:8px; border:1px solid #ccc; text-align:right; font-weight:bold; font-size:13px;">
                        <?php echo number_format(abs($net_profit_with_tax), 3); ?></td>
                </tr>
            </table>

            <!-- Main Detailed Table -->
            <table class="print-table" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#333; color:#fff;">
                        <th colspan="4" style="text-align:center; padding:8px; border:1px solid #333;">PARTICULARS
                            (EXPENSES)</th>
                        <th colspan="4" style="text-align:center; padding:8px; border:1px solid #333;">PARTICULARS
                            (INCOME)</th>
                    </tr>
                    <tr style="background:#eee; font-size:11px;">
                        <th style="text-align:left; padding:6px; border:1px solid #ccc; width:22%;">Description</th>
                        <th style="text-align:right; padding:6px; border:1px solid #ccc; width:10%;">Wo Tax</th>
                        <th style="text-align:right; padding:6px; border:1px solid #ccc; width:8%;">Tax</th>
                        <th style="text-align:right; padding:6px; border:1px solid #ccc; width:10%;">With Tax</th>

                        <th style="text-align:left; padding:6px; border:1px solid #ccc; width:22%;">Description</th>
                        <th style="text-align:right; padding:6px; border:1px solid #ccc; width:10%;">Wo Tax</th>
                        <th style="text-align:right; padding:6px; border:1px solid #ccc; width:8%;">Tax</th>
                        <th style="text-align:right; padding:6px; border:1px solid #ccc; width:10%;">With Tax</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < $max_table_rows; $i++):
                        $exp_row = $expense_table_rows[$i] ?? null;
                        $inc_row = $income_table_rows[$i] ?? null;
                        ?>
                        <tr style="font-size:11px;">
                            <!-- Expense Side -->
                            <td
                                style="padding:5px 6px; border:1px solid #ddd; vertical-align:top; <?php if ($exp_row) {
                                    if ($exp_row['is_head'])
                                        echo 'font-weight:bold;';
                                    if ($exp_row['is_sub'])
                                        echo 'padding-left:18px; color:#555;';
                                } ?>">
                                <?php echo $exp_row ? htmlspecialchars($exp_row['text']) : ''; ?>
                            </td>
                            <td
                                style="text-align:right; padding:5px 6px; border:1px solid #ddd; vertical-align:top; <?php echo ($exp_row && $exp_row['is_head']) ? 'font-weight:bold;' : ''; ?>">
                                <?php echo $exp_row ? number_format($exp_row['wo_tax'], 3) : ''; ?>
                            </td>
                            <td
                                style="text-align:right; padding:5px 6px; border:1px solid #ddd; vertical-align:top; color:#777;">
                                <?php echo $exp_row ? number_format($exp_row['tax'], 3) : ''; ?>
                            </td>
                            <td
                                style="text-align:right; padding:5px 6px; border:1px solid #ddd; vertical-align:top; <?php echo ($exp_row && $exp_row['is_head']) ? 'font-weight:bold;' : ''; ?>">
                                <?php echo $exp_row ? number_format($exp_row['with_tax'], 3) : ''; ?>
                            </td>

                            <!-- Income Side -->
                            <td
                                style="padding:5px 6px; border:1px solid #ddd; vertical-align:top; <?php if ($inc_row) {
                                    if ($inc_row['is_head'])
                                        echo 'font-weight:bold;';
                                    if ($inc_row['is_sub'])
                                        echo 'padding-left:18px; color:#555;';
                                } ?>">
                                <?php echo $inc_row ? htmlspecialchars($inc_row['text']) : ''; ?>
                            </td>
                            <td
                                style="text-align:right; padding:5px 6px; border:1px solid #ddd; vertical-align:top; <?php echo ($inc_row && $inc_row['is_head']) ? 'font-weight:bold;' : ''; ?>">
                                <?php echo $inc_row ? number_format($inc_row['wo_tax'], 3) : ''; ?>
                            </td>
                            <td
                                style="text-align:right; padding:5px 6px; border:1px solid #ddd; vertical-align:top; color:#777;">
                                <?php echo $inc_row ? number_format($inc_row['tax'], 3) : ''; ?>
                            </td>
                            <td
                                style="text-align:right; padding:5px 6px; border:1px solid #ddd; vertical-align:top; <?php echo ($inc_row && $inc_row['is_head']) ? 'font-weight:bold;' : ''; ?>">
                                <?php echo $inc_row ? number_format($inc_row['with_tax'], 3) : ''; ?>
                            </td>
                        </tr>
                    <?php endfor; ?>

                    <!-- Total Rows -->
                    <tr style="font-weight:bold; background:#f9f9f9; font-size:11px;">
                        <td style="padding:6px; border:1px solid #999;">Total Expenses</td>
                        <td style="text-align:right; padding:6px; border:1px solid #999;">
                            <?php echo number_format($expense_total_wo_tax, 3); ?></td>
                        <td style="text-align:right; padding:6px; border:1px solid #999;">
                            <?php echo number_format($expense_total_tax, 3); ?></td>
                        <td style="text-align:right; padding:6px; border:1px solid #999;">
                            <?php echo number_format($expense_total_with_tax, 3); ?></td>

                        <td style="padding:6px; border:1px solid #999;">Total Income</td>
                        <td style="text-align:right; padding:6px; border:1px solid #999;">
                            <?php echo number_format($income_total_wo_tax, 3); ?></td>
                        <td style="text-align:right; padding:6px; border:1px solid #999;">
                            <?php echo number_format($income_total_tax, 3); ?></td>
                        <td style="text-align:right; padding:6px; border:1px solid #999;">
                            <?php echo number_format($income_total_with_tax, 3); ?></td>
                    </tr>

                    <!-- Net Profit/Loss Row -->
                    <tr
                        style="font-weight:bold; font-size:12px; background:<?php echo $is_profit_wo_tax ? '#f0fff4' : '#fff5f5'; ?>;">
                        <td colspan="4" style="padding:10px 8px; text-align:center; border:2px solid #000;">
                            NET <?php echo $is_profit_wo_tax ? 'PROFIT' : 'LOSS'; ?> (WITHOUT TAX):
                            <?php echo number_format(abs($net_profit_wo_tax), 3); ?> BHD
                        </td>
                        <td colspan="4" style="padding:10px 8px; text-align:center; border:2px solid #000;">
                            NET <?php echo $is_profit_with_tax ? 'PROFIT' : 'LOSS'; ?> (WITH TAX):
                            <?php echo number_format(abs($net_profit_with_tax), 3); ?> BHD
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

</section>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>