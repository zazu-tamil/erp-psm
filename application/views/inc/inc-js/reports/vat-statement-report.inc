<script>
    $(document).ready(function () {
        // Initialize select2
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2();
        }
    });

    function exportToExcel() {
        var form = $('#frmsearch');
        var originalAction = form.attr('action');
        // Temporarily change action to include export parameter
        form.attr('action', originalAction + '?export=excel');
        form.submit();
        // Restore original action
        form.attr('action', originalAction);
    }
</script>
