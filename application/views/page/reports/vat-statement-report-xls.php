<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=vat_statement_report_" . date('Ymd_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border="1">
    <thead>
        <tr>
            <th colspan="18" style="font-weight: bold; font-size: 14px; text-align: center; height: 35px; vertical-align: middle;">
                VAT STATEMENT <?php echo htmlspecialchars($selected_company_name); ?> - VAT No: <?php echo htmlspecialchars($selected_company_vat ?: 'N/A'); ?>
            </th>
        </tr>
        <tr>
            <th colspan="18" style="font-weight: bold; font-size: 11px; text-align: center; height: 25px; vertical-align: middle;">
                IMPORT, PURCHASE & SALES INVOICES FOR THE PERIOD OF (<?php echo date('d-m-Y', strtotime($srch_from_date)); ?> TO <?php echo date('d-m-Y', strtotime($srch_to_date)); ?>)
            </th>
        </tr>
        <tr style="background-color: #f8fafc; font-weight: bold; text-align: center;">
            <th rowspan="2" style="vertical-align: middle; text-align: center;">Sl</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center;">Our INV. Date</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center;">Our Invoice No</th>
            <th colspan="10" style="text-align: center; background-color: #f1f5f9;">IMPORT, PURCHASE & EXPENSES (INPUT VAT)</th>
            <th colspan="5" style="text-align: center; background-color: #ecfdf5;">SALES INVOICES (OUTPUT VAT)</th>
        </tr>
        <tr style="background-color: #f8fafc; font-weight: bold; text-align: center;">
            <th style="vertical-align: middle; text-align: center;">Invoice No / Bayan Number</th>
            <th style="vertical-align: middle; text-align: center;">Supplier Inv. Date</th>
            <th style="vertical-align: middle; text-align: center;">Vendor Name</th>
            <th style="vertical-align: middle; text-align: center;">Vendor's VAT No.</th>
            <th style="vertical-align: middle; text-align: right;">CIF VAL BD</th>
            <th style="vertical-align: middle; text-align: right;">DUTY</th>
            <th style="vertical-align: middle; text-align: right;">Total Val Including Duty</th>
            <th style="vertical-align: middle; text-align: right; background-color: #bbf7d0; color: #166534;">10% vat on CIV value Incl. Duty</th>
            <th style="vertical-align: middle; text-align: right; background-color: #dbeafe; color: #1e40af;">VAT PAID BD (Input VAT)</th>
            <th style="vertical-align: middle; text-align: right;">TOTAL</th>
            
            <th style="vertical-align: middle; text-align: center;">Customer</th>
            <th style="vertical-align: middle; text-align: center;">Customer VAT No</th>
            <th style="vertical-align: middle; text-align: right;">Invoice Value W/O VAT</th>
            <th style="vertical-align: middle; text-align: right; color: #991b1b;">AH VAT Charged (Output VAT)</th>
            <th style="vertical-align: middle; text-align: right; font-weight: bold;">Total inclVAT</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $sno = 1;
        
        // Grand totals
        $tot_cif = 0;
        $tot_duty = 0;
        $tot_val_inc_duty = 0;
        $tot_vat_civ = 0;
        $tot_vat_paid = 0;
        $tot_vendor_grand = 0;
        
        $tot_client_wo_vat = 0;
        $tot_client_vat = 0;
        $tot_client_grand = 0;

        if (!empty($records)): 
            foreach ($records as $rec):
                $inv = $rec['invoice'];
                $bills = $rec['bills'];
                $rowspan = count($bills) > 0 ? count($bills) : 1;

                // Accumulate client totals
                $tot_client_wo_vat += floatval($inv['total_amount_wo_tax']);
                $tot_client_vat += floatval($inv['tax_amount']);
                $tot_client_grand += floatval($inv['total_amount']);

                for ($i = 0; $i < $rowspan; $i++):
                    $bill = isset($bills[$i]) ? $bills[$i] : null;

                    if ($bill) {
                        $tot_cif += floatval($bill['cif_val']);
                        $tot_duty += floatval($bill['duty']);
                        $tot_val_inc_duty += floatval($bill['total_val']);
                        $tot_vat_civ += floatval($bill['vat']);
                        $tot_vat_paid += floatval($bill['vat_paid']);
                        $tot_vendor_grand += floatval($bill['grand_total']);
                    }
        ?>
                    <tr>
                        <!-- Customer Inv Left Columns (Rowspanned) -->
                        <?php if ($i === 0): ?>
                            <td rowspan="<?php echo $rowspan; ?>" style="text-align: center; vertical-align: middle;"><?php echo $sno++; ?></td>
                            <td rowspan="<?php echo $rowspan; ?>" style="text-align: center; vertical-align: middle;"><?php echo date('d-m-Y', strtotime($inv['invoice_date'])); ?></td>
                            <td rowspan="<?php echo $rowspan; ?>" style="text-align: center; vertical-align: middle; font-weight: bold;"><?php echo htmlspecialchars($inv['invoice_no']); ?></td>
                        <?php endif; ?>

                        <!-- Vendor Expense Details Columns -->
                        <?php if ($bill): ?>
                            <td style="text-align: center;"><?php echo htmlspecialchars($bill['invoice_no'] ?: 'N/A'); ?></td>
                            <td style="text-align: center;"><?php echo $bill['invoice_date'] ? date('d-m-Y', strtotime($bill['invoice_date'])) : ''; ?></td>
                            <td><?php echo htmlspecialchars($bill['vendor_name']); ?></td>
                            <td style="text-align: center;"><?php echo htmlspecialchars($bill['vendor_vat'] ?: '-'); ?></td>
                            <td style="text-align: right;"><?php echo number_format($bill['cif_val'], 3, '.', ''); ?></td>
                            <td style="text-align: right;"><?php echo number_format($bill['duty'], 3, '.', ''); ?></td>
                            <td style="text-align: right;"><?php echo number_format($bill['total_val'], 3, '.', ''); ?></td>
                            <td style="text-align: right; background-color: #f2fbf5;"><?php echo number_format($bill['vat'], 3, '.', ''); ?></td>
                            <td style="text-align: right; background-color: #eff6ff;"><?php echo number_format($bill['vat_paid'], 3, '.', ''); ?></td>
                            <td style="text-align: right; font-weight: bold;"><?php echo number_format($bill['grand_total'], 3, '.', ''); ?></td>
                        <?php else: ?>
                            <td style="text-align: center;">-</td>
                            <td style="text-align: center;">-</td>
                            <td>No linked expenses</td>
                            <td style="text-align: center;">-</td>
                            <td style="text-align: right;">0.000</td>
                            <td style="text-align: right;">0.000</td>
                            <td style="text-align: right;">0.000</td>
                            <td style="text-align: right; background-color: #f2fbf5;">0.000</td>
                            <td style="text-align: right; background-color: #eff6ff;">0.000</td>
                            <td style="text-align: right;">0.000</td>
                        <?php endif; ?>

                        <!-- Customer Inv Right Columns (Rowspanned) -->
                        <?php if ($i === 0): ?>
                            <td rowspan="<?php echo $rowspan; ?>" style="vertical-align: middle;"><?php echo htmlspecialchars($inv['customer_name']); ?></td>
                            <td rowspan="<?php echo $rowspan; ?>" style="text-align: center; vertical-align: middle;"><?php echo htmlspecialchars($inv['client_vat_no'] ?: '-'); ?></td>
                            <td rowspan="<?php echo $rowspan; ?>" style="text-align: right; vertical-align: middle;"><?php echo number_format($inv['total_amount_wo_tax'], 3, '.', ''); ?></td>
                            <td rowspan="<?php echo $rowspan; ?>" style="text-align: right; vertical-align: middle;"><?php echo number_format($inv['tax_amount'], 3, '.', ''); ?></td>
                            <td rowspan="<?php echo $rowspan; ?>" style="text-align: right; vertical-align: middle; font-weight: bold;"><?php echo number_format($inv['total_amount'], 3, '.', ''); ?></td>
                        <?php endif; ?>
                    </tr>
        <?php 
                endfor;
            endforeach;
        else:
        ?>
            <tr>
                <td colspan="18" style="text-align: center;">No invoice records found for the selected period.</td>
            </tr>
        <?php endif; ?>
    </tbody>
    <!-- Totals Row -->
    <tfoot>
        <tr style="background-color: #f8fafc; font-weight: bold;">
            <td colspan="7" style="text-align: right; font-weight: bold;">TOTAL:</td>
            <td style="text-align: right;"><?php echo number_format($tot_cif, 3, '.', ''); ?></td>
            <td style="text-align: right;"><?php echo number_format($tot_duty, 3, '.', ''); ?></td>
            <td style="text-align: right;"><?php echo number_format($tot_val_inc_duty, 3, '.', ''); ?></td>
            <td style="text-align: right; background-color: #e2fbf0;"><?php echo number_format($tot_vat_civ, 3, '.', ''); ?></td>
            <td style="text-align: right; background-color: #e0f2fe;"><?php echo number_format($tot_vat_paid, 3, '.', ''); ?></td>
            <td style="text-align: right;"><?php echo number_format($tot_vendor_grand, 3, '.', ''); ?></td>
            <td colspan="2"></td>
            <td style="text-align: right;"><?php echo number_format($tot_client_wo_vat, 3, '.', ''); ?></td>
            <td style="text-align: right;"><?php echo number_format($tot_client_vat, 3, '.', ''); ?></td>
            <td style="text-align: right; font-weight: bold;"><?php echo number_format($tot_client_grand, 3, '.', ''); ?></td>
        </tr>
    </tfoot>
</table>
