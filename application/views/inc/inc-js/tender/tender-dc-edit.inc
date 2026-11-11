<style>
   .readonly  { 
         pointer-events: none;
    }
</style>
<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
 
<script> 
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

<script>
  $(document).ready(function () {
    let currentItemIndex = $("#item_container tr").length;
    let cachedPoItems = [];
    let cachedStockItems = [];

    function escapeHtml(text) {
      if (!text) return '';
      return $('<div>').text(text).html();
    }

    /* ==================== STOCK PICKER MODAL LOGIC ==================== */
    $("#btn_open_stock_modal").on("click", function () {
      const poId = $("#tender_po_id").val();
      if (!poId) {
        alert("Customer PO is not selected.");
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
      const currentDcId = <?php echo (int)($header['tender_dc_id'] ?? 0); ?>;
      $.ajax({
        url: "<?php echo site_url('tender/get_available_instock_items_ajax'); ?>",
        type: "POST",
        data: {
          search: kw,
          current_dc_id: currentDcId
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

      // Clear empty placeholder if any
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
    });
  });
</script>
