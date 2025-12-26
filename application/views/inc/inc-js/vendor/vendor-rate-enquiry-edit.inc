<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<style>
    .readonly-select {
        pointer-events: none;
        background-color: #e9ecef;
    }
</style>
<script>
  $(document).ready(function () {
      const tender_enquiry_id = $("#old_tender_enquiry_id").val();

      // Store GST options from PHP
      const gstOptions = [
          <?php if(isset($gst_opt) && !empty($gst_opt)): ?>
              <?php foreach($gst_opt as $gst_id => $gst_pct): ?>
                  { id: '<?php echo $gst_id; ?>', value: '<?php echo $gst_pct; ?>' },
              <?php endforeach; ?>
          <?php endif; ?>
      ];

      // Initialize grand total on page load
      calculateGrandTotal();

      // Calculate amounts for existing items on page load
      $(".item-card").each(function() {
          calculateAmount($(this));
      });

      // Initialize with saved items if tender enquiry is the same
      if (tender_enquiry_id) {
          // Items are already loaded from PHP, no AJAX call needed on initial load
      }

      // Handle vendor change - load contact persons
      $("#srch_vendor_id").on("change", function () {
          const vendor_id = $(this).val();

          // Clear contact person dropdown
          $("#srch_vendor_contact_id").html('<option value="">Select</option>');

          if (vendor_id) {
              loadVendorContacts(vendor_id);
          }
      });

      function loadVendorContacts(vendor_id) {
          $.ajax({
              url: '<?php echo site_url("vendor/get_data"); ?>',
              type: "POST",
              data: { tbl: "get-vendor-contacts", id: vendor_id },
              dataType: "json",
              success: function (res) {
                  let options = '<option value="">Select</option>';

                  if (res.length > 0) {
                      $.each(res, function (i, row) {
                          options += `<option value="${row.vendor_contact_id}">${row.contact_person_name}</option>`;
                      });
                  }

                  $("#srch_vendor_contact_id").html(options);
              },
              error: function() {
                  alert('Error loading vendor contacts.');
              }
          });
      }

      // Handle tender enquiry change
      $("#srch_tender_enquiry_id").on("change", function () {
          const tender_enquiry_id = $(this).val();
          const old_tender_enquiry_id = $("#old_tender_enquiry_id").val();

          const selectedText = $(this).find("option:selected").text();
          const parts = selectedText.split(' -> ');

          if (parts.length >= 4) {
              const customerName = parts[3].trim();
              let customer_id = '';

              // Find customer ID from available options
              <?php foreach ($customer_opt as $id => $name): ?>
              if ("<?php echo $name; ?>" === customerName) {
                  customer_id = '<?php echo $id; ?>';
              }
              <?php endforeach; ?>

              $("#srch_customer_id").val(customer_id);
          }

          // Only load new items if tender enquiry changed
          if (tender_enquiry_id && tender_enquiry_id != old_tender_enquiry_id) {
              loadItems(tender_enquiry_id);
          }
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
    let jsonData = <?php  echo json_encode($item_rows); ?>;
    exportJSONToXLSX(jsonData, $(this).val() +".xlsx");
    });

    const itemHtml = `
                    <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
                        <h5 class="text-primary mb-3">Item Details <span class="cnt"></span></h5> 
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group" style="margin-top: 25px;">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="" checked>
                                        <label class="form-check-label" for=""
                                                    style="margin-left: 5px;">Select</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Item Code</label>
                                    <input type="text" name="item_code[]" class="form-control item_code" value="">
                                </div>

                                <input type="hidden" name="vendor_rate_enquiry_item_id[]" class="vendor_rate_enquiry_item_id" value="">
                                <input type="hidden" name="tender_enquiry_item_id[]" class="tender_enquiry_item_id" value=""> 
                            </div>
                             
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Item Description</label>
                                    <textarea name="item_desc[]" class="form-control desc-textarea" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>UOM</label>
                                            <input type="text" name="uom[]" class="form-control uom" value="" >
                                        </div>
                                        <div class="form-group">
                                            <label>Quantity</label>
                                            <input type="number" step="0.01" name="qty[]" class="form-control qty-input" value="" >
                                        </div>
                                    </div>
                                     
                                </div>
                            </div>
                        </div>
                    </div>
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
                
                let newRow = $(itemHtml);

                newRow.find(".cnt").html((i+1));
                newRow.find(".item-check").val(i);
                newRow.find(".vendor_rate_enquiry_item_id").val(r.vendor_rate_enquiry_item_id);
                newRow.find(".tender_enquiry_item_id").val(r.tender_enquiry_item_id); 
                newRow.find(".item_code").val(r.item_code);
                newRow.find(".desc-textarea").val(r.item_desc);
                newRow.find(".uom").val(r.uom);
                newRow.find(".qty-input").val(r.qty); 

                $("#item_container").append(newRow); 
                  
                i++;
            });
        };
        
        reader.readAsBinaryString(file);
    });

      function loadItems(tender_enquiry_id) {
          $.ajax({
              url: '<?php echo site_url("vendor/get_data"); ?>',
              type: "POST",
              data: { tbl: "get-tender-enquiry-item-list-rate", id: tender_enquiry_id },
              dataType: "json",
              success: function (res) {
                  $("#item_container").empty();

                  if (res.length > 0) {
                      $.each(res, function (i, row) {
                          // Build GST dropdown options
                          let gstOptionsHtml = '<option value="">Select</option>';
                          gstOptions.forEach(function(gst) {
                              gstOptionsHtml += `<option value="${gst.value}">${gst.value}%</option>`;
                          });

                          const itemHtml = `
                              <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
                                  <h5 class="text-primary mb-3">Item Details ${i + 1}</h5>
                                  <input type="hidden" name="vendor_rate_enquiry_item_id[]" value="">
                                  <input type="hidden" name="tender_enquiry_item_id[]" value="${row.tender_enquiry_item_id}">
                                  <input type="hidden" name="category_id[]" value="${row.category_id}">
                                  <input type="hidden" name="item_id[]" value="${row.item_id}">
                                  <div class="row">
                                      <div class="col-md-1">
                                          <div class="form-group" style="margin-top: 25px;">
                                              <div class="form-check">
                                                  <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="${i}" id="check_${i}" checked>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-3">
                                          <div class="row">
                                              <div class="col-md-12">
                                                  <div class="form-group">
                                                      <label>Category Name</label>
                                                      <input type="text" class="form-control" value="${row.category_name || ''}" readonly>
                                                  </div>
                                              </div>
                                              <div class="col-md-12">
                                                  <div class="form-group">
                                                      <label>Item Name</label>
                                                      <input type="text" class="form-control" value="${row.item_name || ''}" readonly>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-4">
                                          <div class="form-group">
                                              <label>Item Description</label>
                                              <textarea name="item_desc[]" class="form-control desc-textarea" rows="3">${row.item_desc || ''}</textarea>
                                          </div>
                                      </div>
                                      <div class="col-md-4">
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label>UOM</label>
                                                      <input type="text" name="uom[]" class="form-control" value="${row.uom || ''}" readonly>
                                                  </div>
                                              </div>
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label>Quantity</label>
                                                      <input type="number" step="0.01" name="qty[]" class="form-control qty-input" value="${row.qty || 0}" readonly>
                                                  </div>
                                              </div>
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label>Rate</label>
                                                      <input type="number" step="0.01" name="rate[]" class="form-control rate-input" value="0">
                                                  </div>
                                              </div>
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label>VAT %</label>
                                                      <select name="gst[]" class="form-control vat-dropdown">
                                                          ${gstOptionsHtml}
                                                      </select>
                                                  </div>
                                              </div>
                                              <div class="col-md-12">
                                                  <div class="form-group">
                                                      <label>Amount (Qty × Rate × VAT %)</label>
                                                      <input type="number" step="0.01" name="amount[]" class="form-control amount-input" value="0.00" readonly>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          `;
                          $("#item_container").append(itemHtml);
                      });
                  } else {
                      $("#item_container").html('<div class="alert alert-warning">No items found.</div>');
                  }

                  calculateGrandTotal();
              },
              error: function() {
                  alert('Error loading items.');
              }
          });
      }

      // Calculate amount when rate or VAT changes
      $(document).on("change", ".rate-input, .vat-dropdown", function() {
          const card = $(this).closest(".item-card");
          calculateAmount(card);
          calculateGrandTotal();
      });

      function calculateAmount(card) {
          const rateInput = card.find(".rate-input");
          const qtyInput = card.find(".qty-input");
          const vatDropdown = card.find(".vat-dropdown");
          const amountInput = card.find(".amount-input");

          const rate = parseFloat(rateInput.val()) || 0;
          const qty = parseFloat(qtyInput.val()) || 0;
          const vat = parseFloat(vatDropdown.val()) || 0;

          const baseAmount = qty * rate;
          const vatAmount = (baseAmount * vat) / 100;
          const finalAmount = baseAmount + vatAmount;

          amountInput.val(finalAmount.toFixed(2));
      }

      function calculateGrandTotal() {
          let grandTotal = 0;

          $(".item-card").each(function() {
              const isChecked = $(this).find(".item-check").is(":checked");
              if (isChecked) {
                  const amount = parseFloat($(this).find(".amount-input").val()) || 0;
                  grandTotal += amount;
              }
          });

          $("#grand_total").val(grandTotal.toFixed(2));
      }

      // Initialize - set opacity based on checkbox state on page load
      $(".item-card").each(function() {
          const isChecked = $(this).find(".item-check").is(":checked");
          if (!isChecked) {
              $(this).css("opacity", "0.6");
          } else {
              $(this).css("opacity", "1");
          }
      });
    $("#srch_customer_id").on("change", function () {
      const customer_id = $(this).val();
        const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $container = $("#item_container");

      // Reset dependent dropdown and items
      $enquiryDropdown.html('<option value="">Select Enquiry</option>').prop('disabled', true);
      $container.html('');

      if (!customer_id) return;

      $.ajax({
        url: "<?php echo site_url('vendor/get_vendor_rate_enquiries_by_customer'); ?>",
        type: "POST",
        data: {
            customer_id: customer_id
        },
        dataType: "json",
        success: function (res) {
          if (res.length > 0) {
            $enquiryDropdown.prop('disabled', false);
            $.each(res, function (i, row) {
              $enquiryDropdown.append(
                $('<option></option>')
                  .attr('value', row.tender_enquiry_id)
                  .text(row.display)
              );
            });
          } else {
            $enquiryDropdown.html('<option value="">No enquiries found</option>');
          }
        },
        error: function () {
          alert('Error loading enquiries');
        }
      });
    });
      // Handle checkbox change - toggle opacity and update grand total
      $(document).on("change", ".item-check", function() {
          const card = $(this).closest(".item-card");
          const isChecked = $(this).is(":checked");

          if (!isChecked) {
              card.css("opacity", "0.6");
          } else {
              card.css("opacity", "1");
          }

          calculateGrandTotal();
      });

      // Form submission - send all items with their checkbox state
      $("#frmadd").on("submit", function (e) {
          let hasChecked = false;

          $(".item-card").each(function () {
              const isChecked = $(this).find(".item-check").is(":checked");
              if (isChecked) {
                  hasChecked = true;
              }
          });

          if (!hasChecked) {
              alert("Please select at least one item.");
              e.preventDefault();
              return false;
          }
      });
  });
</script>
