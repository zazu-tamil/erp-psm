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

    var cashOpDates = <?php echo !empty($cash_op_dates) ? json_encode($cash_op_dates) : '{}'; ?>;

    $('#srch_cash_category_id').on('change', function() {
        var selected = $(this).val();
        var key = selected ? ('cat_' + selected) : 'all';
        var opDate = cashOpDates[key] || cashOpDates['general'] || cashOpDates['all'] || '';
        if (opDate) {
            $('#srch_from_date, #srch_to_date').attr('min', opDate);
            var currentFrom = $('#srch_from_date').val();
            if (!currentFrom || currentFrom < opDate) {
                $('#srch_from_date').val(opDate);
            }
            var currentTo = $('#srch_to_date').val();
            if (!currentTo || currentTo < opDate) {
                $('#srch_to_date').val(opDate);
            }
        } else {
            $('#srch_from_date, #srch_to_date').removeAttr('min');
        }
    });
});
</script>
