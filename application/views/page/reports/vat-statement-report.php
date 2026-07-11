<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header no-print">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Reports</a></li>
        <li class="active"><?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">
    <!-- Search Filter -->
    <div class="box box-info no-print">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('vat-statement-report'); ?>" id="frmsearch">
                <input type="hidden" name="mode" value="Search">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="srch_company_id">Company</label>
                        <select name="srch_company_id" id="srch_company_id" class="form-control select2" required="true">
                            <?php if (!empty($company_list)): ?>
                                <?php foreach ($company_list as $comp): ?>
                                    <option value="<?php echo $comp['company_id']; ?>"
                                        <?php echo ($srch_company_id == $comp['company_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($comp['company_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_customer_id">Customer</label>
                        <select name="srch_customer_id" id="srch_customer_id" class="form-control select2">
                            <option value="">-- All Customers --</option>
                            <?php if (!empty($customer_list)): ?>
                                <?php foreach ($customer_list as $cust): ?>
                                    <option value="<?php echo $cust['customer_id']; ?>"
                                        <?php echo ($srch_customer_id == $cust['customer_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cust['customer_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="srch_from_date">From Date</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="date" name="srch_from_date" id="srch_from_date" class="form-control"
                                value="<?php echo set_value('srch_from_date', $srch_from_date); ?>" required="true">
                        </div>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="srch_to_date">To Date</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="date" name="srch_to_date" id="srch_to_date" class="form-control"
                                value="<?php echo set_value('srch_to_date', $srch_to_date); ?>" required="true">
                        </div>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="srch_text">Invoice No / Name</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-search"></i></div>
                            <input type="text" name="srch_text" id="srch_text" class="form-control"
                                placeholder="Search..." value="<?php echo htmlspecialchars($srch_text); ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show Report</button>
                        <button type="button" onclick="exportToExcel()" class="btn btn-warning"><i class="fa fa-file-excel-o"></i> Export Excel</button>
                        <button type="button" onclick="window.print()" class="btn btn-primary"><i class="fa fa-print"></i> Print PDF</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Table Content -->
    <div class="box box-solid">
        <div class="box-header with-border text-center">
            <h3 class="box-title" style="font-weight: bold; font-size: 16px; display: block; margin: 10px 0;">
                VAT STATEMENT <?php echo htmlspecialchars($selected_company_name); ?> - VAT No: <?php echo htmlspecialchars($selected_company_vat ?: 'N/A'); ?><br>
                <small style="font-size: 13px; font-weight: bold; color: #475569;">
                    IMPORT, PURCHASE & SALES INVOICES FOR THE PERIOD OF 
                    (<?php echo date('d-m-Y', strtotime($srch_from_date)); ?> TO <?php echo date('d-m-Y', strtotime($srch_to_date)); ?>)
                </small>
            </h3>
        </div>
        <div class="box-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" style="margin-bottom: 0; font-size: 11px;">
                    <thead>
                        <tr style="background-color: #f8fafc; font-weight: bold; text-align: center;">
                            <th rowspan="2" style="vertical-align: middle; text-align: center;">Sl</th>
                            <th rowspan="2" style="vertical-align: middle; text-align: center; min-width: 80px;">Our INV.<br>Date</th>
                            <th rowspan="2" style="vertical-align: middle; text-align: center; min-width: 70px;">Our<br>Invoice No</th>
                            <th colspan="10" style="text-align: center; background-color: #f1f5f9;">IMPORT, PURCHASE & EXPENSES (INPUT VAT)</th>
                            <th colspan="5" style="text-align: center; background-color: #ecfdf5;">SALES INVOICES (OUTPUT VAT)</th>
                        </tr>
                        <tr style="background-color: #f8fafc; font-weight: bold; text-align: center;">
                            <th style="vertical-align: middle; text-align: center; min-width: 110px;">Invoice No /<br>Bayan Number</th>
                            <th style="vertical-align: middle; text-align: center; min-width: 80px;">Supplier<br>Inv. Date</th>
                            <th style="vertical-align: middle; text-align: center; min-width: 140px;">Vendor Name</th>
                            <th style="vertical-align: middle; text-align: center; min-width: 100px;">Vendor's VAT No.</th>
                            <th style="vertical-align: middle; text-align: right;">CIF VAL<br>BD</th>
                            <th style="vertical-align: middle; text-align: right;">DUTY</th>
                            <th style="vertical-align: middle; text-align: right;">Total Val<br>Including Duty</th>
                            <th style="vertical-align: middle; text-align: right; background-color: #bbf7d0; color: #166534;">10% vat on CIV<br>value Incl. Duty</th>
                            <th style="vertical-align: middle; text-align: right; background-color: #dbeafe; color: #1e40af;">VAT PAID BD<br>(Input VAT)</th>
                            <th style="vertical-align: middle; text-align: right;">TOTAL</th>
                            
                            <th style="vertical-align: middle; text-align: center; min-width: 140px;">Customer</th>
                            <th style="vertical-align: middle; text-align: center; min-width: 100px;">Customer VAT No</th>
                            <th style="vertical-align: middle; text-align: right;">Invoice Value<br>W/O VAT</th>
                            <th style="vertical-align: middle; text-align: right; color: #991b1b;">AH VAT Charged<br>(Output VAT)</th>
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

                                // Accumulate client totals (only once per customer invoice)
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
                                            <td rowspan="<?php echo $rowspan; ?>" style="vertical-align: middle; text-align: center; font-weight: bold;"><?php echo $sno++; ?></td>
                                            <td rowspan="<?php echo $rowspan; ?>" style="vertical-align: middle; text-align: center;"><?php echo date('d/m/Y', strtotime($inv['invoice_date'])); ?></td>
                                            <td rowspan="<?php echo $rowspan; ?>" style="vertical-align: middle; text-align: center; font-weight: bold; color: #2563eb;">
                                                <?php echo htmlspecialchars($inv['invoice_no']); ?>
                                            </td>
                                        <?php endif; ?>

                                        <!-- Vendor Expense Details Columns -->
                                        <?php if ($bill): ?>
                                            <td style="text-align: center; font-weight: 500;"><?php echo htmlspecialchars($bill['invoice_no'] ?: 'N/A'); ?></td>
                                            <td style="text-align: center;"><?php echo $bill['invoice_date'] ? date('d/m/Y', strtotime($bill['invoice_date'])) : ''; ?></td>
                                            <td><?php echo htmlspecialchars($bill['vendor_name']); ?></td>
                                            <td style="text-align: center;"><?php echo htmlspecialchars($bill['vendor_vat'] ?: '-'); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($bill['cif_val'], 3); ?></td>
                                            <td style="text-align: right;"><?php echo number_format($bill['duty'], 3); ?></td>
                                            <td style="text-align: right; font-weight: 500;"><?php echo number_format($bill['total_val'], 3); ?></td>
                                            <td style="text-align: right; background-color: #f2fbf5; color: #166534; font-weight: 500;"><?php echo number_format($bill['vat'], 3); ?></td>
                                            <td style="text-align: right; background-color: #eff6ff; color: #1e40af; font-weight: 500;"><?php echo number_format($bill['vat_paid'], 3); ?></td>
                                            <td style="text-align: right; font-weight: 600;"><?php echo number_format($bill['grand_total'], 3); ?></td>
                                        <?php else: ?>
                                            <td style="text-align: center; color: #94a3b8;">-</td>
                                            <td style="text-align: center; color: #94a3b8;">-</td>
                                            <td style="color: #94a3b8;">No linked expenses</td>
                                            <td style="text-align: center; color: #94a3b8;">-</td>
                                            <td style="text-align: right; color: #94a3b8;">0.000</td>
                                            <td style="text-align: right; color: #94a3b8;">0.000</td>
                                            <td style="text-align: right; color: #94a3b8;">0.000</td>
                                            <td style="text-align: right; color: #94a3b8; background-color: #f2fbf5;">0.000</td>
                                            <td style="text-align: right; color: #94a3b8; background-color: #eff6ff;">0.000</td>
                                            <td style="text-align: right; color: #94a3b8;">0.000</td>
                                        <?php endif; ?>

                                        <!-- Customer Inv Right Columns (Rowspanned) -->
                                        <?php if ($i === 0): ?>
                                            <td rowspan="<?php echo $rowspan; ?>" style="vertical-align: middle; font-weight: 500;"><?php echo htmlspecialchars($inv['customer_name']); ?></td>
                                            <td rowspan="<?php echo $rowspan; ?>" style="vertical-align: middle; text-align: center;"><?php echo htmlspecialchars($inv['client_vat_no'] ?: '-'); ?></td>
                                            <td rowspan="<?php echo $rowspan; ?>" style="vertical-align: middle; text-align: right; font-weight: 500;"><?php echo number_format($inv['total_amount_wo_tax'], 3); ?></td>
                                            <td rowspan="<?php echo $rowspan; ?>" style="vertical-align: middle; text-align: right; color: #991b1b; font-weight: 500;"><?php echo number_format($inv['tax_amount'], 3); ?></td>
                                            <td rowspan="<?php echo $rowspan; ?>" style="vertical-align: middle; text-align: right; font-weight: bold;"><?php echo number_format($inv['total_amount'], 3); ?></td>
                                        <?php endif; ?>
                                    </tr>
                        <?php 
                                endfor;
                            endforeach;
                        else:
                        ?>
                            <tr>
                                <td colspan="18" class="text-center text-muted" style="padding: 30px;">No invoice records found for the selected period.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <!-- Totals Row -->
                    <tfoot>
                        <tr style="background-color: #f8fafc; font-weight: bold; border-top: 2px solid #cbd5e1;">
                            <td colspan="7" style="text-align: right; font-weight: bold; font-size: 12px; vertical-align: middle;">TOTAL:</td>
                            <td style="text-align: right; font-size: 11px;"><?php echo number_format($tot_cif, 3); ?></td>
                            <td style="text-align: right; font-size: 11px;"><?php echo number_format($tot_duty, 3); ?></td>
                            <td style="text-align: right; font-size: 11px;"><?php echo number_format($tot_val_inc_duty, 3); ?></td>
                            <td style="text-align: right; font-size: 11px; background-color: #e2fbf0; color: #166534;"><?php echo number_format($tot_vat_civ, 3); ?></td>
                            <td style="text-align: right; font-size: 11px; background-color: #e0f2fe; color: #1e40af;"><?php echo number_format($tot_vat_paid, 3); ?></td>
                            <td style="text-align: right; font-size: 11px;"><?php echo number_format($tot_vendor_grand, 3); ?></td>
                            <td colspan="2" style="background-color: #f8fafc;"></td>
                            <td style="text-align: right; font-size: 11px;"><?php echo number_format($tot_client_wo_vat, 3); ?></td>
                            <td style="text-align: right; font-size: 11px; color: #991b1b;"><?php echo number_format($tot_client_vat, 3); ?></td>
                            <td style="text-align: right; font-size: 11px; font-weight: bold;"><?php echo number_format($tot_client_grand, 3); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
