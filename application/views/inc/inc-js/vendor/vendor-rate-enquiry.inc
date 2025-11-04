<script>
  $(document).ready(function () {
    // When tender enquiry changes
    $("#srch_tender_enquiry_id").on("change", function () {
      const tender_enquiry_id = $(this).val();

      // Clear previous items
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
                          <h5 class="text-primary mb-0"><i class="fa fa-cube"></i> Item Details ${i + 1}</h5>
                          <div class="form-check">
                              <input type="checkbox" class="form-check-input item-check" name="selected_items[]" value="${i}" id="check_${i}">
                              <label class="form-check-label" for="check_${i}">Select</label>
                          </div>
                      </div>
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
                                  <textarea name="item_desc[]" class="form-control desc-textarea" rows="3" readonly>${row.item_desc}</textarea>
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
        });
      }
    });

    // Before submitting form - only send checked item values
    $("form").on("submit", function (e) {
      $(".item-card").each(function () {
        const isChecked = $(this).find(".item-check").is(":checked");
        if (!isChecked) {
          $(this).find("input, textarea, select").prop("disabled", true);
        }
      });
    });
  });
</script>
