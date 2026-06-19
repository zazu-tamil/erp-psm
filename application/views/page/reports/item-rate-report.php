<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<style>
    /* Sticky table headers styling */
    #item_search_table_modal thead tr:first-child th {
        position: sticky;
        top: 0;
        z-index: 15;
        background-color: #fff !important;
        border-bottom: 2px solid #ddd;
    }
    #item_search_table_modal thead tr:nth-child(2) th {
        position: sticky;
        top: 38px; /* Height of the first row headers */
        z-index: 15;
        background-color: #fff !important;
        border-bottom: 2px solid #ddd;
    }
    #item_search_table_modal thead th.col-cust-quote {
        background-color: #f9f9f9 !important;
    }
    #item_search_table_modal thead th.col-cust-po {
        background-color: #f5f5f5 !important;
    }
    #item_search_table_modal thead th.col-vend-quote {
        background-color: #f9f9f9 !important;
    }
    #item_search_table_modal thead th.col-vend-po {
        background-color: #f5f5f5 !important;
    }
    /* Prevent columns from wrapping, especially dates */
    #item_search_table_modal th,
    #item_search_table_modal td {
        white-space: nowrap !important;
    }
    #item_search_table_modal td:nth-child(2) {
        white-space: normal !important;
        min-width: 250px;
    }
</style>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Tender</a></li>
        <li class="active">Add <?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-file-text-o"></i> Item Rate Report</h3>
        </div>
        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-12">
                        <label style="margin-right: 20px; font-weight: bold; cursor: pointer;">
                            <input type="checkbox" id="filter_customer" checked style="margin-right: 5px; vertical-align: middle;"> Customer (Tender)
                        </label>
                        <label style="margin-right: 20px; font-weight: bold; cursor: pointer;">
                            <input type="checkbox" id="filter_vendor" checked style="margin-right: 5px; vertical-align: middle;"> Vendor
                        </label>
                        <label style="margin-right: 20px; font-weight: bold; cursor: pointer;">
                            <input type="checkbox" id="filter_quotation" checked style="margin-right: 5px; vertical-align: middle;"> Quotation
                        </label>
                        <label style="margin-right: 20px; font-weight: bold; cursor: pointer;">
                            <input type="checkbox" id="filter_po" checked style="margin-right: 5px; vertical-align: middle;"> PO
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="item_search_modal">Search Item Code</label>
                        <input type="text" id="item_search_modal" class="form-control" placeholder="Search Item Code">
                    </div>
                    <div class="col-md-6">
                        <label for="item_desc_modal">Search Item Desc</label>
                        <input type="text" id="item_desc_modal" class="form-control" placeholder="Search Item Desc">
                    </div>
                </div>
                <div class="table-responsive" style="overflow-x: auto !important; overflow-y: auto !important; max-height: 600px; width: 100%;">
                    <table class="table table-bordered table-striped mt-3" id="item_search_table_modal" style="min-width: 2000px;">
                        <thead>
                            <tr>
                                <th rowspan="2" style="vertical-align: middle;">Item Code</th>
                                <th rowspan="2" style="vertical-align: middle; width: 20%;">Item Desc</th>
                                <th colspan="4" class="text-center col-cust-quote" style="background-color: #f9f9f9;">Customer Quotation</th>
                                <th colspan="4" class="text-center col-cust-po" style="background-color: #f5f5f5;">Customer Po</th>
                                <th colspan="4" class="text-center col-vend-quote" style="background-color: #f9f9f9;">Vendor Quotation</th>
                                <th colspan="4" class="text-center col-vend-po" style="background-color: #f5f5f5;">Vendor Po</th>
                            </tr>
                            <tr>
                                <th class="col-cust-quote">Date</th>
                                <th class="col-cust-quote">Rate</th>
                                <th class="col-cust-quote">Qty</th>
                                <th class="col-cust-quote">Enq Num</th>
                                <th class="col-cust-po">Date</th>
                                <th class="col-cust-po">Rate</th>
                                <th class="col-cust-po">Qty</th>
                                <th class="col-cust-po">Enq Num</th>
                                <th class="col-vend-quote">Date</th>
                                <th class="col-vend-quote">Rate</th>
                                <th class="col-vend-quote">Qty</th>
                                <th class="col-vend-quote">Enq Num</th>
                                <th class="col-vend-po">Date</th>
                                <th class="col-vend-po">Rate</th>
                                <th class="col-vend-po">Qty</th>
                                <th class="col-vend-po">Enq Num</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>

