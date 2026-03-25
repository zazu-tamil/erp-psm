<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<style>
  /* Your existing styles remain unchanged */
  .text-light-blue {
    color: #3c8dbc !important;
  }

  .readonly-select {
    pointer-events: none;
    background-color: #e9ecef;
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

  .total-wrapper {
    display: flex;
    justify-content: flex-end;
    margin: 20px 0;
  }

  .total-box {
    background: linear-gradient(135deg, #f6fff9, #e8f9f0);
    border: 2px solid #b5e0c6;
    border-radius: 12px;
    padding: 12px 24px;
    min-width: 280px;
    text-align: right;
    font-size: 1.2rem;
    font-weight: bold;
  }

  #total_amount {
    font-size: 1.5rem;
    color: #3c8dbc;
  }
</style>

<script>
  $(document).ready(function () {
    // 1. Company → Load Customers
    $("#srch_company_id").on("change", function () {
      const company_id = $(this).val();
      const $customer = $("#srch_customer_id");
      $customer.html('<option value="">Loading...</option>');

      if (!company_id) {
        $customer.html('<option value="">Select Customer</option>');
        return;
      }

      $.post(
        "<?php echo site_url('vendor/get_all_customers'); ?>",
        {},
        function (res) {
          $customer.html('<option value="">Select Customer</option>');
          $.each(res, function (i, row) {
            $customer.append(
              `<option value="${row.customer_id}">${row.customer_name}</option>`
            );
          });
        },
        "json"
      );
    });

    // 2. Customer → Load Tender Enquiries
    $("#srch_customer_id").on("change", function () {
      const customer_id = $(this).val();
      const company_id = $("#srch_company_id").val();
      const $enquiry = $("#srch_tender_enquiry_id");

      if (!customer_id || !company_id) {
        $enquiry
          .html('<option value="">Select Enquiry</option>')
          .prop("disabled", true);
        return;
      }

      $.post(
        "<?php echo site_url('vendor/get_tender_enquiries_by_customer'); ?>",
        {
          company_id: company_id,
          customer_id: customer_id,
        },
        function (res) {
          $enquiry.html('<option value="">Select Enquiry</option>');
          if (res.length > 0) {
            $.each(res, function (i, row) {
              $enquiry.append(
                `<option value="${row.tender_enquiry_id}">${row.display}</option>`
              );
            });
            $enquiry.prop("disabled", false);
          }
        },
        "json"
      );
    });

    // 3. Tender Enquiry → Load Vendors
    $("#srch_tender_enquiry_id").on("change", function () {
      const tender_id = $(this).val();
      const $vendor = $("#srch_vendor_id");
      $vendor.html('<option value="">Loading...</option>');

      if (!tender_id) {
        $vendor.html('<option value="">Select Vendor</option>');
        return;
      }

      $.post(
        "<?php echo site_url('vendor/get_vendor_list_quotation'); ?>",
        {
          srch_tender_enquiry_id: tender_id,
        },
        function (res) {
          $vendor.html('<option value="">Select Vendor</option>');
          if (res.length > 0) {
            $.each(res, function (i, row) {
              $vendor.append(
                `<option value="${row.vendor_id}">${row.vendor_name}</option>`
              );
            });
          }
        },
        "json"
      );
    });

    // 4. Vendor → Load Rate Enquiry Numbers
    $("#srch_vendor_id").on("change", function () {
      const vendor_id = $(this).val();
      const $enquiry = $("#srch_vendor_rate_enquiry_id");
      const $contact = $("#srch_vendor_contact_id");

      $enquiry.html('<option value="">Select Enquiry No</option>');
      $contact.html('<option value="">Select Contact</option>');

      if (!vendor_id) return;

      // Load Rate Enquiry Nos
      $.post(
        "<?php echo site_url('vendor/get_data'); ?>",
        {
          tbl: "get-vendor-rate-enquiry-list",
          id: vendor_id,
          tender_enquiry_id : $('#srch_tender_enquiry_id').val(),
        },
        function (res) {
          if (res.length > 0) {
            $.each(res, function (i, row) {
              $enquiry.append(
                `<option value="${row.vendor_rate_enquiry_id}">${row.vendor_rate_enquiry_no}</option>`
              );
            });
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
      let jsonData = <?php  echo json_encode($items); ?>;
      exportJSONToXLSX(jsonData, $(this).val() +".xlsx");
    });


    const rowHtml = `
                    <tr class="item-row">
                        <td>
                             <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="" checked>
                        </td>

                        <td>
                             <input type="text" name="item_code[]" class="form-control item_code" value="" >
                             <input type="hidden" name="vendor_quote_item_id[]" class="vendor_quote_item_id" value="">
                             <input type="hidden" name="vendor_rate_enquiry_item_id[]" class="vendor_rate_enquiry_item_id" value="">

                        </td>

                        <td>
                            <textarea name="item_desc[]" class="form-control item_desc" rows="2" ></textarea>
                        </td>

                        <td>
                            <input type="text" name="uom[]" class="form-control uom" value="" readonly>
                            <br>
                            <input type="number" step="any" name="qty[]" class="form-control qty" value="">
                        </td>

                        <td>
                            <input type="number" step="any" name="rate[]" class="form-control rate">
                        </td>

                        <td>
                            <input type="number" step="any" name="gst[]" class="form-control vat">
                        </td>

                        <td>
                            <input type="number" name="amount[]" class="form-control amount" value="0.00" readonly>
                        </td>


                    </tr>
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

                  let newRow = $(rowHtml);

                  newRow.find(".cnt").html((i+1));
                  newRow.find(".item-check").val(i);
                  newRow.find(".vendor_quote_item_id").val(r.vendor_quote_item_id);
                  newRow.find(".vendor_rate_enquiry_item_id").val(r.vendor_rate_enquiry_item_id);
                  newRow.find(".item_code").val(r.item_code);
                  newRow.find(".item_desc").val(r.item_desc);
                  newRow.find(".uom").val(r.uom);
                  newRow.find(".qty").val(r.qty);
                  newRow.find(".rate").val(r.rate);
                  newRow.find(".vat").val(r.vat);
                  newRow.find(".amount").val(r.amount);

                  $("#item_container").append(newRow);

                  i++;
              });

              $(".qty, .rate, .vat").trigger("input");

          };

          reader.readAsBinaryString(file);
      });



  // CKEditor
  window.onload = function () {
    CKEDITOR.replace("editor1", { height: 120 });
    CKEDITOR.replace("editor2", { height: 120 });
  };
</script>
<script>
  $("#srch_vendor_rate_enquiry_id").on("change", function () {
    const vendor_rate_enquiry_id = $(this).val();
    const $container = $("#item_container");
    $container.html("");

    if (!vendor_rate_enquiry_id) return;

    $.ajax({
      url: "<?php echo site_url('vendor/get_vendor_quotation_rate_enquiry'); ?>",
      type: "POST",
      data: { vendor_rate_enquiry_id },
      dataType: "json",
      success: function (res) {
        if (res.length === 0) {
          $container.html(
            '<tr><td colspan="10" class="text-center text-danger">No items found for this quotation.</td></tr>',
          );
          return;
        }

        $.each(res, function (i, row) {
          const rowHtml = `
                    <tr class="item-row"> 
                        <td>
                             <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="${i}" checked>
                        </td> 
                       
                        <td> 
                             <input type="text" name="srch_item[]" class="form-control srch_item" autocomplete="off" value="${
                               row.full_item_name || ""
                             }" readonly>
                            
                        <input type="hidden" name="vendor_rate_enquiry_item_id[]" value="${
                          row.vendor_rate_enquiry_item_id
                        }">
                             <input type="hidden" name="item_id[]" class="item_id" value="${
                               row.item_id
                             }">
                             <input type="hidden" name="category_id[]" class="category_id" value="${
                               row.category_id
                             }">
                        </td>

                        <td>
                            <textarea name="item_desc[]" class="form-control" rows="2" readonly>${
                              row.item_desc || ""
                            }</textarea>
                        </td>

                        <td>
                            <input type="text" name="uom[]" class="form-control" value="${
                              row.uom || ""
                            }" readonly>
                        </td>

                        <td>
                            <input type="number" step="any" name="qty[]" class="form-control qty" value="${
                              row.qty
                            }" readonly>
                        </td>

                        <td>
                            <input type="number" step="any" name="rate[]" class="form-control rate">
                        </td>
                        <td>
                            <input type="number"  name="gst[]" class="form-control vat">
                        </td>

                        <td>
                            <input type="number" name="amount[]" class="form-control amounttx amount" value="0.000" readonly>
                        </td> 
                       
                       
                    </tr>
                `;
          calculateTotalAmount();
          $container.append(rowHtml);
        });
      },
      error: function () {
        alert("Error loading quotation items");
      },
    });
  });
</script>

<script>
  $(document).ready(function () {
    /* =====================================================
       ROW CALCULATION
    ===================================================== */
    function calculateRow($row) {
      const isChecked = $row.find(".item-check").is(":checked");

      if (!isChecked) {
        $row.find(".amount").val("0.000");
        $row.find(".amountwotx").val("0.000");
        return;
      }

      const qty = parseFloat($row.find(".qty").val()) || 0;
      const rate = parseFloat($row.find(".rate").val()) || 0;
      const vat = parseFloat($row.find(".vat").val()) || 0;

      const amountWOTax = qty * rate;
      const amountWithTax = amountWOTax + (amountWOTax * vat) / 100;

      $row.find(".amountwotx").val(amountWOTax.toFixed(3));
      $row.find(".amount").val(amountWithTax.toFixed(3));
    }

    /* =====================================================
       BASE TOTALS — returns { wot, wt } to avoid stale DOM reads
    ===================================================== */
    function calculateTotalAmount() {
      let wt = 0;
      let wot = 0;

      $(".item-card").each(function () {
        const $row = $(this);
        if ($row.find(".item-check").is(":checked")) {
          wt += parseFloat($row.find(".amount").val()) || 0;
          wot += parseFloat($row.find(".amountwotx").val()) || 0;
        }
      });

      $("#total_amount").text(wt.toFixed(3));
      $("#total_amount_wo_tax").text(wot.toFixed(3));
      // FIX 1: was missing — your HTML has id="total_tax_amount"
      $("#total_tax_amount").text((wt - wot).toFixed(3));

      return { wot, wt };
    }

    /* =====================================================
       ADDITIONAL CHARGES TOTALS
       Accepts base totals as params — avoids stale DOM reads
    ===================================================== */
    function calculateTotalAmount_addt(base_wot, base_wt) {
      // FIX 2: use passed values if available, else read DOM as fallback
      base_wot =
        base_wot !== undefined
          ? base_wot
          : parseFloat($("#total_amount_wo_tax").text()) || 0;
      base_wt =
        base_wt !== undefined
          ? base_wt
          : parseFloat($("#total_amount").text()) || 0;

      let addt_wot = 0;
      $(".addt_charges_amt").each(function () {
        if (
          $(this).closest("tr").find(".chk_addt_charges_type_id").is(":checked")
        ) {
          addt_wot += parseFloat($(this).val()) || 0;
        }
      });

      let addt_wt = 0;
      $(".addt_charges_tot_amt").each(function () {
        if (
          $(this).closest("tr").find(".chk_addt_charges_type_id").is(":checked")
        ) {
          addt_wt += parseFloat($(this).val()) || 0;
        }
      });

      const total_wot = base_wot + addt_wot;
      const total_wt = base_wt + addt_wt;

      $("#total_amount_wo_tax_addt").text(total_wot.toFixed(3));
      $("#total_amount_addt").text(total_wt.toFixed(3));
      $("#total_tax_amount_addt").text((total_wt - total_wot).toFixed(3));
    }

    /* =====================================================
       INPUT CHANGE — qty / rate / vat
    ===================================================== */
    $(document).on("input change", ".qty, .rate, .vat", function () {
      const $row = $(this).closest(".item-card");
      calculateRow($row);
      const totals = calculateTotalAmount();
      calculateTotalAmount_addt(totals.wot, totals.wt);
    });

    /* =====================================================
       ITEM CHECKBOX CHANGE
    ===================================================== */
    $(document).on("change", ".item-check", function () {
      const $row = $(this).closest(".item-card");
      calculateRow($row);
      const totals = calculateTotalAmount();
      calculateTotalAmount_addt(totals.wot, totals.wt);

      $("#selectall").prop(
        "checked",
        $(".item-check:checked").length === $(".item-check").length,
      );
    });

    /* =====================================================
       SELECT ALL CHECKBOX
    ===================================================== */
    $("#selectall").on("change", function () {
      $(".item-check").prop("checked", this.checked);

      $(".item-card").each(function () {
        calculateRow($(this));
      });

      const totals = calculateTotalAmount();
      calculateTotalAmount_addt(totals.wot, totals.wt);
    });

    /* =====================================================
       ADDITIONAL CHARGES — enable / disable row on checkbox
    ===================================================== */
    $(".chk_addt_charges_type_id").on("change", function () {
      const row = $(this).closest("tr");
      const enabled = $(this).is(":checked");

      // FIX 4: simplified if/else to single prop call
      row
        .find(
          ".addt_charges_amt, .addt_charges_vat, .addt_charges_vat_amt, .addt_charges_tot_amt",
        )
        .prop("disabled", !enabled);

      const totals = calculateTotalAmount();
      calculateTotalAmount_addt(totals.wot, totals.wt);
    });

    /* =====================================================
       ADDITIONAL CHARGES — row amount calculation
    ===================================================== */
    $(document).on(
      "input",
      ".addt_charges_amt, .addt_charges_vat",
      function () {
        const row = $(this).closest("tr");

        const amt = parseFloat(row.find(".addt_charges_amt").val()) || 0;
        const vat = parseFloat(row.find(".addt_charges_vat").val()) || 0;

        const vat_amt = (amt * vat) / 100;
        const total = amt + vat_amt;

        row.find(".addt_charges_vat_amt").val(vat_amt.toFixed(2));
        row.find(".addt_charges_tot_amt").val(total.toFixed(2));

        const totals = calculateTotalAmount();
        calculateTotalAmount_addt(totals.wot, totals.wt);
      },
    );

    /* =====================================================
       EDIT MODE — INITIAL PAGE LOAD
       Recalculate all rows then both total sections
    ===================================================== */
    $(".item-card").each(function () {
      calculateRow($(this));
    });

    // FIX 3: also call addt totals on load so edit mode shows correct values
    const initTotals = calculateTotalAmount();

    // Trigger disable/enable states for addt charges rows
    $(".chk_addt_charges_type_id").change();

    // Now calculate addt totals with correct base values
    calculateTotalAmount_addt(initTotals.wot, initTotals.wt);
  });
</script>
