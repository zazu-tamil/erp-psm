<script>
  $("#frmadd").submit(function () {
    let debit = parseFloat($("#debit").val()) || 0;
    let credit = parseFloat($("#credit").val()) || 0;

    if (debit <= 0 && credit <= 0) {
      alert("Enter either Debit or Credit amount");
      return false;
    }

    if (debit > 0 && credit > 0) {
      alert("Enter only one: Debit OR Credit");
      return false;
    }
  });
</script>
<script>
  jQuery(function ($) {
    $(document).on("click", ".edit_record", function () {
      let id = $(this).val();
      //alert(id);
      $.ajax({
        url: "<?php echo site_url('adit/get_data'); ?>",
        type: "post",
        data: { tbl: "vouchers_entries_list", id: id },
        dataType: "json",
        success: function (d) {
          // console.log(d);

          $("#edit_modal #entry_id").val(d.entry_id);
          $("#edit_modal #voucher_id").val(d.voucher_id);
          $("#edit_modal #ledger_id").val(d.ledger_id);
          $("#edit_modal #debit").val(d.debit);
          $("#edit_modal #credit").val(d.credit);
          $(
            '#edit_modal #edit_modal input:radio[value="' + d.status + '"]'
          ).prop("checked", true);

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
          url: "<?php echo site_url('adit/delete_record'); ?>",
          type: "post",
          data: { tbl: "vouchers_entries_list", id: id },
          success: function (d) {
            alert(d);
            location.reload();
          },
        });
      }
    });
  });
</script>
