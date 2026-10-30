<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>In Stock Item Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #999; padding: 6px 8px; font-size: 12px; }
        th { background-color: #2e6da4; color: #ffffff; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .total-row { background-color: #f2f2f2; font-weight: bold; }
        .title { font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="title">In Stock Item Report</div>
    <div style="margin-bottom: 8px; font-size: 11px;">Generated on: <?php echo date('d-m-Y H:i:s'); ?></div>
    
    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Inward No</th>
                <th>Inward Date</th>
                <th>Vendor</th>
                <th>Tender Info</th>
                <th>Item Code</th>
                <th>Item Description</th>
                <th>UOM</th>
                <th>Inward Qty</th>
                <th>DC Qty</th>
                <th>In Stock Qty</th>
                <th>Unit Rate</th>
                <th>Stock Value</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $tot_inw = 0;
            $tot_dc = 0;
            $tot_stk = 0;
            $tot_val = 0;
            if (!empty($record_list)): 
                foreach ($record_list as $i => $r): 
                    $tot_inw += (float)$r['inward_qty'];
                    $tot_dc += (float)$r['dc_qty'];
                    $tot_stk += (float)$r['instock_item_qty'];
                    $tot_val += (float)$r['stock_value'];
                    $inw_date = (!empty($r['inward_date']) && $r['inward_date'] != '0000-00-00') ? date('d-m-Y', strtotime($r['inward_date'])) : '-';
            ?>
                <tr>
                    <td class="text-center"><?php echo $i + 1; ?></td>
                    <td><?php echo htmlspecialchars($r['inward_no'] ?: '-'); ?></td>
                    <td class="text-center"><?php echo $inw_date; ?></td>
                    <td><?php echo htmlspecialchars($r['vendor_name'] ?: '-'); ?></td>
                    <td><?php echo htmlspecialchars(strip_tags($r['tender_details'] ?: '-')); ?></td>
                    <td><?php echo htmlspecialchars($r['item_code'] ?: '-'); ?></td>
                    <td><?php echo htmlspecialchars($r['item_desc'] ?: '-'); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($r['uom'] ?: '-'); ?></td>
                    <td class="text-right"><?php echo number_format((float)$r['inward_qty'], 2, '.', ''); ?></td>
                    <td class="text-right"><?php echo number_format((float)$r['dc_qty'], 2, '.', ''); ?></td>
                    <td class="text-right"><?php echo number_format((float)$r['instock_item_qty'], 2, '.', ''); ?></td>
                    <td class="text-right"><?php echo number_format((float)$r['rate'], 2, '.', ''); ?></td>
                    <td class="text-right"><?php echo number_format((float)$r['stock_value'], 2, '.', ''); ?></td>
                </tr>
            <?php 
                endforeach; 
            else: 
            ?>
                <tr>
                    <td colspan="13" class="text-center">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="8" class="text-right">Total:</td>
                <td class="text-right"><?php echo number_format($tot_inw, 2, '.', ''); ?></td>
                <td class="text-right"><?php echo number_format($tot_dc, 2, '.', ''); ?></td>
                <td class="text-right"><?php echo number_format($tot_stk, 2, '.', ''); ?></td>
                <td class="text-right">-</td>
                <td class="text-right"><?php echo number_format($tot_val, 2, '.', ''); ?></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>

