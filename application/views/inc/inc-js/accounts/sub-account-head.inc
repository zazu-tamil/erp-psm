
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
    if ($.fn.DataTable.isDataTable("#sub_account_head_list")) {
        $("#sub_account_head_list").DataTable().destroy();
    }

    var table = $("#sub_account_head_list").DataTable({
        paging: true,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: true
    });

    // ---------------------------
    // Populate Account Head based on type (Add Modal)
    // ---------------------------
    $(document).on("change", "#add_modal #type", function() {
        let typeId = $(this).val();
        let $accountHead = $("#add_modal #account_head_id");
        $accountHead.empty(); // Remove existing options

        $.ajax({
            url: "<?= site_url('get-data'); ?>",
            type: "POST",
            data: { tbl: 'get-account-head-type', id: typeId },
            dataType: "json",
            success: function(data) {
                $accountHead.append($('<option>', { value: '', text: 'Select Account Head' }));
                $.each(data, function(key, info) {
                    $accountHead.append($('<option>', { value: info.account_head_id, text: info.account_head_name }));
                });
            },
            error: function(xhr, status, error) {
                alert("Error fetching account heads: " + error);
            }
        });
    });

    // ---------------------------
    // Edit sub-account head
    // ---------------------------
    $(document).on("click", ".edit_record", function() {
        let subId = $(this).val();
        let $editModal = $("#edit_modal");

        $.ajax({
            url: "<?= site_url('get-data'); ?>",
            type: "POST",
            data: { tbl: 'sub_account_head_info', id: subId },
            dataType: "json",
            success: function(d1) {
                $editModal.find("#sub_account_head_id").val(d1.sub_account_head_id);
                $editModal.find("#type").val(d1.type);
                $editModal.find("#sub_account_head_name").val(d1.sub_account_head_name);
                $editModal.find('input:radio[name="status"][value="'+d1.status+'"]').prop('checked', true);
                $editModal.find("#ac_table").prop('checked', d1.ac_table == '1');

                // Populate Account Head dropdown based on type
                let $accountHead = $editModal.find("#account_head_id");
                $accountHead.empty();

                $.ajax({
                    url: "<?= site_url('get-data'); ?>",
                    type: "POST",
                    data: { tbl: 'get-account-head-type', id: d1.type },
                    dataType: "json",
                    success: function(data) {
                        $accountHead.append($('<option>', { value: '', text: 'Select Account Head' }));
                        $.each(data, function(key, info) {
                            let option = $('<option>', { value: info.account_head_id, text: info.account_head_name });
                            if (d1.account_head_id == info.account_head_id) option.attr('selected', true);
                            $accountHead.append(option);
                        });
                    }
                });

                $editModal.modal('show');
            },
            error: function(xhr, status, error) {
                alert("Error fetching sub-account head: " + error);
            }
        });
    });

    // ---------------------------
    // Delete sub-account head
    // ---------------------------
    $(document).on("click", ".del_record", function() {
        let id = $(this).val();
        let row = $(this).closest('tr');

        if (confirm("Are you sure you want to delete this sub-account head?")) {
            $.ajax({
                url: "<?= site_url('accounts/delete_record'); ?>",
                type: "POST",
                data: { tbl: 'sub_account_head_info', id: id },
                success: function(response) {
                    alert(response);
                    // Remove row using DataTables API
                    table.row(row).remove().draw(false);
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert("Error deleting record: " + error);
                }
            });
        }
    });

});
</script>
