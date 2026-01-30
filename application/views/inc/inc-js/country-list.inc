<script>

  jQuery(function ($) {
    $(document).on("click", ".edit_record", function () {
      let id = $(this).val();
      $.ajax({
        url: "<?php echo site_url('get-data');?>",
        type: "post",
        data: { tbl: "country_info", id: id },
        dataType: "json",
        success: function (d) {
         
          $("#edit_modal #country_id").val(d.country_id); 
          $("#edit_modal #country_name").val(d.country_name); 
          $('#edit_modal input:radio[value="' + d.status + '"]').prop("checked",true);

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
          data: { tbl: "country_info", id: id },
          success: function (d) {
            alert(d);
            location.reload();
          },
        });
      }
    });
  });
  
</script>
