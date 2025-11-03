<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<section class="content-header">
    <h1>Items List</h1>
</section>
<!-- <?php echo "vbfdghsdfg" . $srch_category_id; ?> -->
<section class="content">
    <div class="box box-info no-print">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('items-list') ?>" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>Category</label>
                        <?php echo form_dropdown('srch_category_id', array('' => 'Select Category') + $category_opt, set_value('srch_category_id'), ' id="srch_category_id" class="form-control"'); ?>
                    </div>
                    <div class="form-group col-md-2 text-left">
                        <br />
                        <button class="btn btn-success" name="btn_show" value="Show'"><i class="fa fa-search"></i>
                            Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="box box-info">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#add_modal">
                <span class="fa fa-plus-circle"></span> Add New
            </button>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped" id="item_list">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>UOM</th>
                        <th>HSN Code</th>
                        <th>vAT</th>
                        <th>Status</th>
                        <th class="text-center">Edit</th>
                        <th class="text-center">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($record_list as $j => $ls) { ?>
                        <tr>
                            <td class="text-center"><?= ($j + 1) ?></td>
                            <td><?= $ls['item_name'] ?></td>
                            <td><?= $ls['category_name'] ?></td>
                            <td><?= $ls['brand_name'] ?></td>
                            <td><?= $ls['uom'] ?></td>
                            <td><?= $ls['hsn_code'] ?></td>
                            <td><?= $ls['gst'] ?></td>
                            <td><?= $ls['status'] ?></td>
                            <td class="text-center">
                                <button data-toggle="modal" data-target="#edit_modal" value="<?= $ls['item_id'] ?>"
                                    class="edit_record btn btn-primary btn-xs" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>
                            <td class="text-center">
                                <button value="<?= $ls['item_id'] ?>" class="del_record btn btn-danger btn-xs"
                                    title="Delete">
                                    <i class="fa fa-remove"></i>
                                </button>
                            </td>

                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- ADD MODAL -->
            <div class="modal fade" id="add_modal" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <form method="post" action="<?php base_url('items-list') ?>" id="form_Add"
                            enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                <h3 class="modal-title">Add Items</h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6 mb-3">
                                        <label>Category</label>
                                        <?php echo form_dropdown(
                                            'category_id',
                                            array('' => 'Select Category') + $category_opt,
                                            '',
                                            'id="category_id" class="form-control" required'
                                        ); ?>
                                    </div>

                                    <div class="form-group col-md-6 mb-3">
                                        <label>Brand</label>
                                        <?php echo form_dropdown(
                                            'brand_id',
                                            array('' => 'Select Brand') + $brand_opt,
                                            '',
                                            'id="brand_id" class="form-control" required'
                                        ); ?>
                                    </div>

                                    <div class="form-group col-md-12 mb-3">
                                        <label>Item Name</label>
                                        <input class="form-control" type="text" name="item_name" required id="item_name"
                                            placeholder="Enter your item name">
                                    </div>
                                    <div class="form-group col-md-12 mb-3">
                                        <label>Description</label>
                                        <textarea class="form-control" name="item_description" id="item_description"
                                            rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 mb-3">
                                        <label>UOM</label>
                                        <?php echo form_dropdown(
                                            'uom',
                                            array('' => 'Select UOM') + $uom_opt,
                                            '',
                                            'id="uom" class="form-control" required'
                                        ); ?>
                                    </div>

                                    <div class="form-group col-md-6 mb-3">
                                        <label>HSN Code</label>
                                        <input class="form-control" type="text" name="hsn_code" id="hsn_code">
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label>VAT (%)</label>
                                        <?php echo form_dropdown(
                                            'gst',
                                            array('' => 'Select gst') + $gst_opt,
                                            '',
                                            'id="gst" class="form-control" required'
                                        ); ?>
                                    </div>

                                    <div class="form-group col-md-6 mb-3">
                                        <label>Item Image</label>
                                        <input class="form-control" type="file" name="item_image" id="item_image"
                                            accept="image/*">
                                        <img src="" alt="" id="item_image_preview" style="display:none;" width="100px"
                                            height="100px">
                                    </div>
                                    <div class="form-group col-md-12 mb-3">
                                        <label>Status</label><br>
                                        <label><input type="radio" name="status" value="Active" checked>
                                            Active</label>&nbsp;&nbsp;&nbsp;
                                        <label><input type="radio" name="status" value="InActive"> InActive</label>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <input type="submit" value="Save" class="btn btn-primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit MODAL -->
            <div class="modal fade" id="edit_modal" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <form method="post" action="<?php base_url('items-list') ?>" id="frmedit"
                            enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                <h3 class="modal-title">Edit Items</h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="item_id" id="item_id" />
                            </div>
                            <div class="modal-body">
                                <div class="row">

                                    <div class="form-group col-md-6 mb-3">
                                        <label>Category</label>
                                        <?php echo form_dropdown(
                                            'category_id',
                                            array('' => 'Select Category') + $category_opt,
                                            '',
                                            'id="category_id" class="form-control" required'
                                        ); ?>
                                    </div>

                                    <div class="form-group col-md-6 mb-3">
                                        <label>Brand</label>
                                        <?php echo form_dropdown(
                                            'brand_id',
                                            array('' => 'Select Brand') + $brand_opt,
                                            '',
                                            'id="brand_id" class="form-control" required'
                                        ); ?>
                                    </div>

                                    <div class="form-group col-md-12 mb-3">
                                        <label>Item Name</label>
                                        <input class="form-control" type="text" name="item_name" required
                                            id="item_name">
                                    </div>
                                    <div class="form-group col-md-12 mb-3">
                                        <label>Description</label>
                                        <textarea class="form-control" name="item_description" id="item_description"
                                            rows="3"></textarea>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 mb-3">
                                        <label>UOM</label>
                                        <?php echo form_dropdown(
                                            'uom',
                                            array('' => 'Select UOM') + $uom_opt,
                                            '',
                                            'id="uom" class="form-control" required'
                                        ); ?>
                                    </div>

                                    <div class="form-group col-md-6 mb-3">
                                        <label>HSN Code</label>
                                        <input class="form-control" type="text" name="hsn_code" id="hsn_code">
                                    </div>

                                    <div class="form-group col-md-6 mb-3">
                                        <label>VAT (%)</label>
                                        <?php echo form_dropdown(
                                            'gst',
                                            array('' => 'Select gst') + $gst_opt,
                                            '',
                                            'id="gst" class="form-control" required'
                                        ); ?>
                                    </div>

                                    <div class="form-group col-md-6 mb-3">
                                        <label>Item Image</label>
                                        <input class="form-control" type="file" name="item_image" id="item_image"
                                            accept="image/*">
                                        <img src="" alt="" id="item_image_preview" style="display:none;" width="100px"
                                            height="100px">
                                    </div>



                                    <div class="form-group col-md-12 mb-3">
                                        <label>Status</label><br>
                                        <label><input type="radio" name="status" value="Active" checked>
                                            Active</label>&nbsp;&nbsp;&nbsp;
                                        <label><input type="radio" name="status" value="InActive"> InActive</label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <input type="submit" value="Update" class="btn btn-primary" />
                                </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
        <div class="box-footer">
            <div class="form-group col-sm-6">
                <label>Total Records : <?php echo $total_records; ?></label>
            </div>
            <div class="form-group col-sm-6">
                <?php echo $pagination; ?>
            </div>
        </div>

    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
<script>
    $("#item_image").on("change", function () {
        var simgFile = this.files[0]; // get file object
        var simgURL = simgFile ? URL.createObjectURL(simgFile) : '';

        if (simgURL) {
            $("#item_image_preview").attr("src", simgURL).show();
        } else {
            $("#item_image_preview").hide();
        }
    });

</script>
<script>
    $("#edit_modal #item_image").on("change", function () {
        var simgFile = this.files[0]; // get file object
        var simgURL = simgFile ? URL.createObjectURL(simgFile) : '';

        if (simgURL) {
            $("#edit_modal #item_image_preview").attr("src", simgURL).show();
        } else {
            $("#edit_modal #item_image_preview").hide();
        }
    });

</script>