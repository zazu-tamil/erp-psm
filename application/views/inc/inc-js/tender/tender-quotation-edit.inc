<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>

<script>
  $(document).ready(function () {
    // Recalculate everything on load
    calculateTotalAmount();

    // Trigger calculation on quantity, rate, VAT change
    $(document).on(
      "input change",
      ".qty-input, .rate-input, .vat-dropdown, .item-check",
      function () {
        const $row = $(this).closest(".item-card");

        // Only calculate row if checked
        if ($row.find(".item-check").is(":checked")) {
          updateRowAmount($row);
        } else {
          $row.find(".amount-input").val("0.00"); // unchecked = 0 amount
        }

        calculateTotalAmount();
      }
    );

    // --- Function: Update Amount for a Row ---
    function updateRowAmount($row) {
      const qty = parseFloat($row.find(".qty-input").val()) || 0;
      const rate = parseFloat($row.find(".rate-input").val()) || 0;
      const gst = parseFloat($row.find(".vat-dropdown").val()) || 0;
      //alert(gst);
      const amount = (qty * rate) + (qty * rate * gst) / 100;

      $row.find(".amount-input").val(amount.toFixed(2));
    }

    // --- Function: Total Calculation ---
    function calculateTotalAmount() {
      let total = 0;

      $(".item-card").each(function () {
        const $row = $(this);

        if ($row.find(".item-check").is(":checked")) {
          total += parseFloat($row.find(".amount-input").val()) || 0;
        }
      });

      $("#total_amount")
        .fadeOut(100, function () {
          $(this).text(total.toFixed(2));
        })
        .fadeIn(200);
    }
  });

  $(function () {
    CKEDITOR.replace("editor1_edit_modal", {
      height: 100,
      extraPlugins: "justify,colorbutton,font,table",
      removeButtons: "",
      toolbar: [
        {
          name: "clipboard",
          items: [
            "Cut",
            "Copy",
            "Paste",
            "PasteText",
            "PasteFromWord",
            "-",
            "Undo",
            "Redo",
          ],
        },
        {
          name: "basicstyles",
          items: [
            "Bold",
            "Italic",
            "Underline",
            "Strike",
            "RemoveFormat",
            "CopyFormatting",
          ],
        },
        {
          name: "paragraph",
          items: [
            "NumberedList",
            "BulletedList",
            "-",
            "Outdent",
            "Indent",
            "-",
            "Blockquote",
            "JustifyLeft",
            "JustifyCenter",
            "JustifyRight",
            "JustifyBlock",
          ],
        },
        { name: "links", items: ["Link", "Unlink", "Anchor"] },
        {
          name: "insert",
          items: ["Image", "Table", "HorizontalRule", "SpecialChar"],
        },
        { name: "styles", items: ["Format", "Font", "FontSize"] },
        { name: "colors", items: ["TextColor", "BGColor"] },
        { name: "tools", items: ["Maximize", "ShowBlocks"] },
      ],
    });
    CKEDITOR.replace("editor2_edit_modal", {
      height: 100,
      extraPlugins: "justify,colorbutton,font,table",
      removeButtons: "",
      toolbar: [
        {
          name: "clipboard",
          items: [
            "Cut",
            "Copy",
            "Paste",
            "PasteText",
            "PasteFromWord",
            "-",
            "Undo",
            "Redo",
          ],
        },
        {
          name: "basicstyles",
          items: [
            "Bold",
            "Italic",
            "Underline",
            "Strike",
            "RemoveFormat",
            "CopyFormatting",
          ],
        },
        {
          name: "paragraph",
          items: [
            "NumberedList",
            "BulletedList",
            "-",
            "Outdent",
            "Indent",
            "-",
            "Blockquote",
            "JustifyLeft",
            "JustifyCenter",
            "JustifyRight",
            "JustifyBlock",
          ],
        },
        { name: "links", items: ["Link", "Unlink", "Anchor"] },
        {
          name: "insert",
          items: ["Image", "Table", "HorizontalRule", "SpecialChar"],
        },
        { name: "styles", items: ["Format", "Font", "FontSize"] },
        { name: "colors", items: ["TextColor", "BGColor"] },
        { name: "tools", items: ["Maximize", "ShowBlocks"] },
      ],
    });
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
