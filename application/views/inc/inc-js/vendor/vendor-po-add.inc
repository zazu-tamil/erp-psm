<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
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
        console.log(ui);
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
        { },
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
  });

  // CKEditor
  window.onload = function () {
    CKEDITOR.replace("editor1", { height: 120 });
    CKEDITOR.replace("editor2", { height: 120 });
  };
</script>
<script>
  $(document).ready(function () {
    // =====================================
    // LOAD QUOTATION ITEMS
    // =====================================
    $("#srch_vendor_quote_id").on("change", function () {
      const srch_vendor_quote_id = $(this).val();
      const $container = $("#item_container");
      $container.html("");

      if (!srch_vendor_quote_id) {
        calculateTotalAmount();
        return;
      }

      $.ajax({
        url: "<?php echo site_url('vendor/get_vendor_quotation_data_load_po'); ?>",
        type: "POST",
        data: { srch_vendor_quote_id: srch_vendor_quote_id },
        dataType: "json",
        success: function (res) {
          if (!res || res.length === 0) {
            $container.html(`
                        <tr>
                            <td colspan="10" class="text-center text-danger">
                                No items found for this quotation.
                            </td>
                        </tr>
                    `);
            calculateTotalAmount();
            return;
          }

          $.each(res, function (i, row) {
            let qty = parseFloat(row.qty) || 0;
            let rate = parseFloat(row.rate) || 0;
            let gst = parseFloat(row.vat) || 0;

            let amountWithoutTax = qty * rate;
            let amountWithTax =
              amountWithoutTax + (amountWithoutTax * gst) / 100;

            const rowHtml = `
                    <tr class="item-row">

                        <td>
                            <input type="checkbox"
                                   class="item-check"
                                   checked name="selected_items[]">
                        </td>

                        <td>
                            <input type="text" name="item_code[]" class="form-control"
                                   value="${row.item_code || ""}" readonly>

                            <input type="hidden"
                                   name="vendor_rate_enquiry_id"
                                   value="${row.vendor_rate_enquiry_id || ""}">
                            <input type="hidden"
                                   name="vendor_rate_enquiry_item_id[]"
                                   value="${row.vendor_rate_enquiry_item_id || ""}">
                        </td>

                        <td>
                            <textarea name="item_desc[]" class="form-control"
                                      rows="2" readonly>${row.item_desc || ""}</textarea>
                        </td>

                        <td>
                            <input type="text" name="uom[]" class="form-control"
                                   value="${row.uom || ""}" readonly>
                            <br>
                            <input type="number" step="any"
                                   name="qty[]"
                                   class="form-control qty-input"
                                   value="${qty}" readonly>
                        </td>

                        <td>
                            <input type="number"
                                   step="0.001"
                                   name="rate[]"
                                   class="form-control rate-input"
                                   value="${rate.toFixed(3)}">
                        </td>

                        <td>
                            <input type="number"
                                   step="any"
                                   name="gst[]"
                                   class="form-control gst-input"
                                   value="${gst}">
                        </td>

                        <td>
                            <input type="number"
                                   name="amountwotx[]"
                                   class="form-control amountwotx"
                                   value="${amountWithoutTax.toFixed(3)}"
                                   readonly>
                        </td>

                        <td>
                            <input type="number"
                                   name="amount[]"
                                   class="form-control amount-input"
                                   value="${amountWithTax.toFixed(3)}"
                                   readonly>
                        </td>

                    </tr>`;
            $container.append(rowHtml);
          });

          calculateTotalAmount();
        },
      });

      // Load currency
      $.ajax({
        url: "<?php echo site_url('vendor/get_vendor_quotation_currency_id'); ?>",
        type: "POST",
        data: { srch_vendor_quote_id: srch_vendor_quote_id },
        dataType: "json",
        success: function (res) {
          if (res.length > 0) {
            $("#currency_id").val(res[0].currency_id).trigger("change");
          } else {
            $("#currency_id").val("");
          }
        },
      });
    });

    // =====================================
    // ROW CALCULATION (LIVE UPDATE)
    // =====================================
    $(document).on(
      "input change",
      ".rate-input, .gst-input, .qty-input, .item-check",
      function () {
        const $row = $(this).closest(".item-row");

        const qty = parseFloat($row.find(".qty-input").val()) || 0;
        const rate = parseFloat($row.find(".rate-input").val()) || 0;
        const gst = parseFloat($row.find(".gst-input").val()) || 0;

        const amountWithoutTax = qty * rate;
        const amountWithTax = amountWithoutTax + (amountWithoutTax * gst) / 100;

        $row.find(".amountwotx").val(amountWithoutTax.toFixed(3));
        $row.find(".amount-input").val(amountWithTax.toFixed(3));

        calculateTotalAmount();
      },
    );

    // =====================================
    // TOTAL CALCULATION
    // =====================================
    function calculateTotalAmount() {
      let total = 0;
      let totalWOT = 0;

      $(".item-row").each(function () {
        if ($(this).find(".item-check").is(":checked")) {
          total += parseFloat($(this).find(".amount-input").val()) || 0;
          totalWOT += parseFloat($(this).find(".amountwotx").val()) || 0;
        }
      });

      $("#total_amount").text(total.toFixed(3));
      $("#total_amount_wo_tax").text(totalWOT.toFixed(3));
    }
  });
</script>

<script>
  $("#srch_vendor_id").on("change", function () {
    const vendor_id = $(this).val();
    const $enquiry = $("#srch_vendor_quote_id");

    const tender_enquiry_id = $("#srch_tender_enquiry_id").val();

    const $contact = $("#srch_vendor_contact_id");

    if (!vendor_id) return;

    // Load Rate Enquiry Nos
    $.post(
      "<?php echo site_url('vendor/get_data'); ?>",
      {
        tbl: "get-vendor-quotation-load-enquiry-no",
        id: vendor_id,
        tender_enquiry_id: tender_enquiry_id,
      },
      function (res) {
        if (res.length > 0) {
          $.each(res, function (i, row) {
            $enquiry.append(
              `<option value="${row.vendor_quote_id}">${row.vendor_quote_no}</option>`,
            );
          });
        }
        $("#srch_vendor_quote_id").trigger("change");
        $("#currency_id").trigger("change");
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
</script>
