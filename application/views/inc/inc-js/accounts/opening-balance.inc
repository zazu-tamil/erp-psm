<!-- Select2 -->
<script src="<?php echo base_url() ?>asset/bower_components/select2/dist/js/select2.full.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/select2/dist/css/select2.min.css">
<!-- DataTables -->
<link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<script src="<?php echo base_url() ?>asset/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>asset/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<style>
.select2-container { width: 100% !important; }
.select2-dropdown { z-index: 99999999 !important; }
</style>

<script>
jQuery(function($) {

    if ($.fn.select2) {
        $("#add_bank_id, #edit_bank_id, #add_cash_category_id, #edit_cash_category_id").select2({
            width: "100%"
        });
    }

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
        autoWidth: true,
        columnDefs: [
            { orderable: false, targets: [6, 7] }
        ],
        language: {
            emptyTable: "No opening balance records found."
        }
    });

    // ---------------------------
    // Account Group Toggle
    // ---------------------------
    function toggleAccountGroup(prefix, group) {
        if (group === "Bank") {
            $("#" + prefix + "_bank_wrapper").show();
            $("#" + prefix + "_bank_id").prop("required", true);

            $("#" + prefix + "_cash_wrapper").hide();
            $("#" + prefix + "_cash_category_id").prop("required", false).val("").trigger("change");
        } else if (group === "Cash") {
            $("#" + prefix + "_cash_wrapper").show();
            $("#" + prefix + "_cash_category_id").prop("required", true);

            $("#" + prefix + "_bank_wrapper").hide();
            $("#" + prefix + "_bank_id").prop("required", false).val("").trigger("change");
        } else {
            $("#" + prefix + "_bank_wrapper").hide();
            $("#" + prefix + "_bank_id").prop("required", false).val("").trigger("change");
            $("#" + prefix + "_cash_wrapper").hide();
            $("#" + prefix + "_cash_category_id").prop("required", false).val("").trigger("change");
        }
    }

    $("#add_ac_type").on("change", function() {
        toggleAccountGroup("add", $(this).val());
    });

    $("#edit_ac_type").on("change", function() {
        toggleAccountGroup("edit", $(this).val());
    });

    $("#add_modal").on("show.bs.modal", function() {
        $("#frmadd")[0].reset();
        $("#add_ac_type").val("").trigger("change");
    });

    // ---------------------------
    // Edit Opening Balance
    // ---------------------------
    $(document).on("click", ".edit_record", function() {
        let id = $(this).val();

        $.ajax({
            url: "<?= site_url('accounts/get_data'); ?>",
            type: "POST",
            data: { tbl: 'opening_balance_info', id: id },
            dataType: "json",
            success: function(d) {
                let $modal = $("#edit_modal");
                $modal.find("#edit_opening_balance_id").val(d.opening_balance_id);
                $modal.find("#edit_opening_date").val(d.opening_date);
                $modal.find("#edit_ac_type").val(d.ac_type);

                toggleAccountGroup("edit", d.ac_type);

                if (d.ac_type === "Bank" && d.bank_id) {
                    $modal.find("#edit_bank_id").val(d.bank_id).trigger("change");
                } else if (d.ac_type === "Cash" && d.cash_category_id) {
                    $modal.find("#edit_cash_category_id").val(d.cash_category_id).trigger("change");
                }

                $modal.find("#edit_amount").val(d.amount);
                $modal.find("#edit_remarks").val(d.remarks || "");

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
