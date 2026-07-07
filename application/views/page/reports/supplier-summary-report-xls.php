<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #334155;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }
    </style>
</head>

<body>

    <table style="width: 100%; border-collapse: collapse; font-family: Arial, Helvetica, sans-serif;">
        <!-- Title Header -->
        <tr>
            <td colspan="23" style="font-size: 16pt; font-weight: bold; color: #1e293b; padding-bottom: 5px;">
                SUPPLIER SUMMARY REPORT
            </td>
        </tr>
        <tr>
            <td colspan="23"
                style="font-size: 11pt; color: #475569; padding-bottom: 15px; border-bottom: 2px solid #cbd5e1;">
                <strong>Period:</strong>
                <?php echo !empty($srch_from_date) ? date('d-M-Y', strtotime($srch_from_date)) : 'Start'; ?> to
                <?php echo !empty($srch_to_date) ? date('d-M-Y', strtotime($srch_to_date)) : 'End'; ?> &nbsp;|&nbsp;
                <strong>Exported At:</strong> <?php echo date('d-m-Y H:i:s'); ?>
            </td>
        </tr>

        <!-- Spacer row -->
        <tr style="height: 15px;">
            <td colspan="23" style="border: none;"></td>
        </tr>

        <thead>
            <tr style="font-weight:bold; background-color: #f1f5f9;">
                <th rowspan="2" style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #f1f5f9; vertical-align: middle;">Tender Order ID</th>
                <th rowspan="2" style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #f1f5f9; vertical-align: middle;">Customer Name</th>
                
                <th colspan="3" style="padding: 8px; border: 1px solid #cbd5e1; text-align: center; background-color: #e2e8f0; vertical-align: middle;">Customer PO</th>
                <th colspan="3" style="padding: 8px; border: 1px solid #cbd5e1; text-align: center; background-color: #d1fae5; vertical-align: middle;">Customer Invoice</th>
                <th colspan="3" style="padding: 8px; border: 1px solid #cbd5e1; text-align: center; background-color: #fee2e2; vertical-align: middle;">Supplier PO</th>
                <th colspan="3" style="padding: 8px; border: 1px solid #cbd5e1; text-align: center; background-color: #fef3c7; vertical-align: middle;">Supplier Invoice</th>
                <th colspan="3" style="padding: 8px; border: 1px solid #cbd5e1; text-align: center; background-color: #ede9fe; vertical-align: middle;">Local Supplier Bill</th>
                <th colspan="3" style="padding: 8px; border: 1px solid #cbd5e1; text-align: center; background-color: #cffafe; vertical-align: middle;">Delivery Partner Bill</th>
                <th colspan="3" style="padding: 8px; border: 1px solid #cbd5e1; text-align: center; background-color: #e0f2fe; vertical-align: middle;">Customs Bill</th>
            </tr>
            <tr style="font-weight:bold; background-color: #f8fafc;">
                <!-- Customer PO -->
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #e2e8f0;">PO Num</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #e2e8f0;">PO Date</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #e2e8f0;">Amount</th>
                <!-- Customer Invoice -->
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #d1fae5;">Inv Num</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #d1fae5;">Inv Date</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #d1fae5;">Amount</th>
                <!-- Supplier PO -->
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #fee2e2;">PO Num</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #fee2e2;">PO Date</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #fee2e2;">Amount</th>
                <!-- Supplier Invoice -->
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #fef3c7;">Inv Num</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #fef3c7;">Inv Date</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #fef3c7;">Amount</th>
                <!-- Local Supplier Bill -->
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #ede9fe;">Inv Num</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #ede9fe;">Inv Date</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #ede9fe;">Amount</th>
                <!-- Delivery Partner Bill -->
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #cffafe;">Inv Num</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #cffafe;">Inv Date</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #cffafe;">Amount</th>
                <!-- Customs Bill -->
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #e0f2fe;">Inv Num</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #e0f2fe;">Inv Date</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #e0f2fe;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($suppliers)) {
                $grouped = [];
                foreach ($suppliers as $row) {
                    $order_id = !empty($row['tender_order_id']) ? $row['tender_order_id'] : 'N/A';
                    if (!isset($grouped[$order_id])) {
                        $grouped[$order_id] = [
                            'tender_order_id' => $row['tender_order_id'],
                            'customer_name' => $row['customer_name'],
                            'customer_pos' => [],
                            'customer_invoices' => [],
                            'supplier_pos' => [],
                            'supplier_invoices' => [],
                            'local_bills' => [],
                            'dp_bills' => [],
                            'customs_bills' => []
                        ];
                    }

                    // Customer POs
                    if (!empty($row['customer_po_no'])) {
                        $key = trim($row['customer_po_no']);
                        $grouped[$order_id]['customer_pos'][$key] = [
                            'no' => $row['customer_po_no'],
                            'date' => $row['po_date'],
                            'amount' => $row['customer_po_amount']
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

                    // Supplier POs
                    if (!empty($row['supplier_po_no'])) {
                        $key = trim($row['supplier_po_no']);
                        $grouped[$order_id]['supplier_pos'][$key] = [
                            'no' => $row['supplier_po_no'],
                            'date' => $row['supplier_po_date'],
                            'amount' => $row['supplier_po_amount']
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

                // Sort grouped by the earliest customer PO date
                uasort($grouped, function($a, $b) {
                    $datesA = array_column($a['customer_pos'], 'date');
                    $datesB = array_column($b['customer_pos'], 'date');
                    $datesA = array_filter($datesA, function($d) { return !empty($d) && $d !== '0000-00-00'; });
                    $datesB = array_filter($datesB, function($d) { return !empty($d) && $d !== '0000-00-00'; });
                    $minA = !empty($datesA) ? min(array_map('strtotime', $datesA)) : 0;
                    $minB = !empty($datesB) ? min(array_map('strtotime', $datesB)) : 0;
                    if ($minA == $minB) return 0;
                    return ($minA < $minB) ? -1 : 1;
                });

                $row_idx = 0;
                foreach ($grouped as $group) {
                    $cust_pos = array_values($group['customer_pos']);
                    usort($cust_pos, function($a, $b) {
                        $t1 = !empty($a['date']) && $a['date'] !== '0000-00-00' ? strtotime($a['date']) : 0;
                        $t2 = !empty($b['date']) && $b['date'] !== '0000-00-00' ? strtotime($b['date']) : 0;
                        return $t1 - $t2;
                    });

                    $cust_invs = array_values($group['customer_invoices']);
                    usort($cust_invs, function($a, $b) {
                        $t1 = !empty($a['date']) && $a['date'] !== '0000-00-00' ? strtotime($a['date']) : 0;
                        $t2 = !empty($b['date']) && $b['date'] !== '0000-00-00' ? strtotime($b['date']) : 0;
                        return $t1 - $t2;
                    });

                    $supp_pos = array_values($group['supplier_pos']);
                    usort($supp_pos, function($a, $b) {
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

                    $max_rows = max(1, count($cust_pos), count($cust_invs), count($supp_pos), count($supp_invs), count($local_bills), count($dp_bills), count($customs_bills));

                    for ($i = 0; $i < $max_rows; $i++) {
                        $is_first = ($i === 0);
                        $c_po = isset($cust_pos[$i]) ? $cust_pos[$i] : null;
                        $c_inv = isset($cust_invs[$i]) ? $cust_invs[$i] : null;
                        $s_po = isset($supp_pos[$i]) ? $supp_pos[$i] : null;
                        $s_inv = isset($supp_invs[$i]) ? $supp_invs[$i] : null;
                        $l_bill = isset($local_bills[$i]) ? $local_bills[$i] : null;
                        $d_bill = isset($dp_bills[$i]) ? $dp_bills[$i] : null;
                        $cb_bill = isset($customs_bills[$i]) ? $customs_bills[$i] : null;

                        $bg_color = ($row_idx % 2 === 0) ? '#ffffff' : '#f8fafc';
                        $row_idx++;
                        ?>
                        <tr style="background-color: <?php echo $bg_color; ?>;">
                            <?php if ($is_first): ?>
                                <td rowspan="<?php echo $max_rows; ?>" style="padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background-color: #f8fafc; vertical-align: middle;">
                                    <?php echo htmlspecialchars($group['tender_order_id']); ?>
                                </td>
                                <td rowspan="<?php echo $max_rows; ?>" style="padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background-color: #f8fafc; vertical-align: middle;">
                                    <?php echo htmlspecialchars($group['customer_name']); ?>
                                </td>
                            <?php endif; ?>

                            <!-- Customer PO -->
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #f8fafc;"><?php echo $c_po ? htmlspecialchars($c_po['no']) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #f8fafc;"><?php echo ($c_po && !empty($c_po['date']) && $c_po['date'] !== '0000-00-00') ? date('d-m-Y', strtotime($c_po['date'])) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #f8fafc; mso-number-format:'#,##0.000';"><?php echo ($c_po && isset($c_po['amount'])) ? number_format($c_po['amount'], 3) : ''; ?></td>

                            <!-- Customer Invoice -->
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #f0fdf4;"><?php echo $c_inv ? htmlspecialchars($c_inv['no']) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #f0fdf4;"><?php echo ($c_inv && !empty($c_inv['date']) && $c_inv['date'] !== '0000-00-00') ? date('d-m-Y', strtotime($c_inv['date'])) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #f0fdf4; mso-number-format:'#,##0.000';"><?php echo ($c_inv && isset($c_inv['amount'])) ? number_format($c_inv['amount'], 3) : ''; ?></td>

                            <!-- Supplier PO -->
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #fef2f2;"><?php echo $s_po ? htmlspecialchars($s_po['no']) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #fef2f2;"><?php echo ($s_po && !empty($s_po['date']) && $s_po['date'] !== '0000-00-00') ? date('d-m-Y', strtotime($s_po['date'])) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #fef2f2; mso-number-format:'#,##0.000';"><?php echo ($s_po && isset($s_po['amount'])) ? number_format($s_po['amount'], 3) : ''; ?></td>

                            <!-- Supplier Invoice -->
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #fffbeb;"><?php echo $s_inv ? htmlspecialchars($s_inv['no']) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #fffbeb;"><?php echo ($s_inv && !empty($s_inv['date']) && $s_inv['date'] !== '0000-00-00') ? date('d-m-Y', strtotime($s_inv['date'])) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #fffbeb; mso-number-format:'#,##0.000';"><?php echo ($s_inv && isset($s_inv['amount'])) ? number_format($s_inv['amount'], 3) : ''; ?></td>

                            <!-- Local Supplier Bill -->
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #f5f3ff;"><?php echo $l_bill ? htmlspecialchars($l_bill['no']) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #f5f3ff;"><?php echo ($l_bill && !empty($l_bill['date']) && $l_bill['date'] !== '0000-00-00') ? date('d-m-Y', strtotime($l_bill['date'])) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #f5f3ff; mso-number-format:'#,##0.000';"><?php echo ($l_bill && isset($l_bill['amount'])) ? number_format($l_bill['amount'], 3) : ''; ?></td>

                            <!-- Delivery Partner Bill -->
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #ecfeff;"><?php echo $d_bill ? htmlspecialchars($d_bill['no']) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #ecfeff;"><?php echo ($d_bill && !empty($d_bill['date']) && $d_bill['date'] !== '0000-00-00') ? date('d-m-Y', strtotime($d_bill['date'])) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #ecfeff; mso-number-format:'#,##0.000';"><?php echo ($d_bill && isset($d_bill['amount'])) ? number_format($d_bill['amount'], 3) : ''; ?></td>

                            <!-- Customs Bill -->
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #f0f9ff;"><?php echo $cb_bill ? htmlspecialchars($cb_bill['no']) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #f0f9ff;"><?php echo ($cb_bill && !empty($cb_bill['date']) && $cb_bill['date'] !== '0000-00-00') ? date('d-m-Y', strtotime($cb_bill['date'])) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #f0f9ff; mso-number-format:'#,##0.000';"><?php echo ($cb_bill && isset($cb_bill['amount'])) ? number_format($cb_bill['amount'], 3) : ''; ?></td>
                        </tr>
                        <?php
                    }
                }
            } else { ?>
                <tr>
                    <td colspan="23"
                        style="padding: 20px; border: 1px solid #cbd5e1; text-align: center; color: #64748b; font-style: italic;">
                        No records found for the selected date range.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>

</html>