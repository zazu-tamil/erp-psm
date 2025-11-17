<?php  include_once(VIEWPATH . '/inc/header.php'); ?>
 <section class="content-header">
  <h1>NA-Outward Statement</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-cubes"></i> Reports</a></li> 
    <li class="active">NA-Outward Statement</li>
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
             <form method="post" action="<?php echo site_url('na-cash-out-statement')?>" id="frmsearch">          
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
                 <div class="form-group col-md-3">
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
                 <div class="form-group col-md-3">
                    <label>Project </label>
                    <?php echo form_dropdown('srch_project_id',array('' => 'All') + $project_opt  ,set_value('srch_project_id', $srch_project_id) ,' id="srch_project_id" class="form-control"');?>                                             
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
        <h4>Outward Statement : [ <?php echo date('d-m-Y', strtotime($srch_from_date));?> to <?php echo date('d-m-Y', strtotime($srch_to_date));?> ]  </h4>     
    </div>
    <div class="box-body table-responsive"> 
       <table class="table table-striped table-bordered table-hover" id="statement" border="1"> 
                    <caption class="hide">NA-Outward Statement : [ <?php echo date('d-m-Y', strtotime($srch_from_date));?> to <?php echo date('d-m-Y', strtotime($srch_to_date));?> ] </caption>
                    <thead>
                        <tr>
                            <th>#</th>  
                            <th>Date</th>
                            <th>V.No</th> 
                            <th>Project</th> 
                            <th>Account Head</th>
                            <th>Sub Account Head</th>
                            <th>Outward To</th>
                            <th>Remarks</th>
                            <th>Bill</th>
                            <th class="text-right">Amount</th> 
                        </tr>
                    </thead>
                    <tbody> 
                      <?php foreach($record_list as $a_type => $rec){  ?>
                        <tr>
                            <th colspan="10">Account Group : <?php echo $a_type ; ?></th>
                        </tr>  
                       <?php
                            $tot_amount = 0;
                           foreach($rec as $j=> $info){ 
                               $tot_amount += $info['amount'];  
                        ?>                               
                        <tr>
                            <td><?php echo ($this->uri->segment(2, 0) + ($j+1));?></td>
                            <td><?php echo date('d-m-Y', strtotime($info['outward_date']));?></td>
                            <td><?php echo $info['vno']?></td> 
                            <td><?php echo $info['project_name']?></td> 
                            <td><?php echo $info['account_head']?></td> 
                            <td><?php echo $info['sub_account_head']?></td> 
                            <td><?php echo $info['outward_for']?></td> 
                            <td><?php echo $info['remarks']?></td> 
                            <td><?php if(!empty($info['bill_photo'])) echo "<a href='". base_url($info['bill_photo']) ."' target='_blank'>View</a>"; ?></td>
                            <td class="text-right"><i class="fa fa-rupee"></i> <?php echo number_format($info['amount'],2)?></td>   
                        <?php } ?>
                        <tr>
                            <th colspan="9" class="text-right">Total</th>
                            <th class="text-right"><i class="fa fa-rupee"></i> <?php echo number_format($tot_amount,2)?></th>  
                        </tr>
                        <?php } ?>
                    </tbody> 
                </table>  
        
                 
        <?php if(!empty($record_list)) { ?>        
            <div class="text-center"><button class="btn btn-info btnexp" >Export To Excel File</button></div>
         <?php } ?>
        
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
