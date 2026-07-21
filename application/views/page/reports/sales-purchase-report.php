<?php include_once(VIEWPATH . '/inc/header.php'); 
?>
<section class="content-header">
    <h1>VAT Return Form Summary</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Reports</a></li>
        <li class="active">VAT Return Form Summary</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-info no-print">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('sales-purchase-report')?>" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>From Date</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="date" class="form-control pull-right " id="srch_from_date"
                                name="srch_from_date" value="<?php echo set_value('srch_from_date',$srch_from_date);?>"
                                required="true">
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label>To Date</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="date" class="form-control pull-right " id="srch_to_date" name="srch_to_date"
                                value="<?php echo set_value('srch_to_date',$srch_to_date);?>" required="true">
                        </div> 
                    </div>
                    <div class="form-group col-md-2 text-left">
                        <br />
                        <button class="btn btn-success" name="btn_show" value="Show'"><i class="fa fa-search"></i>
                            Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- VAT ON SALES -->
    <?php 
    $total_sales_amt = 0;
    $total_sales_vat = 0;
    ?>
    <h3 class="page-header" style="color: #a02b34; font-weight: bold; font-size: 20px;">VAT on sales</h3>
    <div class="box" style="border-top-color: #a02b34;">
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-striped" style="border: 1px solid #ccc;">
                <thead>
                    <tr style="background-color: #555d66; color: white;">
                        <th width="5%">No.</th>
                        <th width="40%">Description</th>
                        <th class="text-right">Amount (BHD)</th>
                        <th class="text-right">Adjustment / Apportionment(BHD)</th>
                        <th class="text-right">VAT Amount (BHD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $row_no = 1;
                    foreach ($sales_record_list as $s_order => $records) {
                        $grp_name = isset($records[0]['vat_payer_sales_grp']) ? $records[0]['vat_payer_sales_grp'] : '';
                        if(empty($grp_name)) continue;

                        $grp_amt = 0;
                        $grp_vat = 0;
                        if(isset($records[0]['invoice_no']) && $records[0]['invoice_no'] != '') {
                            foreach ($records as $row) {
                                $grp_amt += $row['tot_amt_ex_tax'];
                                $grp_vat += $row['vat_amt'];
                            }
                        }
                        
                        $total_sales_amt += $grp_amt;
                        $total_sales_vat += $grp_vat;
                        ?>
                        <tr>
                            <td><?php echo $row_no; ?></td>
                            <td><?php echo $grp_name; ?></td>
                            <td class="text-right"><?php echo number_format($grp_amt, 3); ?></td>
                            <td class="text-right">0.000</td>
                            <td class="text-right"><?php echo number_format($grp_vat, 3); ?></td>
                        </tr>
                        <?php
                        $row_no++;
                    }
                    ?>
                </tbody>
                <tfoot style="background-color: #9299a1; color: white; font-weight: bold;">
                    <tr>
                        <td><?php echo $row_no; ?>.</td>
                        <td>Total sales</td>
                        <td class="text-right"><?php echo number_format($total_sales_amt, 3); ?></td>
                        <td class="text-right">0.000</td>
                        <td class="text-right"><?php echo number_format($total_sales_vat, 3); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- VAT ON PURCHASES -->
    <?php 
    $total_purchase_amt = 0;
    $total_purchase_vat = 0;
    ?>
    <h3 class="page-header" style="color: #a02b34; font-weight: bold; font-size: 20px; margin-top: 30px;">VAT on purchases</h3>
    <div class="box" style="border-top-color: #a02b34;">
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-striped" style="border: 1px solid #ccc;">
                <thead>
                    <tr style="background-color: #555d66; color: white;">
                        <th width="5%">No.</th>
                        <th width="40%">Description</th>
                        <th class="text-right">Amount (BHD)</th>
                        <th class="text-right">Adjustment / Apportionment(BHD)</th>
                        <th class="text-right">VAT Amount (BHD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $p_row_no = $row_no + 1; // Continuing the numbering
                    foreach ($purchase_record_list as $s_order => $records) {
                        $grp_name = isset($records[0]['vat_payer_purchase_grp']) ? $records[0]['vat_payer_purchase_grp'] : '';
                        if(empty($grp_name)) continue;

                        $grp_amt = 0;
                        $grp_vat = 0;
                        if(isset($records[0]['invoice_no']) && $records[0]['invoice_no'] != '') {
                            foreach ($records as $row) {
                                $grp_amt += $row['tot_amt_ex_tax'];
                                $grp_vat += $row['vat_amt'];
                            }
                        }
                        
                        $total_purchase_amt += $grp_amt;
                        $total_purchase_vat += $grp_vat;
                        ?>
                        <tr>
                            <td><?php echo $p_row_no; ?></td>
                            <td><?php echo $grp_name; ?></td>
                            <td class="text-right"><?php echo number_format($grp_amt, 3); ?></td>
                            <td class="text-right">0.000</td>
                            <td class="text-right"><?php echo number_format($grp_vat, 3); ?></td>
                        </tr>
                        <?php
                        $p_row_no++;
                    }
                    ?>
                </tbody>
                <tfoot style="background-color: #9299a1; color: white; font-weight: bold;">
                    <tr>
                        <td><?php echo $p_row_no; ?>.</td>
                        <td>Total purchases</td>
                        <td class="text-right"><?php echo number_format($total_purchase_amt, 3); ?></td>
                        <td class="text-right">0.000</td>
                        <td class="text-right"><?php echo number_format($total_purchase_vat, 3); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- SUMMARY CALCULATION SECTION -->
    <?php
    $total_vat_due = $total_sales_vat - $total_purchase_vat;
    $corrections = 0;
    $credit_carried = 0;
    $net_vat_due = $total_vat_due + $corrections - $credit_carried;
    ?>
    <div class="box" style="border: none; box-shadow: none;">
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered">
                <tbody>
                    <tr style="background-color: #b91522; color: white; font-weight: bold; font-size: 15px;">
                        <td width="5%"><?php echo $p_row_no + 1; ?></td>
                        <td width="70%">Total VAT due for current period</td>
                        <td class="text-right" colspan="3"><?php echo number_format($total_vat_due, 3); ?></td>
                    </tr>
                    <tr style="background-color: #f4f4f4;">
                        <td><?php echo $p_row_no + 2; ?></td>
                        <td>Corrections from previous period </td>
                        <td class="text-right" colspan="3">
                            <input type="text" class="form-control text-right" style="width: 150px; float: right; background: #fff;" value="<?php echo number_format($corrections, 3); ?>" readonly>
                        </td>
                    </tr>
                    <tr style="background-color: #f4f4f4;">
                        <td><?php echo $p_row_no + 3; ?></td>
                        <td>VAT credit carried forward from previous period(s)</td>
                        <td class="text-right" colspan="3">
                            <input type="text" class="form-control text-right" style="width: 150px; float: right; background: #fff; border:none;" value="<?php echo number_format($credit_carried, 3); ?>" readonly>
                        </td>
                    </tr>
                    <tr style="background-color: #f4f4f4;">
                        <td><?php echo $p_row_no + 4; ?></td>
                        <td>Net VAT due (or reclaimed)</td>
                        <td class="text-right" colspan="3" style="font-weight: bold; font-size: 16px;"><?php echo number_format($net_vat_due, 3); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<!-- /.content -->
<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
