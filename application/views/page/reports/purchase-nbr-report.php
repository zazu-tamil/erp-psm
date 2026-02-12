<?php  include_once(VIEWPATH . '/inc/header.php'); 
// echo "<pre>";
// print_r($record_list);  
// echo "</pre>";
?>
<section class="content-header">
    <h1>Purchase NBR Report</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Reports</a></li>
        <li class="active">Purchase NBR Report</li>
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
            <form method="post" action="<?php echo site_url('purchase-nbr-report')?>" id="frmsearch">
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
            <h3 class="box-title text-white">Purchase NBR Report [
                <?php echo date('d-m-Y', strtotime($srch_from_date)) . ' to ' . date('d-m-Y', strtotime($srch_to_date)) ; ?>
                ]</h3>
        </div>
        <div class="box-body">
            <?php 
        foreach ($record_list as $s_order => $records) {
            if($s_order == 1 || $s_order == 2 ) {  // 1. Standard Rated Domestic Purchases at 5% (Line 8 of the VAT Return) , 2. Standard Rated Domestic Purchases at 10% (Line 8 of the VAT Return)
                 echo ' <div class="box box-info"> <div class="box-header with-border bg-info"> ';
                echo "<b>" . $records[0]['vat_payer_purchase_grp'] . "</b>";
                echo ' </div> <div class="box-body"> ';
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead>
                        <tr>
                            <th>VAT Return Field Number</th> 
                            <th>Invoice Number</th>
                            <th>Invoice Date</th>
                            <th>VAT Account Number</th>
                            <th>Supplier Name</th> 
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
                    echo "<td><b>{$row['supplier_vat_no']}</b></td>";
                    echo "<td><b>{$row['supplier_name']}</b></td>"; 
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
                    <th colspan='6' class='text-left'>". $records[0]['vat_payer_purchase_grp'] ."</th>
                    <th>".number_format($tot_amt_ex_tax, 3)."</th>
                    <th>".number_format($tot_vat_amt, 3)."</th>
                    <th>".number_format($tot_amt_inc_tax, 3)."</th>
                </tr>";
                }
                echo "</tbody></table></div></div><br/><br/>";
            }

            if($s_order == 3 || $s_order == 4) {  // 3. Import subject to VAT paid at customs (Line 9 of the VAT Return) , 4. Imports subject to deferral at customs (Line 10 of the VAT Return)
                 echo ' <div class="box box-info"> <div class="box-header with-border bg-info"> ';  
                echo "<b>" . $records[0]['vat_payer_purchase_grp'] . "</b>";
                echo ' </div> <div class="box-body"> ';
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead>
                        <tr>
                            <th>VAT Return Field Number</th> 
                            <th>Declaration Number</th>
                            <th>Declaration Date</th>
                            <th>Declaration Payment Date</th> 
                            <th>Invoice Date</th> 
                            <th>Supplier Name</th> 
                            <th>Good/Service Description</th>
                            <th>CIF Value (BHD)</th>
                            <th>VAT Amount Paid Per Customs <br> Affairs Receipt</th> 
                        </tr>
                        </tr>
                    </thead>";
                echo "<tbody>";
                if($records[0]['vat_rtn_fld'] != '') {
                $tot_amt_ex_tax = $tot_vat_amt = $tot_amt_inc_tax = 0;
                foreach ($records as $row) { 
                    echo "<tr>";
                    echo "<td>{$row['vat_rtn_fld']}</td>"; 
                    echo "<td>{$row['declaration_no']}</td>";
                    echo "<td>".date('d-m-Y', strtotime($row['declaration_date']))."</td>";
                    echo "<td>".date('d-m-Y', strtotime($row['declaration_date']))."</td>"; 
                    echo "<td>".date('d-m-Y', strtotime($row['invoice_date']))."</td>"; 
                    echo "<td><b>{$row['supplier_name']}</b></td>"; 
                    echo "<td><b>{$row['g_desc']}</b></td>";
                    echo "<td>{$row['tot_amt_ex_tax']}</td>";  
                    echo "<td>{$row['tot_amt_ex_tax']}</td>";  
                    echo "</tr>";
                    $tot_amt_ex_tax += $row['tot_amt_ex_tax']; 
                }
                echo "<tr>
                    <th colspan='7' class='text-left'>". $records[0]['vat_payer_purchase_grp'] ."</th>
                    <th>".number_format($tot_amt_ex_tax, 3)."</th> 
                    <th>".number_format($tot_amt_ex_tax, 3)."</th> 
                </tr>";
                }
                echo "</tbody></table></div></div><br/><br/>";
            }

            if($s_order == 5 || $s_order == 6) {  // 5. Import subject to VAT accounted for through reverse charge mechanism at 5% (Line 11 of the VAT Return), 6.  Import subject to VAT accounted for through reverse charge mechanism at 10% (Line 11 of the VAT Return)
                
                 echo ' <div class="box box-info"> <div class="box-header with-border bg-info"> ';  
                echo "<b>" . $records[0]['vat_payer_purchase_grp'] . "</b>";
                echo ' </div> <div class="box-body"> ';
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead>
                        <tr>
                            <th>VAT Return Field Number</th> 
                            <th>Invoice Number</th>
                            <th>Invoice Date</th>
                            <th>Supplier Name</th>
                            <th>Supplier's country of origin</th>
                            <th>Good/Service Description</th>
                            <th>Total BHD</th>
                        </tr>
                    </thead>";
                echo "<tbody>";
                if($records[0]['vat_rtn_fld'] != '') {
                $tot = 0;
                foreach ($records as $row) { 
                    echo "<tr>";
                    echo "<td>{$row['vat_rtn_fld']}</td>"; 
                    echo "<td>{$row['invoice_no']}</td>";
                    echo "<td>".date('d-m-Y', strtotime($row['invoice_date']))."</td>";
                    echo "<td>{$row['supplier_name']}</td>";
                    echo "<td>{$row['country_code']}</td>";
                    echo "<td>{$row['g_desc']}</td>";
                    echo "<td>{$row['tot_amt_inc_tax']}</td>"; 
                    echo "</tr>";
                    $tot += $row['tot_amt_inc_tax'];
                }
                echo "<tr><th colspan='6' class='text-left'>". $records[0]['vat_payer_purchase_grp'] ."</th><th>".number_format($tot, 3)."</th></tr>";
                }
                echo "</tbody></table></div></div><br/><br/>";
            }

            if($s_order == 7) {  // 7.Purchases subject to domestic reverse charge mechanism (Line 12 of the VAT Return)
                echo ' <div class="box box-info"> <div class="box-header with-border bg-info"> ';
                echo "<b>" . $records[0]['vat_payer_purchase_grp'] . "</b>";
                echo ' </div> <div class="box-body"> ';
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead>
                        <tr>
                            <th>VAT Return Field Number</th> 
                            <th>Invoice Number</th>
                            <th>Invoice Date</th>
                            <th>CR Number</th>
                            <th>VAT Account Number</th>
                            <th>Supplier Name</th> 
                            <th>Good/Service Description</th>
                            <th>Total BHD</th> 
                        </tr>
                    </thead>";
                echo "<tbody>";
                if($records[0]['vat_rtn_fld'] != '') {
                 $tot_amt_inc_tax = 0;
                foreach ($records as $row) { 
                    echo "<tr>";
                    echo "<td>{$row['vat_rtn_fld']}</td>"; 
                    echo "<td><b>{$row['invoice_no']}</b></td>";
                    echo "<td>".date('d-m-Y', strtotime($row['invoice_date']))."</td>";
                    echo "<td><b>{$row['crno']}</b></td>";
                    echo "<td><b>{$row['client_vat_no']}</b></td>";
                    echo "<td><b>{$row['client_name']}</b></td>"; 
                    echo "<td><b>{$row['g_desc']}</b></td>";
                    echo "<td>{$row['tot_amt_inc_tax']}</td>";  
                    echo "</tr>";
                    $tot_amt_inc_tax += $row['tot_amt_inc_tax']; 
                }
                echo "<tr>
                    <th colspan='7' class='text-left'>". $records[0]['vat_payer_purchase_grp'] ."</th>
                    <th>".number_format($tot_amt_inc_tax, 3)."</th> 
                </tr>";
                }
                echo "</tbody></table></div></div><br/><br/>";
            }

            if($s_order == 8) {  // 8.Purchases from non-register taxpayers, zero-rated/ exempt purchases (Line 13 of the VAT Return)
                echo ' <div class="box box-info"> <div class="box-header with-border bg-info"> ';
                echo "<b>" . $records[0]['vat_payer_purchase_grp'] . "</b>";
                echo ' </div> <div class="box-body"> '; 
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead>
                        <tr>
                            <th>VAT Return Field Number</th> 
                            <th>Invoice Number</th>
                            <th>Invoice Date</th>
                            <th>CR Number</th>
                            <th>VAT Account Number</th>
                            <th>Supplier Name</th> 
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
                    echo "<td><b>{$row['crno']}</b></td>";
                    echo "<td><b>{$row['client_vat_no']}</b></td>";
                    echo "<td><b>{$row['client_name']}</b></td>"; 
                    echo "<td><b>{$row['g_desc']}</b></td>";
                    echo "<td>".number_format($row['tot_amt_ex_tax'], 3)."</td>";  
                    echo "<td>".number_format($row['vat_amt'], 3)."</td>";  
                    echo "<td>".number_format($row['tot_amt_inc_tax'], 3)."</td>";  
                    echo "</tr>";
                    $tot_amt_ex_tax += $row['tot_amt_ex_tax']; 
                    $tot_vat_amt += $row['vat_amt']; 
                    $tot_amt_inc_tax += $row['tot_amt_inc_tax']; 
                }
                echo "<tr>
                    <th colspan='7' class='text-left'>". $records[0]['vat_payer_purchase_grp'] ."</th>
                    <th>".number_format($tot_amt_ex_tax, 3)."</th> 
                    <th>".number_format($tot_vat_amt, 3)."</th> 
                    <th>".number_format($tot_amt_inc_tax, 3)."</th> 
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