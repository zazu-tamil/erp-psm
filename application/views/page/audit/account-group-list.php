<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Master</a></li>
        <li class="active"><?php echo $title; ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="" id="frmsearch">
                <div class="row"> 
                    <div class="form-group col-md-3">
                        <label for="srch_group_id">Account Group <span style="color:red;">*</span></label>
                        <?php echo form_dropdown('srch_group_id', ['' => 'All'] + $group_opt, set_value('srch_group_id', $srch_group_id), 'id="srch_group_id" class="form-control select2"'); ?>
                    </div>
 
                    <div class="form-group col-md-3">
                        <label>Select Nature</label>
                        <?php echo form_dropdown('srch_nature', ['' => 'All'] + $nature_opt, set_value('srch_nature', $srch_nature), 'id="srch_nature" class="form-control select2"'); ?>
                    </div>
                    <div class="form-group col-md-3 text-left">
                        <br>
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="box box-success">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#add_modal">
                <span class="fa fa-plus-circle"></span> Add New
            </button>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped table-responsive" id="company_list">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th>Group Name</th>
                        <th>Natural Account</th>
                        <th>Parent Group</th>
                        <th>sequence</th>
                        <th colspan="2" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($record_list as $j => $ls) {
                        ?>
                        <tr class="mb-3">
                            <td class="text-center"><?php echo ($j + 1); ?></td>
                            <td><?php echo $ls['group_name'] ?></td>
                            <td><?php echo $ls['nature'] ?></td>
                            <td><?php echo $ls['group_name'] ?></td>
                            <td><?php echo $ls['sequence'] ?></td>

                            <td class="text-center">
                                <button data-toggle="modal" data-target="#edit_modal" value="<?php echo $ls['group_id'] ?>"
                                    class="edit_record btn btn-primary btn-xs" title="Edit"><i
                                        class="fa fa-edit"></i></button>
                            </td>
                            <td class="text-center">
                                <button value="<?php echo $ls['group_id'] ?>" class="del_record btn btn-danger btn-xs"
                                    title="Delete"><i class="fa fa-remove"></i></button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>

            <!-- Add Modal -->
            <div class="modal fade" id="add_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo site_url('account-group-list'); ?>" id="frmadd"
                            enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel"><strong>Add Account Group</strong></h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Group Name</label>
                                        <input type="text" name="group_name" class="form-control" required
                                            placeholder="Enter group name" id="group_name">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Select Nature</label>
                                        <select name="nature" class="form-control" required id="nature">
                                            <option value="">-- Select Nature --</option>
                                            <option value="Asset">Asset</option>
                                            <option value="Liability">Liability</option>
                                            <option value="Income">Income</option>
                                            <option value="Expense">Expense</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Primary Group</label>
                                        <select name="parent_group" class="form-control" id="parent_group">
                                            <option value="">-- Primary Group --</option>
                                            <!-- <?php foreach ($groups as $g) { ?>
                                                <option value="<?= $g->group_id ?>">
                                                    <?= $g->group_name ?>
                                                </option>
                                            <?php } ?> -->
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Sequence</label>
                                        <input type="number" name="sequence" class="form-control" placeholder="0"
                                            required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Status</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" value="Active" checked="true" />
                                                Active
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" value="InActive" /> InActive
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <input type="submit" name="Save" value="Save" class="btn btn-primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="edit_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo site_url('account-group-list'); ?>" id="frmedit"
                            class="form-material">
                            <div class="modal-header">

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel">Edit Account Group </h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="group_id" id="group_id" value="" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Group Name</label>
                                        <input type="text" name="group_name" class="form-control" required
                                            id="group_name">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Select Nature</label>
                                        <select name="nature" class="form-control" required id="nature">
                                            <option value="">-- Select Nature --</option>
                                            <option value="Asset">Asset</option>
                                            <option value="Liability">Liability</option>
                                            <option value="Income">Income</option>
                                            <option value="Expense">Expense</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Primary Group</label>
                                        <select name="parent_group" class="form-control" id="parent_group">
                                            <option value="">-- Primary Group --</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Sequence</label>
                                        <input type="number" name="sequence" class="form-control" placeholder="0"
                                            id="sequence">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Status</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" value="Active" checked="true" />
                                                Active
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" value="InActive" /> InActive
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <input type="submit" name="Save" value="Update" class="btn btn-primary" />
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
<!-- /.content -->
<?php include_once(VIEWPATH . 'inc/footer.php');  /*const nature_name = $(this).val();
$.post(
"<?php echo site_url('adit/get_data'); ?>",
{
tbl: "account_groups_parent_list",
id: nature_name,
},
function (data) {
console.log(data);
const $cust = $("#parent_group");
$cust.empty().append('<option value="">Select Parent Group</option>');
$.each(data, function (i, c) {
$cust.append(
`<option value="${c.group_id}">${c.group_name}</option>`
);
});

},
"json"
);*/ ?>