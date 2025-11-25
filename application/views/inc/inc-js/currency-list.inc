<script>
  jQuery(function ($) {
    $(document).on("click", ".edit_record", function () {
      let id = $(this).val();
      $.ajax({
        url: "<?php echo site_url('get-data');?>",
        type: "post",
        data: { tbl: "currency_info", id: id },
        dataType: "json",
        success: function (d) {
          $("#edit_modal #currency_id").val(d.currency_id);
          $("#edit_modal #currency_name").val(d.currency_name);
          $("#edit_modal #symbol").val(d.symbol);
          $("#edit_modal #country_name").val(d.country_name);
          $("#edit_modal #currency_code").val(d.currency_code);
          $("#edit_modal #exchange_rate").val(d.exchange_rate);
          $('#edit_modal input:radio[value="' + d.status + '"]').prop(
            "checked",
            true
          );
          $('#edit_modal input:radio[value="' + d.is_base_currency + '"]').prop(
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

    $(".del_record").click(function () {
      if (confirm("Are you sure you want to delete this tender enquiry?")) {
        const id = $(this).val();
        //alert(id);
        $.post(
          "<?php echo site_url('delete-record'); ?>",
          {
            tbl: "currencies_info",
            id: id,
          },
          function (res) {
            alert(res);
            location.reload();
          }
        ).fail(function () {
          alert("Error deleting record.");
        });
      }
    });
  });
</script>
