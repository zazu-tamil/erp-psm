<?php include_once(VIEWPATH . 'inc/header.php'); ?>

<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Tender</a></li>
        <li class="active">Customer Tender PO List</li>
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
                    <div class="form-group col-md-4">
                        <label for="srch_from_date">From Date</label>
                        <input type="date" name="srch_from_date" id="srch_from_date" class="form-control"
                            value="<?php echo set_value('srch_from_date', $srch_from_date); ?>">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="srch_to_date">To Date</label>
                        <input type="date" name="srch_to_date" id="srch_to_date" class="form-control"
                            value="<?php echo set_value('srch_to_date', $srch_to_date); ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Customer</label>
                        <div class="form-group">
                            <?php echo form_dropdown('srch_customer_id', ['' => 'All'] + $customer_opt, $srch_customer_id, 'id="srch_customer_id" class="form-control select2" '); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <label for="">OR</label>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="srch_tender_enquiry_id">Customer RFQ No</label>
                        <input type="text" name="srch_tender_enquiry_id" id="srch_tender_enquiry_id"
                            class="form-control"
                            value="<?php echo set_value('srch_tender_enquiry_id', $srch_tender_enquiry_id); ?>"
                            placeholder="Search The Customer RFQ No">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="srch_enquiry_no">Our Enquiry No</label>
                        <input type="text" name="srch_enquiry_no" id="srch_enquiry_no" class="form-control"
                            value="<?php echo set_value('srch_enquiry_no', $srch_enquiry_no); ?>"
                            placeholder="Search The Our Enquiry No">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="srch_tender_po_no">Customer PO No</label>
                        <input type="text" name="srch_tender_po_no" id="srch_tender_po_no" class="form-control"
                            value="<?php echo set_value('srch_tender_po_no', $srch_tender_po_no); ?>"
                            placeholder="Search The Customer PO No">
                    </div>
                    <div class="form-group col-md-3">
                        <label>&nbsp;</label>
                        <br>
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i>
                            Show</button>
                    </div>

                </div>
                <!-- <div class="row">
                    <div class="form-group col-md-3">
                        <label>PO Status</label>
                        <?php echo form_dropdown('srch_po_status', ['' => 'All'] + $po_status_opt, set_value('srch_po_status', $srch_po_status), 'id="srch_po_status" class="form-control"'); ?>
                    </div>
                  
                </div> -->
            </form>
        </div>
    </div>

    <!-- List Table -->
    <div class="box box-info">
        <div class="box-header with-border">
            <a href="<?php echo site_url('customer-tender-po-add'); ?>" class="btn btn-success">
                <i class="fa fa-plus-circle"></i> Add New PO
            </a>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                        class="fa fa-minus"></i></button>
            </div>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>

                        <th>PO Date</th>
                        <th>Our PO No</th>
                        <th>Company</th>
                        <th>Customer</th>
                        <th>Customer RFQ No</th>
                        <th>Quotation No</th>

                        <th>Customer PO No</th>
                        <th>Delivery Date</th>
                        <th>PO Status</th>
                        <th class="text-center" colspan="3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)): ?>
                        <?php foreach ($record_list as $j => $row): ?>
                            <tr>
                                <td class="text-center"><?php echo ($j + 1 + $sno); ?></td>
                                <td><?php echo $row['po_date'] ? date('d-m-Y', strtotime($row['po_date'])) : '-'; ?></td>

                                <td><?php echo htmlspecialchars($row['our_po_no'] ?? '-'); ?></td>
                                <td><?php echo $row['company_name']; ?><br>
                                    <span class="label label-success"><?php echo $row['tender_details']; ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($row['customer_name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['enquiry_no'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['quotation_no'] ?? '-'); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['customer_po_no'] ?? '-'); ?></strong></td>
                                <td><?php echo $row['delivery_date'] ? date('d-m-Y', strtotime($row['delivery_date'])) : '-'; ?>
                                </td>
                                <?php
                                $po_status = $row['po_status'];

                                $status_colors = [
                                    'Open' => 'primary',
                                    'In Progress' => 'warning',
                                    'Completed' => 'success',
                                    'Cancelled' => 'danger',
                                ];

                                $color = isset($status_colors[$po_status]) ? $status_colors[$po_status] : 'default';
                                ?>
                                <td>
                                    <span class="label label-<?php echo $color; ?>">
                                        <?php echo htmlspecialchars($po_status ?? 'N/A'); ?>
                                    </span>
                                </td>

                                <!-- VIEW -->
                                <!-- <td class="text-center">
                                    <a href="<?php echo site_url('customer-tender-po-view/' . $row['tender_po_id']); ?>"
                                        class="btn btn-info btn-xs" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td> -->

                                <!-- EDIT -->
                                <td class="text-center">
                                    <a href="<?php echo site_url('customer-tender-po-edit/' . $row['tender_po_id']); ?>"
                                        class="btn btn-primary btn-xs" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>

                                <!-- DELETE -->
                                <td class="text-center">
                                    <button value="<?php echo $row['tender_po_id']; ?>" class="del_record btn btn-danger btn-xs"
                                        title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center text-danger">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="box-footer">
            <div class="row">
                <div class="col-sm-6">
                    <label>Total Records: <strong><?php echo $total_records; ?></strong></label>
                </div>
                <div class="col-sm-6 text-right">
                    <?php echo $pagination; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?> 