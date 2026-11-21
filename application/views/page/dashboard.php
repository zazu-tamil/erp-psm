<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap.min.css" />

<?php
// Fallbacks for variables to avoid PHP notices
$selected_period = isset($selected_period) ? $selected_period : 'this_year';
$period_label = isset($period_label) ? $period_label : 'Year ' . date('Y');
$period_sales = isset($period_sales) ? $period_sales : 0;
$period_purchases = isset($period_purchases) ? $period_purchases : 0;
$sales_growth_pct = isset($sales_growth_pct) ? $sales_growth_pct : null;
$prev_period_label = isset($prev_period_label) ? $prev_period_label : '';

$total_receivable = isset($total_receivable) ? $total_receivable : 0;
$total_payable = isset($total_payable) ? $total_payable : 0;
$total_cash_inward = isset($total_cash_inward) ? $total_cash_inward : 0;
$total_cash_outward = isset($total_cash_outward) ? $total_cash_outward : 0;
$cash_balance = isset($cash_balance) ? $cash_balance : ($total_cash_inward - $total_cash_outward);

$total_tenders = isset($total_tenders) ? $total_tenders : 0;
$total_enquiry = isset($total_enquiry) ? $total_enquiry : 0;
$tender_quotation_count = isset($tender_quotation_count) ? $tender_quotation_count : 0;
$vendor_enquiry_count = isset($vendor_enquiry_count) ? $vendor_enquiry_count : 0;

$tenders_closing_today_count = isset($tenders_closing_today_count) ? $tenders_closing_today_count : 0;
$tenders_closing_week_count = isset($tenders_closing_week_count) ? $tenders_closing_week_count : 0;
$tenders_closing_today = isset($tenders_closing_today) ? $tenders_closing_today : [];
$tenders_closing_week = isset($tenders_closing_week) ? $tenders_closing_week : [];

