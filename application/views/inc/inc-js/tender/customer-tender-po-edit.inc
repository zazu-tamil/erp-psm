<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
<style>
  .total-section {
    display: flex;
    justify-content: flex-end;
    width: 100%;
  }
  .total-card {
    background: linear-gradient(135deg, #eafaf3, #dff8ec);
    border: 2px solid #aee0c0;
    border-radius: 16px;
    padding: 18px 28px;
    min-width: 320px;
    transition: all 0.3s ease-in-out;
  }
  .total-card:hover {
    background: #e1f7ec;
    transform: translateY(-3px);
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
  }
  .total-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .total-icon i {
    font-size: 1.8rem;
    background: #d2f0dc;
    padding: 10px;
    border-radius: 50%;
    color: #2e865f;
  }
  .total-text .label {
    font-size: 1.1rem;
    color: #444;
    font-weight: 600;
    margin-right: 6px;
  }
  .total-text .value {
    font-size: 1.6rem;
    font-weight: 700;
    color: #007a4d;
  }
  #total_amount {
    font-weight: 800;
  }

  @media (max-width: 768px) {
    .total-section {
      justify-content: center;
    }
    .total-card {
      text-align: center;
      min-width: 240px;
      padding: 14px 18px;
    }
    .total-icon i {
      font-size: 1.5rem;
      padding: 8px;
    }
  }
</style>

<script>
  $(document).ready(function () {
    calculateTotalAmount();

    const existingCompanyId = $("#srch_company_id").val();
    const existingCustomerId = $("#srch_customer_id").val();
    const existingEnquiryId = $("#srch_tender_enquiry_id").val();
    const existingQuotationId = $("#srch_quotation_no").val();

    if (existingCompanyId && existingCustomerId && existingEnquiryId) {
      loadQuotations(existingCompanyId, existingCustomerId, existingEnquiryId, existingQuotationId);
    }

    $("#srch_company_id").on("change", function () {
      const company_id = $(this).val();
      const $customerDropdown = $("#srch_customer_id");
      const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $quotationDropdown = $("#srch_quotation_no");
      const $container = $("#item_container");

      $enquiryDropdown.html('<option value="">Select Enquiry</option>').prop('disabled', true);
      $quotationDropdown.html('<option value="">Select Quotation</option>').prop('disabled', true);
      $container.html('');

      if (!company_id) {
        $customerDropdown.html('<option value="">Select Customer</option>').prop('disabled', false);
        return;
      }

      $.ajax({
        url: "<?php echo site_url('vendor/get_all_customers'); ?>",
        type: "POST",
        dataType: "json",
        success: function (res) {
          $customerDropdown.html('<option value="">Select Customer</option>');
          if (res.length > 0) {
            $.each(res, function (i, row) {
              $customerDropdown.append(
                $('<option></option>')
                  .attr('value', row.customer_id)
                  .text(row.customer_name)
              );
            });
            $customerDropdown.prop('disabled', false);
          } else {
            $customerDropdown.html('<option value="">No customers found</option>');
          }
        },
        error: function () {
          alert('Error loading customers');
        }
      });
    });

    $("#srch_customer_id").on("change", function () {
      const customer_id = $(this).val();
      const company_id = $("#srch_company_id").val();
      const $enquiryDropdown = $("#srch_tender_enquiry_id");
      const $quotationDropdown = $("#srch_quotation_no");
      const $container = $("#item_container");

      $enquiryDropdown.html('<option value="">Select Enquiry</option>').prop('disabled', true);
      $quotationDropdown.html('<option value="">Select Quotation</option>').prop('disabled', true);
      $container.html('');

      if (!customer_id || !company_id) return;

      $.ajax({
        url: "<?php echo site_url('tender/get_tender_enquiries_by_customer'); ?>",
        type: "POST",
        data: {
          company_id: company_id,
          customer_id: customer_id
        },
        dataType: "json",
        success: function (res) {
          if (res.length > 0) {
            $enquiryDropdown.prop('disabled', false);
            $.each(res, function (i, row) {
              $enquiryDropdown.append(
                $('<option></option>')
                  .attr('value', row.tender_enquiry_id)
                  .text(row.display)
              );
            });
          } else {
            $enquiryDropdown.html('<option value="">No enquiries found</option>');
          }
        },
        error: function () {
          alert('Error loading enquiries');
        }
      });
    });

    $("#srch_tender_enquiry_id").on("change", function () {
      const company_id = $("#srch_company_id").val();
      const customer_id = $("#srch_customer_id").val();
      const tender_enquiry_id = $(this).val();

      if (!company_id || !customer_id || !tender_enquiry_id) return;

      loadQuotations(company_id, customer_id, tender_enquiry_id, null);
    });

    function loadQuotations(company_id, customer_id, tender_enquiry_id, preSelectQuotationId) {
      const $quotationDropdown = $("#srch_quotation_no");

      $.ajax({
        url: "<?php echo site_url('tender/get_quotations_by_customer'); ?>",
        type: "POST",
        data: {
          company_id: company_id,
          customer_id: customer_id,
          tender_enquiry_id: tender_enquiry_id
        },
        dataType: "json",
        success: function (res) {
          $quotationDropdown.html('<option value="">Select Quotation</option>');

          if (res.length > 0) {
            $quotationDropdown.prop('disabled', false);
            $.each(res, function (i, row) {
              const isSelected = preSelectQuotationId && (row.tender_quotation_id == preSelectQuotationId) ? 'selected' : '';
              $quotationDropdown.append(
                $('<option></option>')
                  .attr('value', row.tender_quotation_id)
                  .attr('selected', isSelected)
                  .text(row.display)
              );
            });

            if (preSelectQuotationId) {
              $quotationDropdown.trigger('change');
            }
          } else {
            $quotationDropdown.html('<option value="">No quotations with "Won" status found</option>');
            $quotationDropdown.prop('disabled', true);
          }
        },
        error: function (xhr, status, error) {
          alert('Error loading quotations');
        }
      });
    } 
  
  });
 
</script>
<script>
$(document).ready(function () {

    if (typeof CKEDITOR !== 'undefined') {

        if ($("#editor1").length) {
            CKEDITOR.replace('editor1', {
                height: 120,
                extraPlugins: 'justify,colorbutton,font,table',
            });
        }

        if ($("#editor2").length) {
            CKEDITOR.replace('editor2', {
                height: 120,
                extraPlugins: 'justify,colorbutton,font,table',
            });
        }

    } else {
        console.error("CKEditor not loaded. Check file path.");
    }

});
</script>
<script>
$(document).ready(function () {

    function calculateItemAmount(card) {

        let qty   = parseFloat(card.find(".qty-input").val())   || 0;
        let rate  = parseFloat(card.find(".rate-input").val())  || 0;
        let gst   = parseFloat(card.find(".vat-dropdown").val()) || 0;

        let base_amount = qty * rate;
        let gst_amount  = (base_amount * gst) / 100;
        let total       = base_amount + gst_amount;

        card.find(".amount-input").val(total.toFixed(2));
    }

    function calculateTotal() {
        let total = 0;
        $(".amount-input").each(function () {
            total += parseFloat($(this).val()) || 0;
        });
        $("#total_amount").text(total.toFixed(2));
    }

    // Trigger recalculation on Rate or GST change
    $(document).on("input change", ".rate-input, .vat-dropdown", function () {
        let card = $(this).closest(".item-card");
        calculateItemAmount(card);
        calculateTotal();
    });

    // Initial calculation on page load
    $(".item-card").each(function () {
        calculateItemAmount($(this));
    });
    calculateTotal();

});
</script>
