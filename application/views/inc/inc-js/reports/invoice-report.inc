<script>
  $(document).ready(function () {
    // Handle Export Excel button click
    $(document).on("click", "#btn-export-excel", function (e) {
      e.preventDefault();
      $("#export_excel").val("1");
      $("#frmsearch").submit();
      // Reset back to 0 so subsequent standard filters do not trigger excel export
      setTimeout(function () {
        $("#export_excel").val("0");
      }, 500);
    });
  });
</script>
