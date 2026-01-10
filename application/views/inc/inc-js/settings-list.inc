 <script>
$(document).on('click', '.btn-edit', function () {

    let row = $(this).closest('tr');

    // Enable inputs
    row.find('.setting-input').prop('readonly', false);
    row.find('select.setting-input').prop('disabled', false);

    // Toggle buttons
    $(this).hide();
    row.find('.btn-save').show();
});
</script>
