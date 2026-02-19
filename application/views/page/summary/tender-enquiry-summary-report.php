<?php include_once(VIEWPATH . 'inc/header.php'); ?>

<section class="content-header">
    <h1><?php echo $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Summary</a></li>
        <li class="active"><?php echo $title; ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Search Filter -->
    <div class="box box-info no-print">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>

        <div class="box-body">
            <form method="post" action="<?php echo site_url('tender-enquiry-summary-report'); ?>" id="frmsearch">

                <div class="row">

                    <!-- Enquiry No -->
                    <div class="form-group col-md-3">
                        <label for="srch_enquiry_no_id">Our Enquiry No</label>
                        <input type="text" name="srch_enquiry_no_id" id="srch_enquiry_no_id" class="form-control"
                            value="<?php echo isset($srch_enquiry_no_id) ? $srch_enquiry_no_id : ''; ?>"
                            placeholder="Search The Our Enquiry No">
                        <input type="hidden" name="tender_enquiry_id" id="tender_enquiry_id"
                            value="<?php echo isset($tender_enquiry_id) ? $tender_enquiry_id : ''; ?>">
                    </div>

                    <!-- Open All Checkbox -->
                    <div class="form-group col-md-3" style="margin-top:25px;">
                        <label>
                            <input type="checkbox" id="chk_open_all"> Open All Sections
                        </label>
                    </div>

                    <!-- Show Button -->
                    <div class="form-group col-md-2" style="margin-top:20px;">
                        <button class="btn btn-success" type="submit" name="btn_show" value="Show">
                            <i class="fa fa-search"></i> Show
                        </button>
                    </div>

                </div>

            </form>
        </div>
    </div>


    <!-- Styles -->
    <style>
        .bg_table_header {
            background-color: #3c8dbc !important;
            color: white !important;
        }

        .bg_top_header {
            background-color: #dddddd !important;
            color: #000 !important;
        }

        /* Bootstrap 3 Responsive Table Enhancements */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            -ms-overflow-style: -ms-autohiding-scrollbar;
        }

        @media screen and (max-width: 767px) {
            .table-responsive {
                width: 100%;
                margin-bottom: 15px;
                overflow-y: hidden;
                -ms-overflow-style: -ms-autohiding-scrollbar;
                border: 1px solid #ddd;
            }

            .table-responsive>.table {
                margin-bottom: 0;
            }

            .table-responsive>.table>thead>tr>th,
            .table-responsive>.table>tbody>tr>th,
            .table-responsive>.table>tfoot>tr>th,
            .table-responsive>.table>thead>tr>td,
            .table-responsive>.table>tbody>tr>td,
            .table-responsive>.table>tfoot>tr>td {
                white-space: nowrap;
            }
        }
    </style>


    <!-- Tender Enquiry List -->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border clearfix">
            <h3 class="box-title">Tender Enquiry List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <?php if (!empty($tender_enquiry_list)): ?>
                        <?php foreach ($tender_enquiry_list as $enquiry_info):
                            $info = $enquiry_info['info'] ?? [];
                            ?>
                            <thead>
                                <!-- ENQUIRY HEADER -->
                                <tr class="bg_top_header">
                                    <th width="15%">Company</th>
                                    <td width="18%">
                                        <?php echo htmlspecialchars($info['company_name'] ?? ''); ?>
                                    </td>
                                    <th width="15%">Customer Name</th>
                                    <td width="18%">
                                        <?php echo htmlspecialchars($info['customer_name'] ?? ''); ?>
                                    </td>
                                    <th width="15%">Enquiry No</th>
                                    <td width="19%">
                                        <?php echo htmlspecialchars($info['enquiry_no'] ?? ''); ?>
                                    </td>
                                </tr>
                                <tr class="bg_top_header">
                                    <th>Tender Name</th>
                                    <td>
                                        <?php echo htmlspecialchars($info['tender_name'] ?? ''); ?>
                                    </td>
                                    <th>Enquiry Date</th>
                                    <td>
                                        <?php echo !empty($info['enquiry_date'])
                                            ? date('d-m-Y', strtotime($info['enquiry_date']))
                                            : ''; ?>
                                    </td>
                                    <th>Tender Status</th>
                                    <td>
                                        <?php echo htmlspecialchars($info['tender_status'] ?? ''); ?>
                                    </td>
                                </tr>
                                <!-- ITEM HEADER -->
                                <tr class="bg_table_header">
                                    <th width="5%">S.No</th>
                                    <th width="15%">Item Code</th>
                                    <th width="40%" colspan="2">Item Description</th>
                                    <th width="10%">UOM</th>
                                    <th width="10%" class="text-center">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ITEMS -->
                                <?php if (!empty($enquiry_info['items'])): ?>
                                    <?php foreach ($enquiry_info['items'] as $k => $item): ?>
                                        <tr>
                                            <td>
                                                <?php echo $k + 1; ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($item['item_code'] ?? ''); ?>
                                            </td>
                                            <td colspan="2">
                                                <?php echo htmlspecialchars($item['item_desc'] ?? ''); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($item['uom'] ?? ''); ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo htmlspecialchars($item['qty'] ?? 0); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No items available</td>
                                    </tr>
                                <?php endif; ?>
                                <!-- spacing -->
                                <tr>
                                    <td colspan="6" style="height: 20px; border: none;">&nbsp;</td>
                                </tr>
                            </tbody>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center">No records found</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Tender Quotation List -->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border clearfix">
            <h3 class="box-title">Tender Quotation List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <?php if (!empty($tender_quotation_list)): ?>
                        <?php foreach ($tender_quotation_list as $quotation):
                            $info = $quotation['info'] ?? [];
                            ?>
                            <thead>
                                <!-- QUOTATION HEADER -->
                                <tr class="bg_top_header">
                                    <th width="12%">Company</th>
                                    <td width="15%"><?php echo htmlspecialchars($info['company_name'] ?? ''); ?></td>
                                    <th width="12%">Customer Name</th>
                                    <td width="15%"><?php echo htmlspecialchars($info['customer_name'] ?? ''); ?></td>
                                    <th width="12%">Quotation No</th>
                                    <td colspan="3"><?php echo htmlspecialchars($info['quotation_no'] ?? ''); ?></td>
                                </tr>
                                <tr class="bg_top_header">
                                    <th>Tender Reference No</th>
                                    <td><?php echo htmlspecialchars($info['tender_ref_no'] ?? ''); ?></td>
                                    <th>Quotation Date</th>
                                    <td>
                                        <?php echo !empty($info['quote_date'])
                                            ? date('d-m-Y', strtotime($info['quote_date']))
                                            : ''; ?>
                                    </td>
                                    <th>Quotation Status</th>
                                    <td colspan="3"><?php echo htmlspecialchars($info['quotation_status'] ?? ''); ?></td>
                                </tr>
                                <!-- ITEM HEADER -->
                                <tr class="bg_table_header">
                                    <th width="5%">S.No</th>
                                    <th width="12%">Item Code</th>
                                    <th width="30%">Item Description</th>
                                    <th width="8%">UOM</th>
                                    <th width="10%" class="text-right">Qty</th>
                                    <th width="12%" class="text-right">Rate</th>
                                    <th width="8%" class="text-right">VAT %</th>
                                    <th width="15%" class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ITEMS -->
                                <?php if (!empty($quotation['items'])): ?>
                                    <?php $tot = 0;  foreach ($quotation['items'] as $k => $item):
                                        $decimal = $item['decimal_point'] ?? 2; 
                                        $tot += $item['amount'] ?? 0;
                                        ?>
                                        <tr>
                                            <td><?php echo $k + 1; ?></td>
                                            <td><?php echo htmlspecialchars($item['item_code'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['item_desc'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['uom'] ?? ''); ?></td>
                                            <td class="text-right"><?php echo htmlspecialchars($item['qty'] ?? 0); ?></td>
                                            <td class="text-right">
                                                <?php echo number_format($item['rate'] ?? 0, $decimal); ?>
                                            </td>
                                            <td class="text-right"><?php echo htmlspecialchars($item['gst'] ?? 0); ?></td>
                                            <td class="text-right">
                                                <?php echo $item['currency_code'] . ' ' . number_format($item['amount'] ?? 0, $decimal); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="7" class="text-right"><strong>Total Amount:</strong></td>
                                        <td class="text-right">
                                            <?php echo $item['currency_code'] . ' ' . number_format($tot, 2); ?>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No items available</td>
                                    </tr>
                                <?php endif; ?>
                                <!-- spacing -->
                                <tr>
                                    <td colspan="8" style="height: 20px; border: none;">&nbsp;</td>
                                </tr>
                            </tbody>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center">No records found</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Tender PO List -->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border clearfix">
            <h3 class="box-title">Tender PO List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <?php if (!empty($tender_po_list)): ?>
                        <?php foreach ($tender_po_list as $po_list):
                            $info = $po_list['info'] ?? [];
                            ?>
                            <thead>
                                <!-- PO HEADER -->
                                <tr class="bg_top_header">
                                    <th width="12%">Company</th>
                                    <td width="15%"><?php echo htmlspecialchars($info['company_name'] ?? ''); ?></td>
                                    <th width="12%">Customer Name</th>
                                    <td width="15%"><?php echo htmlspecialchars($info['customer_name'] ?? ''); ?></td>
                                    <th width="12%">PO No</th>
                                    <td colspan="3"><?php echo htmlspecialchars($info['our_po_no'] ?? ''); ?></td>
                                </tr>
                                <tr class="bg_top_header">
                                    <th>Tender Enquiry No</th>
                                    <td><?php echo htmlspecialchars($info['enquiry_no'] ?? ''); ?></td>
                                    <th>PO Date</th>
                                    <td>
                                        <?php echo !empty($info['po_date'])
                                            ? date('d-m-Y', strtotime($info['po_date']))
                                            : ''; ?>
                                    </td>
                                    <th>PO Status</th>
                                    <td colspan="3"><?php echo htmlspecialchars($info['po_status'] ?? ''); ?></td>
                                </tr>
                                <!-- ITEM HEADER -->
                                <tr class="bg_table_header">
                                    <th width="5%">S.No</th>
                                    <th width="12%">Item Code</th>
                                    <th width="30%">Item Description</th>
                                    <th width="8%">UOM</th>
                                    <th width="10%" class="text-right">Qty</th>
                                    <th width="12%" class="text-right">Rate</th>
                                    <th width="8%" class="text-right">VAT %</th>
                                    <th width="15%" class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ITEMS -->
                                <?php if (!empty($po_list['items'])): ?>
                                    <?php $tot = 0; foreach ($po_list['items'] as $k => $item):
                                        $decimal = $item['decimal_point'] ?? 2;
                                        $tot += $item['amount'] ?? 0;   
                                        ?>
                                        <tr>
                                            <td><?php echo $k + 1; ?></td>
                                            <td><?php echo htmlspecialchars($item['item_code'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['item_desc'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['uom'] ?? ''); ?></td>
                                            <td class="text-right"><?php echo htmlspecialchars($item['qty'] ?? 0); ?></td>
                                            <td class="text-right">
                                                <?php echo number_format($item['rate'] ?? 0, $decimal); ?>
                                            </td>
                                            <td class="text-right"><?php echo htmlspecialchars($item['gst'] ?? 0); ?></td>
                                            <td class="text-right">
                                                <?php echo  $item['currency_code'] . ' ' . number_format($item['amount'] ?? 0, $decimal); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="7" class="text-right"><strong>Total Amount:</strong></td>
                                        <td class="text-right">
                                            <?php echo $item['currency_code'] . ' ' . number_format($tot, 2); ?>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No items available</td>
                                    </tr>
                                <?php endif; ?>
                                <!-- spacing -->
                                <tr>
                                    <td colspan="8" style="height: 20px; border: none;">&nbsp;</td>
                                </tr>
                            </tbody>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center">No records found</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Tender DC List -->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border clearfix">
            <h3 class="box-title">Tender DC List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <?php if (!empty($tender_dc_list)): ?>
                        <?php foreach ($tender_dc_list as $dc_list):
                            $info = $dc_list['info'] ?? [];
                            ?>
                            <thead>
                                <!-- DC HEADER -->
                                <tr class="bg_top_header">
                                    <th width="12%">Company</th>
                                    <td width="21%"><?php echo htmlspecialchars($info['company_name'] ?? ''); ?></td>
                                    <th width="12%">Customer Name</th>
                                    <td width="21%"><?php echo htmlspecialchars($info['customer_name'] ?? ''); ?></td>
                                    <th width="12%">DC No</th>
                                    <td width="22%"><?php echo htmlspecialchars($info['dc_no'] ?? ''); ?></td>
                                </tr>
                                <tr class="bg_top_header">
                                    <th>Tender Enquiry No</th>
                                    <td><?php echo htmlspecialchars($info['enquiry_no'] ?? ''); ?></td>
                                    <th>DC Date</th>
                                    <td>
                                        <?php echo !empty($info['dc_date'])
                                            ? date('d-m-Y', strtotime($info['dc_date']))
                                            : ''; ?>
                                    </td>
                                    <th>DC Status</th>
                                    <td><?php echo htmlspecialchars($info['dc_status'] ?? ''); ?></td>
                                </tr>
                                <!-- ITEM HEADER -->
                                <tr class="bg_table_header">
                                    <th width="8%" class="text-center">S.No</th>
                                    <th width="15%">Item Code</th>
                                    <th width="40%">Item Description</th>
                                    <th width="12%" class="text-center">UOM</th>
                                    <th width="12%" class="text-right" colspan="2">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ITEMS -->
                                <?php if (!empty($dc_list['items'])): ?>
                                    <?php foreach ($dc_list['items'] as $k => $item): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $k + 1; ?></td>
                                            <td><?php echo htmlspecialchars($item['item_code'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['item_desc'] ?? ''); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($item['uom'] ?? ''); ?></td>
                                            <td class="text-right" colspan="2"><?php echo number_format($item['qty'] ?? 0, 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No items available</td>
                                    </tr>
                                <?php endif; ?>
                                <!-- spacing -->
                                <tr>
                                    <td colspan="6" style="height: 20px; border: none;">&nbsp;</td>
                                </tr>
                            </tbody>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center">No records found</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Tender Invoice List -->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border clearfix">
            <h3 class="box-title">Tender Invoice List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <?php if (!empty($tender_invoice_list)): ?>
                        <?php foreach ($tender_invoice_list as $invoice_list):
                            $info = $invoice_list['info'] ?? [];
                            ?>
                            <thead>
                                <!-- PO HEADER -->
                                <tr class="bg_top_header">
                                    <th width="12%">Company</th>
                                    <td width="15%">
                                        <?php echo htmlspecialchars($info['company_name'] ?? ''); ?>
                                    </td>
                                    <th width="12%">Customer Name</th>
                                    <td width="15%">
                                        <?php echo htmlspecialchars($info['customer_name'] ?? ''); ?>
                                    </td>
                                    <th width="12%">Invoice No</th>
                                    <td colspan="3">
                                        <?php echo htmlspecialchars($info['invoice_no'] ?? ''); ?>
                                    </td>
                                </tr>
                                <tr class="bg_top_header">
                                    <th>Tender Enquiry No</th>
                                    <td>
                                        <?php echo htmlspecialchars($info['enquiry_no'] ?? ''); ?>
                                    </td>
                                    <th>Invoice Date</th>
                                    <td>
                                        <?php echo !empty($info['invoice_date'])
                                            ? date('d-m-Y', strtotime($info['invoice_date']))
                                            : ''; ?>
                                    </td>
                                    <th>Invoice Status</th>
                                    <td colspan="3">
                                        <?php echo htmlspecialchars($info['invoice_status'] ?? ''); ?>
                                    </td>
                                </tr>
                                <!-- ITEM HEADER -->
                                <tr class="bg_table_header">
                                    <th width="5%">S.No</th>
                                    <th width="12%">Item Code</th>
                                    <th width="30%">Item Description</th>
                                    <th width="8%">UOM</th>
                                    <th width="10%" class="text-right">Qty</th>
                                    <th width="12%" class="text-right">Rate</th>
                                    <th width="8%" class="text-right">VAT %</th>
                                    <th width="15%" class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ITEMS -->
                                <?php if (!empty($invoice_list['items'])): ?>
                                    <?php $tot = 0; foreach ($invoice_list['items'] as $k => $item):
                                        $decimal = $item['decimal_point'] ?? 2;
                                        $tot += $item['amount'] ?? 0;   
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $k + 1; ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($item['item_code'] ?? ''); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($item['item_desc'] ?? ''); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($item['uom'] ?? ''); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo htmlspecialchars($item['qty'] ?? 0); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo number_format($item['rate'] ?? 0, $decimal); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo htmlspecialchars($item['gst'] ?? 0); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo $item['currency_code'] . ' ' . number_format($item['amount'] ?? 0, $decimal); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="7" class="text-right"><strong>Total Amount:</strong></td>
                                        <td class="text-right">
                                            <?php echo $item['currency_code'] . ' ' . number_format($tot, 2); ?>    
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No items available</td>
                                    </tr>
                                <?php endif; ?>
                                <!-- spacing -->
                                <tr>
                                    <td colspan="8" style="height: 20px; border: none;">&nbsp;</td>
                                </tr>
                            </tbody>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center">No records found</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>




    <!-- Vendor rate enquiry List -->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border clearfix">
            <h3 class="box-title">Vendor Rate Enquiry List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <?php if (!empty($vendor_rate_enquiry_list)): ?>
                        <?php foreach ($vendor_rate_enquiry_list as $vendor_rate_enquiry):
                            $info = $vendor_rate_enquiry['info'] ?? [];
                            ?>
                            <thead>
                                <!-- QUOTATION HEADER -->
                                <tr class="bg_top_header">
                                    <th width="12%">Company</th>
                                    <td width="15%">
                                        <?php echo htmlspecialchars($info['company_name'] ?? ''); ?>
                                    </td>
                                    <th width="12%">Customer Name</th>
                                    <td width="15%">
                                        <?php echo htmlspecialchars($info['customer_name'] ?? ''); ?>
                                    </td>
                                    <th width="12%">Vendor Rate Enquiry No</th>
                                    <td colspan="3">
                                        <?php echo htmlspecialchars($info['vendor_rate_enquiry_no'] ?? ''); ?>
                                    </td>
                                </tr>
                                <tr class="bg_top_header">
                                    <th>Tender Enquiry No</th>
                                    <td>
                                        <?php echo htmlspecialchars($info['enquiry_no'] ?? ''); ?>
                                    </td>
                                    <th>Enquiry Date</th>
                                    <td>
                                        <?php echo !empty($info['enquiry_date'])
                                            ? date('d-m-Y', strtotime($info['enquiry_date']))
                                            : ''; ?>
                                    </td>
                                    <th>Enquiry Status</th>
                                    <td colspan="3">
                                        <?php echo htmlspecialchars($info['vendor_rate_enquiry_status'] ?? ''); ?>
                                    </td>
                                </tr>
                                <!-- ITEM HEADER -->
                                <tr class="bg_table_header">
                                    <th width="5%">S.No</th>
                                    <th width="12%">Item Code</th>
                                    <th width="30%">Item Description</th>
                                    <th width="8%">UOM</th>
                                    <th width="10%" class="text-right">Qty</th>
                                    <th width="12%" class="text-right">Rate</th>
                                    <th width="8%" class="text-right">VAT %</th>
                                    <th width="15%" class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ITEMS -->
                                <?php if (!empty($vendor_rate_enquiry['items'])): ?>
                                    <?php $tot = 0; foreach ($vendor_rate_enquiry['items'] as $k => $item):
                                        $decimal = $item['decimal_point'] ?? 2;
                                        $tot += $item['amount'] ?? 0;
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $k + 1; ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($item['item_code'] ?? ''); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($item['item_desc'] ?? ''); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($item['uom'] ?? ''); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo htmlspecialchars($item['qty'] ?? 0); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo number_format($item['rate'] ?? 0, $decimal); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo htmlspecialchars($item['gst'] ?? 0); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo $item['currency_code'] ?? '' . ' ' . number_format($item['amount'] ?? 0, $decimal); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="7" class="text-right"><strong>Total Amount:</strong></td>
                                        <td class="text-right"><?php echo $item['currency_code'] ?? '' . ' ' . number_format($tot, 2); ?></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No items available</td>
                                    </tr>
                                <?php endif; ?>
                                <!-- spacing -->
                                <tr>
                                    <td colspan="8" style="height: 20px; border: none;">&nbsp;</td>
                                </tr>
                            </tbody>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center">No records found</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Vendor Quotation List -->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border clearfix">
            <h3 class="box-title">Vendor Quotation List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <?php if (!empty($vendor_quotation_list)): ?>
                        <?php foreach ($vendor_quotation_list as $vendor_quotation):
                            $info = $vendor_quotation['info'] ?? [];
                            ?>
                            <thead>
                                <!-- QUOTATION HEADER -->
                                <tr class="bg_top_header">
                                    <th width="12%">Company</th>
                                    <td width="15%"><?php echo htmlspecialchars($info['company_name'] ?? ''); ?></td>
                                    <th width="12%">Customer Name</th>
                                    <td width="15%"><?php echo htmlspecialchars($info['customer_name'] ?? ''); ?></td>
                                    <th width="12%">Quotation No</th>
                                    <td colspan="3"><?php echo htmlspecialchars($info['quote_no'] ?? ''); ?></td>
                                </tr>
                                <tr class="bg_top_header">
                                    <th>Tender Enquiry No</th>
                                    <td><?php echo htmlspecialchars($info['enquiry_no'] ?? ''); ?></td>
                                    <th>Quotation Date</th>
                                    <td>
                                        <?php echo !empty($info['quote_date'])
                                            ? date('d-m-Y', strtotime($info['quote_date']))
                                            : ''; ?>
                                    </td>
                                    <th>Quotation Status</th>
                                    <td colspan="3"><?php echo htmlspecialchars($info['quote_status'] ?? ''); ?></td>
                                </tr>
                                <!-- ITEM HEADER -->
                                <tr class="bg_table_header">
                                    <th width="5%">S.No</th>
                                    <th width="12%">Item Code</th>
                                    <th width="30%">Item Description</th>
                                    <th width="8%">UOM</th>
                                    <th width="10%" class="text-right">Qty</th>
                                    <th width="12%" class="text-right">Rate</th>
                                    <th width="8%" class="text-right">VAT %</th>
                                    <th width="15%" class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ITEMS -->
                                <?php if (!empty($vendor_quotation['items'])): ?>
                                    <?php $tot=0; foreach ($vendor_quotation['items'] as $k => $item):
                                        $decimal = $item['decimal_point'] ?? 2;
                                        $tot += $item['amount'] ?? 0;   
                                        ?>
                                        <tr>
                                            <td><?php echo $k + 1; ?></td>
                                            <td><?php echo htmlspecialchars($item['item_code'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['item_desc'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['uom'] ?? ''); ?></td>
                                            <td class="text-right"><?php echo htmlspecialchars($item['qty'] ?? 0); ?></td>
                                            <td class="text-right">
                                                <?php echo number_format($item['rate'] ?? 0, $decimal); ?>
                                            </td>
                                            <td class="text-right"><?php echo htmlspecialchars($item['gst'] ?? 0); ?></td>
                                            <td class="text-right">
                                                <?php echo $item['currency_code'] . ' ' . number_format($item['amount'] ?? 0, $decimal); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="7" class="text-right"><strong>Total Amount:</strong></td>
                                        <td class="text-right"><?php echo $item['currency_code'] . ' ' . number_format($tot, 2); ?></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No items available</td>
                                    </tr>
                                <?php endif; ?>
                                <!-- spacing -->
                                <tr>
                                    <td colspan="8" style="height: 20px; border: none;">&nbsp;</td>
                                </tr>
                            </tbody>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center">No records found</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Vendor Po List -->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border clearfix">
            <h3 class="box-title">Vendor Po List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <?php if (!empty($vendor_po_list)): ?>
                        <?php foreach ($vendor_po_list as $po_list):
                            $info = $po_list['info'] ?? [];
                            ?>
                            <thead>
                                <!-- TOP HEADER -->
                                <tr class="bg_top_header">
                                    <th width="12%">Company</th>
                                    <td width="15%"><?php echo htmlspecialchars($info['company_name'] ?? ''); ?></td>
                                    <th width="12%">Customer Name</th>
                                    <td width="15%"><?php echo htmlspecialchars($info['customer_name'] ?? ''); ?></td>
                                    <th width="12%">Po No</th>
                                    <td colspan="3"><?php echo htmlspecialchars($info['po_no'] ?? ''); ?></td>
                                </tr>
                                <tr class="bg_top_header">
                                    <th>Tender Enquiry No</th>
                                    <td><?php echo htmlspecialchars($info['enquiry_no'] ?? ''); ?></td>
                                    <th>Po Date</th>
                                    <td>
                                        <?php echo !empty($info['po_date'])
                                            ? date('d-m-Y', strtotime($info['po_date']))
                                            : ''; ?>
                                    </td>
                                    <th>Po Status</th>
                                    <td colspan="3"><?php echo htmlspecialchars($info['po_status'] ?? ''); ?></td>
                                </tr>
                                <!-- ITEM HEADER -->
                                <tr class="bg_table_header">
                                    <th width="5%">S.No</th>
                                    <th width="12%">Item Code</th>
                                    <th width="30%">Item Description</th>
                                    <th width="8%">UOM</th>
                                    <th width="10%" class="text-right">Qty</th>
                                    <th width="12%" class="text-right">Rate</th>
                                    <th width="8%" class="text-right">VAT %</th>
                                    <th width="15%" class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ITEMS -->
                                <?php if (!empty($po_list['items'])): ?>
                                    <?php $tot=0; foreach ($po_list['items'] as $k => $item):
                                        $decimal = $item['decimal_point'] ?? 2;
                                        $tot += $item['amount'] ?? 0;
                                        ?>
                                        <tr>
                                            <td><?php echo $k + 1; ?></td>
                                            <td><?php echo htmlspecialchars($item['item_code'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['item_desc'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['uom'] ?? ''); ?></td>
                                            <td class="text-right"><?php echo htmlspecialchars($item['qty'] ?? 0); ?></td>
                                            <td class="text-right">
                                                <?php echo number_format($item['rate'] ?? 0, $decimal); ?>
                                            </td>
                                            <td class="text-right"><?php echo htmlspecialchars($item['gst'] ?? 0); ?></td>
                                            <td class="text-right">
                                                <?php echo $item['currency_code'] . ' ' . number_format($item['amount'] ?? 0, $decimal); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="7" class="text-right"><strong>Total Amount:</strong></td>
                                        <td class="text-right"><?php echo $item['currency_code'] . ' ' . number_format($tot, 2); ?></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No items available</td>
                                    </tr>
                                <?php endif; ?>
                                <!-- spacing -->
                                <tr>
                                    <td colspan="8" style="height: 20px; border: none;">&nbsp;</td>
                                </tr>
                            </tbody>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center">No records found</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>


    <!-- Vendor Purchase Inward List -->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border clearfix">
            <h3 class="box-title">Vendor Purchase Inward List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <?php if (!empty($vendor_pur_inward_list)): ?>
                        <?php foreach ($vendor_pur_inward_list as $pur_inward):
                            $info = $pur_inward['info'] ?? [];
                            ?>
                            <thead>
                                <!-- ENQUIRY HEADER -->
                                <tr class="bg_top_header">
                                    <th width="15%">Company</th>
                                    <td width="18%">
                                        <?php echo htmlspecialchars($info['company_name'] ?? ''); ?>
                                    </td>
                                    <th width="15%">Customer Name</th>
                                    <td width="18%">
                                        <?php echo htmlspecialchars($info['customer_name'] ?? ''); ?>
                                    </td>
                                    <th width="15%">Inward No</th>
                                    <td width="19%">
                                        <?php echo htmlspecialchars($info['inward_no'] ?? ''); ?>
                                    </td>
                                </tr>
                                <tr class="bg_top_header">
                                    <th>Tender Name</th>
                                    <td>
                                        <?php echo htmlspecialchars($info['tender_name'] ?? ''); ?>
                                    </td>
                                    <th>Inward Date</th>
                                    <td>
                                        <?php echo !empty($info['inward_date'])
                                            ? date('d-m-Y', strtotime($info['inward_date']))
                                            : ''; ?>
                                    </td>
                                    <th>Inward Status</th>
                                    <td>
                                        <?php echo htmlspecialchars($info['inward_status'] ?? ''); ?>
                                    </td>
                                </tr>
                                <!-- ITEM HEADER -->
                                <tr class="bg_table_header">
                                    <th width="5%">S.No</th>
                                    <th width="15%">Item Code</th>
                                    <th width="40%" colspan="2">Item Description</th>
                                    <th width="10%">UOM</th>
                                    <th width="10%" class="text-center">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ITEMS -->
                                <?php if (!empty($pur_inward['items'])): ?>
                                    <?php foreach ($pur_inward['items'] as $k => $item): ?>
                                        <tr>
                                            <td>
                                                <?php echo $k + 1; ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($item['item_code'] ?? ''); ?>
                                            </td>
                                            <td colspan="2">
                                                <?php echo htmlspecialchars($item['item_desc'] ?? ''); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($item['uom'] ?? ''); ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo htmlspecialchars($item['qty'] ?? 0); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No items available</td>
                                    </tr>
                                <?php endif; ?>
                                <!-- spacing -->
                                <tr>
                                    <td colspan="6" style="height: 20px; border: none;">&nbsp;</td>
                                </tr>
                            </tbody>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center">No records found</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Vendor Purchase Invoice List -->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border clearfix">
            <h3 class="box-title">Vendor Purchase Invoice List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <?php if (!empty($vendor_invoice_list)): ?>
                        <?php foreach ($vendor_invoice_list as $vendor_invoice):
                            $info = $vendor_invoice['info'] ?? [];
                            ?>
                            <thead>
                                <!-- TOP HEADER -->
                                <tr class="bg_top_header">
                                    <th width="12%">Company</th>
                                    <td width="15%"><?php echo htmlspecialchars($info['company_name'] ?? ''); ?></td>
                                    <th width="12%">Customer Name</th>
                                    <td width="15%"><?php echo htmlspecialchars($info['customer_name'] ?? ''); ?></td>
                                    <th width="12%">Invoice No</th>
                                    <td colspan="3"><?php echo htmlspecialchars($info['invoice_no'] ?? ''); ?></td>
                                </tr>
                                <tr class="bg_top_header">
                                    <th>Tender Enquiry No</th>
                                    <td><?php echo htmlspecialchars($info['enquiry_no'] ?? ''); ?></td>
                                    <th>Invoice Date</th>
                                    <td>
                                        <?php echo !empty($info['invoice_date'])
                                            ? date('d-m-Y', strtotime($info['invoice_date']))
                                            : ''; ?>
                                    </td>
                                    <th>Invoice Status</th>
                                    <td colspan="3"><?php echo htmlspecialchars($info['invoice_status'] ?? ''); ?></td>
                                </tr>
                                <!-- ITEM HEADER -->
                                <tr class="bg_table_header">
                                    <th width="5%">S.No</th>
                                    <th width="12%">Item Code</th>
                                    <th width="30%">Item Description</th>
                                    <th width="8%">UOM</th>
                                    <th width="10%" class="text-right">Qty</th>
                                    <th width="12%" class="text-right">Rate</th>
                                    <th width="8%" class="text-right">VAT %</th>
                                    <th width="15%" class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ITEMS -->
                                <?php if (!empty($vendor_invoice['items'])): ?>
                                    <?php $tot = 0; foreach ($vendor_invoice['items'] as $k => $item):
                                        $decimal = $item['decimal_point'] ?? 2;
                                        $tot += $item['amount'] ?? 0;   
                                        ?>
                                        <tr>
                                            <td><?php echo $k + 1; ?></td>
                                            <td><?php echo htmlspecialchars($item['item_code'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['item_desc'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['uom'] ?? ''); ?></td>
                                            <td class="text-right"><?php echo htmlspecialchars($item['qty'] ?? 0); ?></td>
                                            <td class="text-right">
                                                <?php echo number_format($item['rate'] ?? 0, $decimal); ?>
                                            </td>
                                            <td class="text-right"><?php echo htmlspecialchars($item['gst'] ?? 0); ?></td>
                                            <td class="text-right">
                                                <?php echo $item['currency_code'] . ' ' . number_format($item['amount'] ?? 0, $decimal); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="7" class="text-right"><strong>Total Amount:</strong></td>
                                        <td class="text-right"><?php echo $item['currency_code'] . ' ' . number_format($tot, 2); ?></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No items available</td>
                                    </tr>
                                <?php endif; ?>
                                <!-- spacing -->
                                <tr>
                                    <td colspan="8" style="height: 20px; border: none;">&nbsp;</td>
                                </tr>
                            </tbody>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center">No records found</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>


<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    jQuery(function ($) {
        $("#srch_enquiry_no_id").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url('reports/tender_enquiry_id_search'); ?>",
                    type: "POST",
                    data: { search: request.term },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        response(data);
                    },
                    error: function (xhr, status, error) {
                        console.error("Autocomplete error:", error);
                    }
                });
            },
            minLength: 1,
            select: function (event, ui) { 
                $("#tender_enquiry_id").val(ui.item.tender_enquiry_id);
            }
        });
    });
</script>
<script>
    $(document).ready(function () {

        $("#chk_open_all").on("change", function () {

            if ($(this).is(":checked")) {

                // Open all boxes
                $(".box").each(function () {
                    if ($(this).hasClass("collapsed-box")) {
                        $(this).find('[data-widget="collapse"]').click();
                    }
                });

            } else {

                // Close all boxes
                $(".box").each(function () {
                    if (!$(this).hasClass("collapsed-box")) {
                        $(this).find('[data-widget="collapse"]').click();
                    }
                });

            }

        });

    });
</script>