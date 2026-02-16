<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
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
              `<option value="${row.customer_id}">${row.customer_name}</option>`,
            );
          });
        },
        "json",
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
      const vendor_id = $(this).val();
      const $enquiry = $("#srch_vendor_quote_id");
      const $contact = $("#srch_vendor_contact_id");

      $enquiry.html('<option value="">Select Enquiry No</option>');
      $contact.html('<option value="">Select Contact</option>');

      if (!vendor_id) return;

      // Load Rate Enquiry Nos
      $.post(
        "<?php echo site_url('vendor/get_data'); ?>",
        {
          tbl: "get-vendor-quotation-load-enquiry-no",
          id: vendor_id,
        },
        function (res) {
          if (res.length > 0) {
            $.each(res, function (i, row) {
              $enquiry.append(
                `<option value="${row.vendor_quote_id}">${row.vendor_quote_no}</option>`,
              );
            });
          }
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
    // ===============================
    // LOAD ITEMS FROM QUOTATION (ADD MODE)
    // ===============================
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
                            <td colspan="8" class="text-center text-danger">
                                No items found for this quotation.
                            </td>
                        </tr>
                    `);
            return;
          }

          $.each(res, function (i, row) {
            const rowHtml = `
                    <tr class="item-row">
                        <td>
                            <input type="checkbox" class="form-check-input item-check" checked>
                        </td>

                        <td>
                            <input type="text" name="item_code[]" class="form-control"
                                   value="${row.item_code || ""}" readonly>

                            <input type="hidden" name="vendor_rate_enquiry_item_id[]"
                                   value="${row.vendor_rate_enquiry_item_id}">
                        </td>

                        <td>
                            <textarea name="item_desc[]" class="form-control" rows="2">${row.item_desc || ""}</textarea>
                        </td>

                        <td>
                            <input type="text" name="uom[]" class="form-control"
                                   value="${row.uom || ""}">
                            <br>
                            <input type="number" step="any"
                                   class="form-control qty-input"
                                   name="qty[]" value="${row.qty || 0}">
                        </td>

                        <td>
                            <input type="number" step="any"
                                   class="form-control rate-input"
                                   name="rate[]" value="${row.rate || 0}">
                        </td>

                        <td>
                            <input type="number" step="any"
                                   class="form-control vat"
                                   name="gst[]" value="${row.gst || 0}">
                        </td>

                        <td>
                            <input type="number" class="form-control amountwotx"
                                   name="amount_wo_tax[]" readonly>
                        </td>

                        <td>
                            <input type="number" class="form-control amount"
                                   name="amount[]" readonly>
                        </td>
                    </tr>
                    `;

            $container.append(rowHtml);
          });

          recalculateAll();
        },

        error: function () {
          alert("Error loading quotation items");
        },
      });
    });

    // ===============================
    // ROW CALCULATION
    // ===============================
    function calculateRow($row) {
      const checked = $row.find(".item-check").is(":checked");

      if (!checked) {
        $row.find(".amountwotx").val("0.000");
        $row.find(".amount").val("0.000");
        return;
      }

      const qty = parseFloat($row.find(".qty-input").val()) || 0;
      const rate = parseFloat($row.find(".rate-input").val()) || 0;
      const vat = parseFloat($row.find(".vat").val()) || 0;

      const woTax = qty * rate;
      const withTax = woTax + (woTax * vat) / 100;

      $row.find(".amountwotx").val(woTax.toFixed(3));
      $row.find(".amount").val(withTax.toFixed(3));
    }

    // ===============================
    // TOTAL CALCULATION
    // ===============================
    function calculateTotalAmount() {
      let total = 0;
      let totalWOT = 0;

      $(".item-row").each(function () {
        const $row = $(this);

        if ($row.find(".item-check").is(":checked")) {
          total += parseFloat($row.find(".amount").val()) || 0;
          totalWOT += parseFloat($row.find(".amountwotx").val()) || 0;
        }
      });

      $("#total_amount").text(total.toFixed(3));
      $("#total_amount_wo_tax").text(totalWOT.toFixed(3));
    }

    // ===============================
    // RECALCULATE ALL ROWS
    // ===============================
    function recalculateAll() {
      $(".item-row").each(function () {
        calculateRow($(this));
      });
      calculateTotalAmount();
    }

    // ===============================
    // INPUT CHANGE EVENTS
    // ===============================
    $(document).on(
      "input change",
      ".qty-input, .rate-input, .vat",
      function () {
        const $row = $(this).closest(".item-row");
        calculateRow($row);
        calculateTotalAmount();
      },
    );

    // ===============================
    // CHECKBOX CHANGE
    // ===============================
    $(document).on("change", ".item-check", function () {
      const $row = $(this).closest(".item-row");

      calculateRow($row);
      calculateTotalAmount();

      $("#selectall").prop(
        "checked",
        $(".item-check:checked").length === $(".item-check").length,
      );
    });

    // ===============================
    // SELECT ALL
    // ===============================
    $("#selectall").on("change", function () {
      $(".item-check").prop("checked", this.checked);
      recalculateAll();
    });

    // ===============================
    // EDIT MODE INITIAL LOAD
    // ===============================
    recalculateAll();
  });
</script>

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
