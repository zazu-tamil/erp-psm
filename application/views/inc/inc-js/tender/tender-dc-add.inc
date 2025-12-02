<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>

<script>
  $(document).ready(function () {
    $("#srch_company_id").on("change", function () {
      const company_id = $(this).val();
      const $customerDropdown = $("#srch_customer_id");
      const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $container = $("#item_container");

      // Reset dependent dropdowns
      $enquiryDropdown
        .html('<option value="">Select Enquiry</option>')
        .prop("disabled", true);
      $container.html("");

      if (!company_id) {
        $customerDropdown
          .html('<option value="">Select Customer</option>')
          .prop("disabled", false);
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
                $("<option></option>")
                  .attr("value", row.customer_id)
                  .text(row.customer_name)
              );
            });
            $customerDropdown.prop("disabled", false);
          } else {
            $customerDropdown.html(
              '<option value="">No customers found</option>'
            );
          }
        },
        error: function () {
          alert("Error loading customers");
        },
      });
    });
    $("#srch_customer_id").on("change", function () {
      const customer_id = $(this).val();
      const company_id = $("#srch_company_id").val();
      const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $container = $("#item_container");

      // Reset dependent dropdown and items
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
                $("<option></option>")
                  .attr("value", row.tender_enquiry_id)
                  .text(row.display)
              );
            });
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
    });

    $("#srch_tender_enquiry_id").on("change", function () {
      const tender_enquiry_id = $(this).val();
      const $container = $("#item_container");
      $container.html("");

      if (!tender_enquiry_id) return;

      $.ajax({
        url: "<?php echo site_url('tender/get_data'); ?>",
        type: "POST",
        data: {
          tbl: "get-tender-pur-invoice-item-list-load",
          id: tender_enquiry_id,
        },
        dataType: "json",
        success: function (res) {
          let html = "";

            if (res.length > 0) {
              $.each(res, function (i, row) {
                const hiddenFields = `
                    <input type="hidden" name="category_id[]" value="${row.category_id}">
                    <input type="hidden" name="vendor_pur_inward_item_id[]" value="${row.vendor_pur_inward_item_id}">
                    <input type="hidden" name="vendor_pur_inward_id[]" value="${row.vendor_pur_inward_id}">
                    <input type="hidden" name="item_id[]" value="${row.item_id}">
                  `;

                const formattedDate = new Date(row.inward_date)
                  .toLocaleDateString("en-GB")
                  .replace(/\//g, "-");

                html += `
                    
                  <tr>
                    <td>
                      <input type="checkbox" class="form-check-input item-check" 
                        name="selected_items[]" value="${i}">
                        <small>Item ${i + 1}</small>
                    </td>

                    <td>
                      ${row.vendor_name}<br>
                      ${formattedDate}<br>
                      ${row.inward_no}
                    </td>

                    <td>
                      <input type="text" value="${row.item_code}" class="form-control mb-2" readonly>
                      <textarea name="item_desc[]" class="form-control desc-textarea" rows="2">${row.item_desc}</textarea>
                    </td>

                    <td>
                      <input type="text" name="uom[]" class="form-control mb-2" value="${row.uom}" readonly>
                      <input type="number" name="avail_qty[]" step="0.01" 
                        class="form-control" value="${row.avail_qty}" readonly>
                    </td>

                    <td>
                      <label class="form-label">Enter Delivery Qty</label>
                      <input type="number" name="dc_qty[]" step="0.01" class="form-control dc_qty-input">
                    </td>

                    ${hiddenFields}
                  </tr>
                `;
                      });

                      $container.html(html);
                    } else {
                      $container.html(`
                <div class="alert alert-warning">No items found for this tender enquiry.</div>
              `);
          }
        },

        error: function () {
          alert("Error loading items");
        },
      });
    });

    $(document).on("change", ".item-check", function () {
      const $card = $(this).closest(".item-card");
      if (!$(this).is(":checked")) {
        $card
          .find(
            "input[type=hidden][name^='vendor_pur_inward_id'], " +
            "input[type=hidden][name^='vendor_pur_inward_item_id'], " +
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
