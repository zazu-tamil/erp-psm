<?php include_once(VIEWPATH . '/inc/header.php');

// echo '<pre>';
// print_r($_POST);
// echo '</pre>'; 

?>

<section class="content-header">
    <h1><?php echo $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Master</a></li>
        <li class="active"><?php echo $title; ?></li>
    </ol>
</section>
<style>
    .pointer {
        cursor: pointer !important;
    }
</style>
<!-- Main content -->
<section class="content">

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <!-- Search Filter Box -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('customer-invoice-receipt'); ?>" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>Customer</label>
                        <?php echo form_dropdown('srch_customer_id', ['' => 'All'] + $customer_opt, $srch_customer_id, 'id="srch_customer_id" class="form-control select2"'); ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="srch_enquiry_no">Our Enquiry No</label>
                        <input type="text" name="srch_enquiry_no" id="srch_enquiry_no" class="form-control"
                            value="<?php echo set_value('srch_enquiry_no', $srch_enquiry_no); ?>"
                            placeholder="Search the Our Enquiry No">
                        <input type="hidden" name="tender_enquiry_id_value_id" class="tender_enquiry_id_value_id"
                            value="<?php echo set_value('tender_enquiry_id_value_id', $tender_enquiry_id_value_id); ?>">

                    </div>
                    <div class="form-group col-md-3 text-left">
                        <br>
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show</button>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main List Box -->
    <div class="box box-success">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#add_modal">
                <span class="fa fa-plus-circle"></span> Add New
            </button>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered" id="company_list">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th>Receipt No</th>
                        <th>Date</th>
                        <th>Receipt Mode</th>
                        <th class="text-right">Amount</th>
                        <th colspan="2" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($record_list as $j => $ls): ?>
                        <tr>
                            <td class="text-center"><?php echo ($j + 1); ?></td>
                            <td><?php echo $ls['receipt_no'] ?? ''; ?></td>
                            <td><?php echo $ls['receipt_date'] ?? ''; ?></td>
                            <td>
                                <?php if ($ls['receipt_mode'] == 'Bank'): ?>
                                    <span class="label label-success"><?php echo $ls['receipt_mode']; ?></span>
                                    <br>(<?php echo $ls['bank_name'] ?? ''; ?>)
                                <?php elseif ($ls['receipt_mode'] == 'Cash'): ?>
                                    <span class="label label-success"><?php echo $ls['receipt_mode']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right"><?php echo number_format((float) ($ls['amount'] ?? 0), 3); ?></td>
                            <td class="text-center">
                                <button type="button" data-toggle="modal" data-target="#edit_modal"
                                    data-id="<?php echo $ls['tender_receipt_id'] ?? ''; ?>"
                                    data-customer-id="<?php echo $ls['customer_id'] ?? ''; ?>"
                                    class="edit_record btn btn-primary btn-xs" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>
                            <td class="text-center">
                                <button type="button" value="<?php echo $ls['tender_receipt_id'] ?? ''; ?>"
                                    class="del_record btn btn-danger btn-xs" title="Delete">
                                    <i class="fa fa-remove"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($record_list)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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


    <!-- ===================== ADD MODAL ===================== -->
    <div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="post" action="<?php echo site_url('customer-invoice-receipt'); ?>" id="frmadd"
                    enctype="multipart/form-data">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title" id="addModalLabel"><strong>Add Customer Receipt</strong></h3>
                        <input type="hidden" name="mode" value="Add" />
                    </div>
                    <div class="modal-body">

                        <!-- Search Enquiry -->
                        <div
                            style="border:1px solid #ddd; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:5px;">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Search Enquiry No</label>
                                    <input type="text" name="srch_enq_id" class="form-control add_srch_enq_id" value=""
                                        placeholder="Search Enquiry No" autocomplete="off" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Customer <span class="text-red">*</span></label>
                                <?php echo form_dropdown('customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('customer_id'), 'id="add_customer_id" class="form-control" required="true"'); ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Tender Enquiry No</label>
                                <?php echo form_dropdown('tender_enquiry_id', ['' => 'Select Enquiry'], set_value('tender_enquiry_id'), 'id="add_tender_enquiry_id" class="form-control"'); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Receipt Date <span class="text-red">*</span></label>
                                <input type="date" name="receipt_date" id="add_receipt_date" class="form-control"
                                    required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Receipt Mode <span class="text-red">*</span></label>
                                <?php echo form_dropdown('receipt_mode', ['' => 'Select Receipt Mode', 'Cash' => 'Cash', 'Bank' => 'Bank'], set_value('receipt_mode'), 'id="add_receipt_mode" class="form-control" required="true"'); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6" id="add_bank_div" style="display:none;">
                                <label>Select Bank <span class="text-red">*</span></label>
                                <?php echo form_dropdown('bank_id', ['' => 'Select Bank'] + $bank_opt, set_value('bank_id'), 'id="add_bank_id" class="form-control"'); ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Amount <span class="text-red">*</span></label>
                                <input type="text" name="amount" id="add_grand_total_amount"
                                    class="form-control text-right" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea name="remarks" id="add_remarks" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Status</label><br>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="Active" checked> Active
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="InActive"> InActive
                                </label>
                            </div>
                        </div>

                        <br>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-info">
                                    <tr>
                                        <th class="text-center" style="width:40px;">
                                            <input type="checkbox" id="add_select_all_items">
                                        </th>
                                        <th>Date</th>
                                        <th>Invoice No</th>
                                        <th class="text-right">Invoice Amount</th>
                                        <th>Pay Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="add_item_container"></tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fa fa-times"></i> Cancel
                        </button>
                        <input type="submit" name="Save" value="Save" class="btn btn-primary" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="post" action="<?php echo site_url('customer-invoice-receipt'); ?>" id="frmedit"
                    enctype="multipart/form-data">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title" id="editModalLabel"><strong>Edit Customer Receipt</strong></h3>
                        <input type="hidden" name="mode" value="Edit" />
                        <input type="hidden" name="tender_receipt_id" id="edit_tender_receipt_id" value="" />
                    </div>
                    <div class="modal-body">

                        <!-- Search Enquiry -->
                        <div
                            style="border:1px solid #ddd; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:5px;">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Search Enquiry No</label>
                                    <input type="text" name="srch_enq_id" class="form-control edit_srch_enq_id" value=""
                                        placeholder="Search Enquiry No" autocomplete="off" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Customer <span class="text-red">*</span></label>
                                <?php echo form_dropdown('customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('customer_id'), 'id="edit_customer_id" class="form-control" required="true"'); ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Tender Enquiry No</label>
                                <?php echo form_dropdown('tender_enquiry_id', ['' => 'Select Enquiry'], set_value('tender_enquiry_id'), 'id="edit_tender_enquiry_id" class="form-control"'); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Receipt Date <span class="text-red">*</span></label>
                                <input type="date" name="receipt_date" id="edit_receipt_date" class="form-control"
                                    required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Receipt Mode <span class="text-red">*</span></label>
                                <?php echo form_dropdown('receipt_mode', ['' => 'Select Receipt Mode', 'Cash' => 'Cash', 'Bank' => 'Bank'], set_value('receipt_mode'), 'id="edit_receipt_mode" class="form-control" required="true"'); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6" id="edit_bank_div" style="display:none;">
                                <label>Select Bank <span class="text-red">*</span></label>
                                <?php echo form_dropdown('bank_id', ['' => 'Select Bank'] + $bank_opt, set_value('bank_id'), 'id="edit_bank_id" class="form-control"'); ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Amount <span class="text-red">*</span></label>
                                <input type="text" name="amount" id="edit_grand_total_amount"
                                    class="form-control text-right" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea name="remarks" id="edit_remarks" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Status</label><br>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="Active" checked> Active
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="InActive"> InActive
                                </label>
                            </div>
                        </div>

                        <br>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-info">
                                    <tr>
                                        <th class="text-center" style="width:40px;">
                                            <input type="checkbox" id="edit_select_all_items">
                                        </th>
                                        <th>Date</th>
                                        <th>Invoice No</th>
                                        <th class="text-right">Invoice Amount</th>
                                        <th>Pay Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="edit_item_container"></tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fa fa-times"></i> Cancel
                        </button>
                        <input type="submit" name="Update" value="Update" class="btn btn-primary" />
                    </div>
                </form>
            </div>
        </div>
    </div>

</section>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>