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
                VENDOR BALANCE REPORT
            </td>
        </tr>
        <tr>
            <td colspan="9" style="font-size: 10pt; color: #475569; padding-bottom: 12px; border-bottom: 2px solid #cbd5e1;">
                <strong>As On Date:</strong> <?php echo !empty($as_on_date) ? date('d-M-Y', strtotime($as_on_date)) : date('d-M-Y'); ?> &nbsp;|&nbsp;
                <strong>Vendor:</strong> <?php echo htmlspecialchars($selected_vendor_name); ?> &nbsp;|&nbsp;
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
                TOTAL BILLS
            </td>
            <td style="background-color: #ede9fe; border: 1px solid #ddd6fe; padding: 8px; text-align: center; font-weight: bold; color: #5b21b6;">
                ADVANCE PAID
            </td>
            <td style="background-color: #dcfce7; border: 1px solid #bbf7d0; padding: 8px; text-align: center; font-weight: bold; color: #166534;">
                BILL PAYMENTS
            </td>
            <td style="background-color: #dcfce7; border: 1px solid #bbf7d0; padding: 8px; text-align: center; font-weight: bold; color: #166534;">
                TOTAL PAID
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
                <?php echo number_format($summary['total_bills'], 3); ?>
            </td>
            <td style="background-color: #f5f3ff; border: 1px solid #ddd6fe; padding: 10px; text-align: center; font-size: 13pt; font-weight: bold; color: #7c3aed;">
                <?php echo number_format($summary['advance_paid'], 3); ?>
            </td>
            <td style="background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 10px; text-align: center; font-size: 13pt; font-weight: bold; color: #15803d;">
                <?php echo number_format($summary['bill_payments'], 3); ?>
            </td>
            <td style="background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 10px; text-align: center; font-size: 13pt; font-weight: bold; color: #15803d;">
                <?php echo number_format($summary['total_paid'], 3); ?>
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
                <th style="padding: 8px; border: 1px solid #334155; text-align: left; width: 220px;">Vendor Name</th>
                <th style="padding: 8px; border: 1px solid #334155; text-align: right; width: 110px;">Opening Bal</th>
                <th style="padding: 8px; border: 1px solid #334155; text-align: right; width: 110px;">Total Bills</th>
                <th style="padding: 8px; border: 1px solid #334155; text-align: right; width: 110px;">Advance Paid</th>
                <th style="padding: 8px; border: 1px solid #334155; text-align: right; width: 110px;">Bill Payments</th>
                <th style="padding: 8px; border: 1px solid #334155; text-align: right; width: 110px;">Total Paid</th>
                <th style="padding: 8px; border: 1px solid #334155; text-align: right; width: 120px;">Closing Balance</th>
                <th style="padding: 8px; border: 1px solid #334155; text-align: center; width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($record_list)): ?>
                <?php foreach ($record_list as $i => $row): ?>
                    <tr style="background-color: <?php echo ($i % 2 == 0) ? '#ffffff' : '#f8fafc'; ?>;">
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: center;"><?php echo ($i + 1); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; font-weight: bold;"><?php echo htmlspecialchars($row['vendor_name']); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: right;"><?php echo number_format((float)$row['opening_balance'], 3); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: right;"><?php echo number_format((float)$row['total_bills'], 3); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: right; color: #0284c7;"><?php echo number_format((float)$row['advance_paid'], 3); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: right; color: #16a34a;"><?php echo number_format((float)$row['bill_payments'], 3); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: right; font-weight: bold;"><?php echo number_format((float)$row['total_paid'], 3); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: right; font-weight: bold; <?php echo ($row['closing_balance'] > 0) ? 'color: #dc2626;' : 'color: #16a34a;'; ?>">
                            <?php echo number_format((float)$row['closing_balance'], 3); ?>
                        </td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: center;"><?php echo $row['status']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="padding: 15px; text-align: center; border: 1px solid #cbd5e1; color: #64748b;">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td colspan="2" style="padding: 8px; border: 1px solid #94a3b8; text-align: right;">GRAND TOTAL:</td>
                <td style="padding: 8px; border: 1px solid #94a3b8; text-align: right;"><?php echo number_format($summary['opening_balance'], 3); ?></td>
                <td style="padding: 8px; border: 1px solid #94a3b8; text-align: right;"><?php echo number_format($summary['total_bills'], 3); ?></td>
                <td style="padding: 8px; border: 1px solid #94a3b8; text-align: right; color: #0284c7;"><?php echo number_format($summary['advance_paid'], 3); ?></td>
                <td style="padding: 8px; border: 1px solid #94a3b8; text-align: right; color: #16a34a;"><?php echo number_format($summary['bill_payments'], 3); ?></td>
                <td style="padding: 8px; border: 1px solid #94a3b8; text-align: right;"><?php echo number_format($summary['total_paid'], 3); ?></td>
                <td style="padding: 8px; border: 1px solid #94a3b8; text-align: right; color: #dc2626;"><?php echo number_format($summary['closing_balance'], 3); ?></td>
                <td style="padding: 8px; border: 1px solid #94a3b8;"></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
