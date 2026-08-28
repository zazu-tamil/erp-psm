<?php include_once(VIEWPATH . '/inc/header.php'); ?>

<section class="content-header">
    <h1>Role Management</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-shield"></i> Access Control</a></li>
        <li class="active">Role Management</li>
    </ol>
</section>

<section class="content">
    <div class="box box-success">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success" id="btn_add_role">
                <i class="fa fa-plus-circle"></i> Add Role
            </button>
            <a href="<?php echo site_url('role-permission'); ?>" class="btn btn-primary pull-right">
                <i class="fa fa-key"></i> Manage Role Permissions
            </a>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped" id="role_table">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th>Role Name</th>
                        <th>Role Key (level)</th>
                        <th>Default</th>
                        <th>Status</th>
                        <th colspan="2" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $j => $r): ?>
                        <tr>
                            <td class="text-center"><?php echo $j + 1; ?></td>
                            <td><?php echo htmlspecialchars($r['role_name']); ?></td>
                            <td><code><?php echo htmlspecialchars($r['role_key']); ?></code></td>
                            <td><?php echo $r['is_default'] ? '<span class="label label-success">Default</span>' : '-'; ?></td>
                            <td><?php echo htmlspecialchars($r['status']); ?></td>
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-primary btn-xs edit-role"
                                        data-id="<?php echo $r['role_id']; ?>"
                                        data-key="<?php echo htmlspecialchars($r['role_key']); ?>"
                                        data-name="<?php echo htmlspecialchars($r['role_name']); ?>"
                                        data-default="<?php echo $r['is_default']; ?>"
                                        data-status="<?php echo htmlspecialchars($r['status']); ?>"
                                        title="Edit"><i class="fa fa-edit"></i></button>
                            </td>
                            <td class="text-center">
                                <?php if ($r['role_key'] !== 'Admin'): ?>
                                    <button type="button"
                                            class="btn btn-danger btn-xs del-role"
                                            data-id="<?php echo $r['role_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($r['role_name']); ?>"
                                            title="Delete"><i class="fa fa-remove"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add / Edit Role Modal -->
    <div class="modal fade" id="role_modal" role="dialog" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title" id="roleModalLabel">Add Role</h3>
                </div>
                <form id="role_form">
                    <input type="hidden" name="mode" id="r_mode" value="Add" />
                    <input type="hidden" name="role_id" id="r_role_id" value="" />
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Role Name *</label>
                            <input type="text" class="form-control" name="role_name" id="r_role_name" required />
                        </div>
                        <div class="form-group">
                            <label>Role Key (level) * <small class="text-muted">must match the user's "level" value</small></label>
                            <input type="text" class="form-control" name="role_key" id="r_role_key" required />
                        </div>
                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" name="is_default" id="r_is_default" value="1" /> Default role
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status" id="r_status">
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
