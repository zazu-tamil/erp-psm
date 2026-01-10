<?php include_once(VIEWPATH . 'inc/header.php'); ?>

<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i>Vendor</a></li>
        <li class="active"> <?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-edit"></i> Edit Vendor Purchase Bill Entry
            </h3>

            <a href="<?php echo site_url('vendor-purchase-bill-list'); ?>" class="btn btn-warning pull-right">
                <i class="fa fa-arrow-left"></i> Back To List
            </a>
        </div>

        <form method="post" action="" id="frmedit" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Edit" />
                <input type="hidden" name="vendor_purchase_invoice_id" value="<?php echo $header['vendor_purchase_invoice_id']; ?>" />
                
                <fieldset class="tender-inward">
                    <legend class="text-light-blue">
                        <i class="fa fa-file-text-o"></i> Purchase Bill Details
                    </legend>
                    
                    <div style="border:1px solid #ddd; padding:10px; margin-bottom:10px; background-color:#f9f9f9; border-radius:5px;">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="srch_enq_id">Search Enquiry No</label>
                                <input type="text" name="srch_enq_id" class="form-control srch_enq_id" value="" placeholder="Search Enquiry No" readonly/>
                            </div>
                        </div> 
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Company <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_company_id', ['' => 'Select Company'] + $company_opt, $header['company_id'], 'id="srch_company_id" class="form-control" disabled required'); ?>

                            <input type="hidden" name="srch_company_id" value="<?php echo $header['company_id']; ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Customer <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_customer_id', ['' => 'Select Customer'] + $customer_opt, $header['customer_id'], 'id="srch_customer_id" class="form-control" disabled required'); ?>
                             <input type="hidden" name="srch_customer_id" value="<?php echo $header['customer_id']; ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Tender Enquiry No <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'Select Enquiry'] +$tender_enquiry_opt , $header['tender_enquiry_id'], 'id="srch_tender_enquiry_id" class="form-control" disabled required'); ?>
                             <input type="hidden" name="srch_tender_enquiry_id" value="<?php echo $header['tender_enquiry_id']; ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Vendor Name <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_vendor_id', ['' => 'Select Vendor'] + $vendor_opt, $header['vendor_id'], 'id="srch_vendor_id" class="form-control" disabled required'); ?>
                             <input type="hidden" name="srch_vendor_id" value="<?php echo $header['vendor_id']; ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Vendor PO No <span style="color:red;">*</span>
                                <span data-toggle="tooltip" title="" class=""
                                    data-original-title="Only when Vendor PO Status is Confirmed then select the PO No. Otherwise leave it blank.">
                                    <i class="text-sm text-info fa fa-info-circle"></i>
                                </span>
                            </label>
                            <?php echo form_dropdown('srch_vendor_po_id', ['' => 'Select PO No'] + $vendor_po_opt, $header['vendor_po_id'], 'id="srch_vendor_po_id" class="form-control" disabled required'); ?>
                            <input type="hidden" name="srch_vendor_po_id" id="srch_vendor_po_id" value="<?php echo  $header['vendor_po_id']; ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Contact Person</label>
                            <?php echo form_dropdown('srch_vendor_contact_person_id', ['' => 'Select Contact'] + $vendor_contact_opt, $header['vendor_contact_person_id'], 'id=" " class="form-control"'); ?>
                            <input type="hidden" name="srch_vendor_contact_person_id" id="" value="<?php echo $header['vendor_contact_person_id']; ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Invoice Date</label>
                            <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                                value="<?php echo $header['invoice_date']; ?>" required="true">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Invoice No</label>
                            <input type="text" name="invoice_no" id="invoice_no" class="form-control"
                                value="<?php echo htmlspecialchars($header['invoice_no']); ?>"
                                placeholder="Enter Invoice No" required="true">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Upload Purchase Bill Document</label>
                            <input type="file" name="purchase_bill_upload" id="purchase_bill_upload" class="form-control">
                            <?php if (!empty($header['purchase_bill_upload'])): ?>
                                <small class="text-info">
                                    Current: <a href="<?php echo base_url($header['purchase_bill_upload']); ?>" target="_blank">View File</a>
                                </small>
                            <?php endif; ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Status</label><br>
                            <label class="radio-inline">
                                <input type="radio" name="status" value="Active" 
                                    <?php echo ($header['status'] == 'Active') ? 'checked' : ''; ?>> Active
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="status" value="Inactive" 
                                    <?php echo ($header['status'] == 'Inactive') ? 'checked' : ''; ?>> Inactive
                            </label>
                        </div>

                        <div class="form-group col-md-4">
                            <label>VAT Payer Purchase</label>
                            <?php echo form_dropdown('vat_payer_purchase_grp', $vat_payer_purchase_opt, $header['vat_payer_purchase_grp'], 'id="vat_payer_purchase_grp" class="form-control"'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Declaration Number</label>
                            <input type="text" name="declaration_no" id="declaration_no" class="form-control"
                                value="<?php echo htmlspecialchars($header['declaration_no']); ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Declaration Date</label>
                            <input type="date" name="declaration_date" id="declaration_date" class="form-control"
                                value="<?php echo $header['declaration_date']; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea id="editor2" name="remarks" class="form-control"
                                    placeholder="Enter remarks"><?php echo $header['remarks']; ?></textarea>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="mt-4">
                            <legend class="text-light-blue"><i class="fa fa-list"></i> Item Details</legend>
                            <table class="table table-bordered table-sm table-responsive">
                                <thead>
                                    <tr>
                                        <th style="width:5%;">✔</th>
                                        <th style="width:10%;">Item Code</th>
                                        <th style="width:40%;">Description</th>
                                        <th style="width:10%;">UOM & Qty</th>
                                        <th style="width:10%;">Rate</th>
                                        <th style="width:10%;">VAT %</th>
                                        <th style="width:10%;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="item_container">
                                    <?php if (!empty($all_po_items)): ?>
                                        <?php foreach ($all_po_items as $idx => $po_item): ?>
                                            <?php 
                                                // Check if this PO item is already saved
                                                $is_checked = isset($saved_item_ids[$po_item['vendor_po_item_id']]);
                                                $saved_data = $is_checked ? $saved_item_ids[$po_item['vendor_po_item_id']] : [];
                                                
                                                // Use saved values if available, otherwise PO values
                                                $item_rate = $is_checked ? $saved_data['rate'] : $po_item['rate'];
                                                $item_gst = $is_checked ? $saved_data['gst'] : $po_item['gst'];
                                                $item_qty = $is_checked ? $saved_data['qty'] : $po_item['qty'];
                                                $item_amount = $is_checked ? $saved_data['amount'] : $po_item['amount'];
                                            ?>
                                            <tr class="item-row">
                                                <td>
                                                    <input type="checkbox" class="item-check" 
                                                           name="selected_items[]" 
                                                           value="<?php echo $idx; ?>" 
                                                           <?php echo $is_checked ? 'checked' : ''; ?>>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" 
                                                           value="<?php echo htmlspecialchars($po_item['item_code']); ?>" readonly>
                                                    <input type="hidden" name="vendor_purchase_invoice_item_id[]" 
                                                           value="<?php echo $is_checked ? $saved_data['vendor_purchase_invoice_item_id'] : ''; ?>">
                                                    <input type="hidden" name="vendor_po_item_id[]" 
                                                           value="<?php echo $po_item['vendor_po_item_id']; ?>">
                                                    <input type="hidden" name="category_id[]" 
                                                           value="<?php echo $po_item['category_id']; ?>">
                                                    <input type="hidden" name="item_id[]" 
                                                           value="<?php echo $po_item['item_id']; ?>">
                                                    <input type="hidden" name="item_code[]" 
                                                           value="<?php echo htmlspecialchars($po_item['item_code']); ?>">
                                                </td>
                                                <td>
                                                    <textarea class="form-control" rows="2" name="item_desc[]" readonly><?php echo htmlspecialchars($po_item['item_desc']); ?></textarea>
                                                </td>
                                                <td>
                                                    <input type="text" name="uom[]" class="form-control" 
                                                           value="<?php echo htmlspecialchars($po_item['uom']); ?>" readonly>
                                                    <input type="number" step="0.01" name="qty[]" 
                                                           class="form-control qty-input" 
                                                           value="<?php echo $item_qty; ?>" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="rate[]" 
                                                           class="form-control rate-input" 
                                                           value="<?php echo $item_rate; ?>">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="gst[]" 
                                                           class="form-control gst-input" 
                                                           value="<?php echo $item_gst; ?>">
                                                    <input type="hidden" name="gst_amount[]" class="gst-amount-input">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="amount[]" 
                                                           class="form-control amount-input" 
                                                           value="<?php echo $item_amount; ?>" readonly>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <div class="tot_amt text-right">
                                <label>Total Bill Amount</label>
                                <div id="total_amount_display"><?php echo number_format($header['total_amount'], 2); ?></div>
                                <input type="hidden" name="total_amount" id="total_amount" value="<?php echo $header['total_amount']; ?>">

                                <label>Total GST</label>
                                <div id="total_gst_amount_display"><?php echo number_format($header['tax_amount'], 2); ?></div>
                                <input type="hidden" name="tax_amount" id="total_gst_amount" value="<?php echo $header['tax_amount']; ?>">
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('vendor-purchase-bill-list'); ?>" class="btn btn-warning pull-left">
                    <i class="fa fa-arrow-left"></i> Back To List
                </a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<style>
  .text-light-blue {
    color: #3c8dbc !important;
  }

  fieldset {
    border: 1px solid #d2d6de;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
  }

  legend {
    font-size: 16px;
    font-weight: bold;
    width: auto;
    padding: 0 12px;
  }

  .tot_amt {
    background: #f9f9f9;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-top: 10px;
  }

  .tot_amt label {
    font-weight: bold;
    margin-right: 10px;
  }

  .tot_amt div {
    display: inline-block;
    font-size: 1.2em;
    color: #3c8dbc;
    font-weight: bold;
    margin-right: 20px;
  }
</style>

<script>
  $(document).ready(function () {
    // Calculate totals on page load for existing items
    setTimeout(function() {
      $(".item-row").each(function() {
        calculateRow($(this));
      });
      calculateTotals();
    }, 500);

    // Initialize with existing values
    const initialCompanyId = $("#srch_company_id").val();
    const initialCustomerId = $("#srch_customer_id").val();
    const initialTenderEnqId = $("#srch_tender_enquiry_id").val();
    const initialVendorId = $("#srch_vendor_id").val();
    const initialVendorPoId = $("#srch_vendor_po_id").val();

    // Load initial dropdowns on page load
    if (initialCustomerId && initialCompanyId) {
      load_tender_enq(initialTenderEnqId);
    }

    if (initialTenderEnqId) {
      setTimeout(function() {
        load_vendors(initialTenderEnqId, initialVendorId);
      }, 500);
    }

    if (initialVendorId) {
      setTimeout(function() {
        load_vendor_po_and_contacts(initialVendorId, initialVendorPoId);
      }, 1000);
    }

    // Autocomplete for Enquiry Search
    $(".srch_enq_id").autocomplete({
      source: function (request, response) {
        $.ajax({
          url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
          type: "POST",
          data: { search: request.term },
          dataType: "json",
          success: function (data) {
            response(data);
          },
        });
      },
      minLength: 1,
      select: function (event, ui) {
        $("#srch_company_id").val(ui.item.company_id);
        $("#srch_customer_id").val(ui.item.customer_id).change();
        load_tender_enq(ui.item.tender_enquiry_id);
      },
    });

    function load_tender_enq(t_enq_id = "") {
      const customer_id = $("#srch_customer_id").val();
      const company_id = $("#srch_company_id").val();
      const $enquiryDropdown = $("#srch_tender_enquiry_id");

      $enquiryDropdown
        .html('<option value="">Select Enquiry</option>')
        .prop("disabled", true);

      if (!customer_id || !company_id) return;

      $.ajax({
        url: "<?php echo site_url('vendor/get_tender_enquiries_by_customer'); ?>",
        type: "POST",
        data: {
          company_id: company_id,
          customer_id: customer_id,
        },
        dataType: "json",
        success: function (res) {
          if (res.length > 0) {
            $enquiryDropdown.prop("disabled", false);

            $.each(res, function (i, row) {
              $enquiryDropdown.append(
                $("<option>", {
                  value: row.tender_enquiry_id,
                  text: row.display,
                })
              );
            });

            if (t_enq_id) {
              $enquiryDropdown.val(t_enq_id).trigger("change");
            }
          } else {
            $enquiryDropdown.html('<option value="">No enquiries found</option>');
          }
        },
        error: function () {
          alert("Error loading enquiries");
        },
      });
    }

    $("#srch_customer_id").on("change", function () {
      load_tender_enq("");
    });

    // Load Vendors when Tender Enquiry changes
    $("#srch_tender_enquiry_id").on("change", function () {
      const tender_id = $(this).val();
      load_vendors(tender_id, "");
    });

    function load_vendors(tender_id, selected_vendor = "") {
      const $vendor = $("#srch_vendor_id");
      $vendor.html('<option value="">Loading...</option>');

      if (!tender_id) {
        $vendor.html('<option value="">Select Vendor</option>');
        return;
      }

      $.post(
        "<?php echo site_url('vendor/get_vendor_list_purchase_inward'); ?>",
        { srch_tender_enquiry_id: tender_id },
        function (res) {
          $vendor.html('<option value="">Select Vendor</option>');
          if (res.length > 0) {
            $.each(res, function (i, row) {
              $vendor.append(
                `<option value="${row.vendor_id}">${row.vendor_name}</option>`
              );
            });
            if (selected_vendor) {
              $vendor.val(selected_vendor).trigger("change");
            }
          }
        },
        "json"
      );
    }

    // Load PO and Contacts when Vendor changes
    $("#srch_vendor_id").on("change", function () {
      const vendor_id = $(this).val();
      load_vendor_po_and_contacts(vendor_id, "");
    });

    function load_vendor_po_and_contacts(vendor_id, selected_po = "") {
      const $enquiry = $("#srch_vendor_po_id");
      const $contact = $("#srch_vendor_contact_id");

      $enquiry.html('<option value="">Select Enquiry No</option>');
      $contact.html('<option value="">Select Contact</option>');

      if (!vendor_id) return;

      // Load PO Numbers
      $.post(
        "<?php echo site_url('vendor/get_data'); ?>",
        {
          tbl: "get-vendor-purchase-inward-load-list",
          id: vendor_id,
        },
        function (res) {
          if (res.length > 0) {
            $.each(res, function (i, row) {
              $enquiry.append(
                `<option value="${row.vendor_po_id}">${row.vendor_po_no}</option>`
              );
            });
            if (selected_po) {
              $enquiry.val(selected_po);
            }
          }
        },
        "json"
      );

      // Load Contact Persons
      $.post(
        "<?php echo site_url('vendor/get_data'); ?>",
        {
          tbl: "get-vendor-contacts",
          id: vendor_id,
        },
        function (res) {
          if (res.length > 0) {
            $.each(res, function (i, row) {
              $contact.append(
                `<option value="${row.vendor_contact_id}">${row.contact_person_name}</option>`
              );
            });
          }
        },
        "json"
      );
    }

    // Recalculate on input change
    $(document).on(
      "input change",
      ".rate-input, .gst-input, .qty-input, .item-check",
      function () {
        const $row = $(this).closest('.item-row');
        calculateRow($row);
        calculateTotals();
      }
    );

    function calculateRow($row) {
      const qty = parseFloat($row.find(".qty-input").val()) || 0;
      const rate = parseFloat($row.find(".rate-input").val()) || 0;
      const gst = parseFloat($row.find(".gst-input").val()) || 0;

      const base = qty * rate;
      const gst_amt = (base * gst) / 100;
      const total = base + gst_amt;

      $row.find(".gst-amount-input").val(gst_amt.toFixed(2));
      $row.find(".amount-input").val(total.toFixed(2));
    }

    function calculateTotals() {
      let total_amount = 0;
      let total_gst = 0;

      $(".item-row").each(function () {
        if ($(this).find(".item-check").is(":checked")) {
          total_amount += parseFloat($(this).find(".amount-input").val()) || 0;
          total_gst += parseFloat($(this).find(".gst-amount-input").val()) || 0;
        }
      });

      $("#total_amount_display").text(total_amount.toFixed(2));
      $("#total_amount").val(total_amount.toFixed(2));

      $("#total_gst_amount_display").text(total_gst.toFixed(2));
      $("#total_gst_amount").val(total_gst.toFixed(2));
    }

    // Form validation
    $("form").on("submit", function () {
      if ($(".item-check:checked").length === 0) {
        alert("Please select at least one item");
        return false;
      }
    });
  });

  // CKEditor
  window.onload = function () {
    CKEDITOR.replace("editor2", {
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
  };
</script>