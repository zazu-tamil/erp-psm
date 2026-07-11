<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header no-print">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Reports</a></li>
        <li class="active"> <?php echo $title ?></li>
    </ol>
</section>

<section class="content">
    <!-- Search Filter -->
    <div class="box box-info no-print">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('invoice-report'); ?>" id="frmsearch">
                <input type="hidden" name="export_excel" id="export_excel" value="0">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="srch_from_date">From Date</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="date" name="srch_from_date" id="srch_from_date" class="form-control"
                                value="<?php echo set_value('srch_from_date', $srch_from_date); ?>" required="true">
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="srch_to_date">To Date</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="date" name="srch_to_date" id="srch_to_date" class="form-control"
                                value="<?php echo set_value('srch_to_date', $srch_to_date); ?>" required="true">
                        </div>
                    </div>

                    <div class="form-group col-md-4 text-left">
                        <br>
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Table -->
    <div class="box box-info">
        <div class="box-header with-border">
            <div class="box-title">
                <h3 class="box-title text-white">Invoice Summary Report
                    [<?php echo date('d-m-Y', strtotime($srch_from_date)) . ' to ' . date('d-m-Y', strtotime($srch_to_date)); ?>]
                </h3>
            </div>
            <div class="box-tools pull-right no-print">
                <button type="button" class="btn btn-primary btn-sm" onclick="window.print();"
                    style="margin-right: 5px;">
                    <i class="fa fa-print"></i> Print PDF
                </button>
                <button type="button" id="btn-export-excel" class="btn btn-success btn-sm" style="margin-right: 5px;">
                    <i class="fa fa-file-excel-o"></i> Export Excel
                </button>
                <button type="button" class="btn btn-box-tool text-white" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <style>
                .summary-report-table th {
                    vertical-align: middle !important;
                    text-align: center;
                    font-weight: 700;
                    border: 1px solid #cbd5e1 !important;
                }
                .summary-report-table td {
                    vertical-align: middle !important;
                    border: 1px solid #cbd5e1 !important;
                    font-size: 12px;
                }
                .summary-report-table .group-start td {
                    border-top: 2.5px solid #64748b !important;
                }
                .summary-report-table td[rowspan] {
                    background-color: #f8fafc !important;
                    font-weight: bold;
                    color: #1e293b;
                    border-right: 2px solid #cbd5e1 !important;
                    text-align: center;
                }
                .col-cust-inv { background-color: #f0fdf4; } /* Soft green */
                .col-supp-inv { background-color: #fffbeb; } /* Soft yellow */
                .col-local-bill { background-color: #f5f3ff; } /* Soft purple */
                .col-dp-bill { background-color: #ecfeff; } /* Soft cyan */
                .col-customs-bill { background-color: #f0f9ff; } /* Soft blue */

                .text-right { text-align: right !important; }
                .text-center { text-align: center !important; }
                .text-left { text-align: left !important; }
            </style>

            <?php
            // Group data by tender_order_id
            $grouped = [];
            $tot_c_inv_amt = 0;
            $tot_s_inv_amt = 0;
            $tot_l_bill_amt = 0;
            $tot_dp_bill_amt = 0;
            $tot_cb_bill_amt = 0;

            if (!empty($invoices)) {
                foreach ($invoices as $row) {
                    $order_id = !empty($row['tender_order_id']) ? $row['tender_order_id'] : 'N/A';
                    if (!isset($grouped[$order_id])) {
                        $grouped[$order_id] = [
                            'tender_order_id' => $row['tender_order_id'],
                            'customer_name' => $row['customer_name'],
                            'customer_invoices' => [],
                            'supplier_invoices' => [],
                            'local_bills' => [],
                            'dp_bills' => [],
                            'customs_bills' => []
                        ];
                    }

                    // Customer Invoices
                    if (!empty($row['customer_invoice_no'])) {
                        $key = trim($row['customer_invoice_no']);
                        $grouped[$order_id]['customer_invoices'][$key] = [
                            'no' => $row['customer_invoice_no'],
                            'date' => $row['customer_invoice_date'],
                            'amount' => $row['customer_invoice_amount']
                        ];
                    }

                    // Supplier Invoices
                    if (!empty($row['supplier_invoice_no'])) {
                        $key = trim($row['supplier_invoice_no']);
                        $grouped[$order_id]['supplier_invoices'][$key] = [
                            'no' => $row['supplier_invoice_no'],
                            'date' => $row['supplier_invoice_date'],
                            'amount' => $row['supplier_invoice_amount']
                        ];
                    }

                    // Local Supplier Bills
                    if (!empty($row['local_bill_no'])) {
                        $key = trim($row['local_bill_no']);
                        $grouped[$order_id]['local_bills'][$key] = [
                            'no' => $row['local_bill_no'],
                            'date' => $row['local_bill_date'],
                            'amount' => $row['local_bill_amount']
                        ];
                    }

                    // Delivery Partner Bills
                    if (!empty($row['dp_bill_no'])) {
                        $key = trim($row['dp_bill_no']);
                        $grouped[$order_id]['dp_bills'][$key] = [
                            'no' => $row['dp_bill_no'],
                            'date' => $row['dp_bill_date'],
                            'amount' => $row['dp_bill_amount']
                        ];
                    }

                    // Customs Bills
                    if (!empty($row['customs_bill_no'])) {
                        $key = trim($row['customs_bill_no']);
                        $grouped[$order_id]['customs_bills'][$key] = [
                            'no' => $row['customs_bill_no'],
                            'date' => $row['customs_bill_date'],
                            'amount' => $row['customs_bill_amount']
                        ];
                    }
                }

                // Sort grouped by the earliest customer invoice date
                uasort($grouped, function($a, $b) {
                    $datesA = array_column($a['customer_invoices'], 'date');
                    $datesB = array_column($b['customer_invoices'], 'date');
                    $datesA = array_filter($datesA, function($d) { return !empty($d) && $d !== '0000-00-00'; });
                    $datesB = array_filter($datesB, function($d) { return !empty($d) && $d !== '0000-00-00'; });
                    $minA = !empty($datesA) ? min(array_map('strtotime', $datesA)) : 0;
                    $minB = !empty($datesB) ? min(array_map('strtotime', $datesB)) : 0;
                    if ($minA == $minB) return 0;
                    return ($minA < $minB) ? -1 : 1;
                });
            }
            ?>

            <div class="table-responsive" style="overflow-x: auto !important; overflow-y: auto !important; max-height: 950px; width: 100%;">
                <table class="table table-bordered summary-report-table" style="min-width: 2200px; margin-bottom: 0;">
                    <thead>
                        <tr style="background-color: #f1f5f9; color: #1e293b;">
                            <th rowspan="2" style="vertical-align: middle;">Tender Order ID</th>
                            <th rowspan="2" style="vertical-align: middle;">Customer Name</th>
                            <th colspan="3" class="col-cust-inv text-center">Customer Invoice</th>
                            <th colspan="3" class="col-supp-inv text-center">Supplier Invoice</th>
                            <th colspan="3" class="col-local-bill text-center">Local Supplier Bill</th>
                            <th colspan="3" class="col-dp-bill text-center">Delivery Partner Bill</th>
                            <th colspan="3" class="col-customs-bill text-center">Customs Bill</th>
                        </tr>
                        <tr style="background-color: #f8fafc;">
                            <!-- Customer Invoice -->
                            <th class="col-cust-inv">Inv Num</th>
                            <th class="col-cust-inv">Inv Date</th>
                            <th class="col-cust-inv text-right">Amount</th>
                            <!-- Supplier Invoice -->
                            <th class="col-supp-inv">Inv Num</th>
                            <th class="col-supp-inv">Inv Date</th>
                            <th class="col-supp-inv text-right">Amount</th>
                            <!-- Local Supplier Bill -->
                            <th class="col-local-bill">Inv Num</th>
                            <th class="col-local-bill">Inv Date</th>
                            <th class="col-local-bill text-right">Amount</th>
                            <!-- Delivery Partner Bill -->
                            <th class="col-dp-bill">Inv Num</th>
                            <th class="col-dp-bill">Inv Date</th>
                            <th class="col-dp-bill text-right">Amount</th>
                            <!-- Customs Bill -->
                            <th class="col-customs-bill">Inv Num</th>
                            <th class="col-customs-bill">Inv Date</th>
                            <th class="col-customs-bill text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($grouped)): ?>
                            <?php foreach ($grouped as $group): ?>
                                <?php
                                $cust_invs = array_values($group['customer_invoices']);
                                usort($cust_invs, function($a, $b) {
                                    $t1 = !empty($a['date']) && $a['date'] !== '0000-00-00' ? strtotime($a['date']) : 0;
                                    $t2 = !empty($b['date']) && $b['date'] !== '0000-00-00' ? strtotime($b['date']) : 0;
                                    return $t1 - $t2;
                                });

                                $supp_invs = array_values($group['supplier_invoices']);
                                usort($supp_invs, function($a, $b) {
                                    $t1 = !empty($a['date']) && $a['date'] !== '0000-00-00' ? strtotime($a['date']) : 0;
                                    $t2 = !empty($b['date']) && $b['date'] !== '0000-00-00' ? strtotime($b['date']) : 0;
                                    return $t1 - $t2;
                                });

                                $local_bills = array_values($group['local_bills']);
                                usort($local_bills, function($a, $b) {
                                    $t1 = !empty($a['date']) && $a['date'] !== '0000-00-00' ? strtotime($a['date']) : 0;
                                    $t2 = !empty($b['date']) && $b['date'] !== '0000-00-00' ? strtotime($b['date']) : 0;
                                    return $t1 - $t2;
                                });

                                $dp_bills = array_values($group['dp_bills']);
                                usort($dp_bills, function($a, $b) {
                                    $t1 = !empty($a['date']) && $a['date'] !== '0000-00-00' ? strtotime($a['date']) : 0;
                                    $t2 = !empty($b['date']) && $b['date'] !== '0000-00-00' ? strtotime($b['date']) : 0;
                                    return $t1 - $t2;
                                });

                                $customs_bills = array_values($group['customs_bills']);
                                usort($customs_bills, function($a, $b) {
                                    $t1 = !empty($a['date']) && $a['date'] !== '0000-00-00' ? strtotime($a['date']) : 0;
                                    $t2 = !empty($b['date']) && $b['date'] !== '0000-00-00' ? strtotime($b['date']) : 0;
                                    return $t1 - $t2;
                                });

                                $max_rows = max(1, count($cust_invs), count($supp_invs), count($local_bills), count($dp_bills), count($customs_bills));
                                ?>
                                <?php for ($i = 0; $i < $max_rows; $i++): ?>
                                    <?php
                                    $is_first = ($i === 0);
                                    $c_inv = isset($cust_invs[$i]) ? $cust_invs[$i] : null;
                                    $s_inv = isset($supp_invs[$i]) ? $supp_invs[$i] : null;
                                    $l_bill = isset($local_bills[$i]) ? $local_bills[$i] : null;
                                    $d_bill = isset($dp_bills[$i]) ? $dp_bills[$i] : null;
                                    $cb_bill = isset($customs_bills[$i]) ? $customs_bills[$i] : null;

                                    if ($c_inv) $tot_c_inv_amt += $c_inv['amount'];
                                    if ($s_inv) $tot_s_inv_amt += $s_inv['amount'];
                                    if ($l_bill) $tot_l_bill_amt += $l_bill['amount'];
                                    if ($d_bill) $tot_dp_bill_amt += $d_bill['amount'];
                                    if ($cb_bill) $tot_cb_bill_amt += $cb_bill['amount'];
                                    ?>
                                    <tr class="<?php echo $is_first ? 'group-start' : ''; ?>">
                                        <?php if ($is_first): ?>
                                            <td rowspan="<?php echo $max_rows; ?>">
                                                <?php echo htmlspecialchars($group['tender_order_id']); ?>
                                            </td>
                                            <td rowspan="<?php echo $max_rows; ?>">
                                                <?php echo htmlspecialchars($group['customer_name']); ?>
                                            </td>
                                        <?php endif; ?>

                                        <!-- Customer Invoice -->
                                        <td class="col-cust-inv"><?php echo $c_inv ? htmlspecialchars($c_inv['no']) : ''; ?></td>
                                        <td class="col-cust-inv"><?php echo ($c_inv && !empty($c_inv['date']) && $c_inv['date'] !== '0000-00-00') ? date('d-m-Y', strtotime($c_inv['date'])) : ''; ?></td>
                                        <td class="col-cust-inv text-right"><?php echo ($c_inv && isset($c_inv['amount'])) ? number_format($c_inv['amount'], 3) : ''; ?></td>

                                        <!-- Supplier Invoice -->
                                        <td class="col-supp-inv"><?php echo $s_inv ? htmlspecialchars($s_inv['no']) : ''; ?></td>
                                        <td class="col-supp-inv"><?php echo ($s_inv && !empty($s_inv['date']) && $s_inv['date'] !== '0000-00-00') ? date('d-m-Y', strtotime($s_inv['date'])) : ''; ?></td>
                                        <td class="col-supp-inv text-right"><?php echo ($s_inv && isset($s_inv['amount'])) ? number_format($s_inv['amount'], 3) : ''; ?></td>

                                        <!-- Local Supplier Bill -->
                                        <td class="col-local-bill"><?php echo $l_bill ? htmlspecialchars($l_bill['no']) : ''; ?></td>
                                        <td class="col-local-bill"><?php echo ($l_bill && !empty($l_bill['date']) && $l_bill['date'] !== '0000-00-00') ? date('d-m-Y', strtotime($l_bill['date'])) : ''; ?></td>
                                        <td class="col-local-bill text-right"><?php echo ($l_bill && isset($l_bill['amount'])) ? number_format($l_bill['amount'], 3) : ''; ?></td>

                                        <!-- Delivery Partner Bill -->
                                        <td class="col-dp-bill"><?php echo $d_bill ? htmlspecialchars($d_bill['no']) : ''; ?></td>
                                        <td class="col-dp-bill"><?php echo ($d_bill && !empty($d_bill['date']) && $d_bill['date'] !== '0000-00-00') ? date('d-m-Y', strtotime($d_bill['date'])) : ''; ?></td>
                                        <td class="col-dp-bill text-right"><?php echo ($d_bill && isset($d_bill['amount'])) ? number_format($d_bill['amount'], 3) : ''; ?></td>

                                        <!-- Customs Bill -->
                                        <td class="col-customs-bill"><?php echo $cb_bill ? htmlspecialchars($cb_bill['no']) : ''; ?></td>
                                        <td class="col-customs-bill"><?php echo ($cb_bill && !empty($cb_bill['date']) && $cb_bill['date'] !== '0000-00-00') ? date('d-m-Y', strtotime($cb_bill['date'])) : ''; ?></td>
                                        <td class="col-customs-bill text-right"><?php echo ($cb_bill && isset($cb_bill['amount'])) ? number_format($cb_bill['amount'], 3) : ''; ?></td>
                                    </tr>
                                <?php endfor; ?>
                            <?php endforeach; ?>

                            <!-- GRAND TOTALS -->
                            <tr style="font-weight:bold; background-color: #334155; color: #ffffff; font-size: 13px;">
                                <td colspan="2" class="text-left">Grand Total:</td>
                                <td colspan="2"></td>
                                <td class="text-right"><?php echo number_format($tot_c_inv_amt, 3); ?></td>
                                <td colspan="2"></td>
                                <td class="text-right"><?php echo number_format($tot_s_inv_amt, 3); ?></td>
                                <td colspan="2"></td>
                                <td class="text-right"><?php echo number_format($tot_l_bill_amt, 3); ?></td>
                                <td colspan="2"></td>
                                <td class="text-right"><?php echo number_format($tot_dp_bill_amt, 3); ?></td>
                                <td colspan="2"></td>
                                <td class="text-right"><?php echo number_format($tot_cb_bill_amt, 3); ?></td>
                            </tr>

                        <?php else: ?>
                            <tr>
                                <td colspan="17" class="text-center text-muted" style="padding: 30px; font-style: italic;">
                                    No invoice records found for the selected date range.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
