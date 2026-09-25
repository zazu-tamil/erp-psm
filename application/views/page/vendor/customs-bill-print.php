<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Customs Bill - <?php echo htmlspecialchars($header['invoice_no'] ?? ''); ?></title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }


        #print-header {
            display: none !important;
        }

        @media print {
            body {
                margin-top: 0px;
            }

            #screen-header-row {
                display: table-row !important;
            }

            .no-print {
                display: none !important;
            }

            tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            td,
            th {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            @page {
                margin-top: 10mm !important;
                margin-bottom: 15mm;
                margin-left: 10mm;
                margin-right: 10mm;

                @bottom-right {
                    content: "Page " counter(page);
                    font-size: 12px;
                    font-family: Arial, Helvetica, sans-serif;
                }
            }
        }

        /* ============================================================ */

        .main-table {
            width: 800px;
            margin: auto;
            border-collapse: collapse;
        }

        .main-table td {
            padding: 5px;
        }

        .header-img {
            width: 100%;
        }

        .title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 4px;
        }

        .items-table {
            width: 95%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table th {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .items-table td {
            border: 1px solid #000;
            padding: 8px;
        }

        tr.items-table th,
        tr.items-table td {
            border: 1px solid #000;
            padding: 8px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total {
            font-weight: bold;
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
    </style>

</head>

<body>

    <?php if (!empty($header['ltr_header_img'])): ?>
        <!-- ============================================================
         FIXED PRINT HEADER — renders on every printed page
         ============================================================ -->
        <div id="print-header">
            <img src="<?php echo base_url('') . $header['ltr_header_img']; ?>" alt="Company Header">
        </div>
    <?php endif; ?>

    <table class="main-table" id="quoteTable">

        <!-- Screen Header (hidden during print; replaced by fixed #print-header above) -->
        <tr id="screen-header-row">
            <td colspan="7" height="200px;" style="border:0px solid red;">
                <?php if (!empty($header['ltr_header_img'])): ?>
                    <img src="<?php echo base_url('') . $header['ltr_header_img']; ?>" alt="Company Header"
                        class="header-img">
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <td colspan="7" class="title">
                Customs Bill
            </td>
        </tr>
        <tr>
            <td colspan="7">
                &nbsp;
            </td>
        </tr>

        <tr>
            <td colspan="3">
                <span class="label-bold">Entry Date:</span> <?php echo !empty($header['inv_entry_date']) ? date('d/m/Y', strtotime($header['inv_entry_date'])) : '-'; ?>
            </td>
            <td align="right" colspan="4">
                Date : <?php echo !empty($header['invoice_date']) ? date('d/m/Y', strtotime($header['invoice_date'])) : ''; ?><br>
                Inv No : <?php echo htmlspecialchars($header['invoice_no'] ?? 'N/A'); ?>
            </td>
        </tr>

        <?php
        $decimal_point = isset($header['decimal_point']) ? intval($header['decimal_point']) : 3;
        $currency_code = $header['currency_code'] ?? 'BHD';
        ?>

        <tr>
            <td colspan="4">
                <b>Supplier Details,</b><br><br>
                <span>
                    <strong><?php echo htmlspecialchars($header['supplier_name'] ?? 'N/A'); ?></strong><br>
                    <?php if (!empty($header['supplier_address'])): ?>
                        <?php echo nl2br(htmlspecialchars($header['supplier_address'])); ?><br>
                    <?php endif; ?>
                    <?php if (!empty($header['supplier_mobile'])): ?>
                        Mobile: <?php echo htmlspecialchars($header['supplier_mobile']); ?><br>
                    <?php endif; ?>
                    <?php if (!empty($header['supplier_vat_cr'])): ?>
                        VAT / CR No: <?php echo htmlspecialchars($header['supplier_vat_cr']); ?><br>
                    <?php endif; ?>
                </span>
            </td>
            <td colspan="3" valign="top">
                <b>Customer Details,</b><br><br>
                <span>
                    <strong><?php echo htmlspecialchars($header['customer_name'] ?? 'N/A'); ?></strong><br>
                    <?php if (!empty($header['customer_address'])): ?>
                        <?php echo nl2br(htmlspecialchars($header['customer_address'])); ?><br>
                    <?php endif; ?>
                    <?php if (!empty($header['customer_mobile'])): ?>
                        Mobile: <?php echo htmlspecialchars($header['customer_mobile']); ?><br>
                    <?php endif; ?>
                    <?php if (!empty($header['customer_vat_cr'])): ?>
                        VAT / CR No: <?php echo htmlspecialchars($header['customer_vat_cr']); ?><br>
                    <?php endif; ?>
                </span>
            </td>
        </tr>

        <!-- Remarks -->
        <tr class="items-table">
            <td colspan="7" class="text-left" style="padding: 10px;">
                <strong>Remarks:</strong><br>
                <?php echo nl2br(htmlspecialchars($header['remarks'] ?? '-')); ?>
            </td>
        </tr>

        <!-- Total Excl. VAT -->
        <tr class="items-table">
            <td colspan="5" class="text-right"><strong>AMT W/O VAT</strong></td>
            <td colspan="2" class="text-right" style="width: 15%;">
                <strong><?php echo number_format((($header['custom_stamp_fee'] ?? 0) + ($header['custom_duty'] ?? 0)), 3); ?></strong>
            </td>
        </tr>
        <tr class="items-table">
            <td colspan="5" class="text-right"><strong>VAT AMT</strong></td>
            <td colspan="2" class="text-right">
                <strong><?php echo number_format($header['vat_amt'] ?? 0, 3); ?></strong>
            </td>
        </tr>
        <tr class="items-table">
            <td colspan="5" class="text-right"><strong>TOTAL AMOUNT</strong></td>
            <td colspan="2" class="text-right" style="color: #00a65a;">
                <strong><?php echo number_format($header['customs_payable'] ?? 0, 3); ?></strong>
            </td>
        </tr>
        <tr class="items-table" style="background:#ffff; color:#000;">
            <td colspan="5" class="text-right"><strong>GRAND AMOUNT</strong></td>
            <td colspan="2" class="text-right" style="color: #00a65a;">
                <strong><?php echo number_format($header['customs_tot_amt'] ?? 0, 3); ?></strong>
            </td>
        </tr>

        <tr>
            <td colspan="7" align="right">
                <br><br>
                ____________________________<br>
                Authorized Signature
            </td>
        </tr>

    </table>

    <?php if (!isset($_POST['export_xls'])): ?>
        <div class="button-container no-print">
            <form action="<?php echo site_url('customs-bill-list/' . ($header['customs_bill_id'] ?? '')) ?>"
                method="post">
                <button type="button" class="btn btn-primary"
                    onclick="window.location.href='<?= site_url('customs-bill-list') ?>'">
                    ← Back To List
                </button>
                <button type="button" class="btn btn-success" onclick="window.print()">
                    🖨️ Print
                </button> 
            </form>
        </div>
    <?php endif; ?>

</body>

</html>