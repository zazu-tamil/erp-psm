 <style>
    .text-light-blue { color: #3c8dbc !important; }
    fieldset { border: 1px solid #d2d6de; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
    legend { font-size: 16px; font-weight: bold; width: auto; padding: 0 12px; }

    .item-details-container {
        border: 1px solid #d2d6de;
        border-radius: 6px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .grid-header, .item-row {
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

    .item-row { border-bottom: 1px solid #eee; }
    .item-row:hover { background: #f9f9fb; }
    .item-row:last-child { border-bottom: none; }

    .cat-item-block { display: flex; flex-direction: column; gap: 8px; }
    .cat-item-block select { height: 34px; font-size: 13px; }

    .item-desc-block textarea {
        width: 100%;
        min-height: 68px;
        height: 68px;
        resize: vertical;
        font-size: 13px;
        line-height: 1.5;
        padding: 8px 10px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background: #fdfdff;
    }

    .item-desc-block textarea:focus {
        border-color: #3c8dbc;
        box-shadow: 0 0 0 2px rgba(60,141,188,0.2);
        outline: none;
    }

    .uom-block select, .qty-block input {
        width: 100%;
        font-size: 13px;
        height: 34px;
    }

    .action-block {
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 18px;
    }

    .btn-remove-row {
        background: #dd4b39;
        color: white;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-remove-row:hover {
        background: #c23321;
        transform: scale(1.12);
    }

    @media (max-width: 992px) {
        .grid-header, .item-row {
            grid-template-columns: 1fr;
            gap: 14px;
            padding: 12px;
        }
        .action-block { justify-content: flex-end; padding-top: 0; }
    }
</style>

<script>
jQuery(function($){
    const catOpt = <?php echo $category_json; ?>;
    const uomOpt = <?php echo $uom_json; ?>;
    const siteUrl = "<?php echo site_url('tender/get_data'); ?>";

  

    // Populate items for existing rows on page load
    $('.item-row').each(function(idx){
        const $row = $(this);
        const catId = parseInt($row.data('category-id')) || 0;
        const itemId = parseInt($row.data('item-id')) || 0;
        const itemName = $row.data('item-name') || '';

      

        if(catId > 0 && itemId > 0){
            loadItemsForCategory($row, catId, itemId, itemName);
        }
    });

    // Load items for a category
    function loadItemsForCategory($row, catId, selectedItemId, selectedItemName){
        $.ajax({
            url: siteUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                tbl: 'get-category-item-list',
                id: catId
            },
            success: function(items){
                const $itemSelect = $row.find('.item-select');
                $itemSelect.html('<option value="">Select Item</option>');
                
                let itemFound = false;
                if(items && items.length > 0){
                    $.each(items, function(i, item){
                        const isSelected = (item.item_id == selectedItemId);
                        if(isSelected) itemFound = true;
                        
                        $itemSelect.append(
                            `<option value="${item.item_id}" ${isSelected ? 'selected' : ''}>${item.item_name}</option>`
                        );
                    });
                }

                // If item not found in list, add it as selected option
                if(selectedItemId > 0 && !itemFound){
                    $itemSelect.append(`<option value="${selectedItemId}" selected>${selectedItemName}</option>`);
                }

               
            },
            error: function(xhr, status, error){
                console.error('Error loading items:', error);
            }
        });
    }

    // Category selection change
    $(document).on('change', '.category-select', function(){
        const $row = $(this).closest('.item-row');
        const catId = parseInt($(this).val()) || 0;

        $row.find('.item-select').html('<option value="">Select Item</option>');
        $row.find('textarea[name="item_desc[]"]').val('');
        $row.find('select[name="uom[]"]').val('');

        if(catId > 0){
            loadItemsForCategory($row, catId, 0, '');
        }
    });

    // Item selection change
    $(document).on('change', '.item-select', function(){
        const $row = $(this).closest('.item-row');
        const itemId = parseInt($(this).val()) || 0;

        if(itemId > 0){
            $.ajax({
                url: siteUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    tbl: 'get-uom-desc-from-item',
                    id: itemId
                },
                success: function(data){
                    if(data && data.length > 0){
                        const itemData = data[0];
                        $row.find('textarea[name="item_desc[]"]').val(itemData.item_description || '');
                        $row.find('select[name="uom[]"]').val(itemData.uom || '');
                    }
                },
                error: function(xhr, status, error){
                    console.error('Error loading item details:', error);
                }
            });
        } else {
            $row.find('textarea[name="item_desc[]"]').val('');
            $row.find('select[name="uom[]"]').val('');
        }
    });

    // Add new row
    $('#add_more').on('click', function(){
        const newRow = `
            <div class="item-row" data-rec-id="0">
                <div class="cat-item-block">
                    <select name="category_id[]" class="form-control category-select">
                        <option value="">Select Category</option>
                        ${Object.entries(catOpt).filter(([id]) => id !== '').map(([id, name]) => 
                            `<option value="${id}">${name}</option>`
                        ).join('')}
                    </select>
                    <select name="item_id[]" class="form-control item-select mt-2">
                        <option value="">Select Item</option>
                    </select>
                </div>
                <div class="item-desc-block">
                    <textarea name="item_desc[]" class="form-control" rows="5"></textarea>
                </div>
                <div class="uom-block">
                    <select name="uom[]" class="form-control">
                        ${Object.entries(uomOpt).map(([v, t]) => `<option value="${v}">${t}</option>`).join('')}
                    </select>
                </div>
                <div class="qty-block">
                    <input type="number" step="0.01" name="qty[]" class="form-control" placeholder="0.00" min="0" value="0">
                </div>
                <div class="action-block">
                    <button type="button" class="btn-remove-row">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
                <input type="hidden" name="tender_enquiry_item_id[]" value="0">
            </div>`;

        $('#item_rows').append(newRow);
    
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

    // Remove row
    $(document).on('click', '.btn-remove-row', function(e){
        e.preventDefault();
        const rowCount = $('#item_rows .item-row').length;
        
        if(rowCount > 1){
            $(this).closest('.item-row').remove();
          
        } else {
            alert('At least one item is required.');
        }
    });


});
</script>