<?php include_once(VIEWPATH . 'inc/header.php'); ?>

<style>
    .kpi-card {
        border-radius: 6px;
        padding: 16px 20px;
        margin-bottom: 20px;
        color: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .kpi-card .kpi-label {
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.9;
        margin-bottom: 5px;
    }
    .kpi-card .kpi-value {
        font-size: 24px;
        font-weight: 700;
    }
    .kpi-card .kpi-icon {
        font-size: 38px;
        opacity: 0.3;
    }
    .kpi-primary { background: linear-gradient(135deg, #1e88e5, #1565c0); }
    .kpi-info { background: linear-gradient(135deg, #00acc1, #00838f); }
    .kpi-success { background: linear-gradient(135deg, #43a047, #2e7d32); }
    .kpi-danger { background: linear-gradient(135deg, #e53935, #c62828); }

    .status-badge-paid {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
        padding: 3px 8px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 11px;
    }
    .status-badge-partial {
        background-color: #fff8e1;
        color: #f57f17;
        border: 1px solid #ffe082;
        padding: 3px 8px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 11px;
    }
    .status-badge-pending {
        background-color: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
        padding: 3px 8px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 11px;
    }

    @media print {
        .content-header, .box-filter, .btn-print, .main-sidebar, .main-header, .main-footer, .breadcrumb {
            display: none !important;
        }
        .content-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }
        .box {
            border: none !important;
            box-shadow: none !important;
        }
        .table-responsive {
            overflow: visible !important;
        }
        .print-header {
            display: block !important;
            margin-bottom: 20px;
            text-align: center;
        }
    }
    .print-header { display: none; }
</style>

<section class="content-header">
    <h1>
        <i class="fa fa-clock-o text-red"></i> <?php echo htmlspecialchars($title); ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo site_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#"><i class="fa fa-folder-open"></i> Tender / Sales</a></li>
        <li class="active"><?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">

    <!-- Print Header -->
    <div class="print-header">
        <h2><?php echo htmlspecialchars($title); ?></h2>
        <p>Generated on: <?php echo date('d-m-Y H:i:s'); ?></p>
    </div>

    <!-- KPI Cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="kpi-card kpi-primary">
                <div>
                    <div class="kpi-label">Total Invoices</div>
                    <div class="kpi-value"><?php echo number_format($tot_count); ?></div>
                </div>
                <div class="kpi-icon"><i class="fa fa-files-o"></i></div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="kpi-card kpi-info">
                <div>
                    <div class="kpi-label">Total Invoice Amount</div>
                    <div class="kpi-value"><?php echo number_format($tot_amount, 2); ?></div>
                </div>
                <div class="kpi-icon"><i class="fa fa-money"></i></div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="kpi-card kpi-success">
                <div>
                    <div class="kpi-label">Total Received Amount</div>
                    <div class="kpi-value"><?php echo number_format($tot_paid, 2); ?></div>
                </div>
                <div class="kpi-icon"><i class="fa fa-check-circle"></i></div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="kpi-card kpi-danger">
                <div>
                    <div class="kpi-label">Total Outstanding Receivable</div>
                    <div class="kpi-value"><?php echo number_format($tot_balance, 2); ?></div>
                </div>
                <div class="kpi-icon"><i class="fa fa-exclamation-circle"></i></div>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="box box-default box-filter">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-filter"></i> Search & Filters</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form method="post" action="" id="frmFilter">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="srch_from_date">From Date</label>
                        <input type="date" name="srch_from_date" id="srch_from_date" class="form-control"
                            value="<?php echo htmlspecialchars($srch_from_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_to_date">To Date</label>
                        <input type="date" name="srch_to_date" id="srch_to_date" class="form-control"
                            value="<?php echo htmlspecialchars($srch_to_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_customer_id">Customer</label>
                        <?php echo form_dropdown('srch_customer_id', $customer_opt, $srch_customer_id, 'id="srch_customer_id" class="form-control select2" style="width:100%"'); ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_status">Payment Status</label>
                        <?php echo form_dropdown('srch_status', $status_opt, $srch_status, 'id="srch_status" class="form-control select2" style="width:100%"'); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Apply Filter</button>
                        <a href="<?php echo site_url('customer-pending-invoice-report/clear_filter'); ?>" class="btn btn-default"><i class="fa fa-refresh"></i> Clear</a>
                        <button type="button" class="btn btn-success btn-print" onclick="window.print();"><i class="fa fa-print"></i> Print Report</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Table -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-list"></i> Outstanding Customer Invoices</h3>
            <div class="box-tools pull-right">
                <span class="label label-info" style="font-size: 13px;"><?php echo count($record_list); ?> Records Found</span>
            </div>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped" id="tblReport">
                <thead>
                    <tr class="bg-gray" style="font-weight: 600;">
                        <th class="text-center" style="width: 50px;">S.No</th>
                        <th style="width: 95px;">Invoice Date</th>
                        <th>Invoice No</th>
                        <th>Customer Name</th>
                        <th>Tender / Project</th>
                        <th style="width: 100px;">Bill Type</th>
                        <th class="text-right" style="width: 125px;">Invoice Amount</th>
                        <th class="text-right" style="width: 125px;">Received Amount</th>
                        <th class="text-right" style="width: 135px;">Outstanding Amount</th>
                        <th class="text-center" style="width: 95px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)): ?>
                        <?php foreach ($record_list as $idx => $row): ?>
                            <tr>
                                <td class="text-center"><?php echo $idx + 1; ?></td>
                                <td><?php echo $row['invoice_date'] ? date('d-m-Y', strtotime($row['invoice_date'])) : '-'; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['invoice_no'] ?: '-'); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($row['customer_name'] ?: '-'); ?></td>
                                <td>
                                    <?php if (!empty($row['tender_details'])): ?>
                                        <small class="label label-default" style="color:#333;"><?php echo htmlspecialchars($row['tender_details']); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['bill_type'] == 'Opening Balance'): ?>
                                        <span class="label label-success"><?php echo htmlspecialchars($row['bill_type']); ?></span>
                                    <?php else: ?>
                                        <span class="label label-primary"><?php echo htmlspecialchars($row['bill_type']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right" style="font-weight: 600;">
                                    <?php echo number_format($row['total_amount'], 2); ?>
                                </td>
                                <td class="text-right text-green" style="font-weight: 600;">
                                    <?php echo number_format($row['paid_amount'], 2); ?>
                                </td>
                                <td class="text-right text-red" style="font-weight: 700; font-size: 13px;">
                                    <?php echo number_format($row['balance_amount'], 2); ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['payment_status'] == 'Paid'): ?>
                                        <span class="status-badge-paid"><i class="fa fa-check"></i> Paid</span>
                                    <?php elseif ($row['payment_status'] == 'Partial'): ?>
                                        <span class="status-badge-partial"><i class="fa fa-adjust"></i> Partial</span>
                                    <?php else: ?>
                                        <span class="status-badge-pending"><i class="fa fa-hourglass-start"></i> Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted" style="padding: 30px;">
                                <i class="fa fa-info-circle fa-2x"></i><br>
                                No records found matching the specified filters.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #2c3e50; color: #ffffff; font-weight: bold; font-size: 14px;">
                        <td colspan="6" class="text-right">GRAND TOTAL:</td>
                        <td class="text-right"><?php echo number_format($tot_amount, 2); ?></td>
                        <td class="text-right" style="color: #2ecc71;"><?php echo number_format($tot_paid, 2); ?></td>
                        <td class="text-right" style="color: #e74c3c;"><?php echo number_format($tot_balance, 2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
