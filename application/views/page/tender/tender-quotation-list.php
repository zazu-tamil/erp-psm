<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Tender</a></li>
        <li class="active">Tender Quotation List</li>
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
                        <label>Company</label>
                        <?php echo form_dropdown('srch_company_id', $company_opt, set_value('srch_company_id', $srch_company_id), 'id="srch_company_id" class="form-control select2"'); ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Customer</label>
                        <?php echo form_dropdown('srch_customer_id', $customer_opt, set_value('srch_customer_id', $srch_customer_id), 'id="srch_customer_id" class="form-control select2"'); ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Tender Enquiry No</label>
                        <?php echo form_dropdown('srch_tender_enquiry_id', $tender_enquiry_opt, set_value('srch_tender_enquiry_id', $srch_tender_enquiry_id), 'id="srch_tender_enquiry_id" class="form-control select2"'); ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Status</label>
                        <?php echo form_dropdown('srch_status', $status_opt, set_value('srch_status', $srch_status), 'id="srch_status" class="form-control select2"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- List Table -->
    <div class="box box-info">
        <div class="box-header with-border">
            <a href="<?php echo site_url('add-tender-quotation'); ?>" class="btn btn-success">
                <i class="fa fa-plus-circle"></i> Add New
            </a>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th>Quotation No</th>
                        <th>Tender Ref No</th>
                        <th>Tender Enquiry No</th>
                        <th>Quote Date</th>
                        <th>Company</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th class="text-center" colspan="3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)) : ?>
                        <?php foreach ($record_list as $j => $row) : ?>
                            <tr>
                                <td class="text-center"><?php echo ($j + 1 + $sno); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['quotation_no']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['tender_ref_no']); ?></td>
                                <td><?php echo htmlspecialchars($row['tender_enquiry_no']); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($row['quote_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                <td>
                                    <span class="label label-<?php echo $row['status'] == 'Active' ? 'success' : 'danger'; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>


                                  <!-- PRINT -->
                                <td class="text-center">
                                    <a href="<?php echo site_url('tender-quotation-print/' . $row['tender_quotation_id']); ?>" 
                                       target="_blank"
                                       class="btn btn-info btn-xs" title="Print">
                                        <i class="fa fa-print"></i>
                                    </a>
                                </td>
                                
                                <!-- EDIT -->
                                <td class="text-center">
                                    <a href="<?php echo site_url('tender-quotation-edit/' . $row['tender_quotation_id']); ?>" 
                                       class="btn btn-primary btn-xs" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>

                              

                                <!-- DELETE -->
                                <td class="text-center">
                                    <button value="<?php echo $row['tender_quotation_id']; ?>" 
                                            class="del_record btn btn-danger btn-xs" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="11" class="text-center text-danger">No records found.</td>
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