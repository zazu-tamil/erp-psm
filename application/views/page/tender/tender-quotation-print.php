<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Tender Quotation - <?php echo htmlspecialchars($record['quotation_no'] ?? ''); ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .page {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 0 10mm;
            background: #fff;
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }

        th {
            background: #f2f2f2;
            text-align: center;
        }

        td {
            font-size: 11pt;
        }

        .no-border td {
            border: none !important;
        }

        .section {
            margin-bottom: 25px;
        }

        .signature {
            text-align: right;
            margin-top: 50px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        @media print {
            body {
                margin: 0;
                -webkit-print-color-adjust: exact;
            }

            button {
                display: none;
            }

            .page {
                margin: 0;
                padding: 0 10mm;
            }
        }
    </style>
</head>

<body>

    <div class="page">

        <!-- Company -->
        <h2><?php echo htmlspecialchars($record['our_company'] ?? 'Our Company'); ?></h2>
        <p class="text-center"><strong>Tender Quotation</strong></p>

        <!-- Header -->
        <table class="no-border">
            <tr>
                <td style="width:50%; line-height:1.6;">
                    <p><strong>To,</strong></p>
                    <p><?php echo htmlspecialchars($record['customer_name'] ?? 'Customer Name'); ?></p>
                </td>
                <td style="width:50%; text-align:right; line-height:1.6;">
                    <p><strong>Date:</strong> <?php echo date('d-m-Y', strtotime($record['quote_date'])); ?></p>
                    <p><strong>Quotation No:</strong> <?php echo htmlspecialchars($record['quotation_no'] ?? ''); ?></p>
                    <p><strong>Tender Ref No:</strong>
                        <?php echo htmlspecialchars($record['tender_ref_no'] ?? 'N/A'); ?></p>
                    <p><strong>Tender Enquiry No:</strong>
                        <?php echo htmlspecialchars($record['tender_enquiry_no'] ?? 'N/A'); ?></p>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width:5%;">S.No</th>
                    <th style="width:25%;">Item Description</th>
                    <th style="width:8%;">UOM</th>
                    <th style="width:8%;">Qty</th>
                    <th style="width:10%;">Rate</th>
                    <th style="width:8%;">VAT %</th>
                    <th style="width:10%;">VAT Amt</th>
                    <th style="width:10%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $i => $item): ?>
                        <tr>
                            <td class="text-center"><?php echo $i + 1; ?></td>
                            <td>
                                <?php echo htmlspecialchars($item['item_name'] ?? '-'); ?><br>
                                <?php echo htmlspecialchars($item['item_desc'] ?: $item['item_description'] ?: ''); ?>
                            </td>
                            <td class="text-center"><?php echo htmlspecialchars($item['uom'] ?: $item['item_uom'] ?: '-'); ?>
                            </td>
                            <td class="text-center"><?php echo number_format($item['qty'], 2); ?></td>
                            <td class="text-right"><?php echo number_format($item['rate'], 2); ?></td>
                            <td class="text-center"><?php echo number_format($item['gst'], 2); ?></td>
                            <td class="text-right"><?php echo number_format($item['gst_amount'], 2); ?></td>
                            <td class="text-right"><?php echo number_format($item['amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No items found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-right">Sub Total</th>
                    <th class="text-right"><?php echo number_format($grand_total, 2); ?></th>
                    <th class="text-right"><?php echo number_format($grand_total, 2); ?></th>
                </tr>

                <!-- GST Summary -->
                <?php foreach ($gst_summary as $gst): ?>
                    <tr>
                        <td colspan="6" class="text-right">VAT @ <?php echo $gst['gst']; ?>%</td>
                        <td class="text-right"><?php echo number_format($gst['gst_amount'], 2); ?></td>
                        <td class="text-right"><?php echo number_format($gst['base'] + $gst['gst_amount'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>

                <tr>
                    <th colspan="6" class="text-right" style="font-size:13pt;">Grand Total</th>
                    <th class="text-right" style="font-size:13pt;"><?php echo number_format($final_total, 2); ?></th>
                    <th class="text-right" style="font-size:13pt;"><?php echo number_format($final_total, 2); ?></th>
                </tr>
            </tfoot>
        </table>

        <!-- Remarks -->
        <?php if (!empty($record['remarks'])): ?>
            <div class="section">
                <p><strong>Remarks:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($record['remarks'])); ?></p>
            </div>
        <?php endif; ?>

        <!-- Terms -->
        <div class="section">
            <div class="section">
                 <?php echo $record['terms']; ?>
            </div>

        </div>


        <!-- Signature -->
        <div class="signature">
            <p>For <?php echo htmlspecialchars($record['our_company'] ?? 'Our Company'); ?></p>
            <br><br>
            <p><strong>Authorised Signatory</strong></p>
        </div>

    </div>

    <!-- Print Button -->
    <div style="text-align:center; margin:30px 0;">
        <button onclick="window.print()" style="padding:10px 25px; font-size:14pt; cursor:pointer;">
            Print
        </button>
    </div>

</body>

</html>