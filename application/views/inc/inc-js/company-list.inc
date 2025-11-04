<script>

  jQuery(function ($) {
    $(document).on("click", ".edit_record", function () {
      let id = $(this).val();
      $.ajax({
        url: "<?php echo site_url('get-data');?>",
        type: "post",
        data: { tbl: "company_info", id: id },
        dataType: "json",
        success: function (d) {
          console.log(d); // Debugging: Log the response
          $("#edit_modal #company_id").val(d.company_id);
          $("#edit_modal #company_name").val(d.company_name);
          $("#edit_modal #contact_name").val(d.contact_name);
          $("#edit_modal #country").val(d.country);
          $("#edit_modal #email").val(d.email);
          $("#edit_modal #crno").val(d.crno);
          $("#edit_modal #address").val(d.address);
          $("#edit_modal #mobile").val(d.mobile);
          $("#edit_modal #GST").val(d.GST);
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
          data: { tbl: "company_info", id: id },
          success: function (d) {
            alert(d);
            location.reload();
          },
        });
      }
    });
  });
  
</script>
<script>
  $(document).ready(function () {
    let total = parseInt("<?php echo $total_records; ?>");

    // Set value for display
    $("#user_limet").text(total);

    // Show "Add" button only if total_records == 0
    if (total === 0) {
      $("#btn_add_new").show();
    } else {
      $("#btn_add_new").hide();
    }
  }); 
</script>