<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<section class="content-header">
    <h1>Currency List</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-money"></i> Master</a></li>
        <li class="active">Currency List</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
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
                        <th>Currency Code</th>
                        <th>Currency Name</th>
                        <th>Symbol</th>
                        <th>Country Name</th>
                        <th>Decimal Piint</th>
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
                            <td><?php echo $ls['currency_code'] ?></td>
                            <td><?php echo $ls['currency_name'] ?></td>
                            <td><?php echo $ls['symbol'] ?></td>
                            <td><?php echo $ls['country_name'] ?></td>
                            <!-- decimal point bASED -->
                            <td><?php echo $ls['decimal_point'] ?></td>

                            <td><?php echo $ls['status'] ?></td>
                            <td class="text-center">
                                <button data-toggle="modal" data-target="#edit_modal"
                                    value="<?php echo $ls['currency_id'] ?>" class="edit_record btn btn-primary btn-xs"
                                    title="Edit"><i class="fa fa-edit"></i></button>
                            </td>
                            <td class="text-center">
                                <button value="<?php echo $ls['currency_id'] ?>" class="del_record btn btn-danger btn-xs"
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
                        <form method="post" action="<?php echo site_url('currency-list'); ?>" id="frmadd"
                            enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel"><strong>Add Currency Info</strong></h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>
                            <div class="modal-body">
                                <div class="row">

                                    <div class="form-group col-md-6">
                                        <label>Currency Code</label>
                                        <input type="text" class="form-control" name="currency_code" id="currency_code"
                                            placeholder="Currency Code" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Currency Name</label>
                                        <input type="text" class="form-control" name="currency_name" id="currency_name"
                                            placeholder="Currency Name" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Symbol</label>
                                        <input type="text" class="form-control" name="symbol" id="symbol"
                                            placeholder="Symbol">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Country Name</label>
                                        <input type="text" class="form-control" name="country_name" id="country_name"
                                            placeholder="Country Name">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Exchange Rate</label>
                                        <input type="number" class="form-control" name="exchange_rate"
                                            id="exchange_rate" placeholder="Exchange Rate" step="0.0001" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Decimal Point</label>
                                        <input type="number" class="form-control" name="decimal_point"
                                            id="decimal_point" placeholder="Decimal Point" step="0.000" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Is Base Currency</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="is_base_currency" value="1">
                                                Yes
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="is_base_currency" value="0" checked>
                                                No
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Status</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" value="Active" checked>
                                                Active
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" value="Inactive">
                                                Inactive
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
                        <form method="post" action="<?php echo site_url('currency-list'); ?>" id="frmedit"
                            class="form-material">
                            <div class="modal-header">

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel">Edit Currency </h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="currency_id" id="currency_id" value="" />
                            </div>
                            <div class="modal-body">
                                <div class="row">

                                    <div class="form-group col-md-6">
                                        <label>Currency Code</label>
                                        <input type="text" class="form-control" name="currency_code" id="currency_code"
                                            placeholder="Currency Code" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Currency Name</label>
                                        <input type="text" class="form-control" name="currency_name" id="currency_name"
                                            placeholder="Currency Name" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Symbol</label>
                                        <input type="text" class="form-control" name="symbol" id="symbol"
                                            placeholder="Symbol">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Country Name</label>
                                        <input type="text" class="form-control" name="country_name" id="country_name"
                                            placeholder="Country Name">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Exchange Rate</label>
                                        <input type="number" class="form-control" name="exchange_rate"
                                            id="exchange_rate" placeholder="Exchange Rate" step="0.0001" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Decimal Point</label>
                                        <input type="number" class="form-control" name="decimal_point"
                                            id="decimal_point" placeholder="Decimal Point" step="0.000" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Is Base Currency</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="is_base_currency" value="1">
                                                Yes
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="is_base_currency" value="0" checked>
                                                No
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Status</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" value="Active" checked>
                                                Active
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" value="Inactive">
                                                Inactive
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