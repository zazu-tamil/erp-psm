<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link href="https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"/>
<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<script>
jQuery(function($) {    

    // Initialize DataTable safely
    if ($.fn.DataTable.isDataTable("#voucher_type_list")) {
        $("#voucher_type_list").DataTable().destroy();
    }

    var table = $("#voucher_type_list").DataTable({
        paging: true,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: true
    });

    // ---------------------------
    // Edit Voucher Type
    // ---------------------------
    $(document).on("click", ".edit_record", function() {
        let $row = $(this).closest('tr');

        let c_name   = $row.find('td').eq(1).text();
        let c_prefix = $row.find('td').eq(2).text();
        let c_type   = $row.find('td').eq(3).text();
        let c_status = $row.find('td').eq(4).text();

        let $modal = $("#edit_modal");
        $modal.find("#voucher_type_id").val($(this).val());
        $modal.find("#voucher_type_name").val(c_name);
        $modal.find("#prefix").val(c_prefix);
        $modal.find('input:radio[name="type"][value="'+c_type+'"]').prop('checked', true);
        $modal.find('input:radio[name="status"][value="'+c_status+'"]').prop('checked', true);

        $modal.modal('show');
    });

    // ---------------------------
    // Delete Voucher Type
    // ---------------------------
    $(document).on("click", ".del_record", function() {
        let id = $(this).val();
        let $row = $(this).closest('tr');

        if(confirm("Are you sure you want to delete this voucher type?")) {
            $.ajax({
                url: "<?= site_url('delete-record'); ?>",
                type: "POST",
                data: { tbl: 'voucher_type_info', id: id },
                success: function(d) {
                    alert(d);
                    table.row($row).remove().draw(false); // Remove row from DataTable without reload
                },
                error: function(xhr, status, error) {
                    alert("Error deleting: " + error);
                }
            });
        }
    });

});
</script>