$recent_tenders = isset($recent_tenders) ? $recent_tenders : [];
$top_customers = isset($top_customers) ? $top_customers : [];
$top_vendors = isset($top_vendors) ? $top_vendors : [];
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    .dashboard-container {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background-color: #f8fafc;
        padding: 20px 18px 40px;
    }

    /* Modern Banner Styling */
    .dashboard-banner {
        background: linear-gradient(135deg, #004b8d 0%, #002d5a 100%);
        padding: 24px 30px;
        border-radius: 14px;
        margin-bottom: 24px;
        box-shadow: 0 8px 24px rgba(0, 45, 90, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        color: #ffffff;
    }

    .banner-content h2 {
        color: #ffffff;
        margin: 0 0 6px 0;
        font-weight: 700;
        font-size: 24px;
        letter-spacing: -0.5px;
    }

    .banner-content p {
        color: #cbd5e1;
        margin: 0;
        font-size: 13.5px;
    }

    .banner-controls {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .banner-date {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 7px 14px;
        border-radius: 20px;
        color: #f8fafc;
        font-size: 13px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Period Selector Pills */
    .period-selector {
        background: rgba(255, 255, 255, 0.12);
        padding: 4px;
        border-radius: 24px;
        display: inline-flex;
        gap: 4px;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .period-btn {
        padding: 5px 14px;
        border-radius: 18px;
        font-size: 12px;
        font-weight: 600;
        color: #e2e8f0;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }

    .period-btn:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.15);
    }

    .period-btn.active {
        background: #ffffff;
        color: #004b8d;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    }

    /* Section Headers */
    .dashboard-section-header {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin: 22px 0 14px 0;
        padding-left: 8px;
        border-left: 3px solid #0054a6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dashboard-section-header span.badge-scope {
        font-size: 11px;
        text-transform: none;
        font-weight: 500;
        color: #64748b;
        background: #e2e8f0;
        padding: 3px 8px;
        border-radius: 12px;
    }

    /* Grid Row & Cards */
    .kpi-grid-5 {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 22px;
    }

    .kpi-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 22px;
    }

    @media (max-width: 1200px) {
        .kpi-grid-5 { grid-template-columns: repeat(3, 1fr); }
        .kpi-grid-4 { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 768px) {
        .kpi-grid-5, .kpi-grid-4 { grid-template-columns: 1fr; }
    }

    .kpi-card-v2 {
        background: #ffffff;
        border-radius: 12px;
        padding: 18px 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .kpi-card-v2:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
    }

    .kpi-card-v2::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #cbd5e1;
    }

    .kpi-card-v2.sales::before { background: #10b981; }
    .kpi-card-v2.purchases::before { background: #ef4444; }
    .kpi-card-v2.receivables::before { background: #3b82f6; }
    .kpi-card-v2.payables::before { background: #f59e0b; }
    .kpi-card-v2.cash::before { background: #06b6d4; }
    .kpi-card-v2.alert-red::before { background: #dc2626; }
    .kpi-card-v2.alert-amber::before { background: #f59e0b; }
    .kpi-card-v2.alert-green::before { background: #10b981; }
    .kpi-card-v2.alert-indigo::before { background: #6366f1; }

    .kpi-card-v2 .kpi-content {
        flex: 1;
    }

    .kpi-card-v2 .kpi-title {
        font-size: 11.5px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .kpi-card-v2 .kpi-number {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }

    .kpi-card-v2 .kpi-subtext {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .kpi-card-v2 .kpi-subtext.trend-up {
        color: #10b981;
        font-weight: 600;
    }

    .kpi-card-v2 .kpi-subtext.trend-down {
        color: #ef4444;
        font-weight: 600;
    }

    .kpi-card-v2 .kpi-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .kpi-card-v2.sales .kpi-icon-box { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .kpi-card-v2.purchases .kpi-icon-box { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .kpi-card-v2.receivables .kpi-icon-box { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .kpi-card-v2.payables .kpi-icon-box { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .kpi-card-v2.cash .kpi-icon-box { background: rgba(6, 182, 212, 0.1); color: #06b6d4; }
    .kpi-card-v2.alert-red .kpi-icon-box { background: rgba(220, 38, 38, 0.1); color: #dc2626; }
    .kpi-card-v2.alert-amber .kpi-icon-box { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .kpi-card-v2.alert-green .kpi-icon-box { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .kpi-card-v2.alert-indigo .kpi-icon-box { background: rgba(99, 102, 241, 0.1); color: #6366f1; }

    /* Surface Card */
    .dashboard-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        margin-bottom: 22px;
        padding: 20px 22px;
    }

    .dashboard-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .dashboard-card-title {
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dashboard-card-title i {
        color: #0054a6;
    }

    /* Top Rankings Progress List */
    .ranking-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .ranking-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .ranking-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
    }

    .ranking-name {
        font-weight: 500;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
        max-width: 70%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .rank-pill {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .rank-pill.top-1 { background: #fef3c7; color: #b45309; }
    .rank-pill.top-2 { background: #e0f2fe; color: #0369a1; }
    .rank-pill.top-3 { background: #dcfce7; color: #15803d; }

    .ranking-val {
        font-weight: 600;
        color: #0f172a;
    }

    .ranking-progress-track {
        width: 100%;
        height: 6px;
        background: #f1f5f9;
        border-radius: 4px;
        overflow: hidden;
    }

    .ranking-progress-bar {
        height: 100%;
        border-radius: 4px;
        transition: width 0.6s ease;
    }

    .ranking-progress-bar.customer { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .ranking-progress-bar.vendor { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

    /* Quick Action Hub */
    .action-group-title {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: 0.5px;
        margin: 12px 0 8px 0;
    }

    .action-group-title:first-child {
        margin-top: 0;
    }

    .action-pills-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .action-pill-btn {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        color: #334155;
        font-size: 12.5px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }

    .action-pill-btn:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #0054a6;
        transform: translateY(-1px);
    }

    .action-pill-btn i {
        font-size: 13px;
    }

    /* Status Badges */
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    .status-open { background: #dbeafe; color: #1d4ed8; }
    .status-quoted { background: #ede9fe; color: #6d28d9; }
    .status-won { background: #dcfce7; color: #15803d; }
    .status-lost { background: #fee2e2; color: #b91c1c; }
    .status-hold { background: #fef3c7; color: #b45309; }
    .status-default { background: #f1f5f9; color: #475569; }

    /* Custom Table Styling */
    .custom-table {
        width: 100% !important;
        margin-bottom: 0;
    }

    .custom-table th {
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #64748b;
        background-color: #f8fafc;
        border-bottom: 2px solid #edf2f7 !important;
        padding: 9px 12px !important;
        white-space: nowrap;
    }

    .custom-table td {
        padding: 10px 12px !important;
        font-size: 12.5px;
        color: #334155;
        vertical-align: middle !important;
        border-bottom: 1px solid #f1f5f9;
    }
</style>

<div class="content dashboard-container">
    <!-- Top Executive Banner -->
    <div class="dashboard-banner">
        <div class="banner-content">
            <h2>Welcome back, <?php echo strtoupper($this->session->userdata(SESS_HD . 'staff_name')); ?></h2>
            <p>Executive Overview & Real-Time Performance Dashboard</p>
        </div>
        <div class="banner-controls">
            <!-- Period Selector -->
            <div class="period-selector">
                <a href="<?php echo base_url('dash?period=this_month'); ?>" 
                   class="period-btn <?php echo ($selected_period == 'this_month') ? 'active' : ''; ?>">
                   This Month
                </a>
                <a href="<?php echo base_url('dash?period=this_quarter'); ?>" 
                   class="period-btn <?php echo ($selected_period == 'this_quarter') ? 'active' : ''; ?>">
                   This Quarter
                </a>
                <a href="<?php echo base_url('dash?period=this_year'); ?>" 
                   class="period-btn <?php echo ($selected_period == 'this_year') ? 'active' : ''; ?>">
                   This Year
                </a>
                <a href="<?php echo base_url('dash?period=all_time'); ?>" 
                   class="period-btn <?php echo ($selected_period == 'all_time') ? 'active' : ''; ?>">
                   All Time
                </a>
            </div>
            <div class="banner-date">
                <i class="fa fa-calendar"></i> <?php echo date('d M Y'); ?>
            </div>
        </div>
    </div>

    <!-- ROW 1: Commercial & Financial Position -->
    <div class="dashboard-section-header">
        <span>Commercial & Financial Position</span>
        <span class="badge-scope"><i class="fa fa-filter"></i> Period: <?php echo htmlspecialchars($period_label); ?></span>
    </div>

    <div class="kpi-grid-5">
        <!-- 1. Sales Revenue -->
        <div class="kpi-card-v2 sales">
            <div class="kpi-content">
                <div class="kpi-title">Sales Revenue</div>
                <div class="kpi-number">BD <?php echo number_format($period_sales, 3); ?></div>
                <div class="kpi-subtext <?php echo ($sales_growth_pct !== null && $sales_growth_pct >= 0) ? 'trend-up' : (($sales_growth_pct !== null) ? 'trend-down' : ''); ?>">
                    <?php if ($sales_growth_pct !== null): ?>
                        <i class="fa fa-<?php echo ($sales_growth_pct >= 0) ? 'level-up' : 'level-down'; ?>"></i>
                        <?php echo ($sales_growth_pct >= 0 ? '+' : '') . $sales_growth_pct . '% vs ' . $prev_period_label; ?>
                    <?php else: ?>
                        <i class="fa fa-file-text-o"></i> Invoiced Sales
                    <?php endif; ?>
                </div>
            </div>
            <div class="kpi-icon-box">
                <i class="fa fa-line-chart"></i>
            </div>
        </div>

        <!-- 2. Supplier Purchases -->
        <div class="kpi-card-v2 purchases">
            <div class="kpi-content">
                <div class="kpi-title">Purchases & Bills</div>
                <div class="kpi-number">BD <?php echo number_format($period_purchases, 3); ?></div>
                <div class="kpi-subtext">
                    <i class="fa fa-shopping-cart"></i> Cost of goods & bills
                </div>
            </div>
            <div class="kpi-icon-box">
                <i class="fa fa-truck"></i>
            </div>
        </div>

        <!-- 3. Accounts Receivable (Customer Due) -->
        <div class="kpi-card-v2 receivables">
            <div class="kpi-content">
                <div class="kpi-title">Customer Receivables</div>
                <div class="kpi-number" style="color: <?php echo ($total_receivable >= 0) ? '#0f172a' : '#10b981'; ?>;">
                    BD <?php echo number_format(abs($total_receivable), 3); ?>
                </div>
                <div class="kpi-subtext">
                    <i class="fa fa-info-circle"></i> 
                    <?php echo ($total_receivable >= 0) ? 'Outstanding Due from Clients' : 'Net Advance Paid by Clients'; ?>
                </div>
            </div>
            <div class="kpi-icon-box">
                <i class="fa fa-users"></i>
            </div>
        </div>

        <!-- 4. Accounts Payable (Vendor Due) -->
        <div class="kpi-card-v2 payables">
            <div class="kpi-content">
                <div class="kpi-title">Supplier Payables</div>
                <div class="kpi-number" style="color: <?php echo ($total_payable >= 0) ? '#0f172a' : '#10b981'; ?>;">
                    BD <?php echo number_format(abs($total_payable), 3); ?>
                </div>
                <div class="kpi-subtext">
                    <i class="fa fa-clock-o"></i> 
                    <?php echo ($total_payable >= 0) ? 'Outstanding Due to Vendors' : 'Net Advance to Vendors'; ?>
                </div>
            </div>
            <div class="kpi-icon-box">
                <i class="fa fa-credit-card"></i>
            </div>
        </div>

        <!-- 5. Net Cash Balance -->
        <div class="kpi-card-v2 cash">
            <div class="kpi-content">
                <div class="kpi-title">Net Cash Balance</div>
                <div class="kpi-number" style="color: <?php echo ($cash_balance >= 0) ? '#0f172a' : '#ef4444'; ?>;">
                    BD <?php echo number_format($cash_balance, 3); ?>
                </div>
                <div class="kpi-subtext">
                    <i class="fa fa-university"></i> Ledger cash in hand
                </div>
            </div>
            <div class="kpi-icon-box">
                <i class="fa fa-bank"></i>
            </div>
        </div>
    </div>

    <!-- ROW 2: Action Center & Today's Activity -->
    <div class="dashboard-section-header">
        <span>Action Center & Daily Pipeline</span>
        <span class="badge-scope"><i class="fa fa-clock-o"></i> Today: <?php echo date('d M Y'); ?></span>
    </div>

    <div class="kpi-grid-4">
        <!-- 1. Tenders Closing Soon -->
        <a href="#" data-toggle="modal" data-target="#closingTendersModal" style="text-decoration: none;">
            <div class="kpi-card-v2 alert-red">
                <div class="kpi-content">
                    <div class="kpi-title">Closing Soon (7 Days)</div>
                    <div class="kpi-number" style="color: #dc2626;">
                        <?php echo $tenders_closing_week_count; ?>
                    </div>
                    <div class="kpi-subtext" style="color: #dc2626; font-weight: 600;">
                        <i class="fa fa-exclamation-circle"></i> 
                        <?php echo $tenders_closing_today_count; ?> Closing Today • Click to view
                    </div>
                </div>
                <div class="kpi-icon-box">
                    <i class="fa fa-calendar-times-o"></i>
                </div>
            </div>
        </a>

        <!-- 2. Enquiries Added Today -->
        <a href="#" data-toggle="modal" data-target="#enquiriesTodayModal" style="text-decoration: none;">
            <div class="kpi-card-v2 alert-amber">
                <div class="kpi-content">
                    <div class="kpi-title">Enquiries Received Today</div>
                    <div class="kpi-number">
                        <?php echo $total_enquiry; ?>
                    </div>
                    <div class="kpi-subtext" style="color: #b45309;">
                        <i class="fa fa-inbox"></i> Click to view daily enquiries
                    </div>
                </div>
                <div class="kpi-icon-box">
                    <i class="fa fa-envelope-open-o"></i>
                </div>
            </div>
        </a>

        <!-- 3. Quotations Generated Today -->
        <a href="#" data-toggle="modal" data-target="#quotationsTodayModal" style="text-decoration: none;">
            <div class="kpi-card-v2 alert-green">
                <div class="kpi-content">
                    <div class="kpi-title">Quotations Quoted Today</div>
                    <div class="kpi-number">
                        <?php echo $tender_quotation_count; ?>
                    </div>
                    <div class="kpi-subtext" style="color: #15803d;">
                        <i class="fa fa-check-circle-o"></i> Click to view quotations
                    </div>
                </div>
                <div class="kpi-icon-box">
                    <i class="fa fa-file-text-o"></i>
                </div>
            </div>
        </a>

        <!-- 4. Vendor Rate Inquiries Today -->
        <a href="#" data-toggle="modal" data-target="#vendorInquiriesTodayModal" style="text-decoration: none;">
            <div class="kpi-card-v2 alert-indigo">
                <div class="kpi-content">
                    <div class="kpi-title">Vendor Rate Enquiries Today</div>
                    <div class="kpi-number">
                        <?php echo $vendor_enquiry_count; ?>
                    </div>
                    <div class="kpi-subtext" style="color: #4338ca;">
                        <i class="fa fa-question-circle-o"></i> Click to view vendor inquiries
                    </div>
                </div>
                <div class="kpi-icon-box">
                    <i class="fa fa-handshake-o"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- ROW 3: Visual Analytics & Pipeline Charts -->
    <div class="row">
        <!-- Monthly Sales vs Purchases (8 columns) -->
        <div class="col-md-8">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3 class="dashboard-card-title">
                        <i class="fa fa-line-chart"></i> Monthly Sales & Purchases Trend (12 Months)
                    </h3>
                </div>
                <div style="height: 310px; position: relative;">
                    <canvas id="salesPurchasesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tender Status Pipeline Distribution (4 columns) -->
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3 class="dashboard-card-title">
                        <i class="fa fa-pie-chart"></i> Tender Pipeline Status
                    </h3>
                </div>
                <div style="height: 310px; position: relative;">
                    <canvas id="tenderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 4: Executive Rankings (Top 5 Customers & Suppliers) -->
    <div class="row">
        <!-- Top 5 Customers -->
        <div class="col-md-6">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3 class="dashboard-card-title">
                        <i class="fa fa-trophy" style="color: #f59e0b;"></i> Top Customers by Revenue
                    </h3>
                    <span class="badge-scope"><i class="fa fa-building-o"></i> Top 5 Clients</span>
                </div>
                <div class="ranking-list">
                    <?php if (!empty($top_customers)): ?>
                        <?php 
                        $max_cust_val = max(array_column($top_customers, 'total_val'));
                        $max_cust_val = ($max_cust_val > 0) ? $max_cust_val : 1;
                        ?>
                        <?php foreach ($top_customers as $idx => $cust): ?>
                            <?php 
                            $pct = round(($cust['total_val'] / $max_cust_val) * 100);
                            $rankClass = ($idx == 0) ? 'top-1' : (($idx == 1) ? 'top-2' : (($idx == 2) ? 'top-3' : ''));
                            ?>
                            <div class="ranking-item">
                                <div class="ranking-header">
                                    <div class="ranking-name" title="<?php echo htmlspecialchars($cust['customer_name']); ?>">
                                        <span class="rank-pill <?php echo $rankClass; ?>"><?php echo $idx + 1; ?></span>
                                        <span><?php echo htmlspecialchars($cust['customer_name']); ?></span>
                                        <small class="text-muted" style="font-size: 11px;">(<?php echo $cust['inv_count']; ?> inv)</small>
                                    </div>
                                    <div class="ranking-val">BD <?php echo number_format($cust['total_val'], 3); ?></div>
                                </div>
                                <div class="ranking-progress-track">
                                    <div class="ranking-progress-bar customer" style="width: <?php echo $pct; ?>%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted" style="padding: 30px 0;">No sales invoice data available.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Top 5 Vendors -->
        <div class="col-md-6">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3 class="dashboard-card-title">
                        <i class="fa fa-shopping-bag" style="color: #06b6d4;"></i> Top Suppliers by Purchase Volume
                    </h3>
                    <span class="badge-scope"><i class="fa fa-truck"></i> Top 5 Vendors</span>
                </div>
                <div class="ranking-list">
                    <?php if (!empty($top_vendors)): ?>
                        <?php 
                        $max_vend_val = max(array_column($top_vendors, 'total_val'));
                        $max_vend_val = ($max_vend_val > 0) ? $max_vend_val : 1;
                        ?>
                        <?php foreach ($top_vendors as $idx => $vend): ?>
                            <?php 
                            $pct = round(($vend['total_val'] / $max_vend_val) * 100);
                            $rankClass = ($idx == 0) ? 'top-1' : (($idx == 1) ? 'top-2' : (($idx == 2) ? 'top-3' : ''));
                            ?>
                            <div class="ranking-item">
                                <div class="ranking-header">
                                    <div class="ranking-name" title="<?php echo htmlspecialchars($vend['vendor_name']); ?>">
                                        <span class="rank-pill <?php echo $rankClass; ?>"><?php echo $idx + 1; ?></span>
                                        <span><?php echo htmlspecialchars($vend['vendor_name']); ?></span>
                                        <small class="text-muted" style="font-size: 11px;">(<?php echo $vend['bill_count']; ?> bills)</small>
                                    </div>
                                    <div class="ranking-val">BD <?php echo number_format($vend['total_val'], 3); ?></div>
                                </div>
                                <div class="ranking-progress-track">
                                    <div class="ranking-progress-bar vendor" style="width: <?php echo $pct; ?>%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted" style="padding: 30px 0;">No vendor purchase data available.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 5: Recent Tenders (Fast-Loading Top 15) & Quick Action Hub -->
    <div class="row">
        <!-- Recent Tenders (8 Columns) -->
        <div class="col-md-8">
            <div class="dashboard-card" style="min-height: 420px;">
                <div class="dashboard-card-header">
                    <h3 class="dashboard-card-title">
                        <i class="fa fa-gavel"></i> Recent Active Tenders
                    </h3>
                    <a href="<?php echo base_url('tender-enquiry-list'); ?>" class="btn btn-xs btn-primary" style="border-radius: 14px; padding: 4px 12px; font-weight: 600;">
                        <i class="fa fa-list"></i> View All (<?php echo $total_tenders; ?>)
                    </a>
                </div>
                <div class="table-responsive">
                    <table id="recentTendersTable" class="table custom-table">
                        <thead>
                            <tr>
                                <th>Enquiry No</th>
                                <th>Date</th>
                                <th>Tender Name / Subject</th>
                                <th>Customer Name</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_tenders)): ?>
                                <?php foreach ($recent_tenders as $tender): ?>
                                    <?php
                                    $status = !empty($tender['tender_status']) ? $tender['tender_status'] : 'Open';
                                    $statusClass = 'status-default';
                                    switch (strtolower($status)) {
                                        case 'open': $statusClass = 'status-open'; break;
                                        case 'quoted': $statusClass = 'status-quoted'; break;
                                        case 'won': $statusClass = 'status-won'; break;
                                        case 'lost': $statusClass = 'status-lost'; break;
                                        case 'on hold': $statusClass = 'status-hold'; break;
                                    }
                                    $url = base_url('tender-enquiry-summary-report/' . $tender['tender_enquiry_id']);
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo $url; ?>" style="font-weight: 600; color: #0054a6;">
                                                <?php echo htmlspecialchars($tender['enquiry_no']); ?>
                                            </a>
                                        </td>
                                        <td style="white-space:nowrap; color: #64748b;">
                                            <?php echo !empty($tender['enquiry_date']) ? date('d-m-Y', strtotime($tender['enquiry_date'])) : '-'; ?>
                                        </td>
                                        <td>
                                            <span style="font-weight: 500; color: #1e293b; max-width: 200px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($tender['tender_name']); ?>">
                                                <?php echo htmlspecialchars($tender['tender_name'] ?: '-'); ?>
                                            </span>
                                        </td>
                                        <td style="color: #334155;">
                                            <?php echo htmlspecialchars($tender['customer_name']); ?>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?php echo $url; ?>" class="btn btn-default btn-xs" title="View Summary" style="border-radius: 4px;">
                                                <i class="fa fa-eye text-primary"></i>
                                            </a>
                                            <a href="<?php echo base_url('tender-enquiry-edit/' . $tender['tender_enquiry_id']); ?>" class="btn btn-default btn-xs" title="Edit Tender" style="border-radius: 4px;">
                                                <i class="fa fa-pencil text-warning"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted" style="padding: 30px;">
                                        No recent active tenders found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Action Hub (4 Columns) -->
        <div class="col-md-4">
            <div class="dashboard-card" style="min-height: 420px;">
                <div class="dashboard-card-header">
                    <h3 class="dashboard-card-title">
                        <i class="fa fa-bolt" style="color: #f59e0b;"></i> Quick Actions Hub
                    </h3>
                </div>

                <!-- Sales Section -->
                <div class="action-group-title"><i class="fa fa-bullhorn text-primary"></i> Sales & Tenders</div>
                <div class="action-pills-wrap">
                    <a href="<?php echo base_url('add-tender-enquiry'); ?>" class="action-pill-btn">
                        <i class="fa fa-plus-circle text-primary"></i> New Enquiry
                    </a>
                    <a href="<?php echo base_url('tender-quotation-add'); ?>" class="action-pill-btn">
                        <i class="fa fa-file-text-o text-success"></i> New Quote
                    </a>
                    <a href="<?php echo base_url('tender-invoice-add'); ?>" class="action-pill-btn">
                        <i class="fa fa-file-pdf-o text-info"></i> Sales Invoice
                    </a>
                    <a href="<?php echo base_url('tender-dc-add'); ?>" class="action-pill-btn">
                        <i class="fa fa-truck text-warning"></i> Delivery Challan
                    </a>
                </div>

                <!-- Procurement Section -->
                <div class="action-group-title"><i class="fa fa-shopping-cart text-danger"></i> Procurement & Vendors</div>
                <div class="action-pills-wrap">
                    <a href="<?php echo base_url('vendor-rate-enquiry-add'); ?>" class="action-pill-btn">
                        <i class="fa fa-question-circle text-purple" style="color:#8b5cf6;"></i> Rate Enquiry
                    </a>
                    <a href="<?php echo base_url('vendor-po-add'); ?>" class="action-pill-btn">
                        <i class="fa fa-shopping-bag text-danger"></i> Vendor PO
                    </a>
                    <a href="<?php echo base_url('vendor-pur-inward-add'); ?>" class="action-pill-btn">
                        <i class="fa fa-arrow-down text-info"></i> Inward Bill
                    </a>
                </div>

                <!-- Accounts Section -->
                <div class="action-group-title"><i class="fa fa-university text-success"></i> Accounts & Ledger</div>
                <div class="action-pills-wrap">
                    <a href="<?php echo base_url('account-trial-balance'); ?>" class="action-pill-btn">
                        <i class="fa fa-balance-scale text-primary"></i> Trial Balance
                    </a>
                    <a href="<?php echo base_url('inward-list'); ?>" class="action-pill-btn">
                        <i class="fa fa-sign-in text-success"></i> Cash Inward
                    </a>
                    <a href="<?php echo base_url('outward-list'); ?>" class="action-pill-btn">
                        <i class="fa fa-sign-out text-danger"></i> Cash Outward
                    </a>
                    <a href="<?php echo base_url('petty-cash'); ?>" class="action-pill-btn">
                        <i class="fa fa-money text-warning"></i> Petty Cash
                    </a>
                    <a href="<?php echo base_url('customer-statement-report'); ?>" class="action-pill-btn">
                        <i class="fa fa-file-text text-secondary"></i> Client Statement
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals Section -->

    <!-- 1. Closing Tenders Modal (Today & Upcoming 7 Days) -->
    <div class="modal fade" id="closingTendersModal" tabindex="-1" role="dialog" aria-labelledby="closingTendersModalLabel">
        <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1200px;">
            <div class="modal-content">
                <div class="modal-header" style="background: #004b8d; color: #fff; border-radius: 6px 6px 0 0;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="closingTendersModalLabel">
                        <i class="fa fa-calendar-times-o"></i> Tenders Closing Soon (Next 7 Days)
                    </h4>
                </div>
                <div class="modal-body table-responsive">
                    <table id="closingTendersTable" class="table table-bordered table-striped custom-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>Sr.No</th>
                                <th>Enquiry No</th>
                                <th>Enquiry Date</th>
                                <th>Closing Date</th>
                                <th>Urgency</th>
                                <th>Customer Name</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tenders_closing_week)): ?>
                                <?php foreach ($tenders_closing_week as $index => $tender): ?>
                                    <?php
                                    $url = base_url('tender-enquiry-summary-report/' . $tender['tender_enquiry_id']);
                                    $days_left = isset($tender['days_left']) ? (int)$tender['days_left'] : 0;
                                    $badge = ($days_left <= 0) ? '<span class="label label-danger">Closing Today</span>' : (($days_left == 1) ? '<span class="label label-warning">Tomorrow</span>' : '<span class="label label-info">In ' . $days_left . ' Days</span>');
                                    ?>
                                    <tr style="cursor:pointer;" onclick="window.location='<?php echo $url; ?>';">
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <a href="<?php echo $url; ?>" onclick="event.stopPropagation();" style="font-weight:600; color:#0054a6;">
                                                <?php echo htmlspecialchars($tender['enquiry_no']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo !empty($tender['enquiry_date']) ? date('d-m-Y', strtotime($tender['enquiry_date'])) : '-'; ?></td>
                                        <td><strong><?php echo !empty($tender['closing_date']) ? date('d-m-Y', strtotime($tender['closing_date'])) : '-'; ?></strong></td>
                                        <td><?php echo $badge; ?></td>
                                        <td><?php echo htmlspecialchars($tender['customer_name'] ?: '-'); ?></td>
                                        <td class="text-center">
                                            <a href="<?php echo $url; ?>" onclick="event.stopPropagation();" target="_blank" class="btn btn-primary btn-xs">
                                                <i class="fa fa-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Enquiries Added Today Modal -->
    <div class="modal fade" id="enquiriesTodayModal" tabindex="-1" role="dialog" aria-labelledby="enquiriesTodayModalLabel">
        <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1200px;">
            <div class="modal-content">
                <div class="modal-header" style="background: #f59e0b; color: #fff; border-radius: 6px 6px 0 0;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="enquiriesTodayModalLabel">
                        <i class="fa fa-envelope-open-o"></i> Enquiries Received Today
                    </h4>
                </div>
                <div class="modal-body table-responsive">
                    <table id="enquiriesTodayTable" class="table table-bordered table-striped custom-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>Sr.No</th>
                                <th>Enquiry No</th>
                                <th>Enquiry Date</th>
                                <th>Customer Name</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($enquiries_added_today_list)): ?>
                                <?php foreach ($enquiries_added_today_list as $index => $enq): ?>
                                    <?php 
                                    $url = base_url('tender-enquiry-summary-report/' . $enq['tender_enquiry_id']); 
                                    $status = !empty($enq['tender_status']) ? $enq['tender_status'] : 'Open';
                                    ?>
                                    <tr style="cursor:pointer;" onclick="window.location='<?php echo $url; ?>';">
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <a href="<?php echo $url; ?>" onclick="event.stopPropagation();" style="font-weight:600; color:#b45309;">
                                                <?php echo htmlspecialchars($enq['enquiry_no']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo !empty($enq['enquiry_date']) ? date('d-m-Y', strtotime($enq['enquiry_date'])) : '-'; ?></td>
                                        <td><?php echo htmlspecialchars($enq['customer_name'] ?: '-'); ?></td>
                                        <td><span class="status-badge status-open"><?php echo htmlspecialchars($status); ?></span></td>
                                        <td class="text-center">
                                            <a href="<?php echo $url; ?>" onclick="event.stopPropagation();" target="_blank" class="btn btn-warning btn-xs">
                                                <i class="fa fa-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Quotations Generated Today Modal -->
    <div class="modal fade" id="quotationsTodayModal" tabindex="-1" role="dialog" aria-labelledby="quotationsTodayModalLabel">
        <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1200px;">
            <div class="modal-content">
                <div class="modal-header" style="background: #10b981; color: #fff; border-radius: 6px 6px 0 0;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="quotationsTodayModalLabel">
                        <i class="fa fa-file-text-o"></i> Quotations Generated Today
                    </h4>
                </div>
                <div class="modal-body table-responsive">
                    <table id="quotationsTodayTable" class="table table-bordered table-striped custom-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>Sr.No</th>
                                <th>Quotation No</th>
                                <th>Quote Date</th>
                                <th>Tender Enquiry No</th>
                                <th>Customer Name</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($quotations_generated_today_list)): ?>
                                <?php foreach ($quotations_generated_today_list as $index => $q): ?>
                                    <?php $url = base_url('tender-quotation-edit/' . $q['tender_quotation_id']); ?>
                                    <tr style="cursor:pointer;" onclick="window.location='<?php echo $url; ?>';">
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <a href="<?php echo $url; ?>" onclick="event.stopPropagation();" style="font-weight:600; color:#10b981;">
                                                <?php echo htmlspecialchars($q['quotation_no']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo !empty($q['quote_date']) ? date('d-m-Y', strtotime($q['quote_date'])) : '-'; ?></td>
                                        <td><?php echo htmlspecialchars($q['enquiry_no']); ?></td>
                                        <td><?php echo htmlspecialchars($q['customer_name'] ?: '-'); ?></td>
                                        <td class="text-center">
                                            <a href="<?php echo $url; ?>" onclick="event.stopPropagation();" target="_blank" class="btn btn-success btn-xs">
                                                <i class="fa fa-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Vendor Rate Inquiries Today Modal -->
    <div class="modal fade" id="vendorInquiriesTodayModal" tabindex="-1" role="dialog" aria-labelledby="vendorInquiriesTodayModalLabel">
        <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1200px;">
            <div class="modal-content">
                <div class="modal-header" style="background: #6366f1; color: #fff; border-radius: 6px 6px 0 0;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="vendorInquiriesTodayModalLabel">
                        <i class="fa fa-handshake-o"></i> Vendor Rate Enquiries Today
                    </h4>
                </div>
                <div class="modal-body table-responsive">
                    <table id="vendorInquiriesTodayTable" class="table table-bordered table-striped custom-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>Sr.No</th>
                                <th>Vendor Enquiry No</th>
                                <th>Enquiry Date</th>
                                <th>Tender Enquiry No</th>
                                <th>Customer Name</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($vendor_inquiries_today_list)): ?>
                                <?php foreach ($vendor_inquiries_today_list as $index => $v): ?>
                                    <?php $url = base_url('vendor-rate-enquiry-edit/' . $v['vendor_rate_enquiry_id']); ?>
                                    <tr style="cursor:pointer;" onclick="window.location='<?php echo $url; ?>';">
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <a href="<?php echo $url; ?>" onclick="event.stopPropagation();" style="font-weight:600; color:#6366f1;">
                                                <?php echo htmlspecialchars($v['vendor_enquiry_no']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo !empty($v['enquiry_date']) ? date('d-m-Y', strtotime($v['enquiry_date'])) : '-'; ?></td>
                                        <td><?php echo htmlspecialchars($v['enquiry_no']); ?></td>
                                        <td><?php echo htmlspecialchars($v['customer_name'] ?: '-'); ?></td>
                                        <td class="text-center">
                                            <a href="<?php echo $url; ?>" onclick="event.stopPropagation();" target="_blank" class="btn btn-primary btn-xs">
                                                <i class="fa fa-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
