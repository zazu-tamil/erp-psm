<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Tender Quotation - <?php echo htmlspecialchars($record['quotation_no'] ?? ''); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            background: #f5f5f5;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 15mm;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header-img {
            width: 100%;
            max-height: 120px;
            object-fit: contain;
        }

        .company-title {
            text-align: center;
            font-size: 24pt;
            font-weight: bold;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 10px;
        }

        .document-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            color: #0066cc;
            margin: 20px 0 25px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border: 2px solid #0066cc;
            border-radius: 5px;
            overflow: hidden;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 12px 15px;
            vertical-align: top;
            line-height: 1.8;
        }

        .info-cell.left {
            width: 50%;
            border-right: 2px solid #0066cc;
            background: #f9f9f9;
        }

        .info-cell.right {
            width: 50%;
            background: #fff;
        }

        .info-label {
            font-weight: bold;
            color: #0066cc;
            display: inline-block;
            min-width: 120px;
        }

        .customer-name {
            font-size: 12px;
            font-weight: bold;
            color: #1a1a1a;
            margin-top: 5px;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 10pt;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .items-table thead tr {
            background: linear-gradient(to bottom, #0066cc, #0052a3);
            color: #fff;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
        }

        .items-table thead th {
            padding: 12px 8px;
            border: 1px solid #0052a3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table tbody td {
            padding: 10px 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .items-table tbody tr:hover {
            background: #f0f8ff;
        }

        .item-description {
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 3px;
        }

        .item-details {
            font-size: 9pt;
            color: #666;
            font-style: italic;
        }

        .items-table tfoot tr {
            background: #f5f5f5;
            font-weight: bold;
        }

        .items-table tfoot th {
            padding: 10px 8px;
            border: 1px solid #ddd;
        }

        .items-table tfoot tr.grand-total {
            background: linear-gradient(to bottom, #0066cc, #0052a3);
            color: #fff;
            font-size: 12pt;
        }

        .items-table tfoot tr.grand-total th {
            border: 1px solid #0052a3;
            padding: 12px 8px;
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

        .section {
            margin: 25px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #0066cc;
            border-radius: 3px;
        }

        .section-title {
            font-weight: bold;
            font-size: 12pt;
            color: #0066cc;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .section-content {
            color: #333;
            line-height: 1.6;
        }

        .terms-section {
            margin: 25px 0;
            padding: 15px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .terms-section ul {
            margin-left: 20px;
            margin-top: 10px;
        }

        .terms-section li {
            margin-bottom: 8px;
            line-height: 1.6;
        }

        .signature-section {
            margin-top: 60px;
            text-align: right;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            min-width: 250px;
        }

        .signature-company {
            font-size: 11pt;
            color: #666;
            margin-bottom: 60px;
        }

        .signature-line {
            border-top: 2px solid #333;
            margin: 10px 0;
        }

        .signature-label {
            font-weight: bold;
            font-size: 11pt;
            color: #1a1a1a;
            margin-top: 5px;
        }

        .footer-note {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #0066cc;
            text-align: center;
            font-size: 9pt;
            color: #666;
            font-style: italic;
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
                padding: 15mm;
                box-shadow: none;
            }

            .button-container {
                display: none;
            }

            .items-table tbody tr:hover {
                background: inherit;
            }

            .items-table thead tr {
                background: #0066cc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .items-table tfoot tr.grand-total {
                background: #0066cc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .address_info {
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>

    <div class="page">
        <!-- Header Image -->
        <?php if (!empty($record['ltr_header_img'])): ?>
            <img src="<?php echo base_url('') . $record['ltr_header_img']; ?>" alt="Company Header" class="header-img">
        <?php endif; ?>
        <p>&nbsp;</p>

        <!-- Customer & Quotation Info -->
        <div class="info-section">
            <div class="info-row">
                <div class="info-cell left">
                    <div><span class="info-label">To:</span></div>
                    <div class="customer-name">
                        <?php echo $record['vendor_name']; ?><br>
                        <?php echo nl2br($record['address'] ?? 'N/A'); ?><br>
                        <?php echo nl2br($record['country'] ?? 'N/A'); ?><br>
                     </div>
                </div>
                <div class="info-cell right">
                    <div><span class="info-label">Date:</span>
                        <?php echo date('d-m-Y', strtotime($record['po_date'])); ?></div>
                    <div><span class="info-label">Po No:</span>
                        <?php echo htmlspecialchars($record['po_no'] ?? 'N/A'); ?></div>


                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:5%;">S.No</th>
                    <th style="width:30%;">Item Description</th>
                    <th style="width:8%;">UOM</th>
                    <th style="width:8%;">Qty</th>
                    <th style="width:10%;">Rate</th>
                    <th style="width:8%;">VAT %</th>
                    <th style="width:11%;">VAT Amt</th>
                    <th style="width:12%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $i => $item): ?>
                        <tr>
                            <td class="text-center"><?php echo $i + 1; ?></td> 
                            <td class="text-center"><?php echo htmlspecialchars($item['item_desc'] ?? '-'); ?>
                            <td class="text-center"><?php echo htmlspecialchars($item['uom'] ?? '-'); ?>
                            </td>
                            <td class="text-center"><?php echo number_format($item['qty'], 2); ?></td>
                            <td class="text-right"><?php echo number_format($item['rate'], 2); ?></td>
                            <td class="text-center"><?php echo number_format($item['gst'], 2); ?>%</td>
                            <td class="text-right"><?php echo number_format($item['gst_amount'], 2); ?></td>
                            <td class="text-right"><strong><?php echo number_format($item['amount'], 2); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding:20px; color:#999;">No items found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" class="text-right">Transport, Packing & Courier</th>
                    <th class="text-right"><?php echo number_format($record['handling_charges'] ?? 0, 2); ?></th>
                </tr>
                <tr class="grand-total">
                    <th colspan="7" class="text-right">GRAND TOTAL</th>
                    <th class="text-right"><?php echo number_format($final_total, 2); ?></th>
                </tr>
            </tfoot>
        </table>

        <!-- Remarks -->
        <?php if (!empty($record['remarks'])): ?>
            <div class="section">
                <div class="section-title">Remarks</div>
                <div class="section-content"><?php echo nl2br(htmlspecialchars($record['remarks'])); ?></div>
            </div>
        <?php endif; ?>

        <!-- Terms & Conditions -->
        <?php if (!empty($record['terms'])): ?>
            <div class="terms-section">
                <div class="section-title">Terms & Conditions</div>
                <div class="section-content">
                    <?php echo $record['terms']; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-company">For
                    <?php echo htmlspecialchars($record['our_company'] ?? $record['company_name'] ?? 'Our Company'); ?>
                </div>
                <div class="signature-line"></div>
                <div class="signature-label">Authorised Signatory</div>
            </div>
        </div>


    </div>

    <!-- Action Buttons -->
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