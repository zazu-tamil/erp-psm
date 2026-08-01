<!-- Select2 -->
<script src="<?php echo base_url() ?>asset/bower_components/select2/dist/js/select2.full.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/select2/dist/css/select2.min.css">
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<!-- bootstrap datepicker -->
<script src="<?php echo base_url() ?>asset/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo base_url() ?>asset/bower_components/excelexport.js"></script>  
<script>
jQuery(function($) { 
    $(".select2").select2({
        width: "100%"
    });
    $(".btnexp").click(function() {
        var ee = excelExport("statement-table").parseToCSV().parseToXLS('Cash Statement'); 
        location.href = ee.getXLSDataURI();
    });
});
</script>
