<?php include_once(VIEWPATH . '/inc/header.php'); ?>

<?php
$selected_id = $selected_role ? (int)$selected_role['role_id'] : 0;
$is_admin = $selected_role && (strcasecmp($selected_role['role_key'], 'Admin') === 0 || (int)$selected_role['role_id'] === 1);

// Group slug menus by their top-level section for readability.
$sections = array();
foreach ($slug_menus as $m) {
    $parts = explode(' » ', $m['path']);
    $section = $parts[0];
    $sections[$section][] = $m;
}
?>

<section class="content-header">
    <h1><i class="fa fa-shield text-primary"></i> Role Permissions</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-shield"></i> Access Control</a></li>
        <li class="active">Role Permissions</li>
    </ol>
</section>

<section class="content">
    <div class="box box-success" style="border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div class="box-header with-border" style="padding: 16px 20px;">
            <div class="row" style="display: flex; align-items: flex-end; flex-wrap: wrap;">
                <div class="col-sm-5 col-xs-12">
                    <label style="font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        <i class="fa fa-user-circle"></i> Select Role to Configure:
                    </label>
                    <select class="form-control input-lg" id="role_select" style="font-weight: 600; font-size: 15px; border-radius: 4px;">
                        <?php foreach ($roles as $r): ?>
                            <option value="<?php echo $r['role_id']; ?>"
                                <?php echo ((int)$r['role_id'] === $selected_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($r['role_name']) . ' (' . htmlspecialchars($r['role_key']) . ')'; ?>
                                <?php echo ((int)$r['is_default'] === 1) ? ' — Default' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-7 col-xs-12 text-right" style="padding-top: 10px;">
                    <div class="btn-group" style="margin-right: 8px;">
                        <button type="button" class="btn btn-default" id="btn_check_all" title="Check all permissions">
                            <i class="fa fa-check-square-o text-success"></i> Check All
                        </button>
                        <button type="button" class="btn btn-default" id="btn_uncheck_all" title="Uncheck all permissions">
                            <i class="fa fa-square-o text-muted"></i> Uncheck All
                        </button>
                    </div>
                    <button type="button" class="btn btn-success btn-flat" id="btn_save_perms" <?php echo $is_admin ? 'disabled' : ''; ?> style="padding: 8px 20px; font-weight: 600;">
                        <i class="fa fa-save"></i> Save Permissions
                    </button>
                </div>
            </div>
        </div>

        <div class="box-body" style="padding: 20px;">
            <?php if ($is_admin): ?>
                <div class="alert alert-info" style="border-radius: 4px;">
                    <i class="fa fa-info-circle fa-lg"></i> <strong>Admin Role (Full Access):</strong>
                    Administrator role automatically has full view, add, edit, and delete permissions on all menus and features.
                </div>
            <?php else: ?>
                <div class="alert alert-warning" style="background-color: #fefce8; border-color: #fef08a; color: #854d0e; border-radius: 4px; padding: 10px 15px; margin-bottom: 15px;">
                    <i class="fa fa-lightbulb-o"></i> <strong>Role Mapping Active:</strong>
                    Users logging in with role <strong><?php echo htmlspecialchars($selected_role ? $selected_role['role_name'] . ' (' . $selected_role['role_key'] . ')' : ''); ?></strong>
                    will only see the enabled menus in their sidebar and will be restricted to the selected Add, Edit, and Delete actions.
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="perm_table" style="margin-bottom: 0;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="width: 40%; vertical-align: middle;">Menu & Sub-module</th>
                            <th class="text-center" style="width: 15%; vertical-align: middle;">
                                <label style="cursor: pointer; margin: 0; font-weight: 600;">
                                    <input type="checkbox" id="col_check_view" class="col-toggle" <?php echo $is_admin ? 'disabled' : ''; ?> /> View
                                </label>
                            </th>
                            <th class="text-center" style="width: 15%; vertical-align: middle;">
                                <label style="cursor: pointer; margin: 0; font-weight: 600;">
                                    <input type="checkbox" id="col_check_add" class="col-toggle" <?php echo $is_admin ? 'disabled' : ''; ?> /> Add
                                </label>
                            </th>
                            <th class="text-center" style="width: 15%; vertical-align: middle;">
                                <label style="cursor: pointer; margin: 0; font-weight: 600;">
                                    <input type="checkbox" id="col_check_edit" class="col-toggle" <?php echo $is_admin ? 'disabled' : ''; ?> /> Edit
                                </label>
                            </th>
                            <th class="text-center" style="width: 15%; vertical-align: middle;">
                                <label style="cursor: pointer; margin: 0; font-weight: 600;">
                                    <input type="checkbox" id="col_check_delete" class="col-toggle" <?php echo $is_admin ? 'disabled' : ''; ?> /> Delete
                                </label>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sections as $section => $menus): ?>
                            <tr class="section-row" style="background: #e2e8f0; font-weight: bold;">
                                <td colspan="1" style="font-size: 14px; color: #1e293b; vertical-align: middle;">
                                    <i class="fa fa-folder-open text-primary"></i> <?php echo htmlspecialchars($section); ?>
                                    <span class="badge bg-navy" style="font-size: 11px; margin-left: 6px; font-weight: normal;"><?php echo count($menus); ?> items</span>
                                </td>
                                <td colspan="4" class="text-right" style="vertical-align: middle; padding: 6px 12px;">
                                    <button type="button" class="btn btn-xs btn-default btn-section-toggle" data-action="check" <?php echo $is_admin ? 'disabled' : ''; ?>>
                                        <i class="fa fa-check-square-o text-success"></i> Select Section
                                    </button>
                                    <button type="button" class="btn btn-xs btn-default btn-section-toggle" data-action="uncheck" <?php echo $is_admin ? 'disabled' : ''; ?>>
                                        <i class="fa fa-square-o text-muted"></i> Unselect Section
                                    </button>
                                </td>
                            </tr>
                            <?php foreach ($menus as $m): ?>
                                <?php
                                $mid = (int)$m['menu_id'];
                                $p = isset($perms[$mid]) ? $perms[$mid] : array('view' => 0, 'add' => 0, 'edit' => 0, 'delete' => 0);
                                $disabled = $is_admin ? 'disabled' : '';
                                ?>
                                <tr class="perm-row" data-mid="<?php echo $mid; ?>">
                                    <td style="vertical-align: middle;">
                                        <div style="display: flex; align-items: center; justify-content: space-between;">
                                            <div>
                                                <span class="perm-title"><?php echo htmlspecialchars($m['menu_title']); ?></span>
                                                <span class="perm-path"><i class="fa fa-angle-double-right text-muted"></i> <?php echo htmlspecialchars($m['path']); ?></span>
                                            </div>
                                            <span class="label label-default" style="font-family: monospace; font-size: 10px; font-weight: normal; background-color: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;">
                                                <?php echo htmlspecialchars($m['menu_slug']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <input type="checkbox" class="perm-check perm-view" name="perm[<?php echo $mid; ?>][view]"
                                            value="1" <?php echo $p['view'] ? 'checked' : ''; ?> <?php echo $disabled; ?> title="Allow viewing this page" />
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <input type="checkbox" class="perm-check perm-add" name="perm[<?php echo $mid; ?>][add]"
                                            value="1" <?php echo $p['add'] ? 'checked' : ''; ?> <?php echo $disabled; ?> title="Allow creating new records" />
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <input type="checkbox" class="perm-check perm-edit" name="perm[<?php echo $mid; ?>][edit]"
                                            value="1" <?php echo $p['edit'] ? 'checked' : ''; ?> <?php echo $disabled; ?> title="Allow editing records" />
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <input type="checkbox" class="perm-check perm-delete" name="perm[<?php echo $mid; ?>][delete]"
                                            value="1" <?php echo $p['delete'] ? 'checked' : ''; ?> <?php echo $disabled; ?> title="Allow deleting records" />
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box-footer text-right" style="padding: 16px 20px; background: #f8fafc; border-top: 1px solid #f1f5f9;">
            <button type="button" class="btn btn-success btn-flat" id="btn_save_perms_bottom" <?php echo $is_admin ? 'disabled' : ''; ?> style="padding: 8px 24px; font-weight: 600;">
                <i class="fa fa-save"></i> Save Permissions
            </button>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>
