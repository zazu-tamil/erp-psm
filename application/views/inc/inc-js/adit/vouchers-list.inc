<script>
  jQuery(function ($) {
    $(document).on("click", ".edit_record", function () {
      let id = $(this).val();
      alert(id);
      $.ajax({
        url: "<?php echo site_url('adit/get_data'); ?>",
        type: "post",
        data: { tbl: "vouchers_list", id: id },
        dataType: "json",
        success: function (d) {
          console.log(d);
          $("#edit_modal #voucher_id").val(d.voucher_id);
          $("#edit_modal #voucher_date").val(d.voucher_date);
          $("#edit_modal #voucher_type").val(d.voucher_type);
          $("#edit_modal #narration").val(d.narration);

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
          url: "<?php echo site_url('adit/delete_record'); ?>",
          type: "post",
          data: { tbl: "vouchers_list", id: id },
          success: function (d) {
            alert(d);
            location.reload();
          },
        });
      }
    });
  });
</script>
