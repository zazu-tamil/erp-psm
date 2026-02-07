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
                })
              );
            });

            // ✅ SET SELECTED VALUE HERE
            if (t_enq_id) {
              $enquiryDropdown.val(t_enq_id).trigger("change");
            }
          } else {
            $enquiryDropdown.html(
              '<option value="">No enquiries found</option>'
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
    /* $("#srch_company_id").on("change", function () {
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
    }); */

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
        "<?php echo site_url('vendor/get_vendor_list_purchase_inward'); ?>",
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
      const $enquiry = $("#srch_vendor_po_id");
      const $contact = $("#srch_vendor_contact_id");

      $enquiry.html('<option value="">Select Enquiry No</option>');
      $contact.html('<option value="">Select Contact</option>');

      if (!vendor_id) return;

      // Load Rate Enquiry Nos
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

  // CKEditor
  window.onload = function () {
    CKEDITOR.replace("editor1", { height: 120 });
    CKEDITOR.replace("editor2", { height: 120 });
  };
</script>
<script>
  $(function () {
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
  });
</script>

<script>
  $(document).ready(function () {
    $("#srch_vendor_po_id").on("change", function () {
      const srch_vendor_po_id = $(this).val();
      const $container = $("#item_container");
      $container.empty();

      if (!srch_vendor_po_id) return;

      $.ajax({
        url: "<?= site_url('vendor/get_vendor_pur_inward_item_load'); ?>",
        type: "POST",
        data: { srch_vendor_po_id: srch_vendor_po_id },
        dataType: "json",

        success: function (res) {
           console.log(res);
          if (!res || res.length === 0) {
            $container.html(`
                        <tr>
                            <td colspan="7" class="text-center text-danger">
                                No items found
                            </td>
                        </tr>
                    `);
            return;
          }

          $.each(res, function (i, row) {
            console.log(row);
            const html = `
                        <tr class="item-row">

                            <td>
                                <input type="checkbox"
                                       class="item-check"
                                       name="selected_items[]"
                                       value="${i}">
                            </td>

                            <td>
                                <input type="text" class="form-control" name="item_code"
                                       value="${row.item_code || ""}" readonly>
                                <input type="hidden" name="vendor_po_item_id[]" value="${
                                  row.vendor_po_item_id
                                }">
                               
                            </td>

                            <td>
                                <textarea class="form-control" rows="2"
                                          name="item_desc[]" readonly>${
                                            row.item_desc || ""
                                          }</textarea>
                            </td>

                            <td>
                                <input type="text" name="uom[]" class="form-control"
                                       value="${row.uom || ""}" readonly>
                                <input type="number" step="any"
                                       name="qty[]"
                                       class="form-control qty-input"
                                       value="${row.qty}" readonly>
                            </td>

                            <td>
                                <input type="number" step="any"
                                       name="rate[]"
                                       class="form-control rate-input"
                                       value="${row.rate}">
                            </td>

                            <td>
                                <input type="number" step="any"
                                       name="gst[]"
                                       class="form-control gst-input"
                                       value="${row.gst}">
                                <input type="hidden"
                                       name="gst_amount[]"
                                       class="gst-amount-input">
                            </td>

                            <td>
                                <input type="number" step="any"
                                       name="amount[]"
                                       class="form-control amount-input"
                                       readonly>
                            </td>
                        </tr>
                    `;

            const $row = $(html);
            $container.append($row);
            calculateRow($row);
          });

          calculateTotals();
        },

        error: function () {
          alert("Error loading items");
        },
      });
    });

    $(document).on(
      "input change",
      ".rate-input, .gst-input, .qty-input, .item-check",
      function () {
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
  });
  $("form").on("submit", function () {
    if ($(".item-check:checked").length === 0) {
      alert("Please select at least one item");
      return false;
    }
  });
</script>
