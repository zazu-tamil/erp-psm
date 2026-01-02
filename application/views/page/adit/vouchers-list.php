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
                        <label for="srch_from_date">From Date</label>
                        <input type="date" name="srch_from_date" id="srch_from_date" class="form-control"
                            value="<?php echo set_value('srch_from_date', $srch_from_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_to_date">To Date</label>
                        <input type="date" name="srch_to_date" id="srch_to_date" class="form-control"
                            value="<?php echo set_value('srch_to_date', $srch_to_date); ?>">
                    </div>
                   
                    <div class="form-group col-md-3">
                        <label>Voucher Narration</label>
                        <?php echo form_dropdown('srch_voucher_narration_id', $voucher_narration_opt, set_value('srch_voucher_narration_id', $srch_voucher_narration_id), 'id="srch_voucher_narration_id" class="form-control"'); ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Voucher Type</label>
                        <?php echo form_dropdown('srch_voucher_type', $voucher_type_opt, set_value('srch_voucher_type', $srch_voucher_type), 'id="srch_voucher_type" class="form-control"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
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
                        <th>Voucher Date</th>
                        <th>Voucher Type</th>
                        <th>Voucher Narration</th>
                        <th>Status</th>
                        <th colspan="2" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($record_list as $j => $ls) {
                        ?>
                        <tr class="mb-3">
                            <td class="text-center"><?php echo ($j + 1); ?></td>
                            <td><?php echo date('d-m-Y', strtotime($ls['voucher_date'])) ?></td>
                            <td><?php echo $ls['voucher_type'] ?></td>
                            <td><?php echo $ls['narration'] ?></td>
                            <td><?php echo $ls['status'] ?></td>

                            <td class="text-center">
                                <button data-toggle="modal" data-target="#edit_modal"
                                    value="<?php echo $ls['voucher_id'] ?>" class="edit_record btn btn-primary btn-xs"
                                    title="Edit"><i class="fa fa-edit"></i></button>
                            </td>
                            <td class="text-center">
                                <button value="<?php echo $ls['voucher_id'] ?>" class="del_record btn btn-danger btn-xs"
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
                        <form method="post" action="<?php echo site_url('vouchers-list'); ?>" id="frmadd"
                            enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel"><strong>Add Voucher</strong></h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Voucher Date</label>
                                        <input type="date" name="voucher_date" class="form-control" id="voucher_date"
                                            required value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Voucher Type</label>
                                        <select name="voucher_type" class="form-control" id="voucher_type">
                                            <option value="Journal">Journal</option>
                                            <option value="Payment">Payment</option>
                                            <option value="Receipt">Receipt</option>
                                            <option value="Contra">Contra</option>
                                            <option value="Sales">Sales</option>
                                            <option value="Purchase">Purchase</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Narration</label>
                                        <textarea name="narration" class="form-control" rows="5"
                                            placeholder="Enter your narration" id="narration"></textarea>
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
                        <form method="post" action="<?php echo site_url('vouchers-list'); ?>" id="frmedit"
                            class="form-material">
                            <div class="modal-header">

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel">Edit Account Group </h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="voucher_id" id="voucher_id" value="" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Voucher Date</label>
                                        <input type="date" name="voucher_date" class="form-control" id="voucher_date"
                                            required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Voucher Type</label>
                                        <select name="voucher_type" class="form-control" id="voucher_type">
                                            <option value="Journal">Journal</option>
                                            <option value="Payment">Payment</option>
                                            <option value="Receipt">Receipt</option>
                                            <option value="Contra">Contra</option>
                                            <option value="Sales">Sales</option>
                                            <option value="Purchase">Purchase</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Narration</label>
                                        <textarea name="narration" class="form-control" rows="5" id="narration"
                                            placeholder="Enter your narration"></textarea>
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
<?php include_once(VIEWPATH . 'inc/footer.php'); ?>