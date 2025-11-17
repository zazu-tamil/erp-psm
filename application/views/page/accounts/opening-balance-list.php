<?php  include_once(VIEWPATH . '/inc/header.php'); ?>
 <section class="content-header">
  <h1>Opening Balance List</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-cubes"></i> Accounts</a></li> 
    <li class="active">Opening Balance List</li>
  </ol>
</section>
<!-- Main content -->
<section class="content"> 
  <!-- Default box -->
   
  <div class="box box-info">
    <div class="box-header with-border">
      <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#add_modal"><span class="fa fa-plus-circle"></span> Add New </button>
        
       
    </div>
    <div class="box-body table-responsive"> 
       <table class="table table-hover table-bordered table-striped" id="opening_balance_list">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Date</th>  
                <th>Account Group</th>  
                <th>Amount</th>  
                <th class="text-center">Edit</th>  
                <th class="text-center">Delete</th>  
            </tr>
        </thead>
          <tbody>
               <?php
                   foreach($record_list as $j=> $ls){
                ?> 
                <tr> 
                    <td class="text-center"><?php echo ($j + 1 );?></td> 
                    <td><?php echo date('d-m-Y', strtotime($ls['opening_date']))?></td>   
                    <td><?php echo $ls['ac_type']?></td>   
                    <td><?php echo $ls['amount']?></td>    
                    <td class="text-center">
                        <button data-toggle="modal" data-target="#edit_modal" value="<?php echo $ls['opening_balance_id']?>" class="edit_record btn btn-primary btn-xs" title="Edit"><i class="fa fa-edit"></i></button>
                    </td>                                  
                    <td class="text-center">
                        <button value="<?php echo $ls['opening_balance_id']?>" class="del_record btn btn-danger btn-xs" title="Delete"><i class="fa fa-remove"></i></button>
                    </td>                                      
                </tr>
                <?php
                    }
                ?>                                 
            </tbody>
      </table>
        
                <div class="modal fade" id="add_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <form method="post" action="" id="frmadd">
                            <div class="modal-header">
                                
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel">Add Opening Balance Info</h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>
                            <div class="modal-body">
                                 <div class="form-group">
                                    <label>Date</label>
                                    <input class="form-control" type="date" name="opening_date" id="opening_date"  value="">                                             
                                 </div> 
                                 <div class="form-group">
                                    <label>Account Group</label>
                                    <?php echo form_dropdown('ac_type',array('' => 'Select') + $ac_type_opt ,set_value('ac_type') ,' id="ac_type" class="form-control" required="true"');?>                                             
                                 </div>
                                 <div class="form-group">
                                    <label>Amount</label>
                                    <input class="form-control" type="number" name="amount" id="amount" placeholder="Amount" value="">                                             
                                 </div>  
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> 
                                <input type="submit" name="Save" value="Save"  class="btn btn-primary" />
                            </div> 
                            </form>
                        </div>
                    </div>
                </div> 
                
                <div class="modal fade" id="edit_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <form method="post" action="" id="frmedit">
                            <div class="modal-header">
                                <h5 class="modal-title" id="scrollmodalLabel">Edit Opening Balance Info</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="opening_balance_id" id="opening_balance_id" />
                            </div>
                            <div class="modal-body"> 
                                <div class="form-group">
                                    <label>Date</label>
                                    <input class="form-control" type="date" name="opening_date" id="opening_date"  value="">                                             
                                 </div> 
                                 <div class="form-group">
                                    <label>Account Group</label>
                                    <?php echo form_dropdown('ac_type',array('' => 'Select') + $ac_type_opt ,set_value('ac_type') ,' id="ac_type" class="form-control" required="true"');?>                                             
                                 </div>
                                 <div class="form-group">
                                    <label>Amount</label>
                                    <input class="form-control" type="number" name="amount" id="amount" placeholder="Amount" value="">                                             
                                 </div> 
                                 
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> 
                                <input type="submit" name="Save" value="Update"  class="btn btn-primary" />
                            </div> 
                            </form>
                        </div>
                    </div>
                </div>
        
        
    </div>
     
  </div>
  <!-- /.box -->

</section>
<!-- /.content -->
<?php  include_once(VIEWPATH . 'inc/footer.php'); ?>
