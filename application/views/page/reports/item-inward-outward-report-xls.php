<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #1e293b;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
    </style>
</head>
<body>

    <table style="width: 100%; border-collapse: collapse; font-family: Arial, Helvetica, sans-serif;">
        <!-- Report Header -->
        <tr>
            <td colspan="9" style="font-size: 16pt; font-weight: bold; color: #1e293b; padding-bottom: 4px;">
                ITEM WISE INWARD &amp; OUTWARD REPORT
            </td>
        </tr>
        <tr>
            <td colspan="9" style="font-size: 11pt; color: #475569; padding-bottom: 12px; border-bottom: 2px solid #cbd5e1;">
                <strong>Selected Month:</strong> <?php echo htmlspecialchars($month_label); ?> (<?php echo date('d-M-Y', strtotime($start_date)); ?> to <?php echo date('d-M-Y', strtotime($end_date)); ?>) &nbsp;|&nbsp;
                <strong>Generated On:</strong> <?php echo date('d-M-Y H:i:s'); ?>
                <?php if (!empty($srch_movement) && $srch_movement !== 'all'): ?>
                    &nbsp;|&nbsp; <strong>Filter:</strong> <?php echo ($srch_movement === 'inward_only' ? 'Inward Only' : 'Outward Only'); ?>
                <?php endif; ?>
            </td>
        </tr>

        <!-- Spacer -->
        <tr style="height: 12px;"><td colspan="9" style="border: none;"></td></tr>

        <!-- Summary KPIs Row -->
        <tr style="background-color: #f8fafc;">
            <th colspan="2" style="border: 1px solid #cbd5e1; padding: 8px; text-align: center; background-color: #f1f5f9;">
                Opening Stock: <br><strong style="font-size: 12pt;"><?php echo number_format($kpi['total_opening_qty'], 2); ?></strong>
            </th>
            <th colspan="2" style="border: 1px solid #cbd5e1; padding: 8px; text-align: center; background-color: #d1fae5; color: #065f46;">
                Month Inward: <br><strong style="font-size: 12pt;"><?php echo number_format($kpi['total_inward_qty'], 2); ?></strong>
            </th>
            <th colspan="2" style="border: 1px solid #cbd5e1; padding: 8px; text-align: center; background-color: #fee2e2; color: #991b1b;">
                Month Outward (DC): <br><strong style="font-size: 12pt;"><?php echo number_format($kpi['total_outward_qty'], 2); ?></strong>
            </th>
            <th colspan="2" style="border: 1px solid #cbd5e1; padding: 8px; text-align: center; background-color: #ede9fe; color: #5b21b6;">
                Net Change: <br><strong style="font-size: 12pt;"><?php echo ($kpi['total_net_movement_qty'] > 0 ? '+' : '') . number_format($kpi['total_net_movement_qty'], 2); ?></strong>
            </th>
            <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: center; background-color: #dbeafe; color: #1e40af;">
                Closing Stock: <br><strong style="font-size: 12pt;"><?php echo number_format($kpi['total_closing_qty'], 2); ?></strong>
            </th>
        </tr>

        <!-- Spacer -->
        <tr style="height: 12px;"><td colspan="9" style="border: none;"></td></tr>

        <!-- Table Header -->
        <thead>
            <tr style="background-color: #1e293b; color: #ffffff;">
                <th style="padding: 8px; border: 1px solid #475569; width: 40px; text-align: center; background-color: #1e293b; color: #ffffff;">#</th>
                <th style="padding: 8px; border: 1px solid #475569; text-align: left; background-color: #1e293b; color: #ffffff;">Item Code</th>
                <th style="padding: 8px; border: 1px solid #475569; text-align: left; background-color: #1e293b; color: #ffffff;">Item Description</th>
                <th style="padding: 8px; border: 1px solid #475569; text-align: center; width: 60px; background-color: #1e293b; color: #ffffff;">UOM</th>
                <th style="padding: 8px; border: 1px solid #475569; text-align: right; background-color: #334155; color: #ffffff;">Opening Stock</th>
                <th style="padding: 8px; border: 1px solid #475569; text-align: right; background-color: #065f46; color: #ffffff;">Month Inward</th>
                <th style="padding: 8px; border: 1px solid #475569; text-align: right; background-color: #991b1b; color: #ffffff;">Month Outward (DC)</th>
                <th style="padding: 8px; border: 1px solid #475569; text-align: right; background-color: #5b21b6; color: #ffffff;">Net Movement</th>
                <th style="padding: 8px; border: 1px solid #475569; text-align: right; background-color: #1e40af; color: #ffffff;">Closing Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($records)): ?>
                <?php foreach ($records as $idx => $r): 
                    $bg_color = ($idx % 2 === 0) ? '#ffffff' : '#f8fafc';
                ?>
                    <tr style="background-color: <?php echo $bg_color; ?>;">
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: center;"><?php echo ($idx + 1); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; font-weight: bold;"><?php echo htmlspecialchars($r['item_code']); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1;"><?php echo htmlspecialchars($r['item_desc']); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: center;"><?php echo htmlspecialchars($r['uom']); ?></td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #f1f5f9;">
                            <?php echo number_format($r['opening_qty'], 2); ?>
                        </td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #ecfdf5; font-weight: bold; color: #065f46;">
                            <?php echo number_format($r['inward_qty'], 2); ?>
                        </td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #fef2f2; font-weight: bold; color: #991b1b;">
                            <?php echo number_format($r['outward_qty'], 2); ?>
                        </td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: right; font-weight: bold;">
                            <?php 
                            $net = $r['net_movement_qty'];
                            echo ($net > 0 ? '+' : '') . number_format($net, 2); 
                            ?>
                        </td>
                        <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: right; background-color: #eff6ff; font-weight: bold; color: #1e40af;">
                            <?php echo number_format($r['closing_qty'], 2); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="padding: 20px; border: 1px solid #cbd5e1; text-align: center; color: #64748b;">
                        No item movement records found for <?php echo htmlspecialchars($month_label); ?>.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #0f172a; color: #ffffff; font-weight: bold;">
                <td colspan="4" style="padding: 8px; border: 1px solid #334155; text-align: right; color: #ffffff;">TOTALS:</td>
                <td style="padding: 8px; border: 1px solid #334155; text-align: right; color: #ffffff;"><?php echo number_format($kpi['total_opening_qty'], 2); ?></td>
                <td style="padding: 8px; border: 1px solid #334155; text-align: right; color: #34d399;"><?php echo number_format($kpi['total_inward_qty'], 2); ?></td>
                <td style="padding: 8px; border: 1px solid #334155; text-align: right; color: #f87171;"><?php echo number_format($kpi['total_outward_qty'], 2); ?></td>
                <td style="padding: 8px; border: 1px solid #334155; text-align: right; color: #c084fc;">
                    <?php echo ($kpi['total_net_movement_qty'] > 0 ? '+' : '') . number_format($kpi['total_net_movement_qty'], 2); ?>
                </td>
                <td style="padding: 8px; border: 1px solid #334155; text-align: right; color: #60a5fa;"><?php echo number_format($kpi['total_closing_qty'], 2); ?></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
