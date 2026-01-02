<script>
  jQuery(function ($) {
    // Delete Record AJAX
    $(document).on("click", ".del_record", function () {
      let id = $(this).val();
      //alert(id);
      if (confirm("Are you sure you want to delete?")) {
        $.ajax({
          url: "<?php echo site_url('adit/delete_record');?>",
          type: "post",
          data: { tbl: "account_groups", id: id },
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
  $("#add_modal #nature").on("change", function () {
    const nature = $(this).val();

    if (nature !== "") {
      load_nature(nature, 0, "#add_modal");
    } else {
      $("#add_modal #parent_group")
        .empty()
        .append('<option value="">Select Parent Group</option>');
    }
  });

  function load_nature(nature, selected_id = 0, modal_id) {
    $.ajax({
      url: "<?php echo site_url('adit/get_data'); ?>",
      type: "POST",
      data: {
        tbl: "account_groups_parent_list",
        id: nature,
      },
      dataType: "json",
      success: function (data) {
        const $parent = $(modal_id + " #parent_group");
        $parent.empty().append('<option value="">Select Parent Group</option>');

        $.each(data, function (i, c) {
          let selected = c.group_id == selected_id ? "selected" : "";
          $parent.append(
            `<option value="${c.group_id}" ${selected}>${c.group_name}</option>`
          );
        });
      },
      error: function (xhr) {
        console.error("Parent Load Error:", xhr.responseText);
      },
    });
  }

  $(document).on("click", ".edit_record", function () {
    let id = $(this).val(); // group_id

    $.ajax({
      url: "<?php echo site_url('adit/get_data'); ?>",
      type: "POST",
      data: {
        tbl: "account_groups",
        id: id,
      },
      dataType: "json",
      success: function (d) {
        $("#edit_modal #group_id").val(d.group_id);
        $("#edit_modal #group_name").val(d.group_name);
        $("#edit_modal #nature").val(d.nature);
        $("#edit_modal #sequence").val(d.sequence);

        $("#edit_modal input[name='status'][value='" + d.status + "']").prop(
          "checked",
          true
        );

        load_nature(d.nature, d.parent_group, "#edit_modal");

        $("#edit_modal").modal("show");
      },
      error: function (xhr) {
        console.error("Edit Load Error:", xhr.responseText);
      },
    });
  });

  $("#edit_modal #nature").on("change", function () {
    const nature = $(this).val();

    if (nature !== "") {
      load_nature(nature, 0, "#edit_modal");
    } else {
      $("#edit_modal #parent_group")
        .empty()
        .append('<option value="">Select Parent Group</option>');
    }
  });
</script>
