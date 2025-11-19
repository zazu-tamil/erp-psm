<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Tender</a></li>
        <li class="active">Edit <?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-edit"></i> Edit Vendor PO</h3>
            <a href="<?php echo site_url('vendor-po-list'); ?>" class="btn btn-warning pull-right"><i
                        class="fa fa-arrow-left"></i> Back To List</a>
        </div>

        <form method="post" action="" id="frmadd">
            <div class="box-body">
                <input type="hidden" name="mode" value="Edit" />
                <input type="hidden" name="vendor_po_id" value="<?php echo $header['vendor_po_id']; ?>" />

                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Purchase Order Details</legend>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label>Company <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_company_id', $company_opt, $header['company_id'], 'id="srch_company_id" class="form-control" required readonly'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Customer <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_customer_id', $customer_opt, $header['customer_id'], 'id="srch_customer_id" class="form-control" required readonly'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Tender Enquiry No <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', $tender_enquiry_opt, $header['tender_enquiry_id'], 'id="srch_tender_enquiry_id" class="form-control"readonly  required'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Vendor Rate Enquiry No <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('vendor_rate_enquiry_id', $vendor_rate_enquiry_opt, $header['vendor_rate_enquiry_id'], 'id="srch_vendor_rate_enquiry_id" class="form-control" readonly required');  ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Vendor Name <span class="text-red">*</span></label>
                            <?php echo form_dropdown('srch_vendor_id', $vendor_opt, $header['vendor_id'], 'id="srch_vendor_id" class="form-control" required'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Contact Person</label>
                            <?php echo form_dropdown('srch_vendor_contact_person_id', $vendor_contact_opt, $header['vendor_contact_person_id'], 'id="srch_vendor_contact_id" class="form-control"'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label>PO No</label>
                            <input type="text" name="po_no" class="form-control"
                                value="<?php echo htmlspecialchars($header['po_no']); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>PO Date</label>
                            <input type="date" name="po_date" class="form-control"
                                value="<?php echo $header['po_date']; ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Opening Date</label>
                            <input type="date" name="opening_date" class="form-control"
                                value="<?php echo $header['opening_date']; ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Closing Date</label>
                            <input type="date" name="closing_date" class="form-control"
                                value="<?php echo $header['closing_date']; ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>PO Status <span style="color:red;">*</span></label><br>
                            <?php echo form_dropdown('po_status', ['' => 'Select Po Status'] + $tender_status_opt, $header['po_status'], 'id="po_status" class="form-control" required'); ?> 
                        </div>

                        <div class="form-group col-md-1">
                            <label>Handling Charges</label><br>
                            <input type="number" step="0.01" name="handling_charges" id="handling_charges" class="form-control" value="<?php echo $header['handling_charges']; ?>" placeholder="100.00">
                            
                        </div>
                      
                        <div class="form-group col-md-2">
                            <label>Status</label><br>
                            <label class="radio-inline"><input type="radio" name="status" value="Active" <?php echo ($header['status'] == 'Active') ? 'checked' : ''; ?>> Active</label>
                            <label class="radio-inline"><input type="radio" name="status" value="Inactive" <?php echo ($header['status'] == 'Inactive') ? 'checked' : ''; ?>> Inactive</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control"
                                rows="5"><?php echo htmlspecialchars($header['remarks']); ?></textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label>PO Terms & Conditions</label>
                            <textarea id="editor1" name="terms"
                                class="form-control"><?php echo $header['terms']; ?></textarea>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="mt-4">
                    <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>
                    <div id="item_container">
                        <!-- Items will be loaded here via JS on page load -->
                    </div>
                </fieldset>
            </div>

            <div class="box-footer text-right">
              <a href="<?php echo site_url('vendor-po-list'); ?>" class="btn btn-warning pull-left"><i
                        class="fa fa-arrow-left"></i> Back To List</a>
                <button type="submit" class="btn btn-success">Update</button>
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>

<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
<script>
    $(document).ready(function () {
        
         const existingItems = <?php echo json_encode($items); ?>;

        // Populate dependent dropdowns on load
        function populateOnLoad() {
            $("#srch_company_id").trigger('change');
            setTimeout(() => {
                $("#srch_customer_id").val(header.customer_id).trigger('change');
                setTimeout(() => {
                    $("#srch_tender_enquiry_id").val(header.tender_enquiry_id).trigger('change');
                    setTimeout(() => {
                        $("#srch_vendor_rate_enquiry_id").val(header.vendor_rate_enquiry_id).trigger('change');
                        $("#srch_vendor_id").val(header.vendor_id).trigger('change');
                    }, 500);
                }, 500);
            }, 500);
        }

        // Load items from existing PO
        function loadExistingItems() {
            const $container = $("#item_container");
            $container.empty();

            if (existingItems.length === 0) {
                $container.html('<div class="alert alert-info">No items found.</div>');
                return;
            }

            existingItems.forEach(function (row, i) {
                const itemHtml = `
                <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
                    <h5 class="text-primary mb-3">Item Details ${i + 1}</h5>
                    <div class="row">
                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                            <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="${i}" checked>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group"><label>Category Name</label><input type="text" class="form-control" value="${row.category_name || ''}" readonly></div>
                            <div class="form-group"><label>Item Name</label><input type="text" class="form-control" value="${row.item_name || ''}" readonly></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label>Item Description</label>
                            <textarea name="item_desc[]" class="form-control desc-textarea" rows="3">${row.item_desc || ''}</textarea></div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-6"><label>UOM</label><input type="text" name="uom[]" class="form-control" value="${row.uom || ''}" readonly></div>
                                <div class="col-md-6"><label>Quantity</label><input type="number" step="0.01" name="qty[]" class="form-control qty-input" value="${row.qty}" readonly></div>
                                <div class="col-md-6"><label>Rate</label><input type="number" step="0.01" name="rate[]" class="form-control rate-input" value="${row.rate}"></div>
                                <div class="col-md-6"><label>VAT %</label>
                                    <select name="gst[]" class="form-control vat-dropdown">
                                        <option value="">Select</option>
                                        <?php foreach ($gst_opt as $pct): ?>
                                            <option value="<?= $pct; ?>" ${row.gst == <?= $pct; ?> ? 'selected' : ''}><?= $pct; ?>%</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-12"><label>Amount</label><input type="number" step="0.01" name="amount[]" class="form-control amount-input" value="${row.amount}" readonly></div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="vendor_rate_enquiry_item_id[]" value="${row.vendor_rate_enquiry_item_id}">
                    <input type="hidden" name="category_id[]" value="${row.category_id}">
                    <input type="hidden" name="item_id[]" value="${row.item_id}">
                </div>`;
                $container.append(itemHtml);
            });

            $container.append(`
            <div class="total-wrapper mt-4 mb-4">
                <div class="total-box shadow-sm">
                    <h5 class="mb-0">
                        <i class="fa fa-calculator text-success me-2"></i>
                        <strong>Total Amount:</strong>
                        <span class="text-primary">₹ <span id="total_amount">0.00</span></span>
                    </h5>
                </div>
            </div>
        `);

            calculateTotalAmount();
        }

        function calculateRow($row) {
            const qty = parseFloat($row.find(".qty-input").val()) || 0;
            const rate = parseFloat($row.find(".rate-input").val()) || 0;
            const gst = parseFloat($row.find(".vat-dropdown").val()) || 0;
            const amount = qty * rate * (1 + gst / 100);
            $row.find(".amount-input").val(amount.toFixed(2));
        }

        function calculateTotalAmount() {
            let total = 0;
            $(".amount-input").each(function () {
                total += parseFloat($(this).val()) || 0;
            });
            $("#total_amount").text(total.toFixed(2));
        }

        $(document).on("input change", ".rate-input, .vat-dropdown", function () {
            const $row = $(this).closest(".item-card");
            calculateRow($row);
            calculateTotalAmount();
        });

        // Your existing AJAX chain (company → customer → tender → vendor enquiry → items)
        // ... (keep your existing chain code here, just make sure it doesn't override on edit)

        // On page load: populate dropdowns and load existing items
        populateOnLoad();
        loadExistingItems();

        // Re-calculate on load
        $(".item-card").each(function () { calculateRow($(this)); });
        calculateTotalAmount();

        CKEDITOR.replace('editor1', { height: 100 });
    });
</script>