<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script> 
 
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

    function exportJSONToExcel1(JSONData, fileName = "export.xls") {

      let data = typeof JSONData != "string" ? JSON.stringify(JSONData) : JSONData;
      data = JSON.parse(data);

      let table = "<table border='1'><tr>";

      // Headers
      for (let key in data[0]) {
          table += "<th>" + key.toUpperCase() + "</th>";
      }
      table += "</tr>";

      // Rows
      data.forEach(row => {
          table += "<tr>";
          for (let key in row) {
              table += "<td>" + row[key] + "</td>";
          }
          table += "</tr>";
      });
      table += "</table>";

      // Create Excel Blob
      let blob = new Blob([table], {
          type: "application/vnd.ms-excel"
      });

      // Download
      let a = document.createElement("a");
      a.href = URL.createObjectURL(blob);
      a.download = fileName;
      a.click();
  }

  function exportJSONToXLSX(data, fileName = "export.xlsx") {

    // Convert JSON to worksheet
    const worksheet = XLSX.utils.json_to_sheet(data);

    // Create workbook
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");

    // Export XLSX
    XLSX.writeFile(workbook, fileName);
}

  /*$("#btnExport").click(function () {
      let jsonData = <?php  echo json_encode($items); ?>;
      exportJSONToExcel(jsonData, $(this).val() + ".xls");
  });*/

  $(document).on("click", "#btnExport", function () {

    let jsonData = <?php  echo json_encode($items); ?>;
    exportJSONToXLSX(jsonData, $(this).val() +".xlsx");
  });


  const rowTemplate = `
     <div class="item-card border p-3 mb-3" style="background-color:#f9f9f9; border-radius:8px;">
          <h5 class="text-primary mb-3">Item Details <span class="cnt"></span></h5>
          <div class="row">
              <div class="col-md-1 d-flex align-items-center justify-content-center">
                  <input type="checkbox" class="form-check-input item-check" name="selected_items[]"  value="" checked>
                      <input type="hidden" name="tender_quotation_item_id[]" class="form-control tender_quotation_item_id" value="">
                      <input type="hidden" name="tender_enquiry_item_id[]" class="form-control tender_enquiry_item_id" value="">
                                        
              </div>

              <div class="col-md-3">
                  <div class="form-group">
                      <label>Item Code</label>
                      <input type="text" class="form-control item_code" name="item_code[]" value="" readonly>
                  </div>
              </div>

              <div class="col-md-4">
                  <div class="form-group">
                      <label>Item Description</label>
                      <textarea name="item_desc[]" class="form-control desc-textarea"
                          rows="3"></textarea>
                  </div>
              </div>

              <div class="col-md-4">
                  <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label>UOM</label>
                              <input type="text" name="uom[]" class="form-control uom" value="" readonly>
                          </div>
                      </div>

                      <div class="col-md-6">
                          <div class="form-group">
                              <label>Quantity</label>
                              <input type="number" step="0.01" name="qty[]" class="form-control qty-input" value="" readonly>
                          </div>
                      </div>

                      <div class="col-md-6">
                          <div class="form-group">
                              <label>Rate</label>
                              <input type="number" step="0.01" name="rate[]" class="form-control rate-input" value="">
                          </div>
                      </div>

                      <div class="col-md-6">
                          <div class="form-group">
                              <label>VAT %</label>
                              <input type="text" name="gst[]" class="form-control vat-dropdown" value="" >
                                
                          </div>
                      </div> 
                      <div class="col-md-12">
                          <div class="form-group">
                              <label>Amount</label>
                              <input type="number" step="0.01" name="amount[]"
                                  class="form-control amount-input"
                                  value="" readonly>
                          </div>
                      </div>
                  </div>
              </div>
          </div> 
      </div>
     
     `;
 

    $("#excelFile").on("change", function (e) {

        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();

        reader.onload = function (e) {

            const workbook = XLSX.read(e.target.result, { type: "binary" });
            const sheet = workbook.Sheets[workbook.SheetNames[0]];

            // JSON with headers
            const rows = XLSX.utils.sheet_to_json(sheet, { defval: "" });

            console.log(rows);

            $("#item_container").empty();
            let i = 0;
            rows.forEach(function (r) {
                
                let newRow = $(rowTemplate);

                newRow.find(".cnt").html((i+1));
                newRow.find(".item-check").val(i);
                newRow.find(".tender_quotation_item_id").val(r.tender_quotation_item_id);
                newRow.find(".tender_enquiry_item_id").val(r.tender_enquiry_item_id); 
                newRow.find(".item_code").val(r.item_code);
                newRow.find(".desc-textarea").val(r.item_desc);
                newRow.find(".uom").val(r.uom);
                newRow.find(".qty-input").val(r.qty);
                newRow.find(".vat-dropdown").val(r.vat);
                newRow.find(".rate-input").val(r.rate);
                newRow.find(".amount").val(r.amount);

               

                $("#item_container").append(newRow);

                 newRow.find(".qty-input").change();
                i++;
            });
        };

        reader.readAsBinaryString(file);
    });

    function calculateAmount(row) {  

        let qty  = parseFloat(row.find(".qty-input").val()) || 0;
        let rate = parseFloat(row.find(".rate-input").val()) || 0;
        let vat  = parseFloat(row.find(".vat-dropdown").val()) || 0;

        let baseAmount = qty * rate;
        let vatAmount  = (baseAmount * vat) / 100;
        let total      = baseAmount + vatAmount;

        row.find(".amount").val(total.toFixed(2));
    }


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
