    <!-- bootstrap datepicker -->
<link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
 <!-- bootstrap datepicker -->
<script src="<?php echo base_url() ?>asset/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo base_url() ?>asset/bower_components/excelexport.js"></script>  
<script>
jQuery(function($) { 
   
      
     $('.datepicker').datepicker({
      autoclose: true,
      format : 'yyyy-mm-dd' 
    });
    
    
    $('#frmsearch #srch_account_head_id').change(function() {  
          $("#frmsearch #srch_sub_account_head_id > option").remove();      
          $("#frmsearch #srch_inward_from > option").remove();     
           
          $.ajax({
                url: "<?php echo site_url('get-data');?>",
                type: "post",
                data: { tbl : 'get-sub-account-head', id: $(this).val()  },
                success: function(d) {  
                    var opt1 = $('<option />'); 
                    opt1.val('');
                    opt1.text('All');
                    $('#frmsearch #srch_sub_account_head_id').append(opt1); 
                    
                    var opt1 = $('<option />'); 
                    opt1.val('');
                    opt1.text('All');
                    $('#frmsearch #srch_inward_from').append(opt1); 
			
    				$.each(d,function(key,info) 
	                {
	                    var opt1 = $('<option />'); 
	                    opt1.val(info.sub_account_head_id);
	                    opt1.text(info.sub_account_head_name);
	                    $('#frmsearch #srch_sub_account_head_id').append(opt1); 
	                });
                }
         });  
     }); 
     
      $('#frmsearch #srch_sub_account_head_id').change(function() {  
          $("#frmsearch #srch_inward_from > option").remove();      
          $.ajax({
                url: "<?php echo site_url('get-data');?>",
                type: "post",
                data: { tbl : 'get_sub_account_headlvl3', id: $(this).val()  },
                success: function(d) {  
                    var opt1 = $('<option />'); 
                    opt1.val('');
                    opt1.text('All');
                    $('#frmsearch #srch_inward_from').append(opt1); 
			
    				$.each(d,function(key,info) 
	                {
	                    var opt1 = $('<option />'); 
	                    opt1.val(info.id);
	                    opt1.text(info.val);
	                    $('#frmsearch #srch_inward_from').append(opt1); 
	                });
                }
         });  
     }); 
     
     
      
     $(".btnexp").click(function() {   //alert('hi');
        
        var ee = excelExport("statement").parseToCSV().parseToXLS('Inward Statement'); 
		location.href = ee.getXLSDataURI();
        
	 });
      
      
    
});
</script>