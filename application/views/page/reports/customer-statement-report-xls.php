<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$col_count = empty($customer_id) ? 7 : 6;
$card1_span = 2;
$card2_span = empty($customer_id) ? 2 : 1;
$card3_span = 2;
$card4_span = 1;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #334155;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
    </style>
</head>
<body>

    <table style="width: 100%; border-collapse: collapse; font-family: Arial, Helvetica, sans-serif;">
        <!-- Title Header -->
        <tr>
            <td colspan="<?php echo $col_count; ?>" style="font-size: 16pt; font-weight: bold; color: #1e293b; padding-bottom: 5px;">
                CUSTOMER STATEMENT REPORT
            </td>
        </tr>
        <tr>
            <td colspan="<?php echo $col_count; ?>" style="font-size: 11pt; color: #475569; padding-bottom: 15px; border-bottom: 2px solid #cbd5e1;">
                <strong>Customer:</strong> <?php echo htmlspecialchars($selected_customer_name); ?> &nbsp;|&nbsp;
                <strong>Period:</strong> <?php echo !empty($from_date) ? date('d-M-Y', strtotime($from_date)) : 'Start'; ?> to <?php echo !empty($to_date) ? date('d-M-Y', strtotime($to_date)) : 'End'; ?> &nbsp;|&nbsp;
                <strong>Exported At:</strong> <?php echo date('d-m-Y H:i:s'); ?>
            </td>
        </tr>

        <!-- Spacer row -->
        <tr style="height: 15px;">
            <td colspan="<?php echo $col_count; ?>" style="border: none;"></td>
        </tr>

        <!-- Statistics Cards - Row 1 (Labels) -->
        <?php
        $total_debit_period = 0.000;
        $total_credit_period = 0.000;
        foreach ($record_list as $row) {
            $total_debit_period += $row['debit_amt'];
            $total_credit_period += $row['credit_amt'];
        }
        $closing_balance = $opening_balance + $total_debit_period - $total_credit_period;
        ?>
        <tr>
            <!-- Opening Balance Label -->
            <td colspan="<?php echo $card1_span; ?>" style="padding: 10px 10px 2px 10px; background-color: #fdfaf2; border-top: 1px solid #f39c12; border-left: 5px solid #f39c12; border-right: 1px solid #f39c12; vertical-align: bottom;">
                <span style="font-size: 9pt; font-weight: bold; color: #777d8c; text-transform: uppercase;">Opening Balance</span>
            </td>
            
            <!-- Invoiced / Debit Label -->
            <td colspan="<?php echo $card2_span; ?>" style="padding: 10px 10px 2px 10px; background-color: #f4faf6; border-top: 1px solid #00a65a; border-left: 5px solid #00a65a; border-right: 1px solid #00a65a; vertical-align: bottom;">
                <span style="font-size: 9pt; font-weight: bold; color: #777d8c; text-transform: uppercase;">Invoiced / Debit (Period)</span>
            </td>
            
            <!-- Received / Credit Label -->
            <td colspan="<?php echo $card3_span; ?>" style="padding: 10px 10px 2px 10px; background-color: #fdf5f4; border-top: 1px solid #dd4b39; border-left: 5px solid #dd4b39; border-right: 1px solid #dd4b39; vertical-align: bottom;">
                <span style="font-size: 9pt; font-weight: bold; color: #777d8c; text-transform: uppercase;">Received / Credit (Period)</span>
            </td>
            
            <!-- Closing Balance Label -->
            <td colspan="<?php echo $card4_span; ?>" style="padding: 10px 10px 2px 10px; background-color: #f7f6fb; border-top: 1px solid #605ca8; border-left: 5px solid #605ca8; border-right: 1px solid #605ca8; vertical-align: bottom;">
                <span style="font-size: 9pt; font-weight: bold; color: #777d8c; text-transform: uppercase;">Closing Balance</span>
            </td>
        </tr>

        <!-- Statistics Cards - Row 2 (Values) -->
        <tr>
            <!-- Opening Balance Value -->
            <td colspan="<?php echo $card1_span; ?>" style="padding: 2px 10px 10px 10px; background-color: #fdfaf2; border-bottom: 1px solid #f39c12; border-left: 5px solid #f39c12; border-right: 1px solid #f39c12; vertical-align: top; mso-number-format:'#,##0.000';">
                <span style="font-size: 16pt; font-weight: bold; color: #2c3e50;"><?php echo number_format($opening_balance, 3); ?></span>
            </td>
            
            <!-- Invoiced / Debit Value -->
            <td colspan="<?php echo $card2_span; ?>" style="padding: 2px 10px 10px 10px; background-color: #f4faf6; border-bottom: 1px solid #00a65a; border-left: 5px solid #00a65a; border-right: 1px solid #00a65a; vertical-align: top; mso-number-format:'#,##0.000';">
                <span style="font-size: 16pt; font-weight: bold; color: #2c3e50;"><?php echo number_format($total_debit_period, 3); ?></span>
            </td>
            
            <!-- Received / Credit Value -->
            <td colspan="<?php echo $card3_span; ?>" style="padding: 2px 10px 10px 10px; background-color: #fdf5f4; border-bottom: 1px solid #dd4b39; border-left: 5px solid #dd4b39; border-right: 1px solid #dd4b39; vertical-align: top; mso-number-format:'#,##0.000';">
                <span style="font-size: 16pt; font-weight: bold; color: #2c3e50;"><?php echo number_format($total_credit_period, 3); ?></span>
            </td>
            
            <!-- Closing Balance Value -->
            <td colspan="<?php echo $card4_span; ?>" style="padding: 2px 10px 10px 10px; background-color: #f7f6fb; border-bottom: 1px solid #605ca8; border-left: 5px solid #605ca8; border-right: 1px solid #605ca8; vertical-align: top; mso-number-format:'#,##0.000';">
                <span style="font-size: 16pt; font-weight: bold; color: #605ca8;"><?php echo number_format($closing_balance, 3); ?></span>
            </td>
        </tr>

        <!-- Spacer row -->
        <tr style="height: 20px;">
            <td colspan="<?php echo $col_count; ?>" style="border: none;"></td>
        </tr>

        <!-- Ledger Header Row -->
        <tr style="background-color: #222d32; color: #ffffff; font-weight: bold;">
            <td style="padding: 10px; border: 1px solid #222d32; text-align: center; font-size: 11pt; color: #ffffff; width: 12%;">Date</td>
            <?php if (empty($customer_id)) { ?>
                <td style="padding: 10px; border: 1px solid #222d32; text-align: left; font-size: 11pt; color: #ffffff; width: 20%;">Customer</td>
            <?php } ?>
            <td style="padding: 10px; border: 1px solid #222d32; text-align: left; font-size: 11pt; color: #ffffff; width: 15%;">Voucher No</td>
            <td style="padding: 10px; border: 1px solid #222d32; text-align: left; font-size: 11pt; color: #ffffff;">Description</td>
            <td style="padding: 10px; border: 1px solid #222d32; text-align: right; font-size: 11pt; color: #ffffff; width: 15%;">Debit(+)</td>
            <td style="padding: 10px; border: 1px solid #222d32; text-align: right; font-size: 11pt; color: #ffffff; width: 15%;">Credit(-)</td>
            <td style="padding: 10px; border: 1px solid #222d32; text-align: right; font-size: 11pt; color: #ffffff; width: 18%;">Balance</td>
        </tr>

        <!-- Opening Balance Row -->
        <tr style="background-color: #fdfaf2; font-weight: bold; font-size: 11pt;">
            <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: center;">
                <?php echo !empty($from_date) ? date('d-m-Y', strtotime($from_date)) : 'Opening'; ?>
            </td>
            <?php if (empty($customer_id)) { ?>
                <td style="padding: 8px; border: 1px solid #cbd5e1;">—</td>
            <?php } ?>
            <td style="padding: 8px; border: 1px solid #cbd5e1;">—</td>
            <td style="padding: 8px; border: 1px solid #cbd5e1;">Opening Balance</td>
            <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; mso-number-format:'#,##0.000';">0.000</td>
            <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; mso-number-format:'#,##0.000';">0.000</td>
            <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; color: #2c3e50; mso-number-format:'#,##0.000';"><?php echo number_format($opening_balance, 3); ?></td>
        </tr>

        <!-- Chronological Transactions Loop -->
        <?php
        $running_balance = $opening_balance;
        if (!empty($record_list)) {
            $row_idx = 0;
            foreach ($record_list as $row) {
                $running_balance += $row['debit_amt'];
                $running_balance -= $row['credit_amt'];
                $bg_color = ($row_idx % 2 === 0) ? '#ffffff' : '#f8fafc';
                $row_idx++;
                ?>
                <tr style="background-color: <?php echo $bg_color; ?>; font-size: 11pt;">
                    <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: center;">
                        <?php echo date('d-m-Y', strtotime($row['tr_date'])); ?>
                    </td>
                    <?php if (empty($customer_id)) { ?>
                        <td style="padding: 8px; border: 1px solid #cbd5e1;">
                            <?php echo htmlspecialchars($row['customer_name'] ?? ''); ?>
                        </td>
                    <?php } ?>
                    <td style="padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; color: #34495e;">
                        <?php echo htmlspecialchars($row['voucher_no']); ?>
                    </td>
                    <td style="padding: 8px; border: 1px solid #cbd5e1;">
                        <span style="font-weight: bold; color: <?php echo ($row['type'] == 'invoice') ? '#27ae60' : '#c0392b'; ?>;">
                            <?php echo htmlspecialchars($row['description']); ?>
                        </span>
                    </td>
                    <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; color: #27ae60; mso-number-format:'#,##0.000';">
                        <?php echo ($row['debit_amt'] > 0) ? number_format($row['debit_amt'], 3) : '0.000'; ?>
                    </td>
                    <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; color: #c0392b; mso-number-format:'#,##0.000';">
                        <?php echo ($row['credit_amt'] > 0) ? number_format($row['credit_amt'], 3) : '0.000'; ?>
                    </td>
                    <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; font-weight: bold; color: #2c3e50; mso-number-format:'#,##0.000';">
                        <?php echo number_format($running_balance, 3); ?>
                    </td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr style="font-size: 11pt;">
                <td colspan="<?php echo empty($customer_id) ? 7 : 6; ?>" style="padding: 20px; border: 1px solid #cbd5e1; text-align: center; color: #64748b; font-style: italic;">
                    No transactions recorded for the selected period.
                </td>
            </tr>
            <?php
        }
        ?>

        <!-- Summary Footer Row -->
        <tr style="background-color: #f1f5f9; font-weight: bold; font-size: 11pt; border-top: 2px solid #cbd5e1;">
            <td colspan="<?php echo empty($customer_id) ? 4 : 3; ?>" style="padding: 10px; border: 1px solid #cbd5e1; text-align: right;">Total Period Movement:</td>
            <td style="padding: 10px; border: 1px solid #cbd5e1; text-align: right; color: #27ae60; mso-number-format:'#,##0.000';"><?php echo number_format($total_debit_period, 3); ?></td>
            <td style="padding: 10px; border: 1px solid #cbd5e1; text-align: right; color: #c0392b; mso-number-format:'#,##0.000';"><?php echo number_format($total_credit_period, 3); ?></td>
            <td style="padding: 10px; border: 1px solid #cbd5e1; text-align: right; color: #605ca8; mso-number-format:'#,##0.000';"><?php echo number_format($closing_balance, 3); ?></td>
        </tr>
    </table>

</body>
</html>
