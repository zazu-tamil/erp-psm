<script>
jQuery(function ($) {
    var SAVE_URL = "<?php echo site_url('role-management/save'); ?>";
    var DELETE_URL = "<?php echo site_url('role-management/delete'); ?>";

    $('#btn_add_role').on('click', function () {
        $('#roleModalLabel').text('Add Role');
        $('#r_mode').val('Add');
        $('#r_role_id').val('');
        $('#r_role_name').val('');
        $('#r_role_key').val('');
        $('#r_is_default').prop('checked', false);
        $('#r_status').val('Active');
        $('#role_modal').modal('show');
    });

    $(document).on('click', '.edit-role', function () {
        $('#roleModalLabel').text('Edit Role');
        $('#r_mode').val('Edit');
        $('#r_role_id').val($(this).data('id'));
        $('#r_role_name').val($(this).data('name'));
        $('#r_role_key').val($(this).data('key'));
        $('#r_is_default').prop('checked', parseInt($(this).data('default'), 10) === 1);
        $('#r_status').val($(this).data('status'));
        $('#role_modal').modal('show');
    });

    $('#role_form').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: SAVE_URL,
            type: 'POST',
            data: $('#role_form').serialize(),
            dataType: 'json',
            success: function (d) {
                if (d.success) {
                    location.reload();
                } else {
                    alert(d.msg || 'Failed to save.');
                }
            },
            error: function () { alert('Server error while saving.'); }
        });
    });

    $(document).on('click', '.del-role', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');
        if (!confirm('Delete role "' + name + '"?')) return;
        $.ajax({
            url: DELETE_URL + '/' + id,
            type: 'POST',
            dataType: 'json',
            success: function (d) {
                if (d.success) {
                    location.reload();
                } else {
                    alert(d.msg || 'Failed to delete.');
                }
            },
            error: function () { alert('Server error while deleting.'); }
        });
    });
});
</script>
