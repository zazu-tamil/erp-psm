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
            <td colspan="17" style="font-size: 16pt; font-weight: bold; color: #1e293b; padding-bottom: 5px;">
                INVOICE SUMMARY REPORT
            </td>
        </tr>
        <tr>
            <td colspan="17"
                style="font-size: 11pt; color: #475569; padding-bottom: 15px; border-bottom: 2px solid #cbd5e1;">
                <strong>Period:</strong>
                <?php echo !empty($srch_from_date) ? date('d-M-Y', strtotime($srch_from_date)) : 'Start'; ?> to
                <?php echo !empty($srch_to_date) ? date('d-M-Y', strtotime($srch_to_date)) : 'End'; ?> &nbsp;|&nbsp;
                <strong>Exported At:</strong> <?php echo date('d-m-Y H:i:s'); ?>
            </td>
        </tr>

        <!-- Spacer row -->
        <tr style="height: 15px;">
            <td colspan="17" style="border: none;"></td>
        </tr>

        <thead>
            <tr style="background-color: #f1f5f9; color: #1e293b;">
                <th rowspan="2" style="vertical-align: middle; padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background-color: #f1f5f9;">Tender Order ID</th>
                <th rowspan="2" style="vertical-align: middle; padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background-color: #f1f5f9;">Customer Name</th>
                <th colspan="3" style="text-align: center; padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background-color: #dcfce7; color: #166534;">Customer Invoice</th>
                <th colspan="4" style="text-align: center; padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background-color: #fef3c7; color: #92400e;">Supplier Invoice</th>
                <th colspan="3" style="text-align: center; padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background-color: #ede9fe; color: #5b21b6;">Local Supplier Bill</th>
                <th colspan="3" style="text-align: center; padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background-color: #cffafe; color: #155e75;">Delivery Partner Bill</th>
                <th colspan="3" style="text-align: center; padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background-color: #e0f2fe; color: #075985;">Customs Bill</th>
            </tr>
            <tr style="font-weight:bold; background-color: #f8fafc;">
                <!-- Customer Invoice -->
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #d1fae5;">Inv Num</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #d1fae5;">Inv Date</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #d1fae5;">Amount</th>
                <!-- Supplier Invoice -->
                <th style="padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background-color: #f8fafc;">Supplier Name</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background-color: #f8fafc;">Inv Num</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background-color: #f8fafc;">Inv Date</th>
                <th style="text-align: right; padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background-color: #f8fafc;">Amount</th>
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
            <?php 
            $tot_c_inv_amt = 0;
            $tot_s_inv_amt = 0;
            $tot_l_bill_amt = 0;
            $tot_dp_bill_amt = 0;
            $tot_cb_bill_amt = 0;

            if (!empty($invoices)) {
                $grouped = [];
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
                            'name' => $row['vendor_name'],
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

                // Create display blocks to separate multiple customer invoices for the same order
                $display_blocks = [];
                foreach ($grouped as $order_id => $group) {
                    $cust_invs = array_values($group['customer_invoices']);
                    if (empty($cust_invs)) {
                        $display_blocks[] = $group;
                    } else {
                        // Sort customer invoices to ensure deterministic order for assigning supplier bills
                        usort($cust_invs, function($a, $b) {
                            $cmp = strnatcmp($a['no'], $b['no']);
                            if ($cmp === 0) {
                                $t1 = !empty($a['date']) && $a['date'] !== '0000-00-00' ? strtotime($a['date']) : 0;
                                $t2 = !empty($b['date']) && $b['date'] !== '0000-00-00' ? strtotime($b['date']) : 0;
                                return $t1 - $t2;
                            }
                            return $cmp;
                        });

                        foreach ($cust_invs as $idx => $c_inv) {
                            $block = [
                                'tender_order_id' => $group['tender_order_id'],
                                'customer_name' => $group['customer_name'],
                                'customer_invoices' => [$c_inv['no'] => $c_inv],
                                'supplier_invoices' => ($idx === 0) ? $group['supplier_invoices'] : [],
                                'local_bills' => ($idx === 0) ? $group['local_bills'] : [],
                                'dp_bills' => ($idx === 0) ? $group['dp_bills'] : [],
                                'customs_bills' => ($idx === 0) ? $group['customs_bills'] : []
                            ];
                            $display_blocks[] = $block;
                        }
                    }
                }

                // Sort display blocks by Customer Invoice Date, then Number
                uasort($display_blocks, function($a, $b) {
                    $cA = reset($a['customer_invoices']);
                    $cB = reset($b['customer_invoices']);
                    
                    $t1 = 0;
                    if ($cA && !empty($cA['date']) && $cA['date'] !== '0000-00-00') $t1 = strtotime($cA['date']);
                    else {
                        $sA = reset($a['supplier_invoices']);
                        if ($sA && !empty($sA['date']) && $sA['date'] !== '0000-00-00') $t1 = strtotime($sA['date']);
                    }

                    $t2 = 0;
                    if ($cB && !empty($cB['date']) && $cB['date'] !== '0000-00-00') $t2 = strtotime($cB['date']);
                    else {
                        $sB = reset($b['supplier_invoices']);
                        if ($sB && !empty($sB['date']) && $sB['date'] !== '0000-00-00') $t2 = strtotime($sB['date']);
                    }
                    
                    if ($t1 == $t2) {
                        $no1 = $cA ? $cA['no'] : '';
                        $no2 = $cB ? $cB['no'] : '';
                        return strnatcmp($no1, $no2);
                    }
                    return $t1 - $t2;
                });

                $row_idx = 0;
                foreach ($display_blocks as $group) {
                    $cust_invs = array_values($group['customer_invoices']);
                    usort($cust_invs, function($a, $b) {
                        $cmp = strnatcmp($a['no'], $b['no']);
                        if ($cmp === 0) {
                            $t1 = !empty($a['date']) && $a['date'] !== '0000-00-00' ? strtotime($a['date']) : 0;
                            $t2 = !empty($b['date']) && $b['date'] !== '0000-00-00' ? strtotime($b['date']) : 0;
                            return $t1 - $t2;
                        }
                        return $cmp;
                    });

                    $supp_invs = array_values($group['supplier_invoices']);
                    usort($supp_invs, function($a, $b) {
                        $cmp = strnatcmp($a['no'], $b['no']);
                        if ($cmp === 0) {
                            $t1 = !empty($a['date']) && $a['date'] !== '0000-00-00' ? strtotime($a['date']) : 0;
                            $t2 = !empty($b['date']) && $b['date'] !== '0000-00-00' ? strtotime($b['date']) : 0;
                            return $t1 - $t2;
                        }
                        return $cmp;
                    });

                    $local_bills = array_values($group['local_bills']);
                    usort($local_bills, function($a, $b) {
                        $cmp = strnatcmp($a['no'], $b['no']);
                        if ($cmp === 0) {
                            $t1 = !empty($a['date']) && $a['date'] !== '0000-00-00' ? strtotime($a['date']) : 0;
                            $t2 = !empty($b['date']) && $b['date'] !== '0000-00-00' ? strtotime($b['date']) : 0;
                            return $t1 - $t2;
                        }
                        return $cmp;
                    });

                    $dp_bills = array_values($group['dp_bills']);
                    usort($dp_bills, function($a, $b) {
                        $cmp = strnatcmp($a['no'], $b['no']);
                        if ($cmp === 0) {
                            $t1 = !empty($a['date']) && $a['date'] !== '0000-00-00' ? strtotime($a['date']) : 0;
                            $t2 = !empty($b['date']) && $b['date'] !== '0000-00-00' ? strtotime($b['date']) : 0;
                            return $t1 - $t2;
                        }
                        return $cmp;
                    });

                    $customs_bills = array_values($group['customs_bills']);
                    usort($customs_bills, function($a, $b) {
                        $cmp = strnatcmp($a['no'], $b['no']);
                        if ($cmp === 0) {
                            $t1 = !empty($a['date']) && $a['date'] !== '0000-00-00' ? strtotime($a['date']) : 0;
                            $t2 = !empty($b['date']) && $b['date'] !== '0000-00-00' ? strtotime($b['date']) : 0;
                            return $t1 - $t2;
                        }
                        return $cmp;
                    });

                    $max_rows = max(1, count($cust_invs), count($supp_invs), count($local_bills), count($dp_bills), count($customs_bills));

                    for ($i = 0; $i < $max_rows; $i++) {
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

                            <!-- Customer Invoice -->
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #f0fdf4;"><?php echo $c_inv ? htmlspecialchars($c_inv['no']) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #f0fdf4;"><?php echo ($c_inv && !empty($c_inv['date']) && $c_inv['date'] !== '0000-00-00') ? date('d-m-Y', strtotime($c_inv['date'])) : ''; ?></td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #f0fdf4; mso-number-format:'#,##0.000';"><?php echo ($c_inv && isset($c_inv['amount'])) ? number_format($c_inv['amount'], 3) : ''; ?></td>

                            <!-- Supplier Invoice -->
                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: #fffbeb;"><?php echo $s_inv ? htmlspecialchars($s_inv['name']) : ''; ?></td>
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
                ?>
                <!-- GRAND TOTALS -->
                <tr style="font-weight:bold; background-color: #e2e8f0; color: #0f172a;">
                    <td colspan="2" style="padding: 8px; border: 1px solid #cbd5e1; text-align: left;">Grand Total:</td>
                    <td colspan="2" style="border: 1px solid #cbd5e1;"></td>
                    <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; mso-number-format:'#,##0.000';"><?php echo number_format($tot_c_inv_amt, 3); ?></td>
                    <td colspan="3" style="border: 1px solid #cbd5e1;"></td>
                    <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; mso-number-format:'#,##0.000';"><?php echo number_format($tot_s_inv_amt, 3); ?></td>
                    <td colspan="2" style="border: 1px solid #cbd5e1;"></td>
                    <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; mso-number-format:'#,##0.000';"><?php echo number_format($tot_l_bill_amt, 3); ?></td>
                    <td colspan="2" style="border: 1px solid #cbd5e1;"></td>
                    <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; mso-number-format:'#,##0.000';"><?php echo number_format($tot_dp_bill_amt, 3); ?></td>
                    <td colspan="2" style="border: 1px solid #cbd5e1;"></td>
                    <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; mso-number-format:'#,##0.000';"><?php echo number_format($tot_cb_bill_amt, 3); ?></td>
                </tr>
            <?php } else { ?>
                <tr>
                    <td colspan="18"
                        style="padding: 20px; border: 1px solid #cbd5e1; text-align: center; color: #64748b; font-style: italic;">
                        No invoice records found for the selected date range.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>

</html>
