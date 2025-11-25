<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- jQuery UI -->
<link
  rel="stylesheet"
  href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"
/>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<style>
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

  /* === ITEM DETAILS GRID === */
  .item-details-container {
    border: 1px solid #d2d6de;
    border-radius: 6px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .grid-header,
  .item-row {
    display: grid;
    grid-template-columns: 22% 38% 12% 12% 10%;
    align-items: start;
    column-gap: 16px;
    row-gap: 12px;
    padding: 14px 16px;
    transition: background 0.2s;
  }

  .grid-header {
    background: #f4f6f9;
    font-weight: 600;
    color: #444;
    border-bottom: 2px solid #3c8dbc;
    font-size: 14px;
  }

  .item-row {
    border-bottom: 1px solid #eee;
  }

  .item-row:hover {
    background: #f9f9fb;
  }

  .item-row:last-child {
    border-bottom: none;
  }

  /* === Column Content === */
  .cat-item-block {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .cat-item-block select {
    height: 34px;
    font-size: 13px;
  }

  /* === DESCRIPTION: REDUCED HEIGHT === */
  .item-desc-block textarea {
    width: 100%;
    min-height: 54px; /* Reduced from 68px */
    height: 54px; /* Fixed small height */
    resize: vertical; /* Allow user to expand if needed */
    font-size: 13px;
    padding: 6px 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    line-height: 1.4;
  }

  .uom-block select,
  .qty-block input {
    width: 100%;
    font-size: 13px;
  }

  .action-block {
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding-top: 18px;
  }

  .action-block button {
    background: #dd4b39;
    color: white;
    border: none;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .action-block button:hover {
    background: #c23321;
    transform: scale(1.1);
  }

  /* === RESPONSIVE === */
  @media (max-width: 992px) {
    .grid-header,
    .item-row {
      grid-template-columns: 1fr;
      gap: 12px;
      padding: 12px;
    }

    .action-block {
      justify-content: flex-end;
      padding-top: 0;
    }

    .cat-item-block {
      gap: 10px;
    }

    .item-desc-block textarea {
      min-height: 60px;
      height: auto;
    }
  }
</style>
<script>
  jQuery(function ($) {
    $("#srch_customer_contact_id")
      .trigger("change")
      .val("#srch_customer_id")
      .trigger("change");

    // Company → Customer
    $("#srch_company_id").change(function () {
      const companyId = $(this).val();
      $.post(
        "<?php echo site_url('tender/get_data'); ?>",
        {
          tbl: "get-company-customer-list",
          id: companyId,
        },
        function (data) {
          const $cust = $("#srch_customer_id");
          $cust.empty().append('<option value="">Select Customer</option>');
          $.each(data, function (i, c) {
            $cust.append(
              `<option value="${c.customer_id}">${c.customer_name}</option>`
            );
          });
          $("#item_rows").empty();
          addEmptyRow();
        },
        "json"
      );
    });

    // Category → Item
    $(document).on("change", ".category-select", function () {
      const catId = $(this).val();
      const $row = $(this).closest(".item-row");
      const $item = $row.find(".item-select");

      if (!catId) {
        $item.html('<option value="">Select Item</option>');
        return;
      }

      $.post(
        "<?php echo site_url('tender/get_data'); ?>",
        {
          tbl: "get-category-item-list",
          id: catId,
        },
        function (data) {
          $item.html('<option value="">Select Item</option>');
          $.each(data, function (i, item) {
            $item.append(
              `<option value="${item.item_id}">${item.item_name}</option>`
            );
          });
        },
        "json"
      );
    });

    // Item → UOM + Description
    $(document).on("change", ".item-select", function () {
      const itemId = $(this).val();
      const $row = $(this).closest(".item-row");
      const $desc = $row.find('textarea[name="item_desc[]"]');
      const $uom = $row.find('select[name="uom[]"]');

      if (!itemId) {
        $desc.val("");
        $uom.val("");
        return;
      }

      $.post(
        "<?php echo site_url('tender/get_data'); ?>",
        {
          tbl: "get-uom-desc-from-item",
          id: itemId,
        },
        function (data) {
          if (data && data[0]) {
            $desc.val(data[0].item_description || "");
            $uom.val(data[0].uom || "");
          }
        },
        "json"
      );
    });

    // Initialize Autocomplete for the first row
    initAutocomplete();

    // Add More Button Click
    $("#add_more").on("click", function () {
      addEmptyRow();
    });

    // Remove Row
    $(document).on("click", ".remove-row", function () {
      if ($(".item-row").length > 1) {
        $(this).closest(".item-row").remove();
      } else {
        alert("At least one item is required.");
      }
    });

    // Add Empty Row
    function addEmptyRow() {
      let row = `
        <div class="item-row">

            <div class="cat-item-block">
                <input type="text" name="srch_item[]" class="form-control srch_item" autocomplete="off" placeholder="Search Item">
                <input type="hidden" name="item_id[]" class="item_id">
                <input type="hidden" name="category_id[]" class="category_id">
            </div>

            <div class="item-desc-block">
                <textarea name="item_desc[]" class="form-control" rows="5" placeholder="Description"></textarea>
            </div>

            <div class="uom-block">
                <?php echo form_dropdown('uom[]', ['' => '—'] + $uom_opt, '', 'class="form-control uom_select"'); ?>
            </div>

            <div class="qty-block">
                <input type="number" step="0.01" name="qty[]" class="form-control" placeholder="Qty">
            </div>

            <div class="action-block">
                <button type="button" class="btn btn-danger remove-row">
                    <i class="fa fa-trash"></i>
                </button>
            </div>

        </div>
    `;

      $("#item_rows").append(row);
      initAutocomplete();
    }

    // Autocomplete Initialization
    function initAutocomplete() {
      $(".srch_item").autocomplete({
        source: function (request, response) {
          $.ajax({
            url: "<?php echo base_url('tender/item_search'); ?>",
            type: "POST",
            data: { search: request.term },
            dataType: "json",
            success: function (data) {
              response(data);
            },
          });
        },

        minLength: 1,

        select: function (event, ui) {
          let row = $(this).closest(".item-row");

          $(this).val(ui.item.value);
          row.find('textarea[name="item_desc[]"]').val(ui.item.desc);
          row.find(".item_id").val(ui.item.id);
          row.find(".category_id").val(ui.item.category_id);
          row.find(".uom_select").val(ui.item.uom);

          return false;
        },
      });
    }

    $('input[name="btn_add_customer"]').on("click", function (e) {
      e.preventDefault();

      // ---- REQUIRED FIELD VALIDATION ----
      let customerName = $.trim($("#customer_name").val());
      if (customerName === "") {
        alert("Customer Name is required.");
        $("#customer_name").focus();
        return false;
      }

      let formData = $("#frmadd_Customer").serialize();

      // Disable button
      $('input[name="btn_add_customer"]')
        .val("Saving...")
        .prop("disabled", true);

      $.ajax({
        url: "<?php echo site_url('tender/ajax_add_master_inline'); ?>",
        type: "POST",
        data: formData,
        dataType: "json",

        success: function (response) {
          if (response.status === "success") {
            // Add newly created customer to dropdowns
            $("#srch_customer_id")
              .append(new Option(response.name, response.id, true, true))
              .trigger("change");

            $("#customer_id_two")
              .append(new Option(response.name, response.id, true, true))
              .trigger("change");

            // Reset form
            $("#frmadd_Customer")[0].reset();

            // Auto-close modal smoothly
            $("#add_customer").find(".close").trigger("click");

            // Success alert
            $("#zazualert").html(`
                      <div class="alert alert-success sticky-notification" role="alert">
                          <button type="button" class="close" data-dismiss="alert">&times;</button>
                          <strong>Note:</strong>
                          <div class="note-content">${response.message}</div>
                      </div>
                  `);
          } else {
            toastr.error(response.message || "Failed to add customer.");
          }
        },

        error: function () {
          toastr.error("Server error occurred.");
        },

        complete: function () {
          $('input[name="btn_add_customer"]')
            .val("Save")
            .prop("disabled", false);
        },
      });
    });

    $('input[name="btn_add_brand"]').on("click", function (e) {
      e.preventDefault();

      var formData = $("#frmadd_Brand").serializeArray();

      console.log(formData);

      $.ajax({
        url: '<?php echo site_url("tender/ajax_add_master_inline"); ?>',
        type: "POST",
        data: formData,
        dataType: "json",
        beforeSend: function () {
          $('input[name="btn_add_brand"]')
            .val("Saving...")
            .prop("disabled", true);
        },
        success: function (response) {
          console.log(response);
          if (response.status === "success") {
            // Add new option to the dropdown dynamically
            var newOption = new Option(response.name, response.id, true, true);
            $("#brand_id").append(newOption).trigger("change");

            // Reset form & close modal
            $("#frmadd")[0].reset();
            $("#add_customer").find(".close").trigger("click");

            // Optional success alert
            //toastr.success(response.message);
            alert(response.message);
          } else {
            //toastr.error('Failed to add brand.');
            alert("Failed to add brand.");
          }
        },
        error: function (xhr, status, error) {
          //toastr.error('Error: ' + error);
          alert("Error: " + error);
        },
        complete: function () {
          $('input[name="btn_add_brand"]').val("Save").prop("disabled", false);
        },
      });
    });

 

    $('input[name="btn_add_customer_contact"]').on("click", function (e) {
      e.preventDefault();

      // Disable button (avoid double save)
      $('input[name="btn_add_customer_contact"]')
        .val("Saving...")
        .prop("disabled", true);

      var formData = $("#frmadd_Customer_Contact").serializeArray();

      // Required validation
      var contactName = $('input[name="contact_person_name"]').val().trim();
      var customer_id = $("#customer_id_two").val(); // FIXED

      if (contactName === "") {
        alert("Contact Person Name is required.");
        $('input[name="contact_person_name"]').focus();
        enableButton();
        return false;
      }

      if (!customer_id) {
        alert("Customer Name is required.");
        $("#customer_id_two").focus();
        enableButton();
        return false;
      }

      $.ajax({
        url: '<?php echo site_url("tender/ajax_add_master_inline"); ?>',
        type: "POST",
        data: formData,
        dataType: "json",

        success: function (response) {
          if (response.status === "success") {
            // Append newly added contact to dropdown
            $("#srch_customer_contact_id")
              .append(new Option(response.name, response.id, true, true))
              .trigger("change");

            // Reset form
            $("#frmadd_Customer_Contact")[0].reset();

            // Auto-close modal using close button (smooth fade)
            $("#add_customer_contact_id").find(".close").trigger("click");

            // Success alert
            $("#zazualert").html(`
                      <div class="alert alert-success sticky-notification" role="alert">
                          <button type="button" class="close" data-dismiss="alert">&times;</button>
                          <strong>Note:</strong>
                          <div class="note-content">${response.message}</div>
                      </div>
                  `);
          } else {
            alert("Failed to add contact person.");
          }
        },

        error: function (xhr, status, error) {
          alert("Error: " + error);
        },

        complete: function () {
          enableButton();
        },
      });

      // Function to restore the button state
      function enableButton() {
        $('input[name="btn_add_customer_contact"]')
          .val("Save")
          .prop("disabled", false);
      }
    });

    $('input[name="btn_add_category"]').on("click", function (e) {
      e.preventDefault();

      var formData = $("#frmadd_Category").serialize(); // simpler & cleaner

      $.ajax({
        url: '<?php echo site_url("tender/ajax_add_master_inline"); ?>',
        type: "POST",
        data: formData,
        dataType: "json",
        beforeSend: function () {
          $('input[name="btn_add_category"]')
            .val("Saving...")
            .prop("disabled", true);
        },
        success: function (response) {
          console.log(response);

          if (response.status === "success") {
            // Add new category to all dropdowns dynamically
            $(".category-select").each(function () {
              var exists = $(this).find(
                "option[value='" + response.id + "']"
              ).length;
              if (!exists) {
                var newOption = new Option(
                  response.name,
                  response.id,
                  false,
                  false
                );
                $(this).append(newOption);
              }
            });

            // Select the newly added category in the last row
            $(".item-row:last .category-select")
              .val(response.id)
              .trigger("change");

            $("#add_item .category-select").val(response.id).trigger("change");

            // Reset modal form & close modal
            $("#frmadd_Category")[0].reset();
            $("#add_category").modal("hide");

            // Success message
            alert(response.message);
            // toastr.success(response.message);
          } else {
            alert(response.message || "Failed to add category.");
          }
        },
        error: function (xhr, status, error) {
          alert("Error: " + error);
        },
        complete: function () {
          $('input[name="btn_add_category"]')
            .val("Save")
            .prop("disabled", false);
        },
      });
    });

    $('input[name="btn_add_item"]').on("click", function (e) {
      e.preventDefault();

      // Get values for validation
      let itemName = $('input[name="item_name"]').val().trim();
      let itemCode = $('input[name="item_code"]').val().trim();

      // ---- REQUIRED FIELD CHECK ----
      if (itemName === "") {
        alert("Item Name is required.");
        $('input[name="item_name"]').focus();
        return false;
      }

      if (itemCode === "") {
        alert("Item Code is required.");
        $('input[name="item_code"]').focus();
        return false;
      }

      var formData = $("#frmadd_Item").serialize();

      $('input[name="btn_add_item"]').val("Saving...").prop("disabled", true);

      $.ajax({
        url: '<?php echo site_url("tender/ajax_add_master_inline"); ?>',
        type: "POST",
        data: formData,
        dataType: "json",

        success: function (response) {
          if (response.status === "success") {
            // Add item in all dropdowns without duplication
            $(".item-select").each(function () {
              if (!$(this).find("option[value='" + response.id + "']").length) {
                $(this).append(
                  new Option(response.name, response.id, false, false)
                );
              }
            });

            // Auto-select last row item dropdown
            $(".item-row:last .item-select").val(response.id).trigger("change");

            // Reset form + auto close modal
            $("#frmadd_Item")[0].reset();
            $("#add_item").find(".close").trigger("click");

            // Success
            // Success alert
            $("#zazualert").html(`
                      <div class="alert alert-success sticky-notification" role="alert">
                          <button type="button" class="close" data-dismiss="alert">&times;</button>
                          <strong>Note:</strong>
                          <div class="note-content">${response.message}</div>
                      </div>
                  `);
          } else {
            alert(response.message || "Failed to add item.");
          }
        },

        error: function (xhr, status, error) {
          alert("Error: " + error);
        },

        complete: function () {
          $('input[name="btn_add_item"]').val("Save").prop("disabled", false);
        },
      });
    });

 
  });
</script>
<style>
  .form-control[name="item_desc[]"] {
    height: 80px !important;
  }
</style>
