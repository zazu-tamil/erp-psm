<script>
  jQuery(function ($) {
    // Delete Record AJAX
    $(document).on("click", ".del_record", function () {
      let id = $(this).val();
      if (confirm("Are you sure you want to delete?")) {
        $.ajax({
          url: "<?php echo site_url('audit/delete_record'); ?>",
          type: "post",
          data: { tbl: "vouchers_list", id: id },
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
    // ================= ADD ROW =================
    $("#addRow").on("click", function () {
      let row = `
            <tr>
                <td>
                    <?= form_dropdown('ledger_id[]', array('' => 'Select Ledger Account') + $ledger_accounts_list_opt, '', 'class="form-control ledger-select" required'); ?>
                </td>
                <td>
                    <input type="number" step="0.01" name="debit[]" class="form-control debit" value="0.00">
                </td>
                <td>
                    <input type="number" step="0.01" name="credit[]" class="form-control credit" value="0.00">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm removeRow">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>`;

      $("#addVoucherTable tbody").append(row);
    });

    // ================= REMOVE ROW =================
    $(document).on("click", ".removeRow", function () {
      if ($("#addVoucherTable tbody tr").length > 1) {
        $(this).closest("tr").remove();
        calculateTotals();
      } else {
        alert("At least one row is required.");
      }
    });

    // ================= AUTO ZERO RULE =================
    $(document).on("input", ".debit", function () {
      let tr = $(this).closest("tr");
      if (parseFloat($(this).val()) > 0) {
        tr.find(".credit").val("0.00");
      }
      calculateTotals();
    });

    $(document).on("input", ".credit", function () {
      let tr = $(this).closest("tr");
      if (parseFloat($(this).val()) > 0) {
        tr.find(".debit").val("0.00");
      }
      calculateTotals();
    });

    // ================= TOTAL CALCULATION =================
    function calculateTotals() {
      let totalDebit = 0;
      let totalCredit = 0;

      $(".debit").each(function () {
        totalDebit += parseFloat($(this).val()) || 0;
      });

      $(".credit").each(function () {
        totalCredit += parseFloat($(this).val()) || 0;
      });

      $("#addTotalDebit").text(totalDebit.toFixed(2));
      $("#addTotalCredit").text(totalCredit.toFixed(2));

      if (totalDebit.toFixed(2) !== totalCredit.toFixed(2)) {
        $("#balanceWarning").show();
      } else {
        $("#balanceWarning").hide();
      }
    }

    // ================= PREVENT INVALID SUBMIT =================
    $("#frmadd").on("submit", function (e) {
      let totalDebit = parseFloat($("#addTotalDebit").text()) || 0;
      let totalCredit = parseFloat($("#addTotalCredit").text()) || 0;

      if (totalDebit <= 0 || totalCredit <= 0) {
        alert("Debit and Credit must be greater than 0");
        e.preventDefault();
        return false;
      }

      if (totalDebit.toFixed(2) !== totalCredit.toFixed(2)) {
        alert("Debit and Credit totals must be equal!");
        e.preventDefault();
        return false;
      }

      $("#saveVoucherBtn")
        .prop("disabled", true)
        .html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    });
  });
</script>
<script>
  $(document).ready(function () {

      let ledgerAccountsData = <?= json_encode($ledger_accounts_list_opt, JSON_HEX_APOS | JSON_HEX_QUOT); ?>;


      function buildLedgerOptionsHTML(selectedId = '') {
          let html = '<option value="">Select Ledger Account</option>';
          for (let id in ledgerAccountsData) {
              let selected = (id == selectedId) ? 'selected' : '';
              let name = ledgerAccountsData[id];
              html += `<option value="${id}" ${selected}>${name}</option>`;
          }
          return html;
      }


      $(document).on("click", ".btn_edit_voucher", function () {

          let voucher_id = $(this).val();
          //alert(voucher_id);

          $.ajax({
              url: "<?= site_url('Audit/get_voucher'); ?>",
              type: "POST",
              dataType: "json",
              data: { voucher_id: voucher_id },
              success: function (res) {
                 // console.log(res);
                  if (res.status === 'success') {

                      let v = res.voucher;


                      $('#edit_modal #edit_voucher_id').val(v.voucher_id);
                      $('#edit_modal #voucher_date').val(v.voucher_date);
                      $('#edit_modal #voucher_type').val(v.voucher_type);
                      $('#edit_modal #status').val(v.status);
                      $('#edit_modal #narration').val(v.narration);


                      $('#editTableBody').html('');


                      $.each(res.entries, function (i, row) {

                          let tr = `
                      <tr>
                          <td>
                              <input type="hidden" name="entry_id[]" class="entry_id" value="${row.entry_id}">
                              <select name="ledger_id[]" class="form-control ledger-select" required>
                                  ${buildLedgerOptionsHTML(row.ledger_id)}
                              </select>
                          </td>
                          <td>
                              <input type="number" step="0.01" name="debit[]" class="form-control debit" value="${row.debit}">
                          </td>
                          <td>
                              <input type="number" step="0.01" name="credit[]" class="form-control credit" value="${row.credit}">
                          </td>
                          <td class="text-center">
                              <button type="button" class="btn btn-danger btn-sm  btn_remove_entry_db" data-entry-id="${row.entry_id}">
                                  <i class="fa fa-trash"></i>
                              </button>
                          </td>
                      </tr>`;

                          $('#editTableBody').append(tr);
                      });

                      calculateEditTotals();
                      $('#edit_modal').modal('show');
                  }
              }
          });
      });



      $('#editAddRow').on('click', function () {

          let row = `
      <tr>
          <td>
              <input type="hidden" name="entry_id[]" class="entry_id" value="">
              <select name="ledger_id[]" class="form-control ledger-select" required>
                  ${buildLedgerOptionsHTML()}
              </select>
          </td>
          <td>
              <input type="number" step="0.01" name="debit[]" class="form-control debit" value="0.00">
          </td>
          <td>
              <input type="number" step="0.01" name="credit[]" class="form-control credit" value="0.00">
          </td>
          <td class="text-center">
              <button type="button" class="btn btn-danger btn-sm removeEditRow">
                  <i class="fa fa-trash"></i>
              </button>
          </td>
      </tr>`;

          $('#editTableBody').append(row);
      });



      $(document).on('click', '.removeEditRow', function () {
          if ($('#editTableBody tr').length > 1) {
              $(this).closest('tr').remove();
              calculateEditTotals();
          } else {
              alert("At least one row is required.");
          }
      });

      $(document).on('input', '#editVoucherTable .debit', function () {
          let tr = $(this).closest('tr');
          if (parseFloat($(this).val()) > 0) tr.find('.credit').val('0.00');
          calculateEditTotals();
      });

      $(document).on('input', '#editVoucherTable .credit', function () {
          let tr = $(this).closest('tr');
          if (parseFloat($(this).val()) > 0) tr.find('.debit').val('0.00');
          calculateEditTotals();
      });

      function calculateEditTotals() {

          let totalDebit = 0;
          let totalCredit = 0;

          $('#editVoucherTable .debit').each(function () {
              totalDebit += parseFloat($(this).val()) || 0;
          });

          $('#editVoucherTable .credit').each(function () {
              totalCredit += parseFloat($(this).val()) || 0;
          });

          $('#editTotalDebit').text(totalDebit.toFixed(2));
          $('#editTotalCredit').text(totalCredit.toFixed(2));

          if (totalDebit.toFixed(2) !== totalCredit.toFixed(2)) {
              $('#editBalanceWarning').show();
          } else {
              $('#editBalanceWarning').hide();
          }
      }

      $('#frmedit').on('submit', function (e) {

          let totalDebit = parseFloat($('#editTotalDebit').text()) || 0;
          let totalCredit = parseFloat($('#editTotalCredit').text()) || 0;

          if (totalDebit <= 0 || totalCredit <= 0) {
              alert("Debit and Credit must be greater than 0");
              e.preventDefault();
              return false;
          }

          if (totalDebit.toFixed(2) !== totalCredit.toFixed(2)) {
              alert("Debit and Credit totals must be equal!");
              e.preventDefault();
              return false;
          }

          $('#updateVoucherBtn').prop('disabled', true)
              .html('<i class="fa fa-spinner fa-spin"></i> Updating...');
      });


      $(document).on('click', '.btn_remove_entry_db', function () {

          let btn = $(this);
          let entry_id = btn.data('entry-id');

          if (!entry_id) {
              alert("Entry ID not found");
              return;
          }

          if (confirm("Are you sure you want to delete this entry?")) {

              $.ajax({
                  url: "<?= site_url('audit/delete_record'); ?>",
                  type: "POST",
                  dataType: "json",
                  data: {
                      tbl: "vouchers_entries_list",
                      id: entry_id
                  },
                  success: function (res) {

                      if (res.status === 'success') {

                          let rowCount = $('#editTableBody tr').length;

                          if (rowCount > 1) {
                              btn.closest('tr').remove();
                              calculateEditTotals();
                          } else {
                              alert("At least one row is required.");
                          }

                      } else {
                          alert(res.message || "Delete failed");
                      }
                  },
                  error: function (xhr, status, error) {
                      alert("Server error: " + error);
                  }
              });
          }
      });


  });
</script>
