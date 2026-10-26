<!-- bootstrap datepicker -->
<link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<!-- bootstrap datepicker -->
<script src="<?php echo base_url() ?>asset/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo base_url() ?>asset/bower_components/excelexport.js"></script>  
<script>
jQuery(function($) { 
    $(".btnexp").click(function() {
        var ee = excelExport("statement-table").parseToCSV().parseToXLS('Cash In & Out Statement'); 
        location.href = ee.getXLSDataURI();
    });

    var bankOpDates = <?php echo !empty($bank_op_dates) ? json_encode($bank_op_dates) : '{}'; ?>;

    $('#srch_bank_cash').on('change', function() {
        var selected = $(this).val();
        var opDate = bankOpDates[selected] || bankOpDates['general'] || bankOpDates['all'] || '';
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
