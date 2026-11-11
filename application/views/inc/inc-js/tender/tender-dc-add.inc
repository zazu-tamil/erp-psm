<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
<link
  rel="stylesheet"
  href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"
/>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

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

    /*
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
    */

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

    $("#srch_customer_id1").on("change", function () {
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
                  .text(row.display),
              );
            });
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
    });

    function load_tender_po(tender_enquiry_id) {
      //alert(tender_enquiry_id);
      $("#tender_po_id > option").remove();

      $.ajax({
        url: "<?php echo site_url('tender/get_data');?>",
        type: "post",
        data: { tbl: "get-tender-po-list", id: tender_enquiry_id },
        success: function (res) {
          console.log(res);

          /* var opt1 = $("<option />");
            opt1.val("");
            opt1.text("Select");
            $("#tender_po_id").append(opt1);*/

          $.each(res, function (key, info) {
            var opt1 = $("<option />");
            opt1.val(info.tender_po_id);
            opt1.text(info.customer_po_no + " [ " + info.po_date + " ]");
            $("#tender_po_id").append(opt1);
          });
        },
      });
    }

    let currentItemIndex = 0;
    let cachedPoItems = [];
    let cachedStockItems = [];

    function escapeHtml(text) {
      if (!text) return '';
      return $('<div>').text(text).html();
    }

    $("#srch_tender_enquiry_id").on("change", function () {
      const tender_enquiry_id = $(this).val();
      const $container = $("#item_container");
      $container.html("");
      currentItemIndex = 0;

      if (!tender_enquiry_id) return;

      load_tender_po(tender_enquiry_id);

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

          if (res && res.length > 0) {
            currentItemIndex = res.length;
            $.each(res, function (i, row) {
              const formattedDate = row.inward_date 
                ? new Date(row.inward_date).toLocaleDateString("en-GB").replace(/\//g, "-") 
                : "";

              html += `
                  <tr>
                    <td>
                      <input type="checkbox" class="form-check-input item-check" 
                        name="selected_items[]" value="${i}">
                      <small>Item ${i + 1}</small>
                      <input type="hidden" name="tender_dc_item_id[${i}]" value="">
                      <input type="hidden" name="category_id[${i}]" value="${row.category_id || ''}">
                      <input type="hidden" name="item_id[${i}]" value="${row.item_id || ''}">
                      <input type="hidden" name="vendor_pur_inward_item_id[${i}]" value="${row.vendor_pur_inward_item_id || ''}">
                      <input type="hidden" name="vendor_pur_inward_id[${i}]" value="${row.vendor_pur_inward_id || ''}">
                      <input type="hidden" name="in_stock_item_id[${i}]" value="">
                      <input type="hidden" name="stock_source[${i}]" value="Inward">
                      <input type="hidden" name="source_tender_enquiry_id[${i}]" value="${tender_enquiry_id}">
                      <input type="hidden" name="tender_po_item_id[${i}]" value="">
                    </td>

                    <td>
                      ${row.vendor_name || ''}<br>
                      ${formattedDate}<br>
                      <strong>${row.inward_no || ''}</strong>
                    </td>

                    <td>
                      <input type="text" name="item_code[${i}]" value="${escapeHtml(row.item_code || '')}" class="form-control mb-2" readonly>
                      <textarea name="item_desc[${i}]" class="form-control desc-textarea" rows="2">${escapeHtml(row.item_desc || '')}</textarea>
                    </td>

                    <td>
                      <input type="text" name="uom[${i}]" class="form-control mb-2" value="${escapeHtml(row.uom || '')}" readonly>
                      <input type="number" name="avail_qty[${i}]" step="any" 
                        class="form-control" value="${row.avail_qty || 0}" readonly>
                    </td>

                    <td>
                      <label class="form-label">Delivery Qty</label>
                      <input type="number" step="any" name="dc_qty[${i}]" class="form-control dc_qty-input">
                    </td>
                  </tr>
                `;
            });

            $container.html(html);
          } else {
            $container.html(`
                <tr><td colspan="5" class="text-center text-muted" style="padding: 15px;">No inward items found for this tender enquiry. You can still add items using "Add From Warehouse Stock" above.</td></tr>
              `);
          }
        },

        error: function () {
          alert("Error loading items");
        },
      });
    });

    /* ==================== STOCK PICKER MODAL LOGIC ==================== */
    $("#btn_open_stock_modal").on("click", function () {
      const poId = $("#tender_po_id").val();
      if (!poId) {
        alert("Please select a Customer PO first to map warehouse stock items.");
        $("#tender_po_id").focus();
        return;
      }

      $("#modal_instock_picker").modal("show");
      $("#instock_modal_tbody").html('<tr><td colspan="7" class="text-center" style="padding: 25px;"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Loading available stock items...</td></tr>');
      $("#stock_count_info").text("Loading...");

      // 1. Fetch Customer PO items for mapping
      $.ajax({
        url: "<?php echo site_url('tender/get_tender_po_items_ajax'); ?>",
        type: "POST",
        data: { tender_po_id: poId },
        dataType: "json",
        success: function (poItems) {
          cachedPoItems = poItems || [];
          loadAvailableStock($("#srch_stock_kw").val());
        },
        error: function () {
          alert("Error loading Customer PO items.");
        }
      });
    });

    function loadAvailableStock(kw) {
      $.ajax({
        url: "<?php echo site_url('tender/get_available_instock_items_ajax'); ?>",
        type: "POST",
        data: {
          search: kw,
          current_dc_id: 0
        },
        dataType: "json",
        success: function (stockItems) {
          cachedStockItems = stockItems || [];
          renderStockModalRows(cachedStockItems);
        },
        error: function () {
          $("#instock_modal_tbody").html('<tr><td colspan="7" class="text-center text-danger">Error loading stock items.</td></tr>');
        }
      });
    }

    $("#btn_search_stock").on("click", function () {
      loadAvailableStock($("#srch_stock_kw").val());
    });

    $("#srch_stock_kw").on("keypress", function (e) {
      if (e.which === 13) {
        e.preventDefault();
        loadAvailableStock($(this).val());
      }
    });

    function renderStockModalRows(stockItems) {
      if (!stockItems || stockItems.length === 0) {
        $("#instock_modal_tbody").html('<tr><td colspan="7" class="text-center text-muted" style="padding: 25px;">No available stock items found matching your criteria.</td></tr>');
        $("#stock_count_info").text("0 items found");
        return;
      }

      $("#stock_count_info").text(stockItems.length + " item(s) available in stock");

      let html = "";
      $.each(stockItems, function (idx, item) {
        let poOptions = '<option value="">-- Select Customer PO Line --</option>';
        let autoMatched = false;
        const cleanItemCode = (item.item_code || "").trim().toLowerCase();

        $.each(cachedPoItems, function (pIdx, po) {
          const cleanPoCode = (po.item_code || "").trim().toLowerCase();
          let selected = "";
          if (!autoMatched && cleanItemCode !== "" && cleanItemCode === cleanPoCode) {
            selected = "selected";
            autoMatched = true;
          }
          poOptions += `<option value="${po.tender_po_item_id}" data-item-code="${escapeHtml(po.item_code || '')}" data-item-desc="${escapeHtml(po.item_desc || '')}" ${selected}>#${po.serial_no || (pIdx + 1)} - ${escapeHtml(po.item_code || '')} (PO Qty: ${po.qty})</option>`;
        });

        const isManual = (item.stock_source === "Manual Stock");
        const badgeHtml = isManual 
          ? '<span class="label label-warning" style="font-size: 10px;"><i class="fa fa-cubes"></i> Direct Stock</span>' 
          : '<span class="label label-info" style="font-size: 10px;"><i class="fa fa-exchange"></i> Inward Surplus</span>';

        const sourceInfo = isManual
          ? `${badgeHtml}<br><strong>${escapeHtml(item.vendor_name || 'Warehouse Stock')}</strong>`
          : `${badgeHtml}<br><strong>${escapeHtml(item.inward_no || '')}</strong><br><small class="text-muted">${escapeHtml(item.vendor_name || '')}</small><br><small class="text-muted" style="font-size: 11px;">${escapeHtml(item.tender_details || '')}</small>`;

        html += `
          <tr data-stock-idx="${idx}">
            <td class="text-center" style="vertical-align: middle;">
              <input type="checkbox" class="stock-modal-chk" data-stock-idx="${idx}">
            </td>
            <td>${sourceInfo}</td>
            <td>
              <strong>${escapeHtml(item.item_code || '')}</strong><br>
              <small style="color: #555;">${escapeHtml(item.item_desc || '')}</small>
            </td>
            <td class="text-center" style="vertical-align: middle;">${escapeHtml(item.uom || '')}</td>
            <td class="text-right" style="vertical-align: middle;">
              <span class="badge bg-green" style="font-size: 12px;">${item.avail_qty}</span>
            </td>
            <td>
              <select class="form-control stock-modal-po-select" data-stock-idx="${idx}" style="font-size: 12px; width: 100%;">
                ${poOptions}
              </select>
            </td>
            <td class="text-right">
              <input type="number" step="any" min="0.01" max="${item.avail_qty}" value="${item.avail_qty}" 
                class="form-control stock-modal-qty" data-stock-idx="${idx}" style="text-align: right; width: 100%;">
            </td>
          </tr>
        `;
      });

      $("#instock_modal_tbody").html(html);
    }

    $("#btn_confirm_add_stock").on("click", function () {
      const $checked = $(".stock-modal-chk:checked");
      if ($checked.length === 0) {
        alert("Please select at least one stock item to add.");
        return;
      }

      let validationFailed = false;
      let itemsToAdd = [];

      $checked.each(function () {
        if (validationFailed) return;
        const $row = $(this).closest("tr");
        const idx = $(this).data("stock-idx");
        const stockItem = cachedStockItems[idx];
        const $poSelect = $row.find(".stock-modal-po-select");
        const poItemId = $poSelect.val();
        const poText = $poSelect.find("option:selected").text();
        const qtyVal = parseFloat($row.find(".stock-modal-qty").val());

        if (!poItemId) {
          alert("Please select a Customer PO line item for: " + (stockItem.item_code || "Stock Item"));
          $poSelect.focus();
          validationFailed = true;
          return false;
        }

        if (isNaN(qtyVal) || qtyVal <= 0) {
          alert("Please enter a valid DN quantity for: " + (stockItem.item_code || "Stock Item"));
          $row.find(".stock-modal-qty").focus();
          validationFailed = true;
          return false;
        }

        if (qtyVal > parseFloat(stockItem.avail_qty)) {
          alert("DN quantity (" + qtyVal + ") cannot exceed available quantity (" + stockItem.avail_qty + ") for: " + (stockItem.item_code || "Stock Item"));
          $row.find(".stock-modal-qty").focus();
          validationFailed = true;
          return false;
        }

        itemsToAdd.push({
          stockItem: stockItem,
          poItemId: poItemId,
          poText: poText,
          qty: qtyVal
        });
      });

      if (validationFailed) return;

      // Clear empty placeholder in item_container if any
      if ($("#item_container tr td[colspan]").length > 0) {
        $("#item_container").empty();
      }

      $.each(itemsToAdd, function (i, data) {
        const rowIdx = currentItemIndex++;
        const item = data.stockItem;
        const isManual = (item.stock_source === "Manual Stock");
        const badgeHtml = isManual
          ? '<span class="label label-warning" style="font-size: 10px;"><i class="fa fa-cubes"></i> Direct Stock</span>'
          : '<span class="label label-info" style="font-size: 10px;"><i class="fa fa-exchange"></i> Stock Transfer</span>';

        const sourceInfo = isManual
          ? `<strong>${escapeHtml(item.vendor_name || 'Warehouse Direct Stock')}</strong><br>${badgeHtml}`
          : `${escapeHtml(item.vendor_name || '')}<br><small class="text-muted"><i class="fa fa-calendar"></i> ${item.inward_date || ''}</small><br><strong>${escapeHtml(item.inward_no || '')}</strong><br>${badgeHtml}`;

        const newRowHtml = `
          <tr class="stock-item-row" style="background-color: #fbfbf0;">
            <td>
              <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="${rowIdx}" checked>
              <br>
              <button type="button" class="btn btn-xs btn-danger btn-remove-stock-row" title="Remove" style="margin-top: 4px;">
                <i class="fa fa-trash"></i>
              </button>
              <input type="hidden" name="tender_dc_item_id[${rowIdx}]" value="">
              <input type="hidden" name="vendor_pur_inward_id[${rowIdx}]" value="${item.vendor_pur_inward_id || 0}">
              <input type="hidden" name="vendor_pur_inward_item_id[${rowIdx}]" value="${item.vendor_pur_inward_item_id || 0}">
              <input type="hidden" name="in_stock_item_id[${rowIdx}]" value="${item.in_stock_item_id || ''}">
              <input type="hidden" name="stock_source[${rowIdx}]" value="${item.stock_source || 'Inward'}">
              <input type="hidden" name="source_tender_enquiry_id[${rowIdx}]" value="${item.source_tender_enquiry_id || ''}">
              <input type="hidden" name="tender_po_item_id[${rowIdx}]" value="${data.poItemId}">
              <input type="hidden" name="category_id[${rowIdx}]" value="${item.category_id || ''}">
              <input type="hidden" name="item_id[${rowIdx}]" value="${item.item_id || ''}">
            </td>
            <td>${sourceInfo}</td>
            <td>
              <input type="text" name="item_code[${rowIdx}]" value="${escapeHtml(item.item_code || '')}" class="form-control mb-2" readonly>
              <textarea name="item_desc[${rowIdx}]" class="form-control desc-textarea" rows="2">${escapeHtml(item.item_desc || '')}</textarea>
              <div style="margin-top: 4px; font-size: 11px; color: #3c8dbc; font-weight: 600;">
                <i class="fa fa-link"></i> Mapped: ${escapeHtml(data.poText)}
              </div>
            </td>
            <td>
              <input type="text" name="uom[${rowIdx}]" class="form-control mb-2" value="${escapeHtml(item.uom || '')}" readonly>
              <input type="number" step="any" name="avail_qty[${rowIdx}]" class="form-control" value="${item.avail_qty}" readonly>
            </td>
            <td>
              <label class="form-label">Delivery Qty</label>
              <input type="number" step="any" min="0.01" max="${item.avail_qty}" name="dc_qty[${rowIdx}]" value="${data.qty}" class="form-control dc_qty-input" required>
            </td>
          </tr>
        `;

        $("#item_container").append(newRowHtml);
      });

      $("#modal_instock_picker").modal("hide");
    });

    $(document).on("click", ".btn-remove-stock-row", function () {
      $(this).closest("tr").remove();
      if ($("#item_container tr").length === 0) {
        $("#item_container").html('<tr><td colspan="5" class="text-center">No items added yet.</td></tr>');
      }
    });
  });
</script>

<script>
  $(document).ready(function () {
    var editorConfig = {
      height: 120,
      extraPlugins: "justify,colorbutton,font,table",
    };

    CKEDITOR.replace("editor1", editorConfig);
    CKEDITOR.replace("editor2", editorConfig);

    CKEDITOR.instances.editor1.on("instanceReady", function () {
      let companyId = $("#srch_company_id").val();

      if (companyId) {
        load_terms_and_conditions(companyId);
      }
    });
  });

  function load_terms_and_conditions(companyId) {
    $.ajax({
      url: "<?php echo site_url('get-data'); ?>",
      type: "POST",
      dataType: "json",
      data: {
        tbl: "get-company-terms-and-conditions-list",
        id: companyId,
      },
      success: function (res) {
        console.log("Response:", res);

        if (res && res.length > 0) {
          CKEDITOR.instances.editor1.setData(res[0].dn_terms || "");
        } else {
          CKEDITOR.instances.editor1.setData("");
        }
      },
      error: function (xhr) {
        console.log(xhr.responseText);
      },
    });
  }
</script>
