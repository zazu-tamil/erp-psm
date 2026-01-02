
<script>

    jQuery(function ($) {
        $(document).on("click", ".edit_record", function () {
            let id = $(this).val();
           // alert(id);
            $.ajax({
                url: "<?php echo site_url('audit/get_data'); ?>",
                type: "post",
                data: { tbl: "ledger_accounts", id: id },
                dataType: "json",
                success: function (d) {
                    console.log(d);
                    $("#edit_modal #ledger_id").val(d.ledger_id);
                    $("#edit_modal #ledger_name").val(d.ledger_name);
                    $("#edit_modal #group_id").val(d.group_id);
                    $("#edit_modal #opening_balance").val(d.opening_balance);
                    $('#edit_modal input:radio[value="' + d.status + '"]').prop(
                        "checked",
                        true
                    );
                    $('#edit_modal input:radio[value="' + d.opening_type + '"]').prop(
                        "checked",
                        true
                    );

                    $("#edit_modal").modal("show");
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", xhr.responseText);
                },
            });
        });

        // Delete Record AJAX
        $(document).on("click", ".del_record", function () {
            let id = $(this).val();
            if (confirm("Are you sure you want to delete?")) {
                $.ajax({
                    url: "<?php echo site_url('audit/delete_record'); ?>",
                    type: "post",
                    data: { tbl: "ledger_accounts", id: id },
                    success: function (d) {
                        alert(d);
                        location.reload();
                    },
                });
            }
        });
    });

</script>