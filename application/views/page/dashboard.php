<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap.min.css"/>

<?php
// Fallbacks for variables to avoid PHP notices
$total_sales = isset($total_sales) ? $total_sales : 0;
$total_purchases = isset($total_purchases) ? $total_purchases : 0;
$total_tenders = isset($total_tenders) ? $total_tenders : 0;
$customer_count = isset($customer_count) ? $customer_count : 0;
$vendor_count = isset($vendor_count) ? $vendor_count : 0;
$brand_count = isset($brand_count) ? $brand_count : 0;
$total_category = isset($total_category) ? $total_category : 0;
$total_items = isset($total_items) ? $total_items : 0;
$total_user = isset($total_user) ? $total_user : 0;
$total_enquiry = isset($total_enquiry) ? $total_enquiry : 0;
$tender_quotation_count = isset($tender_quotation_count) ? $tender_quotation_count : 0;
$vendor_enquiry_count = isset($vendor_enquiry_count) ? $vendor_enquiry_count : 0;
$recent_tenders = isset($recent_tenders) ? $recent_tenders : [];

$total_cash_inward = isset($total_cash_inward) ? $total_cash_inward : 0;
$total_cash_outward = isset($total_cash_outward) ? $total_cash_outward : 0;
$recent_cash = isset($recent_cash) ? $recent_cash : [];
$cash_balance = $total_cash_inward - $total_cash_outward;
?>

