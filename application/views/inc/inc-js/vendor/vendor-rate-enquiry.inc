<script>
  $(document).ready(function () {
    $("#srch_tender_enquiry_id").on("change", function () {
      const tender_enquiry_id = $(this).val();
      $("#item_container").html("");
      if (tender_enquiry_id) {
        $.ajax({
          url: '<?php echo site_url("vendor/get_data"); ?>',
          type: "POST",
          data: {
            tbl: "get-tender-enquiry-item-list-rate",
            id: tender_enquiry_id,
          },
          dataType: "json",
          success: function (res) {
            if (res.length > 0) {
              $.each(res, function (i, row) {
                const itemHtml = `
                  <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
                      <div class="d-flex justify-content-between align-items-center mb-2">
                          <h5 class="text-primary mb-0"><i class="fa fa-cube"></i> Item Details ${
                            i + 1
                          }</h5>
                          <div class="form-check">
                              <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="${i}" id="check_${i}">
                              <label class="form-check-label" for="check_${i}">Select</label>
                          </div>
                      </div>
                      <input type="hidden" name="tender_enquiry_item_id[]" value="${
                        row.tender_enquiry_item_id
                      }">
                      <input type="hidden" name="category_id[]" value="${
                        row.category_id
                      }">
                      <input type="hidden" name="item_id[]" value="${
                        row.item_id
                      }">
                      <div class="row">
                          <div class="col-md-4">
                              <div class="row">
                                  <div class="col-md-12">
                                      <div class="form-group">
                                          <label>Category Name</label>
                                          <input type="text" name="category_name[]" class="form-control" value="${
                                            row.category_name
                                          }" readonly>
                                      </div>
                                  </div>
                                  <div class="col-md-12">
                                      <div class="form-group">
                                          <label>Item Name</label>
                                          <input type="text" name="item_name[]" class="form-control" value="${
                                            row.item_name
                                          }" readonly>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label>Item Description</label>
                                <textarea name="item_desc[]" class="form-control desc-textarea" style="height: 120px;" readonly>${
                                  row.item_desc
                                }</textarea>

                              </div>
                          </div>
                          <div class="col-md-2">
                              <div class="row">
                                  <div class="col-md-12">
                                      <div class="form-group">
                                          <label>UOM</label>
                                          <input type="text" name="uom[]" class="form-control" value="${
                                            row.uom
                                          }" readonly>
                                      </div>
                                  </div>
                                  <div class="col-md-12">
                                      <div class="form-group">
                                          <label>Quantity</label>
                                          <input type="number" step="0.01" name="qty[]" class="form-control" value="${
                                            row.qty
                                          }" readonly>
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
        });
      }
    });
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
          console.log(response);

          if (response.status === "success") {
            // Add new vendor option dynamically
            var newOption = new Option(response.name, response.id, true, true);
            $("#srch_vendor_id").append(newOption).trigger("change");

            // Reset the form
            $("#frmadd_Vendor")[0].reset();

            // Close the modal
            $("#add_vendor").modal("hide");

            // Optional success message
            // toastr.success(response.message);
            alert(response.message);
          } else {
            // toastr.error(response.message || 'Failed to add vendor.');
            alert(response.message || "Failed to add vendor.");
          }
        },
        error: function (xhr, status, error) {
          // toastr.error('Error: ' + error);
          alert("Error: " + error);
          console.error(xhr.responseText);
        },
        complete: function () {
          $('input[name="btn_add_vendor"]').val("Save").prop("disabled", false);
        },
      });
    });
  });
</script>
