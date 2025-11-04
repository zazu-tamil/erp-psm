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

    // Add Row
    $("#add_more").click(addEmptyRow);

    // Remove Row
    $(document).on("click", ".remove-row", function () {
      if ($("#item_rows .item-row").length > 1) {
        $(this).closest(".item-row").remove();
      } else {
        alert("At least one item is required.");
      }
    });

    // Add Empty Row
    function addEmptyRow() {
      const row = `
            <div class="item-row">
                <div class="cat-item-block">
                    <?php echo form_dropdown('category_id[]', ['' => 'Select Category'] + $category_opt, '', 'class="form-control category-select"'); ?>
                    <select name="item_id[]" class="form-control item-select mt-2">
                        <option value="">Select Item</option>
                    </select>
                </div>
                <div class="item-desc-block">
                    <textarea name="item_desc[]" class="form-control" rows="5" placeholder="Description auto-fills..."></textarea>
                </div>
                <div class="uom-block">
                    <?php echo form_dropdown('uom[]', ['' => '—'] + $uom_opt, '', 'class="form-control"'); ?>
                </div>
                <div class="qty-block">
                    <input type="number" step="0.01" name="qty[]" class="form-control" placeholder="0.00" min="0">
                </div>
                <div class="action-block">
                    <button type="button" class="remove-row">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>`;
      $("#item_rows").append(row);
    }

    // Initialize
    addEmptyRow();
  });
</script>
<style>
  .form-control[name="item_desc[]"] {
    height:80px !important;
  }
</style>
