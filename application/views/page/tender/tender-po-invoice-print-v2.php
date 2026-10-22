<!DOCTYPE html>
<?php
// echo "<pre>";
// print_r($addt_chrg_list);
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

        /* ── SCREEN: outer page wrapper ── */
        .page {
            width: 210mm;
            /* min-height: 297mm; */
            min-height: auto;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /*
         * THE KEY TRICK:
         * One full-page outer <table>.
         * Its <thead> is rendered by every browser at the
         * top of EVERY printed page — no JS, no fixed positioning needed.
         */
        table.page-table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Header cell — contains the company letterhead image */
        table.page-table>thead>tr>td {
            /* padding: 6mm 10mm 3mm 10mm; */
            padding: 0;
            text-align: center;
        }

        table.page-table>thead>tr>td img {
            width: 100%;
            max-height: 200px;
            display: block;
            /* max-height: 120px;
            object-fit: contain; */
        }

        /* Body cell — contains all invoice content */
        table.page-table>tbody>tr>td {
            padding: 2mm 10mm 10mm 10mm;
            vertical-align: top;
        }

        /* ── INVOICE HEADER ── */
        .invoice-header {
            position: relative;
            text-align: center;
            padding: 10px 0;
            margin-bottom: 15px;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* ── REFERENCE SECTION ── */
        .reference-section {
            margin-bottom: 12px;
            font-size: 9.5pt;
            line-height: 1.6;
            margin-left: 30px;
        }

        .reference-line {
            margin-bottom: 5px;
        }

        /* ── CUSTOMER SECTION ── */
        .customer-section {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #ffffff;
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

        /* ── CURRENCY BADGE ── */
        .currency-badge {
            text-align: right;
            margin: 10px 0;
        }

        .currency-badge span {
            display: inline-block;
            background: #ffffff;
            color: #000000;
            padding: 6px 15px;
            font-weight: bold;
            font-size: 10pt;
        }

        /* ── ITEMS TABLE ── */
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9pt;
        }

        .items-table thead {
            background: #ffffff;
            border: 1px solid #b4b4b4;
        }

        .items-table thead th {
            padding: 8px 5px;
            border: 1px solid #b4b4b4;
            font-weight: bold;
            text-align: center;
            font-size: 9pt;
        }

        .items-table tbody td {
            padding: 6px 5px;
            border: 1px solid #b4b4b4;
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

        /* ── BANK DETAILS ── */
        .bank-details-section {
            background: #ffffff;
            padding: 8px;
            border: 1px solid #b4b4b4;
            font-size: 8.5pt;
            line-height: 1.5;
        }

        .bank-details-title {
            font-weight: bold;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        /* ── SUMMARY ROWS ── */
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 10px;
            border: 1px solid #b4b4b4;
            border-bottom: none;
            font-weight: bold;
            font-size: 10pt;
        }

        .summary-row:last-child {
            border-bottom: 1px solid #000;
            background: #ffffff;
            color: #000000;
            font-size: 11pt;
        }

        /* ── AMOUNT IN WORDS ── */
        .amount-in-words {
            margin: 10px 0;
            padding: 10px;
            background: #ffffff;
            border: 1px solid #b4b4b4;
            font-weight: bold;
            font-size: 10pt;
            line-height: 1.5;
        }

        .payment-terms {
            margin: 15px 0;
            font-size: 9.5pt;
            font-weight: bold;
        }

        /* ── SIGNATURE ── */
        .signature-section {
            margin-top: 30px;
            text-align: right;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            min-width: 250px;
        }

        .signature-company {
            font-size: 10pt;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin: 5px 0;
        }

        /* ── UTILITY ── */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .label-bold {
            font-weight: bold;
        }

        .inline-label {
            display: inline;
        }

        /* ── BUTTONS ── */
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
        }

        .btn-success {
            background: #28a745;
            color: #fff;
        }

        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        /* ══════════════════════════════════════════
           PRINT STYLES
           ══════════════════════════════════════════ */
        @page {
            size: A4;
            /* margin-top: 5mm;
             margin-bottom: 15mm;
             margin-left: 5mm;
             margin-right: 5mm; */
            margin: 10mm;

            @bottom-right {
                content: "Page " counter(page);
                font-size: 11px;
                font-family: Arial, Helvetica, sans-serif;
                padding-right: 15mm;
            }
        }

        * @media print {
            body {
                background: #fff;
                margin: 0;
                padding: 0;
            }

            .page {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }


            table.page-table>thead {
                display: table-header-group;
            }

            table.page-table>tbody {
                display: table-row-group;
            }

            .button-container {
                display: none;
            }

            .items-table thead {
                background: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .summary-row:last-child {
                background: #ffffff !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .currency-badge span {
                background: #ffffff !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @media print {

            html,
            body {
                margin: 0;
                padding: 0;
            }

            .page {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            table.page-table thead {
                display: table-header-group;
            }

            table.page-table tbody {
                display: table-row-group;
            }

            .button-container {
                display: none;
            }
        }
    </style>
</head>

<body>

    <?php
    // Function to convert number to words in Bahraini Dinar format
    function convertAmountToWords($amount, $currency = 'BHD', $decimal_point = 3)
    {
        $ones = [
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
        ];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        $hundreds = [
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
        ];

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

        $amount = floatval($amount);
        $amount = round($amount, $decimal_point);
        $integer_part = floor($amount);
        $multiplier = pow(10, $decimal_point);
        $decimal_part = round(($amount - $integer_part) * $multiplier);

        if ($decimal_part >= $multiplier) {
            $integer_part += 1;
            $decimal_part -= $multiplier;
        }

        $words = ($integer_part > 0)
            ? convertNumberToWords($integer_part, $ones, $tens, $hundreds)
            : 'Zero';

        if ($decimal_part > 0) {
            $formatted_decimal = sprintf("%0{$decimal_point}d", $decimal_part);
            if ($currency == 'BHD') {
                $words .= ' & Fils ' . $formatted_decimal . '/' . $multiplier;
            } elseif ($currency == 'EUR') {
                $words .= ' & Cents ' . $formatted_decimal . '/' . $multiplier;
            } else {
                $words .= ' & ' . $formatted_decimal . '/' . $multiplier;
            }
        }

        return $words;
    }
    ?>

    <div class="page">

        <!--
            ONE outer table whose <thead> the browser repeats
            at the top of EVERY printed page automatically.
            Works in Chrome, Edge, Firefox, Safari — no JS needed.
        -->
        <table class="page-table">

            <!-- ══ REPEATING HEADER IMAGE — shows on every printed page ══ -->
            <thead>
                <tr>
                    <td>
                        <?php if (!empty($record['ltr_header_img'])): ?>
                            <img src="<?php echo base_url('') . $record['ltr_header_img']; ?>" alt="Company Header">
                        <?php endif; ?>
                    </td>
                </tr>
            </thead>

            <!-- ══ ALL INVOICE CONTENT ══ -->
            <tbody>
                <tr>
                    <td>

                        <!-- Invoice Title & Number -->
                        <div class="invoice-header">
                            <?php if ($record['vat_payer_sales_grp'] == 'Exports (Line 5 of the VAT Return)'): ?>
                                <div class="invoice-title">EXPORT INVOICE</div>
                                <div style="float:right; text-align:right;">
                                    Date :
                                    <?php echo htmlspecialchars(date('d/m/Y', strtotime($record['invoice_date']))); ?><br>
                                    Invoice No: <?php echo htmlspecialchars($record['invoice_no'] ?? 'N/A'); ?>
                                </div>
                            <?php else: ?>
                                <div class="invoice-title">TAX INVOICE</div>
                                <div style="float:right; text-align:right;">
                                    Date :
                                    <?php echo htmlspecialchars(date('d/m/Y', strtotime($record['invoice_date']))); ?><br>
                                    Invoice No: <?php echo htmlspecialchars($record['invoice_no'] ?? 'N/A'); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Reference Section -->
                        <div class="reference-section">
                            <div class="reference-line">
                                <span class="label-bold">Your P.O. No:</span>
                                <?php echo htmlspecialchars($record['po_no'] ?? 'N/A'); ?>,
                                <span class="label-bold">Dated:</span>
                                <?php echo date('d-m-Y', strtotime($record['po_date'] ?? 'N/A')); ?>
                            </div>

                            <?php if (!empty($dc_list_info)): ?>
                                <?php foreach ($dc_list_info as $dc_info): ?>
                                    <div class="reference-line">
                                        <span class="label-bold">Our D. N. No:</span>
                                        <?php echo htmlspecialchars($dc_info['dc_no'] ?? 'N/A'); ?>,
                                        <span class="label-bold">Dated:</span>
                                        <?php echo date('d-m-Y', strtotime($dc_info['dc_date'] ?? 'N/A')); ?>.
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center">N/A</div>
                            <?php endif; ?>
                        </div>

                        <!-- Customer Information -->
                        <div class="customer-section">
                            <div class="customer-label">To,</div>
                            <div class="customer-address">
                                <strong><?php echo htmlspecialchars($record['customer_name'] ?? 'N/A'); ?></strong><br>
                                <?php echo nl2br(htmlspecialchars($record['address'] ?? 'N/A')); ?><br>
                                <?php if (!empty($record['customer_country'])): ?>
                                    <?php echo htmlspecialchars($record['customer_country']); ?><br>
                                <?php endif; ?>
                                <?php if (!empty($record['vat_account_no'])): ?>
                                    <strong>VAT Account. No:</strong>
                                    <?php echo htmlspecialchars($record['vat_account_no']); ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Currency Badge -->
                        <?php
                        $decimal_point = isset($record['decimal_point']) ? intval($record['decimal_point']) : 3;
                        $currency_code = $record['currency_code'] ?? 'BHD';
                        ?>
                        <div class="currency-badge">
                            <span>Currency: <?php echo htmlspecialchars($currency_code); ?></span>
                        </div>

                        <!-- Items Table -->
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th style="width:6%;  text-align:center;">Serial<br>No.</th>
                                    <th style="width:40%; text-align:left;">Description</th>
                                    <th style="width:5%;  text-align:center;">Qty</th>
                                    <th style="width:5%;  text-align:center;">Unit</th>
                                    <th style="width:10%; text-align:right;">Unit<br>Rate</th>
                                    <th style="width:10%; text-align:right;">Net Price</th>
                                    <th style="width:7%;  text-align:center;">VAT<br>%</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $total_net_amount = 0;
                                $total_vat_amount = 0;
                                $tot_vat = [];

                                if (!empty($item_list)):
                                    foreach ($item_list as $i => $item):
                                        $qty = floatval($item['qty'] ?? 0);
                                        $rate = floatval($item['rate'] ?? 0);
                                        $net = isset($item['Net_Amount']) ? floatval($item['Net_Amount']) : ($qty * $rate);
                                        $vat_percentage = floatval($item['gst'] ?? 0);
                                        $vat_amount = ($net * $vat_percentage) / 100;
                                        $total_net_amount += $net;
                                        $total_vat_amount += $vat_amount;
                                        $rate_key = number_format($vat_percentage, 2, '.', '');
                                        if (!isset($tot_vat[$rate_key])) {
                                            $tot_vat[$rate_key] = 0;
                                        }
                                        $tot_vat[$rate_key] += $vat_amount;
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo ($i + 1); ?></td>
                                            <td class="text-left">
                                                <?php if (!empty($item['item_code'])): ?>
                                                    <div><strong><?= htmlspecialchars($item['item_code']) ?></strong></div>
                                                <?php endif; ?>

                                                <?= nl2br(htmlspecialchars($item['item_desc'] ?? '')) ?>
                                                <?php if (!empty($item['serial_no']) && $item['serial_no'] != ($i + 1)): ?>
                                                    <div style="font-size: 8pt; color: #555; margin-top: 2px;">(PO Item Ref: #<?= htmlspecialchars($item['serial_no']) ?>)</div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?= number_format($qty, 0) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($item['uom'] ?? '-') ?></td>
                                            <td class="text-right"><?= number_format($rate, $decimal_point) ?></td>
                                            <td class="text-right"><?= number_format($net, $decimal_point) ?></td>
                                            <td class="text-center"><?= number_format($vat_percentage, 2) ?></td>
                                        </tr>
                                        <?php
                                    endforeach;
                                else:
                                    ?>
                                    <tr>
                                        <td colspan="7" class="text-center" style="padding:30px;color:#999;">
                                            No items found
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php
                                $addt_charges_net = 0;
                                $addt_charges_vat = 0;
                                if (!empty($addt_chrg_list)) {
                                    foreach ($addt_chrg_list as $addt_chrg) {
                                        $chrg_amt = floatval($addt_chrg['addt_charges_amt'] ?? 0);
                                        $chrg_vat_rate = floatval($addt_chrg['addt_charges_vat'] ?? 0);
                                        $chrg_vat_amt = floatval($addt_chrg['addt_charges_vat_amt'] ?? 0);
                                        $addt_charges_net += $chrg_amt;
                                        $addt_charges_vat += $chrg_vat_amt;
                                        $rate_key = number_format($chrg_vat_rate, 2, '.', '');
                                        if (!isset($tot_vat[$rate_key])) {
                                            $tot_vat[$rate_key] = 0;
                                        }
                                        $tot_vat[$rate_key] += $chrg_vat_amt;
                                    }
                                }

                                $subtotal_before_discount = $total_net_amount + $addt_charges_net;
                                $vat_before_discount = $total_vat_amount + $addt_charges_vat;

                                $has_discount = (isset($record['is_discount']) && $record['is_discount'] == 1 && floatval($record['discount'] ?? 0) > 0);
                                $discount_amount = $has_discount ? floatval($record['discount']) : 0;
                                $subtotal_after_discount = max(0, $subtotal_before_discount - $discount_amount);

                                if ($has_discount && $subtotal_before_discount > 0) {
                                    $discount_ratio = $discount_amount / $subtotal_before_discount;
                                    $final_vat_amount = round($vat_before_discount * (1 - $discount_ratio), $decimal_point);
                                } else {
                                    $final_vat_amount = round($vat_before_discount, $decimal_point);
                                }

                                $final_grand_total = $subtotal_after_discount + $final_vat_amount;

                                $vat_label = 'VAT Amount';
                                if (count($tot_vat) === 1) {
                                    $single_rate = floatval(key($tot_vat));
                                    if ($single_rate > 0) {
                                        $vat_label = 'VAT ' . ($single_rate + 0) . '%';
                                    }
                                }
                                ?>

                                <!-- Bank Details + Summary Row -->
                                <?php if (!empty($bank_details) && (!empty($bank_details['bank_name']) || !empty($bank_details['account_name']))): ?>
                                    <tr>
                                        <td colspan="4" class="text-left">
                                            <div class="bank-details-section">
                                                <div class="bank-details-title">OUR BANK ACCOUNT DETAILS</div>
                                                <?php if (!empty($bank_details['account_name'])): ?>
                                                    <div><strong>ACCOUNT NAME:</strong>
                                                        <?php echo htmlspecialchars($bank_details['account_name']); ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($bank_details['bank_name'])): ?>
                                                    <div><strong>BANK NAME:</strong>
                                                        <?php echo htmlspecialchars($bank_details['bank_name']); ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($bank_details['account_number'])): ?>
                                                    <div><strong>ACCOUNT NO.:</strong>
                                                        <?php echo htmlspecialchars($bank_details['account_number']); ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($bank_details['iban_no'])): ?>
                                                    <div><strong>IBAN NO:</strong>
                                                        <?php echo htmlspecialchars($bank_details['iban_no']); ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($bank_details['swift_code'])): ?>
                                                    <div><strong>SWIFT/BIC:</strong>
                                                        <?php echo htmlspecialchars($bank_details['swift_code']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td colspan="3" class="text-right" style="vertical-align:middle;">
                                            <div class="summary-section">
                                                <?php if (!empty($addt_chrg_list)): ?>
                                                    <div class="summary-row" style="border-bottom:1px solid #000;">
                                                        <span>TOTAL EXCL. VAT (ITEMS)</span>
                                                        <span><?php echo number_format($total_net_amount, $decimal_point); ?></span>
                                                    </div>
                                                    <?php foreach ($addt_chrg_list as $addt_chrg): ?>
                                                        <div class="summary-row" style="border-bottom:1px solid #000;">
                                                            <span><?php echo htmlspecialchars($addt_chrg['addt_charges_type_name']); ?></span>
                                                            <span><?php echo number_format($addt_chrg['addt_charges_amt'], $decimal_point); ?></span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>

                                                <?php if ($has_discount): ?>
                                                    <div class="summary-row" style="border-bottom:1px solid #000;">
                                                        <span>TOTAL</span>
                                                        <span><?php echo number_format($subtotal_before_discount, $decimal_point); ?></span>
                                                    </div>
                                                    <div class="summary-row" style="border-bottom:1px solid #000;">
                                                        <span>Discount</span>
                                                        <span><?php echo number_format($discount_amount, $decimal_point); ?></span>
                                                    </div>
                                                    <div class="summary-row" style="border-bottom:1px solid #000;">
                                                        <span>TOTAL EXCL. VAT</span>
                                                        <span><?php echo number_format($subtotal_after_discount, $decimal_point); ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="summary-row" style="border-bottom:1px solid #000;">
                                                        <span>TOTAL EXCL. VAT</span>
                                                        <span><?php echo number_format($subtotal_before_discount, $decimal_point); ?></span>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="summary-row" style="border-bottom:1px solid #000;">
                                                    <span><?php echo htmlspecialchars($vat_label); ?></span>
                                                    <span><?php echo number_format($final_vat_amount, $decimal_point); ?></span>
                                                </div>
                                                <div class="summary-row">
                                                    <span>TOTAL <?php echo htmlspecialchars($currency_code); ?></span>
                                                    <span><?php echo number_format($final_grand_total, $decimal_point); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php if (!empty($addt_chrg_list)): ?>
                                        <tr>
                                            <td colspan="5" class="text-right" style="padding:10px; border:1px solid #000;">
                                                <strong>TOTAL EXCL. VAT (ITEMS)</strong>
                                            </td>
                                            <td colspan="2" class="text-right" style="padding:10px; border:1px solid #000;">
                                                <strong><?php echo number_format($total_net_amount, $decimal_point); ?></strong>
                                            </td>
                                        </tr>
                                        <?php foreach ($addt_chrg_list as $addt_chrg): ?>
                                            <tr class="items-table">
                                                <td colspan="5" class="text-right" style="padding:10px; border:1px solid #000;">
                                                    <strong><?php echo htmlspecialchars($addt_chrg['addt_charges_type_name']); ?></strong>
                                                </td>
                                                <td colspan="2" class="text-right" style="padding:10px; border:1px solid #000;">
                                                    <strong><?php echo number_format($addt_chrg['addt_charges_amt'], $decimal_point); ?></strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php if ($has_discount): ?>
                                        <tr class="items-table">
                                            <td colspan="5" class="text-right" style="padding:10px; border:1px solid #000;">
                                                <strong>TOTAL</strong>
                                            </td>
                                            <td colspan="2" class="text-right" style="padding:10px; border:1px solid #000;">
                                                <strong><?php echo number_format($subtotal_before_discount, $decimal_point); ?></strong>
                                            </td>
                                        </tr>
                                        <tr class="items-table">
                                            <td colspan="5" class="text-right" style="padding:10px; border:1px solid #000;">
                                                <strong>Discount</strong>
                                            </td>
                                            <td colspan="2" class="text-right" style="padding:10px; border:1px solid #000;">
                                                <strong><?php echo number_format($discount_amount, $decimal_point); ?></strong>
                                            </td>
                                        </tr>
                                        <tr class="items-table">
                                            <td colspan="5" class="text-right" style="padding:10px; border:1px solid #000;">
                                                <strong>TOTAL EXCL. VAT</strong>
                                            </td>
                                            <td colspan="2" class="text-right" style="padding:10px; border:1px solid #000;">
                                                <strong><?php echo number_format($subtotal_after_discount, $decimal_point); ?></strong>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-right" style="padding:10px; border:1px solid #000;">
                                                <strong>TOTAL EXCL. VAT</strong>
                                            </td>
                                            <td colspan="2" class="text-right" style="padding:10px; border:1px solid #000;">
                                                <strong><?php echo number_format($subtotal_before_discount, $decimal_point); ?></strong>
                                            </td>
                                        </tr>
                                    <?php endif; ?>

                                    <tr>
                                        <td colspan="5" class="text-right" style="padding:10px; border:1px solid #000;">
                                            <strong><?php echo htmlspecialchars($vat_label); ?></strong>
                                        </td>
                                        <td colspan="2" class="text-right" style="padding:10px; border:1px solid #000;">
                                            <strong><?php echo number_format($final_vat_amount, $decimal_point); ?></strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right"
                                            style="padding:12px; border:1px solid #000; font-size:11pt;">
                                            <strong>TOTAL <?php echo htmlspecialchars($currency_code); ?></strong>
                                        </td>
                                        <td colspan="2" class="text-right"
                                            style="padding:12px; border:1px solid #000; font-size:11pt;">
                                            <strong><?php echo number_format($final_grand_total, $decimal_point); ?></strong>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- Total in Words -->
                        <?php $amount_in_words = convertAmountToWords($final_grand_total, $currency_code, $decimal_point); ?>
                        <div class="amount-in-words">
                            <strong>Total <?php echo htmlspecialchars($currency_code); ?>:</strong>
                            <?php echo $amount_in_words; ?> Only.
                        </div>

                        <!-- Payment Terms -->
                        <div class="payment-terms">
                            Payment: Within 30 days from the above date.
                        </div>

                        <!-- Notes / Remarks -->
                        <?php if (!empty($record['remarks'])): ?>
                            <div style="margin:0; padding:10px; background:#ffffff; border-left:3px solid #ffffff;">
                                <div style="font-weight:bold; margin-bottom:5px;">Notes:</div>
                                <div style="font-size:9pt;"><?php echo $record['remarks']; ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Terms & Conditions -->
                        <?php if (!empty($record['terms'])): ?>
                            <div style="margin:0; padding:10px; background:#ffffff; border-left:3px solid #ffffff;">
                                <div style="font-weight:bold; margin-bottom:5px;">Terms &amp; Conditions:</div>
                                <div style="font-size:9pt;"><?php echo $record['terms']; ?></div>
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

                    </td>
                </tr>
            </tbody>

        </table><!-- /.page-table -->

    </div><!-- /.page -->

    <!-- Action Buttons (hidden on print) -->
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