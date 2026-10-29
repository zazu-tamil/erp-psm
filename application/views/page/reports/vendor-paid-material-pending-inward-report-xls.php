<?php
$grand_paid = 0;
$grand_ordered = 0;
$grand_inward = 0;
$grand_pending = 0;
$total_count = 0;
?>
<table border="1">
    <thead>
        <tr>
            <th colspan="12" style="font-size:16px; font-weight:bold; text-align:center; background-color:#3c8dbc; color:#ffffff;">
                Vendor Paid But Material Pending Inward Report
            </th>
        </tr>
        <tr>
            <th colspan="12" style="font-size:12px; text-align:left; background-color:#f4f4f4;">
                From Date: <?php echo !empty($srch_from_date) ? date('d/m/Y', strtotime($srch_from_date)) : 'All'; ?> &nbsp;|&nbsp; 
                To Date: <?php echo !empty($srch_to_date) ? date('d/m/Y', strtotime($srch_to_date)) : 'All'; ?> &nbsp;|&nbsp; 
                Vendor: <?php echo (!empty($vendor_id) && isset($vendor_opt[$vendor_id])) ? $vendor_opt[$vendor_id] : 'All Vendors'; ?> &nbsp;|&nbsp;
                Generated On: <?php echo date('d/m/Y H:i:s'); ?>
            </th>
        </tr>
        <tr style="background-color:#337ab7; color:white; font-weight:bold;">
            <th>S.No</th>
            <th>Payment Date</th>
            <th>Payment Type</th>
            <th>Vendor Name</th>
            <th>PO No</th>
            <th>PO Date</th>
            <th>Invoice No</th>
            <th>Paid Amount</th>
            <th>Ordered Qty</th>
            <th>Inward Qty</th>
            <th>Pending Qty</th>
            <th>Inward Status</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    if (!empty($record_list)):
        foreach ($record_list as $i => $row): 
            $grand_paid += $row['paid_amount'];
            $grand_ordered += $row['ordered_qty'];
            $grand_inward += $row['inward_qty'];
            $grand_pending += $row['pending_qty'];
            $total_count++;
    ?>
        <tr>
            <td><?php echo $i + 1; ?></td>
            <td><?php echo !empty($row['payment_date']) ? date('d/m/Y', strtotime($row['payment_date'])) : ''; ?></td>
            <td><?php echo htmlspecialchars($row['payment_type']); ?></td>
            <td><?php echo htmlspecialchars($row['vendor_name']); ?></td>
            <td><?php echo !empty($row['po_no']) ? htmlspecialchars($row['po_no']) : '-'; ?></td>
            <td><?php echo !empty($row['po_date']) ? date('d/m/Y', strtotime($row['po_date'])) : ''; ?></td>
            <td><?php echo !empty($row['invoice_no']) ? htmlspecialchars($row['invoice_no']) : '-'; ?></td>
            <td align="right"><?php echo number_format($row['paid_amount'], 3); ?></td>
            <td align="right"><?php echo number_format($row['ordered_qty'], 2); ?></td>
            <td align="right"><?php echo number_format($row['inward_qty'], 2); ?></td>
            <td align="right"><?php echo number_format($row['pending_qty'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['inward_status']); ?></td>
        </tr>
    <?php 
        endforeach;
    endif;
    ?>
    </tbody>
    <tfoot>
        <tr style="font-weight:bold; background-color:#28a745; color:#ffffff; font-size:13px;">
            <td colspan="7" align="right">Total (<?php echo $total_count; ?> Records):</td>
            <td align="right"><?php echo number_format($grand_paid, 3); ?></td>
            <td align="right"><?php echo number_format($grand_ordered, 2); ?></td>
            <td align="right"><?php echo number_format($grand_inward, 2); ?></td>
            <td align="right"><?php echo number_format($grand_pending, 2); ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>

