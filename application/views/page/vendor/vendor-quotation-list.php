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
                        <label for="srch_customer_id">Customer <span style="color:red;">*</span></label>
                        <?php echo form_dropdown('srch_customer_id', ['' => 'All'] + $customer_opt, set_value('srch_customer_id', $srch_customer_id), 'id="srch_customer_id" class="form-control select2"'); ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_vendor_rfq_no">Vendor RFQ No</label>
                        <input type="text" name="srch_vendor_rfq_no" id="srch_vendor_rfq_no" class="form-control"
                            value="<?php echo set_value('srch_vendor_rfq_no', $srch_vendor_rfq_no); ?>"
                            placeholder="Search the vendor rfq no">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_enquiry_no">Our Enquiry No</label>
                        <input type="text" name="srch_enquiry_no" id="srch_enquiry_no" class="form-control"
                            value="<?php echo set_value('srch_enquiry_no', $srch_enquiry_no); ?>"
                            placeholder="Search the our enquiry no">
                    </div>


                    <div class="form-group col-md-3">
                        <label for="srch_vendor_quotation_no">Vendor Quotation No</label>
                        <input type="text" name="srch_vendor_quotation_no" id="srch_vendor_quotation_no" class="form-control"
                            value="<?php echo set_value('srch_vendor_quotation_no', $srch_vendor_quotation_no); ?>"
                            placeholder="Search the our quotation no">
                    </div>

                    <!-- <div class="form-group col-md-3">
                        <label>Quotation Status</label>
                        <?php echo form_dropdown('srch_quotation_status', ['' => 'All'] + $quotation_status_opt, set_value('srch_quotation_status', $srch_quotation_status), 'id="srch_quotation_status" class="form-control select2"'); ?>
                    </div> -->
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
            <a href="<?php echo site_url('vendor-quotation-add'); ?>" class="btn btn-success">
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
                        <th>Quotation Date</th>
                        <th>Company / RFQ No</th>
                        <th>Customer</th>
                        <th>Vendor Name</th>
                        <th>Quotation No</th>
                        <th>Quotation Status</th>
                        <th class="text-center" colspan="3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)): ?>
                        <?php foreach ($record_list as $j => $row): ?>
                            <tr>
                                <td class="text-center"><?php echo ($j + 1 + $sno); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($row['quote_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['company_name'] ?? '-'); ?> <br><small
                                        class="label label-success"><?php echo htmlspecialchars($row['tender_enquery_no'] ?? '-'); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($row['customer_name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['vendor_name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['quote_no'] ?? '-'); ?></td>
                                <?php
                                $status = isset($row['quote_status']) ? $row['quote_status'] : '';

                                $badge_colors = [
                                    'Confirmed' => 'success',
                                    'Pending' => 'warning',
                                    'Rejected' => 'danger'
                                ];

                                $color = isset($badge_colors[$status]) ? $badge_colors[$status] : 'secondary';
                                ?>
                                <td>
                                    <span class="label label-<?php echo $color; ?>">
                                        <?php echo $status != '' ? $status : 'N/A'; ?>
                                    </span>
                                </td>
                                <!-- PRINT -->
                                <!-- <td class="text-center">
                                        <a href="<?php echo site_url('vendor-quotation-print/' . $row['vendor_quote_id']); ?>"
                                            class="btn btn-success btn-xs" title="Print" target="_blank">
                                            <i class="fa fa-print"></i>
                                        </a>
                                    </td> -->
                                <!-- EDIT -->
                                <td class="text-center">
                                    <a href="<?php echo site_url('vendor-quotation-edit/' . $row['vendor_quote_id']); ?>"
                                        class="btn btn-primary btn-xs" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                <!-- DELETE -->
                                <td class="text-center">
                                    <button value="<?php echo $row['vendor_quote_id']; ?>"
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
        $("#srch_vendor_quotation_no").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url('vendor/srch_vendor_quotation_no'); ?>",
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