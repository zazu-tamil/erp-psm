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
            font-size: 10pt;
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
        <!-- Header -->
        <tr>
            <td colspan="9" style="font-size: 16pt; font-weight: bold; color: #1e293b; padding-bottom: 5px;">
                CUSTOMER BALANCE REPORT
            </td>
        </tr>
        <tr>
            <td colspan="9" style="font-size: 10pt; color: #475569; padding-bottom: 12px; border-bottom: 2px solid #cbd5e1;">
                <strong>As On Date:</strong> <?php echo !empty($as_on_date) ? date('d-M-Y', strtotime($as_on_date)) : date('d-M-Y'); ?> &nbsp;|&nbsp;
                <strong>Customer:</strong> <?php echo htmlspecialchars($selected_customer_name); ?> &nbsp;|&nbsp;
                <strong>Generated On:</strong> <?php echo date('d-m-Y H:i:s'); ?>
            </td>
        </tr>

        <!-- Spacer -->
        <tr style="height: 12px;">
            <td colspan="9" style="border: none;"></td>
        </tr>

        <!-- KPI Summary Cards -->
        <tr>
            <td colspan="2" style="background-color: #fef3c7; border: 1px solid #fde68a; padding: 8px; text-align: center; font-weight: bold; color: #92400e;">
                TOTAL OPENING
            </td>
            <td colspan="2" style="background-color: #e0f2fe; border: 1px solid #bae6fd; padding: 8px; text-align: center; font-weight: bold; color: #0369a1;">
                TOTAL INVOICES
            </td>
            <td style="background-color: #ede9fe; border: 1px solid #ddd6fe; padding: 8px; text-align: center; font-weight: bold; color: #5b21b6;">
                ADVANCE RECEIVED
            </td>
            <td style="background-color: #dcfce7; border: 1px solid #bbf7d0; padding: 8px; text-align: center; font-weight: bold; color: #166534;">
                INVOICE RECEIPTS
            </td>
            <td style="background-color: #dcfce7; border: 1px solid #bbf7d0; padding: 8px; text-align: center; font-weight: bold; color: #166534;">
                TOTAL RECEIVED
            </td>
            <td colspan="2" style="background-color: #fee2e2; border: 1px solid #fecaca; padding: 8px; text-align: center; font-weight: bold; color: #991b1b;">
                NET OUTSTANDING
            </td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: #fffbeb; border: 1px solid #fde68a; padding: 10px; text-align: center; font-size: 13pt; font-weight: bold; color: #b45309;">
                <?php echo number_format($summary['opening_balance'], 3); ?>
            </td>
            <td colspan="2" style="background-color: #f0f9ff; border: 1px solid #bae6fd; padding: 10px; text-align: center; font-size: 13pt; font-weight: bold; color: #0284c7;">
                <?php echo number_format($summary['total_invoices'], 3); ?>
            </td>
            <td style="background-color: #f5f3ff; border: 1px solid #ddd6fe; padding: 10px; text-align: center; font-size: 13pt; font-weight: bold; color: #7c3aed;">
                <?php echo number_format($summary['advance_received'], 3); ?>
            </td>
            <td style="background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 10px; text-align: center; font-size: 13pt; font-weight: bold; color: #15803d;">
                <?php echo number_format($summary['invoice_receipts'], 3); ?>
            </td>
            <td style="background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 10px; text-align: center; font-size: 13pt; font-weight: bold; color: #15803d;">
                <?php echo number_format($summary['total_received'], 3); ?>
            </td>
            <td colspan="2" style="background-color: #fff1f2; border: 1px solid #fecaca; padding: 10px; text-align: center; font-size: 13pt; font-weight: bold; color: #dc2626;">
                <?php echo number_format($summary['closing_balance'], 3); ?>
            </td>
        </tr>

        <!-- Spacer -->
        <tr style="height: 15px;">
            <td colspan="9" style="border: none;"></td>
        </tr>

        <!-- Table Columns -->
        <thead>
            <tr style="background-color: #1e293b; color: #ffffff;">
                <th style="padding: 8px; border: 1px solid #334155; text-align: center; width: 40px;">S.No</th>
                <th style="padding: 8px; border: 1px solid #334155; text-align: left; width: 220px;">Customer Name</th>
                <th style="padding: 8px; border: 1px solid #334155; text-align: right; width: 110px;">Opening Bal</th>
                <th style="padding: 8px; border: 1px solid #334155; text-align: right; width: 110px;">Total Invoices</th>
                <th style="padding: 8px; border: 1px solid #334155; text-align: right; width: 110px;">Advance Received</th>
                <th style="padding: 8px; border: 1px solid #334155; text-align: right; width: 110px;">Invoice Receipts</th>
                <th style="padding: 8px; border: 1px solid #334155; text-align: right; width: 110px;">Total Received</th>
                <th style="padding: 8px; border: 1px solid #334155; text-align: right; width: 120px;">Closing Balance</th>
                <th style="padding: 8px; border: 1px solid #334155; text-align: center; width: 90px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($record_list)): ?>
                <?php foreach ($record_list as $i => $row): ?>
                    <tr style="<?php echo ($i % 2 == 1) ? 'background-color: #f8fafc;' : 'background-color: #ffffff;'; ?>">
                        <td style="padding: 7px; border: 1px solid #cbd5e1; text-align: center;">
                            <?php echo ($i + 1); ?>
                        </td>
                        <td style="padding: 7px; border: 1px solid #cbd5e1;">
                            <strong><?php echo htmlspecialchars($row['customer_name']); ?></strong>
                            <?php if (!empty($row['crno'])): ?>
                                <br><span style="color: #64748b; font-size: 8pt;">CR: <?php echo htmlspecialchars($row['crno']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 7px; border: 1px solid #cbd5e1; text-align: right;">
                            <?php echo number_format((float)$row['opening_balance'], 3); ?>
                        </td>
                        <td style="padding: 7px; border: 1px solid #cbd5e1; text-align: right;">
                            <?php echo number_format((float)$row['total_invoices'], 3); ?>
                        </td>
                        <td style="padding: 7px; border: 1px solid #cbd5e1; text-align: right; color: #2563eb;">
                            <?php echo number_format((float)$row['advance_received'], 3); ?>
                        </td>
                        <td style="padding: 7px; border: 1px solid #cbd5e1; text-align: right; color: #16a34a;">
                            <?php echo number_format((float)$row['invoice_receipts'], 3); ?>
                        </td>
                        <td style="padding: 7px; border: 1px solid #cbd5e1; text-align: right; font-weight: bold;">
                            <?php echo number_format((float)$row['total_received'], 3); ?>
                        </td>
                        <td style="padding: 7px; border: 1px solid #cbd5e1; text-align: right; font-weight: bold; <?php echo ($row['closing_balance'] > 0) ? 'color: #dc2626;' : (($row['closing_balance'] < 0) ? 'color: #2563eb;' : 'color: #16a34a;'); ?>">
                            <?php echo number_format((float)$row['closing_balance'], 3); ?>
                        </td>
                        <td style="padding: 7px; border: 1px solid #cbd5e1; text-align: center;">
                            <?php echo $row['status']; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="padding: 15px; text-align: center; color: #64748b; border: 1px solid #cbd5e1;">
                        No customer records found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold; border-top: 2px solid #94a3b8;">
                <td colspan="2" style="padding: 8px; border: 1px solid #cbd5e1; text-align: right;">Grand Total:</td>
                <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right;"><?php echo number_format($summary['opening_balance'], 3); ?></td>
                <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right;"><?php echo number_format($summary['total_invoices'], 3); ?></td>
                <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; color: #2563eb;"><?php echo number_format($summary['advance_received'], 3); ?></td>
                <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; color: #16a34a;"><?php echo number_format($summary['invoice_receipts'], 3); ?></td>
                <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right;"><?php echo number_format($summary['total_received'], 3); ?></td>
                <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; <?php echo ($summary['closing_balance'] > 0) ? 'color: #dc2626;' : 'color: #16a34a;'; ?>">
                    <?php echo number_format($summary['closing_balance'], 3); ?>
                </td>
                <td style="padding: 8px; border: 1px solid #cbd5e1;"></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
