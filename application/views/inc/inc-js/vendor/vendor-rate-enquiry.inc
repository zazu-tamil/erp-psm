<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script> 
<link  rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
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
           // $("#srch_company_id").val(ui.item.company_id);
            $("#srch_customer_id").val(ui.item.customer_id).change(); 
            load_tender_enq(ui.item.tender_enquiry_id); 
        }, 
      });

    function load_tender_enq(t_enq_id = '') {

      const customer_id = $('#srch_customer_id').val();
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
  
  // Load vendor contacts when vendor is selected
 $("#srch_vendor_id").on("change", function () {
    const vendor_id = $(this).val();

    // Auto-set vendor in Add Contact Person modal
         $("#contact_vendor_id").val(vendor_id).trigger("change"); 

    // Clear dropdown before loading
    $("#srch_vendor_contact_id").html('<option value="">Select Contact Person</option>');

    if (vendor_id) {
      $.ajax({
        url: '<?php echo site_url("vendor/get_data"); ?>',
        type: "POST",
        data: { tbl: "get-vendor-contacts", id: vendor_id },
        dataType: "json",
        success: function (res) {
          if (res.length > 0) {
            $.each(res, function (i, contact) {
              $("#srch_vendor_contact_id").append(
                `<option value="${contact.vendor_contact_id}">${contact.contact_person_name}</option>`
              );
            });
          } else {
            $("#srch_vendor_contact_id").html('<option>Select Contact Person</option>');
          }
        }
      });
    }
  });

  $("#srch_customer_id1").on("change", function () {
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


  // Load tender enquiry items when tender enquiry is selected
  $("#srch_tender_enquiry_id").on("change", function () {
    const tender_enquiry_id = $(this).val();
    $("#item_container").html("");
    
    if (tender_enquiry_id) {
      $.ajax({
        url: '<?php echo site_url("vendor/get_data"); ?>',
        type: "POST",
        data: {
          tbl: "get-tender-enquiry-item-list-rate",
          id: tender_enquiry_id
        },
        dataType: "json",
        success: function (res) {

          $('.cls_export').show();
          $('#btnExport').attr('data-xls', JSON.stringify(res));
          $('#btnExport').val('Vendor-ENQ-' + $("#srch_quotation_no option:selected").text());
          //console.log(res);



          if (res.length > 0) {
            $.each(res, function (i, row) {
              const itemHtml = `
                <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
                    
                    <input type="hidden" name="tender_enquiry_item_id[]" value="${row.tender_enquiry_item_id}">
                    
                    <div class="row"> 
                    <div class="d-flex justify-content-between align-items-center mb-2 col-md-4">
                        <h5 class="text-primary mb-0"><i class="fa fa-cube"></i> Item Details ${i + 1}</h5>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="${i}" id="check_${i}" checked>
                            <label class="form-check-label" for="check_${i}">Select</label>
                            <br>
                            <label class="form-check-label" for="">Item Code</label>
                            <input type="text" name="item_code[]" class="form-control item_code" value="${row.item_code}">
                        </div>
                    </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Description</label>
                                <textarea name="item_desc[]" class="form-control desc-textarea" style="height: 120px;" >${row.item_desc}</textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>UOM</label>
                                        <input type="text" name="uom[]" class="form-control" value="${row.uom}" >
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Quantity</label>
                                        <input type="number" step="any" name="qty[]" class="form-control" value="${row.qty}" >
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
             $('.cls_export').hide();
            $("#item_container").html(
              '<div class="alert alert-warning">No items found for this tender enquiry.</div>'
            );
          }
        },
        error: function(xhr, status, error) {
          console.error("Error loading tender items:", error);
          
          $("#item_container").html(
            '<div class="alert alert-danger">Error loading items. Please try again.</div>'
          );
        }
      });
    }
  });


   const itemHtml = `
                <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;"> 
                    
                    <div class="row"> 
                    <div class="d-flex justify-content-between align-items-center mb-2 col-md-4">
                        <h5 class="text-primary mb-0"><i class="fa fa-cube"></i> Item Details <span class="cnt"></span></h5>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value=""  checked>
                            <label class="form-check-label" for="">Select</label>
                             <input type="hidden" name="tender_enquiry_item_id[]" class="form-control tender_enquiry_item_id" value="">  
                             <br>
                             <label class="form-check-label" for="">Item Code</label>
                             <input type="text" name="item_code[]" class="form-control item_code" value="">
                        </div>
                    </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Description</label>
                                <textarea name="item_desc[]" class="form-control desc-textarea" style="height: 120px;" ></textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>UOM</label>
                                        <input type="text" name="uom[]" class="form-control uom" value="" >
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Quantity</label>
                                        <input type="number" step="any" name="qty[]" class="form-control qty" value="" >
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
                  newRow.find(".tender_enquiry_item_id").val(r.tender_enquiry_item_id); 
                  newRow.find(".item_code").val(r.item_code);
                  newRow.find(".desc-textarea").val(r.item_desc);
                  newRow.find(".uom").val(r.uom);
                  newRow.find(".qty").val(r.qty); 

                

                  $("#item_container").append(newRow); 
                  i++;
              });

           
          };

          reader.readAsBinaryString(file);
      }); 

  // Handle Add Vendor form submission
  $('input[name="btn_add_vendor"]').on("click", function (e) {
      e.preventDefault();

      var formData = $("#frmadd_Vendor").serialize();

      $.ajax({
          url: "<?php echo base_url('vendor/ajax_add_master_inline'); ?>",
          type: "POST",
          data: formData,
          dataType: "json",
          beforeSend: function () {
              $('input[name="btn_add_vendor"]')
                  .val("Saving...")
                  .prop("disabled", true);
          },
          success: function (response) {

              if (response.status === "success") {

                            // Get values for validation
                  let vendor_name = $('#vendor_name').val().trim(); 

                  
                  if (vendor_name === "") {
                    alert("Vendor Name is required.");
                    $('#vendor_name').focus();
                    return false;
                  }

                  // Create two separate options (append removes from original)
                  var option1 = new Option(response.name, response.id, true, true);
                  var option2 = new Option(response.name, response.id, true, true);

                  // Add vendor to main search dropdown
                  $("#srch_vendor_id").append(option1).trigger("change");

                  // Add vendor to contact dropdown
                  $("#contact_vendor_id").append(option2).trigger("change");

                  // Reset the form
                  $("#frmadd_Vendor")[0].reset();

                  // Close modal
                  $("#add_vendor").modal("hide");

                  // Success alert
                  $('#zazualert').html(`
                      <div class="alert alert-success sticky-notification" role="alert">
                          <button type="button" class="close" data-dismiss="alert">&times;</button>
                          <strong>Note:</strong>
                          <div class="note-content">${response.message}</div>
                      </div>
                  `);

              } else {
                  alert(response.message || "Failed to add vendor.");
              }
          },
          error: function (xhr, status, error) {
              alert("Error: " + error);
              console.error(xhr.responseText);
          },
          complete: function () {
              $('input[name="btn_add_vendor"]').val("Save").prop("disabled", false);
          }
      });
  });


  // Handle Add Contact Person form submission
  $('input[name="btn_add_contact"]').on("click", function (e) {
    e.preventDefault();


    // Validate vendor selection
    var vendorId = $("#contact_vendor_id").val();
    var contactName = $("#contact_person_name").val();
    
    if (!vendorId) {
      alert("Please select a vendor first!");
      return;
    }
    
    if (!contactName) {
      alert("Please enter contact person name!");
      return;
    }

    var formData = $("#frmadd_contact_person").serialize();

    $.ajax({
      url: "<?php echo base_url('vendor/ajax_add_master_inline'); ?>",
      type: "POST",
      data: formData,
      dataType: "json",
      beforeSend: function () {
        $('input[name="btn_add_contact"]')
          .val("Saving...")
          .prop("disabled", true);
      },
      success: function (response) {
  

        if (response.status === "success") {
          // Add new contact option dynamically
          var newOption = new Option(response.name, response.id, true, true);
          $("#srch_vendor_contact_id").append(newOption).trigger("change");

          // Reset the form
          $("#frmadd_contact_person")[0].reset();

          // Close the modal
          $("#add_vendor_contact_pereson").modal("hide");

          // Success message

           $('#zazualert').html(`
                    <div class="alert alert-success sticky-notification" role="alert">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Note:</strong>
                        <div class="note-content">`+ response.message + `</div>
                    </div>
                `);

        
        } else {
          alert(response.message || "Failed to add contact person.");
        }
      },
      error: function (xhr, status, error) {
        alert("Error: " + error);
        console.error(xhr.responseText);
      },
      complete: function () {
        $('input[name="btn_add_contact"]').val("Save").prop("disabled", false);
      }
    });
  });

});
</script>
</script>