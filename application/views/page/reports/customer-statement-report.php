<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header no-print">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Customer</a></li>
        <li class="active"> <?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<style>
    /* Modern SaaS Metric Cards Styling */
    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 20px;
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
    .stat-card.purchases { border-left-color: #00a65a; }
    .stat-card.paid { border-left-color: #dd4b39; }
    .stat-card.closing { border-left-color: #605ca8; }
    
    .stat-card .label-text {
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        color: #777d8c;
        margin-bottom: 8px;
        display: block;
    }
    .stat-card .value-text {
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1.2;
    }
    .stat-card .icon-bg {
        position: absolute;
        right: 15px;
        bottom: 10px;
        font-size: 40px;
        color: rgba(0, 0, 0, 0.05);
        pointer-events: none;
    }
    
    /* Interactive Filter Box Design */
    .box-premium {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        background: #fff;
        border: none;
        margin-bottom: 25px;
    }
    .box-premium .box-header {
        border-bottom: 1px solid #f4f6f9;
        padding: 15px 20px;
        background: #3c8dbc;
        color: #fff;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .box-premium .box-header .box-title {
        font-size: 16px;
        font-weight: 600;
        color: #fff !important;
    }
    .box-premium .box-body {
        padding: 20px;
    }
    
    /* Elegant Table Design */
    .table-premium {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-premium th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding: 12px 16px;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-premium td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
        font-size: 13.5px;
    }
    .table-premium tr:hover td {
        background-color: #f8fafc;
    }
    .table-premium .row-opening {
        background-color: #fdfaf2;
        font-weight: 550;
    }
    .table-premium .row-opening td {
        border-bottom: 1px solid #f6e8c3;
    }
    
    .print-header {
        display: none;
    }
    
    /* Print Media Styling */
    @media print {
        .print-header {
            display: block !important;
            margin-bottom: 20px;
        }
        .main-header,
        .main-sidebar,
        .main-footer {
            display: none !important;
        }
        .content-wrapper {
            margin-left: 0 !important;
            padding-top: 0 !important;
            background-color: #fff !important;
        }
    }
</style>

<?php
$show_dates_style = (!empty($customer_id) && !empty($op_exists)) ? 'display: block;' : 'display: none;';
$min_date_attr = (!empty($customer_id) && !empty($op_exists)) ? 'min="' . $op_details['opening_date'] . '"' : '';
?>

<section class="content" id="stmt-report-container">
    <!-- PRINT HEADER -->
    <div class="print-header">
        <h2 style="margin: 0 0 10px 0; font-size: 20px; font-weight: bold; text-transform: uppercase; border-bottom: 2px solid #333; padding-bottom: 8px; text-align: center;">CUSTOMER STATEMENT REPORT</h2>
        <table style="width: 100%; margin-bottom: 15px; font-size: 12px; border-collapse: collapse;">
            <tr>
                <td style="text-align: left; width: 50%; border: none;">
                    <strong>Customer:</strong> <?php
                        $selected_cust_name = 'All Customers';
                        foreach ($customers as $c) {
                            if ($c['id'] == $customer_id) {
                                $selected_cust_name = $c['customer_name'];
                                break;
                            }
                        }
                        echo htmlspecialchars($selected_cust_name);
                    ?>
                </td>
                <td style="text-align: right; width: 50%; border: none;">
                    <strong>Period:</strong>
                    <?php echo !empty($from_date) ? date('d-M-Y', strtotime($from_date)) : 'Start'; ?>
                    to
                    <?php echo !empty($to_date) ? date('d-M-Y', strtotime($to_date)) : 'End'; ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- Server-Side Warning Alert Banner -->
    <?php if (!empty($customer_id) && empty($op_exists)) { ?>
        <div class="alert alert-warning alert-dismissible opening-balance-warning no-print" style="border-radius: 8px; margin-bottom: 20px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-warning"></i> Attention!</h4>
            Customer opening balance is empty. Please enter the opening balance first!
        </div>
    <?php } ?>

    <!-- Filter Section -->
    <div class="box box-premium no-print">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-filter"></i> Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="get" action="" id="report-filter-form">
                <input type="hidden" name="export_excel" id="export_excel" value="0">
                <div class="row">
                    <div class="col-md-3">
                        <label>Customer</label>
                        <select name="customer_id" id="stmt_customer_select" class="form-control select2" style="border-radius: 6px;">
                            <option value="">Select Customer</option>
                            <?php foreach ($customers as $c) { ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo ($customer_id == $c['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['customer_name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-3" id="div-from-date" style="<?php echo $show_dates_style; ?>">
                        <label>From Date</label>
                        <input type="date" name="from_date" id="from_date" class="form-control" style="border-radius: 6px;" <?php echo $min_date_attr; ?> value="<?php echo htmlspecialchars($from_date); ?>">
                    </div>

                    <div class="col-md-3" id="div-to-date" style="<?php echo $show_dates_style; ?>">
                        <label>To Date</label>
                        <input type="date" name="to_date" id="to_date" class="form-control" style="border-radius: 6px;" <?php echo $min_date_attr; ?> value="<?php echo htmlspecialchars($to_date); ?>">
                    </div>

                    <div class="col-md-3" id="div-submit" style="<?php echo $show_dates_style; ?>">
                        <label>&nbsp;</label><br>
                        <button type="submit" class="btn btn-primary btn-block" style="border-radius: 6px; font-weight: 600; padding: 7px 15px;">
                            <i class="fa fa-search"></i> Filter Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($customer_id)) { ?>
        <!-- Pre-calculate period statistics -->
        <?php
        $total_debit_period = 0.000;
        $total_credit_period = 0.000;
        foreach ($record_list as $row) {
            $total_debit_period += $row['debit_amt'];
            $total_credit_period += $row['credit_amt'];
        }
        $closing_balance = $opening_balance + $total_debit_period - $total_credit_period;
        ?>

        <!-- Modern Summary Statistics Grid -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card opening">
                    <span class="label-text">Opening Balance</span>
                    <div class="value-text"><?php echo number_format($opening_balance, 3); ?></div>
                    <i class="fa fa-folder-open icon-bg"></i>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card purchases">
                    <span class="label-text">Invoiced / Debit (Period)</span>
                    <div class="value-text"><?php echo number_format($total_debit_period, 3); ?></div>
                    <i class="fa fa-shopping-cart icon-bg"></i>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card paid">
                    <span class="label-text">Received / Credit (Period)</span>
                    <div class="value-text"><?php echo number_format($total_credit_period, 3); ?></div>
                    <i class="fa fa-credit-card icon-bg"></i>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card closing">
                    <span class="label-text">Closing Balance</span>
                    <div class="value-text" style="color: #605ca8;"><?php echo number_format($closing_balance, 3); ?></div>
                    <i class="fa fa-balance-scale icon-bg"></i>
                </div>
            </div>
        </div>

        <!-- Ledger Table Section -->
        <div class="box box-premium" style="margin-top: 10px;">
            <div class="box-header" style="background: #222d32;">
                <h3 class="box-title"><i class="fa fa-list"></i> Statement Ledger</h3>
                <div class="box-tools pull-right no-print">
                    <button type="button" class="btn btn-primary btn-sm" onclick="window.print();" style="border-radius: 4px; font-weight: 600; margin-top: -2px; margin-right: 5px;">
                        <i class="fa fa-print"></i> Print PDF
                    </button>
                    <button type="button" id="btn-export-excel" class="btn btn-success btn-sm" style="border-radius: 4px; font-weight: 600; margin-top: -2px;">
                        <i class="fa fa-file-excel-o"></i> Export Excel
                    </button>
                </div>
            </div>
            <div class="box-body table-responsive" style="padding: 0;">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th style="width: 12%;">Date</th>
                            <?php if (empty($customer_id)) { ?>
                                <th style="width: 18%;">Customer</th>
                            <?php } ?>
                            <th style="width: 15%;">Voucher No</th>
                            <th>Description</th>
                            <th class="text-right" style="width: 15%; color: #27ae60;">Debit(+)</th>
                            <th class="text-right" style="width: 15%; color: #c0392b;">Credit(-)</th>
                            <th class="text-right" style="width: 18%;">Balance</th>
                        </tr>   
                    </thead>
                    <tbody>
                        <!-- Opening Balance Row -->
                        <tr class="row-opening">
                            <td>
                                <?php echo !empty($from_date) ? date('d-m-Y', strtotime($from_date)) : 'Opening'; ?>
                            </td>
                            <?php if (empty($customer_id)) { ?>
                                <td>—</td>
                            <?php } ?>
                            <td>—</td>
                            <td>Opening Balance</td>
                            <td class="text-right">0.000</td>
                            <td class="text-right">0.000</td>
                            <td class="text-right" style="font-weight: bold;"><?php echo number_format($opening_balance, 3); ?></td>
                        </tr>

                        <!-- Chronological Transactions loop -->
                        <?php
                        $running_balance = $opening_balance;
                        if (!empty($record_list)) {
                            foreach ($record_list as $row) {
                                $running_balance += $row['debit_amt'];
                                $running_balance -= $row['credit_amt'];
                                ?>
                                <tr>
                                    <td>
                                        <?php echo date('d-m-Y', strtotime($row['tr_date'])); ?>
                                    </td>
                                    <?php if (empty($customer_id)) { ?>
                                        <td style="font-weight: 600; color: #475569;">
                                            <?php echo htmlspecialchars($row['customer_name'] ?? ''); ?>
                                        </td>
                                    <?php } ?>
                                    <td style="font-weight: 600; color: #34495e;">
                                        <?php echo htmlspecialchars($row['voucher_no']); ?>
                                    </td>
                                    <td>
                                        <span class="label <?php 
                                            if ($row['type'] == 'invoice') {
                                                echo 'label-success'; 
                                            } else {
                                                echo 'label-danger';
                                            }
                                        ?>" style="font-size: 11px; padding: .2em .6em .3em;"><?php echo htmlspecialchars($row['description']); ?></span>
                                    </td>
                                    <td class="text-right" style="color: #27ae60; font-weight: 500;">
                                        <?php echo ($row['debit_amt'] > 0) ? number_format($row['debit_amt'], 3) : '-'; ?>
                                    </td>
                                    <td class="text-right" style="color: #c0392b; font-weight: 500;">
                                        <?php echo ($row['credit_amt'] > 0) ? number_format($row['credit_amt'], 3) : '-'; ?>
                                    </td>
                                    <td class="text-right" style="font-weight: bold; color: #2c3e50;">
                                        <?php echo number_format($running_balance, 3); ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="<?php echo empty($customer_id) ? 7 : 6; ?>" class="text-center" style="padding: 30px; color: #94a3b8; font-style: italic;">
                                    No transactions recorded for the selected period.
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                    <!-- Summary Footer Row -->
                    <tfoot>
                        <tr style="background: #f8fafc; font-weight: bold; border-top: 2px solid #cbd5e1;">
                            <td colspan="<?php echo empty($customer_id) ? 4 : 3; ?>" class="text-right" style="padding: 15px 16px;">Total Period Movement:</td>
                            <td class="text-right" style="color: #27ae60; padding: 15px 16px;"><?php echo number_format($total_debit_period, 3); ?></td>
                            <td class="text-right" style="color: #c0392b; padding: 15px 16px;"><?php echo number_format($total_credit_period, 3); ?></td>
                            <td class="text-right" style="color: #605ca8; font-size: 15px; padding: 15px 16px;"><?php echo number_format($closing_balance, 3); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <?php } else { ?>
        <div class="alert alert-info text-center" style="border-radius: 12px; margin-top: 20px; background-color: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; padding: 30px 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <i class="fa fa-info-circle" style="font-size: 36px; color: #0ea5e9; margin-bottom: 12px; display: block;"></i>
            <h4 style="font-weight: 600; margin-top: 0; margin-bottom: 8px;">Please Select a Customer</h4>
            <p style="margin: 0; font-size: 13.5px; color: #475569;">To generate and view the statement report ledger, please select a customer from the search filter above.</p>
        </div>
    <?php } ?>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
