<?php
// Function to convert amount to words
if (!function_exists('voucher_amount_to_words')) {
    function voucher_amount_to_words($amount, $currency_unit = 'rupees', $sub_unit = 'paise', $decimals = 3) {
        $ones = array(
            0 => '', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five',
            6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten',
            11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
            16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen'
        );
        $tens = array(
            2 => 'twenty', 3 => 'thirty', 4 => 'forty', 5 => 'fifty',
            6 => 'sixty', 7 => 'seventy', 8 => 'eighty', 9 => 'ninety'
        );

        $num_to_words_group = function($n) use ($ones, $tens, &$num_to_words_group) {
            $n = (int)$n;
            if ($n == 0) return '';
            $str = '';
            if ($n >= 10000000) {
                $str .= $num_to_words_group(floor($n / 10000000)) . ' crore ';
                $n %= 10000000;
            }
            if ($n >= 100000) {
                $str .= $num_to_words_group(floor($n / 100000)) . ' lakh ';
                $n %= 100000;
            }
            if ($n >= 1000) {
                $str .= $num_to_words_group(floor($n / 1000)) . ' thousand ';
                $n %= 1000;
            }
            if ($n >= 100) {
                $str .= $ones[floor($n / 100)] . ' hundred ';
                $n %= 100;
            }
            if ($n >= 20) {
                $str .= $tens[floor($n / 10)] . ' ';
                $n %= 10;
            }
            if ($n > 0) {
                $str .= $ones[$n] . ' ';
            }
            return trim($str);
        };

        $amount = (float)$amount;
        $whole = floor($amount);
        $multiplier = pow(10, $decimals);
        $fraction = round(($amount - $whole) * $multiplier);

        $whole_str = $whole > 0 ? $num_to_words_group($whole) : 'zero';
        $words = $whole_str . ' ' . $currency_unit;

        if ($fraction > 0) {
            $words .= ' and ' . $num_to_words_group($fraction) . ' ' . $sub_unit;
        }

        return $words . ' only';
    }
}

