<script>
  $(document).ready(function () {
    // Calculate totals on load
    calculateTotalAmount();

    // Trigger calculation on quantity, rate, or VAT change
    $(document).on("input change", ".qty-input, .rate-input, .vat-dropdown", function () {
      const $row = $(this).closest(".item-card");
      const qty = parseFloat($row.find(".qty-input").val()) || 0;
      const rate = parseFloat($row.find(".rate-input").val()) || 0;
      const gst = parseFloat($row.find(".vat-dropdown").val()) || 0;
      const amount = qty * rate * (1 + gst / 100);
      $row.find(".amount-input").val(amount.toFixed(2));
      calculateTotalAmount();
    });

    // Function to calculate and update total amount
    function calculateTotalAmount() {
      let total = 0;
      $(".amount-input").each(function () {
        total += parseFloat($(this).val()) || 0;
      });

      const $total = $("#total_amount");
      $total.fadeOut(100, function () {
        $(this).text(total.toFixed(2)).fadeIn(200);
      });
    }
  });
</script>


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
