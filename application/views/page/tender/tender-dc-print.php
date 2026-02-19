<!DOCTYPE html>
<?php
// echo "<pre>";
// print_r($dc_list_info);
// echo "</pre>";
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Delivery Challan - <?php echo htmlspecialchars($record['dc_no'] ?? ''); ?></title>
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
            margin-bottom: 10px;
        }

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
            background: #ffffff;
            color: #000000;
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
            background: #ffffff;
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

        .item-description {
            font-size: 8.5pt;
            line-height: 1.4;
        }

        .item-code {
            font-weight: 600;
            margin-bottom: 3px;
        }

        .bank-details-section {
            background: #ffffff;
            padding: 8px;
            border: 1px solid #000;
            font-size: 8.5pt;
            line-height: 1.5;
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
            background: #ffffff;
            color: #000000;
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
            margin-top: 60px;
        }

        .signature-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .received-by {
            width: 25%;
        }

        .signature-center {
            width: 25%;
            text-align: center;
        }

        .signature-box {
            width: 40%;
            text-align: right;
        }

        .signature-company {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin-left: auto;
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
        }

        .btn-success {
            background: #28a745;
            color: #fff;
        }

        .btn-success:hover {
            background: #218838;
        }

    
        @page {
            size: A4;
            margin: 5mm;
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
                display: none !important;
            }

            /* Repeat table header on every page */
            thead {
                display: table-header-group !important;
            }

            /* Prevent row from breaking inside */
            tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                orphans: 2;
                widows: 2;
            }

            /* Extra protection for cells with long content */
            td,
            th {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            /* Protect description block */
            .item-description {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            /* Keep totals, amount in words, payment terms, signature together */
            .amount-in-words,
            .payment-terms,
            .signature-section,
            tr:last-child {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                break-before: auto;
            }

            /* Force better color fidelity */
            .items-table thead {
                background: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            tr[style*="background: #000"],
            .summary-row:last-child,
            tr.grand-total-row {
                background: #ffffff !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .currency-badge span {
                background: #ffffff !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
            }
        }

        .label-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <?php
    // Function to convert number to words (BHD style with Fils)
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
        $integer_part = floor($amount);
        $multiplier = pow(10, $decimal_point);
        $decimal_part = round(($amount - $integer_part) * $multiplier);

        $words = $integer_part > 0 ? convertNumberToWords($integer_part, $ones, $tens, $hundreds) : 'Zero';

        if ($decimal_part > 0) {
            $words .= ' & Fils ' . $decimal_part . '/' . $multiplier;
        }

        return $words . ' Only.';
    }
    ?>

    <div class="page">

        <!-- Header Image -->
        <?php if (!empty($record['ltr_header_img'])): ?>
            <img src="<?php echo base_url('') . $record['ltr_header_img']; ?>" alt="Company Header" class="header-img ">
        <?php endif; ?>

        <!-- Invoice Title & Number -->
        <div class="invoice-header">
            <div class="invoice-title">Delivery Challan</div>
            <div class="invoice-number">No: <?php echo htmlspecialchars($record['dc_no'] ?? 'N/A'); ?></div>

        </div>

        <div class="reference-section">
            <div class="reference-line">
                <span class="label-bold">Your P.O. No:</span>
                <?php echo htmlspecialchars($record['po_no'] ?? 'N/A'); ?>,
                <span class="label-bold">Dated:</span>
                <?php echo date('d-m-Y', strtotime($record['po_date'] ?? 'N/A')); ?>
                <span style="float: right;">
                    <span class="label-bold">Date:</span>
                    <?php echo date('d-m-Y', strtotime($record['dc_date'] ?? 'N/A')); ?>
                </span>
            </div>
        </div>

        <!-- Customer -->
        <div class="customer-section">
            <div class="customer-label">To,</div>
            <div class="customer-address">
                <strong><?php echo htmlspecialchars($record['customer_name'] ?? ''); ?></strong><br>
                <?php echo nl2br(htmlspecialchars($record['customer_address'] ?? '')); ?><br>
                <?php if (!empty($record['customer_country'])): ?>
                    Country: <?php echo htmlspecialchars($record['customer_country']); ?><br>
                <?php endif; ?>
                <?php if (!empty($record['vat_account_no'])): ?>
                    VAT Account. No: <?php echo htmlspecialchars($record['vat_account_no']); ?>
                <?php endif; ?>
            </div>
        </div>


        <!-- <div class="currency-badge">
            <span>Currency: <?php echo htmlspecialchars($currency_code); ?></span>
        </div> -->

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:6%;">Item<br>No.</th>
                    <th style="width:74%; text-align:left;">Description</th>
                    <th style="width:10%;">Unit</th>
                    <th style="width:10%;">Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_qty_ordered = 0; // ✅ Initialize before loop
                ?>
                <?php if (!empty($item_list)): ?>
                    <?php foreach ($item_list as $i => $item):

                        $qty = $item['qty'] ?? 0;
                        $total_qty_ordered += $qty; // ✅ Add each qty
                
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $i + 1; ?></td>
                            <td class="text-left">
                                <div class="item-description">
                                    <?php if (!empty($item['item_code'])): ?>
                                        <div class="item-code"><?php echo htmlspecialchars($item['item_code']); ?></div>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($item['item_desc'] ?? ''); ?>
                                </div>
                            </td>
                            <td class="text-center"><?php echo htmlspecialchars($item['uom'] ?? '-'); ?></td>
                            <td class="text-center">
                                <?php echo number_format($item['qty'] ?? 0, 0); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center" style="padding:30px; color:#999;">No items found</td>
                    </tr>
                <?php endif; ?>
                <tr style="background:#ffff; color:#000;">
                    <td colspan="3" class="text-right"><strong>TOTAL QTY</strong></td>
                    <td colspan="2" class="text-center">
                        <strong><?php echo number_format($total_qty_ordered, 0); ?></strong>
                    </td>
                </tr>
            </tbody>
        </table>



        <!-- Notes -->
        <?php if (!empty($record['remarks'])): ?>
            <div
                style="margin:15px 0; padding:10px; background:#f9f9f9;  page-break-inside: avoid; break-inside: avoid;">
                <div style="font-weight:bold; margin-bottom:5px;">Notes:</div>
                <?php echo nl2br($record['remarks']); ?>
            </div>
        <?php endif; ?>

        <!-- Terms & Conditions -->
        <?php if (!empty($record['terms'])): ?>
            <div
                style="margin:15px 0; padding:10px; background:#f9f9f9; page-break-inside: avoid; break-inside: avoid;">
                <div style="font-weight:bold; margin-bottom:5px;">Terms & Conditions:</div>
                <?php echo nl2br($record['terms']); ?>
            </div>
        <?php endif; ?>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-row">

                <div class="received-by">
                    <strong>Received By:</strong>
                </div>

                <div class="signature-center">
                    <strong>Signature:</strong>
                </div>

                <div class="signature-box">
                    <div class="signature-company">
                        For
                        <?php echo htmlspecialchars($record['our_company'] ?? $record['company_name'] ?? 'Our Company'); ?>
                    </div>

                    <!-- If you have signature image -->
                    <?php if (!empty($record['signature_image'])): ?>
                        <div class="signature-image">
                            <img src="<?php echo base_url('uploads/signatures/' . $record['signature_image']); ?>"
                                style="height:60px;">
                        </div>
                    <?php endif; ?>

                    <div class="signature-line"></div>
                </div>

            </div>
        </div>


    </div>

    <!-- Buttons (screen only) -->
    <div class="button-container">
        <button type="button" class="btn btn-primary"
            onclick="window.location.href='<?= site_url('tender-dc-list') ?>'">
            ← Back To List
        </button>
        <button type="button" class="btn btn-success" onclick="window.print()">
            🖨️ Print
        </button>
    </div>

</body>

</html>