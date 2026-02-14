<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script> 
<style>
  /* Your existing styles remain unchanged */
  .text-light-blue {
    color: #3c8dbc !important;
  }

    .readonly-select {
        pointer-events: none;
        background-color: #e9ecef;
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
      const $enquiry = $("#srch_vendor_rate_enquiry_id");
      const $contact = $("#srch_vendor_contact_id");

      $enquiry.html('<option value="">Select Enquiry No</option>');
      $contact.html('<option value="">Select Contact</option>');

      if (!vendor_id) return;

      // Load Rate Enquiry Nos
      $.post(
        "<?php echo site_url('vendor/get_data'); ?>",
        {
          tbl: "get-vendor-rate-enquiry-list",
          id: vendor_id,
          tender_enquiry_id : $('#srch_tender_enquiry_id').val(),
        },
        function (res) {
          if (res.length > 0) {
            $.each(res, function (i, row) {
              $enquiry.append(
                `<option value="${row.vendor_rate_enquiry_id}">${row.vendor_rate_enquiry_no}</option>`
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
      let jsonData = <?php  echo json_encode($items); ?>;
      exportJSONToXLSX(jsonData, $(this).val() +".xlsx");
    });


    const rowHtml = `
                    <tr class="item-row"> 
                        <td>
                             <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="" checked>
                        </td> 
                       
                        <td> 
                             <input type="text" name="item_code[]" class="form-control item_code" value="" >                            
                             <input type="hidden" name="vendor_quote_item_id[]" class="vendor_quote_item_id" value="">
                             <input type="hidden" name="vendor_rate_enquiry_item_id[]" class="vendor_rate_enquiry_item_id" value="">
                             
                        </td>

                        <td>
                            <textarea name="item_desc[]" class="form-control item_desc" rows="2" ></textarea>
                        </td>

                        <td>
                            <input type="text" name="uom[]" class="form-control uom" value="" readonly>
                            <br>
                            <input type="number" step="any" name="qty[]" class="form-control qty" value="">
                        </td>

                        <td>
                            <input type="number" step="any" name="rate[]" class="form-control rate">
                        </td>

                        <td>
                            <input type="number" step="any" name="gst[]" class="form-control vat">
                        </td>

                        <td>
                            <input type="number" name="amount[]" class="form-control amount" value="0.00" readonly>
                        </td> 
                       
                       
                    </tr>
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
                  
                  let newRow = $(rowHtml); 

                  newRow.find(".cnt").html((i+1));
                  newRow.find(".item-check").val(i); 
                  newRow.find(".vendor_quote_item_id").val(r.vendor_quote_item_id); 
                  newRow.find(".vendor_rate_enquiry_item_id").val(r.vendor_rate_enquiry_item_id); 
                  newRow.find(".item_code").val(r.item_code);
                  newRow.find(".item_desc").val(r.item_desc);
                  newRow.find(".uom").val(r.uom);
                  newRow.find(".qty").val(r.qty); 
                  newRow.find(".rate").val(r.rate); 
                  newRow.find(".vat").val(r.vat); 
                  newRow.find(".amount").val(r.amount);   

                  $("#item_container").append(newRow); 
                  
                  i++;
              }); 

              $(".qty, .rate, .vat").trigger("input");
           
          };

          reader.readAsBinaryString(file);
      }); 



  // CKEditor
  window.onload = function () {
    CKEDITOR.replace("editor1", { height: 120 });
    CKEDITOR.replace("editor2", { height: 120 });
  };
</script>
<script>
  $("#srch_vendor_rate_enquiry_id").on("change", function () {
    const vendor_rate_enquiry_id = $(this).val();
    const $container = $("#item_container");
    $container.html("");

    if (!vendor_rate_enquiry_id) return;

    $.ajax({
      url: "<?php echo site_url('vendor/get_vendor_quotation_rate_enquiry'); ?>",
      type: "POST",
      data: { vendor_rate_enquiry_id },
      dataType: "json",
      success: function (res) {
        if (res.length === 0) {
          $container.html(
            '<tr><td colspan="10" class="text-center text-danger">No items found for this quotation.</td></tr>'
          );
          return;
        }

        $.each(res, function (i, row) {
          const rowHtml = `
                    <tr class="item-row"> 
                        <td>
                             <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="${i}" checked>
                        </td> 
                       
                        <td> 
                             <input type="text" name="srch_item[]" class="form-control srch_item" autocomplete="off" value="${
                               row.full_item_name || ""
                             }" readonly>
                            
                        <input type="hidden" name="vendor_rate_enquiry_item_id[]" value="${
                          row.vendor_rate_enquiry_item_id
                        }">
                             <input type="hidden" name="item_id[]" class="item_id" value="${
                               row.item_id
                             }">
                             <input type="hidden" name="category_id[]" class="category_id" value="${
                               row.category_id
                             }">
                        </td>

                        <td>
                            <textarea name="item_desc[]" class="form-control" rows="2" readonly>${
                              row.item_desc || ""
                            }</textarea>
                        </td>

                        <td>
                            <input type="text" name="uom[]" class="form-control" value="${
                              row.uom || ""
                            }" readonly>
                        </td>

                        <td>
                            <input type="number" step="any" name="qty[]" class="form-control qty" value="${
                              row.qty
                            }" readonly>
                        </td>

                        <td>
                            <input type="number" step="any" name="rate[]" class="form-control rate">
                        </td>
                        <td>
                            <input type="number"  name="gst[]" class="form-control vat">
                        </td>

                        <td>
                            <input type="number" name="amount[]" class="form-control amounttx amount" value="0.000" readonly>
                        </td> 
                       
                       
                    </tr>
                `;
     calculateTotalAmount();
          $container.append(rowHtml);
        });
      },
      error: function () {
        alert("Error loading quotation items");
      },
    });
  });
</script>
<script>
$(document).ready(function () {

      $(document).on("input change", ".rate, .vat", function () {  
       const $row = $(this).closest(".item-card");

        const isChecked = $row.find(".item-check").is(":checked");

        if (!isChecked) {
            // If not checked, clear amounts
            $row.find(".amountwotx").val("0.000");
            $row.find(".amount-input").val("0.000");
            calculateTotalAmount();
            return;
        }

       const qty = parseFloat($row.find(".qty").val()) || 0;
       const rate = parseFloat($row.find(".rate").val()) || 0;
       const gst = parseFloat($row.find(".vat").val()) || 0;

       const amount = qty * rate * (1 + gst / 100);
       $row.find(".amount").val(amount.toFixed(3));
       $row.find(".amountwotx").val((qty * rate).toFixed(3));

       calculateTotalAmount();
     });

     function calculateTotalAmount() {
       let total = 0;
       $(".amount").each(function () {
         total += parseFloat($(this).val()) || 0;
       });
       $("#total_amount").text(total.toFixed(3));

       let total_wot = 0;
       $(".amountwotx").each(function () {
         total_wot += parseFloat($(this).val()) || 0;
       });
       $("#total_amount_wo_tax").text(total_wot.toFixed(3));
     }

     calculateTotalAmount();

 $('#selectall').on('change', function () {
      $('.item-check').prop('checked', this.checked);
  });

  // If any item unchecked → uncheck Select All
  $('.item-check').on('change', function () {
      $('#selectall').prop(
          'checked',
          $('.item-check:checked').length === $('.item-check').length
      );
  });
 
});
</script>
