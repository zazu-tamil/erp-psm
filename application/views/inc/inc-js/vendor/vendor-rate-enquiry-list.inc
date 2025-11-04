<script>
jQuery(function ($) {
    // Delete Confirmation
    $('.del_record').click(function () {
        if (confirm('Are you sure you want to mark this vendor rate enquiry as DELETED?\n\nIt will no longer appear in active lists.')) {
            const id = $(this).val();
            $.post("<?php echo site_url('vendor/delete_record'); ?>", {
                tbl: 'vendor_rate_enquiry_info',
                id: id
            }, function (res) {
                alert(res);
                location.reload();
            }).fail(function () {
                alert('Error processing request.');
            });
        }
    });
});
</script>