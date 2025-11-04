<script>
jQuery(function ($) {
    // Delete Confirmation
    $('.del_record').click(function () {
        if (confirm('Are you sure you want to delete this tender quotation?')) {
            const id = $(this).val();
            $.post("<?php echo site_url('tender/delete_record'); ?>", {
                tbl: 'tender_quotation_info',
                id: id
            }, function (res) {
                alert(res);
                location.reload();
            }).fail(function () {
                alert('Error deleting record.');
            });
        }
    });
});
</script>