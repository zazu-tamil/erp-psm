
<script>
    $(document).ready(function () {

         function calcAddAmount() {
            var qty = parseFloat($('#add_qty').val()) || 0;
            var rate = parseFloat($('#add_rate').val()) || 0;
            var vat = parseFloat($('#add_vat').val()) || 0;
            var base = qty * rate;
            var total = base + (base * vat / 100);
            $('#add_amount').val(total.toFixed(2));
        }

        $(document).on('keyup change', '#add_qty, #add_rate, #add_vat', function () {
            calcAddAmount();
        });
 
        function calcEditAmount() {
            var qty = parseFloat($('#edit_qty').val()) || 0;
            var rate = parseFloat($('#edit_rate').val()) || 0;
            var vat = parseFloat($('#edit_vat').val()) || 0;
            var base = qty * rate;
            var total = base + (base * vat / 100);
            $('#edit_amount').val(total.toFixed(2));
        }

        $(document).on('keyup change', '#edit_qty, #edit_rate, #edit_vat', function () {
            calcEditAmount();
        });

        /* ---- Reset Add modal on open ---- */
        $('#add_modal').on('show.bs.modal', function () {
            $('#frmadd')[0].reset();
            $('#add_amount').val('');
        });

        /* ---- Edit record – fetch data via AJAX ---- */
        $(document).on('click', '.edit_record', function () {
            var id = $(this).val();
            $.ajax({
                url: "<?php echo site_url('get-data'); ?>",
                type: 'post',
                data: { tbl: 'in_stock_item_info', id: id },
                dataType: 'json',
                success: function (d) {
                    $('#edit_in_stock_item_id').val(d.in_stock_item_id);
                    $('#edit_item_code').val(d.item_code);
                    $('#edit_uom').val(d.uom);
                    $('#edit_item_desc').val(d.item_desc);
                    $('#edit_qty').val(d.qty);
                    $('#edit_rate').val(d.rate);
                    $('#edit_vat').val(d.vat);
                    $('#edit_amount').val(d.amount);
                    $('#edit_vendor_id').val(d.vendor_id).trigger('change');
                    $('#edit_modal input:radio[name="status"][value="' + d.status + '"]').prop('checked', true);
                    $('#edit_modal').modal('show');
                },
                error: function (xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                }
            });
        });

        /* ---- Delete record ---- */
        $(document).on('click', '.del_record', function () {
            var id = $(this).val();
            if (confirm('Are you sure you want to delete?')) {
                $.ajax({
                    url: "<?php echo site_url('delete-record'); ?>",
                    type: 'post',
                    data: { tbl: 'in_stock_item_info', id: id },
                    success: function (d) {
                        alert(d);
                        location.reload();
                    }
                });
            }
        });

    });
</script>