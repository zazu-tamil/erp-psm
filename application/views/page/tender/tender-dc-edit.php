<?php include_once(VIEWPATH . 'inc/header.php');


echo '<pre>';
print_r($header);
echo '</pre>';

?>



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
                <input type="hidden" name="mode" value="Edit" />

                <fieldset class="tender-inward">
                    <legend class="text-light-blue">
                        <i class="fa fa-file-text-o"></i> Tender DC
                    </legend>

                    <div class="row">

                        <!-- Company -->
                        <div class="form-group col-md-4">
                            <label for="srch_company_id">Company <span style="color:red;">*</span></label>
                            <?php
                            echo form_dropdown(
                                'srch_company_id',
                                ['' => 'Select Company'] + $company_opt,
                                set_value('srch_company_id', $header['company_id']),
                                'id="srch_company_id" class="form-control" required'
                            );
                            ?>
                        </div>

                        <!-- Customer -->
                        <div class="form-group col-md-4">
                            <label for="srch_customer_id">Customer <span style="color:red;">*</span></label>
                            <?php
                            echo form_dropdown(
                                'srch_customer_id',
                                ['' => 'Select Customer'] + $customer_opt,
                                set_value('srch_customer_id', $header['customer_id']),
                                'id="srch_customer_id" class="form-control" required'
                            );
                            ?>
                        </div>

                        <!-- Tender Enquiry -->
                        <div class="form-group col-md-4">
                            <label for="srch_tender_enquiry_id">Tender Enquiry No <span
                                    style="color:red;">*</span></label>
                            <?php
                            echo form_dropdown(
                                'srch_tender_enquiry_id',
                                ['' => 'Select Enquiry'] + $tender_enquiry_opt,
                                set_value('srch_tender_enquiry_id', $header['tender_enquiry_id']),
                                'id="srch_tender_enquiry_id" class="form-control" required'
                            );
                            ?>
                        </div>

                        <!-- DC Date -->
                        <div class="form-group col-md-4">
                            <label>DC Date</label>
                            <input type="date" name="dc_date" id="dc_date" class="form-control"
                                value="<?php echo set_value('dc_date', $header['dc_date'] ?: date('Y-m-d')); ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="dc_no">Tender DC No <span style="color:red;">*</span></label>
                            <input type="text" name="dc_no" id="dc_no" class="form-control"
                                value="<?php echo set_value('dc_no', $header['dc_no']); ?>">

                        </div>

                        <!-- Status -->
                        <div class="form-group col-md-3">
                            <label>Status</label><br>

                            <label class="radio-inline">
                                <input type="radio" name="status" value="Active" <?php echo set_value('status', $header['status']) == 'Inactive' ? '' : 'checked'; ?>>
                                Active
                            </label>

                            <label class="radio-inline">
                                <input type="radio" name="status" value="Inactive" <?php echo set_value('status', $header['status']) == 'Inactive' ? 'checked' : ''; ?>>
                                Inactive
                            </label>

                        </div>

                    </div>

                    <div class="row">

                        <!-- Remarks -->
                        <div class="form-group col-md-6">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" class="form-control" id="editor2" placeholder="Enter your remarks"
                                rows="8"><?php
                                echo nl2br($header['remarks']);
                                ?></textarea>
                        </div>

                        <!-- Terms -->
                        <div class="form-group col-md-6">
                            <label>Quotation Terms</label>
                            <textarea id="editor1" name="terms" class="form-control custom-textarea" required
                                placeholder="Enter quotation terms"><?php
                                echo nl2br($header['terms']);
                                ?></textarea>
                        </div>

                    </div>

                </fieldset>


                <fieldset class="mt-4">
                    <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">Check</th>
                                    <th style="width: 10%;">Vendor / Inward No</th>
                                    <th style="width: 60%;">Item Code / Item Description</th>
                                    <th style="width: 10%;">UOM / Available Qty</th>
                                    <th style="width: 10%;">Dc Qty</th>
                                </tr>
                            </thead>
                            <tbody id="item_container">
                                <?php foreach ($items as $i => $row) { ?>
                                    
                                <?php } ?>
                                <tr>

                                    <td>
                                        <input type="checkbox" class="form-check-input item-check"
                                            name="selected_items[]" value="${i}" ${checkboxChecked}>
                                        <small></small>
                                    </td>

                                    <td>
                                        ${row.vendor_name}<br>
                                        ${formattedDate}<br>
                                        ${row.inward_no}
                                    </td>

                                    <td>
                                        <input type="text" value="${row.item_code}" class="form-control mb-2" readonly>
                                        <textarea name="item_desc[]" class="form-control desc-textarea"
                                            rows="2">${row.item_desc}</textarea>
                                    </td>

                                    <td>
                                        <input type="text" name="uom[]" class="form-control mb-2" value="${row.uom}"
                                            readonly>
                                        <input type="number" name="avail_qty[]" step="0.01" class="form-control"
                                            value="${row.avail_qty}" readonly>
                                    </td>

                                    <td>
                                        <label class="form-label">Enter Delivery Qty</label>
                                        <input type="number" name="dc_qty[]" step="0.01"
                                            class="form-control dc_qty-input" value="${dcQtyValue}">
                                    </td>

                                    ${hiddenFields}

                                </tr>
                            </tbody>
                        </table>
                    </div>
                </fieldset>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('tender-dc-list'); ?>" class="btn btn-default"><i
                        class="fa fa-arrow-left"></i> Back To List</a>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>