<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Tender Reports</a></li>
        <li class="active"><?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">

    <!-- KPI Summary Row -->
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-truck"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Uninvoiced DCs</span>
                    <span class="info-box-number"><?php echo (int)$kpi_total_uninvoiced_dcs; ?></span>
                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                    <span class="progress-description"><?php echo (int)$kpi_not_invoiced_count; ?> completely unbilled</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Delivered Units Unbilled</span>
                    <span class="info-box-number"><?php echo number_format($kpi_total_pending_qty, 2); ?></span>
                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                    <span class="progress-description">Units awaiting invoice creation</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Overdue Aging (> 15 Days)</span>
                    <span class="info-box-number"><?php echo (int)$kpi_overdue_15_count; ?></span>
                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                    <span class="progress-description">Delivered > 15 days ago</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-orange">
                <span class="info-box-icon"><i class="fa fa-hourglass-half"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Partially Invoiced</span>
                    <span class="info-box-number"><?php echo (int)$kpi_partial_invoiced_count; ?></span>
                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                    <span class="progress-description">Balance qty remaining</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title text-white"><i class="fa fa-filter"></i> Search Filter</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form method="post" action="" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="srch_from_date">DC From Date</label>
                        <input type="date" name="srch_from_date" id="srch_from_date" class="form-control"
                            value="<?php echo set_value('srch_from_date', $srch_from_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_to_date">DC To Date</label>
                        <input type="date" name="srch_to_date" id="srch_to_date" class="form-control"
                            value="<?php echo set_value('srch_to_date', $srch_to_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="customer_id">Customer Name</label>
                        <?php echo form_dropdown('customer_id', $customer_opt, set_value('customer_id', $customer_id), 'id="customer_id" class="form-control"'); ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="invoicing_status">Invoicing Status</label>
                        <?php 
                        $status_opts = [
                            '' => 'All Pending (Not Invoiced + Partial)',
                            'Not Invoiced' => 'Not Invoiced Only (0% Invoiced)',
                            'Partial Invoiced' => 'Partially Invoiced Only',
                            'All' => 'All (Including Fully Invoiced)'
                        ];
                        echo form_dropdown('invoicing_status', $status_opts, set_value('invoicing_status', $invoicing_status), 'id="invoicing_status" class="form-control"'); 
                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6" style="margin-top: 10px;">
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show Report</button>
                        <a href="<?php echo site_url('customer-dc-without-invoice-report?reset=1'); ?>" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</a>
                        <button type="submit" name="export_excel" value="1" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Export Excel (Server-side)</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Box -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-list"></i> Customer Delivery Challan Records</h3>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped" id="tbl-data">
                <thead class="bg-gray">
                    <tr style="font-weight:bold;">
                        <th style="width: 40px;">S.No</th>
                        <th>DC Date</th>
                        <th>DC No</th>
                        <th>Customer Name</th>
                        <th>Customer PO No & Date</th>
                        <th>RFQ / Enquiry No</th>
                        <th class="text-right">Delivered Qty</th>
                        <th class="text-right">Invoiced Qty</th>
                        <th class="text-right">Pending Qty</th>
                        <th>Invoice Ref</th>
                        <th class="text-center">Invoicing Status</th>
                        <th class="text-center">Aging (Days)</th>
                        <th class="text-center" style="width: 90px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $sum_delivered_qty = 0;
                $sum_invoiced_qty = 0;
                $sum_pending_qty = 0;

                if (!empty($record_list)):
                    foreach ($record_list as $i => $row):
                        $sum_delivered_qty += (float)$row['delivered_qty'];
                        $sum_invoiced_qty += (float)$row['invoiced_qty'];
                        $sum_pending_qty += (float)$row['pending_qty'];
                        $aging_days = (int)$row['aging_days'];
                ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo !empty($row['dc_date']) ? date('d/m/Y', strtotime($row['dc_date'])) : '-'; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($row['dc_no']); ?></strong>
                            <?php if (!empty($row['tender_dc_id'])): ?>
                                <br><a href="<?php echo site_url('tender-dc-print/' . $row['tender_dc_id']); ?>" target="_blank" class="text-muted" style="font-size:11px;">
                                    <i class="fa fa-print"></i> View DC
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($row['customer_name']); ?></strong>
                            <?php if (!empty($row['customer_code'])): ?>
                                <br><small class="text-muted"><?php echo htmlspecialchars($row['customer_code']); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['customer_po_no'])): ?>
                                <?php echo htmlspecialchars($row['customer_po_no']); ?>
                                <?php if (!empty($row['po_date'])): ?>
                                    <br><small class="text-muted"><i class="fa fa-calendar"></i> <?php echo date('d/m/Y', strtotime($row['po_date'])); ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['enquiry_no'])): ?>
                                <span class="label label-default"><?php echo htmlspecialchars($row['enquiry_no']); ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td align="right" style="font-weight: bold; color: #0073b7;">
                            <?php echo number_format($row['delivered_qty'], 2); ?>
                        </td>
                        <td align="right" style="color: green;">
                            <?php echo number_format($row['invoiced_qty'], 2); ?>
                        </td>
                        <td align="right" style="font-weight: bold; color: red;">
                            <?php echo number_format($row['pending_qty'], 2); ?>
                        </td>
                        <td>
                            <?php if (!empty($row['invoice_nos']) && $row['invoice_nos'] !== 'None'): ?>
                                <small><?php echo $row['invoice_nos']; ?></small>
                            <?php else: ?>
                                <span class="text-muted">Not Generated</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php 
                            if ($row['invoicing_status'] == 'Not Invoiced') {
                                echo '<span class="label label-danger"><i class="fa fa-times-circle"></i> Not Invoiced</span>';
                            } elseif ($row['invoicing_status'] == 'Partial Invoiced') {
                                echo '<span class="label label-warning"><i class="fa fa-hourglass-half"></i> Partial Invoiced</span>';
                            } else {
                                echo '<span class="label label-success"><i class="fa fa-check-circle"></i> Fully Invoiced</span>';
                            }
                            ?>
                        </td>
                        <td class="text-center">
                            <?php if ($aging_days > 15): ?>
                                <span class="badge bg-red" title="Overdue > 15 days"><?php echo $aging_days; ?> d</span>
                            <?php elseif ($aging_days > 7): ?>
                                <span class="badge bg-yellow" title="Follow up > 7 days"><?php echo $aging_days; ?> d</span>
                            <?php else: ?>
                                <span class="badge bg-blue" title="Recent delivery"><?php echo $aging_days; ?> d</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-info btn-xs btn-view-items"
                                data-dc-id="<?php echo (int)$row['tender_dc_id']; ?>">
                                <i class="fa fa-eye"></i> Items
                            </button>
                        </td>
                    </tr>
                <?php 
                    endforeach;
                endif; 
                ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background-color: #f3f4f5; font-size: 14px;">
                        <td colspan="6" class="text-right">TOTAL:</td>
                        <td align="right" style="color: #0073b7;"><?php echo number_format($sum_delivered_qty, 2); ?></td>
                        <td align="right" style="color: green;"><?php echo number_format($sum_invoiced_qty, 2); ?></td>
                        <td align="right" style="color: red;"><?php echo number_format($sum_pending_qty, 2); ?></td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</section>

<!-- Item Comparison Modal -->
<div class="modal fade" id="itemsModal" tabindex="-1" role="dialog" aria-labelledby="itemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="itemsModalLabel" style="color: white;">
                    <i class="fa fa-truck"></i> Delivery Challan Items & Billing Status
                </h4>
            </div>
            <div class="modal-body" id="itemsModalBody">
                <!-- Loaded dynamically via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>

