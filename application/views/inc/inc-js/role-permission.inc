<script>
jQuery(function ($) {
    var SAVE_URL = "<?php echo site_url('role-permission/save'); ?>";
    var ROLE_ID = <?php echo $selected_id; ?>;

    // Role switcher
    $('#role_select').on('change', function () {
        var id = $(this).val();
        window.location.href = "<?php echo site_url('role-permission'); ?>/" + id;
    });

    // Check All & Uncheck All
    $('#btn_check_all').on('click', function () {
        $('.perm-check:not(:disabled)').prop('checked', true);
        updateColToggles();
    });

    $('#btn_uncheck_all').on('click', function () {
        $('.perm-check:not(:disabled)').prop('checked', false);
        updateColToggles();
    });

    // Column-wise toggles
    $('#col_check_view').on('change', function () {
        var state = this.checked;
        $('.perm-view:not(:disabled)').prop('checked', state);
        if (!state) {
            // If view unchecked globally, uncheck other actions too
            $('.perm-add:not(:disabled), .perm-edit:not(:disabled), .perm-delete:not(:disabled)').prop('checked', false);
            $('#col_check_add, #col_check_edit, #col_check_delete').prop('checked', false);
        }
    });

    $('#col_check_add').on('change', function () {
        var state = this.checked;
        $('.perm-add:not(:disabled)').prop('checked', state);
        if (state) {
            $('.perm-view:not(:disabled)').prop('checked', true);
            $('#col_check_view').prop('checked', true);
        }
    });

    $('#col_check_edit').on('change', function () {
        var state = this.checked;
        $('.perm-edit:not(:disabled)').prop('checked', state);
        if (state) {
            $('.perm-view:not(:disabled)').prop('checked', true);
            $('#col_check_view').prop('checked', true);
        }
    });

    $('#col_check_delete').on('change', function () {
        var state = this.checked;
        $('.perm-delete:not(:disabled)').prop('checked', state);
        if (state) {
            $('.perm-view:not(:disabled)').prop('checked', true);
            $('#col_check_view').prop('checked', true);
        }
    });

    // Section-level toggle
    $(document).on('click', '.btn-section-toggle', function () {
        var action = $(this).data('action');
        var $row = $(this).closest('tr');
        var $nextRows = $row.nextUntil('.section-row');
        if (action === 'check') {
            $nextRows.find('.perm-check:not(:disabled)').prop('checked', true);
        } else {
            $nextRows.find('.perm-check:not(:disabled)').prop('checked', false);
        }
        updateColToggles();
    });

    // Row-level smart dependency
    $(document).on('change', '.perm-add, .perm-edit, .perm-delete', function () {
        if (this.checked) {
            // Auto-check View for this row
            $(this).closest('tr').find('.perm-view:not(:disabled)').prop('checked', true);
        }
        updateColToggles();
    });

    $(document).on('change', '.perm-view', function () {
        if (!this.checked) {
            // Auto-uncheck Add, Edit, Delete for this row
            $(this).closest('tr').find('.perm-add, .perm-edit, .perm-delete').prop('checked', false);
        }
        updateColToggles();
    });

    function updateColToggles() {
        var totalView = $('.perm-view:not(:disabled)').length;
        var checkedView = $('.perm-view:not(:disabled):checked').length;
        $('#col_check_view').prop('checked', totalView > 0 && totalView === checkedView);

        var totalAdd = $('.perm-add:not(:disabled)').length;
        var checkedAdd = $('.perm-add:not(:disabled):checked').length;
        $('#col_check_add').prop('checked', totalAdd > 0 && totalAdd === checkedAdd);

        var totalEdit = $('.perm-edit:not(:disabled)').length;
        var checkedEdit = $('.perm-edit:not(:disabled):checked').length;
        $('#col_check_edit').prop('checked', totalEdit > 0 && totalEdit === checkedEdit);

        var totalDelete = $('.perm-delete:not(:disabled)').length;
        var checkedDelete = $('.perm-delete:not(:disabled):checked').length;
        $('#col_check_delete').prop('checked', totalDelete > 0 && totalDelete === checkedDelete);
    }

    // Save Permissions
    $('#btn_save_perms, #btn_save_perms_bottom').on('click', function () {
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        var payload = { role_id: ROLE_ID, perm: {} };
        $('#perm_table input.perm-check').each(function () {
            var name = $(this).attr('name');
            var m = name.match(/^perm\[(\d+)\]\[(view|add|edit|delete)\]$/);
            if (!m) return;
            var mid = m[1];
            var action = m[2];
            if (!payload.perm[mid]) payload.perm[mid] = {};
            payload.perm[mid][action] = this.checked ? 1 : 0;
        });

        $.ajax({
            url: SAVE_URL,
            type: 'POST',
            data: payload,
            dataType: 'json',
            success: function (d) {
                $btn.prop('disabled', false).html(originalHtml);
                if (d.success) {
                    alert('✓ Permissions saved successfully for this role.');
                } else {
                    alert('Error: ' + (d.msg || 'Failed to save permissions.'));
                }
            },
            error: function () {
                $btn.prop('disabled', false).html(originalHtml);
                alert('Server error while saving permissions.');
            }
        });
    });

    updateColToggles();
});
</script>

<style>
    .section-row td { 
        background: #f1f5f9 !important; 
        border-top: 2px solid #cbd5e1 !important;
    }
    .perm-row:hover {
        background-color: #f8fafc !important;
    }
    .perm-title { 
        font-weight: 600; 
        display: block; 
        color: #1e293b; 
        font-size: 13px;
    }
    .perm-path { 
        color: #94a3b8; 
        font-size: 11px; 
    }
    .perm-check { 
        width: 18px; 
        height: 18px; 
        cursor: pointer; 
        accent-color: #004b8d;
    }
    .col-toggle {
        cursor: pointer;
        accent-color: #004b8d;
    }
    #perm_table th {
        border-bottom: 2px solid #cbd5e1;
    }
</style>
