<script>
  jQuery(function ($) {
    $(document).on("click", ".edit_record", function () {
      let id = $(this).val();

      $.ajax({
        url: "<?php echo site_url('audit/get_data'); ?>",
        type: "post",
        data: { tbl: "vouchers_entries_list", id: id },
        dataType: "json",
        success: function (d) {
          $("#edit_modal #entry_id").val(d.entry_id);
          $("#edit_modal #voucher_id").val(d.voucher_id);
          $("#edit_modal #ledger_id").val(d.ledger_id);

          // Detect payment type automatically
          if (parseFloat(d.debit) > 0) {
            $("#edit_modal #payment_type").val("Debit");
            $("#edit_modal #debit").val(d.debit);
            $("#edit_modal #credit").val("");

            $("#edit_modal #debit_div").show();
            $("#edit_modal #credit_div").hide();
          } else {
            $("#edit_modal #payment_type").val("Credit");
            $("#edit_modal #credit").val(d.credit);
            $("#edit_modal #debit").val("");

            $("#edit_modal #credit_div").show();
            $("#edit_modal #debit_div").hide();
          }

          // Status
          $(
            '#edit_modal input:radio[name="status"][value="' + d.status + '"]'
          ).prop("checked", true);

          $("#edit_modal").modal("show");
        },
        error: function (xhr) {
          console.error("AJAX Error:", xhr.responseText);
        },
      });
    });

    // Delete Record AJAX
    $(document).on("click", ".del_record", function () {
      let id = $(this).val();
      if (confirm("Are you sure you want to delete?")) {
        $.ajax({
          url: "<?php echo site_url('audit/delete_record'); ?>",
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
<script>
  $(document).ready(function () {
    function togglePayment() {
      var type = $("#payment_type").val();

      if (type === "Debit") {
        $("#debit_div").show();
        $("#credit_div").hide();
        $("#credit").val("");
      } else {
        $("#credit_div").show();
        $("#debit_div").hide();
        $("#debit").val("");
      }
    }

    // Initial load
    togglePayment();

    // On change
    $("#payment_type").change(function () {
      togglePayment();
    });
  });
</script>
<script>
  $(document).ready(function () {
    function togglePaymentEdit(type) {
      if (type === "Debit") {
        $("#edit_modal #debit_div").show();
        $("#edit_modal #credit_div").hide();
        $("#edit_modal #credit").val("");
      } else {
        $("#edit_modal #credit_div").show();
        $("#edit_modal #debit_div").hide();
        $("#edit_modal #debit").val("");
      }
    }

    // Change event
    $("#edit_modal #payment_type").on("change", function () {
      togglePaymentEdit($(this).val());
    });
  });
</script>
