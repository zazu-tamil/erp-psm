<!-- jQuery UI & DataTables CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" />
<link href="<?php echo base_url('asset/bower_components/select2/dist/css/select2.min.css'); ?>" rel="stylesheet" />

<!-- jQuery UI & DataTables JS with Buttons / Export libraries -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="<?php echo base_url('asset/bower_components/select2/dist/js/select2.full.min.js'); ?>"></script>

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
  }

  /* DataTables & Export Buttons Styling */
  .dataTables_wrapper {
    margin-top: 10px;
  }
  .dt-buttons {
    display: inline-block;
    margin-left: 15px;
    margin-bottom: 8px;
  }
  .dt-buttons .dt-button.btn {
    border-radius: 3px !important;
    padding: 5px 12px !important;
    margin-right: 5px !important;
    background-image: none !important;
    border: none !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    box-shadow: none !important;
  }
  .dt-buttons .dt-button.btn-success {
    background-color: #00a65a !important;
    color: #fff !important;
  }
  .dt-buttons .dt-button.btn-danger {
    background-color: #dd4b39 !important;
    color: #fff !important;
  }
  .dt-buttons .dt-button.btn-success:hover {
    background-color: #008d4c !important;
  }
  .dt-buttons .dt-button.btn-danger:hover {
    background-color: #d73925 !important;
  }
</style>

