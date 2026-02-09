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
                        <?php echo form_dropdown('srch_customer_id', ['' => 'All'] + $customer_opt, set_value('srch_customer_id', $srch_customer_id), 'id="srch_customer_id" class="form-control select2"'); ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_tender_enquiry_id">Customer RFQ No</label>
                        <input type="text" name="srch_tender_enquiry_id" id="srch_tender_enquiry_id"
                            class="form-control"
                            value="<?php echo set_value('srch_tender_enquiry_id', $srch_tender_enquiry_id); ?>"
                            placeholder="Search the customer rfq no">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="srch_enquiry_no">Our Enquiry No</label>
                        <input type="text" name="srch_enquiry_no" id="srch_enquiry_no" class="form-control"
                            value="<?php echo set_value('srch_enquiry_no', $srch_enquiry_no); ?>"
                            placeholder="Search the our enquiry no">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="srch_tender_dc_no">Dc No</label>
                        <input type="text" name="srch_tender_dc_no" id="srch_tender_dc_no" class="form-control"
                            value="<?php echo set_value('srch_tender_dc_no', $srch_tender_dc_no); ?>"
                            placeholder="Search the our Po no">
                    </div>
                    <div class="form-group col-md-3">
                        <label>&nbsp;</label>
                        <br>
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i>
                            Show</button>
                    </div> 
                </div>
            </form>
        </div>
    </div>
    <style>
        .select2-container .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
            padding-right: 0 !important;
            height: auto !important;
            /* margin-top: -4px; */
        }
    </style>

    <!-- List Table -->
    <div class="box box-info">
        <div class="box-header with-border">
            <a href="<?php echo site_url('tender-dc-add'); ?>" class="btn btn-success">
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
                        <th>DC Date</th>
                        <th>DC No</th>
                        <th>Company / RFQ No</th>
                        <th>Customer</th>

                        <!-- <th>Vendor</th>  -->
                        <th>Status</th>
                        <th class="text-center" colspan="3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)): ?>
                        <?php foreach ($record_list as $j => $row): ?>
                            <tr>
                                <td class="text-center"><?php echo ($j + 1 + $sno); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($row['dc_date'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['dc_no']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['company_name'] ?? '-'); ?> <br><small
                                        class="label label-success"><?php echo htmlspecialchars($row['tender_details'] ?? '-'); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($row['customer_name'] ?? '-'); ?></td>

                                <!-- <td><?php echo htmlspecialchars($row['vendor_name'] ?? '-'); ?></td> -->

                                <?php
                                $status = $row['status'];

                                $badge_colors = [
                                    'Active' => 'success',
                                    'Inactive' => 'info',
                                ];

                                $color = isset($badge_colors[$status]) ? $badge_colors[$status] : 'default';
                                ?>
                                <td>
                                    <span class="label label-<?php echo $color; ?>">
                                        <?php echo $status; ?>
                                    </span>
                                </td>



                                <!-- PRINT / VIEW -->
                                <td class="text-center">
                                    <a href="<?php echo site_url('tender-dc-print/' . $row['tender_dc_id']); ?>" target="_blank"
                                        class="btn btn-info btn-xs" title="Print">
                                        <i class="fa fa-print"></i>
                                    </a>
                                </td>
                                <!-- EDIT -->
                                <td class="text-center">
                                    <a href="<?php echo site_url('tender-dc-edit/' . $row['tender_dc_id']); ?>"
                                        class="btn btn-primary btn-xs" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                <!-- DELETE -->
                                <td class="text-center">
                                    <button value="<?php echo $row['tender_dc_id']; ?>" class="del_record btn btn-danger btn-xs"
                                        title="Delete">
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
