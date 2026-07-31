<?php include_once(VIEWPATH . '/inc/header.php'); ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Premium Dashboard Aesthetics matching the image exactly */
    :root {
        --fin-primary: #5a5ce8;
        /* Blue-purple for Balance/Buttons */
        --fin-success: #1fc27d;
        /* Green for Add Funds */
        --fin-danger: #fe5671;
        /* Pink/Red for Expenses */
        --fin-dark: #1e293b;
        --fin-light: #f8fafc;
        --fin-bg: #f3f6fc;
        /* The gray background from the image */
        --fin-border: #edf2f7;
    }

    body,
    .content-wrapper {
        background-color: var(--fin-bg) !important;
    }

    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding: 0 15px;
    }

    .content-header h1 {
        font-weight: 700;
        color: var(--fin-dark);
        font-size: 22px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .content-header h1 .icon-box {
        background: rgba(90, 92, 232, 0.1);
        color: var(--fin-primary);
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 16px;
    }

    .content-header .breadcrumb {
        float: none;
        background: transparent;
        padding: 0;
        margin: 0;
        font-size: 13px;
        color: #94a3b8;
    }

    .content-header .breadcrumb li a {
        color: #94a3b8;
    }

    /* Cards */
    .dash-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        border: none;
        margin-bottom: 24px;
        height: 100%;
    }

    .dash-card-header {
        padding: 20px 24px 10px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .header-title-box {
        display: flex;
        gap: 12px;
    }

    .header-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .icon-blue {
        background: rgba(90, 92, 232, 0.1);
        color: var(--fin-primary);
    }

    .icon-green {
        background: rgba(31, 194, 125, 0.1);
        color: var(--fin-success);
    }

    .icon-purple {
        background: rgba(90, 92, 232, 0.1);
        color: var(--fin-primary);
    }

    .header-text h3 {
        margin: 0 0 4px 0;
        font-size: 16px;
        font-weight: 700;
        color: var(--fin-dark);
    }

    .header-text p {
        margin: 0;
        font-size: 13px;
        color: #94a3b8;
    }

    .dash-card-body {
        padding: 20px 24px 24px;
    }

    /* Top KPI Cards */
    .kpi-row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -12px 24px;
    }

    .kpi-col {
        padding: 0 12px;
        width: 33.333%;
    }

    @media (max-width: 768px) {
        .kpi-col {
            width: 100%;
            margin-bottom: 15px;
        }
    }

    .kpi-card {
        border-radius: 12px;
        padding: 24px;
        color: #fff;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        min-height: 140px;
    }

    .kpi-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .kpi-details h4 {
        margin: 0 0 8px 0;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.9;
    }

    .kpi-details h3 {
        margin: 0 0 8px 0;
        font-size: 28px;
        font-weight: 700;
    }

    .kpi-details h3 small {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 500;
    }

    .kpi-details p {
        margin: 0;
        font-size: 13px;
        opacity: 0.9;
    }

    /* The exact wave graphics from the image */
    .kpi-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 60px;
        background-size: cover;
        background-position: center bottom;
        background-repeat: no-repeat;
        opacity: 0.5;
    }

    .kpi-balance {
        background: #3b82f6;
    }

    .kpi-balance::after {
        background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg"><path fill="%23ffffff" fill-opacity="0.3" d="M0,192L48,176C96,160,192,128,288,138.7C384,149,480,203,576,213.3C672,224,768,192,864,165.3C960,139,1056,117,1152,117.3C1248,117,1344,139,1392,149.3L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
    }

    .kpi-inward {
        background: #1fc27d;
    }

    .kpi-inward::after {
        background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg"><path fill="%23ffffff" fill-opacity="0.3" d="M0,128L60,149.3C120,171,240,213,360,202.7C480,192,600,128,720,117.3C840,107,960,149,1080,176C1200,203,1320,213,1380,218.7L1440,224L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path></svg>');
    }

    .kpi-outward {
        background: #fe5671;
    }

    .kpi-outward::after {
        background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg"><path fill="%23ffffff" fill-opacity="0.3" d="M0,256L80,240C160,224,320,192,480,186.7C640,181,800,203,960,213.3C1120,224,1280,224,1360,224L1440,224L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path></svg>');
    }

    .kpi-menu-dots {
        position: absolute;
        top: 24px;
        right: 24px;
        font-size: 20px;
        opacity: 0.6;
        cursor: pointer;
    }

    /* 3 Column Layout */
    .row-flex {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -12px;
    }

    .col-flex-4 {
        width: 33.333%;
        padding: 0 12px;
        display: flex;
        flex-direction: column;
    }

    /* Forms */
    .form-group label {
        font-weight: 500;
        font-size: 13px;
        color: #475569;
        margin-bottom: 6px;
    }

    .form-control-modern {
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        padding: 8px 12px;
        height: 40px;
        box-shadow: none;
        font-size: 13px;
    }

    .form-control-modern:focus {
        border-color: var(--fin-primary);
        box-shadow: none;
    }

    .btn-modern {
        border-radius: 6px;
        padding: 10px 20px;
        font-weight: 500;
        font-size: 13px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary-modern {
        background: var(--fin-primary);
        color: #fff;
    }

    .btn-success-modern {
        background: var(--fin-success);
        color: #fff;
    }

    /* Mini Summary Cards */
    .mini-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        margin-bottom: 24px;
    }

    .mini-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .mini-card-details {
        min-width: 0;
        /* allows text truncation if needed */
    }

    .mini-card-details p {
        margin: 0 0 4px 0;
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .mini-card-details h4 {
        margin: 0 0 4px 0;
        font-size: 18px;
        font-weight: 700;
        color: var(--fin-dark);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .mini-card-details span {
        font-size: 12px;
        color: #94a3b8;
    }

    /* Table */
    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-modern th {
        padding: 16px 24px;
        font-size: 11px;
        text-transform: uppercase;
        color: #94a3b8;
        font-weight: 600;
        border-bottom: 1px solid var(--fin-border);
        white-space: nowrap;
    }

    .table-modern td {
        padding: 16px 24px;
        vertical-align: middle;
        border-bottom: 1px solid var(--fin-border);
        font-size: 13px;
        color: #475569;
    }

    .table-modern tr:last-child td {
        border-bottom: none;
    }

    .badge-modern {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
    }

    .badge-expense {
        background: #ffe4e6;
        color: #fe5671;
    }

    .badge-funded {
        background: #dcfce7;
        color: #1fc27d;
    }

    .amount-expense {
        color: #fe5671;
        font-weight: 600;
        white-space: nowrap;
    }

    .amount-funded {
        color: #1fc27d;
        font-weight: 600;
        white-space: nowrap;
    }

    .action-btn {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        margin: 0 2px;
    }

    .btn-edit-action {
        background: #eff6ff;
        color: #3b82f6;
    }

    .btn-delete-action {
        background: #ffe4e6;
        color: #fe5671;
    }

    /* Chart Legend Customization */
    .chart-legend-container {
        margin-top: 20px;
        font-size: 12px;
    }

    .legend-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        align-items: center;
    }

    .legend-color-box {
        width: 10px;
        height: 10px;
        border-radius: 2px;
        margin-right: 8px;
        display: inline-block;
    }

    .legend-label {
        color: var(--fin-dark);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .legend-values {
        text-align: right;
    }

    .legend-percent {
        font-weight: 600;
        color: #64748b;
    }

    .legend-amount {
        color: #94a3b8;
        font-size: 11px;
        display: block;
    }

    .chart-center-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    /* Responsive Media Queries */
    @media (max-width: 1200px) {
        .col-flex-4 {
            width: 50%;
            margin-bottom: 24px;
        }
    }

    @media (max-width: 991px) {
        .kpi-col {
            width: 50%;
            margin-bottom: 24px;
        }

        .col-flex-4 {
            width: 50%;
            margin-bottom: 24px;
        }
    }

    @media (max-width: 768px) {
        .kpi-col {
            width: 100%;
            margin-bottom: 15px;
        }

        .col-flex-4 {
            width: 100%;
            margin-bottom: 24px;
        }

        .content-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .dash-card-header {
            flex-direction: column;
            gap: 10px;
        }

        .dash-card-header>div:last-child {
            width: 100%;
        }

        .chart-responsive-col {
            width: 100% !important;
            float: none !important;
        }

        .form-row-responsive>div {
            width: 100% !important;
            float: none !important;
        }

        /* Make table header filters responsive */
        .dash-card-header .table-filters {
            width: 100%;
            justify-content: flex-start;
        }
    }

    .btn-page-nav {
        border: 1px solid #e2e8f0;
        background: #fff;
        min-width: 32px;
        height: 32px;
        border-radius: 6px;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-page-nav:hover:not([disabled]) {
        background: #f1f5f9;
        color: var(--fin-primary);
        border-color: var(--fin-primary);
    }
    .btn-page-nav.active {
        background: var(--fin-primary) !important;
        color: #fff !important;
        border-color: var(--fin-primary) !important;
    }
</style>

<div class="content-header">
    <h1>
        <div class="icon-box"><i class="fa fa-file-text-o"></i></div>
        Petty Cash Dashboard
    </h1>
    <ol class="breadcrumb">
        <li>Accounts</li>
        <li><i class="fa fa-angle-right" style="margin: 0 8px;"></i></li>
        <li>Petty Cash</li>
    </ol>
</div>

<section class="content" style="padding: 0 15px;">
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible"
            style="border-radius: 8px; border: none; background: #dcfce7; color: #166534;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- 0. Date Range Filter Card -->
    <div class="dash-card" style="margin-bottom: 24px; padding: 16px 24px;">
        <form method="post" action="<?= site_url('petty-cash') ?>"
            style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fa fa-calendar" style="color: var(--fin-primary); font-size: 18px;"></i>
                <span style="font-weight: 700; color: var(--fin-dark); font-size: 15px;">Date Range Filter</span>
            </div>
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label style="margin: 0; font-size: 13px; color: #64748b;">From:</label>
                    <input type="date" class="form-control" name="from_date" value="<?= htmlspecialchars($from_date) ?>"
                        style="height: 36px; border-radius: 6px; font-size: 13px;">
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label style="margin: 0; font-size: 13px; color: #64748b;">To:</label>
                    <input type="date" class="form-control" name="to_date" value="<?= htmlspecialchars($to_date) ?>"
                        style="height: 36px; border-radius: 6px; font-size: 13px;">
                </div>
                <button type="submit" class="btn btn-primary"
                    style="height: 36px; padding: 0 16px; border-radius: 6px; background: var(--fin-primary); border: none; font-size: 13px; font-weight: 600;">
                    <i class="fa fa-filter"></i> Apply Filter
                </button>
                <a href="<?= site_url('petty-cash') ?>" class="btn btn-default"
                    style="height: 36px; padding: 6px 16px; border-radius: 6px; font-size: 13px;">
                    <i class="fa fa-refresh"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- 1. Top KPI Cards -->
    <div class="kpi-row">
        <div class="kpi-col">
            <div class="kpi-card kpi-balance">
                <div class="kpi-card-icon"><i class="fa fa-briefcase"></i></div>
                <div class="kpi-details">
                    <h4>Current Balance</h4>
                    <h3><?= number_format($balance, 3) ?> <small>BHD</small></h3>
                    <p><?= $balance < 0 ? 'Overdrawn' : 'Available' ?></p>
                </div>
                <div class="kpi-menu-dots"><i class="fa fa-ellipsis-h"></i></div>
            </div>
        </div>
        <div class="kpi-col">
            <div class="kpi-card kpi-inward">
                <div class="kpi-card-icon"><i class="fa fa-database"></i></div>
                <div class="kpi-details">
                    <h4>Total Funded</h4>
                    <h3><?= number_format($total_inward, 3) ?> <small>BHD</small></h3>
                    <p>Total amount added</p>
                </div>
                <div class="kpi-menu-dots"><i class="fa fa-ellipsis-h"></i></div>
            </div>
        </div>
        <div class="kpi-col">
            <div class="kpi-card kpi-outward">
                <div class="kpi-card-icon"><i class="fa fa-shopping-cart"></i></div>
                <div class="kpi-details">
                    <h4>Total Spent</h4>
                    <h3><?= number_format($total_outward, 3) ?> <small>BHD</small></h3>
                    <p>Total expenses</p>
                </div>
                <div class="kpi-menu-dots"><i class="fa fa-ellipsis-h"></i></div>
            </div>
        </div>
    </div>

    <!-- 2. Middle 3 Columns -->
    <div class="row-flex" style="margin-bottom: 24px;">

        <!-- Record Expense -->
        <div class="col-flex-4">
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="header-title-box">
                        <div class="header-icon icon-blue"><i class="fa fa-file-text-o"></i></div>
                        <div class="header-text">
                            <h3>Record Expense</h3>
                            <p>Add new petty cash expense</p>
                        </div>
                    </div>
                </div>
                <div class="dash-card-body">
                    <form role="form" method="post" action="<?= site_url('pettycash/add_expense') ?>">
                        <div class="row form-row-responsive">
                            <div class="col-xs-6 form-group">
                                <label>Date</label>
                                <input type="date" class="form-control form-control-modern" name="transaction_date"
                                    value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-xs-6 form-group">
                                <label>Amount (BHD)</label>
                                <input type="number" step="0.001" class="form-control form-control-modern" name="amount"
                                    placeholder="0.000" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Remarks / Description</label>
                            <input type="text" class="form-control form-control-modern" name="remarks"
                                placeholder="Optional notes for this expense">
                        </div>
                        <div style="text-align: right; margin-top: 16px;">
                            <button type="submit" class="btn-modern btn-primary-modern"><i class="fa fa-save"></i> Save
                                Expense</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Funds -->
        <div class="col-flex-4">
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="header-title-box">
                        <div class="header-icon icon-green"><i class="fa fa-lock"></i></div>
                        <div class="header-text">
                            <h3>Add Funds to Box</h3>
                            <p>Add funds to petty cash box</p>
                        </div>
                    </div>
                </div>
                <div class="dash-card-body">
                    <form role="form" method="post" action="<?= site_url('pettycash/add_funds') ?>">
                        <div class="row form-row-responsive">
                            <div class="col-xs-6 form-group">
                                <label>Date</label>
                                <input type="date" class="form-control form-control-modern" name="transaction_date"
                                    value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-xs-6 form-group">
                                <label>Amount (BHD)</label>
                                <input type="number" step="0.001" class="form-control form-control-modern" name="amount"
                                    placeholder="0.000" required>
                            </div>
                        </div>
                        <div class="row form-row-responsive">
                            <div class="col-xs-6 form-group">
                                <label>Account Group</label>
                                <select class="form-control form-control-modern" name="ac_type" id="add_ac_type" required>
                                    <option value="">Select Group</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank</option>
                                </select>
                            </div>
                            <div class="col-xs-6 form-group" id="add_bank_group" style="display: none;">
                                <label>Bank</label>
                                <select class="form-control select2" name="bank_id" id="add_bank_id">
                                    <option value="">Select Bank</option>
                                    <?php foreach ($bank_opt as $b_id => $b_name): ?>
                                        <option value="<?= $b_id ?>"><?= $b_name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row form-row-responsive">
                            <div class="col-xs-6 form-group">
                                <label>Account Head</label>
                                <select class="form-control select2" name="account_head_id" id="add_account_head_id" required>
                                    <option value="">Select Account Head</option>
                                    <?php foreach ($account_heads as $ah): ?>
                                        <?php if ($ah['type'] == 'Inward'): ?>
                                            <option value="<?= $ah['account_head_id'] ?>"><?= $ah['account_head_name'] ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-xs-6 form-group">
                                <label>Sub Account Head</label>
                                <select class="form-control select2" name="category_id" id="add_category_id" required>
                                    <option value="">Select Sub Account</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Source / Remarks</label>
                            <input type="text" class="form-control form-control-modern" name="remarks"
                                placeholder="e.g. Cash withdrawn from Bank">
                        </div>
                        <div style="text-align: right; margin-top: 16px;">
                            <button type="submit" class="btn-modern btn-success-modern"><i class="fa fa-upload"></i> Add
                                Funds</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Expense Summary Chart -->
        <div class="col-flex-4">
            <div class="dash-card">
                <div class="dash-card-header" style="align-items: center;">
                    <div class="header-text">
                        <h3 style="font-size: 15px;">Expense Summary</h3>
                    </div>
                    <div class="table-filters" style="display: flex; justify-content: flex-end;">
                        <select class="form-control"
                            style="font-size: 12px; height: 30px; padding: 2px 10px; border-radius: 4px; width: auto;">
                            <option>This Month</option>
                        </select>
                    </div>
                </div>
                <div class="dash-card-body" style="padding-top: 10px;">
                    <div class="row form-row-responsive">
                        <div class="col-xs-6 chart-responsive-col"
                            style="position: relative; height: 160px; margin-bottom: 20px;">
                            <canvas id="expenseChart"></canvas>
                            <div class="chart-center-text">
                                <span style="font-size: 11px; color:#94a3b8;">Total</span><br>
                                <strong
                                    style="font-size: 16px; color:var(--fin-dark);"><?= number_format($this_month_outward, 3) ?></strong><br>
                                <span style="font-size: 11px; color:#94a3b8;">BHD</span>
                            </div>
                        </div>
                        <div class="col-xs-6 chart-responsive-col">
                            <div class="chart-legend-container" id="chart-legend" style="margin-top: 0;">
                                <!-- Legend generated by JS -->
                            </div>
                        </div>
                    </div>
                    <div style="text-align: center; margin-top: 20px;">
                        <button class="btn btn-default btn-block"
                            style="border-radius: 6px; font-size: 12px; color: var(--fin-primary); background: #f8fafc; border: 1px solid #e2e8f0;">
                            <i class="fa fa-bar-chart"></i> View Full Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2.5 Income vs Expense Bar Chart Row -->
    <div class="row" style="margin-bottom: 24px;">
        <div class="col-md-12">
            <div class="dash-card">
                <div class="dash-card-header" style="align-items: center;">
                    <div class="header-title-box">
                        <div class="header-icon icon-blue"><i class="fa fa-bar-chart"></i></div>
                        <div class="header-text">
                            <h3 style="font-size: 16px;">Income & Expense Comparison (Bar Chart)</h3>
                            <p style="font-size: 13px; color:#94a3b8;">Overall Income vs Overall Expenses & Pettycash
                                Breakdown</p>
                        </div>
                    </div>
                </div>
                <div class="dash-card-body">
                    <div style="position: relative; height: 260px;">
                        <canvas id="incomeExpenseBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Mini Summary Cards -->
    <div class="row" style="margin-bottom: 24px;">
        <div class="col-md-3 col-sm-6">
            <div class="mini-card">
                <div class="mini-card-icon" style="background: #eff6ff; color: #3b82f6;"><i
                        class="fa fa-binoculars"></i></div>
                <div class="mini-card-details">
                    <p>Total Transactions</p>
                    <h4><?= $this_month_transactions ?></h4>
                    <span>This Month</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="mini-card">
                <div class="mini-card-icon" style="background: #dcfce7; color: #1fc27d;"><i class="fa fa-arrow-up"></i>
                </div>
                <div class="mini-card-details">
                    <p>Total Inward</p>
                    <h4><?= number_format($this_month_inward, 3) ?> <small>BHD</small></h4>
                    <span>This Month</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="mini-card">
                <div class="mini-card-icon" style="background: #ffe4e6; color: #fe5671;"><i
                        class="fa fa-arrow-down"></i></div>
                <div class="mini-card-details">
                    <p>Total Outward</p>
                    <h4><?= number_format($this_month_outward, 3) ?> <small>BHD</small></h4>
                    <span>This Month</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="mini-card">
                <div class="mini-card-icon" style="background: #f3e8ff; color: #a855f7;"><i class="fa fa-briefcase"></i>
                </div>
                <div class="mini-card-details">
                    <p>Balance</p>
                    <h4><?= number_format($balance, 3) ?> <small>BHD</small></h4>
                    <span>Current Balance</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Transaction History Table -->
    <div class="dash-card">
        <div class="dash-card-header" style="align-items: center;">
            <div class="header-title-box">
                <div class="header-icon" style="color: var(--fin-primary); font-size:18px;"><i
                        class="fa fa-history"></i></div>
                <div class="header-text">
                    <h3 style="margin-top: 6px;">Transaction History</h3>
                </div>
            </div>
            <div class="table-filters" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" id="table_search" class="form-control" placeholder="Search..." style="width: 140px; font-size:12px; height:32px; border-radius:6px;">
                <select id="type_filter" class="form-control" style="width: 120px; font-size:12px; height:32px; border-radius:6px;">
                    <option value="all">All Types</option>
                    <option value="Inward">Inward (+)</option>
                    <option value="Outward">Outward (-)</option>
                </select>
            </div>
        </div>
        <div class="dash-card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table-modern" id="petty_cash_history_table">
                    <thead>
                        <tr>
                            <th style="padding-left: 24px;">Date</th>
                            <th>Type</th>
                            <th>Particulars</th>
                            <th>Inward / Outward</th>
                            <th>Remarks</th>
                            <th class="text-right">Inward (+)</th>
                            <th class="text-right">Outward (-)</th>
                            <th class="text-center" style="padding-right: 24px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="history_table_body">
                        <?php if (empty($history)): ?>
                            <tr>
                                <td colspan="8" class="text-center" style="padding: 40px; color: #94a3b8;">No petty cash history found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($history as $row): ?>
                                <tr data-type="<?= $row['transaction_type'] ?>">
                                    <td style="padding-left: 24px;"><?= date('d M Y', strtotime($row['transaction_date'])) ?></td>
                                    <td>
                                        <?php if ($row['transaction_type'] == 'Inward'): ?>
                                            <span class="badge-modern badge-funded">Funded</span>
                                        <?php else: ?>
                                            <span class="badge-modern badge-expense">Expense</span>
                                        <?php endif; ?>
                                        <?php if (!empty($row['ac_type'])): ?>
                                            <br><small style="color: #64748b;"><?= htmlspecialchars($row['ac_type']) ?><?= ($row['ac_type'] == 'Bank' && !empty($row['bank_name'])) ? ' (' . htmlspecialchars($row['bank_name']) . ')' : '' ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= !empty($row['account_head_name']) ? htmlspecialchars($row['account_head_name']) : '-' ?></td>
                                    <td><?= !empty($row['category_name']) ? htmlspecialchars($row['category_name']) : '-' ?></td>
                                    <td><?= !empty($row['remarks']) ? htmlspecialchars($row['remarks']) : '-' ?></td>
                                    <td class="text-right amount-funded">
                                        <?= $row['transaction_type'] == 'Inward' ? number_format($row['amount'], 3) : '-' ?>
                                    </td>
                                    <td class="text-right amount-expense">
                                        <?= $row['transaction_type'] == 'Outward' ? number_format($row['amount'], 3) : '-' ?>
                                    </td>
                                    <td class="text-center" style="padding-right: 24px;">
                                        <?php if (($row['source_type'] ?? 'Petty Cash') == 'Petty Cash'): ?>
                                            <button type="button" class="action-btn btn-edit-action btn-edit"
                                                data-id="<?= $row['id'] ?>" data-date="<?= $row['transaction_date'] ?>"
                                                data-type="<?= $row['transaction_type'] ?>"
                                                data-ac-type="<?= htmlspecialchars($row['ac_type'] ?? '') ?>"
                                                data-bank="<?= htmlspecialchars($row['bank_id'] ?? '') ?>"
                                                data-account-head="<?= $row['account_head_id'] ?>"
                                                data-category="<?= $row['category_id'] ?>" data-amount="<?= $row['amount'] ?>"
                                                data-remarks="<?= htmlspecialchars($row['remarks'] ?? '') ?>">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <button type="button" class="action-btn btn-delete-action btn-delete"
                                                data-id="<?= $row['id'] ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        <?php elseif (($row['source_type'] ?? '') == 'Tender Receipt'): ?>
                                            <span class="badge-modern" style="background:#e0f2fe; color:#0284c7;">Tender Receipt</span>
                                        <?php elseif (($row['source_type'] ?? '') == 'Vendor Payment'): ?>
                                            <span class="badge-modern" style="background:#fef3c7; color:#d97706;">Vendor Bill</span>
                                        <?php elseif (($row['source_type'] ?? '') == 'Customs Bill'): ?>
                                            <span class="badge-modern" style="background:#f3e8ff; color:#9333ea;">Customs Bill</span>
                                        <?php elseif (($row['source_type'] ?? '') == 'DP Bill'): ?>
                                            <span class="badge-modern" style="background:#fce7f3; color:#db2777;">DP Bill</span>
                                        <?php elseif (($row['source_type'] ?? '') == 'Local Purchase Bill'): ?>
                                            <span class="badge-modern" style="background:#ffedd5; color:#ea580c;">Local Purchase Bill</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--fin-border); color: #94a3b8; font-size: 12px;">
                <div id="history_showing_count">Showing 1 to <?= min(10, count($history)) ?> of <?= count($history) ?> entries</div>
                <div id="history_pagination_container" style="display: flex; gap: 4px;"></div>
            </div>
        </div>
    </div>
</section>

<!-- Edit Transaction Modal -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" role="dialog"
    aria-labelledby="editTransactionModalLabel">
    <div class="modal-dialog" role="document">
        <form method="post" action="<?= site_url('pettycash/edit_transaction') ?>">
            <div class="modal-content"
                style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                <div class="modal-header"
                    style="background: var(--fin-light); border-radius: 12px 12px 0 0; border-bottom: 1px solid var(--fin-border); padding: 20px 24px;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="opacity: 0.5;"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editTransactionModalLabel"
                        style="font-weight: 600; color: var(--fin-dark); margin: 0;"><i class="fa fa-pencil-square-o"
                            style="color: var(--fin-primary); margin-right:8px;"></i> Edit Transaction</h4>
                </div>
                <div class="modal-body" style="padding: 24px;">
                    <input type="hidden" name="id" id="edit_id">
                    <input type="hidden" name="transaction_type" id="edit_type">

                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" class="form-control form-control-modern" name="transaction_date"
                            id="edit_date" required>
                    </div>

                    <div class="form-group" id="edit_ac_type_group">
                        <label>Account Group</label>
                        <select class="form-control form-control-modern" name="ac_type" id="edit_ac_type" required>
                            <option value="">Select Group</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank</option>
                        </select>
                    </div>

                    <div class="form-group" id="edit_bank_group" style="display: none;">
                        <label>Bank</label>
                        <select class="form-control select2" name="bank_id" id="edit_bank_id" style="width: 100%;">
                            <option value="">Select Bank</option>
                            <?php foreach ($bank_opt as $b_id => $b_name): ?>
                                <option value="<?= $b_id ?>"><?= $b_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" id="edit_account_head_group">
                        <label>Account Head</label>
                        <select class="form-control select2" name="account_head_id" id="edit_account_head_id"
                            style="width: 100%;">
                            <option value="">Select Account Head</option>
                            <?php foreach ($account_heads as $ah): ?>
                                <option value="<?= $ah['account_head_id'] ?>"><?= $ah['account_head_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" id="edit_category_group">
                        <label>Sub Account Head</label>
                        <select class="form-control select2" name="category_id" id="edit_category_id"
                            style="width: 100%;">
                            <option value="">Select Sub Account</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Amount (BHD)</label>
                        <input type="number" step="0.001" class="form-control form-control-modern" name="amount"
                            id="edit_amount" required>
                    </div>

                    <div class="form-group">
                        <label>Remarks</label>
                        <input type="text" class="form-control form-control-modern" name="remarks" id="edit_remarks">
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--fin-border); padding: 16px 24px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal"
                        style="border-radius:6px; border:none; background:#f1f5f9; color:#64748b; padding:8px 16px;">Cancel</button>
                    <button type="submit" class="btn btn-primary"
                        style="border-radius:6px; background:var(--fin-primary); border:none; padding:8px 16px;">Save
                        Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
// Prepare Chart Data
$chart_labels = [];
$chart_data = [];
$total_outward_month = $this_month_outward > 0 ? $this_month_outward : 1; // prevent divide by zero

foreach ($expense_summary as $ex) {
    $chart_labels[] = $ex['category_name'] ? $ex['category_name'] : 'Uncategorized';
    $chart_data[] = $ex['total_amount'];
}
?>

<script>
    var chartLabels = <?= json_encode($chart_labels) ?>;
    var chartData = <?= json_encode($chart_data) ?>;
    var chartColors = ['#5a5ce8', '#1fc27d', '#facc15', '#a855f7', '#fe5671', '#0ea5e9'];
    var totalOutward = <?= $total_outward_month ?>;

    var overallIncome = <?= (float) ($overall_income ?? 0) ?>;
    var overallExpenses = <?= (float) ($overall_expenses ?? 0) ?>;
    var pettycashIncome = <?= (float) ($pettycash_income ?? 0) ?>;
    var pettycashExpenses = <?= (float) ($pettycash_expenses ?? 0) ?>;
</script>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>