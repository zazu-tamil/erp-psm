<?php include_once(VIEWPATH . 'inc/header.php'); ?>

<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Tender</a></li>
        <li class="active"> <?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-plus-circle"></i> <?php echo htmlspecialchars($title); ?></h3>
        </div>

        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Add" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Tender DN </legend>

                    <div
                        style="border:1px solid #ddd; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:5px;">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="srch_enq_id">Search Enquiry No</label>
                                <input type="text" name="srch_enq_id" class="form-control srch_enq_id" value=""
                                    placeholder="Search Enquiry No" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="srch_company_id">Company <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_company_id', $company_opt, set_value('srch_company_id'), 'id="srch_company_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="srch_customer_id">Customer <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select Customer'] + $customer_opt, set_value('srch_customer_id'), 'id="srch_customer_id" class="form-control" required '); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="srch_tender_enquiry_id">Tender Enquiry No <span
                                    style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select Enquiry'], set_value('srch_tender_enquiry_id'), 'id="srch_tender_enquiry_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="tender_po_id">Tender PO No <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('tender_po_id', ['' => 'Select PO'], set_value('tender_po_id'), 'id="tender_po_id" class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>DN Date</label>
                            <input type="date" name="dc_date" id="dc_date" class="form-control"
                                value="<?php echo set_value('dc_date', date('Y-m-d')); ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active" checked>
                                Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Inactive">
                                Inactive</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="remarks">Note:</label>
                            <textarea name="remarks" class="form-control" id="editor2" placeholder="Enter your remarks"
                                rows="8"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>DN Terms & Conditions</label>
                                <textarea id="editor1" name="terms" class="form-control custom-textarea"
                                    placeholder="Enter quotation terms" required></textarea>
                            </div>
                        </div>
                    </div>

                </fieldset>

                <fieldset class="mt-4">
                    <legend class="text-light-blue">
                        <i class="fa fa-list"></i> Item Details
                        <button type="button" class="btn btn-warning btn-sm pull-right" id="btn_open_stock_modal" style="font-weight: 600; margin-top: -5px;">
                            <i class="fa fa-cubes"></i> Add From Warehouse Stock
                        </button>
                    </legend>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">✔</th>
                                    <th style="width: 10%;">Vendor / Inward No</th>
                                    <th style="width: 60%;">Item Code / Item Description</th>
                                    <th style="width: 10%;">UOM / Available Qty</th>
                                    <th style="width: 10%;">DN Qty</th>
                                </tr>
                            </thead>

                            <tbody id="item_container">
                                <tr>
                                    <td colspan="5" class="text-center">No items found. Please select Tender Enquiry
                                        No.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </fieldset>

            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('tender-dc-list'); ?>" class="btn btn-default"><i
                        class="fa fa-arrow-left"></i> Back To List</a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</section>

    <!-- ==================== MODAL: ADD FROM IN-STOCK ==================== -->
    <div class="modal fade" id="modal_instock_picker" role="dialog" aria-labelledby="stockModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 92%; max-width: 1200px;" role="document">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="stockModalLabel">
                        <i class="fa fa-cubes"></i> <strong>Select In-Stock Item to Deliver (DC) to Customer</strong>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info" style="font-size: 12px; margin-bottom: 12px; padding: 8px 12px;">
                        <i class="fa fa-info-circle"></i> <strong>Tip:</strong> You can select items available in warehouse stock (surplus from prior inwards or direct stock). Map each item to the relevant Customer PO Line item so that Customer invoicing will reflect the correct agreed rate.
                    </div>
                    <div class="row" style="margin-bottom: 12px;">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="srch_stock_kw" class="form-control" placeholder="Search by item code, description, inward no, vendor, original tender...">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" type="button" id="btn_search_stock"><i class="fa fa-search"></i> Search</button>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 text-right" style="padding-top: 6px;">
                            <span class="text-muted" id="stock_count_info">Loading available stock...</span>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                        <table class="table table-bordered table-striped table-hover" id="table_instock_modal">
                            <thead class="bg-gray-active">
                                <tr>
                                    <th style="width: 40px;" class="text-center">Select</th>
                                    <th style="width: 18%;">Source / Inward Info</th>
                                    <th style="width: 25%;">Item Code &amp; Description</th>
                                    <th class="text-center" style="width: 8%;">UOM</th>
                                    <th class="text-right" style="width: 10%;">Available Qty</th>
                                    <th style="width: 27%;">Map to Customer PO Line Item <span class="text-danger">*</span></th>
                                    <th style="width: 12%;" class="text-right">DN Qty <span class="text-danger">*</span></th>
                                </tr>
                            </thead>
                            <tbody id="instock_modal_tbody">
                                <tr>
                                    <td colspan="7" class="text-center" style="padding: 20px;">
                                        <i class="fa fa-spinner fa-spin fa-2x"></i><br>Loading in-stock items...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                    <button type="button" class="btn btn-success" id="btn_confirm_add_stock"><i class="fa fa-plus-circle"></i> Add Selected Items to DN</button>
                </div>
            </div>
        </div>
    </div>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>

