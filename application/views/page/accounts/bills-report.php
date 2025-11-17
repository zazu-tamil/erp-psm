<?php  include_once(VIEWPATH . '/inc/header.php'); ?>
 <section class="content-header">
  <h1>Bill Verification Report</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-cubes"></i> Reports</a></li> 
    <li><a href="#"><i class="fa fa-cubes"></i> Auditing</a></li> 
    <li class="active">Bills Verification Report</li>
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
             <form method="post" action="<?php echo site_url('bills-report')?>" id="frmsearch">          
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
        <h4>Bills Verification  Report : [ <?php echo date('d-m-Y', strtotime($srch_from_date));?> to <?php echo date('d-m-Y', strtotime($srch_to_date));?> ]  </h4>     
    </div>
    <div class="box-body table-responsive"> 
        
       <table class="table table-striped table-bordered table-hover" id="statement" border="1"> 
             <thead>
                <tr>
                    <th>#</th>  
                    <th>Date & <br />V.No</th> 
                    <th>Account &<br /> Sub Account Head</th>
                    <th>Outward To</th>
                    <th>Remarks</th> 
                    <th>Photo</th> 
                    <th class="text-right">Amount</th> 
                    <th colspan="2">Bill Status</th>
                </tr>
            </thead>
            <tbody> 
              <?php foreach($record_list as $a_type => $rec){  ?>
                <tr>
                    <th colspan="9">Account Group : <?php echo $a_type ; ?></th>
                </tr>  
               <?php
                    $tot_amount = $tot_tds = 0;
                   foreach($rec as $j=> $info){ 
                       $tot_amount += $info['amount'];   
                ?>                               
                <tr>
                    <td><?php echo ($this->uri->segment(2, 0) + ($j+1));?></td>
                    <td><?php echo date('d-m-Y', strtotime($info['outward_date']));?><br /><?php echo $info['vno']?></td>
                     <td><?php echo $info['account_head']?><br /><?php echo $info['sub_account_head']?></td> 
                    <td><?php echo $info['outward_for']?></td> 
                    <td><?php echo $info['remarks']?></td> 
                    <td><?php if(!empty($info['bill_photo'])) echo "<a href='". base_url($info['bill_photo']) ."' target='_blank'>View</a>"; ?></td>
                    <td class="text-right"><?php echo number_format($info['amount'],2)?></td>  
                    <td>
                        <?php echo $info['bill_status']?><br />
                        <i class="text-blue"><?php echo $info['bill_remarks']?></i> 
                         
                    </td>  
                    <td>
                        <button type="button" 
                            data-toggle="modal" 
                            data-target="#upd_modal" 
                            class="btn btn-sm btn_verify btn-info" 
                            name="btn_verify" 
                            value="<?php echo $info['cash_outward_id']?>" 
                            data-msg="<?php echo $info['outward_for']?> - <?php echo $info['remarks']?>">
                            <i class="fa fa-save"></i>
                        </button>
                    </td>
                <?php } ?>
                <tr>
                    <th colspan="5" class="text-right">Total</th>
                    <th class="text-right"><?php echo number_format($tot_amount,2)?></th>  
                </tr>
               
                <?php } ?>
            </tbody> 
        </table>        
                 
        <?php /*if(!empty($record_list)) { ?>        
            <div class="text-center"><button class="btn btn-info btnexp" >Export To Excel File</button></div>
         <?php } */ ?>
        
    </div>
    <!-- /.box-body -->
    
    
     
  </div>
  <!-- /.box -->
  
  <div class="modal fade" id="upd_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form method="post" action="" id="frmadd">
            <div class="modal-header">
                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="scrollmodalLabel">Bill Verification Info</h3>
                <input type="hidden" name="mode" value="Update Bills Verification" /> 
                <input type="hidden" name="cash_outward_id" id="cash_outward_id" value="" /> 
            </div>
            <div class="modal-body">
                 <div class="form-group">
                    <label>Record Info</label>
                    <div class="msg"></div>
                 </div>   
                 <div class="form-group">
                    <label>Bill Status</label>
                    <div class="radio">
                        <label>
                            <input type="radio" name="bill_status"  value="Verified" checked="true" /> Verified 
                        </label> 
                    </div>
                    <div class="radio">
                        <label>
                             <input type="radio" name="bill_status"  value="Doubt"  /> Doubt
                        </label>
                    </div> 
                    <div class="radio">
                        <label>
                             <input type="radio" name="bill_status"  value="Clarified"  /> Clarified
                        </label>
                    </div> 
                 </div>
                 
                 <div class="form-group">
                    <label>Bill Remarks</label>
                    <?php  echo form_textarea('bill_remarks','','id="bill_remarks" class="form-control"');?> 
                 </div>
                 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> 
                <input type="submit" name="Save" value="Updated"  class="btn btn-primary" />
            </div> 
            </form>
        </div>
    </div>
</div> 
  <?php
	}
?>

</section>
<!-- /.content -->
<?php  include_once(VIEWPATH . 'inc/footer.php'); ?>