<style>
    /* Theme Font & Settings override */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    .dashboard-container {
        font-family: 'Inter', 'Source Sans Pro', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background-color: #f8fafc;
        padding: 20px 15px;
    }

    /* Modern Banner Styling */
    .dashboard-banner {
        background: #004b8d;
        padding: 30px 40px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 75, 141, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .banner-content h2 {
        color: #ffffff;
        margin: 0 0 8px 0;
        font-weight: 700;
        font-size: 28px;
        letter-spacing: -0.5px;
    }

    .banner-content p {
        color: #94a3b8;
        margin: 0;
        font-size: 15px;
        font-weight: 400;
    }

    .banner-date {
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 10px 18px;
        border-radius: 30px;
        color: #f1f5f9;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Section Headers */
    .dashboard-section-header {
        font-size: 14px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 25px 0 15px 0;
        padding-left: 5px;
        border-left: 4px solid #0054a6;
        line-height: 1;
    }

    /* Grid Layouts */
    .kpi-row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -12px;
        margin-left: -12px;
        margin-bottom: 5px;
    }

    .kpi-col {
        padding-left: 12px;
        padding-right: 12px;
        margin-bottom: 24px;
    }

    /* Premium KPI Cards Styling */
    .kpi-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
        border: 1px solid rgba(226, 232, 240, 0.8);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 100%;
    }

    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.06);
        border-color: rgba(0, 84, 166, 0.2);
    }

    .kpi-info {
        flex: 1;
    }

    .kpi-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .kpi-value {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }

    .kpi-change {
        font-size: 11px;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
        font-weight: 500;
    }

    .kpi-change.up { color: #10b981; }
    .kpi-change.down { color: #ef4444; }
    .kpi-change.neutral { color: #64748b; }

    .kpi-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: all 0.3s ease;
    }

    /* KPI Colors and Gradient Details */
    .kpi-card.sales .kpi-icon-wrapper { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
    .kpi-card.purchases .kpi-icon-wrapper { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .kpi-card.inward .kpi-icon-wrapper { background-color: rgba(6, 182, 212, 0.1); color: #06b6d4; }
    .kpi-card.outward .kpi-icon-wrapper { background-color: rgba(249, 115, 22, 0.1); color: #f97316; }
    .kpi-card.balance .kpi-icon-wrapper { background-color: rgba(247, 147, 30, 0.1); color: #f7931e; }
    
    .kpi-card.tenders .kpi-icon-wrapper { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .kpi-card.customers .kpi-icon-wrapper { background-color: rgba(168, 85, 247, 0.1); color: #a855f7; }
    .kpi-card.vendors .kpi-icon-wrapper { background-color: rgba(236, 72, 153, 0.1); color: #ec4899; }
    .kpi-card.items .kpi-icon-wrapper { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .kpi-card.categories .kpi-icon-wrapper { background-color: rgba(100, 116, 139, 0.1); color: #64748b; }

    /* Operational Stats Mini Grid */
    .ops-row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -8px;
        margin-left: -8px;
        margin-bottom: 25px;
    }

    .ops-col {
        padding-left: 8px;
        padding-right: 8px;
        margin-bottom: 16px;
    }

    .ops-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 20px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.2s ease;
    }

    .ops-card:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .ops-icon {
        font-size: 20px;
        color: #475569;
    }

    .ops-value {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 2px;
    }

    .ops-label {
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
    }

    /* Analytics Charts Cards */
    .analytics-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
        margin-bottom: 30px;
        padding: 24px;
    }

    .analytics-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f1f5f9;
    }

    .analytics-title {
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .analytics-title i {
        color: #0054a6;
    }

    .chart-container {
        position: relative;
        width: 100%;
    }

    /* Bottom Section Lists */
    .section-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
        margin-bottom: 25px;
        padding: 24px;
        min-height: 480px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Recent Tenders Table Styles */
    .table-responsive {
        border: none;
    }

    .custom-table {
        width: 100%;
        margin-bottom: 0;
    }

    .custom-table th {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #64748b;
        background-color: #f8fafc;
        border-bottom: 2px solid #edf2f7 !important;
        padding: 12px 16px !important;
    }

    .custom-table td {
        padding: 14px 16px !important;
        font-size: 13px;
        color: #334155;
        vertical-align: middle !important;
        border-bottom: 1px solid #f1f5f9;
    }

    .custom-table tbody tr:hover td {
        background-color: #f8fafc;
    }

    /* Status Badges */
    .status-badge {
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        display: inline-block;
        letter-spacing: 0.5px;
    }
    
    .status-open { background-color: rgba(59, 130, 246, 0.1); color: #2563eb; }
    .status-quoted { background-color: rgba(139, 92, 246, 0.1); color: #7c3aed; }
    .status-won { background-color: rgba(16, 185, 129, 0.1); color: #059669; }
    .status-lost { background-color: rgba(239, 68, 68, 0.1); color: #dc2626; }
    .status-hold { background-color: rgba(245, 158, 11, 0.1); color: #d97706; }
    .status-default { background-color: #f1f5f9; color: #64748b; }

    /* Quick Actions Layout */
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .action-btn {
        background-color: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 12px;
        padding: 20px 15px;
        text-align: center;
        color: #334155;
        text-decoration: none !important;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .action-btn:hover {
        background-color: #0054a6;
        color: #ffffff;
        border-color: #0054a6;
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 84, 166, 0.2);
    }

    .action-btn i {
        font-size: 22px;
        margin-bottom: 2px;
    }

    .action-btn span {
        font-size: 13px;
        font-weight: 600;
    }

    /* Custom DataTables Styling to match premium theme */
    .dataTables_wrapper {
        padding: 0;
        margin-top: 10px;
    }
    
    .dataTables_length, .dataTables_filter {
        margin-bottom: 20px;
        font-size: 13px;
        color: #475569;
    }

    .dataTables_length select {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 4px 8px;
        outline: none;
        color: #334155;
    }

    .dataTables_filter input {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 6px 12px;
        outline: none;
        transition: all 0.2s ease;
        margin-left: 8px;
        font-weight: 400;
        color: #334155;
    }

    .dataTables_filter input:focus {
        border-color: #0054a6;
        box-shadow: 0 0 0 3px rgba(0, 84, 166, 0.15);
    }

    .dataTables_info {
        font-size: 13px;
        color: #64748b;
        padding-top: 15px;
    }

    .dataTables_paginate {
        padding-top: 15px;
        text-align: right;
    }

    .dataTables_paginate .paginate_button {
        padding: 6px 12px !important;
        margin-left: 4px !important;
        border-radius: 8px !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        color: #475569 !important;
        font-weight: 500 !important;
        font-size: 12px !important;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-block;
        text-decoration: none !important;
    }

    .dataTables_paginate .paginate_button:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
    }

    .dataTables_paginate .paginate_button.current, 
    .dataTables_paginate .paginate_button.current:hover {
        background: #0054a6 !important;
        color: #ffffff !important;
        border-color: #0054a6 !important;
        font-weight: 600 !important;
    }

    .dataTables_paginate .paginate_button.disabled,
    .dataTables_paginate .paginate_button.disabled:hover,
    .dataTables_paginate .paginate_button.disabled:active {
        background: #f8fafc !important;
        color: #cbd5e1 !important;
        border-color: #e2e8f0 !important;
        cursor: not-allowed;
    }

    /* Responsive Media Queries */
    @media (max-width: 1200px) {
        .kpi-col { width: 33.333% !important; }
    }

    @media (max-width: 768px) {
        .dashboard-banner {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
            padding: 20px 24px;
        }

        .banner-content h2 { font-size: 22px; }

        .kpi-col { width: 50% !important; }

        .ops-col { width: 50% !important; }

        .analytics-card, .section-card {
            padding: 16px;
        }
    }

    @media (max-width: 480px) {
        .kpi-col { width: 100% !important; }

        .ops-col { width: 100% !important; }

        .quick-actions-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="content dashboard-container">
    <!-- Modern Banner Header -->
    <div class="dashboard-banner">
        <div class="banner-content">
            <h2>Welcome back, <?php echo strtoupper($this->session->userdata(SESS_HD . 'staff_name')); ?></h2>
            <p>Here is the business overview and activity log for today.</p>
        </div>
        <div class="banner-right">
            <div class="banner-date">
                <i class="fa fa-calendar"></i> <?php echo date('d M Y'); ?>
            </div>
        </div>
    </div>

    <!-- Commercial / Financial KPIs Row -->
    <div class="dashboard-section-header">Financial Ledger & Invoices</div>
    <div class="kpi-row">
        <!-- Sales Card -->
        <div class="col-md-3 kpi-col" style="width: 20%;">
            <div class="kpi-card sales">
                <div class="kpi-info">
                    <div class="kpi-label">Sales Revenue</div>
                    <div class="kpi-value">BD <?php echo number_format($total_sales, 3); ?></div>
                    <div class="kpi-change up">
                        <i class="fa fa-line-chart"></i> Invoiced sales
                    </div>
                </div>
                <div class="kpi-icon-wrapper">
                    <i class="fa fa-money"></i>
                </div>
            </div>
        </div>

        <!-- Purchases Card -->
        <div class="col-md-3 kpi-col" style="width: 20%;">
            <div class="kpi-card purchases">
                <div class="kpi-info">
                    <div class="kpi-label">Supplier Purchases</div>
                    <div class="kpi-value">BD <?php echo number_format($total_purchases, 3); ?></div>
                    <div class="kpi-change neutral">
                        <i class="fa fa-truck"></i> Total bill cost
                    </div>
                </div>
                <div class="kpi-icon-wrapper">
                    <i class="fa fa-shopping-cart"></i>
                </div>
            </div>
        </div>

        <!-- Cash Inwards Card -->
        <div class="col-md-3 kpi-col" style="width: 20%;">
            <div class="kpi-card inward">
                <div class="kpi-info">
                    <div class="kpi-label">Cash Inward</div>
                    <div class="kpi-value">BD <?php echo number_format($total_cash_inward, 3); ?></div>
                    <div class="kpi-change up">
                        <i class="fa fa-arrow-down"></i> Cash Inflows
                    </div>
                </div>
                <div class="kpi-icon-wrapper">
                    <i class="fa fa-sign-in"></i>
                </div>
            </div>
        </div>

        <!-- Cash Outwards Card -->
        <div class="col-md-3 kpi-col" style="width: 20%;">
            <div class="kpi-card outward">
                <div class="kpi-info">
                    <div class="kpi-label">Cash Outward</div>
                    <div class="kpi-value">BD <?php echo number_format($total_cash_outward, 3); ?></div>
                    <div class="kpi-change down">
                        <i class="fa fa-arrow-up"></i> Cash Outflows
                    </div>
                </div>
                <div class="kpi-icon-wrapper">
                    <i class="fa fa-sign-out"></i>
                </div>
            </div>
        </div>

        <!-- Cash Balance Card -->
        <div class="col-md-3 kpi-col" style="width: 20%;">
            <div class="kpi-card balance">
                <div class="kpi-info">
                    <div class="kpi-label">Net Cash Balance</div>
                    <div class="kpi-value" style="color: <?php echo $cash_balance >= 0 ? '#10b981' : '#ef4444'; ?>;">
                        BD <?php echo number_format($cash_balance, 3); ?>
                    </div>
                    <div class="kpi-change neutral">
                        <i class="fa fa-university"></i> Ledger balance
                    </div>
                </div>
                <div class="kpi-icon-wrapper">
                    <i class="fa fa-bank"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Operations & CRM KPIs Row -->
    <div class="dashboard-section-header">Operational Summary</div>
    <div class="kpi-row">
        <!-- Total Tenders Card -->
        <div class="col-md-3 kpi-col" style="width: 20%;">
            <div class="kpi-card tenders">
                <div class="kpi-info">
                    <div class="kpi-label">Total Tenders</div>
                    <div class="kpi-value"><?php echo $total_tenders; ?></div>
                    <div class="kpi-change up">
                        <i class="fa fa-gavel"></i> Active bids
                    </div>
                </div>
                <div class="kpi-icon-wrapper">
                    <i class="fa fa-file-text-o"></i>
                </div>
            </div>
        </div>

        <!-- Customers Card -->
        <div class="col-md-3 kpi-col" style="width: 20%;">
            <div class="kpi-card customers">
                <div class="kpi-info">
                    <div class="kpi-label">Customers</div>
                    <div class="kpi-value"><?php echo $customer_count; ?></div>
                    <div class="kpi-change neutral">
                        <i class="fa fa-users"></i> Registered clients
                    </div>
                </div>
                <div class="kpi-icon-wrapper">
                    <i class="fa fa-user"></i>
                </div>
            </div>
        </div>

        <!-- Vendors Card -->
        <div class="col-md-3 kpi-col" style="width: 20%;">
            <div class="kpi-card vendors">
                <div class="kpi-info">
                    <div class="kpi-label">Vendors</div>
                    <div class="kpi-value"><?php echo $vendor_count; ?></div>
                    <div class="kpi-change neutral">
                        <i class="fa fa-industry"></i> Active suppliers
                    </div>
                </div>
                <div class="kpi-icon-wrapper">
                    <i class="fa fa-handshake-o"></i>
                </div>
            </div>
        </div>

        <!-- Total Items Card -->
        <div class="col-md-3 kpi-col" style="width: 20%;">
            <div class="kpi-card items">
                <div class="kpi-info">
                    <div class="kpi-label">In-Stock Items</div>
                    <div class="kpi-value"><?php echo $total_items; ?></div>
                    <div class="kpi-change neutral">
                        <i class="fa fa-cubes"></i> Inventory items
                    </div>
                </div>
                <div class="kpi-icon-wrapper">
                    <i class="fa fa-archive"></i>
                </div>
            </div>
        </div>

        <!-- Total Categories Card -->
        <div class="col-md-3 kpi-col" style="width: 20%;">
            <div class="kpi-card categories">
                <div class="kpi-info">
                    <div class="kpi-label">Categories</div>
                    <div class="kpi-value"><?php echo $total_category; ?></div>
                    <div class="kpi-change neutral">
                        <i class="fa fa-list"></i> Category groups
                    </div>
                </div>
                <div class="kpi-icon-wrapper">
                    <i class="fa fa-folder-open"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Activities Row (Created Today counters) -->
    <div class="ops-row">
        <!-- Today's Tender Enquiries -->
        <div class="col-md-4 ops-col" style="width: 33.333%;">
            <div class="ops-card">
                <div class="ops-icon" style="color: #f59e0b;"><i class="fa fa-envelope-o"></i></div>
                <div>
                    <div class="ops-value"><?php echo $total_enquiry; ?></div>
                    <div class="ops-label">Enquiries Added Today</div>
                </div>
            </div>
        </div>
        
        <!-- Today's Quotations -->
        <div class="col-md-4 ops-col" style="width: 33.333%;">
            <div class="ops-card">
                <div class="ops-icon" style="color: #10b981;"><i class="fa fa-file-powerpoint-o"></i></div>
                <div>
                    <div class="ops-value"><?php echo $tender_quotation_count; ?></div>
                    <div class="ops-label">Quotations Generated Today</div>
                </div>
            </div>
        </div>

        <!-- Today's Vendor Enquiries -->
        <div class="col-md-4 ops-col" style="width: 33.333%;">
            <div class="ops-card">
                <div class="ops-icon" style="color: #6366f1;"><i class="fa fa-question-circle-o"></i></div>
                <div>
                    <div class="ops-value"><?php echo $vendor_enquiry_count; ?></div>
                    <div class="ops-label">Vendor Rate Inquiries Today</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <!-- Monthly Sales vs Purchases Comparison (Line/Area) -->
        <div class="col-md-8">
            <div class="analytics-card">
                <div class="analytics-header">
                    <h3 class="analytics-title">
                        <i class="fa fa-line-chart"></i> Monthly Sales & Purchases Comparison
                    </h3>
                </div>
                <div class="chart-container" style="height: 320px;">
                    <canvas id="salesPurchasesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tender Status Distribution (Doughnut) -->
        <div class="col-md-4">
            <div class="analytics-card">
                <div class="analytics-header">
                    <h3 class="analytics-title">
                        <i class="fa fa-pie-chart"></i> Tender Status Distribution
                    </h3>
                </div>
                <div class="chart-container" style="height: 320px;">
                    <canvas id="tenderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Lists Section (Recent Tenders & Quick Actions side-by-side) -->
    <div class="row">
        <!-- Recent Tenders (8 columns) -->
        <div class="col-md-8">
            <div class="section-card" style="min-height: 480px;">
                <div class="section-header">
                    <h3 class="section-title"><i class="fa fa-gavel"></i> Recent Tender Enquiries</h3>
                </div>
                <div class="table-responsive">
                    <table id="recentTendersTable" class="table custom-table">
                        <thead>
                            <tr>
                                <th>Enquiry No</th>
                                <th>Date</th>
                                <th>Tender Name / Subject</th>
                                <th>Customer Name</th>
                                <th>Company</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_tenders)): ?>
                                <?php foreach ($recent_tenders as $tender): ?>
                                    <?php 
                                        $statusClass = 'status-default';
                                        $status = $tender['tender_status'] ? $tender['tender_status'] : 'Open';
                                        if (strtolower($status) == 'open') $statusClass = 'status-open';
                                        elseif (strtolower($status) == 'quoted') $statusClass = 'status-quoted';
                                        elseif (strtolower($status) == 'won') $statusClass = 'status-won';
                                        elseif (strtolower($status) == 'lost') $statusClass = 'status-lost';
                                        elseif (strtolower($status) == 'on hold') $statusClass = 'status-hold';
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo base_url('tender-enquiry-edit/' . $tender['tender_enquiry_id']); ?>" style="font-weight: 600; color: #4f46e5;">
                                                <?php echo $tender['enquiry_no']; ?>
                                            </a>
                                        </td>
                                        <td><?php echo date('d-m-Y', strtotime($tender['enquiry_date'])); ?></td>
                                        <td><span style="font-weight: 500; color: #1e293b;"><?php echo $tender['tender_name'] ? $tender['tender_name'] : '-'; ?></span></td>
                                        <td><?php echo $tender['customer_name']; ?></td>
                                        <td><span class="text-muted" style="font-size: 12px;"><?php echo $tender['company_name'] ? $tender['company_name'] : '-'; ?></span></td>
                                        <td>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo $status; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center" style="color: #64748b; padding: 30px !important;">
                                        No recent active tenders found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions Grid (4 columns) -->
        <div class="col-md-4">
            <div class="section-card" style="min-height: 480px;">
                <div class="section-header">
                    <h3 class="section-title"><i class="fa fa-bolt"></i> Quick Actions</h3>
                </div>
                <div class="quick-actions-grid">
                    <a href="<?php echo base_url('add-tender-enquiry'); ?>" class="action-btn" style="padding: 15px 10px;">
                        <i class="fa fa-plus-circle" style="color: #6366f1;"></i>
                        <span>New Enquiry</span>
                    </a>
                    <a href="<?php echo base_url('tender-invoice-add'); ?>" class="action-btn" style="padding: 15px 10px;">
                        <i class="fa fa-file-text-o" style="color: #10b981;"></i>
                        <span>New Invoice</span>
                    </a>
                    <a href="<?php echo base_url('credit-debit-note-add'); ?>" class="action-btn" style="padding: 15px 10px;">
                        <i class="fa fa-calculator" style="color: #ef4444;"></i>
                        <span>Debit/Credit Note</span>
                    </a>
                    <a href="<?php echo base_url('inward-list'); ?>" class="action-btn" style="padding: 15px 10px;">
                        <i class="fa fa-arrow-down" style="color: #06b6d4;"></i>
                        <span>Add Cash Inward</span>
                    </a>
                    <a href="<?php echo base_url('outward-list'); ?>" class="action-btn" style="padding: 15px 10px;">
                        <i class="fa fa-arrow-up" style="color: #f97316;"></i>
                        <span>Add Cash Outward</span>
                    </a>
                    <a href="<?php echo base_url('cash-ledger'); ?>" class="action-btn" style="padding: 15px 10px;">
                        <i class="fa fa-university" style="color: #8b5cf6;"></i>
                        <span>Cash Ledger</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>