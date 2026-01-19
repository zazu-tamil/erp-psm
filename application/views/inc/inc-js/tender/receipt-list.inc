<script>
  $(document).ready(function () {
    $("#payment_mode").on("change", function () {
      const mode = $(this).val();
      if (mode === "Bank") {
        $("#bank_ledger_wrap").show();
        $("#bank_ledger_id").prop("required", true);
      } else {
        $("#bank_ledger_wrap").hide();
        $("#bank_ledger_id").prop("required", false).val("");
      }
    });

    // Initialize on load (in case browser keeps old values)
    $("#payment_mode").trigger("change");
  });
</script>
