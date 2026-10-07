<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
<link
  rel="stylesheet"
  href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"
/>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<link
  rel="stylesheet"
  href="<?php echo base_url() ?>asset/bower_components/select2/dist/css/select2.min.css"
/>

<script src="<?php echo base_url() ?>asset/bower_components/select2/dist/js/select2.full.min.js"></script>

<script>
  $(document).ready(function () {
    $(".select2").select2();
    // ─── Globals ────────────────────────────────────────────────────────
    var selectedItems = {}; // keyed by unique row id (vendor_po_item_id)
    var rowCounter = 0;

    // ─── Preload items for Edit Mode ────────────────────────────────────
    if (typeof preloaded_items !== 'undefined' && preloaded_items.length > 0) {
      $.each(preloaded_items, function(i, item) {
        var uid = "item_" + item.vendor_po_item_id;
        
        var editItem = {
          uid: uid,
          vendor_purchase_multiple_invoice_item_id: item.vendor_purchase_multiple_invoice_item_id,
          vendor_po_item_id: item.vendor_po_item_id,
          vendor_po_id: item.vendor_po_id,
          customer_id: item.customer_id,
          customer_name: item.customer_name,
          tender_enquiry_id: item.tender_enquiry_id,
          tender_details: item.tender_details,
          enquiry_no: item.enquiry_no,
          po_no: item.po_no,
          item_code: item.real_item_code,
          item_desc: item.item_desc,
          uom: item.uom,
          qty: item.qty,
          rate: item.rate,
          gst: item.gst,
          category_id: item.category_id,
          item_id: item.item_id,
          conversion_rate: item.conversion_rate
        };
        
        selectedItems[uid] = editItem;
      });
      renderSelectedTable();
    }

    // ─── Vendor change → enable "Add Items" button, load POs ────────────
    $("#srch_vendor_id").on("change", function () {
      var vendor_id = $(this).val();

      selectedItems = {};
      renderSelectedTable();

      if (!vendor_id) {
        $("#btn_open_item_modal").prop("disabled", true);
        $("#vendor_hint").text(" (Select a vendor first)");
        return;
      }

      $("#btn_open_item_modal").prop("disabled", false);
      $("#vendor_hint").text("");

      // Load Contact Persons
      $("#srch_vendor_contact_id").html(
        '<option value="">Select Contact</option>',
      );
      $.post(
        '<?php echo site_url("vendor/get_data"); ?>',
        { tbl: "get-vendor-contacts", id: vendor_id },
        function (res) {
          if (res && res.length > 0) {
            $.each(res, function (i, row) {
              $("#srch_vendor_contact_id").append(
                '<option value="' +
                  row.vendor_contact_id +
                  '">' +
                  row.contact_person_name +
                  "</option>",
              );
            });
          }
        },
        "json",
      );
    });

    // ─── Open modal ─────────────────────────────────────────────────────
    $("#btn_open_item_modal").on("click", function () {
      $("#modal_enquiry_search").val("");
      $("#modal_results_area").html(
        '<div class="no-results-msg"><i class="fa fa-info-circle text-info fa-2x"></i><br>Type an enquiry number and click Search to load items.</div>',
      );
      updateModalCount();
      $("#itemSearchModal").modal("show");
    });

    // ─── Search items in modal ───────────────────────────────────────────
    $("#btn_search_items").on("click", function () {
      doItemSearch();
    });
    $("#modal_enquiry_search").on("keypress", function (e) {
      if (e.which === 13) {
        e.preventDefault();
        doItemSearch();
      }
    });

    // jQuery UI Autocomplete for the search input
    $("#modal_enquiry_search").autocomplete({
      source: function (request, response) {
        $.ajax({
          url: "<?php echo base_url('Multiple_customer_vendor/tender_enquiry_id_search'); ?>",
          type: "POST",
          data: {
            search: request.term,
            vendor_id: $("#srch_vendor_id").val(),
          },
          dataType: "json",
          success: function (data) {
            response(data);
          },
        });
      },
      minLength: 1,
      select: function (event, ui) {
        // When selected, set the value and trigger the search
        $("#modal_enquiry_search").data(
          "selected_id",
          ui.item.tender_enquiry_id,
        );
        setTimeout(function () {
          doItemSearch();
        }, 100);
      },
    });

    $("#modal_enquiry_search").on("input", function () {
      // Clear the selected ID if the user changes the text manually
      $(this).removeData("selected_id");
    });

    function doItemSearch() {
      var vendor_id = $("#srch_vendor_id").val();
      var enquiry_search = $("#modal_enquiry_search").val().trim();
      var tender_enquiry_id =
        $("#modal_enquiry_search").data("selected_id") || "";

      if (!vendor_id) {
        alert("Please select a vendor first.");
        return;
      }

      $("#modal_results_area").html(
        '<div class="text-center" style="padding:30px;"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><br>Loading items...</div>',
      );

      $.ajax({
        url: '<?php echo site_url("Multiple_customer_vendor/get_multi_customer_po_items"); ?>',
        type: "POST",
        data: {
          vendor_id: vendor_id,
          enquiry_search: enquiry_search,
          tender_enquiry_id: tender_enquiry_id,
        },
        dataType: "json",
        success: function (res) {
          if (res.error) {
            $("#modal_results_area").html(
              '<div class="alert alert-danger">' + res.error + "</div>",
            );
            return;
          }
          renderModalResults(res.customers);
        },
        error: function () {
          $("#modal_results_area").html(
            '<div class="alert alert-danger">Error loading items. Please try again.</div>',
          );
        },
      });
    }

    function renderModalResults(customers) {
      if (!customers || customers.length === 0) {
        $("#modal_results_area").html(
          '<div class="no-results-msg"><i class="fa fa-exclamation-circle text-warning fa-2x"></i><br>No items found for this vendor/enquiry.</div>',
        );
        return;
      }

      var html = "";
      $.each(customers, function (ci, cust) {
        html += '<div class="customer-block">';
        html += '<div class="customer-block-header">';
        html +=
          '<span><i class="fa fa-user-circle-o"></i> ' +
          escHtml(cust.customer_name || "Unknown Customer") +
          "</span>";
        html +=
          '<button type="button" class="btn btn-xs btn-light select-all-btn" data-cid="' +
          cust.customer_id +
          '">Select All</button>';
        html += "</div>";
        html += '<div style="padding:0;">';
        html +=
          '<table class="table table-bordered table-hover item-modal-table" style="margin:0;">';
        html +=
          '<thead><tr><th width="4%"></th><th>Enquiry / PO</th><th>Item Code</th><th>Description</th><th>UOM</th><th>Qty</th><th>Rate</th><th>GST%</th><th>Amount</th></tr></thead>';
        html += "<tbody>";

        $.each(cust.items, function (ii, item) {
          var uid = "item_" + item.vendor_po_item_id;
          var chk = selectedItems[uid] ? "checked" : "";
          var baseAmt =
            (parseFloat(item.qty) || 0) * (parseFloat(item.rate) || 0);
          var gstAmt = (baseAmt * (parseFloat(item.gst) || 0)) / 100;
          var totalAmt = (baseAmt + gstAmt).toFixed(3);

          html +=
            '<tr class="modal-item-row" data-cid="' + cust.customer_id + '">';
          html +=
            '<td><input type="checkbox" class="modal-item-chk" data-uid="' +
            uid +
            '" ';
          html +=
            "data-item='" +
            JSON.stringify({
              uid: uid,
              vendor_po_item_id: item.vendor_po_item_id,
              vendor_po_id: item.vendor_po_id,
              customer_id: cust.customer_id,
              customer_name: cust.customer_name,
              tender_enquiry_id: item.tender_enquiry_id,
              tender_details: item.tender_details,
              enquiry_no: item.enquiry_no,
              po_no: item.po_no,
              item_code: item.item_code,
              item_desc: item.item_desc,
              uom: item.uom,
              qty: item.qty,
              rate: item.rate,
              gst: item.gst,
              amount: totalAmt,
              category_id: item.category_id,
              item_id: item.item_id,
            }).replace(/'/g, "&#39;") +
            "' " +
            chk +
            "></td>";
          html +=
            '<td><span class="enquiry-tag">' +
            escHtml(item.tender_details || item.enquiry_no || "") +
            "</span><br><small>" +
            escHtml(item.po_no || "") +
            "</small></td>";
          html += "<td>" + escHtml(item.item_code || "") + "</td>";
          html += "<td>" + escHtml(item.item_desc || "") + "</td>";
          html += "<td>" + escHtml(item.uom || "") + "</td>";
          html += "<td>" + parseFloat(item.qty).toFixed(3) + "</td>";
          html += "<td>" + parseFloat(item.rate).toFixed(3) + "</td>";
          html += "<td>" + parseFloat(item.gst || 0).toFixed(1) + "%</td>";
          html += "<td>" + totalAmt + "</td>";
          html += "</tr>";
        });

        html += "</tbody></table></div></div>";
      });

      $("#modal_results_area").html(html);

      // Re-apply already selected states
      $(".modal-item-chk").each(function () {
        var uid = $(this).data("uid");
        if (selectedItems[uid]) $(this).prop("checked", true);
      });

      updateModalCount();

      // Select-all per customer
      $(document).on("click", ".select-all-btn", function () {
        var cid = $(this).data("cid");
        $(".modal-item-chk").each(function () {
          var row = $(this).closest("tr");
          if (row.data("cid") == cid) {
            $(this).prop("checked", true);
            var item = $(this).data("item");
            if (typeof item === "string") item = JSON.parse(item);
            selectedItems[item.uid] = item;
          }
        });
        updateModalCount();
      });
    }

    // Checkbox state tracking
    $(document).on("change", ".modal-item-chk", function () {
      var uid = $(this).data("uid");
      var item = $(this).data("item");
      if (typeof item === "string") item = JSON.parse(item);

      if ($(this).is(":checked")) {
        selectedItems[uid] = item;
      } else {
        delete selectedItems[uid];
      }
      updateModalCount();
    });

    function updateModalCount() {
      var count = Object.keys(selectedItems).length;
      $("#modal_selected_count").text(count + " selected");
    }

    // Select/Deselect All
    $("#btn_select_all_modal").on("click", function () {
      $(".modal-item-chk").each(function () {
        $(this).prop("checked", true);
        var uid = $(this).data("uid");
        var item = $(this).data("item");
        if (typeof item === "string") item = JSON.parse(item);
        selectedItems[uid] = item;
      });
      updateModalCount();
    });

    $("#btn_deselect_all_modal").on("click", function () {
      $(".modal-item-chk").prop("checked", false);
      selectedItems = {};
      updateModalCount();
    });

    // ─── Add selected items to the main table ────────────────────────────
    $("#btn_add_selected_items").on("click", function () {
      var count = Object.keys(selectedItems).length;
      if (count === 0) {
        alert("Please select at least one item.");
        return;
      }
      renderSelectedTable();
      $("#itemSearchModal").modal("hide");
    });

    function renderSelectedTable() {
      var $tbody = $("#item_container");
      $tbody.empty();

      var keys = Object.keys(selectedItems);
      if (keys.length === 0) {
        $tbody.html(
          '<tr id="no_items_row"><td colspan="12" class="no-results-msg"><i class="fa fa-info-circle"></i> No items added yet.</td></tr>',
        );
        calculateTotals();
        return;
      }

      $.each(keys, function (i, uid) {
        var item = selectedItems[uid];
        var rowIdx = i;
        var qty = parseFloat(item.qty) || 0;
        var rate = parseFloat(item.rate) || 0;
        var gst = parseFloat(item.gst) || 0;
        var cRate = parseFloat(item.conversion_rate) || 1;
        var baseAmt = qty * rate * cRate;
        var vatAmt = (baseAmt * gst) / 100;
        var total = baseAmt + vatAmt;

        var row = $('<tr class="selected-item-row" data-uid="' + uid + '">');
        
        // 1. Checkbox / Delete
        row.append(`
          <td class="text-center">
            <input type="checkbox" class="item-check chk_po_item" name="selected_items[]" value="${rowIdx}" checked>
            <br><button type="button" class="btn btn-danger btn-xs remove-item-btn mt-2" data-uid="${uid}" style="margin-top:5px;"><i class="fa fa-trash"></i></button>
          </td>
        `);

        // 2. Item Code & Description (with Customer/Enquiry/PO)
        row.append(`
          <td>
            <input type="text" class="form-control" name="item_code[${rowIdx}]" value="${escHtml(item.item_code || "")}" readonly>
            <input type="hidden" name="vendor_purchase_multiple_invoice_item_id[${rowIdx}]" value="${item.vendor_purchase_multiple_invoice_item_id || ''}">
            <input type="hidden" name="customer_id[${rowIdx}]" value="${item.customer_id}">
            <input type="hidden" name="tender_enquiry_id[${rowIdx}]" value="${item.tender_enquiry_id}">
            <input type="hidden" name="vendor_po_id[${rowIdx}]" value="${item.vendor_po_id}">
            <input type="hidden" name="vendor_po_item_id[${rowIdx}]" value="${item.vendor_po_item_id}">
            <input type="hidden" name="item_id[${rowIdx}]" value="${item.item_id}">
            <input type="hidden" name="category_id[${rowIdx}]" value="${item.category_id}">
            <div style="margin-top:5px; margin-bottom:5px;">
              <span class="badge bg-blue" style="font-size:10px;">${escHtml(item.customer_name || "")}</span>
              <span class="badge bg-green" style="font-size:10px;">${escHtml(item.tender_details || item.enquiry_no || "")}</span>
              <span class="badge bg-yellow" style="font-size:10px;">${escHtml(item.po_no || "")}</span>
            </div>
            <i class="text-sm">Desc</i>
            <textarea class="form-control" rows="2" name="item_desc[${rowIdx}]" readonly>${escHtml(item.item_desc || "")}</textarea>
          </td>
        `);

        // 3. UOM & Qty
        row.append(`
          <td>
            <input type="text" name="uom[${rowIdx}]" class="form-control" value="${escHtml(item.uom || "")}" readonly>
            <i class="text-sm">Qty</i>
            <input type="number" step="any" name="qty[${rowIdx}]" class="form-control qty-input" value="${qty.toFixed(3)}" data-uid="${uid}">
          </td>
        `);

        // 4. Rate & Conversion
        row.append(`
          <td>
            <input type="number" step="any" name="rate[${rowIdx}]" class="form-control rate-input" value="${rate.toFixed(3)}" data-uid="${uid}"> 
            <i class="text-sm">Conversion Rate</i> 
            <input type="number" step="any" name="conversion_rate[${rowIdx}]" class="form-control conversion_rate_input" value="${cRate.toFixed(5)}" data-uid="${uid}">
          </td>
        `);

        // 5. Amount (Act Amt)
        row.append(`
          <td>
            <input type="number" step="any" name="act_amt[${rowIdx}]" class="form-control act_amt" value="${baseAmt.toFixed(3)}" readonly>
          </td>
        `);

        // 6. VAT %
        row.append(`
          <td>
            <input type="number" step="any" name="gst[${rowIdx}]" class="form-control gst-input" value="${gst.toFixed(1)}" data-uid="${uid}">
          </td>
        `);

        // 7. W/O Tax & With Tax
        row.append(`
          <td>
            <input type="number" step="any" name="amountwotx[${rowIdx}]" class="form-control amountwotx" value="${baseAmt.toFixed(3)}" readonly>
            <i class="text-sm">With Tax</i>
            <input type="number" step="any" name="amount[${rowIdx}]" class="form-control amount-hidden" value="${total.toFixed(3)}" readonly>
          </td>
        `);

        $tbody.append(row);
      });

      calculateTotals();
    }

    // Live recalculate on qty/rate/gst/conversion change
    $(document).on("input", ".qty-input, .rate-input, .gst-input, .conversion_rate_input", function () {
      var uid = $(this).data("uid");
      var $row = $(this).closest("tr");
      var qty = parseFloat($row.find(".qty-input").val()) || 0;
      var rate = parseFloat($row.find(".rate-input").val()) || 0;
      var cRate = parseFloat($row.find(".conversion_rate_input").val()) || 1;
      var gst = parseFloat($row.find(".gst-input").val()) || 0;
      
      var ac_amt = qty * rate * cRate;
      var amountWithoutTax = ac_amt; 
      var vat = (amountWithoutTax * gst) / 100;
      var total = amountWithoutTax + vat;
      
      $row.find(".act_amt").val(ac_amt.toFixed(3));
      $row.find(".amountwotx").val(amountWithoutTax.toFixed(3));
      $row.find(".amount-hidden").val(total.toFixed(3));

      // Update selectedItems too
      if (selectedItems[uid]) {
        selectedItems[uid].qty = qty;
        selectedItems[uid].rate = rate;
        selectedItems[uid].gst = gst;
        selectedItems[uid].conversion_rate = cRate;
      }
      calculateTotals();
    });

    // Remove item row
    $(document).on("click", ".remove-item-btn", function () {
      var uid = $(this).data("uid");
      delete selectedItems[uid];
      renderSelectedTable();
    });

    function calculateTotals() {
      var totalWoTax = 0,
        totalVat = 0;
      $("#item_container tr.selected-item-row").each(function () {
        var amt = parseFloat($(this).find(".amount-hidden").val()) || 0;
        var gst = parseFloat($(this).find(".gst-input").val()) || 0;
        var qty = parseFloat($(this).find(".qty-input").val()) || 0;
        var rate = parseFloat($(this).find(".rate-input").val()) || 0;
        var cRate = parseFloat($(this).find(".conversion_rate_input").val()) || 1;
        var base = qty * rate * cRate;
        var vat = (base * gst) / 100;
        totalWoTax += base;
        totalVat += vat;
      });
      var totalWithTax = totalWoTax + totalVat;
      $("#total_amount_wo_tax").text(totalWoTax.toFixed(3));
      $("#hidden_total_amount_wo_tax").val(totalWoTax.toFixed(3));

      $("#total_tax_amount").text(totalVat.toFixed(3));
      $("#hidden_total_vat_amount").val(totalVat.toFixed(3));

      $("#total_amount").text(totalWithTax.toFixed(3));
      $("#hidden_total_amount").val(totalWithTax.toFixed(3));

      // Additional Charges
      var addtWoTax = 0,
        addtVat = 0;
      $(".chk_addt_charge:checked").each(function () {
        var $row = $(this).closest("tr");
        var amt = parseFloat($row.find(".addt_charges_amt").val()) || 0;
        var convRate = parseFloat($row.find(".addt_charges_conversion_rate").val()) || 0;
        var convAmt = amt * convRate;
        var vatPct = parseFloat($row.find(".addt_charges_vat").val()) || 0;
        var vatAmt = (convAmt * vatPct) / 100;
        var totAmt = convAmt + vatAmt;

        $row.find(".addt_charges_conversion_amt").val(convAmt.toFixed(3));
        $row.find(".addt_charges_vat_amt").val(vatAmt.toFixed(3));
        $row.find(".addt_charges_tot_amt").val(totAmt.toFixed(3));

        addtWoTax += convAmt;
        addtVat += vatAmt;
      });

      // Clear unchecked rows
      $(".chk_addt_charge:not(:checked)").each(function () {
        var $row = $(this).closest("tr");
        $row.find(".addt_charges_vat_amt").val("0.000");
        $row.find(".addt_charges_tot_amt").val("0.000");
      });

      var grandWoTax = totalWoTax + addtWoTax;
      var grandVat = totalVat + addtVat;
      var grandWithTax = grandWoTax + grandVat;

      if (!$("#fix_theamount_total").is(":checked")) {
        $("#total_amount_wo_tax_addt").text(grandWoTax.toFixed(3));
        $("#hidden_total_amount_wo_tax_inc_addl").val(grandWoTax.toFixed(3));

        $("#total_tax_amount_addt").text(grandVat.toFixed(3));
        $("#hidden_total_tax_amount_inc_addl").val(grandVat.toFixed(3));

        $("#total_amount_addt").text(grandWithTax.toFixed(3));
        $("#hidden_total_amount_inc_addl").val(grandWithTax.toFixed(3));
      }
    }

    $(document).on(
      "input",
      ".addt_charges_amt, .addt_charges_conversion_rate, .addt_charges_vat",
      function () {
        var $row = $(this).closest("tr");
        if ($row.find(".chk_addt_charge").is(":checked")) {
          calculateTotals();
        }
      },
    );

    $(document).on("change", ".chk_addt_charge", function () {
      var $row = $(this).closest("tr");
      if ($(this).is(":checked")) {
        $row
          .find(".addt_charges_amt, .addt_charges_conversion_rate, .addt_charges_vat")
          .removeAttr("readonly");
      } else {
        $row
          .find(".addt_charges_amt, .addt_charges_conversion_rate, .addt_charges_vat")
          .attr("readonly", true);
        $row.find(".addt_charges_amt").val("0.000");
        $row.find(".addt_charges_conversion_rate").val("1.00000");
        $row.find(".addt_charges_vat").val("0.00");
      }
      calculateTotals();
    });

    $(document).on("change", "#fix_theamount_total", function () {
      if ($(this).is(":checked")) {
        $("#hidden_total_amount_wo_tax_inc_addl, #hidden_total_tax_amount_inc_addl, #hidden_total_amount_inc_addl").removeAttr("readonly");
      } else {
        $("#hidden_total_amount_wo_tax_inc_addl, #hidden_total_tax_amount_inc_addl, #hidden_total_amount_inc_addl").attr("readonly", true);
        calculateTotals();
      }
    });

    // VAT accounting toggle
    $("#only_accounting_entry").on("change", function () {
      $("#vat_payer_purchase_grp").prop("disabled", $(this).is(":checked"));
    });

    // Helper: escape HTML
    function escHtml(str) {
      return $("<div>").text(str).html();
    }

    // CKEditor for remarks
    if (typeof CKEDITOR !== "undefined") {
      CKEDITOR.replace("editor2", { height: 80 });
    }

    // Form submit – copy CKEditor value
    $("#frmadd").on("submit", function () {
      if (typeof CKEDITOR !== "undefined" && CKEDITOR.instances["editor2"]) {
        $('[name="remarks"]').val(CKEDITOR.instances["editor2"].getData());
      }
      if (Object.keys(selectedItems).length === 0) {
        alert("Please add at least one item before saving.");
        return false;
      }
      return true;
    });
  });
</script>
