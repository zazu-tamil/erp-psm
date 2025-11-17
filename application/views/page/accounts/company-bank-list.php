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
    <!-- Search Filter -->
    <div class="box box-info no-print">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('company-bank-list') ?>" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>Company</label>
                        <?php
                        $company_options = array('' => 'All');
                        foreach ($companies as $company) {
                            $company_options[$company['company_id']] = $company['company_name'];
                        }
                        echo form_dropdown('srch_company', $company_options, set_value('srch_company', $srch_company), 'id="srch_company" class="form-control"');
                        ?>
                    </div>
                    <div class="form-group col-md-2 text-left">
                        <br />
                        <button class="btn btn-success" name="btn_show" value="Show"><i class="fa fa-search"></i> Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                        <th>Company</th>
                        <th>Bank Name</th>
                        <th>Branch</th>
                        <th>Account Type</th>
                        <th>Account No</th>
                        <th>IFSC Code</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 8%;">Edit</th>
                        <th class="text-center" style="width: 8%;">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)) : ?>
                        <?php foreach ($record_list as $j => $ls) : ?>
                            <tr>
                                <td class="text-center"><?php echo ($j + 1); ?></td>
                                <td><?php echo isset($ls['company_name']) ? $ls['company_name'] : 'N/A'; ?></td>
                                <td><?php echo isset($ls['bank_name']) ? $ls['bank_name'] : ''; ?></td>
                                <td><?php echo isset($ls['branch']) ? $ls['branch'] : ''; ?></td>
                                <td><?php echo isset($ls['account_type_name']) ? $ls['account_type_name'] : 'N/A'; ?></td>
                                <td><?php echo isset($ls['account_no']) ? $ls['account_no'] : ''; ?></td>
                                <td><?php echo isset($ls['IFSC_code']) ? $ls['IFSC_code'] : ''; ?></td>
                                <td><?php echo isset($ls['remarks']) ? $ls['remarks'] : ''; ?></td>
                                <td><?php echo isset($ls['status']) ? $ls['status'] : 'N/A'; ?></td>
                                <td class="text-center">
                                    <button data-toggle="modal" data-target="#edit_modal" value="<?php echo $ls['bank_id']; ?>" class="edit_record btn btn-primary btn-xs" title="Edit"><i class="fa fa-edit"></i></button>
                                </td>
                                <td class="text-center">
                                    <button value="<?php echo $ls['bank_id']; ?>" class="del_record btn btn-danger btn-xs" title="Delete"><i class="fa fa-remove"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
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
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="pagination-links">
                <?php echo $pagination; ?>
            </div>

            <!-- Add Modal -->
            <div class="modal fade" id="add_modal" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <form method="post" action="<?php echo site_url('company-bank-list'); ?>" id="frmadd" enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                <h3 class="modal-title">Add Company Bank Info</h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6 mb-3">
                                        <label>Company <span style="color:red;">*</span></label>
                                        <?php echo form_dropdown('company', $company_opt, set_value('company'), 'id="company" class="form-control" required'); ?>
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label>Bank Name <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="bank_name" id="bank_name" required>
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label>Branch <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="branch" id="branch" required>
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label>Account Type <span style="color:red;">*</span></label>
                                        <?php echo form_dropdown('account_type', $account_types, set_value('account_type'), 'id="account_type" class="form-control" required'); ?>
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label>Account Number <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="account_no" id="account_no" required>
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label>IFSC Code <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="IFSC_code" id="IFSC_code" required>
                                    </div>

                                     <div class="form-group col-md-6">
                                        <label for="qr_code">QR Code</label>
                                        <input class="form-control" type="file" name="qr_code" id="qr_code"
                                            accept="image/*">
                                        <div id="preview_qr_code" class="mt-2">
                                            <img id="qr_code_img_preview" src="" alt="QR Code Preview"
                                                style="max-height:300px; display:none; border:1px solid #ccc; padding:5px;"
                                                width="300px" />
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12 mb-3">
                                        <label>Remarks</label>
                                        <textarea class="form-control" name="remarks" id="remarks"></textarea>
                                    </div>
                                    <div class="form-group col-md-12 mb-3">
                                        <label>Status</label><br>
                                        <label><input type="radio" name="status" value="Active" checked> Active</label>&nbsp;&nbsp;&nbsp;
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
                        <form method="post" action="<?php echo site_url('company-bank-list'); ?>" id="frmedit" enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                <h3 class="modal-title">Edit Company Bank Info</h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="bank_id" id="bank_id" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6 mb-3">
                                        <label>Company <span style="color:red;">*</span></label>
                                        <?php echo form_dropdown('company', $company_opt, set_value('company'), 'id="company_edit" class="form-control" required'); ?>
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label>Bank Name <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="bank_name" id="bank_name_edit" required>
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label>Branch <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="branch" id="branch_edit" required>
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label>Account Type <span style="color:red;">*</span></label>
                                        <?php echo form_dropdown('account_type', $account_types, set_value('account_type'), 'id="account_type_edit" class="form-control" required'); ?>
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label>Account Number <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="account_no" id="account_no_edit" required>
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label>IFSC Code <span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="IFSC_code" id="IFSC_code_edit" required>
                                    </div>

                                    
                                    <div class="form-group col-md-4">
                                        <label for="qr_code">QR Code</label>
                                        <input class="form-control" type="file" name="qr_code" id="qr_code"
                                            accept="image/*">
                                        <div id="preview_qr_code" class="mt-2">
                                            <img id="qr_code_img_preview" src="" alt="QR Code Preview"
                                                style="max-height:200px; display:none; border:1px solid #ccc; padding:5px;"
                                                width="200px" />
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12 mb-3">
                                        <label>Remarks</label>
                                        <textarea class="form-control" name="remarks" id="remarks_edit"></textarea>
                                    </div>

                                      
                                    <div class="form-group col-md-12 mb-3">
                                        <label>Status</label><br>
                                        <label><input type="radio" name="status" value="Active" checked> Active</label>&nbsp;&nbsp;&nbsp;
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

