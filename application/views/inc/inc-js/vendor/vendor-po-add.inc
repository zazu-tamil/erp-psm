<!-- ============================================================
     STYLES
============================================================ -->
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

<!-- ============================================================
     EXTERNAL SCRIPTS
============================================================ -->
<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
<link
  rel="stylesheet"
  href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"
/>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- ============================================================
     ALL JAVASCRIPT — SINGLE $(document).ready() BLOCK
============================================================ -->
<script>
  $(document).ready(function () {
    /* ============================================================
       VARIABLES
    ============================================================ */
    let rowIndex = 0;

    /* ============================================================
       HELPER: CALCULATE ITEM TOTALS (base, excl additional charges)
    ============================================================ */
    function calculateTotalAmount() {
      let total = 0;
      let total_wot = 0;

      $(".item-row").each(function () {
        if ($(this).find(".item-check").is(":checked")) {
          total += parseFloat($(this).find(".amount-input").val()) || 0;
          total_wot += parseFloat($(this).find(".amountwotx").val()) || 0;
        }
      });

      $("#total_amount").text(total.toFixed(3));
      $("#total_amount_wo_tax").text(total_wot.toFixed(3));
      $("#total_tax_amount").text((total - total_wot).toFixed(3));

      // Always refresh grand total after base total changes
      calculateTotalAmount_addt();
    }

    /* ============================================================
       HELPER: CALCULATE GRAND TOTAL (inc additional charges)
    ============================================================ */
    function calculateTotalAmount_addt() {
      let base_total = parseFloat($("#total_amount").text()) || 0;
      let base_wot = parseFloat($("#total_amount_wo_tax").text()) || 0;

      let addt_total = 0;
      let addt_wot = 0;

      $("#tb_addt_chrg_list tr").each(function () {
        if ($(this).find(".chk_vendor_quote_addtchrg_id").is(":checked")) {
          addt_total +=
            parseFloat($(this).find(".addt_charges_tot_amt").val()) || 0;
          addt_wot += parseFloat($(this).find(".addt_charges_amt").val()) || 0;
        }
      });

      let grand_total = base_total + addt_total;
      let grand_wot = base_wot + addt_wot;

      $("#total_amount_addt").text(grand_total.toFixed(3));
      $("#total_amount_wo_tax_addt").text(grand_wot.toFixed(3));
      $("#total_tax_amount_addt").text((grand_total - grand_wot).toFixed(3));
    }

    /* ============================================================
       HELPER: LOAD TERMS & CONDITIONS INTO CKEDITOR
    ============================================================ */
    function load_terms_and_conditions(companyId) {
      $.ajax({
        url: "<?php echo site_url('get-data'); ?>",
        type: "POST",
        dataType: "json",
        data: { tbl: "get-company-terms-and-conditions-list", id: companyId },
        success: function (res) {
          if (res && res.length > 0) {
            CKEDITOR.instances.editor2.setData(res[0].vendor_po_terms || "");
          } else {
            CKEDITOR.instances.editor2.setData("");
          }
        },
        error: function (xhr) {
          console.error("Terms load error:", xhr.responseText);
        },
      });
    }

    /* ============================================================
       HELPER: LOAD TENDER ENQUIRIES (by company + customer)
       Pass t_enq_id to pre-select a specific enquiry after load.
    ============================================================ */
    function load_tender_enq(t_enq_id) {
      const customer_id = $("#srch_customer_id").val();
      const company_id = $("#srch_company_id").val();
      const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $container = $("#item_container");

      // Reset downstream fields
      $enquiryDropdown
        .html('<option value="">Select Enquiry</option>')
        .prop("disabled", true);
      $("#srch_vendor_id").html('<option value="">Select Vendor</option>');
      $("#srch_vendor_quote_id").html("");
      $container.html("");
      rowIndex = 0;
      calculateTotalAmount();

      if (!customer_id || !company_id) return;

      $.ajax({
        url: "<?php echo site_url('vendor/get_tender_enquiries_by_customer'); ?>",
        type: "POST",
        dataType: "json",
        data: { company_id: company_id, customer_id: customer_id },
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
            // Pre-select if coming from autocomplete
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
          alert("Error loading enquiries.");
        },
      });
    }

    /* ============================================================
       CKEDITOR INIT
    ============================================================ */
    var editorConfig = {
      height: 120,
      extraPlugins: "justify,colorbutton,font,table",
    };

    CKEDITOR.replace("editor1", editorConfig);
    CKEDITOR.replace("editor2", editorConfig);

    CKEDITOR.instances.editor2.on("instanceReady", function () {
      let companyId = $("#srch_company_id").val();
      if (companyId) {
        load_terms_and_conditions(companyId);
      }
    });

    /* ============================================================
       AUTOCOMPLETE: Search Enquiry No
    ============================================================ */
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
        $("#srch_customer_id").val(ui.item.customer_id).trigger("change");
        // load_tender_enq is called inside the customer change handler,
        // but we also need to pre-select the specific enquiry id:
        load_tender_enq(ui.item.tender_enquiry_id);
      },
    });

    /* ============================================================
       CUSTOMER CHANGE → Load Tender Enquiries
    ============================================================ */
    $("#srch_customer_id").on("change", function () {
      load_tender_enq(""); // No pre-selection when user manually changes customer
    });

    /* ============================================================
       TENDER ENQUIRY CHANGE → Load Vendors
    ============================================================ */
    $("#srch_tender_enquiry_id").on("change", function () {
      const tender_id = $(this).val();
      const $vendor = $("#srch_vendor_id");

      // Reset vendor & quote dropdowns
      $vendor.html('<option value="">Select Vendor</option>');
      $("#srch_vendor_quote_id").html("");
      $("#item_container").html("");
      rowIndex = 0;
      calculateTotalAmount();

      if (!tender_id) return;

      $.post(
        "<?php echo site_url('vendor/get_vendor_list_quotation'); ?>",
        { srch_tender_enquiry_id: tender_id },
        function (res) {
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

    /* ============================================================
       VENDOR CHANGE → Load Quotation Nos + Contact Persons
    ============================================================ */
    $("#srch_vendor_id").on("change", function () {
      const vendor_id = $(this).val();
      const tender_enquiry_id = $("#srch_tender_enquiry_id").val();
      const $enquiry = $("#srch_vendor_quote_id");
      const $contact = $("#srch_vendor_contact_id");

      // Reset quote dropdown & items
      $enquiry.html("");
      $("#item_container").html("");
      rowIndex = 0;
      calculateTotalAmount();

      if (!vendor_id) return;

      // Load Vendor Quotation Nos
      $.post(
        "<?php echo site_url('vendor/get_data'); ?>",
        {
          tbl: "get-vendor-quotation-load-enquiry-no",
          id: vendor_id,
          tender_enquiry_id: tender_enquiry_id,
        },
        function (res) {
          //$enquiry.html('<option value="">Select Quotation</option>');
          if (res.length > 0) {
            $.each(res, function (i, row) {
              $enquiry.append(
                `<option value="${row.vendor_quote_id}">${row.vendor_quote_no}</option>`,
              );
            });
          }
          // Auto-trigger to load items for first quotation
          $("#srch_vendor_quote_id").trigger("change");
          $("#currency_id").trigger("change");
        },
        "json",
      );

      // Load Contact Persons
      $.post(
        "<?php echo site_url('vendor/get_data'); ?>",
        { tbl: "get-vendor-contacts", id: vendor_id },
        function (res) {
         // $contact.html('<option value="">Select Contact</option>');
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

    /* ============================================================
       VENDOR QUOTATION CHANGE → Load Items + Additional Charges
    ============================================================ */
    $("#srch_vendor_quote_id").on("change", function () {
      const srch_vendor_quote_id = $(this).val();
      const $container = $("#item_container");

      $container.html("");
      rowIndex = 0;

      if (!srch_vendor_quote_id) {
        calculateTotalAmount();
        return;
      }

      // --- Load Items ---
      $.ajax({
        url: "<?php echo site_url('vendor/get_vendor_quotation_data_load_po'); ?>",
        type: "POST",
        dataType: "json",
        data: { srch_vendor_quote_id: srch_vendor_quote_id },
        success: function (res) {
          console.log("Quotation data:", res);

          // Additional Charges
          if (res.addt_chrg_list && res.addt_chrg_list.length > 0) {
            $("#div_addt_chrg").removeClass("hide");
            let ctnt_html = "";
            $.each(res.addt_chrg_list, function (i, row) {
              ctnt_html += `
                        <tr>
                            <td>
                                <input type="checkbox"
                                    class="chk_vendor_quote_addtchrg_id"
                                    id="chk_vendor_quote_addtchrg_id${row.vendor_quote_addtchrg_id}"
                                    name="chk_vendor_quote_addtchrg_id[]"
                                    value="${row.vendor_quote_addtchrg_id}"
                                    checked>
                            </td>
                            <td>
                                <input type="hidden"
                                    class="form-control addt_charges_type_id"
                                    name="addt_charges_type_id[${row.vendor_quote_addtchrg_id}]"
                                    value="${row.addt_charges_type_id}">
                                ${row.addt_charges_type_name}
                            </td>
                            <td>
                                <input type="number" step="any"
                                    class="form-control addt_charges_amt"
                                    name="addt_charges_amt[${row.vendor_quote_addtchrg_id}]"
                                    value="${row.addt_charges_amt}">
                            </td>
                            <td>
                                <input type="number" step="any"
                                    class="form-control addt_charges_vat"
                                    name="addt_charges_vat[${row.vendor_quote_addtchrg_id}]"
                                    value="${row.addt_charges_vat}">
                            </td>
                            <td>
                                <input type="number" step="any"
                                    class="form-control addt_charges_vat_amt"
                                    name="addt_charges_vat_amt[${row.vendor_quote_addtchrg_id}]"
                                    value="${row.addt_charges_vat_amt}"
                                    readonly>
                            </td>
                            <td>
                                <input type="number" step="any"
                                    class="form-control addt_charges_tot_amt"
                                    name="addt_charges_tot_amt[${row.vendor_quote_addtchrg_id}]"
                                    value="${row.addt_charges_tot_amt}"
                                    readonly>
                            </td>
                        </tr>`;
            });
            $("#tb_addt_chrg_list").html(ctnt_html);
          } else {
            $("#div_addt_chrg").addClass("hide");
            $("#tb_addt_chrg_list").html("");
          }

          // Items
          if (!res.items || res.items.length === 0) {
            $container.html(`
                        <tr>
                            <td colspan="8" class="text-center text-danger">No items found</td>
                        </tr>`);
            calculateTotalAmount();
            return;
          }

          $.each(res.items, function (i, row) {
            let qty = parseFloat(row.qty) || 0;
            let rate = parseFloat(row.rate) || 0;
            let gst = parseFloat(row.vat) || 0;
            let wot = qty * rate;
            let total = wot + (wot * gst) / 100;

            const html = `
                    <tr class="item-row">
                        <td>
                            <input type="checkbox"
                                class="item-check"
                                name="selected_items[]"
                                value="${rowIndex}"
                                checked>
                            <input type="hidden" name="vendor_po_item_id[${rowIndex}]" value="">
                            <input type="hidden" name="vendor_rate_enquiry_id[${rowIndex}]" value="${row.vendor_rate_enquiry_id || ""}">
                            <input type="hidden" name="vendor_rate_enquiry_item_id[${rowIndex}]" value="${row.vendor_rate_enquiry_item_id || ""}">
                            <input type="hidden" name="vendor_quote_item_id[${rowIndex}]" value="${row.vendor_quote_item_id || ""}">
                        </td>
                        <td>
                            <input type="text" class="form-control"
                                name="item_code[${rowIndex}]"
                                value="${row.item_code || ""}" readonly>
                        </td>
                        <td>
                            <textarea class="form-control" name="item_desc[${rowIndex}]" readonly>${row.item_desc || ""}</textarea>
                        </td>
                        <td>
                            <input type="text" class="form-control"
                                name="uom[${rowIndex}]"
                                value="${row.uom || ""}" readonly>
                            <br>
                            <input type="number" class="form-control qty-input"
                                name="qty[${rowIndex}]"
                                value="${qty}" readonly>
                        </td>
                        <td>
                            <input type="number" step="0.001" class="form-control rate-input"
                                name="rate[${rowIndex}]"
                                value="${rate.toFixed(3)}">
                        </td>
                        <td>
                            <input type="number" class="form-control gst-input"
                                name="gst[${rowIndex}]"
                                value="${gst}">
                        </td>
                        <td>
                            <input type="number" class="form-control amountwotx"
                                name="amount_wo_tax[${rowIndex}]"
                                value="${wot.toFixed(3)}" readonly>
                        </td>
                        <td>
                            <input type="number" class="form-control amount-input"
                                name="amount[${rowIndex}]"
                                value="${total.toFixed(3)}" readonly>
                        </td>
                    </tr>`;

            $container.append(html);
            rowIndex++;
          });

          calculateTotalAmount();
          calculateTotalAmount_addt();
        },
        error: function (xhr) {
          console.error("Error loading quotation items:", xhr.responseText);
        },
      });

      // --- Load Currency ---
      $.ajax({
        url: "<?php echo site_url('vendor/get_vendor_quotation_currency_id'); ?>",
        type: "POST",
        dataType: "json",
        data: { srch_vendor_quote_id: srch_vendor_quote_id },
        success: function (res) {
          $("#currency_id")
            .val(res.length ? res[0].currency_id : "")
            .trigger("change");
        },
      });
    });

    /* ============================================================
       ITEM ROW: Rate / GST / Qty change → recalculate row amount
    ============================================================ */
    $(document).on(
      "input change",
      ".qty-input, .rate-input, .gst-input",
      function () {
        let $row = $(this).closest(".item-row");
        let qty = parseFloat($row.find(".qty-input").val()) || 0;
        let rate = parseFloat($row.find(".rate-input").val()) || 0;
        let gst = parseFloat($row.find(".gst-input").val()) || 0;

        let wot = qty * rate;
        let tax = (wot * gst) / 100;
        let total = wot + tax;

        $row.find(".amountwotx").val(wot.toFixed(3));
        $row.find(".amount-input").val(total.toFixed(3));

        calculateTotalAmount();
      },
    );

    /* ============================================================
       ITEM CHECKBOX → recalculate totals
    ============================================================ */
    $(document).on("change", ".item-check", function () {
      calculateTotalAmount();
    });

    /* SELECT ALL checkbox */
    $(document).on("change", "#selectall", function () {
      $(".item-check").prop("checked", this.checked);
      calculateTotalAmount();
    });

    /* ============================================================
       ADDITIONAL CHARGES: Amt / VAT change → recalculate row
    ============================================================ */
    $(document).on(
      "input change",
      ".addt_charges_amt, .addt_charges_vat",
      function () {
        let $row = $(this).closest("tr");
        let amt = parseFloat($row.find(".addt_charges_amt").val()) || 0;
        let vat = parseFloat($row.find(".addt_charges_vat").val()) || 0;
        let vat_amt = (amt * vat) / 100;
        let total = amt + vat_amt;

        $row.find(".addt_charges_vat_amt").val(vat_amt.toFixed(3));
        $row.find(".addt_charges_tot_amt").val(total.toFixed(3));

        calculateTotalAmount_addt();
      },
    );

    /* ============================================================
       ADDITIONAL CHARGES CHECKBOX → enable/disable fields + recalc
    ============================================================ */
    $(document).on("change", ".chk_vendor_quote_addtchrg_id", function () {
      let $row = $(this).closest("tr");

      if ($(this).is(":checked")) {
        $row
          .find(".addt_charges_amt, .addt_charges_vat")
          .prop("disabled", false);
      } else {
        $row
          .find(".addt_charges_amt, .addt_charges_vat")
          .prop("disabled", true);
      }

      calculateTotalAmount_addt();
    });
  }); // END $(document).ready()
</script>
