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
            <form method="post" action="<?php echo site_url('supplier-summary-report'); ?>" id="frmsearch">
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
                <h3 class="box-title text-white">Supplier Summary Report
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

        <div class="box-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr style="font-weight:bold; background-color: #f4f4f4;">
                        <th>Order</th>
                        <th>Customer</th>
                        <th>PO Date</th>
                        <th class="text-right">PO Amt</th>
                        <th>Customer Inv</th>
                        <th>Customer Inv Date</th>
                        <th class="text-right">Customer Inv Amt</th>
                        <th>Vendor</th>
                        <th>Vendor Inv Date</th>
                        <th>Vendor Inv No</th>
                        <th class="text-right">Vendor Amt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($suppliers)) {
                        // Group flat records by order_id to eliminate duplicates and group properly by Order
                        $grouped_suppliers = [];
                        foreach ($suppliers as $row) {
                            $group_key = $row['order_id'];
                            if (!isset($grouped_suppliers[$group_key])) {
                                $grouped_suppliers[$group_key] = [
                                    'order_id' => $row['order_id'],
                                    'pos' => [],
                                    'customer_invoices' => [],
                                    'vendor_invoices' => []
                                ];
                            }

                            // Add PO record if not already added
                            $po_key = $row['tender_po_id'];
                            if (!isset($grouped_suppliers[$group_key]['pos'][$po_key])) {
                                $grouped_suppliers[$group_key]['pos'][$po_key] = [
                                    'po_date' => $row['po_date'],
                                    'customer' => $row['customer'],
                                    'po_amt' => $row['po_amt']
                                ];
                            }

                            // Add customer invoice if present and not already added
                            if (!empty($row['invoice_no'])) {
                                $cust_inv_key = trim($row['invoice_no']);
                                $grouped_suppliers[$group_key]['customer_invoices'][$cust_inv_key] = [
                                    'invoice_no' => trim($row['invoice_no']),
                                    'invoice_date' => $row['invoice_date'],
                                    'customer_invoice_amt' => $row['customer_invoice_amt']
                                ];
                            }

                            // Add vendor invoice if present and not already added
                            if (!empty($row['vendor_invoice_no'])) {
                                $vend_inv_key = trim($row['vendor_name']) . '_' . trim($row['vendor_invoice_no']);
                                $grouped_suppliers[$group_key]['vendor_invoices'][$vend_inv_key] = [
                                    'vendor_name' => $row['vendor_name'],
                                    'vendor_invoice_date' => $row['vendor_invoice_date'],
                                    'vendor_invoice_no' => $row['vendor_invoice_no'],
                                    'vendor_invoice_amt' => $row['vendor_invoice_amt']
                                ];
                            }
                        }

                        // Render grouped data side-by-side
                        foreach ($grouped_suppliers as $group_key => $group) {
                            $pos = array_values($group['pos']);
                            $cust_invs = array_values($group['customer_invoices']);
                            $vend_invs = array_values($group['vendor_invoices']);
                            $max_rows = max(1, count($pos), count($cust_invs), count($vend_invs));

                            for ($i = 0; $i < $max_rows; $i++) {
                                $is_first_row = ($i === 0);
                                $po = isset($pos[$i]) ? $pos[$i] : null;
                                $cust_inv = isset($cust_invs[$i]) ? $cust_invs[$i] : null;
                                $vend = isset($vend_invs[$i]) ? $vend_invs[$i] : null;
                                ?>
                                <tr>
                                    <!-- Order/PO Details printed only on the first row of each Order group -->
                                    <td><?php echo $is_first_row ? htmlspecialchars($group['order_id']) : ''; ?></td>
                                    
                                    <!-- PO columns: printed row by row based on distinct PO records -->
                                    <td><?php echo ($po && !empty($po['po_date'])) ? date('d-m-Y', strtotime($po['po_date'])) : ''; ?></td>
                                    <td><?php echo $po ? htmlspecialchars($po['customer']) : ''; ?></td>
                                    <td class="text-right">
                                        <?php echo ($po && isset($po['po_amt'])) ? number_format($po['po_amt'], 3) : ''; ?>
                                    </td>

                                    <!-- Customer Invoice Columns -->
                                    <td><?php echo $cust_inv ? htmlspecialchars($cust_inv['invoice_no']) : ''; ?></td>
                                    <td><?php echo ($cust_inv && !empty($cust_inv['invoice_date'])) ? date('d-m-Y', strtotime($cust_inv['invoice_date'])) : ''; ?></td>
                                    <td class="text-right">
                                        <?php echo ($cust_inv && isset($cust_inv['customer_invoice_amt'])) ? number_format($cust_inv['customer_invoice_amt'], 3) : ''; ?>
                                    </td>

                                    <!-- Vendor Invoice Columns -->
                                    <td><?php echo $vend ? htmlspecialchars($vend['vendor_name']) : ''; ?></td>
                                    <td><?php echo ($vend && !empty($vend['vendor_invoice_date'])) ? date('d-m-Y', strtotime($vend['vendor_invoice_date'])) : ''; ?></td>
                                    <td><?php echo $vend ? htmlspecialchars($vend['vendor_invoice_no']) : ''; ?></td>
                                    <td class="text-right">
                                        <?php echo ($vend && isset($vend['vendor_invoice_amt'])) ? number_format($vend['vendor_invoice_amt'], 3) : ''; ?>
                                    </td>
                                </tr>
                            <?php
                            }
                        }
                    } else { ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">No records found for the selected date range.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include_once(VIEWPATH . 'inc/footer.php'); ?>