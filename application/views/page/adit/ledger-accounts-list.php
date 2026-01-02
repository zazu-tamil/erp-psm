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
                        <?php echo form_dropdown('srch_group_id', ['' => 'All'] + $group_opt, set_value('srch_group_id', $srch_group_id), 'id="srch_group_id" class="form-control"'); ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="srch_group_id">Ledger Account <span style="color:red;">*</span></label>
                        <?php echo form_dropdown('srch_ledger_account', ['' => 'All'] + $ledger_opt, set_value('srch_ledger_account', $srch_ledger_account), 'id="srch_ledger_account" class="form-control"'); ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Select Opening Type</label>
                        <?php echo form_dropdown('srch_opening_type', ['' => 'All'] + $opening_type_opt, set_value('srch_opening_type', ), 'id="srch_opening_type" class="form-control"'); ?>
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
                        <th>Ledger Name</th>
                        <th class="text-right">Opening Balance</th>
                        <th class="text-center">Opening Type</th>
                        <th colspan="2" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $prev_group_name = "";
                    $prev_ledger_name = "";
                    foreach ($record_list as $j => $ls) {
                        $group_name = "";
                        $ledger_name = "";
                        if ($ls['group_name'] != $prev_group_name) {
                            $group_name = $ls['group_name'];
                            $prev_group_name = $ls['group_name'];
                        }
                        if ($ls['ledger_name'] != $prev_ledger_name) {
                            $ledger_name = $ls['ledger_name'];
                            $prev_ledger_name = $ls['ledger_name'];
                        }


                        ?>

                        <tr class="mb-3">
                            <td class="text-center"><?php echo ($j + 1); ?></td>
                            <td><?php echo $group_name ?></td>
                            <td><?php echo $ledger_name ?></td>
                            <td class="text-right"><?php echo number_format($ls['opening_balance'], 2) ?></td>
                            <td class="text-center"><?php echo $ls['opening_type'] ?></td>

                            <td class="text-center">
                                <button data-toggle="modal" data-target="#edit_modal" value="<?php echo $ls['ledger_id'] ?>"
                                    class="edit_record btn btn-primary btn-xs" title="Edit"><i
                                        class="fa fa-edit"></i></button>
                            </td>
                            <td class="text-center">
                                <button value="<?php echo $ls['ledger_id'] ?>" class="del_record btn btn-danger btn-xs"
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
                        <form method="post" action="<?php echo site_url('ledger-accounts-list'); ?>" id="frmadd"
                            enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel"><strong>Add Ledger Account</strong></h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Ledger Name</label>
                                        <input type="text" name="ledger_name" class="form-control" required
                                            placeholder="Enter Ledger name" id="ledger_name">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="group_id">Select Account Group <span
                                                style="color:red;">*</span></label>
                                        <?php echo form_dropdown('group_id', ['' => 'All'] + $group_opt, set_value('group_id'), 'id="group_id" class="form-control"'); ?>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Opening Balance</label>
                                        <input type="number" name="opening_balance" class="form-control"
                                            placeholder="000.00" required id="opening_balance">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Opening Type</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="opening_type" value="Debit" checked="true" />
                                                Debit
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="opening_type" value="Credit" /> Credit
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
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
                        <form method="post" action="<?php echo site_url('ledger-accounts-list'); ?>" id="frmedit"
                            class="form-material">
                            <div class="modal-header">

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel">Edit Ledger Account </h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="ledger_id" id="ledger_id" value="" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Ledger Name</label>
                                        <input type="text" name="ledger_name" class="form-control" required
                                            placeholder="Enter Ledger name" id="ledger_name">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="group_id">Select Account Group <span
                                                style="color:red;">*</span></label>
                                        <?php echo form_dropdown('group_id', ['' => 'All'] + $group_opt, set_value('group_id'), 'id="group_id" class="form-control"'); ?>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Opening Balance</label>
                                        <input type="number" name="opening_balance" class="form-control"
                                            id="opening_balance" placeholder="000.00" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Opening Type</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="opening_type" value="Debit" checked="true" />
                                                Debit
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="opening_type" value="Credit" /> Credit
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
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
<?php include_once(VIEWPATH . 'inc/footer.php'); ?>