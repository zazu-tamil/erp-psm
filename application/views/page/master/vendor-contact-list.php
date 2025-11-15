<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<section class="content-header">
    <h1>Vendor Contact List</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Master</a></li>
        <li class="active">Vendor Contact List</li>
    </ol>
</section>

<section class="content">
        <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>Vendor</label>
                        <?php echo form_dropdown('srch_vendor_id', $vendor_opt, set_value('srch_vendor_id', $srch_vendor_id), 'id="srch_vendor_id" class="form-control select2"'); ?>
                    </div> 
                    <div class="form-group col-md-3 text-left">
                        <br>
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="box box-info">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#add_modal">
                <span class="fa fa-plus-circle"></span> Add New Contact
            </button>
        </div>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
        <?php endif; ?>

        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped" id="contact_list">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Vendor</th>
                        <th>Contact Person</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th colspan="2" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($record_list as $j => $ls): ?>
                        <tr>
                            <td class="text-center"><?php echo ($j + 1 + $sno); ?></td>
                            <td><?php echo htmlspecialchars($ls['vendor_name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($ls['contact_person_name']); ?></td>
                            <td><?php echo htmlspecialchars($ls['department']); ?></td>
                            <td><?php echo htmlspecialchars($ls['designation']); ?></td>
                            <td><a href="tel:<?php echo $ls['mobile']; ?>"><?php echo $ls['mobile']; ?></a></td>
                            <td><a href="mailto:<?php echo $ls['email']; ?>"><?php echo $ls['email']; ?></a></td>
                            <td><?php echo nl2br(htmlspecialchars($ls['address'])); ?></td>
                            <td><?php echo $ls['status']; ?></td>
                            <td>
                                <button data-toggle="modal" data-target="#edit_modal"
                                    value="<?php echo $ls['vendor_contact_id']; ?>"
                                    class="edit_record btn btn-primary btn-xs" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>
                            <td>
                                <?php if ($this->session->userdata(SESS_HD . 'level') == 'Admin'): ?>
                                    <button value="<?php echo $ls['vendor_contact_id']; ?>"
                                        class="del_record btn btn-danger btn-xs" title="Delete">
                                        <i class="fa fa-remove"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- ADD MODAL -->
            <div class="modal fade" id="add_modal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="post" action="" id="frmadd">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h3 class="modal-title">Add Contact Person</h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Vendor <span class="text-danger">*</span></label>
                                        <?php echo form_dropdown('vendor_id', $vendor_opt, '', 'class="form-control" required'); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Contact Person Name</label>
                                        <input type="text" name="contact_person_name" class="form-control" placeholder="Name" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Department</label>
                                        <input type="text" name="department" class="form-control" placeholder="Department">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Designation</label>
                                        <input type="text" name="designation" class="form-control" placeholder="Designation">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Mobile</label>
                                        <input type="text" name="mobile" class="form-control" placeholder="Mobile">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" placeholder="Email">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Status</label><br>
                                        <label><input type="radio" name="status" value="Active" checked> Active</label>
                                        <label class="ml-3"><input type="radio" name="status" value="InActive"> InActive</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="address" class="form-control" rows="3" placeholder="Full Address"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <input type="submit" value="Save" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- EDIT MODAL -->
            <div class="modal fade" id="edit_modal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="post" action="" id="frmedit">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h3 class="modal-title">Edit Contact Person</h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="vendor_contact_id" id="vendor_contact_id" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Vendor</label>
                                        <?php echo form_dropdown('vendor_id', $vendor_opt, '', 'id="vendor_id" class="form-control" required'); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Contact Person Name</label>
                                        <input type="text" name="contact_person_name" id="contact_person_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Department</label>
                                        <input type="text" name="department" id="department" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Designation</label>
                                        <input type="text" name="designation" id="designation" class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Mobile</label>
                                        <input type="text" name="mobile" id="mobile" class="form-control">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Email</label>
                                        <input type="email" name="email" id="email" class="form-control">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Status</label><br>
                                        <label><input type="radio" name="status" value="Active"> Active</label>
                                        <label class="ml-3"><input type="radio" name="status" value="InActive"> InActive</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="address" id="address" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <input type="submit" value="Update" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="box-footer">
            <div class="form-group col-sm-6">
                <label>Total Records: <?php echo $total_records; ?></label>
            </div>
            <div class="form-group col-sm-6">
                <?php echo $pagination; ?>
            </div>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>