// Extract view variables depending on whether called from vendor payment, cash outward, customer receipt, or cash inward
if (!empty($payment)) {
    $voucher_title = 'Voucher';
    $meta_no_label = 'Voucher No';
    $vno_num = (int)($payment['payment_no'] ?? 0);
    $voucher_no = $vno_num > 0 ? str_pad($vno_num, 4, '0', STR_PAD_LEFT) : '0000';
    $voucher_date = $payment['payment_date'] ?? date('d-m-Y');
    $company_name = !empty($payment['company_name']) ? $payment['company_name'] : 'AL HILLO TRADING CO W.L.L';
    $company_address = !empty($payment['company_address']) ? $payment['company_address'] : '';
    $party_label = 'Vendor Name';
    $party_name = $payment['vendor_name'] ?? '-';
    $mode_label = 'Payment Mode';
    
    // Payment mode text
    $payment_mode = $payment['payment_mode'] ?? 'Cash';
    if ($payment_mode === 'Cash') {
        $payment_mode_text = 'Cash' . (!empty($payment['category_name']) ? ' (' . $payment['category_name'] . ')' : '');
    } elseif ($payment_mode === 'Bank') {
        $bank_extra = array();
        if (!empty($payment['bank_name'])) $bank_extra[] = $payment['bank_name'];
        if (!empty($payment['cheque_no'])) $bank_extra[] = 'Cheque: ' . $payment['cheque_no'];
        $payment_mode_text = 'Bank' . (!empty($bank_extra) ? ' (' . implode(' - ', $bank_extra) . ')' : '');
    } else {
        $payment_mode_text = $payment_mode;
    }

    $amount = (float)($payment['amount'] ?? 0);
    $curr_code = !empty($payment['currency_code']) ? strtoupper($payment['currency_code']) : '';
    if ($curr_code === 'USD') {
        $curr_symbol = '$';
        $curr_unit = 'dollars';
        $sub_unit = 'cents';
    } elseif ($curr_code === 'EUR') {
        $curr_symbol = '€';
        $curr_unit = 'euros';
        $sub_unit = 'cents';
    } elseif ($curr_code === 'BHD' && !empty($payment['currency_symbol']) && $payment['currency_symbol'] !== '.د.ب' && $payment['currency_symbol'] !== '.\u062f.\u0628') {
        $curr_symbol = $payment['currency_symbol'];
        $curr_unit = 'bahraini dinars';
        $sub_unit = 'fils';
    } else {
        $curr_symbol = 'BD';
        $curr_unit = 'bahraini dinars';
        $sub_unit = 'fils';
    }
    $amount_in_words = voucher_amount_to_words($amount, $curr_unit, $sub_unit, 3);

    $bill_label = 'Bill No';
    $bill_nos = $payment['bill_nos'] ?? '-';
    $back_url = function_exists('site_url') ? site_url('vendor-payment-list') : 'vendor-payment-list';
    $receiver_sig_label = 'Receiver Signature';

} elseif (!empty($outward)) {
    $voucher_title = 'Voucher';
    $meta_no_label = 'Voucher No';
    $vno_num = (int)($outward['vno'] ?? 0);
    $voucher_no = $vno_num > 0 ? str_pad($vno_num, 4, '0', STR_PAD_LEFT) : '0000';
    $voucher_date = $outward['outward_date'] ?? date('d-m-Y');
    $company_name = !empty($outward['company_name']) ? $outward['company_name'] : 'AL HILLO TRADING CO W.L.L';
    $company_address = !empty($outward['company_address']) ? $outward['company_address'] : '';
    $party_label = 'Paid To';
    $mode_label = 'Payment Mode';
    
    $party_parts = array();
    if (!empty($outward['account_head_name'])) $party_parts[] = $outward['account_head_name'];
    if (!empty($outward['sub_account_head_name'])) $party_parts[] = $outward['sub_account_head_name'];
    if (!empty($outward['out_for'])) $party_parts[] = $outward['out_for'];
    $party_name = !empty($party_parts) ? implode(' - ', $party_parts) : (!empty($outward['cash_received_by']) ? $outward['cash_received_by'] : '-');

    $ac_type = $outward['ac_type'] ?? 'Cash';
    if ($ac_type === 'Cash') {
        $payment_mode_text = 'Cash' . (!empty($outward['category_name']) ? ' (' . $outward['category_name'] . ')' : '');
    } elseif ($ac_type === 'Bank') {
        $payment_mode_text = 'Bank' . (!empty($outward['bank_name']) ? ' (' . $outward['bank_name'] . ')' : '');
    } else {
        $payment_mode_text = $ac_type;
    }

    $amount = (float)($outward['amount'] ?? 0);
    $curr_symbol = 'BD';
    $curr_unit = 'bahraini dinars';
    $sub_unit = 'fils';
    $amount_in_words = voucher_amount_to_words($amount, $curr_unit, $sub_unit, 3);

    $bill_label = 'Remarks';
    $bill_nos = !empty($outward['remarks']) ? nl2br(htmlspecialchars($outward['remarks'])) : '-';
    $back_url = function_exists('site_url') ? site_url('outward-list') : 'outward-list';
    $receiver_sig_label = 'Receiver Signature';

} elseif (!empty($receipt)) {
    $voucher_title = 'Receipt';
    $meta_no_label = 'Receipt No';
    $rno_num = (int)($receipt['receipt_no'] ?? 0);
    $voucher_no = $rno_num > 0 ? str_pad($rno_num, 4, '0', STR_PAD_LEFT) : '0000';
    $voucher_date = $receipt['receipt_date'] ?? date('d-m-Y');
    $company_name = !empty($receipt['company_name']) ? $receipt['company_name'] : 'AL HILLO TRADING CO W.L.L';
    $company_address = !empty($receipt['company_address']) ? $receipt['company_address'] : '';
    $party_label = 'Received From';
    $party_name = $receipt['customer_name'] ?? '-';
    $mode_label = 'Receipt Mode';
    
    // Receipt mode text
    $receipt_mode = $receipt['receipt_mode'] ?? 'Cash';
    if ($receipt_mode === 'Cash') {
        $payment_mode_text = 'Cash' . (!empty($receipt['category_name']) ? ' (' . $receipt['category_name'] . ')' : '');
    } elseif ($receipt_mode === 'Bank') {
        $bank_extra = array();
        if (!empty($receipt['bank_name'])) $bank_extra[] = $receipt['bank_name'];
        if (!empty($receipt['cheque_no'])) $bank_extra[] = 'Cheque: ' . $receipt['cheque_no'];
        $payment_mode_text = 'Bank' . (!empty($bank_extra) ? ' (' . implode(' - ', $bank_extra) . ')' : '');
    } else {
        $payment_mode_text = $receipt_mode;
    }

    $amount = (float)($receipt['amount'] ?? 0);
    $curr_code = !empty($receipt['currency_code']) ? strtoupper($receipt['currency_code']) : '';
    if ($curr_code === 'USD') {
        $curr_symbol = '$';
        $curr_unit = 'dollars';
        $sub_unit = 'cents';
    } elseif ($curr_code === 'EUR') {
        $curr_symbol = '€';
        $curr_unit = 'euros';
        $sub_unit = 'cents';
    } elseif ($curr_code === 'BHD' && !empty($receipt['currency_symbol']) && $receipt['currency_symbol'] !== '.د.ب' && $receipt['currency_symbol'] !== '.\u062f.\u0628') {
        $curr_symbol = $receipt['currency_symbol'];
        $curr_unit = 'bahraini dinars';
        $sub_unit = 'fils';
    } else {
        $curr_symbol = 'BD';
        $curr_unit = 'bahraini dinars';
        $sub_unit = 'fils';
    }
    $amount_in_words = voucher_amount_to_words($amount, $curr_unit, $sub_unit, 3);

    $bill_label = 'Invoice No';
    $bill_nos = !empty($receipt['invoice_nos']) && $receipt['invoice_nos'] !== '-' ? $receipt['invoice_nos'] : '-';
    $back_url = function_exists('site_url') ? site_url('customer-invoice-receipt') : 'customer-invoice-receipt';
    $receiver_sig_label = 'Authorized / Received By Signature';

} elseif (!empty($inward)) {
    $voucher_title = 'Receipt';
    $meta_no_label = 'Receipt No';
    $rno_num = (int)($inward['vno'] ?? 0);
    $voucher_no = $rno_num > 0 ? str_pad($rno_num, 4, '0', STR_PAD_LEFT) : '0000';
    $voucher_date = !empty($inward['inward_date']) ? date('d-m-Y', strtotime($inward['inward_date'])) : date('d-m-Y');
    $company_name = !empty($inward['company_name']) ? $inward['company_name'] : 'AL HILLO TRADING CO W.L.L';
    $company_address = !empty($inward['company_address']) ? $inward['company_address'] : '';
    $party_label = 'Received From';
    $mode_label = 'Receipt Mode';
    
    $party_parts = array();
    if (!empty($inward['account_head_name'])) $party_parts[] = $inward['account_head_name'];
    if (!empty($inward['sub_account_head_name'])) $party_parts[] = $inward['sub_account_head_name'];
    $party_name = !empty($party_parts) ? implode(' - ', $party_parts) : '-';

    $ac_type = $inward['ac_type'] ?? 'Cash';
    if ($ac_type === 'Cash') {
        $payment_mode_text = 'Cash' . (!empty($inward['category_name']) ? ' (' . $inward['category_name'] . ')' : '');
    } elseif ($ac_type === 'Bank') {
        $bank_extra = array();
        if (!empty($inward['bank_name'])) $bank_extra[] = $inward['bank_name'];
        $payment_mode_text = 'Bank' . (!empty($bank_extra) ? ' (' . implode(' - ', $bank_extra) . ')' : '');
    } else {
        $payment_mode_text = $ac_type;
    }

    $amount = (float)($inward['amount'] ?? 0);
    $curr_symbol = 'BD';
    $curr_unit = 'bahraini dinars';
    $sub_unit = 'fils';
    $amount_in_words = voucher_amount_to_words($amount, $curr_unit, $sub_unit, 3);

    $bill_label = 'Remarks';
    $bill_nos = !empty($inward['remarks']) ? nl2br(htmlspecialchars($inward['remarks'])) : '-';
    $back_url = function_exists('site_url') ? site_url('inward-list') : 'inward-list';
    $receiver_sig_label = 'Authorized / Received By Signature';

} else {
    $voucher_title = 'Voucher';
    $meta_no_label = 'Voucher No';
    $voucher_no = '0000';
    $voucher_date = date('d-m-Y');
    $company_name = 'AL HILLO TRADING CO W.L.L';
    $company_address = '';
    $party_label = 'Party Name';
    $party_name = '-';
    $mode_label = 'Mode';
    $payment_mode_text = 'Cash';
    $amount = 0.000;
    $curr_symbol = 'BD';
    $amount_in_words = 'zero bahraini dinars only';
    $bill_label = 'Reference';
    $bill_nos = '-';
    $back_url = function_exists('site_url') ? site_url('vendor-payment-list') : 'vendor-payment-list';
    $receiver_sig_label = 'Receiver Signature';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($voucher_title); ?> - <?php echo htmlspecialchars($voucher_no); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>asset/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>asset/bower_components/font-awesome/css/font-awesome.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f0f2f5;
            color: #000;
            padding: 30px 15px;
        }
        .action-bar {
            max-width: 950px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .voucher-box {
            max-width: 950px;
            margin: 0 auto;
            background: #fff;
            border: 2px solid #000;
            box-shadow: 0 4px 18px rgba(0,0,0,0.12);
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #000;
        }
        .header-table td {
            vertical-align: middle;
        }
        .company-col {
            padding: 16px 22px;
            width: 40%;
            line-height: 1.35;
        }
        .company-name {
            font-size: 19px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000;
        }
        .company-address {
            font-size: 12.5px;
            font-weight: 500;
            color: #222;
            line-height: 1.35;
            margin-top: 5px;
        }
        .title-col {
            font-size: 32px;
            font-weight: 900;
            text-align: center;
            width: 32%;
            letter-spacing: 1px;
            padding: 20px 10px;
        }
        .meta-col {
            width: 28%;
            border-left: 2px solid #000;
            padding: 0;
        }
        .meta-item {
            padding: 10px 18px;
            font-size: 15px;
            font-weight: 700;
            white-space: nowrap;
        }
        .meta-divider {
            border-top: 1px solid #000;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
        }
        .content-table tr {
            border-bottom: 1px solid #000;
        }
        .content-table tr:last-child {
            border-bottom: 2px solid #000;
        }
        .content-table td {
            padding: 14px 20px;
            font-size: 16px;
            vertical-align: top;
            line-height: 1.5;
        }
        .col-label {
            width: 190px;
            font-weight: 800;
            color: #111;
        }
        .col-colon {
            width: 35px;
            font-weight: 800;
            text-align: center;
        }
        .col-value {
            font-weight: 500;
            color: #000;
        }
        .amount-main {
            font-size: 17px;
            font-weight: 700;
        }
        .amount-sub {
            display: block;
            margin-top: 4px;
            font-size: 14.5px;
            font-weight: 400;
            color: #222;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            height: 140px;
        }
        .signatures-table td {
            width: 50%;
            height: 140px;
            vertical-align: bottom;
            text-align: center;
            padding-bottom: 20px;
            font-size: 15.5px;
            font-weight: 700;
        }
        .sig-border {
            border-right: 2px solid #000;
        }
        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
            }
            .action-bar {
                display: none !important;
            }
            .voucher-box {
                border: 2px solid #000 !important;
                box-shadow: none !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            @page {
                size: A4 portrait;
                margin: 1.5cm 1cm;
            }
        }
    </style>
