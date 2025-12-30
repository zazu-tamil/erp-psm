<?php include_once(VIEWPATH . 'inc/header.php');
// echo '<pre>';
// print_r($_POST);
// echo '</pre>';
?>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Tender</a></li>
        <li class="active">Tender Enquiry List</li>
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
                        <label>Company</label>
                        <?php echo form_dropdown('srch_company_id', $company_opt, set_value('srch_company_id', $srch_company_id), 'id="srch_company_id" class="form-control select2"'); ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Customer</label>
                        <div class="form-group">
                            <?php echo form_dropdown('srch_customer_id', ['' => 'All'] + $customer_opt, $srch_customer_id, 'id="srch_customer_id" class="form-control select2" '); ?>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="srch_customer_rfq_id">Tender RFQ No</label>
                        <?php echo form_dropdown('srch_customer_rfq_id', ['' => 'All'] + $customer_rfq_opt, $srch_customer_rfq_id, 'id="srch_customer_rfq_id" class="form-control select2"'); ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Customer Contact Person</label> 
                        <?php
                        echo form_dropdown(
                            'srch_customer_contact_id',
                            ['' => 'All'] + $customer_contact_opt,
                            set_value('srch_customer_contact_id' , $srch_customer_contact_id),
                            'id="srch_srch_customer_contact_id" class="form-control" '
                        );
                        ?> 
                    </div> 
                    <div class="form-group col-md-3">
                        <label>Status</label>
                        <?php echo form_dropdown('srch_status', $tender_status_opt, set_value('srch_status', $srch_status), 'id="srch_status" class="form-control"'); ?>
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
            <a href="<?php echo site_url('add-tender-enquiry'); ?>" class="btn btn-success">
                <i class="fa fa-plus-circle"></i> Add New
            </a>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th>Enquiry Date</th>
                        <th>Company Refrerence No</th>
                        <th>Customer</th>
                        <th>Customer Code</th>
                        <th>Customer Contact Name</th>
                        <th>RFQ No</th>
                        <th>Closing Date</th>
                        <th>Tender Status</th>
                        <th class="text-center" colspan="2">Action</th>
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
                                <td><?php echo $row['customer_name']; ?></td>
                                <td><?php echo $row['customer_code']; ?></td>
                                <td><?php echo $row['contact_person_name']; ?></td>
                                <td><?php echo $row['enquiry_no']; ?></td>
                                <td><?php echo $row['closing_date'] ? date('d-m-Y H:i', strtotime($row['closing_date'])) : '-'; ?>
                                </td>
                                <?php
                                $status = $row['tender_status'];

                                $badge_colors = [
                                    'Open' => 'primary',
                                    'Quoted' => 'info',
                                    'Won' => 'success',
                                    'Lost' => 'danger',
                                    'On Hold' => 'warning',
                                ];

                                $color = isset($badge_colors[$status]) ? $badge_colors[$status] : 'default';
                                ?>
                                <td>
                                    <span class="label label-<?php echo $color; ?>">
                                        <?php echo $status; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo site_url('tender-enquiry-edit/' . $row['tender_enquiry_id']); ?>"
                                        class="btn btn-primary btn-xs" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <button value="<?php echo $row['tender_enquiry_id']; ?>"
                                        class="del_record btn btn-danger btn-xs" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-danger">No records found.</td>
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
