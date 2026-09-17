<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header no-print">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-industry"></i> Supplier Report</a></li>
        <li class="active"><?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<style>
    /* Metric KPI Cards */
    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 18px 20px;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
        border-left: 5px solid #3c8dbc;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
    }
    .stat-card.opening { border-left-color: #f39c12; }
    .stat-card.bills { border-left-color: #00c0ef; }
    .stat-card.adv { border-left-color: #3c8dbc; }
    .stat-card.paid { border-left-color: #00a65a; }
    .stat-card.closing { border-left-color: #dd4b39; }

    .stat-card .label-text {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        color: #777d8c;
        margin-bottom: 6px;
        display: block;
        letter-spacing: 0.5px;
    }
    .stat-card .value-text {
        font-size: 22px;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1.2;
    }
    .stat-card .icon-bg {
        position: absolute;
        right: 15px;
        bottom: 8px;
        font-size: 38px;
        color: rgba(0, 0, 0, 0.05);
        pointer-events: none;
    }

    /* Filter Box */
    .box-premium {
        border-radius: 10px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        background: #fff;
        border: none;
        margin-bottom: 20px;
    }
    .box-premium .box-header {
        border-bottom: 1px solid #f0f3f6;
        padding: 14px 20px;
        background: #3c8dbc;
        color: #fff;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
    .box-premium .box-header .box-title {
        font-size: 15px;
        font-weight: 600;
        color: #fff !important;
    }
    .box-premium .box-body {
        padding: 18px 20px;
    }

    /* Table styling */
    #tbl_vendor_balance th {
        background-color: #f4f6f9;
        color: #334155;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        vertical-align: middle;
        border-bottom: 2px solid #dde2e8;
    }
    #tbl_vendor_balance td {
        vertical-align: middle;
        font-size: 13px;
    }
    #tbl_vendor_balance tfoot th {
        background-color: #edf2f7;
        font-weight: 700;
        font-size: 13px;
        border-top: 2px solid #cbd5e1;
    }

    .select2-container .select2-selection--single {
        height: 34px !important;
        border-radius: 4px !important;
        border: 1px solid #d2d6de !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
        padding-left: 10px !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }

    /* Print header */
    .print-header {
        display: none;
    }

    @media print {
        .no-print, .main-header, .main-sidebar, .main-footer, .dataTables_filter, .dataTables_length, .dataTables_paginate, .dataTables_info {
            display: none !important;
        }
        .print-header {
            display: block !important;
            margin-bottom: 15px;
        }
        .content-wrapper {
            margin-left: 0 !important;
            padding-top: 0 !important;
            background: #fff !important;
        }
        .box-premium {
            box-shadow: none !important;
            border: 1px solid #ccc !important;
        }
        #tbl_vendor_balance {
            width: 100% !important;
            font-size: 11px !important;
        }
    }
</style>

<section class="content">
    <!-- PRINT HEADER -->
    <div class="print-header text-center">
        <h2 style="margin: 0 0 5px 0; font-size: 20px; font-weight: bold; text-transform: uppercase;">VENDOR BALANCE REPORT</h2>
        <p style="margin: 0; font-size: 13px; color: #555;">
            <strong>As On Date:</strong> <?php echo !empty($as_on_date) ? date('d-M-Y', strtotime($as_on_date)) : date('d-M-Y'); ?>
            &nbsp;|&nbsp;
            <strong>Vendor:</strong> <?php echo htmlspecialchars($selected_vendor_name); ?>
        </p>
        <hr style="margin: 10px 0 15px 0; border-top: 1px solid #333;">
    </div>

    <!-- SEARCH FILTER -->
    <div class="box box-premium no-print">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-filter"></i> Search Filter</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool text-white" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('vendor-balance-report'); ?>" id="frmVendorBalance">
                <input type="hidden" name="export_excel" id="export_excel" value="0">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="vendor_id">Vendor</label>   
                        <select name="vendor_id" id="vendor_id" class="form-control select2">
                            <option value="">All Vendors</option>
                            <?php foreach ($vendors as $v): ?>
                                <option value="<?php echo $v['vendor_id']; ?>" <?php echo ($vendor_id == $v['vendor_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($v['vendor_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="as_on_date">As On Date</label>
                        <input type="date" name="as_on_date" id="as_on_date" class="form-control"
                            value="<?php echo htmlspecialchars($as_on_date); ?>">
                    </div>

                    <div class="form-group col-md-2" style="padding-top: 28px;">
                        <label style="font-weight: 500; cursor: pointer;">
                            <input type="checkbox" name="hide_zero" value="1" <?php echo ($hide_zero == '1') ? 'checked' : ''; ?>>
                            Hide Zero Balance
                        </label>
                    </div>

                    <div class="form-group col-md-3 text-right" style="padding-top: 24px;">
                        <button type="submit" class="btn btn-primary" id="btnFilter">
                            <i class="fa fa-search"></i> Show
                        </button>
                        <a href="<?php echo site_url('vendor-balance-report/reset'); ?>" class="btn btn-default" title="Reset Filters">
                            <i class="fa fa-refresh"></i> Reset
                        </a>
                        <button type="button" class="btn btn-success" id="btnExportExcel" title="Export to Excel">
                            <i class="fa fa-file-excel-o"></i> Excel
                        </button>
                        <button type="button" class="btn btn-info" onclick="window.print();" title="Print Report">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI METRIC CARDS -->
    <div class="row no-print">
        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="stat-card opening">
                <span class="label-text">Total Opening</span>
                <div class="value-text"><?php echo number_format($summary['opening_balance'], 3); ?></div>
                <i class="fa fa-hourglass-start icon-bg"></i>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="stat-card bills">
                <span class="label-text">Total Bills</span>
                <div class="value-text"><?php echo number_format($summary['total_bills'], 3); ?></div>
                <i class="fa fa-file-text-o icon-bg"></i>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="stat-card adv">
                <span class="label-text">Advance Paid</span>
                <div class="value-text"><?php echo number_format($summary['advance_paid'], 3); ?></div>
                <i class="fa fa-credit-card icon-bg"></i>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="stat-card paid">
                <span class="label-text">Bill Payments</span>
                <div class="value-text"><?php echo number_format($summary['bill_payments'], 3); ?></div>
                <i class="fa fa-check-circle-o icon-bg"></i>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="stat-card paid">
                <span class="label-text">Total Paid</span>
                <div class="value-text"><?php echo number_format($summary['total_paid'], 3); ?></div>
                <i class="fa fa-money icon-bg"></i>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="stat-card closing">
                <span class="label-text">Net Outstanding</span>
                <div class="value-text" style="<?php echo ($summary['closing_balance'] > 0) ? 'color: #dd4b39;' : 'color: #00a65a;'; ?>">
                    <?php echo number_format($summary['closing_balance'], 3); ?>
                </div>
                <i class="fa fa-balance-scale icon-bg"></i>
            </div>
        </div>
    </div>

    <!-- MAIN REPORT TABLE -->
    <div class="box box-premium">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-list"></i> Vendor Balances
                <small style="color: rgba(255,255,255,0.85); font-weight: normal; margin-left: 8px;">
                    (<?php echo count($record_list); ?> vendors listed)
                </small>
            </h3>
            <div class="box-tools pull-right no-print">
                <button type="button" class="btn btn-box-tool text-white" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped" id="tbl_vendor_balance" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 45px;">S.No</th>
                        <th>Vendor Name</th>
                        <th class="text-right" style="width: 110px;">Opening Bal</th>
                        <th class="text-right" style="width: 115px;">Total Bills</th>
                        <th class="text-right" style="width: 110px;">Advance Paid</th>
                        <th class="text-right" style="width: 110px;">Bill Payments</th>
                        <th class="text-right" style="width: 115px;">Total Paid</th>
                        <th class="text-right" style="width: 125px;">Closing Balance</th>
                        <th class="text-center" style="width: 90px;">Status</th>
                        <th class="text-center no-print" style="width: 90px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)): ?>
                        <?php foreach ($record_list as $i => $row): ?>
                            <tr>
                                <td class="text-center"><?php echo ($i + 1); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['vendor_name']); ?></strong>
                                    <?php if (!empty($row['crno'])): ?>
                                        <br><small class="text-muted">CR: <?php echo htmlspecialchars($row['crno']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right" data-order="<?php echo (float)$row['opening_balance']; ?>">
                                    <?php echo number_format((float)$row['opening_balance'], 3); ?>
                                </td>
                                <td class="text-right" data-order="<?php echo (float)$row['total_bills']; ?>">
                                    <?php echo number_format((float)$row['total_bills'], 3); ?>
                                </td>
                                <td class="text-right" data-order="<?php echo (float)$row['advance_paid']; ?>" style="color: #3c8dbc;">
                                    <?php echo number_format((float)$row['advance_paid'], 3); ?>
                                </td>
                                <td class="text-right" data-order="<?php echo (float)$row['bill_payments']; ?>" style="color: #00a65a;">
                                    <?php echo number_format((float)$row['bill_payments'], 3); ?>
                                </td>
                                <td class="text-right" data-order="<?php echo (float)$row['total_paid']; ?>" style="font-weight: 600;">
                                    <?php echo number_format((float)$row['total_paid'], 3); ?>
                                </td>
                                <td class="text-right" data-order="<?php echo (float)$row['closing_balance']; ?>" style="font-weight: 700; <?php echo ($row['closing_balance'] > 0) ? 'color: #dd4b39;' : (($row['closing_balance'] < 0) ? 'color: #3c8dbc;' : 'color: #00a65a;'); ?>">
                                    <?php echo number_format((float)$row['closing_balance'], 3); ?>
                                </td>
                                <td class="text-center">
                                    <span class="label label-<?php echo $row['status_color']; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td class="text-center no-print">
                                    <form method="post" action="<?php echo site_url('vendor-statement-report'); ?>" target="_blank" style="display:inline-block; margin:0;">
                                        <input type="hidden" name="vendor_id" value="<?php echo $row['vendor_id']; ?>">
                                        <input type="hidden" name="to_date" value="<?php echo htmlspecialchars($as_on_date); ?>">
                                        <button type="submit" class="btn btn-info btn-xs" title="View Detailed Statement">
                                            <i class="fa fa-file-text-o"></i> Statement
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted" style="padding: 25px;">
                                <i class="fa fa-info-circle fa-2x"></i><br>No vendor records found for the selected filter.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-gray-light">
                        <th colspan="2" class="text-right">Grand Total:</th>
                        <th class="text-right"><?php echo number_format($summary['opening_balance'], 3); ?></th>
                        <th class="text-right"><?php echo number_format($summary['total_bills'], 3); ?></th>
                        <th class="text-right" style="color: #3c8dbc;"><?php echo number_format($summary['advance_paid'], 3); ?></th>
                        <th class="text-right" style="color: #00a65a;"><?php echo number_format($summary['bill_payments'], 3); ?></th>
                        <th class="text-right"><?php echo number_format($summary['total_paid'], 3); ?></th>
                        <th class="text-right" style="<?php echo ($summary['closing_balance'] > 0) ? 'color: #dd4b39;' : 'color: #00a65a;'; ?>">
                            <?php echo number_format($summary['closing_balance'], 3); ?>
                        </th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
