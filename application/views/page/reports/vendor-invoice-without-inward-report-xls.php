<?php
$grand_total = 0;
$paid_total = 0;
$balance_total = 0;
$invoice_no_count = 0;
?>
<table border="1">
    <thead>
        <tr style="background-color:#337ab7; color:white; font-weight:bold;">
            <th>S.No</th>
            <th>PO No</th>
            <th>PO Date</th>
            <th>Invoice No</th>
            <th>Invoice Date</th>
            <th>Vendor Name</th>
            <th>Inward Status</th>
            <th>Last Payment Date</th>
            <th>Invoice Amt</th>
            <th>Paid Amt</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($record_list as $i => $row) {
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
            <td><?php echo $row['inward_status']; ?></td>
            <td><?php echo $row['last_payment_date'] ? date('d/m/Y', strtotime($row['last_payment_date'])) : ''; ?></td>
            <td align="right"><?php echo number_format($row['total_amount'], 3); ?></td>
            <td align="right"><?php echo number_format($row['paid_amount'], 3); ?></td>
            <td align="right"><?php echo number_format($balance, 3); ?></td>
        </tr>
    <?php } ?>
    </tbody>
    <tfoot>
    <tr style="font-weight:bold; background:green; font-size:16px; color:#ffffff;">
        <td colspan="2" align="right"> Total Invoice :</td>
        <td><?= $invoice_no_count; ?></td>
        <td colspan="5" align="right">Grand Total :</td>
        <td align="right"><?= number_format($grand_total, 3); ?></td>
        <td align="right"><?= number_format($paid_total, 3); ?></td>
        <td align="right"><?= number_format($balance_total, 3); ?></td>
    </tr>
    </tfoot>
</table>
