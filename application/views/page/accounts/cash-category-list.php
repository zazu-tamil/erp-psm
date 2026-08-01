<?php include_once(VIEWPATH . '/inc/header.php'); ?>

<section class="content-header">
    <h1>Cash Category Master</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Accounts</a></li>
        <li class="active">Cash Category List</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1"
                    data-toggle="modal" data-target="#add_modal">
                <span class="fa fa-plus-circle"></span> Add New Category
            </button>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped"
                   id="cash_category_table">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th>Category Name</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th class="text-center">Edit</th>
                        <th class="text-center">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)): ?>
                        <?php foreach ($record_list as $j => $ls): ?>
                            <?php
                                $typeColor = '#6c757d';
                                if ($ls['category_type'] == 'Inward')  $typeColor = '#10b981';
                                if ($ls['category_type'] == 'Outward') $typeColor = '#ef4444';
                                if ($ls['category_type'] == 'Both')    $typeColor = '#3b82f6';
                            ?>
                            <tr>
                                <td class="text-center"><?php echo ($j + 1); ?></td>
                                <td><strong><?php echo htmlspecialchars($ls['category_name']); ?></strong></td>
                                <td>
                                    <span style="
                                        background-color:<?php echo $typeColor; ?>22;
                                        color:<?php echo $typeColor; ?>;
                                        padding:3px 10px;
                                        border-radius:20px;
                                        font-size:11px;
                                        font-weight:600;
                                        text-transform:uppercase;
                                        letter-spacing:.4px;
                                    ">
                                        <?php echo $ls['category_type']; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($ls['description'] ?? '-'); ?></td>
                                <td>
                                    <?php if ($ls['status'] == 'Active'): ?>
                                        <span class="label label-success">Active</span>
                                    <?php else: ?>
                                        <span class="label label-default">InActive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button data-toggle="modal" data-target="#edit_modal"
                                            value="<?php echo $ls['cash_category_id']; ?>"
                                            class="edit_record btn btn-primary btn-xs"
                                            title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </td>
                                <td class="text-center">
                                    <button value="<?php echo $ls['cash_category_id']; ?>"
                                            class="del_record btn btn-danger btn-xs"
                                            title="Delete">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding:30px;">
                                <i class="fa fa-inbox fa-2x"></i><br/>
                                No cash categories found. Click <strong>Add New Category</strong> to create one.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div><!-- /.box-body -->

        <!-- ══════════════════════════════════════
             ADD MODAL
        ══════════════════════════════════════ -->
        <div class="modal fade" id="add_modal" role="dialog"
             aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <form method="post" action="" id="frmadd">
                        <input type="hidden" name="mode" value="Add" />
                        <div class="modal-header" style="background:#004b8d; color:#fff;">
                            <button type="button" class="close" data-dismiss="modal"
                                    aria-label="Close" style="color:#fff; opacity:1;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="addModalLabel">
                                <i class="fa fa-plus-circle"></i> Add Cash Category
                            </h4>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <label>Category Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text"
                                       name="category_name" id="add_category_name"
                                       placeholder="e.g. Office Supplies, Rent, Sales Revenue"
                                       required />
                            </div>

                            <div class="form-group">
                                <label>Category Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="category_type" id="add_category_type" required>
                                    <option value="">-- Select Type --</option> 
                                    <option value="Cash">Cash</option>
                                </select>
                                <small class="text-muted">
                                    Choose whether this category applies to cash inflow, outflow, or both.
                                </small>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" id="add_description"
                                          rows="2" placeholder="Optional notes about this category..."></textarea>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="status" value="Active" checked="checked" /> Active
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="status" value="InActive" /> InActive
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default"
                                    data-dismiss="modal">Cancel</button>
                            <input type="submit" value="Save Category"
                                   class="btn btn-success" />
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════
             EDIT MODAL
        ══════════════════════════════════════ -->
        <div class="modal fade" id="edit_modal" role="dialog"
             aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <form method="post" action="" id="frmedit">
                        <input type="hidden" name="mode" value="Edit" />
                        <input type="hidden" name="cash_category_id" id="edit_cash_category_id" />
                        <div class="modal-header" style="background:#0054a6; color:#fff;">
                            <button type="button" class="close" data-dismiss="modal"
                                    aria-label="Close" style="color:#fff; opacity:1;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="editModalLabel">
                                <i class="fa fa-edit"></i> Edit Cash Category
                            </h4>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <label>Category Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text"
                                       name="category_name" id="edit_category_name"
                                       placeholder="Category name" required />
                            </div>

                            <div class="form-group">
                                <label>Category Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="category_type"
                                        id="edit_category_type" required>
                                    <option value="">-- Select Type --</option> 
                                    <option value="Cash">Cash</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" id="edit_description"
                                          rows="2"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="status" value="Active" /> Active
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="status" value="InActive" /> InActive
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default"
                                    data-dismiss="modal">Cancel</button>
                            <input type="submit" value="Update Category"
                                   class="btn btn-primary" />
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div><!-- /.box -->
</section>
<!-- /.content -->

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
