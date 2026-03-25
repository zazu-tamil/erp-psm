<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link
  rel="stylesheet"
  href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"
/>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<style>
  /* Your existing styles remain unchanged */
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
    .total-wrapper {
      justify-content: center;
    }
    .total-box {
      text-align: center;
      min-width: 220px;
    }
  }
</style>
<script>
  $(document).ready(function () {
    $(".srch_enq_id").autocomplete({
      source: function (request, response) {
        $.ajax({
          url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
          type: "POST",
          data: { search: request.term },
          dataType: "json",
          success: function (data) {
            //console.log(data);
            response(data);
          },
        });
      },

      minLength: 1,

      select: function (event, ui) {
        //console.log(ui);
        $("#srch_company_id").val(ui.item.company_id);
        $("#srch_customer_id").val(ui.item.customer_id).change();
        //$("#srch_tender_enquiry_id").val(ui.item.tender_enquiry_id);
        load_tender_enq(ui.item.tender_enquiry_id);
      },
    });

    function load_tender_enq(t_enq_id = "") {
      const customer_id = $("#srch_customer_id").val(); // <-- IMPORTANT
      const company_id = $("#srch_company_id").val();
      const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $container = $("#item_container");

      // Reset
      $enquiryDropdown
        .html('<option value="">Select Enquiry</option>')
        .prop("disabled", true);

      $container.html("");

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
                }),
              );
            });

            // ✅ SET SELECTED VALUE HERE
            if (t_enq_id) {
              $enquiryDropdown.val(t_enq_id).trigger("change");
            }
          } else {
            $enquiryDropdown.html(
              '<option value="">No enquiries found</option>',
            );
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

    // 1. Company → Load Customers
    /*$("#srch_company_id").on("change", function () {
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
    });*/

    // 2. Customer → Load Tender Enquiries
    $("#srch_customer_id1").on("change", function () {
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
                `<option value="${row.tender_enquiry_id}">${row.display}</option>`,
              );
            });
            $enquiry.prop("disabled", false);
          }
        },
        "json",
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
                `<option value="${row.vendor_id}">${row.vendor_name}</option>`,
              );
            });
          }
        },
        "json",
      );
    });

    // 4. Vendor → Load Rate Enquiry Numbers
    $("#srch_vendor_id").on("change", function () {
      //alert($('#srch_tender_enquiry_id').val());
      //alert($(this).val());
      const vendor_id = $(this).val();
      const $enquiry = $("#srch_vendor_rate_enquiry_id");
      const $contact = $("#srch_vendor_contact_id");

      //$enquiry.html('<option value="">Select Enquiry No</option>');
      //$contact.html('<option value="">Select Contact</option>');
      $enquiry.html("");
      $contact.html("");

      if (!vendor_id) return;

      // Load Rate Enquiry Nos
      $.post(
        "<?php echo site_url('vendor/get_data'); ?>",
        {
          tbl: "get-vendor-rate-enquiry-list",
          id: $(this).val(),
          tender_enquiry_id: $("#srch_tender_enquiry_id").val(),
        },
        function (res) {
          //console.log(res); alert('hi');
          if (res.length > 0) {
            $.each(res, function (i, row) {
              $enquiry.append(
                `<option value="${row.vendor_rate_enquiry_id}">${row.vendor_rate_enquiry_no} [ ${row.enquiry_date} ]</option>`,
              );
            });
          }

          // 🔥 trigger AFTER options are ready
          $enquiry.prop("selectedIndex", 0).trigger("change");
        },
        "json",
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
                `<option value="${row.vendor_contact_id}">${row.contact_person_name}</option>`,
              );
            });
          }
        },
        "json",
      );
    });
  });

  // CKEditor
  window.onload = function () {
    CKEDITOR.replace("editor1", { height: 120 });
    CKEDITOR.replace("editor2", { height: 120 });
  };
