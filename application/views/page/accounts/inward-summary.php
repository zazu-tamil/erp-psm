<?php  include_once(VIEWPATH . '/inc/header.php'); ?>
 <section class="content-header">
  <h1>Inward Summary</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-cubes"></i> Reports</a></li> 
    <li class="active">Inward Summary</li>
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
             <form method="post" action="<?php echo site_url('inward-summary')?>" id="frmsearch">          
             <div class="row">   
                 <div class="form-group col-md-3"> 
                    <label>From Month</label>
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="month" class="form-control pull-right " id="srch_from_date" name="srch_from_date" value="<?php echo set_value('srch_from_date',$srch_from_date);?>" required="true">
                    </div>
                    <!-- /.input group -->                                             
                 </div> 
                 <div class="form-group col-md-3"> 
                    <label>To Month</label>
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="month" class="form-control pull-right " id="srch_to_date" name="srch_to_date" value="<?php echo set_value('srch_to_date',$srch_to_date);?>" required="true">
                    </div>
                    <!-- /.input group -->                                             
                 </div>
                 <div class="form-group col-md-4">
                    <label>Account Group</label>
                    <?php echo form_dropdown('srch_ac_type',array('' => 'All') + $ac_type_opt ,set_value('srch_ac_type',$srch_ac_type) ,' id="srch_ac_type" class="form-control"');?>                                             
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
        <h4>Inward Summary : [ <?php echo date('M-Y', strtotime($srch_from_date));?> to <?php echo date('M-Y', strtotime($srch_to_date));?> ]  </h4>     
    </div>
    <div class="box-body table-responsive"> 
        <div class="row">
                <?php foreach($inward_rec as $ac_typ => $info) {?>
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header">
                            <h3 class="box-title">
                               Account Group : <?php echo $ac_typ ; ?>
                          </h3> 
                          <i class="badge pull-right">Inward</i>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th>#</th> 
                                    <th>Account Head</th>
                                    <?php $tot =  array(); foreach($ap_mon as $n_mon => $mon) { $tot[$n_mon] = 0; ?>
                                    <th class="text-right"><?php echo $mon ; ?></th>
                                    <?php }   ?>
                                    <th class="text-right">Total</th>
                                </tr>
                                <?php
                                
                                    //print_r($tot);
                                    $i=1; $htot = 0;
                                   foreach($info as $head => $ls){  
                                 ?> 
                                <tr>
                                    
                                    <td><?php echo $i; ?></td>  
                                    <td><?php echo $head;   ?></td>  
                                    <?php foreach($ap_mon as $n_mon => $mon) {?>
                                    <?php   
                                        if(isset($ls[$mon]['inward'])){
                                        $tot[$n_mon] += $ls[$mon]['inward'];
                                        $htot += $ls[$mon]['inward'];
                                        }
                                    ?>
                                    <td class="text-right"><span data-toggle="modal" data-target="#view_modal"  class="btn_breakup" data-ac-type="<?php echo $ac_typ ; ?>" data-id="<?php if(isset($ls[$mon]['id'])) echo $ls[$mon]['id'];?>" data-mon="<?php if(isset($ls[$mon]['num_mon'])) echo $ls[$mon]['num_mon'];?>"><?php if(isset($ls[$mon]['inward'])) echo number_format($ls[$mon]['inward'],2); else echo '0.00';?></span></td> 
                                    <?php } ?> 
                                    <td class="text-right"> <?php echo number_format($htot,2); ?></td> 
                                </tr>
                                <?php $i++; $htot = 0; } ?>
                                <tr>
                                    <th class="text-right" colspan="2">Total</th>
                                     <?php $tot_sum = 0; foreach($ap_mon as $n_mon => $mon) { $tot_sum += $tot[$n_mon]; ?>
                                    <th class="text-right"> <?php echo number_format($tot[$n_mon],2);?></th>
                                    <?php }   ?>  
                                    <th class="text-right"> <?php echo number_format($tot_sum,2);?></th>
                                </tr>
                            </table>
                        </div>
                        <div class="box-footer no-border">
                        </div>
                    </div>
                </div>
                <?php } ?> 
            </div>
        
    </div>
    <!-- /.box-body -->
    
    <div class="modal fade" id="view_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content"> 
                <div class="modal-header">
                    
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> 
                    <h3 class="modal-title" id="scrollmodalLabel"><strong>View Details</strong></h3>
                </div>
                <div class="modal-body table-responsive"> 
                    <span class="master"></span> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>  
                </div>  
            </div>
        </div>
    </div> 
     
  </div>
  <!-- /.box -->
  <?php
	}
?>

</section>
<!-- /.content -->
<?php  include_once(VIEWPATH . 'inc/footer.php'); ?>
