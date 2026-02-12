<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>


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
                                 <input type="number" step="any" name="qty[]" class="form-control qty-input" value="" readonly>
                             </div>
                         </div>

                         <div class="col-md-6">
                             <div class="form-group">
                                 <label>Rate</label>
                                 <input type="number" step="any" name="rate[]" class="form-control rate-input" value="">
                             </div>
                         </div>

                         <div class="col-md-6">
                             <div class="form-group">
                                 <label>VAT %</label>
                                 <input type="text" name="gst[]" class="form-control vat-dropdown" value="" >

                             </div>
                         </div>
                         <div class="col-md-6">
                           <div class="form-group">
                             <label>Amount WO Tax</label>
                             <input type="number" step="any"
                                   name="amount_wo_tax[]"
                                   class="form-control amountwotx"
                                   value="0.000" readonly>
                           </div>
                         </div>

                         <div class="col-md-6">
                           <div class="form-group">
                             <label>Amount With Tax</label>
                             <input type="number" step="any"
                                   name="amount[]"
                                   class="form-control amount-input"
                                   value="0.000" readonly>
                           </div>
                       </div>
                     </div>
                 </div>
             </div>
         </div>

        `;
  calculateTotalAmount();

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

  /* ================================================================
        5. Amount Calculation (Rate / VAT change)
        ================================================================ */
     $(document).on("input change", ".rate-input, .vat-dropdown", function () {
       const $row = $(this).closest(".item-card");
       const qty = parseFloat($row.find(".qty-input").val()) || 0;
       const rate = parseFloat($row.find(".rate-input").val()) || 0;
       const gst = parseFloat($row.find(".vat-dropdown").val()) || 0;

       const amount = qty * rate * (1 + gst / 100);
       $row.find(".amount-input").val(amount.toFixed(3));
       $row.find(".amountwotx").val((qty * rate).toFixed(3));

       calculateTotalAmount();
     });

     function calculateTotalAmount() {
       let total = 0;
       $(".amount-input").each(function () {
         total += parseFloat($(this).val()) || 0;
       });
       $("#total_amount").text(total.toFixed(3));

       let total_wot = 0;
       $(".amountwotx").each(function () {
         total_wot += parseFloat($(this).val()) || 0;
       });
       $("#total_amount_wo_tax").text(total_wot.toFixed(3));
     }

     calculateTotalAmount();


     $(document).on("change", ".item-check", function () {
       const $card = $(this).closest(".item-card");
       if (!$(this).is(":checked")) {
         $card
           .find(
             "input[type=hidden][name^='tender_enquiry_item_id'], " +
               "input[type=hidden][name^='category_id'], " +
               "input[type=hidden][name^='item_id']",
           )
           .remove();
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
  .total-wrapper {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    width: 100%;
  }
  .total-box {
    background: linear-gradient(135deg, #f6fff9, #e8f9f0);
    border: 2px solid #b5e0c6;
    border-radius: 12px;
    padding: 12px 24px;
    font-family: "Segoe UI", sans-serif;
    font-size: 1.15rem;
    color: #333;
    min-width: 280px;
    text-align: right;
    transition: all 0.3s ease-in-out;
  }
  .total-box:hover {
    background: #e3f7ec;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
  }
  .total-box i {
    font-size: 1.3rem;
    vertical-align: middle;
  }
  #total_amount,
  #total_amount_wo_tax {
    font-size: 1.4rem;
    font-weight: 700;
    margin-left: 5px;
  }
  @media (max-width: 768px) {
    .total-wrapper {
      justify-content: center;
    }
    .total-box {
      text-align: center;
      min-width: 220px;
    }
  }

  .readonly-select {
    pointer-events: none;
    background-color: #e9ecef;
  }
</style>
