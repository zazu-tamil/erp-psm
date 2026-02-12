<?php  include_once(VIEWPATH . '/inc/header.php'); 
// echo "<pre>";
// print_r($record_list);  
// echo "</pre>";
?>
<section class="content-header">
    <h1>Sales NBR Report</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Reports</a></li>
        <li class="active">Sales NBR Report</li>
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
            <form method="post" action="<?php echo site_url('sales-nbr-report')?>" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>From Month</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="date" class="form-control pull-right " id="srch_from_date"
                                name="srch_from_date" value="<?php echo set_value('srch_from_date',$srch_from_date);?>"
                                required="true">
                        </div>
                        <!-- /.input group -->
                    </div>
                    <div class="form-group col-md-3">
                        <label>To Month</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="date" class="form-control pull-right " id="srch_to_date" name="srch_to_date"
                                value="<?php echo set_value('srch_to_date',$srch_to_date);?>" required="true">
                        </div>
                        <!-- /.input group -->
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

    <?php  if(!empty($record_list)) { ?>
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Sales NBR Report [
                <?php echo date('d-m-Y', strtotime($srch_from_date)) . ' to ' . date('d-m-Y', strtotime($srch_to_date)) ; ?>
                ]</h3>
        </div>
        <div class="box-body">
            <?php 
        foreach ($record_list as $s_order => $records) {
            if($s_order == 1 || $s_order == 2 ) {  // 1. Standard Rated Sales at 5% (Line 1 of the VAT Return) , 2. Standard Rated Sales at 10% (Line 2 of the VAT Return)
             echo ' <div class="box box-info"> <div class="box-header with-border bg-info"> ';
                echo "<b>" . $records[0]['vat_payer_sales_grp'] . "</b>";
                echo ' </div> <div class="box-body"> ';
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead>
                        <tr>
                            <th>VAT Return Field Number</th> 
                            <th>Invoice Number</th>
                            <th>Invoice Date</th>
                            <th>Client VAT Account Number</th>
                            <th>Client Name</th> 
                            <th>Good/Service Description</th>
                            <th>Total BHD <br>(Exclusive of VAT)</th>
                            <th>VAT Amount</th>
                            <th>Total BHD <br>(Inclusive of VAT)</th>
                        </tr>
                    </thead>";
                echo "<tbody>";
                if($records[0]['vat_rtn_fld'] != '') {
                $tot_amt_ex_tax = $tot_vat_amt = $tot_amt_inc_tax = 0;
                foreach ($records as $row) { 
                    echo "<tr>";
                    echo "<td>{$row['vat_rtn_fld']}</td>"; 
                    echo "<td><b>{$row['invoice_no']}</b></td>";
                    echo "<td>".date('d-m-Y', strtotime($row['invoice_date']))."</td>";
                    echo "<td><b>{$row['client_vat_no']}</b></td>";
                    echo "<td><b>{$row['client_name']}</b></td>"; 
                    echo "<td><b>{$row['g_desc']}</b></td>";
                    echo "<td>{$row['tot_amt_ex_tax']}</td>"; 
                    echo "<td>{$row['vat_amt']}</td>"; 
                    echo "<td>{$row['tot_amt_inc_tax']}</td>"; 
                    echo "</tr>";
                    $tot_amt_ex_tax += $row['tot_amt_ex_tax'];
                    $tot_vat_amt += $row['vat_amt'];
                    $tot_amt_inc_tax += $row['tot_amt_inc_tax'];
                }
                echo "<tr>
                    <th colspan='6' class='text-left'>". $records[0]['vat_payer_sales_grp'] ."</th>
                    <th>".number_format($tot_amt_ex_tax, 3)."</th>
                    <th>".number_format($tot_vat_amt, 3)."</th>
                    <th>".number_format($tot_amt_inc_tax, 3)."</th>
                </tr>";
                }
                echo "</tbody></table></div></div><br/><br/>";
            }

            if($s_order == 3) {  // 3. Zero Rated Sales (Line 3 of the VAT Return)
                echo ' <div class="box box-info"> <div class="box-header with-border bg-info"> ';
                echo "<b>" . $records[0]['vat_payer_sales_grp'] . "</b>"; 
                echo ' </div> <div class="box-body"> ';
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead>
                        <tr>
                            <th>VAT Return Field Number</th> 
                            <th>Invoice Number</th>
                            <th>Invoice Date</th>
                            <th>Client VAT Account Number</th>
                            <th>Client Name</th> 
                            <th>Good/Service Description</th>
                            <th>Total BHD <br>(Exclusive of VAT)</th> 
                        </tr>
                    </thead>";
                echo "<tbody>";
                if($records[0]['vat_rtn_fld'] != '') {
                $tot_amt_ex_tax = $tot_vat_amt = $tot_amt_inc_tax = 0;
                foreach ($records as $row) { 
                    echo "<tr>";
                    echo "<td>{$row['vat_rtn_fld']}</td>"; 
                    echo "<td><b>{$row['invoice_no']}</b></td>";
                    echo "<td>".date('d-m-Y', strtotime($row['invoice_date']))."</td>";
                    echo "<td><b>{$row['client_vat_no']}</b></td>";
                    echo "<td><b>{$row['client_name']}</b></td>"; 
                    echo "<td><b>{$row['g_desc']}</b></td>";
                    echo "<td>{$row['tot_amt_ex_tax']}</td>";  
                    echo "</tr>";
                    $tot_amt_ex_tax += $row['tot_amt_ex_tax']; 
                }
                echo "<tr>
                    <th colspan='6' class='text-left'>". $records[0]['vat_payer_sales_grp'] ."</th>
                    <th>".number_format($tot_amt_ex_tax, 3)."</th> 
                </tr>";
                }
                echo "</tbody></table></div></div><br/><br/>";
            }

            if($s_order == 4) {  // 4. Exports (Line 5 of the VAT Return)
                echo ' <div class="box box-info"> <div class="box-header with-border bg-info"> ';
                echo "<b>" . $records[0]['vat_payer_sales_grp'] . "</b>";
                echo ' </div> <div class="box-body"> ';
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead>
                        <tr>
                            <th>VAT Return Field Number</th>
                            <th>Declaration Number</th>
                            <th>Declaration Date</th>
                            <th>Invoice Number</th>
                            <th>Invoice Date</th>
                            <th>Client Name</th>
                            <th>Client's country of origin</th>
                            <th>Good/Service Description</th>
                            <th>CIF Value</th>
                        </tr>
                    </thead>";
                echo "<tbody>";
                if($records[0]['vat_rtn_fld'] != '') {
                $tot = 0;
                foreach ($records as $row) { 
                    echo "<tr>";
                    echo "<td>{$row['vat_rtn_fld']}</td>";
                    echo "<td>{$row['declaration_no']}</td>";
                    echo "<td>".date('d-m-Y', strtotime($row['declaration_date']))."</td>";
                    echo "<td>{$row['invoice_no']}</td>";
                    echo "<td>".date('d-m-Y', strtotime($row['invoice_date']))."</td>";
                    echo "<td>{$row['client_name']}</td>";
                    echo "<td>{$row['country_code']}</td>";
                    echo "<td>{$row['g_desc']}</td>";
                    echo "<td>{$row['tot_amt_inc_tax']}</td>"; 
                    echo "</tr>";
                    $tot += $row['tot_amt_inc_tax'];
                }
                echo "<tr><th colspan='8' class='text-left'>". $records[0]['vat_payer_sales_grp'] ."</th><th>".number_format($tot, 3)."</th></tr>";
                }
                echo "</tbody></table></div></div><br/><br/>";
            }

            if($s_order == 5) {  // 5. Exempt Sales (Line 6 of the VAT Return)
                echo ' <div class="box box-info"> <div class="box-header with-border bg-info"> ';
                echo "<b>" . $records[0]['vat_payer_sales_grp'] . "</b>";
                echo ' </div> <div class="box-body"> ';
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead>
                        <tr>
                            <th>VAT Return Field Number</th> 
                            <th>Invoice Number</th>
                            <th>Invoice Date</th>
                            <th>Client VAT Account Number</th>
                            <th>Client Name</th> 
                            <th>Good/Service Description</th>
                            <th>Total BHD <br>(Exclusive of VAT)</th> 
                        </tr>
                    </thead>";
                echo "<tbody>";
                if($records[0]['vat_rtn_fld'] != '') {
                $tot_amt_ex_tax = $tot_vat_amt = $tot_amt_inc_tax = 0;
                foreach ($records as $row) { 
                    echo "<tr>";
                    echo "<td>{$row['vat_rtn_fld']}</td>"; 
                    echo "<td><b>{$row['invoice_no']}</b></td>";
                    echo "<td>".date('d-m-Y', strtotime($row['invoice_date']))."</td>";
                    echo "<td><b>{$row['client_vat_no']}</b></td>";
                    echo "<td><b>{$row['client_name']}</b></td>"; 
                    echo "<td><b>{$row['g_desc']}</b></td>";
                    echo "<td>{$row['tot_amt_ex_tax']}</td>";  
                    echo "</tr>";
                    $tot_amt_ex_tax += $row['tot_amt_ex_tax']; 
                }
                echo "<tr>
                    <th colspan='6' class='text-left'>". $records[0]['vat_payer_sales_grp'] ."</th>
                    <th>".number_format($tot_amt_ex_tax, 3)."</th> 
                </tr>";
                }
                echo "</tbody></table></div></div><br/><br/>";
            }
        }
        ?>
        </div>
    </div>
    <?php  } else { echo "<p>No records found for the selected date range.</p>"; }  ?>

</section>
<!-- /.content -->
<?php  include_once(VIEWPATH . 'inc/footer.php'); ?>