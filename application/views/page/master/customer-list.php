<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<section class="content-header">
    <h1>Customer List</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Master</a></li>
        <li class="active">Customer List</li>
    </ol>
</section>

<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#add_modal">
                <span class="fa fa-plus-circle"></span> Add New
            </button>
        </div>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger">
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success">
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped" id="customer_list">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Customer Name</th>
                        <th>Contact Name</th>
                        <th>Address</th>
                        <th>CRNO</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th class="text-center">Map Loqation</th>
                        <th>Status</th>
                        <th colspan="2" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($record_list as $j => $ls) { ?>
                        <tr>
                            <td class="text-center"><?php echo ($j + 1); ?></td>
                            <td><?php echo $ls['customer_name'] ?><br />
                                <i class="badge"><?php echo $ls['gst'] ?></i>
                            </td>
                            <td><?php echo $ls['contact_name']; ?></td>
                            <td><?php echo $ls['address']; ?></td>
                            <td><?php echo $ls['crno']; ?></td>
                            <td>
                                <a href="tel:<?php echo $ls['mobile']; ?>"
                                    rel="noopener noreferrer"><?php echo $ls['mobile']; ?></a>
                                <br><a href="tel:<?php echo $ls['mobile_alt']; ?>"
                                    rel="noopener noreferrer"><?php echo $ls['mobile_alt']; ?></a>
                            </td>
                            <td><a href="<?php echo $ls['email'] ?>"><?php echo $ls['email']; ?></a></td>
                            <td class="text-center">
                                <?php if (!empty($ls['google_map_location'])): ?>
                                    <a href="<?php echo $ls['google_map_location']; ?>" target="_blank"
                                        class="btn btn-xs btn-info" title="View Location">
                                        <i class="fa fa-map-marker"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted"><i class="fa fa-map-marker text-gray"></i></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $ls['status']; ?></td>

                            <td>
                                <button data-toggle="modal" data-target="#edit_modal"
                                    value="<?php echo $ls['customer_id']; ?>" class="edit_record btn btn-primary btn-xs"
                                    title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>
                            <td>
                                <?php if ($this->session->userdata(SESS_HD . 'level') == 'Admin') { ?>
                                    <button value="<?php echo $ls['customer_id']; ?>" class="del_record btn btn-danger btn-xs"
                                        title="Delete">
                                        <i class="fa fa-remove"></i>
                                    </button>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- ADD MODAL -->
            <div class="modal fade" id="add_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title">Add Customer</h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Customer Name</label>
                                        <input class="form-control" type="text" name="customer_name" id="customer_name"
                                            placeholder="Customer Name">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Contact Name</label>
                                        <input class="form-control" type="text" name="contact_name" id="contact_name"
                                            placeholder="Contact Person">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>CR No</label>
                                        <input class="form-control" type="text" name="crno" id="crno"
                                            placeholder="Commercial Registration No">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Country</label>
                                            <?php echo form_dropdown('country', ['' => 'Select Country'] + $country_opt, set_value('country'), 'id="country" class="form-control" required'); ?>
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
                                    <div class="form-group col-md-4">
                                        <label>Mobile</label>
                                        <input class="form-control" type="text" name="mobile" id="mobile"
                                            placeholder="Mobile">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Alternate Mobile</label>
                                        <input class="form-control" type="text" name="mobile_alt" id="mobile_alt"
                                            placeholder="Alternate Mobile">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Email</label>
                                        <input class="form-control" type="email" name="email" id="email"
                                            placeholder="Email">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>VAT No</label>
                                        <input class="form-control" type="text" name="gst" id="gst"
                                            placeholder="VAT No">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Remarks</label>
                                        <textarea name="remarks" id="remarks" class="form-control" rows="1"></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Latitude</label>
                                        <input class="form-control" type="text" name="latitude" id="latitude"
                                            placeholder="Latitude">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Longitude</label>
                                        <input class="form-control" type="text" name="longitude" id="longitude"
                                            placeholder="Longitude">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Google Map Location</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="google_map_location"
                                                    id="google_map_location" placeholder="Google Map Location URL">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-primary" id="get-location-btn">
                                                        <i class="fa fa-map-marker"></i> Capture Location
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row mt-3">
                                    <div class="form-group col-md-6">
                                        <label>Status</label><br>
                                        <label><input type="radio" name="status" value="Active" checked> Active</label>
                                        <label class="ml-3"><input type="radio" name="status" value="InActive">
                                            InActive</label>
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

            <!-- EDIT MODAL -->
            <div class="modal fade" id="edit_modal" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="post" action="" id="frmedit" enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                <h3 class="modal-title">Edit Customer</h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="customer_id" id="customer_id" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Customer Name</label>
                                        <input class="form-control" type="text" name="customer_name" id="customer_name"
                                            placeholder="Customer Name">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Contact Name</label>
                                        <input class="form-control" type="text" name="contact_name" id="contact_name"
                                            placeholder="Contact Person">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>CR No</label>
                                        <input class="form-control" type="text" name="crno" id="crno"
                                            placeholder="Commercial Registration No">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Country</label>
                                            <?php echo form_dropdown('country', ['' => 'Select Country'] + $country_opt, set_value('country'), 'id="country" class="form-control" required'); ?>
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
                                    <div class="form-group col-md-4">
                                        <label>Mobile</label>
                                        <input class="form-control" type="text" name="mobile" id="mobile"
                                            placeholder="Mobile">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Alternate Mobile</label>
                                        <input class="form-control" type="text" name="mobile_alt" id="mobile_alt"
                                            placeholder="Alternate Mobile">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Email</label>
                                        <input class="form-control" type="email" name="email" id="email"
                                            placeholder="Email">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>VAT No</label>
                                        <input class="form-control" type="text" name="gst" id="gst"
                                            placeholder="VAT No">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Remarks</label>
                                        <textarea name="remarks" id="remarks" class="form-control" rows="1"></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Latitude</label>
                                        <input class="form-control" type="text" name="latitude" id="latitude"
                                            placeholder="Latitude">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Longitude</label>
                                        <input class="form-control" type="text" name="longitude" id="longitude"
                                            placeholder="Longitude">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Google Map Location</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="google_map_location"
                                                    id="google_map_location" placeholder="Google Map Location URL">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-primary" id="get-location-btn">
                                                        <i class="fa fa-map-marker"></i> Capture Location
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row mt-3">
                                    <div class="form-group col-md-6">
                                        <label>Status</label><br>
                                        <label><input type="radio" name="status" value="Active" checked> Active</label>
                                        <label class="ml-3"><input type="radio" name="status" value="InActive">
                                            InActive</label>
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