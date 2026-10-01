<link  rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" />

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script>
  jQuery(function ($) {

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
    $("#srch_tender_enquiry_id").autocomplete({
        source: function (request, response) {
          $.ajax({
            url: "<?php echo base_url('tender/tender_srch_rfq_no'); ?>",
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




    // Delete Confirmation
    $(".del_record").click(function () {
      if (confirm("Are you sure you want to delete this tender enquiry?")) {
        var id = $(this).val();
        $.post(
          "<?php echo site_url('delete-record'); ?>",
          {
            tbl: "tender_enquiry_info",
            id: id,
          },
          function (res) {
            alert(res);
            location.reload();
          }
        ).fail(function () {
          alert("Error deleting record.");
        });
      }
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
    
    if ($.fn.DataTable.isDataTable("#tender_enquiry_table")) {
        $("#tender_enquiry_table").DataTable().destroy();
    }
    $('#tender_enquiry_table').DataTable({
        dom: 'lBfrtip',
        lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
        buttons: [
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ],
                    modifier: { page: 'current' }
                },
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var styles = xlsx.xl['styles.xml'];
                    
                    // Set all cells to have borders (style 25)
                    $('row c', sheet).attr('s', '25');

                    // Inject custom styles into styles.xml
                    var fills = $('fills', styles);
                    fills.append('<fill><patternFill patternType="solid"><fgColor rgb="FF1E282C" /><bgColor indexed="64" /></patternFill></fill>');
                    var fillCount = parseInt(fills.attr('count')) + 1;
                    fills.attr('count', fillCount);

                    var fonts = $('fonts', styles);
                    fonts.append('<font><b /><color rgb="FFFFFFFF" /><name val="Calibri" /><family val="2" /></font>');
                    var fontCount = parseInt(fonts.attr('count')) + 1;
                    fonts.attr('count', fontCount);

                    var cellXfs = $('cellXfs', styles);
                    cellXfs.append('<xf numFmtId="0" fontId="' + (fontCount - 1) + '" fillId="' + (fillCount - 1) + '" borderId="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>');
                    var xfCount = parseInt(cellXfs.attr('count')) + 1;
                    cellXfs.attr('count', xfCount);

                    // Apply the newly created style to the header row (typically row index 1 when a title exists)
                    var headerStyle = xfCount - 1;
                    $('row:eq(1) c', sheet).attr('s', headerStyle);
                }
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ],
                    modifier: { page: 'current' }
                },
                customize: function (doc) {
                    doc.defaultStyle.fontSize = 8;
                    doc.styles.tableHeader.fontSize = 9;
                    doc.styles.tableHeader.alignment = 'left';
                    
                    // Add full grid borders
                    var objLayout = {};
                    objLayout['hLineWidth'] = function(i) { return .5; };
                    objLayout['vLineWidth'] = function(i) { return .5; };
                    objLayout['hLineColor'] = function(i) { return '#aaa'; };
                    objLayout['vLineColor'] = function(i) { return '#aaa'; };
                    objLayout['paddingLeft'] = function(i) { return 4; };
                    objLayout['paddingRight'] = function(i) { return 4; };
                    doc.content[1].layout = objLayout;

                    // 100% total width for 8 columns
                    doc.content[1].table.widths = [
                        '5%',  // S.No
                        '10%', // Enquiry Date
                        '30%', // Company Reference No
                        '15%', // Customer
                        '15%', // Customer Contact Name
                        '10%', // RFQ No
                        '10%', // Closing Date
                        '5%'   // Tender Status
                    ];
                }
            }
        ]
    });
  });

  $("#srch_customer_id").on("change", function () {
    const customer_id = $(this).val();
    const $enquiryDropdown = $("#srch_customer_rfq_id");
    const $container = $("#item_container");

    // Reset dependent dropdown and items
    $enquiryDropdown
      .html('<option value="">All</option>')
      .prop("disabled", true);
    $container.html("");

    if (!customer_id) return;

    $.ajax({
      url: "<?php echo site_url('tender/get_tender_enquiries_by_customer_rfq'); ?>",
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
          $enquiryDropdown.html('<option value="">No enquiries found</option>');
        }
      },
      error: function () {
        alert("Error loading enquiries");
      },
    });
  });
</script>
