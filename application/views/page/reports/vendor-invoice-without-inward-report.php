<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Vendor</a></li>
        <li class="active"> <?php echo $title ?></li>
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
                        <label for="srch_from_date">Invoice From Date</label>
                        <input type="date" name="srch_from_date" id="srch_from_date" class="form-control"
                            value="<?php echo set_value('srch_from_date', $srch_from_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_to_date">Invoice To Date</label>
                        <input type="date" name="srch_to_date" id="srch_to_date" class="form-control"
                            value="<?php echo set_value('srch_to_date', $srch_to_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_po_from_date">PO From Date</label>
                        <input type="date" name="srch_po_from_date" id="srch_po_from_date" class="form-control"
                            value="<?php echo set_value('srch_po_from_date', $srch_po_from_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_po_to_date">PO To Date</label>
                        <input type="date" name="srch_po_to_date" id="srch_po_to_date" class="form-control"
                            value="<?php echo set_value('srch_po_to_date', $srch_po_to_date); ?>">
                    </div>
                </div>
                <div class="row">

                    <div class="form-group col-md-3">
                        <label for="vendor_id">Vendor Name</label>
                        <?php echo form_dropdown('vendor_id', $vendor_opt, set_value('vendor_id', $vendor_id), 'id="vendor_id" class="form-control "'); ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="payment_status">Payment Status</label>
                        <?php echo form_dropdown('payment_status', ['' => 'All', 'Paid' => 'Paid', 'Unpaid' => 'Unpaid', 'Partially Paid' => 'Partially Paid'], set_value('payment_status', $payment_status), 'id="payment_status" class="form-control "'); ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inward_status">Inward Status</label>
                        <?php echo form_dropdown('inward_status', ['' => 'All', 'Inward Entered' => 'Inward Entered', 'Partial Inward' => 'Partial Inward', 'Inward Not Entered' => 'Inward Not Entered'], set_value('inward_status', $inward_status), 'id="inward_status" class="form-control "'); ?>
                    </div>
                    <div class="form-group col-md-3 text-left">
                        <br>
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show</button>
                        <a href="<?php echo site_url('vendor-invoice-without-inward-report?reset=1'); ?>" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- List Table -->
    <div class="box box-info">
        <div class="box-header with-border">
            <div class="box-title">
                <h3 class="box-title text-white">Vendor PO Invoice & Payment List</h3>
            </div>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <?php
        $grand_total = 0;
        $paid_total = 0;
        $invoice_no_count = 0;
        ?>

        <div class="box-body table-responsive">

            <table class="table table-bordered table-striped" id="tbl-data">
                <thead class="bg-gray">
                    <tr style="font-weight:bold;">
                        <th>S.No</th>
                        <th>PO No</th>
                        <th>PO Date</th>
                        <th>Invoice No</th>
                        <th>Invoice Date</th>
                        <th>Vendor Name</th>
                        <th>Inward Status</th>
                        <th>Last Payment Date</th>
                        <th class="text-right">Invoice Amt</th>
                        <th class="text-right">Paid Amt</th>
                        <th class="text-right">Balance</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $balance_total = 0;
                foreach ($record_list as $i => $row) {
                    $balance = $row['total_amount'] - $row['paid_amount'];
                    $grand_total += $row['total_amount'];
                    $paid_total += $row['paid_amount'];
                    $balance_total += $balance;
                    $invoice_no_count++;
                    ?>
                    <tr>
                        <td><?php echo  $i + 1;  ?></td>
                        <td><?php echo $row['po_no'] ? $row['po_no'] : 'Direct'; ?></td>
                        <td><?php echo $row['po_date'] ? date('d/m/Y', strtotime($row['po_date'])) : ''; ?></td>
                        <td><?php echo $row['invoice_no']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['invoice_date'])); ?></td>
                        <td><?php echo $row['vendor_name']; ?></td>
                        <td>
                            <?php 
                                if($row['inward_status'] == 'Inward Entered') {
                                    echo '<span class="label label-success">'.$row['inward_status'].'</span>';
                                } elseif($row['inward_status'] == 'Partial Inward') {
                                    echo '<span class="label label-warning">'.$row['inward_status'].'</span>';
                                } elseif($row['inward_status'] == 'Inward Not Entered') {
                                    echo '<span class="label label-danger">'.$row['inward_status'].'</span>';
                                } else {
                                    echo '<span class="label label-default">'.$row['inward_status'].'</span>';
                                }
                            ?>
                        </td>
                        <td><?php echo $row['last_payment_date'] ? date('d/m/Y', strtotime($row['last_payment_date'])) : ''; ?></td>
                        <td align="right"><?php echo number_format($row['total_amount'], 3); ?></td>
                        <td align="right"><?php echo number_format($row['paid_amount'], 3); ?></td>
                        <td align="right"><?php echo number_format($balance, 3); ?></td>
                        <td class="text-center">
                            <?php if ($row['vendor_po_id'] > 0): ?>
                                <button type="button" class="btn btn-info btn-xs btn-view-items" data-invoice-id="<?= $row['vendor_purchase_invoice_id'] ?>" data-po-id="<?= $row['vendor_po_id'] ?>">View Items</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                <!-- GRAND TOTAL -->
                <tr style="font-weight:bold; background:green; font-size:16px; color:#ffffff;">
                    <td colspan="2" class="text-right"> Total Invoice :</td>
                    <td><?= $invoice_no_count; ?></td>
                    <td colspan="5" class="text-right">Grand Total :</td>
                    <td class="text-right"><?= number_format($grand_total, 3); ?></td>
                    <td class="text-right"><?= number_format($paid_total, 3); ?></td>
                    <td class="text-right"><?= number_format($balance_total, 3); ?></td>
                    <td></td>
                </tr>
                </tfoot>
            </table>

        </div>
    </div>
</section>

<!-- Items Modal -->
<div class="modal fade" id="itemsModal" tabindex="-1" role="dialog" aria-labelledby="itemsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="itemsModalLabel">Item-Wise Invoice vs Inward Comparison</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="itemsModalBody">
        <!-- Content loaded via AJAX -->
        <div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
