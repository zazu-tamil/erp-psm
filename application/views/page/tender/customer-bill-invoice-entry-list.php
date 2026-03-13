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
                        <label>Customer</label>
                        <div class="form-group">
                            <?php echo form_dropdown('srch_customer_id', ['' => 'All'] + $customer_opt, $srch_customer_id, 'id="srch_customer_id" class="form-control select2" '); ?>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Vendor</label>
                        <?php echo form_dropdown('srch_vendor_id', ['' => 'All'] + $vendor_opt, $srch_vendor_id, 'id="srch_vendor_id" class="form-control select2" style="width:100%"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <label for="">OR</label>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="srch_declaration_no">Declaration No</label>
                        <input type="text" name="srch_declaration_no" id="srch_declaration_no" class="form-control"
                            value="<?php echo set_value('srch_declaration_no', $srch_declaration_no); ?>"
                            placeholder="Search the Our Invoice No">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="srch_enquiry_no">Our Enquiry No</label>
                        <input type="text" name="srch_enquiry_no" id="srch_enquiry_no"
                            class="form-control srch_enquiry_no"
                            value="<?php echo set_value('srch_enquiry_no', $srch_enquiry_no); ?>"
                            placeholder="Search the Our Enquiry No">
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
                        <th>Vendor Name</th>
                        <th>Declaration No</th>
                        <th>Total Amount W/O Tax</th>
                        <th>Total Amount With Tax</th>
                        <th colspan="2" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($record_list as $j => $ls) {
                        ?>
                        <tr class="mb-3">
                            <td class="text-center"><?php echo ($j + 1); ?></td> 
                            <td><?php echo htmlspecialchars($ls['vendor_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($ls['declaration_no'] ?? ''); ?></td>
                            <td class="text-right">
                                <?php echo number_format((float) ($ls['total_amount_wo_tax'] ?? 0), 3); ?>
                            </td>
                            <td class="text-right">
                                <?php echo number_format((float) ($ls['total_amount_with_tax'] ?? 0), 3); ?>
                            </td>
                            <td class="text-center">
                                <button data-toggle="modal" data-target="#edit_modal"
                                    class="edit_record btn btn-primary btn-xs" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>
                            <td class="text-center">
                                <button value="<?php echo $ls['id'] ?? ''; ?>" class="del_record btn btn-danger btn-xs"
                                    title="Delete">
                                    <i class="fa fa-remove"></i>
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>

            <!-- Add Modal (Parent) -->
            <div class="modal fade" id="add_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo site_url('purchase-invoice/save'); ?>" id="frmadd"
                            enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel"><strong>Add Customer Invoice
                                        Entry</strong></h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>
                            <div class="modal-body">
                                <div
                                    style="border:1px solid #ddd; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:5px;">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="srch_enq_id">Search Enquiry No</label>
                                            <input type="text" name="srch_enq_id" class="form-control srch_enq_id"
                                                value="" placeholder="Search Enquiry No" />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Account Status</label><br>

                                            <label class="radio-inline">
                                                <input type="radio" name="account_status" value="accountable" checked="true">
                                                Accountable
                                            </label>

                                            <label class="radio-inline">
                                                <input type="radio" name="account_status" value="not_accountable"> Not
                                                Accountable
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Customer <span class="text-red">*</span></label>
                                        <?php echo form_dropdown('customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('customer_id'), 'id="srch_customer_id" class="form-control" required'); ?>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="srch_tender_enquiry_id">Tender Enquiry No</label>
                                        <?php echo form_dropdown('tender_enquiry_id', ['' => 'Select Enquiry'], set_value('tender_enquiry_id'), 'id="srch_tender_enquiry_id" class="form-control"'); ?>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="srch_vendor_id">Vendor Name <span class="text-red">*</span></label>
                                        <div class="input-group">
                                            <?php echo form_dropdown('vendor_id', ['' => 'Select'] + $vendor_opt, set_value('vendor_id'), 'id="srch_vendor_id" class="form-control" required'); ?>
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-info" id="btn_open_add_vendor">Add
                                                    New</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Declaration No <span class="text-red">*</span></label>
                                        <input type="text" name="declaration_no" id="declaration_no"
                                            class="form-control" placeholder="Declaration No" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Declaration Date</label>
                                        <input type="date" name="declaration_date" id="declaration_date"
                                            class="form-control" value="<?php echo set_value('declaration_date'); ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>VAT Payer Sales / Purchase Group</label>
                                        <?php echo form_dropdown('vat_payer_purchase_grp', $vat_payer_purchase_opt, set_value('vat_payer_purchase_grp'), 'id="vat_payer_purchase_grp" class="form-control"'); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- Customs Duty -->
                                    <div class="form-group col-md-4">
                                        <label>Customs Duty</label>
                                        <input type="number" name="custom_duty" id="custom_duty" class="form-control">
                                    </div>

                                    <!-- Custom Stamp Fee -->
                                    <div class="form-group col-md-4">
                                        <label>Custom Stamp Fee</label>
                                        <input type="number" name="custom_stamp_fee" id="custom_stamp_fee"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="row">

                                    <!-- Amount Without VAT -->
                                    <div class="form-group col-md-4">
                                        <label>Total Amount w/o VAT</label>
                                        <input type="number" name="amount_without_vat" id="amount_without_vat"
                                            class="form-control">
                                    </div>

                                    <!-- VAT -->
                                    <div class="form-group col-md-4">
                                        <label>VAT</label>
                                        <input type="number" name="vat" id="vat" class="form-control">
                                    </div>

                                    <!-- Total Amount -->
                                    <div class="form-group col-md-4">
                                        <label>Total Amount</label>
                                        <input type="number" name="total_amount" id="total_amount" class="form-control"
                                            readonly>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Status</label><br>
                                        <label class="radio-inline"><input type="radio" name="status" value="Active"
                                                checked> Active</label>
                                        <label class="radio-inline"><input type="radio" name="status" value="InActive">
                                            InActive</label>
                                    </div>
                                </div>

                                <!-- Alert placeholder inside parent modal body -->
                                <div id="zazualert"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <input type="submit" name="Save" value="Save" class="btn btn-primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="edit_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo site_url('purchase-invoice/save'); ?>" id="frmedit"
                            enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel"><strong>Edit Purchase Invoice</strong>
                                </h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="id" id="edit_id" value="" />
                            </div>
                            <div class="modal-body">
                                <div
                                    style="border:1px solid #ddd; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:5px;">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label>A/c Sub Head</label>
                                            <?php echo form_dropdown('account_head_id', $ac_sub_head_opt, set_value('account_head_id'), 'id="edit_account_head_id" class="form-control"'); ?>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Vendor Name <span class="text-red">*</span></label>
                                            <?php echo form_dropdown('vendor_id', ['' => 'Select'] + $vendor_opt, set_value('vendor_id'), 'id="edit_vendor_id" class="form-control" required'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Inward Date</label>
                                        <input type="date" name="inward_date" id="edit_inward_date"
                                            class="form-control">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Invoice No <span class="text-red">*</span></label>
                                        <input type="text" name="invoice_no" id="edit_invoice_no" class="form-control"
                                            required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Entry Date</label>
                                        <input type="date" name="entry_date" id="edit_entry_date" class="form-control">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>VAT Payer Sales / Purchase Group</label>
                                        <?php echo form_dropdown('vat_payer_purchase_grp', $vat_payer_purchase_opt, set_value('vat_payer_purchase_grp'), 'id="edit_vat_payer_purchase_grp" class="form-control"'); ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Tax Percentage</label>
                                        <input type="number" step="0.01" name="tax_percentage" id="edit_tax_percentage"
                                            class="form-control">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Total Amount W/O Tax</label>
                                        <input type="number" step="0.001" name="total_amount_wo_tax"
                                            id="edit_total_amount_wo_tax" class="form-control">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Total Amount With Tax</label>
                                        <input type="number" step="0.001" name="total_amount_with_tax"
                                            id="edit_total_amount_with_tax" class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Status</label><br>
                                        <label class="radio-inline"><input type="radio" name="status" value="Active"
                                                id="edit_status_active"> Active</label>
                                        <label class="radio-inline"><input type="radio" name="status" value="InActive"
                                                id="edit_status_inactive"> InActive</label>
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

            <!-- Add Vendor Modal (Child / Nested) -->
            <div class="modal fade" id="add_vendor" tabindex="-1" role="dialog" aria-labelledby="addVendorLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form method="post" action="" id="frmadd_Vendor" enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" id="btn_close_vendor_modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="addVendorLabel">Add Vendor</h3>
                                <input type="hidden" name="mode" value="Add Vendor" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Vendor Name</label>
                                        <input class="form-control" type="text" name="vendor_name" id="vendor_name"
                                            placeholder="Vendor Name">
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Country <span class="text-danger">*</span></label>
                                            <?php echo form_dropdown('country', ['' => 'Select Country'] + $country_opt, set_value('country'), 'id="country" class="form-control"'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>VAT No</label>
                                        <input class="form-control" type="text" name="gst" id="gst"
                                            placeholder="VAT No">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control" name="address" id="address"
                                                placeholder="Address" required="true" rows="4"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Remarks</label>
                                        <textarea name="remarks" id="remarks" rows="4" class="form-control"></textarea>
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
                                <button type="button" class="btn btn-secondary"
                                    id="btn_cancel_vendor_modal">Cancel</button>
                                <input type="submit" name="btn_add_vendor" value="Save" class="btn btn-primary" />
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
    </div>
</section>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>
<style>
    .ui-autocomplete {
        z-index: 9999999 !important;
    }

    /* Parent modals */
    #add_modal,
    #edit_modal {
        z-index: 1050 !important;
    }

    /* Child modal */
    #add_vendor {
        z-index: 1080 !important;
    }

    /* Parent backdrop */
    .modal-backdrop {
        z-index: 1040 !important;
    }

    /* Child backdrop */
    .modal-backdrop.child-backdrop {
        z-index: 1070 !important;
    }
</style>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
    $(function () {

        var parentModal = null;

        /* OPEN CHILD MODAL */
        $('#btn_open_add_vendor').click(function () {

            parentModal = $('#add_modal');

            $('#add_vendor').modal({
                backdrop: false,
                keyboard: false,
                show: true
            });

            $('<div class="modal-backdrop fade in child-backdrop"></div>').appendTo(document.body);
        });

        function closeVendorModal() {
            $('#add_vendor').modal('hide');
        }

        $('#btn_close_vendor_modal,#btn_cancel_vendor_modal').click(function () {
            closeVendorModal();
        });

        $('#add_vendor').on('hidden.bs.modal', function () {

            $('.modal-backdrop.child-backdrop').remove();

            if (parentModal && parentModal.hasClass('in')) {
                $('body').addClass('modal-open');
            }

            parentModal = null;
        });




        $('#frmadd_Vendor').submit(function (e) {

            e.preventDefault();

            var vendor_name = $('#vendor_name').val().trim();

            if (vendor_name == '') {
                alert('Vendor Name is required');
                $('#vendor_name').focus();
                return false;
            }

            $.ajax({

                url: "<?php echo base_url('vendor/ajax_add_master_inline'); ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",

                success: function (res) {

                    if (res.status == 'success') {

                        var newOption = new Option(res.name, res.id, true, true);

                        $('#srch_vendor_id').append(newOption).trigger('change');

                        $('#frmadd_Vendor')[0].reset();

                        closeVendorModal();

                        $('#zazualert').html(
                            '<div class="alert alert-success alert-dismissible">' +
                            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                            '<strong>Success!</strong> ' + res.message +
                            '</div>'
                        );

                    } else {
                        alert(res.message);
                    }

                },

                error: function () {
                    alert('Server Error');
                }

            });

        });


        $('#srch_enquiry_no').autocomplete({



            source: function (request, response) {

                $.ajax({

                    url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
                    type: "POST",
                    dataType: "json",
                    data: { search: request.term },

                    success: function (data) {
                        response(data);
                    }

                });

            },

            minLength: 1,

            select: function (event, ui) {
                $("#srch_enquiry_no").val(ui.item.value);
            }

        });

        $('#add_modal .srch_enq_id').autocomplete({

            appendTo: "#add_modal",

            source: function (request, response) {

                $.ajax({

                    url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
                    type: "POST",
                    dataType: "json",
                    data: { search: request.term },

                    success: function (data) {
                        response(data);
                    }

                });

            },

            minLength: 1,

            select: function (event, ui) {

                $('#add_modal #srch_customer_id')
                    .val(ui.item.customer_id)
                    .trigger('change');

                load_tender_enq(ui.item.tender_enquiry_id);

            }

        });
        function load_tender_enq(t_enq_id) {

            t_enq_id = t_enq_id || '';

            var customer_id = $('#add_modal #srch_customer_id').val();

            var dd = $('#add_modal #srch_tender_enquiry_id');

            dd.html('<option value="">Select Enquiry</option>');
            dd.prop('disabled', true);

            if (!customer_id) return;

            $.ajax({

                url: "<?php echo site_url('vendor/get_vendor_rate_enquiries_by_customer'); ?>",
                type: "POST",
                dataType: "json",
                data: { customer_id: customer_id },

                success: function (res) {

                    if (res.length > 0) {

                        dd.prop('disabled', false);

                        $.each(res, function (i, row) {

                            dd.append(
                                $('<option>', {
                                    value: row.tender_enquiry_id,
                                    text: row.display
                                })
                            );

                        });

                        if (t_enq_id != '') {
                            dd.val(t_enq_id).trigger('change');
                        }

                    }
                    else {
                        dd.html('<option value="">No enquiries found</option>');
                    }

                },

                error: function () {
                    alert('Error loading enquiries');
                }

            });

        } 
    });
    $(document).ready(function () {

    function calculateTotal() {

        var duty = parseFloat($("#custom_duty").val()) || 0;
        var stamp = parseFloat($("#custom_stamp_fee").val()) || 0;
        var amount = parseFloat($("#amount_without_vat").val()) || 0;
        var vat = parseFloat($("#vat").val()) || 0;

        // subtotal
        var subtotal = duty + stamp + amount;

        // VAT calculation
        var vat_amount = (subtotal * vat) / 100;

        // final total
        var total = subtotal + vat_amount;

        $("#total_amount").val(total.toFixed(2));
    }

    // Trigger calculation on input
    $("#custom_duty, #custom_stamp_fee, #amount_without_vat, #vat").on("keyup change", function () {
        calculateTotal();
    });

});
</script>
