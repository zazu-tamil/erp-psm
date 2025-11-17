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
    
    
     $(".btnexp").click(function() {   //alert('hi');
        
        var ee = excelExport("statement").parseToCSV().parseToXLS('Ledger Account'); 
		location.href = ee.getXLSDataURI();
        
	 });
    
 });
 </script>   