<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
jQuery(function ($) {

     if ($('#cash_category_table').length) {
        $('#cash_category_table').DataTable({
            "order": [[1, "asc"]],
            "pageLength": 25,
            "language": {
                "search": "",
                "searchPlaceholder": "Search categories..."
            }
        });
    }

    // ── Edit Record ───────────────────────────────────────────────
    $(document).on('click', '.edit_record', function () {
        var id = $(this).val();
        $.ajax({
            url: "<?php echo site_url('get-data'); ?>",
            type: 'post',
            data: { tbl: 'cash_category', id: id },
            dataType: 'json',
            success: function (d) {
                console.log(d);
                $('#edit_cash_category_id').val(d.cash_category_id);
                $('#edit_category_name').val(d.category_name);
                $('#edit_description').val(d.description);

                // Set category_type dropdown
                $('#edit_category_type').val(d.category_type);

                // Set status radio
                $('#edit_modal input[name="status"]').prop('checked', false);
                $('#edit_modal input[name="status"][value="' + d.status + '"]').prop('checked', true);

                $('#edit_modal').modal('show');
            },
            error: function (xhr) {
                console.error('AJAX Error:', xhr.responseText);
                alert('Failed to load record. Please try again.');
            }
        });
    });

    // ── Delete Record ─────────────────────────────────────────────
    $(document).on('click', '.del_record', function () {
        var id = $(this).val();
        if (confirm('Are you sure you want to delete this cash category?\nThis cannot be undone.')) {
            $.ajax({
                url: "<?php echo site_url('delete-record'); ?>",
                type: 'post',
                data: { tbl: 'cash_category', id: id },
                success: function (d) {
                    alert(d);
                    location.reload();
                },
                error: function (xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                    alert('Delete failed. Please try again.');
                }
            });
        }
    });

});
</script>
