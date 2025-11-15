<script>
  jQuery(function ($) {
    // Edit Record
    $(document).on("click", ".edit_record", function () {
      let id = $(this).val();
      $.ajax({
        url: "<?php echo site_url('get-data'); ?>",
        type: "POST",
        data: { tbl: "vendor_contact_info", id: id },
        dataType: "json",
        success: function (d) {
          $("#edit_modal #vendor_contact_id").val(d.vendor_contact_id);
          $("#edit_modal #vendor_id").val(d.vendor_id);
          $("#edit_modal #contact_person_name").val(d.contact_person_name);
          $("#edit_modal #department").val(d.department);
          $("#edit_modal #designation").val(d.designation);
          $("#edit_modal #mobile").val(d.mobile);
          $("#edit_modal #email").val(d.email);
          $("#edit_modal #address").val(d.address);
          $(`#edit_modal input[name="status"][value="${d.status}"]`).prop(
            "checked",
            true
          );
          $("#edit_modal").modal("show");
        },
        error: function () {
          alert("Error loading data.");
        },
      });
    });

    // Delete Record
    $(document).on("click", ".del_record", function () {
      let id = $(this).val();
      if (confirm("Delete this contact?")) {
        $.ajax({
          url: "<?php echo site_url('delete-record'); ?>",
          type: "POST",
          data: { tbl: "vendor_contact_info", id: id },
          success: function (res) {
            alert(res);
            location.reload();
          },
        });
      }
    });
  });
</script>
