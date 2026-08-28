<?php include_once(VIEWPATH . '/inc/header.php'); ?>

<?php
// Recursive renderer for the sortable menu tree (unique name to avoid collisions).
function render_mm_tree_node($node)
{
    $id       = (int)$node['menu_id'];
    $title    = htmlspecialchars($node['menu_title']);
    $slug     = htmlspecialchars($node['menu_slug']);
    $icon     = htmlspecialchars($node['menu_icon']);
    $isHeader = (int)$node['is_header'];
    $status   = htmlspecialchars($node['status']);
    $parent   = (int)$node['parent_id'];
    ?>
    <li data-id="<?php echo $id; ?>">
        <div class="menu-node <?php echo $isHeader ? 'is-header' : ''; ?>"
             data-id="<?php echo $id; ?>"
             data-title="<?php echo $title; ?>"
             data-slug="<?php echo $slug; ?>"
             data-icon="<?php echo $icon; ?>"
             data-header="<?php echo $isHeader; ?>"
             data-status="<?php echo $status; ?>"
             data-parent="<?php echo $parent; ?>">
            <span class="drag-handle" title="Drag to reorder"><i class="fa fa-bars"></i></span>
            <span class="node-icon"><i class="<?php echo $icon ?: 'fa fa-circle-o'; ?>"></i></span>
            <span class="node-title"><?php echo $title; ?></span>
            <?php if ($slug !== ''): ?>
                <span class="node-slug">/<?php echo $slug; ?></span>
            <?php endif; ?>
            <?php if ($isHeader): ?>
                <span class="label label-default">HEADER</span>
            <?php endif; ?>
            <span class="node-actions">
                <button type="button" class="btn btn-xs btn-primary edit-menu"><i class="fa fa-edit"></i> Edit</button>
                <?php if (!$isHeader): ?>
                    <button type="button" class="btn btn-xs btn-info add-child"><i class="fa fa-plus"></i> Child</button>
                <?php endif; ?>
                <button type="button" class="btn btn-xs btn-danger del-menu"><i class="fa fa-remove"></i> Delete</button>
            </span>
        </div>
        <?php if (!$isHeader): ?>
            <ul class="menu-sortable">
                <?php foreach ($node['children'] as $child) { render_mm_tree_node($child); } ?>
            </ul>
        <?php endif; ?>
    </li>
    <?php
}
?>

<section class="content-header">
    <h1>Menu Management</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-shield"></i> Access Control</a></li>
        <li class="active">Menu Management</li>
    </ol>
</section>

<section class="content">
    <div class="box box-success">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success" id="btn_add_menu">
                <i class="fa fa-plus-circle"></i> Add Menu
            </button>
            <button type="button" class="btn btn-primary pull-right" id="btn_save_order">
                <i class="fa fa-save"></i> Save Order
            </button>
        </div>
        <div class="box-body">
            <p class="text-muted">
                Drag &amp; drop items to rearrange the menu. Drop an item <strong>onto</strong> another item to make
                it a child. Press <strong>Save Order</strong> to persist changes.
            </p>
            <ul id="menu-root" class="menu-sortable root-sortable">
                <?php foreach ($menu_tree as $node) { render_mm_tree_node($node); } ?>
            </ul>
        </div>
    </div>

    <!-- Add / Edit Menu Modal -->
    <div class="modal fade" id="menu_modal" role="dialog" aria-labelledby="menuModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title" id="menuModalLabel">Add Menu</h3>
                </div>
                <form id="menu_form">
                    <input type="hidden" name="mode" id="m_mode" value="Add" />
                    <input type="hidden" name="menu_id" id="m_menu_id" value="" />
                    <input type="hidden" name="parent_id" id="m_parent_id" value="0" />
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Menu Title *</label>
                            <input type="text" class="form-control" name="menu_title" id="m_menu_title" required />
                        </div>
                        <div class="form-group">
                            <label>Route Slug <small class="text-muted">(e.g. user-list — leave blank for a group/header)</small></label>
                            <input type="text" class="form-control" name="menu_slug" id="m_menu_slug" />
                        </div>
                        <div class="form-group">
                            <label>Icon</label>
                            <select class="form-control" name="menu_icon" id="m_menu_icon">
                                <option value="">— None —</option>
                                <?php foreach ($icons as $ic): ?>
                                    <option value="<?php echo $ic; ?>"><?php echo $ic; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" name="is_header" id="m_is_header" value="1" /> Section Header (label)
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status" id="m_status">
                                <option value="Active">Active</option>
                                <option value="InActive">InActive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>
