<style>
    .text-light-blue { color: #3c8dbc !important; }
    fieldset { border: 1px solid #d2d6de; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    legend { font-size: 16px; font-weight: bold; width: auto; padding: 0 10px; }
    .mt-4 { margin-top: 1.5rem; }
    .mt-3 { margin-top: 1rem; }

    /* Card-style item row */
    .item-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        position: relative;
    }
    .item-card .form-group {
        margin-bottom: 12px;
    }
    .item-card label {
        font-weight: 600;
        color: #555;
        margin-bottom: 5px;
        display: block;
    }
    .remove-item {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .desc-textarea {
        min-height: 80px;
        resize: vertical;
    }
</style>

<script>
jQuery(function ($) {
    const categoryOpts = <?php echo json_encode(['' => 'Select Category'] + $category_opt); ?>;
    const uomOpts = <?php echo json_encode(['' => 'Select UOM'] + $uom_opt); ?>;

    // Build Category Dropdown
    function buildCategorySelect() {
        let html = '<select name="category_id[]" class="form-control category-select">';
        $.each(categoryOpts, function(val, text) {
            html += `<option value="${val}">${text}</option>`;
        });
        html += '</select>';
        return html;
    }

    // Build Item Select (empty)
    function buildItemSelect() {
        return '<select name="item_id[]" class="form-control item-select"><option value="">Select Item</option></select>';
    }

    // Build UOM Dropdown
    function buildUomSelect() {
        let html = '<select name="uom[]" class="form-control uom-select">';
        $.each(uomOpts, function(val, text) {
            html += `<option value="${val}">${text}</option>`;
        });
        html += '</select>';
        return html;
    }

    // Add Empty Item Card
    function addItemRow() {
        const rowHtml = `
            <div class="item-card">
                <button type="button" class="btn btn-danger btn-xs remove-item"><i class="fa fa-trash"></i></button>
                
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Category</label>
                            ${buildCategorySelect()}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Item</label>
                            ${buildItemSelect()}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="item_desc[]" class="form-control desc-textarea" rows="3" placeholder="Item description will auto-fill..."></textarea>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>UOM</label>
                            ${buildUomSelect()}
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>Qty</label>
                            <input type="number" step="0.01" name="qty[]" class="form-control" placeholder="0.00">
                        </div>
                    </div>
                </div>
            </div>`;
        $('#item_container').append(rowHtml);
    }

    // Company → Customer
    $("#srch_company_id").change(function () {
        const companyId = $(this).val();
        $.post("<?php echo site_url('tender/get_data');?>", {
            tbl: "get-company-customer-list",
            id: companyId
        }, function (d) {
            const $cust = $("#srch_customer_id");
            $cust.empty().append('<option value="">Select Customer</option>');
            $.each(d, function (i, v) {
                $cust.append(`<option value="${v.customer_id}">${v.customer_name}</option>`);
            });
            $('#item_container').empty();
            addItemRow();
        }, 'json');
    });

    // Category → Load Items
    $(document).on('change', '.category-select', function () {
        const $row = $(this).closest('.item-card');
        const $item = $row.find('.item-select');
        const catId = $(this).val();

        $item.empty().append('<option value="">Loading...</option>');

        if (catId) {
            $.post("<?php echo site_url('tender/get_data');?>", {
                tbl: 'get-category-item-list',
                id: catId
            }, function (data) {
                $item.empty().append('<option value="">Select Item</option>');
                $.each(data, function (i, item) {
                    $item.append(`<option value="${item.item_id}">${item.item_name}</option>`);
                });
            }, 'json');
        } else {
            $item.empty().append('<option value="">Select Item</option>');
        }
    });

    // Item → Load UOM & Description
    $(document).on('change', '.item-select', function () {
        const itemId = $(this).val();
        const $row = $(this).closest('.item-card');
        const $desc = $row.find('textarea[name="item_desc[]"]');
        const $uom = $row.find('.uom-select');

        if (itemId) {
            $.post("<?php echo site_url('tender/get_data');?>", {
                tbl: 'get-uom-desc-from-item',
                id: itemId
            }, function (data) {
                if (data && data[0]) {
                    $desc.val(data[0].item_description || '');
                    $uom.val(data[0].uom || '');
                }
            }, 'json');
        } else {
            $desc.val('');
            $uom.val('');
        }
    });

    // Add More
    $('#add_more').click(function () {
        addItemRow();
    });

    // Remove Row
    $(document).on('click', '.remove-item', function () {
        if ($('.item-card').length > 1) {
            $(this).closest('.item-card').remove();
        } else {
            alert('At least one item is required.');
        }
    });

    // Initialize
    addItemRow();
});
</script>
