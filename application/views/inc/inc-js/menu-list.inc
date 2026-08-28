<script>
jQuery(function ($) {
    var BASE = "<?php echo base_url(); ?>";
    var SAVE_URL = "<?php echo site_url('menu-management/save'); ?>";
    var REORDER_URL = "<?php echo site_url('menu-management/reorder'); ?>";
    var DELETE_URL = "<?php echo site_url('menu-management/delete'); ?>";

    // ---- Load jQuery UI (only on this page, avoids global tooltip conflict) ----
    function loadJqueryUI(cb) {
        if (window.jQuery && jQuery.ui) { cb(); return; }
        var s = document.createElement('script');
        s.src = BASE + 'asset/bower_components/jquery-ui/jquery-ui.min.js';
        s.onload = function () { cb(); };
        document.head.appendChild(s);
    }

    function initSortable() {
        $('.menu-sortable').sortable({
            connectWith: '.menu-sortable',
            handle: '.drag-handle',
            placeholder: 'menu-placeholder',
            tolerance: 'pointer',
            items: '> li'
        });
    }

    loadJqueryUI(initSortable);

    // ---- Serialize tree into [{id, parent_id, sort_order}] ----
    function serializeTree($ul, parentId) {
        var items = [];
        $ul.children('li').each(function (idx) {
            var $li = $(this);
            var id = parseInt($li.data('id'), 10);
            items.push({ id: id, parent_id: parentId, sort_order: idx + 1 });

            var $childUl = $li.children('ul.menu-sortable');
            if ($childUl.length) {
                items = items.concat(serializeTree($childUl, id));
            }
        });
        return items;
    }

    // ---- Save order ----
    $('#btn_save_order').on('click', function () {
        var payload = serializeTree($('#menu-root'), 0);
        $.ajax({
            url: REORDER_URL,
            type: 'POST',
            data: { items: JSON.stringify(payload) },
            dataType: 'json',
            success: function (d) {
                alert(d.success ? 'Menu order saved.' : (d.msg || 'Failed to save.'));
            },
            error: function () { alert('Server error while saving order.'); }
        });
    });

    // ---- Open Add modal ----
    $('#btn_add_menu').on('click', function () {
        $('#menu_modal #menuModalLabel').text('Add Menu');
        $('#m_mode').val('Add');
        $('#m_menu_id').val('');
        $('#m_parent_id').val('0');
        $('#m_menu_title').val('');
        $('#m_menu_slug').val('');
        $('#m_menu_icon').val('');
        $('#m_is_header').prop('checked', false);
        $('#m_status').val('Active');
        toggleHeaderFields();
        $('#menu_modal').modal('show');
    });

    // ---- Open Add-Child modal ----
    $(document).on('click', '.add-child', function () {
        var $node = $(this).closest('.menu-node');
        $('#menu_modal #menuModalLabel').text('Add Child Menu');
        $('#m_mode').val('Add');
        $('#m_menu_id').val('');
        $('#m_parent_id').val($node.data('id'));
        $('#m_menu_title').val('');
        $('#m_menu_slug').val('');
        $('#m_menu_icon').val('');
        $('#m_is_header').prop('checked', false);
        $('#m_status').val('Active');
        toggleHeaderFields();
        $('#menu_modal').modal('show');
    });

    // ---- Open Edit modal ----
    $(document).on('click', '.edit-menu', function () {
        var $node = $(this).closest('.menu-node');
        $('#menu_modal #menuModalLabel').text('Edit Menu');
        $('#m_mode').val('Edit');
        $('#m_menu_id').val($node.data('id'));
        $('#m_parent_id').val($node.data('parent'));
        $('#m_menu_title').val($node.data('title'));
        $('#m_menu_slug').val($node.data('slug'));
        $('#m_menu_icon').val($node.data('icon'));
        $('#m_is_header').prop('checked', parseInt($node.data('header'), 10) === 1);
        $('#m_status').val($node.data('status'));
        toggleHeaderFields();
        $('#menu_modal').modal('show');
    });

    // ---- Header toggle disables slug/icon ----
    function toggleHeaderFields() {
        var isHeader = $('#m_is_header').is(':checked');
        $('#m_menu_slug, #m_menu_icon').prop('disabled', isHeader);
    }
    $('#m_is_header').on('change', toggleHeaderFields);

    // ---- Submit add/edit ----
    $('#menu_form').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: SAVE_URL,
            type: 'POST',
            data: $('#menu_form').serialize(),
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

    // ---- Delete ----
    $(document).on('click', '.del-menu', function () {
        var $node = $(this).closest('.menu-node');
        var id = $node.data('id');
        var title = $node.data('title');
        if (!confirm('Delete "' + title + '" and all of its children?')) return;
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

<style>
    .menu-sortable { list-style: none; padding-left: 26px; margin: 4px 0; min-height: 12px; }
    .menu-sortable.root-sortable { padding-left: 0; }
    .menu-node {
        display: flex; align-items: center; gap: 8px;
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;
        padding: 7px 10px; margin: 3px 0; cursor: default;
    }
    .menu-node.is-header { background: #eef2f7; border-style: dashed; }
    .drag-handle { cursor: move; color: #94a3b8; }
    .node-icon { color: #0054a6; width: 18px; text-align: center; }
    .node-title { font-weight: 600; color: #1e293b; }
    .node-slug { color: #94a3b8; font-size: 12px; font-family: monospace; }
    .node-actions { margin-left: auto; white-space: nowrap; }
    .menu-placeholder { background: #dbeafe; border: 1px dashed #3b82f6; border-radius: 6px; min-height: 34px; }
</style>
