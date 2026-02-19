<!DOCTYPE html>
<?php
// echo "<pre>";
// print_r($dc_list_info);
// echo "</pre>";
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Tax Invoice - <?php echo htmlspecialchars($record['invoice_no'] ?? ''); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
            background: #f5f5f5;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 10mm;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header-img {
            width: 100%; 
        }

        .invoice-header {
            position: relative;
            text-align: center;
            padding: 10px 0;
            /* border-top: 2px solid #000;
            border-bottom: 2px solid #000; */
            margin-bottom: 15px;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .invoice-number {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            font-weight: bold;
        }

        .reference-section {
            margin-bottom: 12px;
            font-size: 9.5pt;
            line-height: 1.6;
            margin-left: 30px;
        }

        .reference-line {
            margin-bottom: 5px;
        }

        .customer-section {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #000;
            min-height: 80px;
        }

        .customer-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .customer-address {
            line-height: 1.5;
            font-size: 9.5pt;
        }

        .currency-badge {
            text-align: right;
            margin: 10px 0;
        }

        .currency-badge span {
            display: inline-block;
            background: #000;
            color: #fff;
            padding: 6px 15px;
            font-weight: bold;
            font-size: 10pt;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9pt;
        }

        .items-table thead {
            background: #d9e2f3;
            border: 2px solid #000;
        }

        .items-table thead th {
            padding: 8px 5px;
            border: 1px solid #000;
            font-weight: bold;
            text-align: center;
            font-size: 9pt;
        }

        .items-table tbody td {
            padding: 6px 5px;
            border: 1px solid #000;
            vertical-align: top;
            font-size: 9pt;
        }

        .items-table tbody tr {
            background: #fff;
        }

        .item-description {
            font-size: 8.5pt;
            line-height: 1.4;
        }

        .item-code {
            font-weight: 600;
            margin-bottom: 3px;
        }

        .bank-details-section {
            background: #f0f0f0;
            padding: 8px;
            border: 1px solid #000;
            font-size: 8.5pt;
            line-height: 1.5;
        }

        .bank-details-title {
            font-weight: bold;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 10px;
            border: 1px solid #000;
            border-bottom: none;
            font-weight: bold;
            font-size: 10pt;
        }

        .summary-row:last-child {
            border-bottom: 1px solid #000;
            background: #000;
            color: #fff;
            font-size: 11pt;
        }

        .amount-in-words {
            margin: 10px 0;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #000;
            font-weight: bold;
            font-size: 10pt;
            line-height: 1.5;
        }

        .payment-terms {
            margin: 15px 0;
            font-size: 9.5pt;
            font-weight: bold;
        }

        .signature-section {
            /* margin-top: 60px; */
            text-align: right;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            min-width: 250px;
        }

        .signature-company {
            font-size: 10pt;
            /* margin-bottom: 50px; */
        }

        .signature-line {
            border-top: 1px solid #000;
            margin: 5px 0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 0 10px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: #0066cc;
            color: #fff;
        }

        .btn-primary:hover {
            background: #0052a3;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .btn-success {
            background: #28a745;
            color: #fff;
        }

        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        @page {
            size: A4;
            margin: 0;
        }

        @media print {
            body {
                background: #fff;
                margin: 0;
                padding: 0;
            }

            .page {
                width: 210mm;
                margin: 0;
                padding: 10mm;
                box-shadow: none;
            }

            .button-container {
                display: none;
            }

            .items-table thead {
                background: #d9e2f3 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .summary-row:last-child {
                background: #000 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .currency-badge span {
                background: #000 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .label-bold {
            font-weight: bold;
        }

        .inline-label {
            display: inline;
        }
    </style>
</head>

<body>
    <?php
    // Function to convert number to words in Bahraini Dinar format
    function convertAmountToWords($amount, $currency = 'BHD', $decimal_point = 3)
    {
        $ones = array(
            '',
            'One',
            'Two',
            'Three',
            'Four',
            'Five',
            'Six',
            'Seven',
            'Eight',
            'Nine',
            'Ten',
            'Eleven',
            'Twelve',
            'Thirteen',
            'Fourteen',
            'Fifteen',
            'Sixteen',
            'Seventeen',
            'Eighteen',
            'Nineteen'
        );

        $tens = array(
            '',
            '',
            'Twenty',
            'Thirty',
            'Forty',
            'Fifty',
            'Sixty',
            'Seventy',
            'Eighty',
            'Ninety'
        );

        $hundreds = array(
            '',
            'One Hundred',
            'Two Hundred',
            'Three Hundred',
            'Four Hundred',
            'Five Hundred',
            'Six Hundred',
            'Seven Hundred',
            'Eight Hundred',
            'Nine Hundred'
        );

        function convertNumberToWords($number, $ones, $tens, $hundreds)
        {
            if ($number == 0)
                return 'Zero';

            $words = '';

            if ($number >= 1000000) {
                $millions = floor($number / 1000000);
                $words .= convertNumberToWords($millions, $ones, $tens, $hundreds) . ' Million ';
                $number %= 1000000;
            }

            if ($number >= 1000) {
                $thousands = floor($number / 1000);
                $words .= convertNumberToWords($thousands, $ones, $tens, $hundreds) . ' Thousand ';
                $number %= 1000;
            }

            if ($number >= 100) {
                $words .= $hundreds[floor($number / 100)] . ' ';
                $number %= 100;
            }

            if ($number >= 20) {
                $words .= $tens[floor($number / 10)] . ' ';
                $number %= 10;
            }

            if ($number > 0) {
                $words .= $ones[$number] . ' ';
            }

            return trim($words);
        }

        // Split amount into integer and decimal parts
        $amount = floatval($amount);
        $integer_part = floor($amount);

        // Calculate decimal part based on decimal points
        $multiplier = pow(10, $decimal_point);
        $decimal_part = round(($amount - $integer_part) * $multiplier);

        // Convert integer part to words
        $words = '';
        if ($integer_part > 0) {
            $words = convertNumberToWords($integer_part, $ones, $tens, $hundreds);
        } else {
            $words = 'Zero';
        }

        // Add "& Fils" for decimal part in BHD format
        if ($decimal_part > 0) {
            $words .= ' & Fils ' . $decimal_part . '/' . $multiplier;
        }

        return $words;
    }
    ?>

    <div class="page">
        <!-- Header Image -->
        <?php if (!empty($record['ltr_header_img'])): ?>
            <img src="<?php echo base_url('') . $record['ltr_header_img']; ?>" alt="Company Header" class="header-img">
        <?php endif; ?>

        <!-- Invoice Title & Number -->
        <div class="invoice-header">
            <?php if ($record['vat_payer_sales_grp'] == 'Exports (Line 5 of the VAT Return)') { ?>
                <div class="invoice-title">Exports INVOICE</div>
                <div class="invoice-number">No:
                    <?php echo htmlspecialchars($record['invoice_no'] ?? 'N/A'); ?>
                </div>
            <?php } else { ?>
                <div class="invoice-title">TAX INVOICE</div>
                <div class="invoice-number">No:
                    <?php echo htmlspecialchars($record['invoice_no'] ?? 'N/A'); ?>
                </div>
            <?php } ?>
        </div> 


        <div class="reference-section">
            <div class="reference-line">
                <span class="label-bold">Your P.O. No:</span>
                <?php echo htmlspecialchars($record['po_no'] ?? 'N/A'); ?>,
                <span class="label-bold">Dated:</span>
                <?php echo date('d-m-Y', strtotime($record['po_date'] ?? 'N/A')); ?>
                <span style="float: right;">
                    <span class="label-bold">Date:</span>
                    <?php echo date('d-m-Y', strtotime($record['invoice_date'] ?? 'N/A')); ?>
                </span>
            </div>

            <?php if (!empty($dc_list_info)) { ?>
                <?php foreach ($dc_list_info as $dc_info) { ?>
                    <div class="reference-line">
                        <span class="label-bold">Our D. N. No:</span>
                        <?php echo htmlspecialchars($dc_info['dc_no'] ?? 'N/A'); ?>,
                        <span class="label-bold">Dated:</span>
                        <?php echo date('d-m-Y', strtotime($dc_info['dc_date'] ?? 'N/A')); ?>.
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="text-center">N/A</div>
            <?php } ?>
        </div>

        <!-- Customer Information -->
        <div class="customer-section">
            <div class="customer-label">To,</div>
            <div class="customer-address">
                <strong><?php echo htmlspecialchars($record['customer_name'] ?? 'N/A'); ?></strong><br>
                <?php echo nl2br(htmlspecialchars($record['address'] ?? 'N/A')); ?><br>
                <?php if (!empty($record['customer_country'])): ?>
                    Country: (<?php echo htmlspecialchars($record['customer_country']); ?>)<br>
                <?php endif; ?>
                <?php if (!empty($record['vat_account_no'])): ?>
                    VAT Account. No: <?php echo htmlspecialchars($record['vat_account_no']); ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Currency Badge -->
        <?php
            $decimal_point = isset($record['decimal_point']) ? intval($record['decimal_point']) : 3;
            $currency_code = $record['currency_code'] ?? 'BHD';

            // Calculate totals
            $subtotal = 0;
            $total_vat = 0;
            if (!empty($item_list)) {
                foreach ($item_list as $item) {
                    $subtotal += floatval($item['Net_amount'] ?? 0);
                    $vat_amount = (floatval($item['Net_amount'] ?? 0) * floatval($item['gst'] ?? 0)) / 100;
                    $total_vat += $vat_amount;
                }
            }
            $grand_total_amount = $subtotal + $total_vat;
        ?>
        <div class="currency-badge">
            <span>Currency: <?php echo htmlspecialchars($currency_code); ?></span>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:6%; text-align:center;">Item<br>No.</th>
                    <th style="width:40%; text-align:left;">Description</th>
                    <th style="width:5%; text-align:center;">Qty</th>
                    <th style="width:5%; text-align:center;">Unit</th>
                    <th style="width:10%; text-align:right;">Unit<br>Rate</th>
                    <th style="width:10%; text-align:right;">Net Price</th>
                    <th style="width:7%; text-align:center;">VAT<br>%</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $total_net_amount = 0;
                $total_vat_amount = 0;
                $grand_total = 0;
                $vat_percentage = 0;

                if (!empty($item_list)):
                    foreach ($item_list as $i => $item):
                        $net_amount = $item['Net_amount'] ?? 0;
                        $vat_percentage = floatval($item['gst'] ?? 0);
                        $vat_amount = ($net_amount * $vat_percentage) / 100;

                        $total_net_amount += $net_amount;
                        $total_vat_amount += $vat_amount;
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $i + 1; ?></td>
                            <td class="text-left">
                                <div class="item-description">
                                    <?php if (!empty($item['item_code'])): ?>
                                        <div class="item-code"><?php echo htmlspecialchars($item['item_code']); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['item_desc'])): ?>
                                        <?php echo htmlspecialchars($item['item_desc']); ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php echo number_format($item['qty'] ?? 0, 0); ?>
                            </td>
                            <td class="text-center">
                                <?php echo htmlspecialchars($item['uom'] ?? '-'); ?>
                            </td>
                            <td class="text-right">
                                <?php echo number_format($item['rate'] ?? 0, $decimal_point); ?>
                            </td>
                            <td class="text-right">
                                <?php echo number_format($net_amount, $decimal_point); ?>
                            </td>
                            <td class="text-center">
                                <?php echo number_format($vat_percentage, 2); ?>
                            </td>
                        </tr>
                        <?php
                    endforeach;
                    $grand_total = $total_net_amount + $total_vat_amount;
                else:
                    ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding:30px; color:#999;">
                            No items found
                        </td>
                    </tr>
                <?php endif; ?>

                <!-- Bank Details Row -->
                <?php if (!empty($bank_details) && !empty($bank_details['account_name'])): ?>
                    <tr>
                        <td colspan="4" class="text-left">
                            <div class="bank-details-section">
                                <div class="bank-details-title">OUR BANK ACCOUNT DETAILS</div>
                                <?php if (!empty($bank_details['account_name'])): ?>
                                    <div><strong>ACCOUNT NAME:</strong>
                                        <?php echo htmlspecialchars($bank_details['account_name']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($bank_details['bank_name'])): ?>
                                    <div><strong>BANK NAME:</strong> <?php echo htmlspecialchars($bank_details['bank_name']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($bank_details['account_number'])): ?>
                                    <div><strong>ACCOUNT NO.:</strong>
                                        <?php echo htmlspecialchars($bank_details['account_number']); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($bank_details['iban_no'])): ?>
                                    <div><strong>IBAN NO:</strong> <?php echo htmlspecialchars($bank_details['iban_no']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($bank_details['swift_code'])): ?>
                                    <div><strong>SWIFT/BIC:</strong>
                                        <?php echo htmlspecialchars($bank_details['swift_code']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td colspan="3" class="text-right" style="vertical-align: top; position: relative;">
                            <div class="summary-section">
                                <div class="summary-row" style="border-bottom: 1px solid #000;">
                                    <span>TOTAL EXCL. VAT</span>
                                    <span><?php echo number_format($total_net_amount, $decimal_point); ?></span>
                                </div>
                                <div class="summary-row" style="border-bottom: 1px solid #000;">
                                    <span>VAT <?php echo number_format($vat_percentage ?? 0, 0); ?>%</span>
                                    <span><?php echo number_format($total_vat_amount, $decimal_point); ?></span>
                                </div>
                                <div class="summary-row">
                                    <span>TOTAL <?php echo htmlspecialchars($currency_code); ?></span>
                                    <span><?php echo number_format($grand_total, $decimal_point); ?></span>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <!-- Alternative layout if no bank details -->
                    <tr>
                        <td colspan="5" class="text-right" style="padding: 10px; border: 1px solid #000;">
                            <strong>TOTAL EXCL. VAT</strong>
                        </td>
                        <td colspan="2" class="text-right" style="padding: 10px; border: 1px solid #000;">
                            <strong><?php echo number_format($total_net_amount, $decimal_point); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-right" style="padding: 10px; border: 1px solid #000;">
                            <strong>VAT <?php echo number_format($vat_percentage ?? 0, 0); ?>%</strong>
                        </td>
                        <td colspan="2" class="text-right" style="padding: 10px; border: 1px solid #000;">
                            <strong><?php echo number_format($total_vat_amount, $decimal_point); ?></strong>
                        </td>
                    </tr>
                    <tr
                        style="background: #000; color: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                        <td colspan="5" class="text-right" style="padding: 12px; border: 1px solid #000; font-size: 11pt;">
                            <strong>TOTAL <?php echo htmlspecialchars($currency_code); ?></strong>
                        </td>
                        <td colspan="2" class="text-right"
                            style="padding: 12px; border: 1px solid #000; font-size: 11pt; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                            <strong><?php echo number_format($grand_total, $decimal_point); ?></strong>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Total in Words - Enhanced Display -->
        <?php
        // Generate amount in words
        $amount_in_words = convertAmountToWords($grand_total, $currency_code, $decimal_point);
        ?>
        <div class="amount-in-words">
            <strong>Total <?php echo htmlspecialchars($currency_code); ?>:</strong>
            <?php echo $amount_in_words; ?> Only.
        </div>

        <!-- Payment Terms -->
        <div class="payment-terms">
            Payment: Within 30 days from the above date.
        </div>

        <!-- Notes/Remarks -->
        <?php if (!empty($record['remarks'])): ?>
            <div style="margin: 15px 0; padding: 10px; background: #f9f9f9; border-left: 3px solid #000;">
                <div style="font-weight: bold; margin-bottom: 5px;">Notes:</div>
                <div style="font-size: 9pt;"><?php echo nl2br(htmlspecialchars($record['remarks'])); ?></div>
            </div>
        <?php endif; ?>

        <!-- Terms & Conditions -->
        <?php if (!empty($record['terms'])): ?>
            <div style="margin: 15px 0; padding: 10px; background: #f9f9f9; border-left: 3px solid #000;">
                <div style="font-weight: bold; margin-bottom: 5px;">Terms & Conditions:</div>
                <div style="font-size: 9pt;"><?php echo nl2br(htmlspecialchars($record['terms'])); ?></div>
            </div>
        <?php endif; ?>

        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-company">
                    For
                    <?php echo htmlspecialchars($record['our_company'] ?? $record['company_name'] ?? 'Our Company'); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="button-container">
        <button type="button" class="btn btn-primary"
            onclick="window.location.href='<?= site_url('tender-invoice-list') ?>'">
            ← Back To List
        </button>
        <button type="button" class="btn btn-success" onclick="window.print()">
            🖨️ Print
        </button>
    </div>

</body>

</html>