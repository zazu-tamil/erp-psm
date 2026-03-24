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
                        <th>Item Description</th>
                        <th>UOM</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>VAT</th>
                        <th>Amount</th>
                        <th colspan="2" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($record_list as $j => $ls) { ?>
                        <tr class="mb-3">
                            <td class="text-center"><?php echo ($j + 1); ?></td>
                            <td>
                                <?php echo $ls['item_code'] ?><br><br>
                                <?php echo $ls['item_desc'] ?>
                            </td>
                            <td><?php echo $ls['uom'] ?></td>
                            <td><?php echo $ls['qty'] ?></td>
                            <td><?php echo $ls['rate'] ?></td>
                            <td><?php echo $ls['vat'] ?></td>
                            <td><?php echo $ls['amount'] ?></td>
                            <td class="text-center">
                                <button data-toggle="modal" data-target="#edit_modal"
                                    value="<?php echo $ls['in_stock_item_id'] ?>" class="edit_record btn btn-primary btn-xs"
                                    title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>
                            <td class="text-center">
                                <button value="<?php echo $ls['in_stock_item_id'] ?>"
                                    class="del_record btn btn-danger btn-xs" title="Delete">
                                    <i class="fa fa-remove"></i>
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- ==================== ADD MODAL ==================== -->
            <div class="modal fade" id="add_modal" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo site_url('in-stock-item-list'); ?>" id="frmadd"
                            enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="addModalLabel"><strong>Add <?php echo $title; ?></strong>
                                </h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Item Code <span class="text-red">*</span></label>
                                        <input class="form-control" type="text" name="item_code" id="add_item_code"
                                            placeholder="Item Code" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>UOM <span class="text-red">*</span></label>
                                        <input class="form-control" type="text" name="uom" id="add_uom"
                                            placeholder="UOM" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Item Description</label>
                                        <textarea name="item_desc" id="add_item_desc" class="form-control"
                                            rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Qty <span class="text-red">*</span></label>
                                        <input class="form-control add_calc" type="text" name="qty" id="add_qty"
                                            data-field="qty" placeholder="Qty" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Rate <span class="text-red">*</span></label>
                                        <input class="form-control add_calc" type="text" name="rate" id="add_rate"
                                            data-field="rate" placeholder="Rate" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>VAT (%) <span class="text-red">*</span></label>
                                        <input class="form-control add_calc" type="text" name="vat" id="add_vat"
                                            data-field="vat" placeholder="VAT" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Select Vendor</label>
                                        <?php echo form_dropdown('vendor_id', ['' => 'Select'] + $vendor_opt, set_value('vendor_id'), 'id="add_vendor_id" class="form-control"'); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Amount</label>
                                        <input class="form-control" type="text" name="amount" id="add_amount"
                                            placeholder="Amount" required readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Status</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" value="Active" checked /> Active
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

            <!-- ==================== EDIT MODAL ==================== -->
            <div class="modal fade" id="edit_modal" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo site_url('in-stock-item-list'); ?>" id="frmedit"
                            class="form-material">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="editModalLabel"><strong>Edit <?php echo $title; ?></strong>
                                </h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="in_stock_item_id" id="edit_in_stock_item_id" value="" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Item Code</label>
                                        <input class="form-control" type="text" name="item_code" id="edit_item_code"
                                            placeholder="Item Code" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>UOM</label>
                                        <input class="form-control" type="text" name="uom" id="edit_uom"
                                            placeholder="UOM" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Item Description</label>
                                        <textarea name="item_desc" id="edit_item_desc" class="form-control"
                                            rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Qty</label>
                                        <input class="form-control edit_calc" type="text" name="qty" id="edit_qty"
                                            data-field="qty" placeholder="Qty" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Rate</label>
                                        <input class="form-control edit_calc" type="text" name="rate" id="edit_rate"
                                            data-field="rate" placeholder="Rate" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>VAT (%)</label>
                                        <input class="form-control edit_calc" type="text" name="vat" id="edit_vat"
                                            data-field="vat" placeholder="VAT" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Select Vendor</label>
                                        <?php echo form_dropdown('vendor_id', ['' => 'Select'] + $vendor_opt, set_value('vendor_id'), 'id="edit_vendor_id" class="form-control"'); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Amount</label>
                                        <input class="form-control" type="text" name="amount" id="edit_amount"
                                            placeholder="Amount" required readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Status</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" value="Active" checked /> Active
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

        </div><!-- /.box-body -->

        <div class="box-footer">
            <div class="form-group col-sm-6">
                <label>Total Records : <?php echo $total_records; ?></label>
            </div>
            <div class="form-group col-sm-6">
                <?php echo $pagination; ?>
            </div>
        </div>

    </div><!-- /.box -->
</section>
<!-- /.content -->
<?php include_once(VIEWPATH . 'inc/footer.php'); ?>

<script>
    $(document).ready(function () {

         function calcAddAmount() {
            var qty = parseFloat($('#add_qty').val()) || 0;
            var rate = parseFloat($('#add_rate').val()) || 0;
            var vat = parseFloat($('#add_vat').val()) || 0;
            var base = qty * rate;
            var total = base + (base * vat / 100);
            $('#add_amount').val(total.toFixed(2));
        }

        $(document).on('keyup change', '#add_qty, #add_rate, #add_vat', function () {
            calcAddAmount();
        });
 
        function calcEditAmount() {
            var qty = parseFloat($('#edit_qty').val()) || 0;
            var rate = parseFloat($('#edit_rate').val()) || 0;
            var vat = parseFloat($('#edit_vat').val()) || 0;
            var base = qty * rate;
            var total = base + (base * vat / 100);
            $('#edit_amount').val(total.toFixed(2));
        }

        $(document).on('keyup change', '#edit_qty, #edit_rate, #edit_vat', function () {
            calcEditAmount();
        });

        /* ---- Reset Add modal on open ---- */
        $('#add_modal').on('show.bs.modal', function () {
            $('#frmadd')[0].reset();
            $('#add_amount').val('');
        });

        /* ---- Edit record – fetch data via AJAX ---- */
        $(document).on('click', '.edit_record', function () {
            var id = $(this).val();
            $.ajax({
                url: "<?php echo site_url('get-data'); ?>",
                type: 'post',
                data: { tbl: 'in_stock_item_info', id: id },
                dataType: 'json',
                success: function (d) {
                    $('#edit_in_stock_item_id').val(d.in_stock_item_id);
                    $('#edit_item_code').val(d.item_code);
                    $('#edit_uom').val(d.uom);
                    $('#edit_item_desc').val(d.item_desc);
                    $('#edit_qty').val(d.qty);
                    $('#edit_rate').val(d.rate);
                    $('#edit_vat').val(d.vat);
                    $('#edit_amount').val(d.amount);
                    $('#edit_vendor_id').val(d.vendor_id).trigger('change');
                    $('#edit_modal input:radio[name="status"][value="' + d.status + '"]').prop('checked', true);
                    $('#edit_modal').modal('show');
                },
                error: function (xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                }
            });
        });

        /* ---- Delete record ---- */
        $(document).on('click', '.del_record', function () {
            var id = $(this).val();
            if (confirm('Are you sure you want to delete?')) {
                $.ajax({
                    url: "<?php echo site_url('delete-record'); ?>",
                    type: 'post',
                    data: { tbl: 'in_stock_item_info', id: id },
                    success: function (d) {
                        alert(d);
                        location.reload();
                    }
                });
            }
        });

    });
</script>