<?php include_once(VIEWPATH . '/inc/header.php'); ?>
<section class="content-header">
    <h1>Customer Opening Balance List</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Master</a></li>
        <li class="active"> Customer Opening Balance List</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    
    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')) { ?>
        <div class="alert alert-success alert-dismissible auto-hide">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-check"></i> Success!</h4>
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php } ?>
    <?php if ($this->session->flashdata('error')) { ?>
        <div class="alert alert-danger alert-dismissible auto-hide">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-ban"></i> Error!</h4>
            <?php echo $this->session->flashdata('error'); ?>
        </div>
    <?php } ?>

    <!-- Search Filter Box -->
    <div class="box box-success no-print">
        <div class="box-header with-border" style="background: #f4f4f4; border-bottom: 1px solid #e5e5e5;">
            <h3 class="box-title" style="font-weight: 600; color: #333;"><i class="fa fa-filter"></i> Search Filter</h3>
        </div>
        <div class="box-body" style="padding: 15px;">
            <form method="post" action="<?php echo site_url('customer-opening-balance-list'); ?>" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label style="color: #475569; font-weight: 600;">Filter by Customer</label>
                        <?php echo form_dropdown('srch_customer_id', ['' => 'All Customers'] + $customer_opt, $srch_customer_id, 'id="customer_select_filter" class="form-control select2"'); ?>
                    </div>
                    <div class="form-group col-md-4 text-left">
                        <br />
                        <button class="btn btn-success" name="btn_show" value="Show" style="font-weight: 600;"><i class="fa fa-search"></i> Filter</button>
                        <button type="submit" name="btn_reset" value="Reset" class="btn btn-default" style="margin-left: 5px; font-weight: 600;"><i class="fa fa-refresh"></i> Reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="box box-success">
        <div class="box-header with-border">
            <button type="button" class="btn btn-success mb-1" style="font-weight: 600;" data-toggle="modal" data-target="#add_modal">
                <span class="fa fa-plus-circle"></span> Add New Opening Balance
            </button>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped" id="opening_balance_table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 8%;">S.No</th>
                        <th>Customer Name</th>
                        <th>Opening Date</th>
                        <th>Balance Type</th>
                        <th class="text-right">Opening Amount</th>
                        <th>Remarks</th>
                        <th colspan="2" class="text-center" style="width: 12%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($record_list)) {
                        foreach ($record_list as $j => $ls) {
                            ?>
                            <tr>
                                <td class="text-center"><?php echo ($j + 1 + $sno); ?></td>
                                <td style="font-weight: 600; color: #2c3e50;">
                                    <?php echo htmlspecialchars($ls['customer_name']); ?>
                                </td>
                                <td><?php echo date('d-m-Y', strtotime($ls['opening_date'])); ?></td>
                                <td>
                                    <?php if ($ls['balance_type'] == 'DR') { ?>
                                        <span class="label label-success" title="Receivable from Customer">DR (Receivable from Customer)</span>
                                    <?php } else { ?>
                                        <span class="label label-danger" title="Advance Received / Credit">CR (Advance Received)</span>
                                    <?php } ?>
                                </td>
                                <td class="text-right" style="font-weight: bold; color: #2c3e50;">
                                    <?php echo number_format($ls['opening_amount'], 2); ?>
                                </td>
                                <td style="color: #64748b; font-style: italic;"><?php echo htmlspecialchars($ls['remarks'] ?? '—'); ?></td>
                                <td class="text-center">
                                    <button data-toggle="modal" data-target="#edit_modal" value="<?php echo $ls['opening_id']; ?>"
                                        class="edit_record btn btn-primary btn-xs" title="Edit"><i
                                            class="fa fa-edit"></i></button>
                                </td>
                                <td class="text-center">
                                    <button value="<?php echo $ls['opening_id']; ?>" class="del_record btn btn-danger btn-xs"
                                        title="Delete"><i class="fa fa-remove"></i></button>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="8" class="text-center" style="padding: 20px; color: #94a3b8; font-style: italic;">
                                No opening balances recorded.
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>

            <!-- Add Modal -->
            <div class="modal fade" id="add_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo site_url('customer-opening-balance-list'); ?>" id="frmadd">
                            <div class="modal-header" style="background: #00a65a; color: white;">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel" style="color: white; font-weight: 600;">Add Customer Opening Balance</h3>
                                <input type="hidden" name="mode" value="Add" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Customer Name</label>
                                        <?php echo form_dropdown('customer_id', ['' => 'Select Customer'] + $customer_opt_add, '', 'id="customer_select_add" class="form-control select2" required="true"'); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Opening Date</label>
                                        <input class="form-control" type="date" name="opening_date" value="<?php echo date('Y-m-d'); ?>" required="true">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Balance Type</label>
                                        <select name="balance_type" class="form-control" required="true">
                                            <option value="DR">DR (Receivable from Customer)</option>
                                            <option value="CR">CR (Advance Received from Customer)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Opening Amount</label>
                                        <input class="form-control" type="number" step="0.01" name="opening_amount" placeholder="0.00" required="true">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Remarks</label>
                                        <textarea class="form-control" name="remarks" placeholder="Enter optional remarks..." rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <input type="submit" name="Save" value="Save" class="btn btn-success" style="font-weight: 600;" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="edit_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo site_url('customer-opening-balance-list'); ?>" id="frmedit">
                            <div class="modal-header" style="background: #3c8dbc; color: white;">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h3 class="modal-title" id="scrollmodalLabel" style="color: white; font-weight: 600;">Edit Customer Opening Balance</h3>
                                <input type="hidden" name="mode" value="Edit" />
                                <input type="hidden" name="opening_id" id="opening_id" value="" />
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Customer Name</label>
                                        <?php echo form_dropdown('customer_id', ['' => 'Select Customer'] + $customer_opt, '', 'id="customer_select_edit" class="form-control select2" required="true"'); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Opening Date</label>
                                        <input class="form-control" type="date" name="opening_date" id="opening_date" required="true">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Balance Type</label>
                                        <select name="balance_type" id="balance_type" class="form-control" required="true">
                                            <option value="DR">DR (Receivable from Customer)</option>
                                            <option value="CR">CR (Advance Received from Customer)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Opening Amount</label>
                                        <input class="form-control" type="number" step="0.01" name="opening_amount" id="opening_amount" required="true">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Remarks</label>
                                        <textarea class="form-control" name="remarks" id="remarks" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <input type="submit" name="Save" value="Update" class="btn btn-primary" style="font-weight: 600;" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
        <div class="box-footer">
            <div class="form-group col-sm-6">
                <label>Total Records : <?php echo $total_records; ?></label>
            </div>
            <div class="form-group col-sm-6">
                <?php echo $pagination; ?>
            </div>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
