<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Challan - Gujarat Freight Tools</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", sans-serif;
            background: #eef2f3;
            color: #1a1a1a;
            padding: 20px;
        }

        /* -------------------------------------
            BUTTONS 
        ---------------------------------------*/
        .top-buttons {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 22px;
            border-radius: 6px;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-right: 10px;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
        }

        .btn-back {
            background: #007bff;
        }

        .btn-print {
            background: #28a745;
        }

        .btn:hover {
            opacity: 0.85;
        }

        /* -------------------------------------
            WRAPPER
        ---------------------------------------*/


        /* -------------------------------------
            HEADER
        ---------------------------------------*/
        .header {
            background: #ffffffff;
            color: white;
            padding: 25px 32px;
            margin: 0 auto;
            justify-content: space-between;
            align-items: center;
        }

        .header img {

            margin: 0 auto;
            display: block;
            align-items: center;
        }

        .company-info h1 {
            font-size: 32px;
            font-weight: 700;
        }

        .tagline {
            opacity: 0.9;
            font-size: 15px;
            font-weight: 500;
        }

        .company-details {
            margin-top: 8px;
            font-size: 13px;
        }

        .logo {
            width: 110px;
            height: 110px;
            background: #fff;
            border-radius: 12px;
            object-fit: contain;
            padding: 8px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.4);
        }

        /* TITLE BAR */
        .title-bar {
            background: #48459b;
            color: white;
            text-align: center;
            padding: 14px;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* -------------------------------------
            INFO SECTION
        ---------------------------------------*/
        .info-section {
            padding: 25px 32px;
            display: flex;
            gap: 50px;
            border-bottom: 1px dashed #ccc;
            font-size: 14px;
        }

        .info-block {
            flex: 1;
        }

        .label {
            width: 135px;
            display: inline-block;
            font-weight: 600;
            color: #2c3e50;
        }

        /* -------------------------------------
            TABLE
        ---------------------------------------*/
        table {
            width: 100%;
            margin: 25px auto;
            border-collapse: collapse;
            font-size: 13px;
        }

        th {
            background: #48459b;
            color: white;
            padding: 12px;
            font-size: 13px;
            text-transform: uppercase;
            border: 1px solid #2f6e2d;
        }

        td {
            padding: 11px;
            border: 1px solid #444;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background: #f8fff8;
        }

        .total-row {
            background: #e6f9e6 !important;
            font-weight: 700;
            font-size: 14px;
        }

        /* -------------------------------------
            TERMS & FOOTER
        ---------------------------------------*/
        .terms {
              margin-bottom: 30px;

            padding: 16px;
            background: #fafafa;
            border-left: 4px solid #48459b;
            border-radius: 6px;
            font-size: 13.5px;
        }

        .footer {
            padding: 40px 30px;
            text-align: right;
            border-top: 1px solid #ddd;
        }

        .signature-box {
            display: inline-block;

            font-weight: 600;
            padding-top: 10px;
            width: 260px;
        }
        .no-border td {
            border: none !important; 
            background: transparent !important;
        }

        /* -------------------------------------
            PRINT MODE (Color Safe)
        ---------------------------------------*/
        @media print {

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                background: white !important;
            }

            .no-print {
                display: none !important;
            }

            @page {
                margin: 5mm;
            }
        }
    </style>
</head>

<body>


    <!-- BUTTONS -->
    <div class="top-buttons no-print">
        <a href="<?php echo site_url('tender-dc-list'); ?>" class="btn btn-back">Back list</a>
        <!-- Print Button -->
        <button type="button" class="btn btn-print" onclick="window.print()">
            🖨 Print
        </button>
    </div>

    <!-- CONTENT -->
    <div class="invoice-wrapper">

        <!-- Header -->
        <div class="header">
            <img src="<?= base_url('' . $record['ltr_header_img']); ?>">
        </div>

        <!-- Title -->
        <div class="title-bar">Delivery Challan</div>

        <!-- Info -->
        <div class="info-section">
            <div class="info-block">
                <strong>Bill To:</strong><br>
                <strong>M/S:</strong> <?php echo $record['customer_name']; ?><br>
                <strong>Address:</strong> <br> <?php echo nl2br($record['customer_address']); ?>
            </div>

            <div class="info-block">
                <div><span class="label">DC No:</span> <?php echo $record['dc_no']; ?></div>
                <div><span class="label">Date:</span> <?php echo date('d-m-Y', strtotime($record['dc_date'])); ?></div>
                <div><span class="label">PO No:</span> <?php echo $record['po_no']; ?></div>
                <div><span class="label">PO Date:</span> <?php echo date('d-m-Y', strtotime($record['po_date'])); ?></div>
                <!-- <div><span class="label">Place of Supply:</span> Gujarat (24)</div>
                <div><span class="label">Phone:</span> 9814556613</div>
                <div><span class="label">GSTIN:</span> 24AAACC1206D1ZG</div> -->
                <table class="table no-border">
                    <tr>
                        <td>DC No : </td><td><?php echo $record['dc_no']; ?></td>
                    </tr>
                    <tr>
                        <td>DC No : </td><td><?php echo $record['dc_no']; ?></td>
                    </tr>
                    <tr>
                        <td>DC No : </td><td><?php echo $record['dc_no']; ?></td>
                    </tr>
                    <tr>
                        <td>DC No : </td><td><?php echo $record['dc_no']; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Table -->
        <table>
            <tr>
                <th style="width: 5%;">Sr.</th> 
                <th style="width: 60%;">Description of Goods</th>
                <th style="width: 10%;">UOM</th>
                <th style="width: 10%;">Quantity</th>
            </tr>

            <?php foreach ($items as $j => $item) { ?>
                <tr>
                    <td class="text-center"><?php echo ($j + 1); ?></td> 
                    <td class="text-center"><?php echo $item['item_code']; ?> : <?php echo $item['item_desc']; ?></td>
                    <td class="text-center"><?php echo $item['uom']; ?></td>
                    <td class="text-center"><?php echo $item['qty']; ?></td>
                </tr>
            <?php } ?>
            <tr class="">
                <td colspan="3" style="text-align: right;"><strong>Total Qty :</strong></td>
                <?php
                $total_qty = 0;
                foreach ($items as $row) {
                    $total_qty += $row['qty'];
                }
                ?>
                <td class="text-center"><strong><?= $total_qty; ?></strong></td>
            </tr>
        </table>

        <?php if ($record['remarks'] != '') { ?>
            <div class="terms">
                <strong>Note:</strong><br><br>
                <?php echo nl2br($record['remarks']); ?>
            </div>
        <?php } ?>

        <?php if ($record['terms'] != '') { ?>
            <div class="terms">
                <strong>Terms & Conditions:</strong><br><br>
                <?php echo nl2br($record['terms']); ?>
            </div>
        <?php } ?>


        <style>
            .span_sing {
                display: block;
                border-bottom: 1px solid black;
                padding-bottom: 32px;
                text-align: center;
            }
        </style>
        <!-- Footer -->
        <div class="foote1r">
            <table style="width:100%; height:100px; margin-top:50px; border:none;">
                <tr>
                    <td><span class="span_sing1"> Goods Received By</span></td>
                    <td><span class="span_sing1"> Seal & Signatare</span></td>
                </tr>
                </table>
             
             
        </div>
    </div>

</body>

</html>