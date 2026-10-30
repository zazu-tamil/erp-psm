<?php
$grand_delivered = 0;
$grand_invoiced = 0;
$grand_pending = 0;
$total_count = 0;
?>
<table border="1">
    <thead>
        <tr>
            <th colspan="13" style="font-size:16px; font-weight:bold; text-align:center; background-color:#3c8dbc; color:#ffffff;">
                Customer Delivery Challan Generated But Not Invoiced Report
            </th>
        </tr>
        <tr>
            <th colspan="13" style="font-size:12px; text-align:left; background-color:#f4f4f4;">
                From Date: <?php echo !empty($srch_from_date) ? date('d/m/Y', strtotime($srch_from_date)) : 'All'; ?> &nbsp;|&nbsp; 
                To Date: <?php echo !empty($srch_to_date) ? date('d/m/Y', strtotime($srch_to_date)) : 'All'; ?> &nbsp;|&nbsp; 
                Customer: <?php echo (!empty($customer_id) && isset($customer_opt[$customer_id])) ? $customer_opt[$customer_id] : 'All Customers'; ?> &nbsp;|&nbsp;
                Generated On: <?php echo date('d/m/Y H:i:s'); ?>
            </th>
        </tr>
        <tr style="background-color:#337ab7; color:white; font-weight:bold;">
            <th>S.No</th>
            <th>DC Date</th>
            <th>DC No</th>
            <th>Customer Name</th>
            <th>Customer PO No</th>
            <th>Customer PO Date</th>
            <th>RFQ / Enquiry No</th>
            <th>Delivered Qty</th>
            <th>Invoiced Qty</th>
            <th>Pending Qty</th>
            <th>Invoice Reference</th>
            <th>Invoicing Status</th>
            <th>Aging (Days)</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    if (!empty($record_list)):
        foreach ($record_list as $i => $row): 
            $grand_delivered += (float)$row['delivered_qty'];
            $grand_invoiced += (float)$row['invoiced_qty'];
            $grand_pending += (float)$row['pending_qty'];
            $total_count++;
    ?>
        <tr>
            <td><?php echo $i + 1; ?></td>
            <td><?php echo !empty($row['dc_date']) ? date('d/m/Y', strtotime($row['dc_date'])) : ''; ?></td>
            <td><?php echo htmlspecialchars($row['dc_no']); ?></td>
            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
            <td><?php echo !empty($row['customer_po_no']) ? htmlspecialchars($row['customer_po_no']) : '-'; ?></td>
            <td><?php echo !empty($row['po_date']) ? date('d/m/Y', strtotime($row['po_date'])) : ''; ?></td>
            <td><?php echo !empty($row['enquiry_no']) ? htmlspecialchars($row['enquiry_no']) : '-'; ?></td>
            <td align="right"><?php echo number_format($row['delivered_qty'], 2); ?></td>
            <td align="right"><?php echo number_format($row['invoiced_qty'], 2); ?></td>
            <td align="right"><?php echo number_format($row['pending_qty'], 2); ?></td>
            <td><?php echo strip_tags($row['invoice_nos']); ?></td>
            <td><?php echo htmlspecialchars($row['invoicing_status']); ?></td>
            <td align="center"><?php echo (int)$row['aging_days']; ?></td>
        </tr>
    <?php 
        endforeach;
    endif;
    ?>
    </tbody>
    <tfoot>
        <tr style="font-weight:bold; background-color:#28a745; color:#ffffff; font-size:13px;">
            <td colspan="7" align="right">Total (<?php echo $total_count; ?> Records):</td>
            <td align="right"><?php echo number_format($grand_delivered, 2); ?></td>
            <td align="right"><?php echo number_format($grand_invoiced, 2); ?></td>
            <td align="right"><?php echo number_format($grand_pending, 2); ?></td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>

