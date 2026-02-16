<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header">
    <h1>Company Bank List</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Petty Cash</a></li>
        <li class="active">Company Bank List</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <!-- Data Table -->
    <div class="box box-info">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#add_modal">
                <span class="fa fa-plus-circle"></span> Add New
            </button>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped" id="company_bank_list">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">S.No</th>
                        <th>Account Name</th>
                        <th>Bank Name</th>
                        <th>Account No</th>
                        <th>IBAN No</th>
                        <th>SWIFT Code</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th class="text-center" colspan="2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)): ?>
                        <?php foreach ($record_list as $j => $ls): ?>
                            <tr>
                                <td class="text-center"><?php echo ($j + 1); ?></td>
                                <td><?php echo $ls['account_name']; ?></td>
                                <td><?php echo $ls['bank_name']; ?></td>
                                <td><?php echo $ls['account_number']; ?></td>
                                <td><?php echo $ls['iban_no']; ?></td>
                                <td><?php echo $ls['swift_code']; ?></td>
                                <td><?php echo $ls['remarks']; ?></td>
                                <td class="text-center">
                                    <button data-toggle="modal" data-target="#edit_modal" value="<?php echo $ls['bank_id']; ?>"
                                        class="edit_record btn btn-primary btn-xs" title="Edit"><i
                                            class="fa fa-edit"></i></button>
                                </td>
                                <td class="text-center">
                                    <button value="<?php echo $ls['bank_id']; ?>" class="del_record btn btn-danger btn-xs"
                                        title="Delete"><i class="fa fa-remove"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center">No records found</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>


            <!-- Add Modal -->
            <div class="modal fade" id="add_modal" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <form method="post" action="<?php echo site_url('company-bank-list'); ?>" id="frmadd"
                            enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                <h3 class="modal-title">Add Company Bank Info</h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>
                            <div class="modal-body">
                                <div class="row">

                                    <div class="form-group col-md-6 mb-3">
                                        <label>Account Name <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="account_name" id="account_name"
                                            required placeholder="Enter Account Name">
                                    </div>

                                    <div class="form-group col-md-6 mb-3">
                                        <label>Bank Name <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="bank_name" id="bank_name" required
                                            placeholder="Enter Bank Name">
                                    </div>

                                    <div class="form-group col-md-6 mb-3">
                                        <label>Account Number <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="account_number"
                                            id="account_number" required placeholder="EX:0000 0000 0000">
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label> IBAN Number <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="iban_no" id="iban_no"
                                            placeholder="Enter IBAN No" required>
                                    </div>
                                    <div class="form-group col-md-12 mb-3">
                                        <label>SWIFT Code <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="swift_code" id="swift_code"
                                            placeholder="Enter SWIFT Code" required>
                                    </div>
                                    <div class="form-group col-md-12 mb-3">
                                        <label>Remarks</label>
                                        <textarea class="form-control" name="remarks" id="remarks"></textarea>
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

            <!-- Edit Modal -->
            <div class="modal fade" id="edit_modal" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <form method="post" action="<?php echo site_url('company-bank-list'); ?>" id="frmedit"
                            enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                <h3 class="modal-title">Edit Company Bank Info</h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="bank_id" id="bank_id" />
                            </div>
                            <div class="modal-body">
                                <div class="row">

                                    <div class="form-group col-md-6 mb-3">
                                        <label>Account Name <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="account_name" id="account_name"
                                            required placeholder="Enter Account Name">
                                    </div>

                                    <div class="form-group col-md-6 mb-3">
                                        <label>Bank Name <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="bank_name" id="bank_name" required
                                            placeholder="Enter Bank Name">
                                    </div>

                                    <div class="form-group col-md-6 mb-3">
                                        <label>Account Number <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="account_number"
                                            id="account_number" required placeholder="EX:0000 0000 0000">
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label> IBAN Number <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="iban_no" id="iban_no"
                                            placeholder="Enter IBAN No" required>
                                    </div>
                                    <div class="form-group col-md-12 mb-3">
                                        <label>SWIFT Code <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="swift_code" id="swift_code"
                                            placeholder="Enter SWIFT Code" required>
                                    </div>
                                    <div class="form-group col-md-12 mb-3">
                                        <label>Remarks</label>
                                        <textarea class="form-control" name="remarks" id="remarks"></textarea>
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
                                <input type="submit" value="Update" class="btn btn-primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>

<script>

    jQuery(function ($) {
        $(document).on("click", ".edit_record", function () {
            let id = $(this).val();
            $.ajax({
                url: "<?php echo site_url('get-data'); ?>",
                type: "post",
                data: { tbl: "company_bank_info", id: id },
                dataType: "json",
                success: function (d) {
                    $("#edit_modal #bank_id").val(d.bank_id);
                    $("#edit_modal #account_name").val(d.account_name);
                    $("#edit_modal #bank_name").val(d.bank_name);
                    $("#edit_modal #account_number").val(d.account_number);
                    $("#edit_modal #iban_no").val(d.iban_no);
                    $("#edit_modal #swift_code").val(d.swift_code);
                    $("#edit_modal #remarks").val(d.remarks);
                    $('#edit_modal input:radio[value="' + d.status + '"]').prop(
                        "checked",
                        true
                    );

                    $("#edit_modal").modal("show");
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", xhr.responseText);
                },
            });
        });

        // Delete Record AJAX
        $(document).on("click", ".del_record", function () {
            let id = $(this).val();
            if (confirm("Are you sure you want to delete?")) {
                $.ajax({
                    url: "<?php echo site_url('delete-record'); ?>",
                    type: "post",
                    data: { tbl: "company_bank_info", id: id },
                    success: function (d) {
                        alert(d);
                        location.reload();
                    },
                });
            }
        });
    });

</script>