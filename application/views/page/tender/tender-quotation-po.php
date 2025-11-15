<?php include_once(VIEWPATH . 'inc/header.php'); ?>

<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Tender</a></li>
        <li class="active">Add <?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-plus-circle"></i> Add Tender Quotation</h3>
        </div>

        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Add" />
                <fieldset class="tender-inward">
                    <legend class="text-light-blue"><i class="fa fa-file-text-o"></i> Tender Quotation</legend>

                    <div class="row">

                        <div class="form-group col-md-3">
                            <label for="srch_company_id">Company</label>
                            <?php echo form_dropdown('srch_company_id', ['' => 'Select'] + $company_opt, set_value('srch_company_id'), 'id="srch_company_id" class="form-control "'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="srch_customer_id">Customer</label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select'] + $customer_opt, set_value('srch_customer_id'), 'id="srch_customer_id" class="form-control "'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="srch_tender_enquiry_id">Tender Enquiry No</label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select'] + $tender_enquiry_opt, set_value('srch_tender_enquiry_id'), 'id="srch_tender_enquiry_id" class="form-control"'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Quotation No</label>
                            <input type="text" name="quotation_no" id="quotation_no" class="form-control"
                                placeholder="e.g., TEN-2025-001" value="<?php echo set_value('quotation_no'); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Tender Ref No</label>
                            <input type="text" name="tender_ref_no" id="tender_ref_no" class="form-control"
                                placeholder="e.g., TEN-2025-001" value="<?php echo set_value('tender_ref_no'); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Quotation Date</label>
                            <input type="date" name="quote_date" id="quote_date" class="form-control"
                                value="<?php echo set_value('quote_date', date('Y-m-d')); ?>">
                        </div>
                        <!-- Quotation Status -->
                        <div class="form-group col-md-3">
                            <label>Quotation Status</label><br>
                            <label class="radio-inline"><input type="radio" name="quotation_status" value="Confirmed">
                                Confirmed</label>
                            <label class="radio-inline"><input type="radio" name="quotation_status" value="Pending"
                                    checked> Pending</label>
                        </div>

                        <!-- Active or Inactive -->
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
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" class="form-control" id="remarks" placeholder="Enter your remarks "
                                rows="8"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Quotation Terms</label>
                                <textarea id="editor1" name="terms" class="form-control custom-textarea"
                                    placeholder="Enter quotation terms" required></textarea>
                            </div>
                        </div>
                    </div>

                </fieldset>

                <fieldset class="mt-4">
                    <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>
                    <div id="item_container"></div>
                </fieldset>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('vendor-rate-enquiry-list'); ?>" class="btn btn-default"><i
                        class="fa fa-arrow-left"></i> Back To List</a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>

