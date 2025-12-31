<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- jQuery UI -->
<link
  rel="stylesheet"
  href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"
/>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

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
    const rowTemplate = `
    <div class="item-row">
        <div class="cat-item-block">
            <input type="text" name="item_code[]" class="form-control item_code"> 
        </div>
        <div class="item-desc-block">
            <textarea name="item_desc[]" class="form-control item_desc" rows="5"></textarea>
        </div>
        <div class="uom-block">
             <input type="text" name="uom[]" class="form-control uom">
        </div>
        <div class="qty-block">
            <input type="number" step="0.01" name="qty[]" class="form-control qty">
        </div>
        <div class="action-block">
            <button type="button" class="remove-row"><i class="fa fa-trash"></i></button>
        </div>
    </div>`;

    $("#excelFile").on("change", function (e) {
      const file = e.target.files[0];
      const reader = new FileReader();

      reader.onload = function (e) {
        const workbook = XLSX.read(e.target.result, { type: "binary" });
        const sheet = workbook.Sheets[workbook.SheetNames[0]];
        const rows = XLSX.utils.sheet_to_json(sheet); // JSON with headers

        $("#item_rows").empty(); // Clear previous rows

        rows.forEach(function (r) {
          let newRow = $(rowTemplate);
          //alert(r.item_desc);
          newRow.find(".item_code").val(r.item_code);
          newRow.find(".item_desc").val(r.item_desc);
          newRow.find(".uom").val(r.uom); // Should match option value
          newRow.find(".qty").val(r.qty);

          $("#item_rows").append(newRow);
        });
      };

      reader.readAsBinaryString(file);
    });

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
        },
        "json"
      );
    });

    // Remove Row
    $(document).on("click", ".remove-row", function () {
      if ($("#item_rows .item-row").length > 0) {
        $(this).closest(".item-row").remove();
      } else {
        alert("At least one item is required.");
      }
    });

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

    $("#srch_customer_id").on("change", function () {
      let v = $(this).val();
      $("#customer_id_two").val(v); // select same value
    });
    $("#srch_customer_id").on("change", function () {
      let v = $(this).val();

      $('select[name="customer_id"]').each(function () {
        if ($(this).attr("id") !== "srch_customer_id") {
          $(this).val(v);
        }
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

    $("#srch_customer_id").change(function () {
      var customerId = $(this).val();

      // Reset contact dropdown
      var $contact = $("#srch_customer_contact_id");
      $contact.html('<option value="">Select Contact Person</option>');

      if (!customerId) return;

      $.ajax({
        url: "<?php echo site_url('tender/get_data'); ?>",
        type: "POST",
        data: { tbl: "get-customer-contacts", id: customerId },
        dataType: "json",
        success: function (data) {
          $.each(data, function (i, item) {
            $contact.append(
              $("<option>")
                .val(item.customer_contact_id)
                .text(item.contact_person_name)
            );
          });
        },
        error: function (xhr) {
          console.error(xhr.responseText);
          alert("Failed to load contact persons");
        },
      });
    });

    $(document).on("keyup", "#item_search_modal", function () {
      let searchText = $(this).val().trim();

      $.ajax({
        url: "<?php echo base_url('tender/item_search_v2'); ?>",
        type: "POST",
        data: { search: searchText, srch_typ: "code" },
        dataType: "json",
        success: function (data) {
          let tbody = $("#item_search_table_modal tbody");
          tbody.empty(); // clear table

          if (data.length === 0) {
            tbody.append(
              `<tr><td colspan="5" class="text-center">No Records Found</td></tr>`
            );
            return;
          }

          $.each(data, function (i, item) {
            tbody.append(`
                          <tr>
                              <td>${item.item_code}</td>
                              <td>${item.item_desc}</td>
                              <td>${item.uom}</td>
                              <td>${item.tbl}</td>
                              <td>${item.po_date}</td>
                              <td>
                                  <button type="button" class="btn btn-success btn-sm select_item_btn"
                                      data-item='${JSON.stringify(item)}'>
                                      Select
                                  </button>
                              </td>
                          </tr>
                      `);
          });
        },
      });
    });

    $(document).on("keyup", "#item_desc_modal", function () {
      let searchText = $(this).val().trim();

      $.ajax({
        url: "<?php echo base_url('tender/item_search_v2'); ?>",
        type: "POST",
        data: { search: searchText, srch_typ: "desc" },
        dataType: "json",
        success: function (data) {
          let tbody = $("#item_search_table_modal tbody");
          tbody.empty(); // clear table

          if (data.length === 0) {
            tbody.append(
              `<tr><td colspan="5" class="text-center">No Records Found</td></tr>`
            );
            return;
          }

          $.each(data, function (i, item) {
            tbody.append(`
                          <tr>
                              <td>${item.item_code}</td>
                              <td>${item.item_desc}</td>
                              <td>${item.uom}</td>
                              <td>${item.tbl}</td>
                              <td>${item.po_date}</td>
                              <td>
                                  <button type="button" class="btn btn-success btn-sm select_item_btn"
                                      data-item='${JSON.stringify(item)}'>
                                      Select
                                  </button>
                              </td>
                          </tr>
                      `);
          });
        },
      });
    });

    $(document).on("click", ".select_item_btn", function () {
      let item = $(this).data("item");

      let row = `

                <div class="item-row">
                    <div class="cat-item-block"> 
                        <input type="text" name="item_code[]" class="form-control item_code" value="${item.item_code}"> 
                        <button type="button" class="btn btn-link" data-toggle="modal"
                                              data-target="#select_item_modal"><i class="fa fa-search"></i></button>
                    </div>
                    <div class="item-desc-block">
                        <textarea name="item_desc[]" class="form-control" rows="5">${item.item_desc}</textarea>
                    </div>
                    <div class="uom-block"> 
                         <input type="text"  name="uom[]" class="form-control" value="${item.uom}">
                    </div>
                    <div class="qty-block">
                        <input type="number" step="0.01" name="qty[]" class="form-control" value="1">
                    </div>
                    <div class="action-block">
                        <button type="button" class="remove-row"><i class="fa fa-trash"></i></button>
                    </div>
                </div> 
            `;

      $("#item_rows").append(row);

      $("#select_item_modal .btn_cancel").click();
      //$("#select_item_modal").modal("hide");
    });
  });
</script>
<style>
  .form-control[name="item_desc[]"] {
    height: 80px !important;
  }
</style>
<script>
  $("#item_image").on("change", function () {
    var simgFile = this.files[0]; // get file object
    var simgURL = simgFile ? URL.createObjectURL(simgFile) : "";

    if (simgURL) {
      $("#item_image_preview").attr("src", simgURL).show();
    } else {
      $("#item_image_preview").hide();
    }
  });

  $(document).ready(function () {
    // Add Row on button click
    $("#add_more_added").on("click", function () {
      addEmptyRow();
    });

    // Remove Row
    $(document).on("click", ".remove-row", function () {
      if ($("#item_rows_add .item-row").length > 1) {
        $(this).closest(".item-row").remove();
      } else {
        alert("At least one item is required.");
      }
    });

    // Function to add new row
    function addEmptyRow() {
      const row = `
            <div class="item-row">
                <div class="cat-item-block">
                    <input type="text" name="item_code[]" class="form-control">
                </div>

                <div class="item-desc-block">
                    <textarea name="item_desc[]" class="form-control" rows="5"></textarea>
                </div>

                <div class="uom-block">
                    <input type="text" name="uom[]" class="form-control">
                </div>

                <div class="qty-block">
                    <input type="number" step="0.01" name="qty[]" class="form-control">
                </div>

                <div class="action-block">
                    <button type="button" class="remove-row">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        `;

      $("#item_rows_add").append(row);

      // If you have autocomplete or other JS init
      if (typeof initAutocomplete === "function") {
        initAutocomplete();
      }
    }
  });
</script>
