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
                        <label for="srch_company_id">Company <span style="color:red;">*</span></label>
                        <?php echo form_dropdown('srch_company_id', ['' => 'All'] + $company_opt, set_value('srch_company_id', $srch_company_id), 'id="srch_company_id" class="form-control select2"'); ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_customer_id">Customer <span style="color:red;">*</span></label>
                        <?php echo form_dropdown('srch_customer_id', ['' => 'All'] + $customer_opt, set_value('srch_customer_id', $srch_customer_id), 'id="srch_customer_id" class="form-control select2"'); ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_tender_enquiry_id">Tender Enquiry No <span style="color:red;">*</span></label>
                        <?php echo form_dropdown('srch_tender_enquiry_id', ['' => 'All'] + $tender_enquiry_opt, set_value('srch_tender_enquiry_id', $srch_tender_enquiry_id), 'id="srch_tender_enquiry_id" class="form-control select2"'); ?>
                    </div> 
                      <div class="form-group col-md-3">
                            <label>Vendor Name <span style="color:red;">*</span></label>
                            <?php echo form_dropdown('srch_vendor_id', ['' => 'All'] + $vendor_opt, set_value('srch_vendor_id' , $srch_vendor_id), 'id="srch_vendor_id" class="form-control select2"'); ?>
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
            <a href="<?php echo site_url('vendor-pur-inward-add'); ?>" class="btn btn-success">
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
                        <th>Invoice Date</th>
                        <th>Invoice No</th>
                        <th>Company / RFQ No</th>
                        <th>Customer</th> 
                        <th>Vendor</th>  
                        <th class="text-center" colspan="3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)): ?>
                        <?php foreach ($record_list as $j => $row): ?>
                            <tr>
                                <td class="text-center"><?php echo ($j + 1 + $sno); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($row['invoice_date'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['invoice_no']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['company_name'] ?? '-'); ?> <br><small
                                        class="label label-success"><?php echo htmlspecialchars($row['tender_details'] ?? '-'); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($row['customer_name'] ?? '-'); ?></td> 
                                <td><?php echo htmlspecialchars($row['vendor_name'] ?? '-'); ?></td>
                          
                                <!-- EDIT -->
                                <!-- <td class="text-center">
                                    <a href="<?php echo site_url('vendor-purchase-bill-edit/' . $row['vendor_purchase_invoice_id']); ?>"
                                        class="btn btn-primary btn-xs" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td> -->
                                <!-- DELETE -->
                                <td class="text-center">
                                    <button value="<?php echo $row['vendor_purchase_invoice_id']; ?>" class="del_record btn btn-danger btn-xs"
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