<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link href="https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"/>
<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<script>
jQuery(function($) {

    // Initialize DataTable safely
    if ($.fn.DataTable.isDataTable("#opening_balance_list")) {
        $("#opening_balance_list").DataTable().destroy();
    }

    var table = $("#opening_balance_list").DataTable({
        paging: true,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: true
    });

    // ---------------------------
    // Edit Opening Balance
    // ---------------------------
    $(document).on("click", ".edit_record", function() {
        let id = $(this).val();

        $.ajax({
            url: "<?= site_url('get-data'); ?>",
            type: "POST",
            data: { tbl: 'opening_balance_info', id: id },
            dataType: "json",
            success: function(d) {
                let $modal = $("#edit_modal");
                $modal.find("#opening_balance_id").val(d.opening_balance_id);
                $modal.find("#opening_date").val(d.opening_date);
                $modal.find("#ac_type").val(d.ac_type);
                $modal.find("#amount").val(d.amount);

                $modal.modal('show');
            },
            error: function(xhr, status, error) {
                alert("Error fetching data: " + error);
            }
        });
    });

    // ---------------------------
    // Delete Opening Balance
    // ---------------------------
    $(document).on("click", ".del_record", function() {
        let id = $(this).val();
        let $row = $(this).closest('tr');

        if (confirm("Are you sure you want to delete this opening balance?")) {
            $.ajax({
                url: "<?= site_url('accounts/delete_record'); ?>",
                type: "POST",
                data: { tbl: 'cb_opening_balance_info_delet', id: id },
                success: function(d) {
                    alert(d);
                    // Optional: remove row without reload
                    table.row($row).remove().draw(false);
                     location.reload();
                },
                error: function(xhr, status, error) {
                    alert("Error deleting: " + error);
                }
            });
        }
    });

});
</script>
