<script>
  jQuery(function ($) {
    // Delete Confirmation
    $(".del_record").click(function () {
      if (
        confirm(
          "Are you sure you want to mark this vendor rate enquiry as DELETED?\n\nIt will no longer appear in active lists."
        )
      ) {
        const id = $(this).val();
        $.post(
          "<?php echo site_url('vendor/delete_record'); ?>",
          {
            tbl: "vendor_rate_enquiry_info",
            id: id,
          },
          function (res) {
            alert(res);
            location.reload();
          }
        ).fail(function () {
          alert("Error processing request.");
        });
      }
    });
    $("#srch_customer_id").on("change", function () {
      const customer_id = $(this).val();
      const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $container = $("#item_container");

      // Reset dependent dropdown and items
      $enquiryDropdown
        .html('<option value="">All</option>')
        .prop("disabled", true);
      $container.html("");

      if (!customer_id) return;

      $.ajax({
        url: "<?php echo site_url('vendor/get_vendor_rate_enquiries_by_customer'); ?>",
        type: "POST",
        data: {
          customer_id: customer_id,
        },
        dataType: "json",
        success: function (res) {
          if (res.length > 0) {
            $enquiryDropdown.prop("disabled", false);
            $.each(res, function (i, row) {
              $enquiryDropdown.append(
                $("<option></option>")
                  .attr("value", row.tender_enquiry_id)
                  .text(row.display)
              );
            });
          } else {
            $enquiryDropdown.html(
              '<option value="">No enquiries found</option>'
            );
          }
        },
        error: function () {
          alert("Error loading enquiries");
        },
      });
    });
  });
</script>
