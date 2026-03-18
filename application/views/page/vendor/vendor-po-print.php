<!DOCTYPE html>
<?php
// echo "<pre>";
// print_r($record);
// echo "</pre>";
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Purchase Order - <?php echo htmlspecialchars($record['po_no'] ?? ''); ?></title>
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

        /* ── Screen wrapper ── */
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        /* ── The magic repeating-header table ── */
        .print-header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        /* thead repeats on every page when printing */
        .print-header-table > thead {
            display: table-header-group;
        }

        .print-header-table > thead > tr > td {
            padding: 8mm 10mm 4mm 10mm;
        }

        .print-header-table > thead > tr > td img {
            width: 100%;
            display: block;
        }

        /* tbody holds all body content */
        .print-header-table > tbody > tr > td {
            padding: 0 10mm 10mm 10mm;
            vertical-align: top;
        }

        /* ── Invoice Header ── */
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
           border: 1px solid #b4b4b4;
            font-size: 8.5pt;
            line-height: 1.5;
        }

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

        .signature-section {
            margin-top: 40px;
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
            width: 250px;
        }

        .text-center { text-align: center; }
        .text-right  { text-align: right;  }
        .text-left   { text-align: left;   }

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
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .btn-primary          { background: #0066cc; color: #fff; }
        .btn-primary:hover    { background: #0052a3; }
        .btn-success          { background: #28a745; color: #fff; }
        .btn-success:hover    { background: #218838; }

        .label-bold { font-weight: bold; }

        /* ──────────────────────────────────────────────── */
        /*               PRINT-SPECIFIC FIXES               */
        /* ──────────────────────────────────────────────── */

        @page {
            size: A4;
            margin: 0mm;
        }

        @media print {

            body {
                background: #fff;
                margin: 0;
                padding: 0;
            }

            /* Remove screen shadow / margin from .page */
            .page {
                width: 210mm;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            /* Hide print/back buttons */
            .button-container {
                display: none !important;
            }

            /* ── The repeating-header table ── */
            .print-header-table {
                display: table !important;
                width: 100%;
                border-collapse: collapse;
            }

            /* THIS is what makes the header repeat on every printed page */
            .print-header-table > thead {
                display: table-header-group !important;
            }

            .print-header-table > thead > tr > td {
                padding: 8mm 10mm 4mm 10mm;
            }

            .print-header-table > thead > tr > td img {
                width: 100%;
                display: block;
            }

            /* Body content cell */
            .print-header-table > tbody > tr > td {
                padding: 0 10mm 10mm 10mm;
                vertical-align: top;
            }

            /* ── Items table tweaks ── */

            /* Repeat items-table column headers on every page */
            .items-table thead {
                display: table-header-group !important;
                background: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Prevent row splitting across pages */
            tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                orphans: 2;
                widows: 2;
            }

            td, th {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .item-description {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            /* Keep totals / signature block together */
            .amount-in-words,
            .payment-terms,
            .signature-section,
            tr:last-child {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                break-before: auto;
            }

            /* Colour fidelity for total rows */
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
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>

<body>

<?php
// ── Amount-to-words helper ──────────────────────────────────────────────────
function convertAmountToWords($amount, $currency = 'BHD', $decimal_point = 3)
{
    $ones = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
        'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
        'Seventeen', 'Eighteen', 'Nineteen'
    ];
    $tens     = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    $hundreds = [
        '', 'One Hundred', 'Two Hundred', 'Three Hundred', 'Four Hundred',
        'Five Hundred', 'Six Hundred', 'Seven Hundred', 'Eight Hundred', 'Nine Hundred'
    ];

    function convertNumberToWords($number, $ones, $tens, $hundreds)
    {
        if ($number == 0) return 'Zero';
        $words = '';
        if ($number >= 1000000) {
            $millions = floor($number / 1000000);
            $words   .= convertNumberToWords($millions, $ones, $tens, $hundreds) . ' Million ';
            $number  %= 1000000;
        }
        if ($number >= 1000) {
            $thousands = floor($number / 1000);
            $words    .= convertNumberToWords($thousands, $ones, $tens, $hundreds) . ' Thousand ';
            $number   %= 1000;
        }
        if ($number >= 100) {
            $words  .= $hundreds[floor($number / 100)] . ' ';
            $number %= 100;
        }
        if ($number >= 20) {
            $words  .= $tens[floor($number / 10)] . ' ';
            $number %= 10;
        }
        if ($number > 0) {
            $words .= $ones[$number] . ' ';
        }
        return trim($words);
    }

    $amount       = floatval($amount);
    $integer_part = floor($amount);
    $multiplier   = pow(10, $decimal_point);
    $decimal_part = round(($amount - $integer_part) * $multiplier);

    $words = $integer_part > 0 ? convertNumberToWords($integer_part, $ones, $tens, $hundreds) : 'Zero';

    if ($decimal_part > 0) {
        $words .= ' & Fils ' . $decimal_part . '/' . $multiplier;
    }

    return $words . ' Only.';
}
?>

    <!-- ══════════════════════════════════════════════════════════════════
         .page  –  outer wrapper (screen only)
         Inside it: ONE big table whose <thead> repeats on every print page
         ══════════════════════════════════════════════════════════════════ -->
    <div class="page">

        <table class="print-header-table">

            <!-- ── REPEATING HEADER ── printed on every page ── -->
            <thead>
                <tr>
                    <td>
                        <?php if (!empty($record['ltr_header_img'])): ?>
                            <img src="<?php echo base_url('') . $record['ltr_header_img']; ?>"
                                 alt="Company Header">
                        <?php endif; ?>
                    </td>
                </tr>
            </thead>

            <!-- ── BODY CONTENT ── -->
            <tbody>
                <tr>
                    <td>

                        <!-- Invoice Title & Number -->
                        <div class="invoice-header">
                            <div class="invoice-title">Purchase Order</div>
                            <div style="float:right; text-align:right;">
                                Date : <?php echo htmlspecialchars(date('d/m/Y', strtotime($record['po_date'] ?? ''))); ?><br>
                                Our PO No: <?php echo htmlspecialchars($record['po_no'] ?? 'N/A'); ?>
                            </div>
                        </div>
                        <br>

                        <!-- Vendor / Customer -->
                        <div class="customer-section">
                            <div class="customer-label">To,</div>
                            <div class="customer-address" style="padding-left:10px;">
                                <strong><?php echo htmlspecialchars($record['vendor_name'] ?? 'N/A'); ?></strong><br>
                                <?php echo nl2br(htmlspecialchars($record['vendor_address'] ?? 'N/A')); ?><br>
                                <?php if (!empty($record['vendor_country'])): ?>
                                    <?php echo htmlspecialchars($record['vendor_country']); ?><br>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($record['contact_person_name'])): ?>
                            <div>
                                <b>Attn: <?php echo htmlspecialchars($record['contact_person_name']); ?></b>
                            </div>
                        <?php endif; ?>

                        <div>
                            <b>Your Quote Ref: <?php echo htmlspecialchars($record['quote_no']); ?>, Dated on
                                <?php echo date('d-m-Y', strtotime($record['quote_date'] ?? 'N/A')); ?>
                            </b>
                            <div style="float:right;">
                                <b>Our Ref: <?php echo htmlspecialchars($record['enquiry_no']); ?></b>
                            </div>
                        </div>

                        <!-- Currency -->
                        <?php
                        $decimal_point     = isset($record['decimal_point']) ? intval($record['decimal_point']) : 3;
                        $currency_code     = $record['currency_code'] ?? 'BHD';
                        $total_net_amount  = 0;
                        $total_vat_amount  = 0;
                        $vat_rate          = 0;

                        if (!empty($item_list)) {
                            foreach ($item_list as $item) {
                                $net      = floatval($item['Net_amount'] ?? 0);
                                $vat_rate = floatval($item['gst'] ?? 0);
                                $total_net_amount += $net;
                            }
                        }

                        $total_vat_amount = (
                            ($total_net_amount + $record['transport_charges'] + $record['other_charges'])
                            * $vat_rate / 100
                        );
                        $grand_total = (
                            $total_net_amount
                            + $record['transport_charges']
                            + $record['other_charges']
                        ) + $total_vat_amount;
                        ?>

                        <div class="currency-badge">
                            <span>Currency: <?php echo htmlspecialchars($currency_code); ?></span>
                        </div>

                        <!-- Items Table -->
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th style="width:6%;">Item<br>No.</th>
                                    <th style="width:40%; text-align:left;">Description</th>
                                    <th style="width:5%;">Qty</th>
                                    <th style="width:5%;">Unit</th>
                                    <th style="width:10%; text-align:right;">Unit<br>Rate</th>
                                    <th style="width:10%; text-align:right;">Net Price</th>
                                    <th style="width:7%;">VAT<br>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($item_list)): ?>
                                    <?php foreach ($item_list as $i => $item):
                                        $net_amount     = floatval($item['Net_amount'] ?? 0);
                                        $vat_percentage = floatval($item['gst'] ?? 0);
                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo $i + 1; ?></td>
                                            <td class="text-left">
                                                <div class="item-description">
                                                    <?php echo htmlspecialchars($item['item_desc'] ?? ''); ?>
                                                </div>
                                            </td>
                                            <td class="text-center"><?php echo number_format($item['qty'] ?? 0, 0); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($item['uom'] ?? '-'); ?></td>
                                            <td class="text-right"><?php echo number_format($item['rate'] ?? 0, $decimal_point); ?></td>
                                            <td class="text-right"><?php echo number_format($net_amount, $decimal_point); ?></td>
                                            <td class="text-center"><?php echo number_format($vat_percentage, 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center" style="padding:30px; color:#999;">No items found</td>
                                    </tr>
                                <?php endif; ?>

                                <!-- Summary rows -->
                                <tr>
                                    <td colspan="5" class="text-right"><strong>TOTAL EXCL. VAT</strong></td>
                                    <td colspan="2" class="text-right">
                                        <strong><?php echo number_format($total_net_amount, $decimal_point); ?></strong>
                                    </td>
                                </tr>

                                <?php if ($record['transport_charges'] > 0): ?>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>TRANSPORT CHARGES</strong></td>
                                        <td colspan="2" class="text-right">
                                            <strong><?php echo number_format($record['transport_charges'], $decimal_point); ?></strong>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php if ($record['other_charges'] > 0): ?>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>OTHER CHARGES</strong></td>
                                        <td colspan="2" class="text-right">
                                            <strong><?php echo number_format($record['other_charges'], $decimal_point); ?></strong>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <tr>
                                    <td colspan="5" class="text-right">
                                        <strong>VAT <?php echo number_format($vat_rate, 0); ?>%</strong>
                                    </td>
                                    <td colspan="2" class="text-right">
                                        <strong><?php echo number_format($total_vat_amount, $decimal_point); ?></strong>
                                    </td>
                                </tr>

                                <tr style="background:#ffff; color:#000;">
                                    <td colspan="5" class="text-right">
                                        <strong>TOTAL <?php echo htmlspecialchars($currency_code); ?></strong>
                                    </td>
                                    <td colspan="2" class="text-right">
                                        <strong><?php echo number_format($grand_total, $decimal_point); ?></strong>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                        <!-- end items-table -->

                        <p>&nbsp;</p>

                        <!-- Notes -->
                        <?php if (!empty($record['remarks'])): ?>
                            <div style="margin:0; padding:10px; page-break-inside:avoid; break-inside:avoid;">
                                <div style="font-weight:bold; margin-bottom:5px;">Notes:</div>
                                <?php echo ($record['remarks']); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Terms & Conditions -->
                        <?php if (!empty($record['terms'])): ?>
                            <div style="margin:0; padding:10px; page-break-inside:avoid; break-inside:avoid;">
                                <div style="font-weight:bold; margin-bottom:5px;">Terms & Conditions:</div>
                                <?php echo ($record['terms']); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Signature -->
                        <div class="signature-section">
                            <div class="signature-box">
                                <div class="signature-company">
                                    For <?php echo htmlspecialchars($record['our_company'] ?? $record['company_name'] ?? 'Our Company'); ?>
                                </div>
                                <div class="signature-line"></div>
                            </div>
                        </div>

                    </td>
                </tr>
            </tbody>

        </table><!-- end print-header-table -->

    </div><!-- end .page -->

    <!-- Buttons (screen only, hidden on print) -->
    <div class="button-container">
        <button type="button" class="btn btn-primary"
            onclick="window.location.href='<?= site_url('vendor-po-list') ?>'">
            ← Back To List
        </button>
        <button type="button" class="btn btn-success" onclick="window.print()">
            🖨️ Print
        </button>
    </div>

</body>
</html>