</head>
<body>

    <div class="action-bar no-print">
        <a href="<?php echo $back_url; ?>" onclick="goBack(event);" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
        <button type="button" onclick="window.print()" class="btn btn-primary"><i class="fa fa-print"></i> Print <?php echo htmlspecialchars($voucher_title); ?></button>
    </div>

    <div class="voucher-box">
        <!-- Top Header -->
        <table class="header-table">
            <tr>
                <td class="company-col">
                    <div class="company-name"><?php echo htmlspecialchars($company_name); ?></div>
                    <?php if (!empty($company_address)): ?>
                        <div class="company-address"><?php echo nl2br(htmlspecialchars(trim($company_address))); ?></div>
                    <?php endif; ?>
                </td>
                <td class="title-col">
                    <?php echo htmlspecialchars($voucher_title); ?>
                </td>
                <td class="meta-col">
                    <div class="meta-item">
                        <?php echo htmlspecialchars($meta_no_label); ?> &nbsp;:&nbsp; <?php echo htmlspecialchars($voucher_no); ?>
                    </div>
                    <div class="meta-divider"></div>
                    <div class="meta-item">
                        Date &nbsp;:&nbsp; <?php echo htmlspecialchars($voucher_date); ?>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Main Details Table -->
        <table class="content-table">
            <tr>
                <td class="col-label"><?php echo htmlspecialchars($party_label); ?></td>
                <td class="col-colon">:</td>
                <td class="col-value"><?php echo htmlspecialchars($party_name); ?></td>
            </tr>
            <tr>
                <td class="col-label"><?php echo htmlspecialchars($mode_label ?? 'Payment Mode'); ?></td>
                <td class="col-colon">:</td>
                <td class="col-value"><?php echo htmlspecialchars($payment_mode_text); ?></td>
            </tr>
            <tr>
                <td class="col-label">Amount</td>
                <td class="col-colon">:</td>
                <td class="col-value">
                    <span class="amount-main"><?php echo htmlspecialchars($curr_symbol) . ' ' . number_format($amount, 3); ?></span>
                    <span class="amount-sub">(the sum of <?php echo htmlspecialchars($amount_in_words); ?>)</span>
                </td>
            </tr>
            <tr>
                <td class="col-label"><?php echo htmlspecialchars($bill_label); ?></td>
                <td class="col-colon">:</td>
                <td class="col-value"><?php echo !empty($bill_nos) && $bill_nos !== '-' ? $bill_nos : '-'; ?></td>
            </tr>
        </table>

        <!-- Bottom Signatures -->
        <table class="signatures-table">
            <tr>
                <td class="sig-border">Accounts Manager Signature</td>
                <td><?php echo htmlspecialchars($receiver_sig_label ?? 'Receiver Signature'); ?></td>
            </tr>
        </table>
    </div>

    <script>
        function goBack(e) {
            // If opened in a new tab / popup window via target="_blank"
            if (window.opener) {
                if (e && e.preventDefault) e.preventDefault();
                window.close();
                setTimeout(function() {
                    window.location.href = "<?php echo $back_url; ?>";
                }, 150);
                return;
            }
            // If navigated in same window and history referrer is available within this site
            if (document.referrer && document.referrer !== window.location.href && document.referrer.indexOf(window.location.host) !== -1) {
                if (e && e.preventDefault) e.preventDefault();
                window.location.href = document.referrer;
                return;
            }
            // Otherwise, natural href navigation to $back_url happens automatically
        }
    </script>
</body>
</html>