<script>
    $(document).ready(function () {
        $("#srch_tender_enquiry_id").on("change", function () {
            const tender_enquiry_id = $(this).val();
            const $container = $("#item_container");
            $container.html("");

            if (!tender_enquiry_id) return;

            $.ajax({
                url: "<?php echo site_url('vendor/get_data'); ?>",
                type: "POST",
                data: {
                    tbl: "get-tender-enquiry-item-list-rate",
                    id: tender_enquiry_id,
                },
                dataType: "json",
                success: function (res) {
                    if (res.length > 0) {
                        $.each(res, function (i, row) {
                            const hiddenFields = `
              <input type="hidden" name="tender_enquiry_item_id[]" value="${row.tender_enquiry_item_id}">
              <input type="hidden" name="category_id[]" value="${row.category_id}">
              <input type="hidden" name="item_id[]" value="${row.item_id}">
            `;

                            const itemHtml = `
              <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
                <h5 class="text-primary mb-3">Item Details ${i + 1}</h5>
                <div class="row">
                  <div class="col-md-1 d-flex align-items-center justify-content-center">
                    <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="${i}">
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Category Name</label>
                      <input type="text" class="form-control" value="${row.category_name
                                }" readonly>
                    </div>
                    <div class="form-group">
                      <label>Item Name</label>
                      <input type="text" class="form-control" value="${row.item_name
                                }" readonly>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Item Description</label>
                      <textarea name="item_desc[]" class="form-control desc-textarea" rows="3">${row.item_desc
                                }</textarea>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>UOM</label>
                          <input type="text" name="uom[]" class="form-control" value="${row.uom
                                }" readonly>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Quantity</label>
                          <input type="number" step="0.01" name="qty[]" class="form-control qty-input" value="${row.qty
                                }" readonly>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Rate</label>
                          <input type="number" step="0.01" name="rate[]" class="form-control rate-input" value="${row.rate ?? 0
                                }">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label>VAT %</label>
                          <select name="gst[]" class="form-control vat-dropdown">
                            <option value="">Select</option>
                            <?php foreach ($gst_opt as $gid => $pct): ?>
                              <option value="<?= $pct; ?>"><?= $pct; ?>%</option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>

                      <div class="col-md-12">
                        <div class="form-group">
                          <label>Amount</label>
                          <input type="number" step="0.01" name="amount[]" class="form-control amount-input" value="0.00" readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                ${hiddenFields}
              </div>
            `;
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

            <style>
              .total-wrapper {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                width: 100%;
              }
              .total-box {
                background: linear-gradient(135deg, #f6fff9, #e8f9f0);
                border: 2px solid #b5e0c6;
                border-radius: 12px;
                padding: 12px 24px;
                font-family: "Segoe UI", sans-serif;
                font-size: 1.15rem;
                color: #333;
                min-width: 280px;
                text-align: right;
                transition: all 0.3s ease-in-out;
              }
              .total-box:hover {
                background: #e3f7ec;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
                transform: translateY(-2px);
              }
              .total-box i {
                font-size: 1.3rem;
                vertical-align: middle;
              }
              #total_amount {
                font-size: 1.4rem;
                font-weight: 700;
                margin-left: 5px;
              }
              @media (max-width: 768px) {
                .total-wrapper { justify-content: center; }
                .total-box { text-align: center; min-width: 220px; }
              }
            </style>
          `);
                    } else {
                        $container.html(
                            '<div class="alert alert-warning">No items found for this tender enquiry.</div>'
                        );
                    }
                },
            });
        });


        $(document).on("input change", ".rate-input, .vat-dropdown", function () {
            const $row = $(this).closest(".item-card");
            const qty = parseFloat($row.find(".qty-input").val()) || 0;
            const rate = parseFloat($row.find(".rate-input").val()) || 0;
            const gst = parseFloat($row.find(".vat-dropdown").val()) || 0;

            const amount = qty * rate * (1 + gst / 100);
            $row.find(".amount-input").val(amount.toFixed(2));

            calculateTotalAmount();
        });

        function calculateTotalAmount() {
            let total = 0;
            $(".amount-input").each(function () {
                total += parseFloat($(this).val()) || 0;
            });
            $("#total_amount").text(total.toFixed(2));
        }


        $(document).on("change", ".item-check", function () {
            const $card = $(this).closest(".item-card");
            if (!$(this).is(":checked")) {
                $card
                    .find(
                        "input[type=hidden][name^='tender_enquiry_item_id'], " +
                        "input[type=hidden][name^='category_id'], " +
                        "input[type=hidden][name^='item_id']"
                    )
                    .remove();
            }
        });
    });

    $(function () {

        CKEDITOR.replace("editor1", {
            height: 80,
            extraPlugins: "justify,colorbutton,font,table",
            removeButtons: "",
            toolbar: [
                {
                    name: "clipboard",
                    items: [
                        "Cut",
                        "Copy",
                        "Paste",
                        "PasteText",
                        "PasteFromWord",
                        "-",
                        "Undo",
                        "Redo",
                    ],
                },
                {
                    name: "basicstyles",
                    items: [
                        "Bold",
                        "Italic",
                        "Underline",
                        "Strike",
                        "RemoveFormat",
                        "CopyFormatting",
                    ],
                },
                {
                    name: "paragraph",
                    items: [
                        "NumberedList",
                        "BulletedList",
                        "-",
                        "Outdent",
                        "Indent",
                        "-",
                        "Blockquote",
                        "JustifyLeft",
                        "JustifyCenter",
                        "JustifyRight",
                        "JustifyBlock",
                    ],
                },
                { name: "links", items: ["Link", "Unlink", "Anchor"] },
                {
                    name: "insert",
                    items: ["Image", "Table", "HorizontalRule", "SpecialChar"],
                },
                { name: "styles", items: ["Format", "Font", "FontSize"] },
                { name: "colors", items: ["TextColor", "BGColor"] },
                { name: "tools", items: ["Maximize", "ShowBlocks"] },
            ],
        });
        CKEDITOR.replace("editor1_edit_modal", {
            height: 100,
            extraPlugins: "justify,colorbutton,font,table",
            removeButtons: "",
            toolbar: [
                {
                    name: "clipboard",
                    items: [
                        "Cut",
                        "Copy",
                        "Paste",
                        "PasteText",
                        "PasteFromWord",
                        "-",
                        "Undo",
                        "Redo",
                    ],
                },
                {
                    name: "basicstyles",
                    items: [
                        "Bold",
                        "Italic",
                        "Underline",
                        "Strike",
                        "RemoveFormat",
                        "CopyFormatting",
                    ],
                },
                {
                    name: "paragraph",
                    items: [
                        "NumberedList",
                        "BulletedList",
                        "-",
                        "Outdent",
                        "Indent",
                        "-",
                        "Blockquote",
                        "JustifyLeft",
                        "JustifyCenter",
                        "JustifyRight",
                        "JustifyBlock",
                    ],
                },
                { name: "links", items: ["Link", "Unlink", "Anchor"] },
                {
                    name: "insert",
                    items: ["Image", "Table", "HorizontalRule", "SpecialChar"],
                },
                { name: "styles", items: ["Format", "Font", "FontSize"] },
                { name: "colors", items: ["TextColor", "BGColor"] },
                { name: "tools", items: ["Maximize", "ShowBlocks"] },
            ],
        });

    });
</script>