<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Vendor</a></li>
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
    
    /* Alert styling */
    .premium-alert {
        background: #e0f2fe;
        border-left: 5px solid #0ea5e9;
        color: #0369a1;
        padding: 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
    }
    .premium-alert i {
        font-size: 24px;
    }
</style>

<?php
$show_dates_style = (!empty($vendor_id) && !empty($op_exists)) ? 'display: block;' : 'display: none;';
$min_date_attr = (!empty($vendor_id) && !empty($op_exists)) ? 'min="' . $op_details['opening_date'] . '"' : '';
?>

<section class="content" id="stmt-report-container">
    <!-- Server-Side Warning Alert Banner -->
    <?php if (!empty($vendor_id) && empty($op_exists)) { ?>
        <div class="alert alert-warning alert-dismissible opening-balance-warning" style="border-radius: 8px; margin-bottom: 20px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-warning"></i> Attention!</h4>
            Vendor opening balance is empty. Please enter the opening balance first!
        </div>
    <?php } ?>

    <!-- Filter Section -->
    <div class="box box-premium">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-filter"></i> Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="get" action="">
                <div class="row">
                    <div class="col-md-3">
                        <label>Vendor</label>
                        <select name="vendor_id" id="stmt_vendor_select" class="form-control select2" style="border-radius: 6px;">
                            <option value="">Select Vendor</option>
                            <?php foreach ($vendors as $v) { ?>
                                <option value="<?php echo $v['id']; ?>" <?php echo ($vendor_id == $v['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($v['vendor_name']); ?>
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

    <?php if (!empty($vendor_id)) { ?>
        <!-- Pre-calculate period statistics -->
        <?php
        $total_purchases_period = 0.000;
        $total_paid_period = 0.000;
        foreach ($record_list as $row) {
            $total_purchases_period += $row['purchase_amt'];
            $total_paid_period += $row['paid_amt'];
        }
        $closing_balance = $opening_balance + $total_purchases_period - $total_paid_period;
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
                    <span class="label-text">Purchases (Period)</span>
                    <div class="value-text"><?php echo number_format($total_purchases_period, 3); ?></div>
                    <i class="fa fa-shopping-cart icon-bg"></i>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card paid">
                    <span class="label-text">Paid (Period)</span>
                    <div class="value-text"><?php echo number_format($total_paid_period, 3); ?></div>
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
            </div>
            <div class="box-body table-responsive" style="padding: 0;">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th style="width: 12%;">Date</th>
                            <?php if (empty($vendor_id)) { ?>
                                <th style="width: 18%;">Vendor</th>
                            <?php } ?>
                            <th style="width: 15%;">Voucher No</th>
                            <th>Description</th>
                            <th class="text-right" style="width: 15%; color: #27ae60;">Credit(+)</th>
                            <th class="text-right" style="width: 15%; color: #c0392b;">Debit(-)</th>
                            <th class="text-right" style="width: 18%;">Balance</th>
                        </tr>   
                    </thead>
                    <tbody>
                        <!-- Opening Balance Row -->
                        <tr class="row-opening">
                            <td>
                                <?php echo !empty($from_date) ? date('d-m-Y', strtotime($from_date)) : 'Opening'; ?>
                            </td>
                            <?php if (empty($vendor_id)) { ?>
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
                                $running_balance += $row['purchase_amt'];
                                $running_balance -= $row['paid_amt'];
                                ?>
                                <tr>
                                    <td>
                                        <?php echo date('d-m-Y', strtotime($row['tr_date'])); ?>
                                    </td>
                                    <?php if (empty($vendor_id)) { ?>
                                        <td style="font-weight: 600; color: #475569;">
                                            <?php echo htmlspecialchars($row['vendor_name'] ?? ''); ?>
                                        </td>
                                    <?php } ?>
                                    <td style="font-weight: 600; color: #34495e;">
                                        <?php echo htmlspecialchars($row['voucher_no']); ?>
                                    </td>
                                    <td>
                                        <span class="label <?php 
                                            if ($row['type'] == 'purchase') {
                                                echo 'label-success'; 
                                            } else {
                                                echo 'label-danger';
                                            }
                                        ?>" style="font-size: 11px; padding: .2em .6em .3em;"><?php echo htmlspecialchars($row['description']); ?></span>
                                    </td>
                                    <td class="text-right" style="color: #27ae60; font-weight: 500;">
                                        <?php echo ($row['purchase_amt'] > 0) ? number_format($row['purchase_amt'], 3) : '-'; ?>
                                    </td>
                                    <td class="text-right" style="color: #c0392b; font-weight: 500;">
                                        <?php echo ($row['paid_amt'] > 0) ? number_format($row['paid_amt'], 3) : '-'; ?>
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
                                <td colspan="<?php echo empty($vendor_id) ? 7 : 6; ?>" class="text-center" style="padding: 30px; color: #94a3b8; font-style: italic;">
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
                            <td colspan="<?php echo empty($vendor_id) ? 4 : 3; ?>" class="text-right" style="padding: 15px 16px;">Total Period Movement:</td>
                            <td class="text-right" style="color: #27ae60; padding: 15px 16px;"><?php echo number_format($total_purchases_period, 3); ?></td>
                            <td class="text-right" style="color: #c0392b; padding: 15px 16px;"><?php echo number_format($total_paid_period, 3); ?></td>
                            <td class="text-right" style="color: #605ca8; font-size: 15px; padding: 15px 16px;"><?php echo number_format($closing_balance, 3); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <?php } else { ?>
        <div class="alert alert-info text-center" style="border-radius: 12px; margin-top: 20px; background-color: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; padding: 30px 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <i class="fa fa-info-circle" style="font-size: 36px; color: #0ea5e9; margin-bottom: 12px; display: block;"></i>
            <h4 style="font-weight: 600; margin-top: 0; margin-bottom: 8px;">Please Select a Vendor</h4>
            <p style="margin: 0; font-size: 13.5px; color: #475569;">To generate and view the statement report ledger, please select a vendor from the search filter above.</p>
        </div>
    <?php } ?>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
