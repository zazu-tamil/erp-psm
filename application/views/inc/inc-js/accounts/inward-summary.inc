<script>
jQuery(function($) { 
   
    
    
    $('.btn_breakup').click(function() {  
           
         $.ajax({
                    url: "<?php echo site_url('get-content');?>",
                    type: "post",
                    data: { tbl : 'inward-breakup-summary', id: $(this).attr('data-id'), mon: $(this).attr('data-mon'), ac_type: $(this).attr('data-ac-type')  ,edit_mode : 0, del_mode : 0},
                    success: function(ctnt) {                                 
                         
                        $('#view_modal .modal-body .master').html(ctnt) ; 
                        
                    }
          });  
           
     }); 
      
    
    
    
});
</script>