<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Vendor Rate Enquiry - <?php echo htmlspecialchars($record['enquiry_no'] ?? ''); ?></title>
    <style>
        /* Base layout */
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
            background: #fff;
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

        .hide-rate-value {
            color: transparent;
            user-select: none;
        }

        .empty-space {
            min-height: 20px;
            border-bottom: 1px solid #000;
        }

        /* Keep printer's default margins */
        @page {
            size: A4;
            margin: default;
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
                box-shadow: none;
                page-break-after: always;
            }
        }
    </style>
</head>

<body>

    <div class="page">

        <h2><?php echo htmlspecialchars($record['our_company'] ?? 'Our Company'); ?></h2>
        <p style="text-align: center;"><strong>Vendor Rate Enquiry</strong></p>

        <!-- Header Section -->
        <table class="no-border">
            <tr>
                <td style="width:50%; line-height:1.6;">
                    <p><strong>To,</strong></p>
                    <p><?php echo htmlspecialchars($record['vendor_name'] ?? 'Vendor Name'); ?></p>

                    <?php
                    $address = '';
                    if (!empty($record['vendor_address'])) {
                        $address = $record['vendor_address'];
                    } elseif (!empty($record['address'])) {
                        $address = $record['address'];
                    }
                    if ($address) {
                        echo '<p>' . nl2br(htmlspecialchars($address)) . '</p>';
                    }
                    ?>

                    <p><strong>Mobile:</strong> <?php echo htmlspecialchars($record['vendor_mobile'] ?? $record['mobile'] ?? 'N/A'); ?></p>
                </td>

                <td style="width:50%; text-align:right; line-height:1.6;">
                    <p><strong>Date:</strong> <?php echo date('d-m-Y', strtotime($record['enquiry_date'])); ?></p>
                    <p><strong>Enquiry No:</strong>
                        <?php echo htmlspecialchars($record['enquiry_no'] ?? ''); ?>
                    </p>
                    <p><strong>Customer:</strong>
                        <?php echo htmlspecialchars($record['customer_name'] ?? 'N/A'); ?>
                    </p>
                    <p><strong>Tender Ref:</strong>
                        <?php echo htmlspecialchars($record['tender_enquiry_no'] ?? 'N/A'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <!-- Item Table -->
        <table>
            <thead>
                <tr>
                    <th style="width:5%;">S.No</th>
                    <!-- <th style="width:15%;">Category</th> -->
                    <!-- <th style="width:20%;">Item</th> -->
                    <th style="width:25%;">Item Description</th>
                    <th style="width:8%;">UOM</th>
                    <th style="width:8%;">Qty</th>
                    <th class="rate-column" style="width:10%;">Rate</th>
                    <th class="rate-column" style="width:8%;">VAT (%)</th>
                    <th class="rate-column" style="width:10%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $index => $item): ?>
                        <tr>
                            <td style="text-align:center;"><?php echo $index + 1; ?></td>
                            <!-- <td><?php echo htmlspecialchars($item['category_name'] ?? '-'); ?></td> -->
                            <!-- <td><?php echo htmlspecialchars($item['item_name'] ?? '-'); ?></td> -->
                            <td>
                                <?php echo htmlspecialchars($item['item_name'] ?? '-'); ?> <br>
                                <?php echo htmlspecialchars($item['item_desc'] ?: $item['item_description'] ?: 'N/A'); ?>
                            </td>
                            <td style="text-align:center;"><?php echo htmlspecialchars($item['uom'] ?: $item['item_uom'] ?: '-'); ?></td>
                            <td style="text-align:center;"><?php echo number_format($item['qty'], 2); ?></td>
                            <td class="rate-column" style="text-align:right;"><span class="rate-value"><?php echo number_format($item['rate'], 2); ?></span></td>
                            <td class="rate-column" style="text-align:center;"><span class="rate-value"><?php echo number_format($item['gst'], 2); ?></span></td>
                            <td class="rate-column" style="text-align:right;"><span class="rate-value"><?php echo number_format($item['amount'], 2); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center;">No items found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="rate-column">
                    <th colspan="6" style="text-align:right;">Sub Total</th>
                    <th style="text-align:right;"><span class="rate-value"><?php echo number_format($grand_total, 2); ?></span></th>
                </tr>
                <tr class="rate-column">
                    <th colspan="6" style="text-align:right;">VAT</th>
                    <th style="text-align:right;"><span class="rate-value"><?php echo number_format($total_gst, 2); ?></span></th>
                </tr>
                <tr class="rate-column">
                    <th colspan="6" style="text-align:right; font-size:13pt;">Grand Total</th>
                    <th style="text-align:right; font-size:13pt;"><span class="rate-value"><?php echo number_format($final_total, 2); ?></span></th>
                </tr>
            </tfoot>
        </table>

        <p>&nbsp;</p>

        <!-- Terms -->
        <div class="section">
            <p><strong>Terms & Conditions:</strong></p>
            <p>1. Rates quoted are inclusive of all taxes unless specified.</p>
            <p>2. Validity of rates: 15 days from the date of this enquiry.</p>
            <p>3. Payment terms: As mutually agreed.</p>
            <p>4. Delivery: Ex-works / FOR destination.</p>
        </div>

        <!-- Signature -->
        <div class="signature">
            <p>For <?php echo htmlspecialchars($record['our_company'] ?? 'Our Company'); ?></p>
            <br>
            <p><strong>Authorised Signatory</strong></p>
        </div>
    </div>

    <div style="text-align:center; margin:20px;">
        <button onclick="printWithRate()">Print with Rate</button>
        <button onclick="printWithoutRate()">Print without Rate</button>
    </div>

    <script>
        function printWithRate() {
            // Show all rate values
            const rateValues = document.querySelectorAll('.rate-value');
            rateValues.forEach(val => {
                val.classList.remove('hide-rate-value');
            });
            window.print();
        }

        function printWithoutRate() {
            // Hide rate values but keep the space
            const rateValues = document.querySelectorAll('.rate-value');
            rateValues.forEach(val => {
                val.classList.add('hide-rate-value');
            });
            window.print();

            // Show them again after print (so user can see them on screen)
            setTimeout(() => {
                rateValues.forEach(val => {
                    val.classList.remove('hide-rate-value');
                });
            }, 500);
        }
    </script>

</body>

</html>