</script>
<script>
  $(document).ready(function () {
    let rowIndex = 0; // ⭐ VERY IMPORTANT

    $(".cls_export").hide();

    function getRowTemplate(index) {
      return `
          <tr class="item-row">

          <td class="text-center">
          <input type="checkbox"
          class="form-check-input item-check"
          name="selected_items[]"
          value="${index}">
          </td>

          <td>
          <input type="text" name="item_code[]" class="form-control item_code">
          <input type="hidden"
          name="vendor_rate_enquiry_item_id[]"
          class="vendor_rate_enquiry_item_id">
          </td>

          <td>
          <textarea name="item_desc[]" 
          class="form-control item_desc"></textarea>
          </td>

          <td>
          <input type="text" name="uom[]" class="form-control uom mb-1">
          <input type="number" step="any"
          name="qty[]" class="form-control qty">
          </td>

          <td>
          <input type="number" step="any"
          name="rate[]" class="form-control rate">
          </td>

          <td>
          <input type="number" step="any"
          name="gst[]" class="form-control vat" value="10">
          </td>

          <td>
          <input type="number"
          name="amount_wo_tax[]"
          class="form-control amount_wo_tax" readonly>
          </td>

          <td>
          <input type="number"
          name="amount[]"
          class="form-control amount" readonly>
          </td>

          </tr>`;
    }

    /* =====================================================
   EXPORT EXCEL
===================================================== */
    $(document).on("click", "#btnExport", function () {
      let jsonData = $(this).data("xls");

      if (typeof jsonData === "string") {
        jsonData = JSON.parse(jsonData);
      }

      const ws = XLSX.utils.json_to_sheet(jsonData);
      const wb = XLSX.utils.book_new();

      XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
      XLSX.writeFile(wb, "VendorQuote.xlsx");
    });

    /* =====================================================
   IMPORT EXCEL
===================================================== */
    $("#excelFile").on("change", function (e) {
      const file = e.target.files[0];
      if (!file) return;

      const reader = new FileReader();

      reader.onload = function (e) {
        const wb = XLSX.read(e.target.result, { type: "binary" });
        const sheet = wb.Sheets[wb.SheetNames[0]];
        const rows = XLSX.utils.sheet_to_json(sheet, { defval: "" });

        $("#item_container").empty();
        rowIndex = 0;

        rows.forEach(function (r) {
          let newRow = $(getRowTemplate(rowIndex));

          newRow
            .find(".vendor_rate_enquiry_item_id")
            .val(r.vendor_rate_enquiry_item_id || "");

          newRow.find(".item_code").val(r.item_code || "");
          newRow.find(".item_desc").val(r.item_desc || "");
          newRow.find(".uom").val(r.uom || "");
          newRow.find(".qty").val(r.qty || 0);
          newRow.find(".rate").val(r.rate || 0);
          newRow.find(".vat").val(r.vat || 10);

          $("#item_container").append(newRow);

          rowIndex++;
        });

        calculateAllRows();
      };

      reader.readAsBinaryString(file);
    });

    /* =====================================================
   AJAX LOAD ITEMS
===================================================== */
    $("#srch_vendor_rate_enquiry_id").change(function () {
      const id = $(this).val();
      const container = $("#item_container");

      container.html("");
      rowIndex = 0;

      if (!id) return;

      $.ajax({
        url: "<?= site_url('vendor/get_vendor_quotation_rate_enquiry');?>",
        type: "POST",
        data: { vendor_rate_enquiry_id: id },
        dataType: "json",

        success: function (res) {
          $(".cls_export").show();

          $("#btnExport").attr("data-xls", JSON.stringify(res));

          $.each(res, function (i, row) {
            let newRow = $(getRowTemplate(rowIndex));

            newRow
              .find(".vendor_rate_enquiry_item_id")
              .val(row.vendor_rate_enquiry_item_id);

            newRow.find(".item_code").val(row.item_code);
            newRow.find(".item_desc").val(row.item_desc);
            newRow.find(".uom").val(row.uom);
            newRow.find(".qty").val(row.qty);
            newRow.find(".rate").val(row.rate);
            newRow.find(".vat").val(row.vat || 10);

            container.append(newRow);

            rowIndex++;
          });

          calculateAllRows();
        },
      });
    });

   
    $(document).on("change", "#selectall", function () {
      $(".item-check").prop("checked", this.checked);
    });

    $(document).on("change", ".item-check", function () {
      $("#selectall").prop(
        "checked",
        $(".item-check:checked").length === $(".item-check").length,
      );
    });

    $(document).on("input change", ".qty, .rate, .vat", function () {
      calculateRow($(this).closest(".item-row"));
    });

    $(document).on("input change", ".rate-input, .vat-dropdown", function () {
      const $row = $(this).closest(".item-card");
      const qty = parseFloat($row.find(".qty-input").val()) || 0;
      const rate = parseFloat($row.find(".rate-input").val()) || 0;
      const gst = parseFloat($row.find(".vat-dropdown").val()) || 0;

      const amount = qty * rate * (1 + gst / 100);
      $row.find(".amount-input").val(amount.toFixed(3));
      $row.find(".amountwotx").val((qty * rate).toFixed(3));

      const totals = calculateTotalAmount();
      calculateTotalAmount_addt(totals.wot, totals.wt);
    });

    function calculateRow($row) {
      const qty = parseFloat($row.find(".qty").val()) || 0;
      const rate = parseFloat($row.find(".rate").val()) || 0;
      const vat = parseFloat($row.find(".vat").val()) || 0;

      const amountWoTax = qty * rate;
      const amount = amountWoTax * (1 + vat / 100);

      $row.find(".amount").val(amount.toFixed(3));
      $row.find(".amount_wo_tax").val(amountWoTax.toFixed(3));

      const totals = calculateTotalAmount();
      calculateTotalAmount_addt(totals.wot, totals.wt);
    }

    function calculateAllRows() {
      $(".item-row").each(function () {
        const $row = $(this);
        const qty = parseFloat($row.find(".qty").val()) || 0;
        const rate = parseFloat($row.find(".rate").val()) || 0;
        const vat = parseFloat($row.find(".vat").val()) || 0;

        const amountWoTax = qty * rate;
        const amount = amountWoTax * (1 + vat / 100);

        $row.find(".amount").val(amount.toFixed(3));
        $row.find(".amount_wo_tax").val(amountWoTax.toFixed(3));
      });

      // After all rows calculated, update totals once
      const totals = calculateTotalAmount();
      calculateTotalAmount_addt(totals.wot, totals.wt);
    }

    function calculateTotalAmount() {
      let wt = 0;
      $(".amount-input, .amount").each(function () {
        wt += parseFloat($(this).val()) || 0;
      });

      let wot = 0;
      $(".amountwotx, .amount_wo_tax").each(function () {
        wot += parseFloat($(this).val()) || 0;
      });

      $("#total_amount_wo_tax").text(wot.toFixed(3));
      $("#total_amount").text(wt.toFixed(3));
      $("#tax_amount").text((wt - wot).toFixed(3)); // FIX: was #total_tax_amount

      return { wot, wt };
    }

    $(".chk_addt_charges_type_id").on("change", function () {
      const row = $(this).closest("tr");
      const enabled = $(this).is(":checked");

      row
        .find(
          ".addt_charges_amt, .addt_charges_vat, .addt_charges_vat_amt, .addt_charges_tot_amt",
        )
        .prop("disabled", !enabled);

      calculateTotalAmount_addt();
    });

    // Trigger on page load to set initial disabled states
    $(".chk_addt_charges_type_id").change();

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

        calculateTotalAmount_addt();
      },
    );

    function calculateTotalAmount_addt(base_wot, base_wt) {
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
  });
</script>