<script>
  jQuery(function ($) {
    // Initialize select2
    $(".select2").select2({
      width: "100%",
    });

    // Initialize DataTable on Customer Tender PO Table
    if ($.fn.DataTable.isDataTable("#customer_tender_po_table")) {
      $("#customer_tender_po_table").DataTable().destroy();
    }

    var table = $('#customer_tender_po_table').DataTable({
      dom: 'lBfrtip',
      lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
      pageLength: 25,
      ordering: true,
      order: [[1, 'desc']], // Default sort by PO Date DESC
      columnDefs: [
        { orderable: false, targets: [0, 13] }, // Disable sorting on S.No and Action
        { searchable: false, targets: [13] }     // Disable search on Action
      ],
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search POs...",
        lengthMenu: "Show _MENU_ entries",
        info: "Showing _START_ to _END_ of _TOTAL_ entries",
        infoEmpty: "Showing 0 to 0 of 0 entries",
        infoFiltered: "(filtered from _MAX_ total entries)"
      },
      buttons: [
        {
          extend: 'excelHtml5',
          text: '<i class="fa fa-file-excel-o"></i> Excel',
          className: 'btn btn-success btn-sm',
          title: 'Customer_Tender_PO_List_' + new Date().toISOString().slice(0, 10),
          exportOptions: {
            columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 ],
            modifier: {
              page: 'current',
              search: 'applied',
              order: 'current'
            },
            format: {
              body: function (data, rowIdx, colIdx, node) {
                if (colIdx === 0) {
                  return rowIdx + 1;
                }
                if (node) {
                  var text = node.innerText || node.textContent || '';
                  return text.trim();
                }
                if (typeof data === 'string') {
                  return data.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim();
                }
                return data;
              }
            }
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

            // Apply the header style to the table header row
            var headerStyle = xfCount - 1;
            if ($('row', sheet).length > 1) {
              $('row:eq(1) c', sheet).attr('s', headerStyle);
            } else {
              $('row:eq(0) c', sheet).attr('s', headerStyle);
            }
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fa fa-file-pdf-o"></i> PDF',
          className: 'btn btn-danger btn-sm',
          title: 'Customer Tender PO List',
          orientation: 'landscape',
          pageSize: 'A4',
          exportOptions: {
            columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 ],
            modifier: {
              page: 'current',
              search: 'applied',
              order: 'current'
            },
            format: {
              body: function (data, rowIdx, colIdx, node) {
                if (colIdx === 0) {
                  return rowIdx + 1;
                }
                if (node) {
                  var text = node.innerText || node.textContent || '';
                  return text.trim();
                }
                if (typeof data === 'string') {
                  return data.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim();
                }
                return data;
              }
            }
          },
          customize: function (doc) {
            doc.defaultStyle.fontSize = 7;
            doc.styles.tableHeader.fontSize = 7.5;
            doc.styles.tableHeader.alignment = 'left';
            
            // Add full grid borders
            var objLayout = {};
            objLayout['hLineWidth'] = function(i) { return 0.5; };
            objLayout['vLineWidth'] = function(i) { return 0.5; };
            objLayout['hLineColor'] = function(i) { return '#aaa'; };
            objLayout['vLineColor'] = function(i) { return '#aaa'; };
            objLayout['paddingLeft'] = function(i) { return 2; };
            objLayout['paddingRight'] = function(i) { return 2; };

            for (var i = 0; i < doc.content.length; i++) {
              if (doc.content[i].table) {
                doc.content[i].layout = objLayout;
                // Total 13 columns widths totaling 100%
                doc.content[i].table.widths = [
                  '4%',  // 0: S.No
                  '7%',  // 1: PO Date
                  '8%',  // 2: Our PO No
                  '13%', // 3: Company
                  '12%', // 4: Customer
                  '8%',  // 5: Customer RFQ No
                  '9%',  // 6: Quotation No
                  '9%',  // 7: Customer PO No
                  '7%',  // 8: Delivery Date
                  '6%',  // 9: PO Status
                  '6%',  // 10: Amt W/O Tax
                  '5%',  // 11: Amt Tax
                  '6%'   // 12: Amt With Tax
                ];

                // Right align amounts and center S.No
                var body = doc.content[i].table.body;
                for (var r = 1; r < body.length; r++) {
                  if (body[r][0]) body[r][0].alignment = 'center';
                  if (body[r][10]) body[r][10].alignment = 'right';
                  if (body[r][11]) body[r][11].alignment = 'right';
                  if (body[r][12]) body[r][12].alignment = 'right';
                }
                break;
              }
            }
          }
        }
      ]
    });

    // Automatically renumber S.No on search, sort, and pagination
    table.on('order.dt search.dt page.dt', function () {
      var info = table.page.info();
      table.column(0, { search: 'applied', order: 'current', page: 'current' }).nodes().each(function (cell, i) {
        cell.innerHTML = info.start + i + 1;
      });
    });

    // Customer selection change handler
    $("#srch_customer_id").on("change", function () {
      var customerId = $(this).val();
      var $contact = $("#srch_customer_contact_id");

      // Reset contact dropdown
      $contact.html('<option value="">All</option>');

      if (!customerId) {
        $contact.trigger('change');
        return;
      }

      $.ajax({
        url: "<?php echo site_url('tender/get_data'); ?>",
        type: "POST",
        data: { tbl: "get-customer-contacts", id: customerId },
        dataType: "json",
        success: function (data) {
          $.each(data, function (i, item) {
            $contact.append(
              $("<option></option>")
                .val(item.customer_contact_id)
                .text(item.contact_person_name)
            );
          });
          $contact.trigger('change');
        },
        error: function (xhr) {
          console.error(xhr.responseText);
          alert("Failed to load contact persons");
        },
      });
    });

    // Autocomplete for Our Enquiry No
    $("#srch_enquiry_no").autocomplete({
      source: function (request, response) {
        $.ajax({
          url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
          type: "POST",
          data: { search: request.term },
          dataType: "json",
          success: function (data) {
            response(data);
          },
        });
      },
      minLength: 1,
      select: function (event, ui) {}
    });

    // Autocomplete for Customer RFQ No
    $("#srch_tender_enquiry_id").autocomplete({
      source: function (request, response) {
        $.ajax({
          url: "<?php echo base_url('tender/tender_quotation_ref_no'); ?>",
          type: "POST",
          data: { search: request.term },
          dataType: "json",
          success: function (data) {
            response(data);
          },
        });
      },
      minLength: 1,
      select: function (event, ui) {}
    });

    // Autocomplete for Customer PO No
    $("#srch_tender_po_no").autocomplete({
      source: function (request, response) {
        $.ajax({
          url: "<?php echo base_url('tender/srch_tender_po_no'); ?>",
          type: "POST",
          data: { search: request.term },
          dataType: "json",
          success: function (data) {
            response(data);
          },
        });
      },
      minLength: 1,
      select: function (event, ui) {}
    });

    // Delegated Delete Confirmation (works across all DataTable pages)
    $(document).on("click", ".del_record", function () {
      let id = $(this).val();
      if (confirm("Are you sure you want to delete?")) {
        $.ajax({
          url: "<?php echo site_url('delete-record');?>",
          type: "post",
          data: { tbl: "customer_tender_po_info", id: id },
          success: function (d) {
            alert(d);
            location.reload();
          },
          error: function () {
            alert("Error deleting record.");
          }
        });
      }
    });
  });
</script>
