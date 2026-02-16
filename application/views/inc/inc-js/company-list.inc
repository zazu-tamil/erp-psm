<script src="<?php echo base_url('asset/bower_components/ckeditor/ckeditor.js'); ?>"></script>

<script>
  jQuery(function ($) {
    $(document).on("click", ".edit_record", function () {
      let id = $(this).val();
      $.ajax({
        url: "<?php echo site_url('get-data'); ?>",
        type: "post",
        data: { tbl: "company_info", id: id },
        dataType: "json",
        success: function (d) {
          console.log(d);
          $("#edit_modal #company_id").val(d.company_id);
          $("#edit_modal #bank_id").val(d.bank_id).change();

          $("#edit_modal #company_name").val(d.company_name);
          $("#edit_modal #contact_name").val(d.contact_name);
          $("#edit_modal #country").val(d.country);
          $("#edit_modal #email").val(d.email);
          $("#edit_modal #crno").val(d.crno);
          $("#edit_modal #address").val(d.address);
          $("#edit_modal #company_code").val(d.company_code);
          $("#edit_modal #mobile").val(d.mobile);
          $("#edit_modal #GST").val(d.GST);
          $('#edit_modal input:radio[value="' + d.status + '"]').prop(
            "checked",
            true,
          );

          // ✅ Load CKEditor content safely
          if (typeof d !== "undefined") {
            if (CKEDITOR.instances["editor1_edit_modal"]) {
              CKEDITOR.instances["editor1_edit_modal"].setData(
                d.quote_terms || "",
              );
            }
            if (CKEDITOR.instances["editor2_edit_modal"]) {
              CKEDITOR.instances["editor2_edit_modal"].setData(
                d.invoice_terms || "",
              );
            }
          }

          if (d.ltr_header_img) {
            let headerUrl =
              "<?php echo base_url(); ?>" +
              d.ltr_header_img +
              "?t=" +
              new Date().getTime();
            $("#edit_modal #header_img").attr("src", headerUrl).show();
          } else {
            $("#edit_modal #header_img").hide();
          }

          $("#edit_modal").modal("show");
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", xhr.responseText);
        },
      });
    });

    $(document).on("click", ".del_record", function () {
      let id = $(this).val();
      if (confirm("Are you sure you want to delete?")) {
        $.ajax({
          url: "<?php echo site_url('delete-record'); ?>",
          type: "post",
          data: { tbl: "company_info", id: id },
          success: function (d) {
            alert(d);
            location.reload();
          },
        });
      }
    });

    $("#add_modal #ltr_header_img").change(function () {
      readImage(this, "#add_modal #header_img");
    });

    $("#edit_modal #ltr_header_img").change(function () {
      readImage(this, "#edit_modal #header_img");
    });

    function readImage(input, targetImg) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $(targetImg).attr("src", e.target.result).show();
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
  });
</script>

<script>
  $(document).ready(function () {
    let total = parseInt("<?php echo $total_records; ?>");
    $("#user_limet").text(total);
    if (total === 0) {
      $("#btn_add_new").show();
    } else {
      $("#btn_add_new").hide();
    }

    // ✅ Initialize CKEditor with alignment, fonts, colors, tables, etc.
    $(function () {
      CKEDITOR.replace("editor1", {
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

      CKEDITOR.replace("editor2", {
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
  });
</script>
