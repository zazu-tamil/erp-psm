<link
	rel="stylesheet"
	href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"
/>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link
	href="https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
/>
<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<script>
jQuery(function($) {    

    // ✅ Initialize DataTable safely
    if ($.fn.DataTable.isDataTable("#account_head_list")) {
        $("#account_head_list").DataTable().destroy();
    }

    var table = $("#account_head_list").DataTable({
        paging: true,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: true
    });

    // ✅ Edit account head (delegated for dynamic rows)
    $(document).on("click", ".edit_record", function() {
        var row = $(this).closest('tr');
        var c_name = row.find('td').eq(1).text().trim();
        var c_type = row.find('td').eq(2).text().trim();
        var c_ac = row.find('td').eq(3).text().trim();
        var c_status = row.find('td').eq(4).text().trim();

        $('#edit_modal #account_head_id').val($(this).val());
        $('#edit_modal #account_head_name').val(c_name);
        $('#edit_modal input:radio[value="'+ c_type +'"]').prop('checked', true);
        $('#edit_modal #ac_table').prop('checked', c_ac === 'Yes');
        $('#edit_modal input:radio[name="status"][value="'+ c_status +'"]').prop('checked', true);

        // Show modal
        $('#edit_modal').modal('show');
    });

    // ✅ Delete account head (delegated for dynamic rows)
    $(document).on("click", ".del_record", function() {
        var id = $(this).val();
        var row = $(this).closest('tr');

        if (confirm("Are you sure you want to delete this account head?")) {
            $.ajax({
                url: "<?= site_url('delete-record'); ?>",
                type: "POST",
                data: { tbl: 'account_head_info', id: id },
                success: function(response) {
                    alert(response);

                    // Remove row using DataTables API to keep table intact
                    table.row(row).remove().draw(false);
                },
                error: function(xhr, status, error) {
                    alert("Error deleting record: " + error);
                }
            });
        }
    });

});
</script>
