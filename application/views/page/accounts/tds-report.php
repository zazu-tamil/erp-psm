<?php  include_once(VIEWPATH . '/inc/header.php'); ?>
 <section class="content-header">
  <h1>TDS Report</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-cubes"></i> Reports</a></li> 
    <li><a href="#"><i class="fa fa-cubes"></i> Auditing</a></li> 
    <li class="active">TDS Report</li>
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
             <form method="post" action="<?php echo site_url('tds-report')?>" id="frmsearch">          
             <div class="row">   
                 <div class="form-group col-md-3"> 
                    <label>From Date</label>
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="date" class="form-control pull-right " id="srch_from_date" name="srch_from_date" value="<?php echo set_value('srch_from_date',$srch_from_date);?>" min="<?php echo $min_date ?>"  max="<?php echo $max_date ?>" required="true">
                    </div>
                    <!-- /.input group -->                                             
                 </div> 
                 <div class="form-group col-md-3"> 
                    <label>To Date</label>
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="date" class="form-control pull-right " id="srch_to_date" name="srch_to_date" value="<?php echo set_value('srch_to_date',$srch_to_date);?>" required="true">
                    </div>
                    <!-- /.input group -->                                             
                 </div>
                 <div class="form-group col-md-4">
                    <label>Account Group</label>
                    <?php echo form_dropdown('srch_ac_type',array('' => 'All') + $ac_type_opt ,set_value('srch_ac_type',$srch_ac_type) ,' id="srch_ac_type" class="form-control"');?>                                             
                 </div>
                 <div class="form-group col-md-3">
                    <label>Account Head</label>
                    <?php echo form_dropdown('srch_account_head_id',array('' => 'All') + $account_head_opt ,set_value('srch_account_head_id',$srch_account_head_id) ,' id="srch_account_head_id" class="form-control"');?>                                             
                 </div>
                 <div class="form-group col-md-3">
                    <label>Sub Account Head</label>
                    <?php echo form_dropdown('srch_sub_account_head_id',array('' => 'All') + $sub_account_head_opt ,set_value('srch_sub_account_head_id', $srch_sub_account_head_id) ,' id="srch_sub_account_head_id" class="form-control"');?>                                             
                 </div>
                 <div class="form-group col-md-3">
                    <label>Outward To</label>
                    <?php echo form_dropdown('srch_outward_for',array('' => 'All') + $outward_for_opt  ,set_value('srch_outward_for', $srch_outward_for) ,' id="srch_outward_for" class="form-control"');?>                                             
                 </div>
                <div class="form-group col-md-2 text-left">
                    <br />
                    <button class="btn btn-success" name="btn_show" value="Show'"><i class="fa fa-search"></i> Show</button>
                </div>
             </div>  
            </form>
         </div> 
     </div>  
     <?php
	 //if(!empty($srch_agent_id)) 
     {
    ?>   
  <div class="box box-info">
    <div class="box-header with-border">
        <h4>TDS Report : [ <?php echo date('d-m-Y', strtotime($srch_from_date));?> to <?php echo date('d-m-Y', strtotime($srch_to_date));?> ]  </h4>     
    </div>
    <div class="box-body table-responsive"> 
       <form method="post" action="<?php echo site_url('tds-report')?>" id="frm_tds">  
       <input type="hidden" name="mode" value="Update TDS" /> 
       <table class="table table-striped table-bordered table-hover" id="statement" border="1"> 
            <caption class="hide">Outward Statement : [ <?php echo date('d-m-Y', strtotime($srch_from_date));?> to <?php echo date('d-m-Y', strtotime($srch_to_date));?> ] </caption>
            <thead>
                <tr>
                    <th>#</th>  
                    <th>Date & <br />V.No</th> 
                    <th>Account &<br /> Sub Account Head</th>
                    <th>Outward To</th>
                    <th>Remarks</th> 
                    <th class="text-right">Amount</th> 
                    <th>TDS % & Amount</th>
                </tr>
            </thead>
            <tbody> 
              <?php foreach($record_list as $a_type => $rec){  ?>
                <tr>
                    <th colspan="8">Account Group : <?php echo $a_type ; ?></th>
                </tr>  
               <?php
                    $tot_amount = $tot_tds = 0;
                   foreach($rec as $j=> $info){ 
                       $tot_amount += $info['amount'];  
                       $tot_tds += $info['tds_amt'];  
                ?>                               
                <tr>
                    <td><?php echo ($this->uri->segment(2, 0) + ($j+1));?></td>
                    <td><?php echo date('d-m-Y', strtotime($info['outward_date']));?><br /><?php echo $info['vno']?></td>
                     <td><?php echo $info['account_head']?><br /><?php echo $info['sub_account_head']?></td> 
                    <td><?php echo $info['outward_for']?></td> 
                    <td><?php echo $info['remarks']?></td> 
                    <td class="text-right"><?php echo number_format($info['amount'],2)?></td>  
                    <td class="text-center">
                        <input type="hidden" name="cash_outward_id[]" id="cash_outward_id_<?php echo $info['cash_outward_id']?>" value="<?php echo $info['cash_outward_id']?>" />
                        <input type="hidden" name="exp_amt[<?php echo $info['cash_outward_id']?>]" id="exp_amt_<?php echo $info['cash_outward_id']?>" value="<?php echo $info['amount']?>" />
                        <?php echo form_dropdown('tds_prt['. $info['cash_outward_id'] .']',$tds_prt_opt ,set_value('tds_prt',$info['tds_prt']),'class="form-control tds_prt" id="tds_prt_'. $info['cash_outward_id'] .'" ');?>
                        <br />
                        <input type="number" class="form-control text-right" name="tds_amt[<?php echo $info['cash_outward_id']?>]" id="tds_amt_<?php echo $info['cash_outward_id']?>" value="<?php echo $info['tds_amt']?>" readonly="true"  />
                         
                        <i class="label bg-purple"><?php echo $info['tds_updt_by']?></i>
                   </td> 
                    
                <?php } ?>
                <tr>
                    <th colspan="5" class="text-right">Total</th>
                    <th class="text-right"><?php echo number_format($tot_amount,2)?></th>  
                    <th class="text-right"><?php echo number_format($tot_tds,2)?></th>  
                    
                </tr>
                <tr>
                    <th colspan="3"><label>TDS Entry By</label></th>
                    <th colspan="2"> 
                        <input type="text" class="form-control col-md-4" name="tds_updt_by" id="tds_updt_by" value="" />
                    </th>
                    <th colspan="2"> 
                        <button class="btn btn-sm btn-info" type="submit" name="btn_save">Update</button>
                    </th>
                </tr>
                <?php } ?>
            </tbody> 
        </table>  
        </form>        
                 
        <?php /*if(!empty($record_list)) { ?>        
            <div class="text-center"><button class="btn btn-info btnexp" >Export To Excel File</button></div>
         <?php } */ ?>
        
    </div>
    <!-- /.box-body -->
     
  </div>
  <!-- /.box -->
  <?php
	}
?>

</section>
<!-- /.content -->
<?php  include_once(VIEWPATH . 'inc/footer.php'); ?>
