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
<link
  href="<?php echo base_url() ?>asset/bower_components/select2/dist/css/select2.min.css"
  rel="stylesheet"
/>
<script src="<?php echo base_url() ?>asset/bower_components/select2/dist/js/select2.full.min.js"></script>

<style>
  /* FULL RESPONSIVE WIDTH */
  .select2-container {
    width: 100% !important;
  }

  /* ADMINLTE INPUT MATCH */
  .select2-container--default .select2-selection--single {
    height: 34px !important;
    padding: 6px 12px !important;
    border: 1px solid #d2d6de !important;
    border-radius: 0 !important;
    background-color: #fff !important;
  }

  /* TEXT ALIGN FIX */
  .select2-selection__rendered {
    line-height: 22px !important;
  }

  /* DROPDOWN ARROW */
  .select2-selection__arrow {
    height: 32px !important;
  }

  /* MOBILE TOUCH FIX */
  @media (max-width: 768px) {
    .select2-container--default .select2-selection--single {
      height: 37px !important;
      padding: 8px 12px !important;
    }
  }

  .select2-container .select2-selection--single .select2-selection__rendered {
    padding-left: 0 !important;
    padding-right: 0 !important;
    height: auto !important;
    /* margin-top: -4px; */
  }
</style>
<script>
  $(document).ready(function () {
    $(".select2").select2({
      width: "100%",
    });
  });
</script>

<script>
    jQuery(function ($) {
        // Initialize Select2 for better UI
        $('.select2').select2();

        // Delete Confirmation
        $(".del_record").click(function () {
            if (confirm(
                "Are you sure you want to mark this vendor rate enquiry as DELETED?\n\nIt will no longer appear in active lists."
            )) {
                const id = $(this).val();
                $.post("<?php echo site_url('vendor/delete_record'); ?>", {
                    tbl: "vendor_rate_enquiry_info",
                    id: id,
                }, function (res) {
                    alert(res);
                    location.reload();
                }).fail(function () {
                    alert("Error processing request.");
                });
            }
        });

        // Load Tender Enquiries when Customer changes
        $("#srch_customer_id").on("change", function () {
            const customer_id = $(this).val();
            const $enquiryDropdown = $("#srch_tender_enquiry_id");

            if (!customer_id) {
                $enquiryDropdown.html('<option value="">All</option>');
                return;
            }

            $.ajax({
                url: "<?php echo site_url('vendor/get_vendor_rate_enquiries_by_customer'); ?>",
                type: "POST",
                data: {
                    customer_id: customer_id
                },
                dataType: "json",
                success: function (res) {
                    $enquiryDropdown.html('<option value="">All</option>');
                    if (res.length > 0) {
                        $.each(res, function (i, row) {
                            $enquiryDropdown.append(
                                $("<option></option>").attr("value", row
                                    .tender_enquiry_id).text(row.display)
                            );
                        });
                    }
                },
                error: function () {
                    alert("Error loading enquiries");
                }
            });
        });

        // Trigger change on page load if customer already selected
        <?php if (!empty($srch_customer_id)): ?>
            $("#srch_customer_id").trigger('change');
            <?php if (!empty($srch_tender_enquiry_id)): ?>
                $("#srch_tender_enquiry_id").val("<?php echo $srch_tender_enquiry_id; ?>");
            <?php endif; ?>
        <?php endif; ?>
    });
</script>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    jQuery(function ($) {
        $("#srch_vendor_rfq_no").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url('vendor/vendor_srch_rfq_no'); ?>",
                    type: "POST",
                    data: { search: request.term },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        response(data);
                    },
                });
            },

            minLength: 1,

            select: function (event, ui) {
                console.log(ui);
                // $("#srch_company_id").val(ui.item.company_id);
                // $("#srch_customer_id").val(ui.item.customer_id).change();
            },
        });

        $("#srch_enquiry_no").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
                    type: "POST",
                    data: { search: request.term },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        response(data);
                    },
                });
            },

            minLength: 1,

            select: function (event, ui) {
                console.log(ui);
                // $("#srch_company_id").val(ui.item.company_id);
                // $("#srch_customer_id").val(ui.item.customer_id).change();
            },
        });
    });

</script>