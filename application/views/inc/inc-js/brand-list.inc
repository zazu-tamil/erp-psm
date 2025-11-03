<script>

  jQuery(function ($) {
    $(document).on("click", ".edit_record", function () {
      let id = $(this).val();
      $.ajax({
        url: "<?php echo site_url('get-data');?>",
        type: "post",
        data: { tbl: "brand_info", id: id },
        dataType: "json",
        success: function (d) {
          console.log(d); // Debugging: Log the response
          $("#edit_modal #brand_id").val(d.brand_id); 
          $("#edit_modal #brand_name").val(d.brand_name); 
          $('#edit_modal input:radio[value="' + d.status + '"]').prop(
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
          url: "<?php echo site_url('delete-record');?>",
          type: "post",
          data: { tbl: "brand_info", id: id },
          success: function (d) {
            alert(d);
            location.reload();
          },
        });
      }
    });
  });
  
</script>
