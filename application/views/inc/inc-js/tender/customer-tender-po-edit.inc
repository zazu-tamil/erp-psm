<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
  #total_amount,
  #total_amount_wo_tax {
    font-size: 1.4rem;
    font-weight: 700;
    margin-left: 5px;
  }
  @media (max-width: 768px) {
    .total-wrapper {
      justify-content: center;
    }
    .total-box {
      text-align: center;
      min-width: 220px;
    }
  }

  .readonly-select {
    pointer-events: none;
    background-color: #e9ecef;
  }
</style>
<script>
  $(document).ready(function () 
  {

    // Enable / Disable on checkbox change
     $(document).on("change", ".chk_tender_quote_addtchrg_id", function () { 
      
      //alert('CHK Box');

        let row = $(this).closest('tr');

        if ($(this).is(':checked')) {
            row.find(
                    '.addt_charges_amt, .addt_charges_vat, .addt_charges_vat_amt, .addt_charges_tot_amt'
                )
                .prop('disabled', false);
        } else {
            row.find(
                    '.addt_charges_amt, .addt_charges_vat, .addt_charges_vat_amt, .addt_charges_tot_amt'
                )
                .prop('disabled', true);
            // .val('0'); // clear values
        }

        calculateTotalAmount_addt();
    });

    $('#tb_addt_chrg_list .chk_tender_quote_addtchrg_id').change();


    // Calculation on change
    $(document).on('input', '.addt_charges_amt, .addt_charges_vat', function() {

        let row = $(this).closest('tr');

        let amt = parseFloat(row.find('.addt_charges_amt').val()) || 0;
        let vat = parseFloat(row.find('.addt_charges_vat').val()) || 0;

        let vat_amt = (amt * vat) / 100;
        let total = amt + vat_amt;

        row.find('.addt_charges_vat_amt').val(vat_amt.toFixed(2));
        row.find('.addt_charges_tot_amt').val(total.toFixed(2));

        calculateTotalAmount_addt();
    });


    function calculateTotalAmount_addt() {
        let total_addt_amt_wo_tax = 0;
        $(".addt_charges_amt").each(function() {  
            if ($(this).closest('tr').find('.chk_tender_quote_addtchrg_id').is(':checked')) {
                total_addt_amt_wo_tax += parseFloat($(this).val()) || 0;
            }
        });
        //alert("TEST" + $("#total_amount_wo_tax").text());
        total_addt_amt_wo_tax = parseFloat($("#total_amount_wo_tax").text()) + parseFloat(total_addt_amt_wo_tax);
        $("#total_amount_wo_tax_addt").text(total_addt_amt_wo_tax.toFixed(3));


        let total_addt_amt_w_tax = 0;
        $(".addt_charges_tot_amt").each(function() {
            if ($(this).closest('tr').find('.chk_tender_quote_addtchrg_id').is(':checked')) {
                total_addt_amt_w_tax += parseFloat($(this).val()) || 0;
            }
        });
        total_addt_amt_w_tax = parseFloat($("#total_amount").text()) + parseFloat(total_addt_amt_w_tax);
        $("#total_amount_addt").text(total_addt_amt_w_tax.toFixed(3));


        $("#total_tax_amount_addt").text((total_addt_amt_w_tax - total_addt_amt_wo_tax).toFixed(3));

    }



    /* ================================================================
       1. COMPANY CHANGE - Load ALL Customers (no filtering by company)
       ================================================================ */
    $("#srch_company_id").on("change", function () {
      const company_id = $(this).val();
      const $customerDropdown = $("#srch_customer_id");
      const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $quotationDropdown = $("#srch_quotation_no");
      const $container = $("#item_container");

      // Reset dependent dropdowns
      $enquiryDropdown.html('<option value="">Select Enquiry</option>').prop('disabled', true);
      $quotationDropdown.html('<option value="">Select Quotation</option>').prop('disabled', true);
      $container.html('');

      if (!company_id) {
        $customerDropdown.html('<option value="">Select Customer</option>').prop('disabled', false);
        return;
      }

      // Load ALL customers (not filtered by company)
      $.ajax({
        url: "<?php echo site_url('vendor/get_all_customers'); ?>",
        type: "POST",
        dataType: "json",
        success: function (res) {
          $customerDropdown.html('<option value="">Select Customer</option>');
          if (res.length > 0) {
            $.each(res, function (i, row) {
              $customerDropdown.append(
                $('<option></option>')
                  .attr('value', row.customer_id)
                  .text(row.customer_name)
              );
            });
            $customerDropdown.prop('disabled', false);
          } else {
            $customerDropdown.html('<option value="">No customers found</option>');
          }
        },
        error: function () {
          alert('Error loading customers');
        }
      });
    });

    /* ================================================================
       2. CUSTOMER CHANGE - Load Tender Enquiries (filtered by company + customer)
       ================================================================ */

    $("#srch_customer_id").on("change", function () {
      const customer_id = $(this).val();
      const company_id = $("#srch_company_id").val();
      const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $quotationDropdown = $("#srch_quotation_no");
      const $container = $("#item_container");

      // Reset dependent dropdowns and items
      $enquiryDropdown.html('<option value="">Select Enquiry</option>').prop('disabled', true);
      $quotationDropdown.html('<option value="">Select Quotation</option>').prop('disabled', true);
      $container.html('');

      if (!customer_id || !company_id) return;

      $.ajax({
        url: "<?php echo site_url('tender/get_tender_enquiries_by_customer'); ?>",
        type: "POST",
        data: {
          company_id: company_id,
          customer_id: customer_id
        },
        dataType: "json",
        success: function (res) {
          if (res.length > 0) {
            $enquiryDropdown.prop('disabled', false);
            $.each(res, function (i, row) {
              $enquiryDropdown.append(
                $('<option></option>')
                  .attr('value', row.tender_enquiry_id)
                  .text(row.display)
              );
            });
          } else {
            $enquiryDropdown.html('<option value="">No enquiries found</option>');
          }
        },
        error: function () {
          alert('Error loading enquiries');
        }
      });
    });

    /* ================================================================
       3. TENDER ENQUIRY CHANGE - Load Quotations with "Won" status
       ================================================================ */
    $("#srch_tender_enquiry_id").on("change", function () {

      const company_id = $("#srch_company_id").val();
      const customer_id = $("#srch_customer_id").val();
      const $quotationDropdown = $("#srch_quotation_no");
      const $container = $("#item_container");

      // Reset quotation and items
      $quotationDropdown.html('<option value="">Select Quotation</option>').prop('disabled', true);
      $container.html('');

      if (!company_id || !customer_id) return;

      $.ajax({
        url: "<?php echo site_url('tender/get_quotations_by_customer'); ?>",
        type: "POST",
        data: {
          company_id: company_id,
          customer_id: customer_id
        },
        dataType: "json",
        success: function (res) {
            console.log(res);
          if (res.length > 0) {
            $quotationDropdown.prop('disabled', false);
            $.each(res, function (i, row) {
              $quotationDropdown.append(
                $('<option></option>')
                  .attr('value', row.tender_quotation_id)
                  .text(row.display)
              );
            });
          } else {
            $quotationDropdown.html('<option value="">No quotations with "Won" status found</option>');
          }
        },
        error: function () {
          alert('Error loading quotations');
        }
      });
    });


    function exportJSONToXLSX(data, fileName = "export.xlsx") {

        // Convert JSON to worksheet
        const worksheet = XLSX.utils.json_to_sheet(data);

        // Create workbook
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");

        // Export XLSX
        XLSX.writeFile(workbook, fileName);
    }

    $(document).on("click", "#btnExport", function () {
      let jsonData = <?php  echo json_encode($merged_items); ?>;
      exportJSONToXLSX(jsonData, $(this).val() +".xlsx");
    });

    const itemHtml = `
                <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
                  <h5 class="text-primary mb-3">Item Details <span class="cnt"></span></h5>
                  <div class="row">
                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                      <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="" checked>
                       <input type="hidden" name="tender_po_item_id[]" class="form-control tender_po_item_id" value="">
                      <input type="hidden" name="tender_quotation_item_id[]" class="form-control tender_quotation_item_id" value="">
                    </div>

                    <div class="col-md-3">
                      <div class="form-group">
                        <label>Item Code</label>
                        <input type="text" class="form-control item_code" name="item_code[]" value="" readonly>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Item Description</label>
                        <textarea name="item_desc[]" class="form-control desc-textarea" rows="3"></textarea>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>UOM</label>
                            <input type="text" name="uom[]" class="form-control uom" value="" readonly>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" step="any" name="qty[]" class="form-control qty-input" value="" readonly>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Rate</label>
                            <input type="number" step="any" name="rate[]" class="form-control rate-input" value="">
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <label>VAT %</label>
                            <input type="number" step="any" name="gst[]" class="form-control vat-dropdown" value="">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Amount WO Tax</label>
                            <input type="number" step="any" name="amountwotx[]" class="form-control amountwotx" value="" readonly>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Amount</label>
                            <input type="number" step="any" name="amount[]" class="form-control amount-input" value="0.00" readonly>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              `;


    $("#excelFile").on("change", function (e) {

        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();

        reader.onload = function (e) {

            const workbook = XLSX.read(e.target.result, { type: "binary" });
            const sheet = workbook.Sheets[workbook.SheetNames[0]];

            // JSON with headers
            const rows = XLSX.utils.sheet_to_json(sheet, { defval: "" });

            console.log(rows);

            $("#item_container").empty();
            let i = 0;
            rows.forEach(function (r) {

                let newRow = $(itemHtml);

                newRow.find(".cnt").html((i+1));
                newRow.find(".item-check").val(i);
                newRow.find(".tender_quotation_item_id").val(r.tender_quotation_item_id);
                newRow.find(".tender_po_item_id").val(r.tender_po_item_id);
                newRow.find(".item_code").val(r.item_code);
                newRow.find(".desc-textarea").val(r.item_desc);
                newRow.find(".uom").val(r.uom);
                newRow.find(".qty-input").val(r.qty);
                newRow.find(".vat-dropdown").val(r.vat);
                newRow.find(".rate-input").val(r.rate);
                newRow.find(".amount-input").val(r.amount);

                $("#item_container").append(newRow);

                newRow.find(".rate-input").change();

                i++;
            });
        };

        reader.readAsBinaryString(file);
    });

 
    $("#srch_quotation_no").on("change", function () {
      const tender_quotation_id = $(this).val();
      const $container = $("#item_container");
      $container.html("");

      if (!tender_quotation_id) return;

      $.ajax({
        url: "<?php echo site_url('tender/get_quotation_items'); ?>",
        type: "POST",
        data: {
          tender_quotation_id: tender_quotation_id
        },
        dataType: "json",
        success: function (res) {
          if (res.length > 0) {
            $.each(res, function (i, row) {
              const hiddenFields = `
                <input type="hidden" name="tender_quotation_item_id[]" value="${row.tender_quotation_item_id}">
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
                        <label>Item Code</label>
                        <input type="text" class="form-control" value="${row.item_code || ''}" readonly>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Item Description</label>
                        <textarea name="item_desc[]" class="form-control desc-textarea" rows="3">${row.item_desc || ''}</textarea>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>UOM</label>
                            <input type="text" name="uom[]" class="form-control" value="${row.uom || ''}" readonly>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" step="any" name="qty[]" class="form-control qty-input" value="${row.qty || 0}" readonly>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Rate</label>
                            <input type="number" step="any" name="rate[]" class="form-control rate-input" value="${row.rate || 0}">
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <label>VAT %</label>
                            <select name="gst[]" class="form-control vat-dropdown">
                              <option value="">Select</option>
                              <?php foreach($gst_opt as $gid => $pct): ?>
                                <option value="<?= $pct; ?>"><?= $pct; ?>%</option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Amount WO Tax</label>
                            <input type="number" step="any" name="amountwotx[]" class="form-control amountwotx" value="" readonly>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Amount</label>
                            <input type="number" step="any" name="amount[]" class="form-control amount-input" value="0.00" readonly>
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

            // Add total row
           /* $container.append(`
              <div class="total-wrapper mt-4 mb-4">
                <div class="total-box shadow-sm">
                  <h5 class="mb-0">
                    <i class="fa fa-calculator text-success me-2"></i>
                    <strong>Total Amount:</strong>
                    <span class="text-primary">₹ <span id="total_amount">0.00</span></span>
                  </h5>
                </div>
              </div>
            `);*/
            $container.append(`
                    <div class="row">
                        <div class="col-md-3 pull-right ">
                            <div class="total-box shadow-sm">
                                <h5 class="mb-0">
                                    <i class="fa fa-calculator text-success me-2"></i>
                                    <strong>Total Amount With Tax:</strong>
                                    <span class="text-primary"><span id="total_amount">0.000</span></span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-md-3 pull-right">
                            <div class="total-box shadow-sm">
                                <h5 class="mb-0">
                                    <i class="fa fa-calculator text-success me-2"></i>
                                    <strong>Total Amount WO Tax:</strong>
                                    <span class="text-primary"><span id="total_amount_wo_tax">0.000</span></span>
                                </h5>
                            </div>
                        </div>
                    </div>
            `);

            // Calculate totals on load
            calculateTotalAmount();
          } else {
            $container.html('<div class="alert alert-warning">No items found for this quotation.</div>');
          }
        },
        error: function () {
          alert('Error loading quotation items');
        }
      });
    });
     calculateTotalAmount();
  

  $(document).on("input change", ".rate-input, .vat-dropdown, .qty-input, .item-check", function () {

      const $row = $(this).closest(".item-card");
      const isChecked = $row.find(".item-check").is(":checked");

      const qty  = parseFloat($row.find(".qty-input").val()) || 0;
      const rate = parseFloat($row.find(".rate-input").val()) || 0;
      const gst  = parseFloat($row.find(".vat-dropdown").val()) || 0;

      if (isChecked) {

          const amount = qty * rate * (1 + gst / 100);

          $row.find(".amount-input").val(amount.toFixed(3));
          $row.find(".amountwotx").val((qty * rate).toFixed(3));

      } else { 
          $row.find(".amount-input").val("0.000");
          $row.find(".amountwotx").val("0.000");
      } 
      calculateTotalAmount();
  });

  function calculateTotalAmount() {

      let total = 0;
      let total_wot = 0;

      $(".item-card").each(function () {

          if ($(this).find(".item-check").is(":checked")) {

              total += parseFloat($(this).find(".amount-input").val()) || 0;
              total_wot += parseFloat($(this).find(".amountwotx").val()) || 0;
          }
      });

      $("#total_amount").text(total.toFixed(3));
      $("#total_amount_wo_tax").text(total_wot.toFixed(3));
      $("#total_tax_amount").text((total - total_wot).toFixed(3));

      calculateTotalAmount_addt();
  }

  $(document).on("change", ".item-check", function () {

        const $card = $(this).closest(".item-card");

        if (!$(this).is(":checked")) {

            // remove hidden fields
            $card.find(
                "input[type=hidden][name^='tender_quotation_item_id'], \
                input[type=hidden][name^='category_id'], \
                input[type=hidden][name^='item_id']"
            ).remove();

            // reset amount
            $card.find(".amount-input").val("0.000");
            $card.find(".amountwotx").val("0.000");
        }

        calculateTotalAmount();
  });

   calculateTotalAmount();

  $(function () {
    CKEDITOR.replace("editor1", {
      height: 80,
      extraPlugins: "justify,colorbutton,font,table",
      removeButtons: "",
      toolbar: [
        {
          name: "clipboard",
          items: ["Cut", "Copy", "Paste", "PasteText", "PasteFromWord", "-", "Undo", "Redo"],
        },
        {
          name: "basicstyles",
          items: ["Bold", "Italic", "Underline", "Strike", "RemoveFormat", "CopyFormatting"],
        },
        {
          name: "paragraph",
          items: [
            "NumberedList", "BulletedList", "-", "Outdent", "Indent", "-",
            "Blockquote", "JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyBlock",
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
        CKEDITOR.replace("editor2", {
      height: 80,
      extraPlugins: "justify,colorbutton,font,table",
      removeButtons: "",
      toolbar: [
        {
          name: "clipboard",
          items: ["Cut", "Copy", "Paste", "PasteText", "PasteFromWord", "-", "Undo", "Redo"],
        },
        {
          name: "basicstyles",
          items: ["Bold", "Italic", "Underline", "Strike", "RemoveFormat", "CopyFormatting"],
        },
        {
          name: "paragraph",
          items: [
            "NumberedList", "BulletedList", "-", "Outdent", "Indent", "-",
            "Blockquote", "JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyBlock",
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

  });
</script>
