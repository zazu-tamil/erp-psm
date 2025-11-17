<script>
$(document).ready(function () {
  
  // Load vendor contacts when vendor is selected
 $("#srch_vendor_id").on("change", function () {
    const vendor_id = $(this).val();

    // Auto-set vendor in Add Contact Person modal
    $("#contact_vendor_id").val(vendor_id);

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
            $("#srch_vendor_contact_id").html('<option>No contacts found</option>');
          }
        }
      });
    }
  });

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
          if (res.length > 0) {
            $.each(res, function (i, row) {
              const itemHtml = `
                <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="text-primary mb-0"><i class="fa fa-cube"></i> Item Details ${i + 1}</h5>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="${i}" id="check_${i}">
                            <label class="form-check-label" for="check_${i}">Select</label>
                        </div>
                    </div>
                    <input type="hidden" name="tender_enquiry_item_id[]" value="${row.tender_enquiry_item_id}">
                    <input type="hidden" name="category_id[]" value="${row.category_id}">
                    <input type="hidden" name="item_id[]" value="${row.item_id}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Category Name</label>
                                        <input type="text" name="category_name[]" class="form-control" value="${row.category_name}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Item Name</label>
                                        <input type="text" name="item_name[]" class="form-control" value="${row.item_name}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Description</label>
                                <textarea name="item_desc[]" class="form-control desc-textarea" style="height: 120px;" readonly>${row.item_desc}</textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>UOM</label>
                                        <input type="text" name="uom[]" class="form-control" value="${row.uom}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Quantity</label>
                                        <input type="number" step="0.01" name="qty[]" class="form-control" value="${row.qty}" readonly>
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
          // Add new vendor option dynamically
          var newOption = new Option(response.name, response.id, true, true);
          $("#srch_vendor_id").append(newOption).trigger("change");

          // Reset the form
          $("#frmadd_Vendor")[0].reset();

          // Close the modal
          $("#add_vendor").modal("hide");

           $('#zazualert').html(`
                    <div class="alert alert-success sticky-notification" role="alert">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Note:</strong>
                        <div class="note-content">`+ response.message + `</div>
                    </div>
                `);


          // Success message
          
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