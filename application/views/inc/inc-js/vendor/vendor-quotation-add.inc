<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script> 
<link  rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
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
           //console.log(ui);
            $("#srch_company_id").val(ui.item.company_id);
            $("#srch_customer_id").val(ui.item.customer_id).change();
            //$("#srch_tender_enquiry_id").val(ui.item.tender_enquiry_id); 
            load_tender_enq(ui.item.tender_enquiry_id); 
        }, 
      });


      function load_tender_enq(t_enq_id = '') {

      const customer_id = $("#srch_customer_id").val(); // <-- IMPORTANT
      const company_id  = $("#srch_company_id").val();
      const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $container = $("#item_container");  

      // Reset
      $enquiryDropdown
          .html('<option value="">Select Enquiry</option>')
          .prop('disabled', true);

       
      $container.html('');

      if (!customer_id || !company_id) return;

      $.ajax({
          url: "<?php echo site_url('vendor/get_tender_enquiries_by_customer'); ?>",
          type: "POST",
          data: { 
              company_id: company_id,
              customer_id: customer_id 
          },
          dataType: "json",
          success: function (res) {

              if (res.length > 0) {
                  $enquiryDropdown.prop('disabled', false);

                  $.each(res, function (i, row) {
                      $enquiryDropdown.append(
                          $('<option>', {
                              value: row.tender_enquiry_id,
                              text: row.display
                          })
                      );
                  });

                  // ✅ SET SELECTED VALUE HERE
                  if (t_enq_id) {
                      $enquiryDropdown.val(t_enq_id).trigger('change');
                  }

              } else {
                  $enquiryDropdown.html('<option value="">No enquiries found</option>');
              }
          },
          error: function () {
              alert('Error loading enquiries');
          }
      });
    }   
       
    $("#srch_customer_id").on("change", function () {
      load_tender_enq('');
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
      //alert($('#srch_tender_enquiry_id').val());
      //alert($(this).val());
      const vendor_id = $(this).val();
      const $enquiry = $("#srch_vendor_rate_enquiry_id");
      const $contact = $("#srch_vendor_contact_id");

      //$enquiry.html('<option value="">Select Enquiry No</option>');
      //$contact.html('<option value="">Select Contact</option>');
      $enquiry.html('');
      $contact.html('');

      if (!vendor_id) return;

      // Load Rate Enquiry Nos
      $.post(
        "<?php echo site_url('vendor/get_data'); ?>",
        {
          tbl: "get-vendor-rate-enquiry-list",
          id: $(this).val(),
          tender_enquiry_id : $('#srch_tender_enquiry_id').val(),
        },
        function (res) { //console.log(res); alert('hi');
          if (res.length > 0) {
            $.each(res, function (i, row) {
              $enquiry.append(
                `<option value="${row.vendor_rate_enquiry_id}">${row.vendor_rate_enquiry_no} [ ${row.enquiry_date} ]</option>`
              );
            });
          }

          // 🔥 trigger AFTER options are ready
          $enquiry.prop('selectedIndex', 0).trigger("change");
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

    $('.cls_export').hide();

    $(document).on("click", "#btnExport", function () {

         let jsonData = $(this).data("xls"); // ✅ already an object

        let fileName = $(this).val() || "export";
        exportJSONToXLSX(jsonData, fileName + ".xlsx");
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

      const rowHtml = `
                    <tr class="item-row"> 
                        <td>
                              Item # <span class="cnt"></span> <br>
                             <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="" checked>
                        </td> 
                       
                        <td> 
                             <input type="text" name="item_code[]" class="form-control item_code"  value=""> 
                             <input type="hidden" name="vendor_rate_enquiry_item_id[]" class="vendor_rate_enquiry_item_id" value=""> 
                        </td>

                        <td>
                            <textarea name="item_desc[]" class="form-control item_desc" rows="2"></textarea>
                        </td>

                        <td  class="text-right">
                            <input type="text" name="uom[]" class="form-control uom" value="" >
                            <br>
                            <input type="number" step="any" name="qty[]" class="form-control qty" value="" >
                        </td> 
                        <td  class="text-right">
                            <input type="number" step="any" name="rate[]" class="form-control rate" value="">
                        </td>
                        <td  class="text-right">
                          <input type="number" step="any" name="gst[]" class="form-control vat" value="10.00"> 
                        </td> 
                        <td  class="text-right">
                            <input type="number" name="amount[]" class="form-control amount" value="" readonly>
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

             // console.log(rows);

              $("#item_container").empty();
              let i = 0;
              rows.forEach(function (r) {
                  
                  let newRow = $(rowHtml); 

                  newRow.find(".cnt").html((i+1));
                  newRow.find(".item-check").val(i); 
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

          $('.cls_export').show();
          $('#btnExport').attr('data-xls', JSON.stringify(res));
          $('#btnExport').val('Vendor-Quote-' + $("#srch_vendor_rate_enquiry_id option:selected").text());
          //console.log(res);


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
                             <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="${i}">
                        </td> 
                       
                        <td> 
                             <input type="text" name="item_code[]" class="form-control item_code"  value="${ row.item_code || ""}" > 
                             <input type="hidden" name="vendor_rate_enquiry_item_id[]" value="${row.vendor_rate_enquiry_item_id}"> 
                        </td>

                        <td>
                            <textarea name="item_desc[]" class="form-control" rows="2" >${ row.item_desc || "" }</textarea>
                        </td>

                        <td  class="text-right">
                            <input type="text" name="uom[]" class="form-control" value="${ row.uom || ""}" >
                            <br>
                            <input type="number" step="any" name="qty[]" class="form-control qty" value="${row.qty}" >
                        </td> 
                        <td  class="text-right">
                            <input type="number" step="any" name="rate[]" class="form-control rate" value="${row.rate|| "0"}">
                        </td>
                        <td  class="text-right">
                          <input type="number" step="any" name="gst[]" class="form-control vat" value="${row.vat || "10.00"}"> 
                        </td> 
                        <td  class="text-right">
                            <input type="number" name="amount[]" class="form-control amount" value="${row.amount || "0"}" readonly>
                        </td> 
                       
                       
                    </tr>
                `;

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

// Recalculate amount on qty, rate, or GST change
$(document).on("input change", ".rate, .vat, .qty", function () {
    const $row = $(this).closest("tr.item-row");

    const qty  = parseFloat($row.find(".qty").val()) || 0;
    const rate = parseFloat($row.find(".rate").val()) || 0;
    const gst  = parseFloat($row.find(".vat").val()) || 0;

    // Amount = qty * rate
    let amount = qty * rate;

    // GST added only if gst selected
    if (gst > 0) {
        amount = amount + (amount * gst / 100);
    }

    $row.find(".amount").val(amount.toFixed(3));

    calculateTotalAmount();
});


// Calculate full total of all item rows
function calculateTotalAmount() {
    let total = 0;

    $(".item-check:checked").each(function () {
        let row = $(this).closest("tr");
        total += parseFloat(row.find(".amount").val()) || 0;
    });

    $("#total_amount").text(total.toFixed(3));
}


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

</script>