<script>
    jQuery(function ($) {
        function updateColumns() {
            let showCustomer = $("#filter_customer").is(":checked");
            let showVendor = $("#filter_vendor").is(":checked");
            let showQuotation = $("#filter_quotation").is(":checked");
            let showPO = $("#filter_po").is(":checked");

            // Determine visibility of each column group
            let custQuoteVisible = showCustomer && showQuotation;
            let custPOVisible = showCustomer && showPO;
            let vendQuoteVisible = showVendor && showQuotation;
            let vendPOVisible = showVendor && showPO;

            // Toggle headers and cells dynamically
            $(".col-cust-quote").toggle(custQuoteVisible);
            $(".col-cust-po").toggle(custPOVisible);
            $(".col-vend-quote").toggle(vendQuoteVisible);
            $(".col-vend-po").toggle(vendPOVisible);
        }

        // Initialize column visibility
        updateColumns();

        // Listen for checkbox changes
        $(document).on("change", "#filter_customer, #filter_vendor, #filter_quotation, #filter_po", function () {
            updateColumns();
        });

        $(document).on("keyup", "#item_search_modal", function () {
            let searchText = $(this).val().trim();

            $.ajax({
                url: "<?php echo base_url('reports/item_search_v3'); ?>",
                type: "POST",
                data: { search: searchText, srch_typ: "code" },
                dataType: "json",
                success: function (data) {
                    let tbody = $("#item_search_table_modal tbody");
                    tbody.empty(); // clear table

                    if (data.length === 0) {
                        tbody.append(
                            `<tr><td colspan="18" class="text-center">No Records Found</td></tr>`
                        );
                        return;
                    }

                    $.each(data, function (i, item) {
                        let cq = item.cust_quote || { date: '', rate: '', qty: '', enq_no: '' };
                        let cp = item.cust_po || { date: '', rate: '', qty: '', enq_no: '' };
                        let vq = item.vend_quote || { date: '', rate: '', qty: '', enq_no: '' };
                        let vp = item.vend_po || { date: '', rate: '', qty: '', enq_no: '' };

                        tbody.append(`
                          <tr>
                              <td>${item.item_code}</td>
                              <td>${item.item_desc}</td>
                              
                              <td class="col-cust-quote">${cq.date}</td>
                              <td class="col-cust-quote">${cq.rate}</td>
                              <td class="col-cust-quote">${cq.qty}</td>
                              <td class="col-cust-quote">${cq.enq_no}</td>
                              
                              <td class="col-cust-po">${cp.date}</td>
                              <td class="col-cust-po">${cp.rate}</td>
                              <td class="col-cust-po">${cp.qty}</td>
                              <td class="col-cust-po">${cp.enq_no}</td>
                              
                              <td class="col-vend-quote">${vq.date}</td>
                              <td class="col-vend-quote">${vq.rate}</td>
                              <td class="col-vend-quote">${vq.qty}</td>
                              <td class="col-vend-quote">${vq.enq_no}</td>
                              
                              <td class="col-vend-po">${vp.date}</td>
                              <td class="col-vend-po">${vp.rate}</td>
                              <td class="col-vend-po">${vp.qty}</td>
                              <td class="col-vend-po">${vp.enq_no}</td>
                          </tr>
                        `);
                    });

                    updateColumns();
                },
            });
        });

        $(document).on("keyup", "#item_desc_modal", function () {
            let searchText = $(this).val().trim();

            $.ajax({
                url: "<?php echo base_url('reports/item_search_v3'); ?>",
                type: "POST",
                data: { search: searchText, srch_typ: "desc" },
                dataType: "json",
                success: function (data) {
                    let tbody = $("#item_search_table_modal tbody");
                    tbody.empty(); // clear table

                    if (data.length === 0) {
                        tbody.append(
                            `<tr><td colspan="18" class="text-center">No Records Found</td></tr>`
                        );
                        return;
                    }

                    $.each(data, function (i, item) {
                        let cq = item.cust_quote || { date: '', rate: '', qty: '', enq_no: '' };
                        let cp = item.cust_po || { date: '', rate: '', qty: '', enq_no: '' };
                        let vq = item.vend_quote || { date: '', rate: '', qty: '', enq_no: '' };
                        let vp = item.vend_po || { date: '', rate: '', qty: '', enq_no: '' };

                        tbody.append(`
                          <tr>
                              <td>${item.item_code}</td>
                              <td>${item.item_desc}</td>
                              
                              <td class="col-cust-quote">${cq.date}</td>
                              <td class="col-cust-quote">${cq.rate}</td>
                              <td class="col-cust-quote">${cq.qty}</td>
                              <td class="col-cust-quote">${cq.enq_no}</td>
                              
                              <td class="col-cust-po">${cp.date}</td>
                              <td class="col-cust-po">${cp.rate}</td>
                              <td class="col-cust-po">${cp.qty}</td>
                              <td class="col-cust-po">${cp.enq_no}</td>
                              
                              <td class="col-vend-quote">${vq.date}</td>
                              <td class="col-vend-quote">${vq.rate}</td>
                              <td class="col-vend-quote">${vq.qty}</td>
                              <td class="col-vend-quote">${vq.enq_no}</td>
                              
                              <td class="col-vend-po">${vp.date}</td>
                              <td class="col-vend-po">${vp.rate}</td>
                              <td class="col-vend-po">${vp.qty}</td>
                              <td class="col-vend-po">${vp.enq_no}</td>
                          </tr>
                        `);
                    });

                    updateColumns();
                },
            });
        });
    });
</script>