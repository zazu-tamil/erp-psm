<?php include_once(VIEWPATH . 'inc/header.php'); ?>
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
            <h3 class="box-title"><i class="fa fa-plus-circle"></i> Add Tender Enquiry</h3>
        </div>
        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
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
                <div class="table-responsive">
                    <table class="table table-bordered  table-striped mt-3" id="item_search_table_modal">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th width="50%">Item Desc</th>
                                <th>UOM</th>
                                <th>Table</th>
                                <th>Date</th> 
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
        $(document).on("keyup", "#item_search_modal", function () {
            let searchText = $(this).val().trim();

            $.ajax({
                url: "<?php echo base_url('reports/item_search_v2'); ?>",
                type: "POST",
                data: { search: searchText, srch_typ: "code" },
                dataType: "json",
                success: function (data) {
                    let tbody = $("#item_search_table_modal tbody");
                    tbody.empty(); // clear table

                    if (data.length === 0) {
                        tbody.append(
                            `<tr><td colspan="5" class="text-center">No Records Found</td></tr>`
                        );
                        return;
                    }

                    $.each(data, function (i, item) {
                        tbody.append(`
                          <tr>
                              <td>${item.item_code}</td>
                              <td>${item.item_desc}</td>
                              <td>${item.uom}</td>
                              <td>${item.tbl}</td>
                              <td>${item.po_date}</td> 
                          </tr>
                      `);
                    });
                },
            });
        });
        $(document).on("keyup", "#item_desc_modal", function () {
            let searchText = $(this).val().trim();

            $.ajax({
                url: "<?php echo base_url('reports/item_search_v2'); ?>",
                type: "POST",
                data: { search: searchText, srch_typ: "desc" },
                dataType: "json",
                success: function (data) {
                    let tbody = $("#item_search_table_modal tbody");
                    tbody.empty(); // clear table

                    if (data.length === 0) {
                        tbody.append(
                            `<tr><td colspan="5" class="text-center">No Records Found</td></tr>`
                        );
                        return;
                    }

                    $.each(data, function (i, item) {
                        tbody.append(`
                          <tr>
                              <td>${item.item_code}</td>
                              <td>${item.item_desc}</td>
                              <td>${item.uom}</td>
                              <td>${item.tbl}</td>
                              <td>${item.po_date}</td> 
                          </tr>
                      `);
                    });
                },
            });
        });
    });
</script>