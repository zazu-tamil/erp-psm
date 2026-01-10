<?php include_once(VIEWPATH . '/inc/header.php'); ?>

<section class="content-header">
    <h1><?php echo $title; ?></h1>
</section>

<section class="content">
    <div class="box box-success">
        <div class="box-header with-border">
            <p class="box-title">Settings list</p>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Setting Key</th>
                        <th>Setting Value</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($record_list as $j => $ls) { ?>
                    <tr>
                        <form method="post" action="<?php echo site_url('settings'); ?>" class="settings-form">

                            <input type="hidden" name="mode" value="Update">
                            <input type="hidden" name="setting_key" value="<?php echo $ls['setting_key']; ?>">

                            <td><?php echo $j + 1; ?></td>

                            <td><?php echo $ls['setting_key']; ?></td>

                            <td>
                                <input type="text"
                                       class="form-control setting-input"
                                       name="setting_value"
                                       value="<?php echo $ls['setting_value']; ?>"
                                       readonly>
                            </td>

                            <td>
                                <select name="status"
                                        class="form-control setting-input"
                                        disabled>
                                    <option value="Active" <?= ($ls['status'] == 'Active') ? 'selected' : ''; ?>>
                                        Active
                                    </option>
                                    <option value="Inactive" <?= ($ls['status'] == 'Inactive') ? 'selected' : ''; ?>>
                                        Inactive
                                    </option>
                                </select>
                            </td>

                            <td>
                                <button type="button" class="btn btn-sm btn-warning btn-edit">
                                    <i class="fa fa-pencil"></i> Enable to Edit
                                </button>

                                <button type="submit"
                                        class="btn btn-sm btn-success btn-save"
                                        style="display:none;">
                                    <i class="fa fa-save"></i> Save
                                </button>
                            </td>

                        </form>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
