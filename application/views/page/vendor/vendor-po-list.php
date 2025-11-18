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
                        <label for="srch_company_id">Company <span style="color:red;">*</span></label>
                        <?php echo form_dropdown('srch_company_id', ['' => 'All'] + $company_opt, set_value('srch_company_id' , $srch_company_id), 'id="srch_company_id" class="form-control"'); ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_customer_id">Customer <span style="color:red;">*</span></label>
                        <?php echo form_dropdown('srch_customer_id', ['' => 'All']+$customer_opt, set_value('srch_customer_id' , $srch_customer_id), 'id="srch_customer_id" class="form-control"'); ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_tender_enquiry_id">Tender Enquiry No <span style="color:red;">*</span></label>
                        <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'All'] + $tender_enquiry_opt, set_value('srch_tender_enquiry_id' ,$srch_tender_enquiry_id), 'id="srch_tender_enquiry_id" class="form-control"'); ?>
                    </div> 

                    <div class="form-group col-md-3">
                        <label>Status</label>
                        <?php echo form_dropdown('srch_po_status', $po_status_opt, set_value('srch_po_status', $srch_po_status), 'id="srch_po_status" class="form-control select2"'); ?>
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
                        <th>PO No</th>
                        <th>Tender Enquiry No</th>
                        <th>PO Date</th>
                        <th>Customer</th>
                        <th>Vendor</th>
                        <th>Opening Date</th>
                        <th>Closing Date</th>
                        <th>PO Status</th>
                        <th class="text-center" colspan="3">Action</th> <!-- Now 3 columns -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)): ?>
                        <?php foreach ($record_list as $j => $row): ?>
                            <tr>
                                <td class="text-center"><?php echo ($j + 1 + $sno); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['po_no']); ?></strong></td>
                                <td><strong><?php echo htmlspecialchars($row['tender_enquiry_no']); ?></strong></td>
                                <td><?php echo date('d-m-Y', strtotime($row['po_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['customer_name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['vendor_name'] ?? '-'); ?></td>
                                <td><?php echo $row['opening_date'] ? date('d-m-Y H:i', strtotime($row['opening_date'])) : '-'; ?>
                                </td>
                                <td><?php echo $row['closing_date'] ? date('d-m-Y H:i', strtotime($row['closing_date'])) : '-'; ?>
                                </td>
                                <td>
                                    <?php
                                    $badge = 'default';
                                    if ($row['po_status'] == 'Open') {
                                        $badge = 'primary';
                                    } elseif ($row['po_status'] == 'In Progress') {
                                        $badge = 'warning';
                                    } elseif ($row['po_status'] == 'Completed') {
                                        $badge = 'success';
                                    } elseif ($row['po_status'] == 'Cancelled') {
                                        $badge = 'danger';
                                    }
                                    ?>
                                    <span class="label label-<?php echo $badge; ?>">
                                        <?php echo htmlspecialchars($row['po_status']); ?>
                                    </span>
                                </td>



                                <!-- PRINT / VIEW -->
                                <td class="text-center">
                                    <a href="<?php echo site_url('vendor-po-print/' . $row['vendor_po_id']); ?>" target="_blank"
                                        class="btn btn-info btn-xs" title="Print / View">
                                        <i class="fa fa-print"></i>
                                    </a>
                                </td>
                                <!-- EDIT -->
                                <td class="text-center">
                                    <a href="<?php echo site_url('vendor-po-edit/' . $row['vendor_po_id']); ?>"
                                        class="btn btn-primary btn-xs" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                <!-- DELETE -->
                                <td class="text-center">
                                    <button value="<?php echo $row['vendor_po_id']; ?>" class="del_record btn btn-danger btn-xs"
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