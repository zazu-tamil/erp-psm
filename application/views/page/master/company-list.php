<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<section class="content-header">
    <h1>Company List</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Master</a></li>
        <li class="active">Company List</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="box box-success">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" id="btn_add_new" data-toggle="modal"
                data-target="#add_modal">
                <span class="fa fa-plus-circle"></span> Add New
            </button>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped table-responsive" id="company_list">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th>Company Name</th>
                        <th>Mobile / Email</th>
                        <th>Address</th>
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
                            <td><?php echo $ls['company_name'] ?><br />
                                <i class="badge"><?php echo $ls['GST'] ?></i>
                            </td>
                            <td>
                                <?php echo '<i class="fa fa-phone"></i>  ' . ($ls['mobile']); ?><br />
                                <?php echo '<i class="fa fa-envelope"></i> ' . $ls['email'] ?>
                            </td>
                            <td><?php echo str_replace("\n", "<br>", $ls['address']); ?></td>
                            <td><?php echo $ls['status'] ?></td>
                            <td class="text-center">
                                <button data-toggle="modal" data-target="#edit_modal"
                                    value="<?php echo $ls['company_id'] ?>" class="edit_record btn btn-primary btn-xs"
                                    title="Edit"><i class="fa fa-edit"></i></button>
                            </td>
                            <td class="text-center">
                                <button value="<?php echo $ls['company_id'] ?>" class="del_record btn btn-danger btn-xs"
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
                        <form method="post" action="<?php echo site_url('company-list'); ?>" id="frmadd"
                            enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel"><strong>Add Company Info</strong></h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Company Name</label>
                                        <input class="form-control" type="text" name="company_name" id="company_name"
                                            value="" placeholder="Company Name" required="true">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Contact Name</label>
                                        <input class="form-control" type="text" name="contact_name" id="contact_name"
                                            placeholder="Contact Name">
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="form-group col-md-6">
                                        <label>Mobile</label>
                                        <input class="form-control" type="text" name="mobile" id="mobile"
                                            placeholder="Enter your Mobile" required="true">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Email</label>
                                        <input class="form-control" type="email" name="email" id="email" value=""
                                            placeholder="Email ID" required="true">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Cr No</label>
                                        <input class="form-control" type="text" name="crno" id="crno"
                                            placeholder="Enter your Cr No">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>VAT No</label>
                                        <input class="form-control" type="text" name="GST" id="GST"
                                            placeholder="Enter your VAT No" required="true">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Company Code</label>
                                        <input class="form-control" type="text" name="company_code" id="company_code"
                                            placeholder="Enter your Company Code" required="true">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Country <span class="text-danger">*</span></label>
                                            <?php echo form_dropdown('country', ['' => 'Select Country'] + $country_opt, set_value('country'), 'id="country" class="form-control" required'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="ltr_header_img">Letter Pad Header Image</label>
                                        <input class="form-control" type="file" name="ltr_header_img"
                                            id="ltr_header_img" accept="image/*">
                                        <div id="preview_header" class="mt-2">
                                            <img id="header_img" src="" alt="Header Preview"
                                                style="max-height:200px; display:none; border:1px solid #ccc; padding:5px;"
                                                width="400px" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control" name="address" id="address"
                                                placeholder="Address" required="true" rows="4"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="nav-tabs-custom">
                                        <ul class="nav nav-tabs">
                                            <li class="active">
                                                <a href="#tab_1" data-toggle="tab">Terms & Conditions Quotation</a>
                                            </li>
                                            <li>
                                                <a href="#tab_2" data-toggle="tab">Terms & Conditions Invoice</a>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                            <!-- Tab 1 -->
                                            <div class="tab-pane active" id="tab_1">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Quotation Terms</label>
                                                            <textarea id="editor1" name="quote_terms"
                                                                class="form-control custom-textarea"
                                                                placeholder="Enter quotation terms" required></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tab 2 -->
                                            <div class="tab-pane" id="tab_2">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Invoice Terms</label>
                                                            <textarea id="editor2" name="invoice_terms"
                                                                class="form-control custom-textarea"
                                                                placeholder="Enter invoice terms" required></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Select Bank <span class="text-danger">*</span></label>
                                            <?php echo form_dropdown('bank_id', ['' => 'Select Bank'] + $bank_opt, set_value('bank_id'), 'id="bank_id" class="form-control" required'); ?>
                                        </div>
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
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo site_url('company-list'); ?>" id="frmedit"
                            class="form-material" enctype="multipart/form-data">
                            <div class="modal-header">

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel">Edit Company</h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="company_id" id="company_id" value="" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Company Name</label>
                                        <input class="form-control" type="text" name="company_name" id="company_name"
                                            value="" placeholder="Company Name" required="true">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Contact Name</label>
                                        <input class="form-control" type="text" name="contact_name" id="contact_name"
                                            placeholder="Contact Name">
                                    </div>


                                    <div class="form-group col-md-4">
                                        <label>Mobile</label>
                                        <input class="form-control" type="text" name="mobile" id="mobile"
                                            placeholder="Enter your Mobile" required="true">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Email</label>
                                        <input class="form-control" type="email" name="email" id="email" value=""
                                            placeholder="Email ID" required="true">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Cr No</label>
                                        <input class="form-control" type="text" name="crno" id="crno"
                                            placeholder="Enter your Cr No">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>VAT No</label>
                                        <input class="form-control" type="text" name="GST" id="GST"
                                            placeholder="Enter your VAT No" required="true">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Company Code</label>
                                        <input class="form-control" type="text" name="company_code" id="company_code"
                                            placeholder="Enter your Company Code" required="true">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Country <span class="text-danger">*</span></label>
                                            <?php echo form_dropdown('country', ['' => 'Select Country'] + $country_opt, set_value('country'), 'id="country" class="form-control" required'); ?>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="ltr_header_img">Letter Pad Header Image</label>
                                        <input class="form-control" type="file" name="ltr_header_img"
                                            id="ltr_header_img" accept="image/*">
                                        <div id="preview_header" class="mt-2">
                                            <!-- Existing image OR preview new -->
                                            <img id="header_img" src="" alt="Header Preview"
                                                style="max-height:200px; display:none; border:1px solid #ccc; padding:5px;"
                                                width="400px" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control" name="address" id="address"
                                                placeholder="Address" required="true" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="nav-tabs-custom">
                                        <ul class="nav nav-tabs">
                                            <li class="active">
                                                <a href="#edit_tab_1" data-toggle="tab">Terms & Conditions Quotation</a>
                                            </li>
                                            <li>
                                                <a href="#edit_tab_2" data-toggle="tab">Terms & Conditions Invoice</a>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                            <!-- Tab 1 -->
                                            <div class="tab-pane active" id="edit_tab_1">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Quotation Terms</label>
                                                            <textarea id="editor1_edit_modal" name="quote_terms"
                                                                class="form-control custom-textarea"
                                                                placeholder="Enter quotation terms" required></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tab 2 -->
                                            <div class="tab-pane" id="edit_tab_2">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Invoice Terms</label>
                                                            <textarea id="editor2_edit_modal" name="invoice_terms"
                                                                class="form-control custom-textarea"
                                                                placeholder="Enter invoice terms" required></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Select Bank <span class="text-danger">*</span></label>
                                            <?php echo form_dropdown('bank_id', ['' => 'Select Bank'] + $bank_opt, set_value('bank_id'), 'id="bank_id" class="form-control" required'); ?>
                                        </div>
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
                <label>Total Records : <span id="user_limet"><?php echo $total_records; ?></span></label>
            </div>
            <div class="form-group col-sm-6">
                <?php echo $pagination; ?>
            </div>
        </div>

    </div>

</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>