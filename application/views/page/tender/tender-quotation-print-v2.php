<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Quotation-<?php echo $record['tender_quotation_no']; ?></title>

    <style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
    }

    .main-table {
        width: 900px;
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
        width: 100%;
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

    <table class="main-table" id="quoteTable">

        <!-- Header -->
        <tr>
            <td colspan="7" height="200px;" style="border:0px solid red;">
                <?php if (!empty($record['ltr_header_img'])): ?>
                <img src="<?php echo base_url('') . $record['ltr_header_img']; ?>" alt="Company Header"
                    class="header-img ">
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <td colspan="7" class="title">
                QUOTATION
            </td>
        </tr>
        <?php if (!empty($record['contact_person'])): ?>
        <tr>
            <td colspan="7" class="title">
                <b> Attn: <?php echo htmlspecialchars($record['contact_person']); ?></b>
            </td>
        </tr>
        <?php endif; ?>

        <tr>
            <td colspan="4">
                <span class="label-bold">Our Ref No:</span> <?php echo $record['our_enq_ref_details'] ?? '-'; ?>
            </td>

            <td align="right" colspan="3">
                Date : <?php echo htmlspecialchars(date('d/m/Y', strtotime($record['quote_date']))); ?><br>
                Quote No : <?php echo htmlspecialchars($record['tender_quotation_no'] ?? 'N/A'); ?>
            </td>
        </tr>

        <!-- Customer -->

        
                <?php 
                $decimal_point = isset($record['decimal_point']) ? intval($record['decimal_point']) : 3;
                $currency_code = $record['currency_code'] ?? 'BHD';
                 $total_net_amount = 0;
                 $total_vat_amount = 0;
                ?>

                
                    <tr>
                        <td colspan="5">
                            <b>To,</b><br><br>
                            <span style="">
                                <strong><?php echo htmlspecialchars($record['customer_name'] ?? 'N/A'); ?></strong><br>
                                <?php echo nl2br(htmlspecialchars($record['address'] ?? 'N/A')); ?><br>
                                <?php if (!empty($record['customer_country'])): ?>
                                <b><?php echo htmlspecialchars($record['customer_country']); ?></b><br>
                                <?php endif; ?>
                                <?php if (!empty($record['vat_account_no'])): ?>
                                VAT Account. No: <?php echo htmlspecialchars($record['vat_account_no']); ?>
                                <?php endif; ?>
                            </span>
                        </td>

                        <td colspan="2"  align="right">
                            <span>Currency: <?php echo htmlspecialchars($currency_code); ?></span>
                        </td>
                    </tr>
                 

        <!-- Subject -->

        <tr>
            <td colspan="7" align="center">
                <u>SUB: Your Enquiry Ref:
                    <?php echo htmlspecialchars($record['tender_ref_no']); ?>, Dated:
                    <?php echo date('d/m/Y', strtotime($record['tender_enquiry_date'] ?? '')); ?>.</u>
            </td>
        </tr>

        <tr>
            <td colspan="7">
                Dear Sir,<br><br>
                Thank you for your enquiry, we are pleased to offer our best price with terms and conditions below.
            </td>
        </tr>

        <!-- Items Table -->

        <!-- <tr>
            <td colspan="7">

                <table class="items-table"> -->

                    <tr class="items-table">
                        <th width="8%">Item No</th>
                        <th width="40%" align="left">Description</th>
                        <th width="8%">Qty</th>
                        <th width="8%">Unit</th>
                        <th width="12%">Unit Rate</th>
                        <th width="12%">Net Price</th>
                        <th width="10%">VAT %</th>
                    </tr>


                    <?php if (!empty($item_list)): ?>
                    <?php foreach ($item_list as $i => $item):
                        $net_amount = floatval($item['Net_amount'] ?? 0);
                        $vat_percentage = floatval($item['gst'] ?? 0);
                        
                        $net = floatval($item['Net_amount'] ?? 0);
                        $vat_rate = floatval($item['gst'] ?? 0);
                        $vat = $net * $vat_rate / 100;
                        $total_net_amount += $net;
                        $total_vat_amount += $vat;
                        ?>
                    <tr class="items-table">
                        <td class="text-center"><?php echo $i + 1; ?></td>
                        <td class="text-left">
                            <div class="item-description">
                                <?php if (!empty($item['item_code'])): ?>
                                <div class="item-code"><?php echo htmlspecialchars($item['item_code']); ?></div>
                                <?php endif; ?>
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
                    <tr class="items-table">
                        <td colspan="7" class="text-center" style="padding:30px; color:#999;">No items found</td>
                    </tr>
                    <?php endif; 
                    
                    $grand_total = $total_net_amount + $total_vat_amount;
                    ?>

                    <tr class="items-table">
                        <td colspan="5" class="text-right"><strong>TOTAL EXCL. VAT</strong></td>
                        <td colspan="2" class="text-right">
                            <strong><?php echo number_format($total_net_amount, $decimal_point); ?></strong>
                        </td>
                    </tr>
                    <tr class="items-table">
                        <td colspan="5" class="text-right"><strong>VAT
                                <?php echo number_format($vat_percentage ?? 0, 0); ?>%</strong></td>
                        <td colspan="2" class="text-right">
                            <strong><?php echo number_format($total_vat_amount, $decimal_point); ?></strong>
                        </td>
                    </tr>
                    <tr class="items-table" style="background:#ffff; color:#000;">
                        <td colspan="5" class="text-right"><strong>TOTAL
                                <?php echo htmlspecialchars($currency_code); ?></strong></td>
                        <td colspan="2" class="text-right">
                            <strong><?php echo number_format($grand_total, $decimal_point); ?></strong>
                        </td>
                    </tr>

                <!-- </table>

            </td>
        </tr> -->
        <?php if (!empty($record['remarks'])): ?>
        <tr>
            <td colspan="7">
                <div style="font-weight:bold; margin-bottom:5px;">Notes:</div>
                <?php echo ($record['remarks']); ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($record['terms'])): ?>
        <tr>
            <td colspan="7">
                <div style="padding:1px; ">
                    <div style="font-weight:bold; margin-bottom:5px;">Terms & Conditions:</div>
                    <?php echo ($record['terms']); ?>
                </div>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
            <td colspan="7" height="80"></td>
        </tr>

        <tr>
            <td colspan="7" align="right">
                For <?php echo htmlspecialchars($record['our_company'] ?? $record['company_name'] ?? 'Our Company'); ?>
                <br><br>
                ____________________________
            </td>
        </tr>

    </table>
     <?php if(!isset($_POST['export_xls'])) { ?>       
    <div class="button-container">
        <form action="<?php echo site_url('tender/tender_quotation_xls_export/' . $record['tender_quotation_id']) ?>" method="post">  
        <button type="button" class="btn btn-primary"
            onclick="window.location.href='<?= site_url('tender-quotation-list') ?>'">
            ← Back To List
        </button>
        <button type="button" class="btn btn-success" onclick="window.print()">
            🖨️ Print
        </button>
          
        <button class="btn btn-success" type="submit" name="export_xls" value="export To xls" >
            Export to Excel
            </button>
            </form>
    </div>
    <?php } ?>


</body>
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function exportTableToExcel(tableID, filename = ''){
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

    filename = filename ? filename + '.xls' : 'excel_data.xls';

    var downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);

    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
        downloadLink.download = filename;
        downloadLink.click();
    }
}

</script> -->
    
</html>