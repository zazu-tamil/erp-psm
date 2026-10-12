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

    <table style="border-collapse: collapse; font-family: Arial, Helvetica, sans-serif;">
        <!-- Header -->
        <tr>
            <td colspan="11" style="font-size: 16pt; font-weight: bold; color: #1e293b; padding-bottom: 5px;">
                CUSTOMER INVOICE REPORT
            </td>
        </tr>
        <tr>
            <td colspan="11" style="font-size: 10pt; color: #475569; padding-bottom: 12px; border-bottom: 2px solid #cbd5e1;">
                <strong>Period:</strong> <?php echo !empty($srch_from_date) ? date('d-M-Y', strtotime($srch_from_date)) : 'Start'; ?> to <?php echo !empty($srch_to_date) ? date('d-M-Y', strtotime($srch_to_date)) : 'Today'; ?> &nbsp;|&nbsp;
                <strong>Customer:</strong> <?php echo htmlspecialchars($selected_customer_name); ?> &nbsp;|&nbsp;
                <strong>Generated On:</strong> <?php echo date('d-m-Y H:i:s'); ?>
            </td>
        </tr>

        <!-- Spacer -->
        <tr style="height: 12px;">
            <td colspan="11" style="border: none;"></td>
        </tr>

        <!-- KPI Summary Cards -->
        <tr>
            <td colspan="2" style="background-color: #e0f2fe; border: 1px solid #bae6fd; padding: 6px 8px; text-align: center; font-weight: bold; color: #0369a1; font-size: 9pt; white-space: nowrap;">
                TOTAL INVOICES
            </td>
            <td colspan="3" style="background-color: #e0f2fe; border: 1px solid #bae6fd; padding: 6px 8px; text-align: center; font-weight: bold; color: #0284c7; font-size: 9pt; white-space: nowrap;">
                TAXABLE (EXCL. VAT)
            </td>
            <td colspan="3" style="background-color: #fef3c7; border: 1px solid #fde68a; padding: 6px 8px; text-align: center; font-weight: bold; color: #92400e; font-size: 9pt; white-space: nowrap;">
                TOTAL VAT AMOUNT
            </td>
            <td colspan="3" style="background-color: #dcfce7; border: 1px solid #bbf7d0; padding: 6px 8px; text-align: center; font-weight: bold; color: #166534; font-size: 9pt; white-space: nowrap;">
                TOTAL AMOUNT (INCL. VAT)
            </td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: #f0f9ff; border: 1px solid #bae6fd; padding: 8px; text-align: center; font-size: 13pt; font-weight: bold; color: #0284c7;">
                <?php echo number_format($total_invoices); ?>
            </td>
            <td colspan="3" style="background-color: #f0f9ff; border: 1px solid #bae6fd; padding: 8px; text-align: center; font-size: 13pt; font-weight: bold; color: #0284c7;">
                <?php echo number_format($total_taxable, 3); ?>
            </td>
            <td colspan="3" style="background-color: #fffbeb; border: 1px solid #fde68a; padding: 8px; text-align: center; font-size: 13pt; font-weight: bold; color: #b45309;">
                <?php echo number_format($total_vat, 3); ?>
            </td>
            <td colspan="3" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 8px; text-align: center; font-size: 13pt; font-weight: bold; color: #15803d;">
                <?php echo number_format($grand_total, 3); ?>
            </td>
        </tr>

        <!-- Spacer -->
        <tr style="height: 15px;">
            <td colspan="11" style="border: none;"></td>
        </tr>

        <!-- Table Columns -->
        <thead>
            <tr>
                <th style="background-color: #1e293b; color: #ffffff; padding: 8px; border: 1px solid #334155; text-align: center; width: 45px;">S.No</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 8px; border: 1px solid #334155; text-align: center; width: 100px;">Date</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 8px; border: 1px solid #334155; text-align: center; width: 110px;">Invoice No</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 8px; border: 1px solid #334155; text-align: left; width: 220px;">Customer Name</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 8px; border: 1px solid #334155; text-align: center; width: 110px;">VAT / CR No</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 8px; border: 1px solid #334155; text-align: left; width: 140px;">Tender / Order Ref</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 8px; border: 1px solid #334155; text-align: left; width: 130px;">Customer PO No</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 8px; border: 1px solid #334155; text-align: center; width: 50px;">Curr</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 8px; border: 1px solid #334155; text-align: right; width: 110px;">Taxable Amt</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 8px; border: 1px solid #334155; text-align: right; width: 90px;">VAT</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 8px; border: 1px solid #334155; text-align: right; width: 120px;">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($records)): ?>
                <?php $sno = 1; foreach ($records as $r): ?>
                    <tr>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: center; mso-number-format:'\@';"><?php echo $sno++; ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: center; mso-number-format:'\@'; white-space: nowrap;"><?php echo date('d-m-Y', strtotime($r['invoice_date'])); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: center; font-weight: bold; mso-number-format:'\@';"><?php echo htmlspecialchars($r['invoice_no']); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: left; mso-number-format:'\@';"><?php echo htmlspecialchars($r['customer_name'] ?? '-'); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: center; mso-number-format:'\@';"><?php echo htmlspecialchars($r['client_vat_no'] ? $r['client_vat_no'] : ($r['customer_crno'] ? $r['customer_crno'] : '-')); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: left; mso-number-format:'\@';"><?php echo htmlspecialchars($r['tender_details'] ? $r['tender_details'] : ($r['enquiry_no'] ? $r['enquiry_no'] : '-')); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: left; mso-number-format:'\@';"><?php echo htmlspecialchars($r['customer_po_no'] ? $r['customer_po_no'] : ($r['our_po_no'] ? $r['our_po_no'] : '-')); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: center; mso-number-format:'\@';"><?php echo htmlspecialchars($r['currency_code']); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: right; mso-number-format:'\#\,\#\#0\.000';"><?php echo number_format((float)$r['taxable_amount'], (int)$r['decimal_point']); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: right; mso-number-format:'\#\,\#\#0\.000';"><?php echo number_format((float)$r['tax_amount'], (int)$r['decimal_point']); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: right; font-weight: bold; mso-number-format:'\#\,\#\#0\.000';"><?php echo number_format((float)$r['total_amount'], (int)$r['decimal_point']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" style="padding: 20px; border: 1px solid #cbd5e1; text-align: center; color: #888;">
                        No invoices found matching the selected filter criteria.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" style="background-color: #f1f5f9; font-weight: bold; padding: 8px; border: 1px solid #cbd5e1; text-align: right;">
                    Total (Invoices: <?php echo number_format($total_invoices); ?>):
                </td>
                <td style="background-color: #f1f5f9; font-weight: bold; padding: 8px; border: 1px solid #cbd5e1; text-align: right; mso-number-format:'\#\,\#\#0\.000';"><?php echo number_format($total_taxable, 3); ?></td>
                <td style="background-color: #f1f5f9; font-weight: bold; padding: 8px; border: 1px solid #cbd5e1; text-align: right; mso-number-format:'\#\,\#\#0\.000';"><?php echo number_format($total_vat, 3); ?></td>
                <td style="background-color: #f1f5f9; font-weight: bold; padding: 8px; border: 1px solid #cbd5e1; text-align: right; color: #15803d; mso-number-format:'\#\,\#\#0\.000';"><?php echo number_format($grand_total, 3); ?></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
