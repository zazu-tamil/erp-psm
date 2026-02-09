<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Vendor</a></li>
        <li class="active">Vendor Rate Enquiry List</li>
    </ol>
</section>

<section class="content">
    <!-- Search Filter -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="srch_from_date">From Date</label>
                        <input type="date" name="srch_from_date" id="srch_from_date" class="form-control"
                            value="<?php echo set_value('srch_from_date', $srch_from_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_to_date">To Date</label>
                        <input type="date" name="srch_to_date" id="srch_to_date" class="form-control"
                            value="<?php echo set_value('srch_to_date', $srch_to_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label>Customer</label>
                        <div class="form-group">
                            <?php echo form_dropdown('srch_customer_id', ['' => 'All'] + $customer_opt, $srch_customer_id, 'id="srch_customer_id" class="form-control select2" '); ?>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="srch_vendor_rfq_no">Vendor RFQ No</label>
                        <input type="text" name="srch_vendor_rfq_no" id="srch_vendor_rfq_no" class="form-control"
                            value="<?php echo set_value('srch_vendor_rfq_no', $srch_vendor_rfq_no); ?>"
                            placeholder="Search the vendor rfq no">
                    </div>


                </div>
                <div class="row">


                    <div class="form-group col-md-3">
                        <label for="srch_enquiry_no">Our Enquiry No</label>
                        <input type="text" name="srch_enquiry_no" id="srch_enquiry_no" class="form-control"
                            value="<?php echo set_value('srch_enquiry_no', $srch_enquiry_no); ?>"
                            placeholder="Search the our enquiry no">
                    </div>

                    <div class="form-group col-md-3">
                        <label>Vendor</label>
                        <?php echo form_dropdown('srch_vendor_id', ['' => 'All'] + $vendor_opt, $srch_vendor_id, 'id="srch_vendor_id" class="form-control select2" style="width:100%"'); ?>
                    </div>

                    <div class="form-group col-md-3 text-left">
                        <br>
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- List Table -->
    <div class="box box-info">
        <div class="box-header with-border">
            <a href="<?php echo site_url('vendor-rate-enquiry'); ?>" class="btn btn-success">
                <i class="fa fa-plus-circle"></i> Add New
            </a>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th>Enquiry Date</th>
                        <th>Company / RFQ No</th>
                        <th>Customer</th>
                        <th>Vendor Enquiry No</th>
                        <th>Vendor Name</th>
                        <th>Enquiry Status </th>
                        <th>Status </th>
                        <th>Closing Date</th>
                        <th class="text-center" colspan="3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)): ?>
                        <?php foreach ($record_list as $j => $row): ?>
                            <tr>
                                <td class="text-center"><?php echo ($j + 1 + $sno); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($row['enquiry_date'])); ?></td>
                                <td><strong><?php echo $row['company_name']; ?></strong>
                                    <br>
                                    <small class="label label-success"><?php echo $row['tender_details']; ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($row['customer_name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['enquiry_no']); ?></td>
                                <td><?php echo htmlspecialchars($row['vendor_name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['vendor_rate_enquiry_status'] ?? 'Prepared RFQ'); ?></td>
                                <td><?php echo htmlspecialchars($row['status'] ?? '-'); ?></td>

                                <td>
                                    <?php echo $row['closing_date'] ? date('d-m-Y H:i', strtotime($row['closing_date'])) : '-'; ?>
                                </td>


                                <td class="text-center">
                                    <a href="<?php echo site_url('vendor-rate-enquiry-print/' . $row['vendor_rate_enquiry_id']); ?>"
                                        target="_blank" class="btn btn-info btn-xs" title="Print / View">
                                        <i class="fa fa-print"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo site_url('vendor-rate-enquiry-edit/' . $row['vendor_rate_enquiry_id']); ?>"
                                        class="btn btn-primary btn-xs" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <button value="<?php echo $row['vendor_rate_enquiry_id']; ?>"
                                        class="del_record btn btn-danger btn-xs" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12" class="text-center text-danger">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="box-footer">
            <div class="form-group col-sm-6">
                <label>Total Records: <?php echo $total_records; ?></label>
            </div>
            <div class="form-group col-sm-6 text-right">
                <?php echo $pagination; ?>
            </div>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>

<script>
    jQuery(function ($) {
        // Initialize Select2 for better UI
        $('.select2').select2();

        // Delete Confirmation
        $(".del_record").click(function () {
            if (confirm(
                "Are you sure you want to mark this vendor rate enquiry as DELETED?\n\nIt will no longer appear in active lists."
            )) {
                const id = $(this).val();
                $.post("<?php echo site_url('vendor/delete_record'); ?>", {
                    tbl: "vendor_rate_enquiry_info",
                    id: id,
                }, function (res) {
                    alert(res);
                    location.reload();
                }).fail(function () {
                    alert("Error processing request.");
                });
            }
        });

        // Load Tender Enquiries when Customer changes
        $("#srch_customer_id").on("change", function () {
            const customer_id = $(this).val();
            const $enquiryDropdown = $("#srch_tender_enquiry_id");

            if (!customer_id) {
                $enquiryDropdown.html('<option value="">All</option>');
                return;
            }

            $.ajax({
                url: "<?php echo site_url('vendor/get_vendor_rate_enquiries_by_customer'); ?>",
                type: "POST",
                data: {
                    customer_id: customer_id
                },
                dataType: "json",
                success: function (res) {
                    $enquiryDropdown.html('<option value="">All</option>');
                    if (res.length > 0) {
                        $.each(res, function (i, row) {
                            $enquiryDropdown.append(
                                $("<option></option>").attr("value", row
                                    .tender_enquiry_id).text(row.display)
                            );
                        });
                    }
                },
                error: function () {
                    alert("Error loading enquiries");
                }
            });
        });

        // Trigger change on page load if customer already selected
        <?php if (!empty($srch_customer_id)): ?>
            $("#srch_customer_id").trigger('change');
            <?php if (!empty($srch_tender_enquiry_id)): ?>
                $("#srch_tender_enquiry_id").val("<?php echo $srch_tender_enquiry_id; ?>");
            <?php endif; ?>
        <?php endif; ?>
    });
</script>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    jQuery(function ($) {
        $("#srch_vendor_rfq_no").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url('vendor/vendor_srch_rfq_no'); ?>",
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
    });

</script>