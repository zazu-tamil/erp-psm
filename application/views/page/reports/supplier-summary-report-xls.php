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
            <td colspan="9" style="font-size: 16pt; font-weight: bold; color: #1e293b; padding-bottom: 5px;">
                SUPPLIER SUMMARY REPORT
            </td>
        </tr>
        <tr>
            <td colspan="9"
                style="font-size: 11pt; color: #475569; padding-bottom: 15px; border-bottom: 2px solid #cbd5e1;">
                <strong>Period:</strong>
                <?php echo !empty($srch_from_date) ? date('d-M-Y', strtotime($srch_from_date)) : 'Start'; ?> to
                <?php echo !empty($srch_to_date) ? date('d-M-Y', strtotime($srch_to_date)) : 'End'; ?> &nbsp;|&nbsp;
                <strong>Exported At:</strong> <?php echo date('d-m-Y H:i:s'); ?>
            </td>
        </tr>

        <!-- Spacer row -->
        <tr style="height: 15px;">
            <td colspan="11" style="border: none;"></td>
        </tr>

        <thead>
            <tr style="font-weight:bold; background-color: #f1f5f9;">
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #f1f5f9;">Order
                </th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #f1f5f9;">
                    Customer</th>

                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #f1f5f9;">PO
                    Date</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #f1f5f9;">PO
                    Amt</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #f1f5f9;">
                    Customer Inv</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #f1f5f9;">
                    Customer Inv Date</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #f1f5f9;">
                    Customer Inv Amt</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #f1f5f9;">Vendor
                </th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #f1f5f9;">Vendor
                    Inv Date</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: left; background-color: #f1f5f9;">Vendor
                    Inv No</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #f1f5f9;">
                    Vendor Amt</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($suppliers)) {
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

                    $po_key = $row['tender_po_id'];
                    if (!isset($grouped_suppliers[$group_key]['pos'][$po_key])) {
                        $grouped_suppliers[$group_key]['pos'][$po_key] = [
                            'po_date' => $row['po_date'],
                            'customer' => $row['customer'],
                            'po_amt' => $row['po_amt']
                        ];
                    }

                    if (!empty($row['invoice_no'])) {
                        $cust_inv_key = trim($row['invoice_no']);
                        $grouped_suppliers[$group_key]['customer_invoices'][$cust_inv_key] = [
                            'invoice_no' => trim($row['invoice_no']),
                            'invoice_date' => $row['invoice_date'],
                            'customer_invoice_amt' => $row['customer_invoice_amt']
                        ];
                    }

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

                $row_idx = 0;
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
                        $bg_color = ($row_idx % 2 === 0) ? '#ffffff' : '#f8fafc';
                        $row_idx++;
                        ?>
                        <tr style="background-color: <?php echo $bg_color; ?>;">
                            <td style="padding: 8px; border: 1px solid #cbd5e1;">
                                <?php echo $is_first_row ? htmlspecialchars($group['order_id']) : ''; ?>
                            </td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1;">
                                <?php echo ($po && !empty($po['po_date'])) ? date('d-m-Y', strtotime($po['po_date'])) : ''; ?>
                            </td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1;">
                                <?php echo $po ? htmlspecialchars($po['customer']) : ''; ?>
                            </td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; mso-number-format:'#,##0.000';">
                                <?php echo ($po && isset($po['po_amt'])) ? number_format($po['po_amt'], 3) : ''; ?>
                            </td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1;">
                                <?php echo $cust_inv ? htmlspecialchars($cust_inv['invoice_no']) : ''; ?>
                            </td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1;">
                                <?php echo ($cust_inv && !empty($cust_inv['invoice_date'])) ? date('d-m-Y', strtotime($cust_inv['invoice_date'])) : ''; ?>
                            </td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; mso-number-format:'#,##0.000';">
                                <?php echo ($cust_inv && isset($cust_inv['customer_invoice_amt'])) ? number_format($cust_inv['customer_invoice_amt'], 3) : ''; ?>
                            </td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1;">
                                <?php echo $vend ? htmlspecialchars($vend['vendor_name']) : ''; ?>
                            </td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1;">
                                <?php echo ($vend && !empty($vend['vendor_invoice_date'])) ? date('d-m-Y', strtotime($vend['vendor_invoice_date'])) : ''; ?>
                            </td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1;">
                                <?php echo $vend ? htmlspecialchars($vend['vendor_invoice_no']) : ''; ?>
                            </td>
                            <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; mso-number-format:'#,##0.000';">
                                <?php echo ($vend && isset($vend['vendor_invoice_amt'])) ? number_format($vend['vendor_invoice_amt'], 3) : ''; ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
            } else { ?>
                <tr>
                    <td colspan="11"
                        style="padding: 20px; border: 1px solid #cbd5e1; text-align: center; color: #64748b; font-style: italic;">
                        No records found for the selected date range.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>

</html>