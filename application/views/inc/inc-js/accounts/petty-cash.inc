<script>
  $(document).ready(function () {
    // Initialize Select2 if available
    if ($.fn.select2) {
      $(".select2").select2({ width: "100%" });
    }

    $("#add_ac_type").change(function () {
      if ($(this).val() === "Bank") {
        $("#add_bank_group").show();
        $("#add_bank_id").prop("required", true);
      } else {
        $("#add_bank_group").hide();
        $("#add_bank_id").val("").trigger("change.select2").prop("required", false);
      }
    });

    $("#edit_ac_type").change(function () {
      if ($(this).val() === "Bank") {
        $("#edit_bank_group").show();
        $("#edit_bank_id").prop("required", true);
      } else {
        $("#edit_bank_group").hide();
        $("#edit_bank_id").val("").trigger("change.select2").prop("required", false);
      }
    });

    $("#add_account_head_id").change(function () {
      var ah_id = $(this).val();
      if (ah_id) {
        $.post(
          '<?= site_url("pettycash/get_sub_accounts") ?>',
          { account_head_id: ah_id },
          function (data) {
            $("#add_category_id").html(data);
            if ($.fn.select2) $("#add_category_id").trigger("change.select2");
          },
        );
      } else {
        $("#add_category_id").html('<option value="">Select Sub Account</option>');
        if ($.fn.select2) $("#add_category_id").trigger("change.select2");
      }
    });

    $("#account_head_id").change(function () {
      var ah_id = $(this).val();
      if (ah_id) {
        $.post(
          '<?= site_url("pettycash/get_sub_accounts") ?>',
          { account_head_id: ah_id },
          function (data) {
            $("#category_id").html(data);
            if ($.fn.select2) $("#category_id").trigger("change.select2");
          },
        );
      } else {
        $("#category_id").html('<option value="">Select Sub Account</option>');
        if ($.fn.select2) $("#category_id").trigger("change.select2");
      }
    });

    $("#edit_account_head_id").change(function (e, isInit) {
      if (isInit) return; // Skip if triggered programmatically during modal init
      var ah_id = $(this).val();
      if (ah_id) {
        $.post(
          '<?= site_url("pettycash/get_sub_accounts") ?>',
          { account_head_id: ah_id },
          function (data) {
            $("#edit_category_id").html(data);
            if ($.fn.select2) $("#edit_category_id").trigger("change.select2");
          },
        );
      } else {
        $("#edit_category_id").html(
          '<option value="">Select Sub Account</option>',
        );
        if ($.fn.select2) $("#edit_category_id").trigger("change.select2");
      }
    });

    $(".btn-edit").click(function () {
      var id = $(this).data("id");
      var date = $(this).data("date");
      var type = $(this).data("type");
      var ac_type = $(this).data("ac-type");
      var bank = $(this).data("bank");
      var account_head = $(this).data("account-head");
      var category = $(this).data("category");
      var amount = $(this).data("amount");
      var remarks = $(this).data("remarks");

      $("#edit_id").val(id);
      $("#edit_type").val(type);
      $("#edit_date").val(date);
      $("#edit_amount").val(amount);
      $("#edit_remarks").val(remarks);

      if (type === "Inward") {
        $("#edit_ac_type_group").show();
        $("#edit_ac_type").val(ac_type).trigger("change");
        $("#edit_bank_id").val(bank).trigger("change.select2");
        $("#edit_ac_type").prop("required", true);

        $("#edit_account_head_group").show();
        $("#edit_category_group").show();

        $("#edit_account_head_id").val(account_head).trigger("change", [true]);

        $.post(
          '<?= site_url("pettycash/get_sub_accounts") ?>',
          { account_head_id: account_head },
          function (data) {
            $("#edit_category_id").html(data);
            $("#edit_category_id").val(category);
            if ($.fn.select2) $("#edit_category_id").trigger("change.select2");
          },
        );

        $("#edit_account_head_id").prop("required", true);
        $("#edit_category_id").prop("required", true);
      } else if (type === "Outward") {
        $("#edit_ac_type_group").hide();
        $("#edit_bank_group").hide();
        $("#edit_ac_type").val("").trigger("change").prop("required", false);

        $("#edit_account_head_group").show();
        $("#edit_category_group").show();

        $("#edit_account_head_id").val(account_head).trigger("change", [true]);

        $.post(
          '<?= site_url("pettycash/get_sub_accounts") ?>',
          { account_head_id: account_head },
          function (data) {
            $("#edit_category_id").html(data);
            $("#edit_category_id").val(category);
            if ($.fn.select2) $("#edit_category_id").trigger("change.select2");
          },
        );

        $("#edit_account_head_id").prop("required", true);
        $("#edit_category_id").prop("required", true);
      } else {
        $("#edit_ac_type_group").hide();
        $("#edit_bank_group").hide();
        $("#edit_ac_type").val("").trigger("change").prop("required", false);

        $("#edit_account_head_group").hide();
        $("#edit_category_group").hide();
        $("#edit_account_head_id").val("").trigger("change", [true]);
        $("#edit_category_id").html(
          '<option value="">Select Sub Account</option>',
        );
        $("#edit_category_id").val("").trigger("change.select2");
        $("#edit_account_head_id").prop("required", false);
        $("#edit_category_id").prop("required", false);
      }

      $("#editTransactionModal").modal("show");
    });

    $(".btn-delete").click(function () {
      var id = $(this).data("id");
      if (
        confirm(
          "Are you sure you want to delete this transaction? This action cannot be undone.",
        )
      ) {
        window.location.href =
          '<?= site_url("pettycash/delete_transaction/") ?>' + id;
      }
    });

    // Initialize Chart.js Donut Chart
    if (typeof chartData !== "undefined" && chartData.length > 0) {
      var ctx = document.getElementById("expenseChart").getContext("2d");
      var expenseChart = new Chart(ctx, {
        type: "doughnut",
        data: {
          labels: chartLabels,
          datasets: [
            {
              data: chartData,
              backgroundColor: chartColors,
              borderWidth: 0,
              hoverOffset: 4,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: "75%",
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: function (context) {
                  var label = context.label || "";
                  if (label) {
                    label += ": ";
                  }
                  if (context.parsed !== null) {
                    label +=
                      new Intl.NumberFormat("en-US", {
                        minimumFractionDigits: 3,
                      }).format(context.parsed) + " BHD";
                  }
                  return label;
                },
              },
            },
          },
        },
      });

      // Generate Custom Legend
      var legendHtml = "";
      for (var i = 0; i < chartLabels.length; i++) {
        if (i > 4) break; // Limit to 5 legend items
        var percent = ((chartData[i] / totalOutward) * 100).toFixed(1);
        var amountFmt = new Intl.NumberFormat("en-US", {
          minimumFractionDigits: 3,
        }).format(chartData[i]);
        var color = chartColors[i % chartColors.length];

        legendHtml += '<div class="legend-item">';
        legendHtml +=
          '<div class="legend-label"><span class="legend-color-box" style="background:' +
          color +
          ';"></span>' +
          chartLabels[i] +
          "</div>";
        legendHtml +=
          '<div class="legend-values"><span class="legend-percent">' +
          percent +
          '%</span><span class="legend-amount">' +
          amountFmt +
          " BHD</span></div>";
        legendHtml += "</div>";
      }
      $("#chart-legend").html(legendHtml);
    } else {
      $("#expenseChart").replaceWith(
        '<div style="color:#94a3b8; text-align:center; padding-top:40px;">No expenses this month</div>',
      );
    }

    // Initialize Bar Chart for Income & Expense Comparison
    if ($("#incomeExpenseBarChart").length > 0 && typeof overallIncome !== "undefined") {
      var barCtx = document.getElementById("incomeExpenseBarChart").getContext("2d");
      new Chart(barCtx, {
        type: "bar",
        data: {
          labels: [
            "Overall Income",
            "Overall Expenses",
            "Pettycash Income",
            "Pettycash Expenses",
          ],
          datasets: [
            {
              label: "Amount (BHD)",
              data: [
                overallIncome,
                overallExpenses,
                pettycashIncome,
                pettycashExpenses,
              ],
              backgroundColor: [
                "#1fc27d", // Overall Income - Green
                "#fe5671", // Overall Expenses - Red
                "#5a5ce8", // Pettycash Income - Blue
                "#f59e0b", // Pettycash Expenses - Orange
              ],
              borderRadius: 6,
              borderWidth: 0,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: function (context) {
                  var label = context.label || "";
                  if (label) {
                    label += ": ";
                  }
                  if (context.parsed !== null && context.parsed.y !== null) {
                    label +=
                      new Intl.NumberFormat("en-US", {
                        minimumFractionDigits: 3,
                      }).format(context.parsed.y) + " BHD";
                  }
                  return label;
                },
              },
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: { color: "#f1f5f9" },
              ticks: {
                callback: function (value) {
                  return (
                    new Intl.NumberFormat("en-US", {
                      minimumFractionDigits: 0,
                    }).format(value) + " BHD"
                  );
                },
              },
            },
            x: {
              grid: { display: false },
            },
          },
        },
      });
    }

    // Dynamic Table Pagination & Filtering
    var currentPage = 1;
    var pageSize = 10;

    function renderTablePage() {
      var typeFilter = $("#type_filter").val() || "all";
      var searchQuery = ($("#table_search").val() || "").toLowerCase();

      var $allRows = $("#history_table_body tr");
      var matchingRows = [];

      $allRows.each(function () {
        var $row = $(this);
        if ($row.find("td").length <= 1) return;

        var rowType = $row.data("type") || "";
        var rowText = $row.text().toLowerCase();

        var matchesType = (typeFilter === "all" || rowType === typeFilter);
        var matchesSearch = (!searchQuery || rowText.indexOf(searchQuery) !== -1);

        if (matchesType && matchesSearch) {
          matchingRows.push($row);
        } else {
          $row.hide();
        }
      });

      var totalEntries = matchingRows.length;
      var totalPages = Math.ceil(totalEntries / pageSize) || 1;

      if (currentPage > totalPages) currentPage = totalPages;
      if (currentPage < 1) currentPage = 1;

      var startIdx = (currentPage - 1) * pageSize;
      var endIdx = startIdx + pageSize;

      $.each(matchingRows, function (index, $row) {
        if (index >= startIdx && index < endIdx) {
          $row.show();
        } else {
          $row.hide();
        }
      });

      var showingStart = totalEntries > 0 ? startIdx + 1 : 0;
      var showingEnd = Math.min(endIdx, totalEntries);
      $("#history_showing_count").text(
        "Showing " + showingStart + " to " + showingEnd + " of " + totalEntries + " entries"
      );

      var navHtml = "";
      var prevDisabled = currentPage === 1 ? 'disabled style="opacity:0.4; cursor:not-allowed;"' : "";
      navHtml += '<button type="button" class="btn-page-nav page-prev" ' + prevDisabled + '><i class="fa fa-angle-left"></i></button>';

      for (var p = 1; p <= totalPages; p++) {
        if (p === currentPage) {
          navHtml += '<button type="button" class="btn-page-nav active">' + p + "</button>";
        } else {
          navHtml += '<button type="button" class="btn-page-nav page-num" data-page="' + p + '">' + p + "</button>";
        }
      }

      var nextDisabled = currentPage === totalPages ? 'disabled style="opacity:0.4; cursor:not-allowed;"' : "";
      navHtml += '<button type="button" class="btn-page-nav page-next" ' + nextDisabled + '><i class="fa fa-angle-right"></i></button>';

      $("#history_pagination_container").html(navHtml);
    }

    $(document).on("click", ".page-num", function () {
      currentPage = parseInt($(this).data("page"));
      renderTablePage();
    });

    $(document).on("click", ".page-prev", function () {
      if (currentPage > 1) {
        currentPage--;
        renderTablePage();
      }
    });

    $(document).on("click", ".page-next", function () {
      currentPage++;
      renderTablePage();
    });

    $(document).on("change", "#type_filter", function () {
      currentPage = 1;
      renderTablePage();
    });

    $(document).on("keyup search", "#table_search", function () {
      currentPage = 1;
      renderTablePage();
    });

    if ($("#history_table_body tr").length > 0) {
      renderTablePage();
    }
  });
</script>
