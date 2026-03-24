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
    <div class="box box-info no-print" id="search_filter_box">
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
                            <input type="checkbox" id="chk_open_all" name="chk_open_all" value="1" <?php
                            if (($this->input->post('chk_open_all') != '') && $this->input->post('chk_open_all') == 1) {
                                echo 'checked';
                            }

                            ?>> Open All Sections
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
                                    <th>Company</th>
                                    <td colspan="2">
                                        <?php echo htmlspecialchars($info['company_name'] ?? ''); ?>
                                    </td>
                                    <th>Customer Name</th>
                                    <td colspan="2">
                                        <?php echo htmlspecialchars($info['customer_name'] ?? ''); ?>
                                    </td>
                                    <th>Enquiry No</th>
                                    <td width="20%  ">
                                        <?php echo htmlspecialchars($info['enquiry_no'] ?? ''); ?>
                                    </td>
                                </tr>
                                <tr class="bg_top_header">
                                    <th width="15%"> Opening Date</th>
                                    <td>
                                        <?php echo date('d-m-Y', strtotime($info['opening_date'])); ?>
                                    </td>
                                    <th width="10%"> Closing Date</th>
                                    <td>
                                        <?php echo date('d-m-Y', strtotime($info['closing_date'])); ?>
                                    </td>
                                    <th>Enquiry Date</th>
                                    <td>
                                        <?php echo !empty($info['enquiry_date']) ? date('d-m-Y', strtotime($info['enquiry_date'])) : ''; ?>
                                    </td>
                                    <th width="10%">Tender Status</th>
                                    <td width="10%">
                                        <?php echo htmlspecialchars($info['tender_status'] ?? ''); ?>
                                    </td>
                                </tr>
                                <!-- ITEM HEADER -->
                                <tr class="bg_table_header">
                                    <th width="5%">Serial No</th>
                                    <th width="5%">Tender Enq ID</th>
                                    <th width="5%">Tender Enq Item ID</th>
                                    <th width="15%">Item Code</th>
                                    <th width="40%" colspan="2">Item Description</th>
                                    <th width="10%">UOM</th>
                                    <th width="10%" class="text-center">Qty</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php if (!empty($enquiry_info['items'])): ?>
                                    <?php
                                    $tot = 0;
                                    foreach ($enquiry_info['items'] as $k => $item):
                                        $tot += $item['qty'] ?? 0;
                                        ?>
                                        <tr>
                                            <td><?php echo $item['serial_no'] ?? ''; ?></td>

                                            <td class="text-center">
                                                <?= $item['tender_enquiry_id'] ?? ''; ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($item['tender_enquiry_item_id'] ?? ''); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($item['item_code'] ?? ''); ?>
                                            </td>

                                            <td colspan="2">
                                                <?= htmlspecialchars($item['item_desc'] ?? ''); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($item['uom'] ?? ''); ?>
                                            </td>

                                            <td class="text-center">
                                                <?= number_format($item['qty'] ?? 0, 2); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <!-- TOTAL ROW -->
                                    <tr>
                                        <td colspan="7" class="text-right">
                                            <strong>Total Qty :</strong>
                                        </td>
                                        <td class="text-center">
                                            <strong><?= number_format($tot, 2); ?></strong>
                                        </td>
                                    </tr>

                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            No items available
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <!-- SPACING ROW -->
                                <tr>
                                    <td colspan="8" style="height:20px;border:none;">&nbsp;</td>
                                </tr>

                            </tbody>
                        <?php endforeach; ?>


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
                                    <th colspan="2">Quotation No</th>
                                    <td><?php echo htmlspecialchars($info['quotation_no'] ?? ''); ?></td>
                                    <th colspan="2">Tender Reference No</th>
                                    <td><?php echo htmlspecialchars($info['tender_ref_no'] ?? ''); ?></td>
                                    <th colspan="1">Transport Charges</th>
                                    <td><?php echo htmlspecialchars($info['transport_charges'] ?? ''); ?></td>
                                    <th colspan="2">Currency Code</th>
                                    <td colspan="2"><?php echo htmlspecialchars($info['currency_code'] ?? ''); ?></td>
                                </tr>
                                <tr class="bg_top_header">
                                    <th colspan="2">Quotation Date</th>
                                    <td>
                                        <?php echo !empty($info['quote_date'])
                                            ? date('d-m-Y', strtotime($info['quote_date']))
                                            : ''; ?>
                                    </td>
                                    <th colspan="2">Quotation Status</th>
                                    <td><?php echo htmlspecialchars($info['quotation_status'] ?? ''); ?></td>
                                    <th>Other Charges</th>
                                    <td><?php echo htmlspecialchars($info['other_charges'] ?? ''); ?></td>
                                    <th>Tender Enquiry Id</th>
                                    <td colspan="3"><?php echo $info['tender_enquiry_id']; ?></td>
                                </tr>
                                <!-- ITEM HEADER -->
                                <tr class="bg_table_header">
                                    <th width="5%">Serial No</th>
                                    <th width="5%">Tender Quotation Id</th>
                                    <th width="5%">Tender Quotation Item Id</th>
                                    <th width="5%">Tender Enquiry Item Id</th>
                                    <th width="8%">Item Code</th>
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
                                    <?php $tot = 0;
                                    foreach ($quotation['items'] as $k => $item):
                                        $decimal = $item['decimal_point'] ?? 2;
                                        $tot += $item['amount'] ?? 0;
                                        ?>
                                        <tr>
                                            <td><?php echo $item['serial_no'] ?? ''; ?></td>
                                            <td class="text-center">
                                                <?php echo $item['tender_quotation_id'] ?? ''; ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($item['tender_quotation_item_id'] ?? ''); ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $item['tender_enquiry_item_id'] ?? ''; ?>
                                            </td>
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
                                        <td class="text-right" colspan="3">
                                            <?php echo $item['currency_code'] . ' ' . number_format($tot, 3); ?>
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
        <style>
            .bg_table_header th {
                white-space: nowrap;
            }
        </style>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped w-100">
                    <?php if (!empty($tender_po_list)): ?>
                        <?php foreach ($tender_po_list as $po_list):
                            $info = $po_list['info'] ?? [];
                            ?>
                            <thead>

                                <!-- ================= PO HEADER ================= -->
                                <tr class="bg_top_header">
                                    <th colspan="2">Customer PO No</th>
                                    <td colspan="3"><?= htmlspecialchars($info['customer_po_no'] ?? ''); ?></td>

                                    <th colspan="2">Our PO No</th>
                                    <td colspan="2"><?= htmlspecialchars($info['our_po_no'] ?? ''); ?></td>

                                    <th>Delivery Date</th>
                                    <td>
                                        <?= !empty($info['delivery_date'])
                                            ? date('d-m-Y', strtotime($info['delivery_date']))
                                            : ''; ?>
                                    </td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th colspan="2">PO Date</th>
                                    <td colspan="3">
                                        <?= !empty($info['po_date'])
                                            ? date('d-m-Y', strtotime($info['po_date']))
                                            : ''; ?>
                                    </td>

                                    <th colspan="2">PO Received Date</th>
                                    <td colspan="2">
                                        <?= !empty($info['po_received_date'])
                                            ? date('d-m-Y', strtotime($info['po_received_date']))
                                            : ''; ?>
                                    </td>

                                    <th>PO Status</th>
                                    <td><?= htmlspecialchars($info['po_status'] ?? ''); ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th colspan="2">Tender Enquiry Id</th>
                                    <td colspan="3">
                                        <?= htmlspecialchars($info['tender_enquiry_id'] ?? ''); ?>
                                    </td>

                                    <th colspan="2">Tender Quotation Id</th>
                                    <td colspan="4">
                                        <?= htmlspecialchars($info['tender_quotation_id'] ?? ''); ?>
                                    </td>
                                </tr>

                                <!-- ================= ITEM HEADER ================= -->
                                <tr class="bg_table_header">
                                    <th width="4%">Serial No</th>
                                    <th width="6%">Tender PO Id</th>
                                    <th width="8%">Tender PO Item Id</th>
                                    <th width="10%">Tender Quotation Item Id</th>
                                    <th width="10%">Item Code</th>
                                    <th width="26%">Item Description</th>
                                    <th width="6%">UOM</th>
                                    <th width="8%" class="text-right">Qty</th>
                                    <th width="8%" class="text-right">Rate</th>
                                    <th width="6%" class="text-right">VAT %</th>
                                    <th width="8%" class="text-right">Amount</th>
                                </tr>

                            </thead>
                            <tbody>
                                <!-- ITEMS -->
                                <?php if (!empty($po_list['items'])): ?>
                                    <?php $tot = 0;
                                    foreach ($po_list['items'] as $k => $item):
                                        $decimal = $item['decimal_point'] ?? 2;
                                        $tot += $item['amount'] ?? 0;
                                        ?>
                                        <tr>
                                            <td><?php echo $item['serial_no'] ?? ''; ?></td>
                                            <td class="text-center">
                                                <?php echo $item['tender_po_id'] ?? ''; ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($item['tender_po_item_id'] ?? ''); ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $item['tender_quotation_item_id'] ?? ''; ?>
                                            </td>
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
                                        <td colspan="9" class="text-right"><strong>Total Amount:</strong></td>
                                        <td class="text-right" colspan="4">
                                            <?php echo $item['currency_code'] . ' ' . number_format($tot, $decimal); ?>
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

                                <!-- ================= PO HEADER ================= -->
                                <tr class="bg_top_header">
                                    <th colspan="2">Customer Name</th>
                                    <td colspan="2"><?= htmlspecialchars($info['customer_name'] ?? ''); ?></td>

                                    <th colspan="2">DC No</th>
                                    <td colspan="2"><?= htmlspecialchars($info['dc_no'] ?? ''); ?></td>

                                    <th>Tender Enquiry Id</th>
                                    <td><?= htmlspecialchars($info['tender_enquiry_id'] ?? ''); ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th colspan="2">DC Date</th>
                                    <td colspan="2">
                                        <?= !empty($info['dc_date'])
                                            ? date('d-m-Y', strtotime($info['dc_date']))
                                            : ''; ?>
                                    </td>

                                    <th colspan="2">DC Status</th>
                                    <td colspan="2"><?= htmlspecialchars($info['dc_status'] ?? ''); ?></td>

                                    <th>Tender PO Id</th>
                                    <td><?= htmlspecialchars($info['tender_po_id'] ?? ''); ?></td>
                                </tr>

                                <!-- ================= ITEM HEADER ================= -->
                                <tr class="bg_table_header">
                                    <th width="6%" class="text-center">Serial No</th>
                                    <th width="8%">Tender Dc Id</th>
                                    <th width="8%">Tender Dc Item Id</th>
                                    <th width="10%">Vendor Purchase Inward Id</th>
                                    <th width="10%">Vendor Purchase Inward Item Id</th>
                                    <th width="12%">Item Code</th>
                                    <th width="28%">Item Description</th>
                                    <th width="8%" class="text-center">UOM</th>
                                    <th width="10%" colspan="2" class="text-right">Qty</th>
                                </tr>

                            </thead>
                            <tbody>
                                <!-- ITEMS -->
                                <?php if (!empty($dc_list['items'])): ?>
                                    <?php foreach ($dc_list['items'] as $k => $item): ?>
                                        <tr>
                                            <td class="text-center"><?php echo  $item['serial_no'] ?? ''; ?></td>
                                            <td class="text-center">
                                                <?php echo $item['tender_dc_id'] ?? ''; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $item['tender_dc_item_id'] ?? ''; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $item['vendor_pur_inward_id'] ?? ''; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $item['vendor_pur_inward_item_id'] ?? ''; ?>
                                            </td>
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

                                <tr class="bg_top_header">
                                    <th width="12%">Company</th>
                                    <td width="15%">
                                        <?php echo htmlspecialchars($info['company_name'] ?? ''); ?>
                                    </td>
                                    <th width="12%">Customer Name</th>
                                    <td width="15%">
                                        <?php echo htmlspecialchars($info['customer_name'] ?? ''); ?>
                                    </td>
                                    <th colspan="3">Invoice No</th>
                                    <td colspan="4">
                                        <?php echo htmlspecialchars($info['invoice_no'] ?? ''); ?>
                                    </td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th>Tender Invoice Amount</th>
                                    <td>
                                        <?php echo htmlspecialchars($info['total_amount'] ?? ''); ?>
                                    </td>
                                    <th colspan="3">Invoice Date</th>
                                    <td>
                                        <?php echo !empty($info['invoice_date'])
                                            ? date('d-m-Y', strtotime($info['invoice_date']))
                                            : ''; ?>
                                    </td>
                                    <th colspan="3">Invoice Status</th>
                                    <td colspan="3">
                                        <?php echo htmlspecialchars($info['invoice_status'] ?? ''); ?>
                                    </td>
                                </tr>



                                <tr class="bg_table_header">
                                    <th width="5%">Serial No</th>
                                    <th width="5%">Tender Enquiry Invoice Id</th>
                                    <th width="5%">Tender Enquiry Invoice Item Id</th>
                                    <th width="5%">Tender PO Item Id</th>
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
                                    <?php $tot = 0;
                                    foreach ($invoice_list['items'] as $k => $item):
                                        $decimal = $item['decimal_point'] ?? 2;
                                        $tot += $item['amount'] ?? 0;
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $item['serial_no'] ?? ''; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $item['tender_enq_invoice_id'] ?? ''; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $item['tender_enq_invoice_item_id'] ?? ''; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $item['tender_po_item_id'] ?? ''; ?>
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
                                        <td colspan="9" class="text-right"><strong>Total Amount:</strong></td>
                                        <td colspan="2" class="text-right">
                                            <?php echo $item['currency_code'] . ' ' . number_format($tot, 3); ?>
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





    <!-- Tender Invoice Receipt List -->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border clearfix">
            <h3 class="box-title">Tender Invoice Receipt List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">

                    <?php if (!empty($tender_receipt_invoice_list)): ?>

                        <?php foreach ($tender_receipt_invoice_list as $receipt_invoice_list):
                            $info = $receipt_invoice_list['info'] ?? [];
                            $items = $receipt_invoice_list['items'] ?? [];
                            ?>

                            <thead>
                                <tr class="bg_top_header">
                                    <th>Receipt Mode</th>
                                    <td><?= htmlspecialchars($info['receipt_mode'] ?? '') ?></td>

                                    <th>Customer Name</th>
                                    <td><?= htmlspecialchars($info['customer_name'] ?? '') ?></td>

                                    <th>Receipt No</th>
                                    <td><?= htmlspecialchars($info['receipt_no'] ?? '') ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th>Total Amount</th>
                                    <td><?= htmlspecialchars($info['amount'] ?? '') ?></td>

                                    <th>Receipt Date</th>
                                    <td><?= $info['receipt_date'] ?? '' ?></td>

                                    <th>Receipt Type</th>
                                    <td><?= htmlspecialchars($info['receipt_type'] ?? '') ?></td>
                                </tr>

                                <tr class="bg_table_header">
                                    <th>S.No</th>
                                    <th>Invoice ID</th>
                                    <th>Enquiry ID</th>
                                    <th>Receipt ID</th>
                                    <th class="text-right" colspan="2">Amount</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (!empty($items)): ?>
                                    <?php $tot = 0; ?>

                                    <?php foreach ($items as $k => $item):
                                        $amount = $item['inv_amount'] ?? 0;
                                        $tot += $amount;
                                        ?>
                                        <tr>
                                            <td><?= $k + 1 ?></td>
                                            <td class="text-center"><?= $item['tender_enq_invoice_id'] ?></td>
                                            <td class="text-center"><?= $item['tender_enquiry_id'] ?></td>
                                            <td class="text-center"><?= $item['tender_receipt_id'] ?></td>
                                            <td colspan="2" class="text-right"><?= number_format($amount, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <!-- TOTAL -->
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Total</strong></td>
                                        <td class="text-right">
                                            <strong><?= number_format($tot, 2) ?></strong>
                                        </td>
                                    </tr>

                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No items available</td>
                                    </tr>
                                <?php endif; ?>

                                <tr>
                                    <td colspan="5" style="height:20px;border:none;"></td>
                                </tr>

                            </tbody>

                        <?php endforeach; ?>

                    <?php else: ?>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">No records found</td>
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

                                <!-- ================= QUOTATION HEADER ================= -->
                                <tr class="bg_top_header">
                                    <th colspan="2">Vendor Name</th>
                                    <td><?= htmlspecialchars($info['vendor_name'] ?? ''); ?></td>

                                    <th colspan="2">Customer Name</th>
                                    <td><?= htmlspecialchars($info['customer_name'] ?? ''); ?></td>

                                    <th colspan="2">Vendor Rate Enquiry No</th>
                                    <td><?= htmlspecialchars($info['vendor_rate_enquiry_no'] ?? ''); ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th colspan="2">Vendor Contact Person</th>
                                    <td><?= htmlspecialchars($info['vendor_contact_person'] ?? ''); ?></td>

                                    <th colspan="2">Vendor Rate Enquiry Date</th>
                                    <td>
                                        <?= !empty($info['vendor_rate_enquiry_date'])
                                            ? date('d-m-Y', strtotime($info['vendor_rate_enquiry_date']))
                                            : ''; ?>
                                    </td>

                                    <th colspan="2">Enquiry Status</th>
                                    <td><?= htmlspecialchars($info['vendor_rate_enquiry_status'] ?? ''); ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th colspan="2"> Opening Date</th>
                                    <td>
                                        <?= !empty($info['opening_date'])
                                            ? date('d-m-Y', strtotime($info['opening_date']))
                                            : ''; ?>
                                    </td>

                                    <th colspan="2"> Closing Date</th>
                                    <td>
                                        <?= !empty($info['closing_date'])
                                            ? date('d-m-Y', strtotime($info['closing_date']))
                                            : ''; ?>
                                    </td>

                                    <th>Tender Enquiry Id</th>
                                    <td colspan="2"><?= htmlspecialchars($info['tender_enquiry_id'] ?? ''); ?></td>
                                </tr>

                                <!-- ================= ITEM HEADER ================= -->
                                <tr class="bg_table_header">
                                    <th width="5%">S.No</th>
                                    <th width="10%">Vendor Rate Enquiry Id</th>
                                    <th width="12%">Vendor Rate Enquiry Item Id</th>
                                    <th width="12%">Tender Enquiry Item Id</th>
                                    <th width="12%">Item Code</th>
                                    <th width="29%" colspan="2">Item Description</th>
                                    <th width="8%">UOM</th>
                                    <th width="12%" class="text-right">Qty</th>
                                </tr>

                            </thead>
                            <tbody>
                                <!-- ITEMS -->
                                <?php if (!empty($vendor_rate_enquiry['items'])): ?>
                                    <?php $tot = 0;
                                    foreach ($vendor_rate_enquiry['items'] as $k => $item):
                                        $decimal = $item['decimal_point'] ?? 2;
                                        $tot += $item['qty'] ?? 0;
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $k + 1; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $item['vendor_rate_enquiry_id'] ?? ''; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $item['vendor_rate_enquiry_item_id'] ?? ''; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $item['tender_enquiry_item_id'] ?? ''; ?>
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
                                            <td class="text-right">
                                                <?php echo htmlspecialchars($item['qty'] ?? 0); ?>
                                            </td>
                                            <!-- <td class="text-right">
                                                <?php // echo number_format($item['rate'] ?? 0, $decimal); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php //echo htmlspecialchars($item['gst'] ?? 0); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php //echo $item['currency_code'] ?? '' . ' ' . number_format($item['amount'] ?? 0, $decimal); ?>
                                            </td> -->
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="8" class="text-right"><strong>Total Qty:</strong></td>
                                        <td class="text-right">
                                            <!-- <?php //echo $item['currency_code'] ?? '' . ' ' . number_format($tot, 2); ?> -->
                                            <!-- total qty  -->
                                            <?php echo number_format($tot, 2); ?>
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

                                <!-- ================= QUOTATION HEADER ================= -->
                                <tr class="bg_top_header">
                                    <th colspan="2">Vendor Name</th>
                                    <td colspan="2"><?= htmlspecialchars($info['vendor_name'] ?? ''); ?></td>

                                    <th colspan="2">Contact Person</th>
                                    <td colspan="2"><?= htmlspecialchars($info['contact_person_name'] ?? ''); ?></td>

                                    <th>Customer Name</th>
                                    <td colspan="2"><?= htmlspecialchars($info['customer_name'] ?? ''); ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th colspan="2">Vendor Quotation No</th>
                                    <td colspan="2"><?= htmlspecialchars($info['quote_no'] ?? ''); ?></td>

                                    <th colspan="2">Quotation Date</th>
                                    <td colspan="2">
                                        <?= !empty($info['quote_date'])
                                            ? date('d-m-Y', strtotime($info['quote_date']))
                                            : ''; ?>
                                    </td>

                                    <th colspan="2">Quotation Status</th>
                                    <td><?= htmlspecialchars($info['quote_status'] ?? ''); ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th colspan="2">Tender Enquiry Id</th>
                                    <td colspan="2"><?= htmlspecialchars($info['tender_enquiry_id'] ?? ''); ?></td>

                                    <th colspan="2">Transport Charges</th>
                                    <td colspan="2"><?= $info['transport_charges'] ?? '0.00'; ?></td>

                                    <th>Other Charges</th>
                                    <td colspan="2"><?= $info['other_charges'] ?? '0.00'; ?></td>
                                </tr>

                                <!-- ================= ITEM HEADER ================= -->
                                <tr class="bg_table_header">
                                    <th width="4%">S.No</th>
                                    <th width="8%">Vendor Quotation Id</th>
                                    <th width="10%">Vendor Quote Item Id</th>
                                    <th width="10%">Vendor Rate Enquiry Item Id</th>
                                    <th width="10%">Item Code</th>
                                    <th width="22%">Item Description</th>
                                    <th width="6%">UOM</th>
                                    <th width="8%" class="text-right">Qty</th>
                                    <th width="8%" class="text-right">Rate</th>
                                    <th width="6%" class="text-right">VAT %</th>
                                    <th width="8%" class="text-right">Amount</th>
                                </tr>

                            </thead>
                            <tbody>
                                <?php if (!empty($vendor_quotation['items'])): ?>
                                    <?php $tot = 0;
                                    foreach ($vendor_quotation['items'] as $k => $item):
                                        $decimal = $item['decimal_point'] ?? 2;
                                        $tot += $item['amount'] ?? 0;
                                        ?>
                                        <tr>
                                            <td><?php echo $k + 1; ?></td>
                                            <td><?php echo $item['vendor_quote_id'] ?? ''; ?></td>
                                            <td><?php echo $item['vendor_quote_item_id'] ?? ''; ?></td>
                                            <td><?php echo $item['vendor_rate_enquiry_item_id'] ?? ''; ?></td>
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
                                        <td colspan="9" class="text-right"><strong>Total Amount:</strong></td>
                                        <td class="text-right" colspan="2">
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

                                <!-- ================= QUOTATION HEADER ================= -->
                                <tr class="bg_top_header">
                                    <th colspan="2">Vendor Name</th>
                                    <td colspan="2"><?= htmlspecialchars($info['vendor_name'] ?? ''); ?></td>

                                    <th colspan="2">Contact Person</th>
                                    <td colspan="2"><?= htmlspecialchars($info['contact_person_name'] ?? ''); ?></td>

                                    <th>Customer Name</th>
                                    <td colspan="2"><?= htmlspecialchars($info['customer_name'] ?? ''); ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th colspan="2">Vendor PO No</th>
                                    <td colspan="2"><?= htmlspecialchars($info['po_no'] ?? ''); ?></td>

                                    <th colspan="2">PO Date</th>
                                    <td colspan="2">
                                        <?= !empty($info['po_date'])
                                            ? date('d-m-Y', strtotime($info['po_date']))
                                            : ''; ?>
                                    </td>

                                    <th colspan="2">PO Status</th>
                                    <td><?= htmlspecialchars($info['po_status'] ?? ''); ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th colspan="2">Tender Enquiry Id</th>
                                    <td colspan="2"><?= htmlspecialchars($info['tender_enquiry_id'] ?? ''); ?></td>

                                    <th colspan="2">Transport Charges</th>
                                    <td colspan="2"><?= $info['transport_charges'] ?? '0.00'; ?></td>

                                    <th>Other Charges</th>
                                    <td colspan="2"><?= $info['other_charges'] ?? '0.00'; ?></td>
                                </tr>

                                <!-- ================= ITEM HEADER ================= -->
                                <tr class="bg_table_header">
                                    <th width="4%">S.No</th>
                                    <th width="8%">Vendor PO Id</th>
                                    <th width="10%">Vendor PO Item Id</th>
                                    <th width="10%">Vendor Quotation Item Id</th>
                                    <th width="10%">Item Code</th>
                                    <th width="22%">Item Description</th>
                                    <th width="6%">UOM</th>
                                    <th width="8%" class="text-right">Qty</th>
                                    <th width="8%" class="text-right">Rate</th>
                                    <th width="6%" class="text-right">VAT %</th>
                                    <th width="8%" class="text-right">Amount</th>
                                </tr>

                            </thead>
                            <tbody>
                                <!-- ITEMS -->
                                <?php if (!empty($po_list['items'])): ?>
                                    <?php $tot = 0;
                                    foreach ($po_list['items'] as $k => $item):
                                        $decimal = $item['decimal_point'] ?? 2;
                                        $tot += $item['amount'] ?? 0;
                                        ?>
                                        <tr>
                                            <td><?php echo $k + 1; ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($item['vendor_po_id'] ?? ''); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($item['vendor_po_item_id'] ?? ''); ?>
                                            </td>
                                            <td class="text-center"><?php echo htmlspecialchars($item['vendor_quote_item_id'] ?? ''); ?>
                                            </td>
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
                                        <td colspan="9" class="text-right"><strong>Total Amount:</strong></td>
                                        <td class="text-right" colspan="2">
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
                                <tr class="bg_top_header">
                                    <th colspan="2">Vendor Name</th>
                                    <td colspan="2"><?= htmlspecialchars($info['vendor_name'] ?? ''); ?></td>

                                    <th colspan="2">Contact Person</th>
                                    <td><?= htmlspecialchars($info['contact_person_name'] ?? ''); ?></td>

                                    <th>Customer Name</th>
                                    <td><?= htmlspecialchars($info['customer_name'] ?? ''); ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th colspan="2">Vendor Inward No</th>
                                    <td><?= htmlspecialchars($info['inward_no'] ?? ''); ?></td>

                                    <th colspan="2">Inward Date</th>
                                    <td>
                                        <?= !empty($info['inward_date'])
                                            ? date('d-m-Y', strtotime($info['inward_date']))
                                            : ''; ?>
                                    </td>

                                    <th colspan="2">Inward Status</th>
                                    <td><?= htmlspecialchars($info['inward_status'] ?? 'Active'); ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th colspan="2">Tender Enquiry Id</th>
                                    <td><?= htmlspecialchars($info['tender_enquiry_id'] ?? ''); ?></td>
                                    <th colspan="2">Vendor Po Id</th>
                                    <td><?= htmlspecialchars($info['vendor_po_id'] ?? ''); ?></td>

                                    <th>Other Charges</th>
                                    <td colspan="2"><?= $info['other_charges'] ?? '0.00'; ?></td>
                                </tr>

                                <tr class="bg_table_header">
                                    <th width="5%">S.No</th>
                                    <th width="5%">Vendor Purchase Inward Id</th>
                                    <th width="5%">Vendor Purchase Inward Item Id</th>
                                    <th width="5%">Vendor PO Item Id</th>
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
                                                <?php echo htmlspecialchars($item['vendor_pur_inward_id'] ?? ''); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($item['vendor_pur_inward_item_id'] ?? ''); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($item['vendor_po_item_id'] ?? ''); ?>
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

        <?php
        //         echo '<pre>';
        // print_r($vendor_invoice_list);
        // echo '</pre>';
        ?>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <?php if (!empty($vendor_invoice_list)): ?>
                        <?php foreach ($vendor_invoice_list as $vendor_invoice):
                            $info = $vendor_invoice['info'] ?? [];
                            ?>
                            <thead>
                                <tr class="bg_top_header">
                                    <th colspan="2">Vendor Name</th>
                                    <td colspan="2"><?= htmlspecialchars($info['vendor_name'] ?? ''); ?></td>

                                    <th colspan="2">Contact Person</th>
                                    <td><?= htmlspecialchars($info['contact_person_name'] ?? ''); ?></td>

                                    <th colspan="2">Customer Name</th>
                                    <td colspan="2"><?= htmlspecialchars($info['customer_name'] ?? ''); ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th colspan="2">Vendor Invoice No</th>
                                    <td><?= htmlspecialchars($info['invoice_no'] ?? ''); ?></td>

                                    <th colspan="2">Invoice Date</th>
                                    <td>
                                        <?= !empty($info['invoice_date'])
                                            ? date('d-m-Y', strtotime($info['invoice_date']))
                                            : ''; ?>
                                    </td>

                                    <th colspan="2">Entry Date</th>
                                    <td colspan="3">
                                        <?= !empty($info['entry_date']) ? date('d-m-Y', strtotime($info['entry_date'])) : ''; ?>
                                    </td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th colspan="2">Vat Payer Id</th>
                                    <td colspan="3"><?= htmlspecialchars($info['vat_payer_purchase_grp'] ?? ''); ?></td>

                                    <th colspan="1">Declaration No</th>
                                    <td>
                                        <?= !empty($info['declaration_no'])
                                            ? $info['declaration_no']
                                            : ''; ?>
                                    </td>

                                    <th colspan="2">Declaration Date</th>
                                    <td colspan="2">
                                        <?= !empty($info['declaration_date']) ? date('d-m-Y', strtotime($info['declaration_date'])) : ''; ?>
                                    </td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th colspan="2">Tender Enquiry Id</th>
                                    <td><?= htmlspecialchars($info['tender_enquiry_id'] ?? ''); ?></td>
                                    <th colspan="2">Vendor Po Id</th>
                                    <td><?= htmlspecialchars($info['vendor_po_id'] ?? ''); ?></td>

                                    <th>Invoice Total</th>
                                    <td colspan="4"><?= $info['total_amount'] ?? '0.00'; ?></td>
                                </tr>

                                <tr class="bg_table_header">
                                    <th width="5%">S.No</th>
                                    <th width="5%">Vendor Purchase Invoice Id</th>
                                    <th width="5%">Vendor Purchase Invoice Item Id</th>
                                    <th width="5%">Vendor PO Item Id</th>
                                    <th width="15%">Item Code</th>
                                    <th width="40%">Item Description</th>
                                    <th width="10%">UOM</th>
                                    <th width="8%" class="text-right">Qty</th>
                                    <th width="8%" class="text-right">Rate</th>
                                    <th width="6%" class="text-right">VAT %</th>
                                    <th width="8%" class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ITEMS -->
                                <?php if (!empty($vendor_invoice['items'])): ?>
                                    <?php $tot = 0;
                                    foreach ($vendor_invoice['items'] as $k => $item):
                                        $decimal = $item['decimal_point'] ?? 2;
                                        $tot += $item['amount'] ?? 0;
                                        ?>
                                        <tr>
                                            <td><?php echo $k + 1; ?></td>
                                            <td class="text-center">
                                                <?php echo htmlspecialchars($item['vendor_purchase_invoice_id'] ?? ''); ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo htmlspecialchars($item['vendor_purchase_invoice_item_id'] ?? ''); ?>
                                            </td>
                                            <td class="text-center"><?php echo htmlspecialchars($item['vendor_po_item_id'] ?? ''); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($item['item_code'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['item_desc'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['uom'] ?? ''); ?></td>
                                            <td class="text-right"><?php echo htmlspecialchars($item['qty'] ?? 0); ?></td>
                                            <td class="text-right">
                                                <?php echo number_format($item['rate'] ?? 0, $decimal); ?>
                                            </td>
                                            <td class="text-right"><?php echo htmlspecialchars($item['gst'] ?? 0); ?></td>
                                            <td class="text-right">
                                                <?php echo number_format($item['amount'] ?? 0, $decimal); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="9" class="text-right"><strong>Total Amount:</strong></td>
                                        <td class="text-right" colspan="2"><?php echo number_format($tot, 2); ?>
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


    <!-- Vendor Payment Bill List -->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border clearfix">
            <h3 class="box-title">Vendor Payment Purchase Invoice List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">

                    <?php if (!empty($vendor_payment_list)): ?>

                        <?php foreach ($vendor_payment_list as $bill_list):
                            $info = $bill_list['info'] ?? [];
                            $items = $bill_list['items'] ?? [];
                            ?>

                            <thead>
                                <tr class="bg_top_header">
                                    <th>Payment Mode</th>
                                    <td><?= htmlspecialchars($info['payment_mode'] ?? '') ?></td>

                                    <th>Vendor Name</th>
                                    <td><?= htmlspecialchars($info['vendor_name'] ?? '') ?></td>

                                    <th>Payment No</th>
                                    <td><?= htmlspecialchars($info['payment_no'] ?? '') ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th>Payment Date</th>
                                    <td><?= $info['payment_date'] ?? '' ?></td>
                                    <th>Payment Date</th>
                                    <td><?= $info['payment_date'] ?? '' ?></td>

                                    <th>Payment Type</th>
                                    <td><?= htmlspecialchars($info['payment_type'] ?? '') ?></td>
                                </tr>

                                <tr class="bg_table_header">
                                    <th>S.No</th>
                                    <th>Payment ID</th>
                                    <th>Bill ID</th>
                                    <th>Enquiry ID</th>
                                    <th>Bill Type</th>
                                    <th class="text-right" colspan="2">Amount</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (!empty($items)): ?>
                                    <?php $tot = 0; ?>

                                    <?php foreach ($items as $k => $item):
                                        $amount = $item['bill_amount'] ?? 0;
                                        $tot += $amount;
                                        ?>
                                        <tr>
                                            <td><?= $k + 1 ?></td>
                                            <td><?= $item['vendor_payment_id'] ?? '' ?></td>
                                            <td><?= $item['bill_id'] ?? '' ?></td>
                                            <td><?= $item['tender_enquiry_id'] ?? '' ?></td>
                                            <td><?= $item['bill_type'] ?? '' ?></td>
                                            <td colspan="2" class="text-right"><?= number_format($amount, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <!-- TOTAL -->
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Total</strong></td>
                                        <td class="text-right">
                                            <strong><?= number_format($tot, 2) ?></strong>
                                        </td>
                                    </tr>

                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No items available</td>
                                    </tr>
                                <?php endif; ?>

                                <tr>
                                    <td colspan="5" style="height:20px;border:none;"></td>
                                </tr>

                            </tbody>

                        <?php endforeach; ?>

                    <?php else: ?>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">No records found</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>

                </table>
            </div>
        </div>
    </div>


    <!-- Vendor Payment Local  Bill List -->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border clearfix">
            <h3 class="box-title">Vendor Payment Local Bill List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">

                    <?php if (!empty($vendor_payment_local_bill_list)): ?>

                        <?php foreach ($vendor_payment_local_bill_list as $bill_list):
                            $info = $bill_list['info'] ?? [];
                            $items = $bill_list['items'] ?? [];
                            ?>

                            <thead>
                                <tr class="bg_top_header">
                                    <th>Payment Mode</th>
                                    <td><?= htmlspecialchars($info['payment_mode'] ?? '') ?></td>

                                    <th>Vendor Name</th>
                                    <td><?= htmlspecialchars($info['vendor_name'] ?? '') ?></td>

                                    <th>Payment No</th>
                                    <td><?= htmlspecialchars($info['payment_no'] ?? '') ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th>Payment Date</th>
                                    <td><?= $info['payment_date'] ?? '' ?></td>
                                    <th>Payment Date</th>
                                    <td><?= $info['payment_date'] ?? '' ?></td>

                                    <th>Payment Type</th>
                                    <td><?= htmlspecialchars($info['payment_type'] ?? '') ?></td>
                                </tr>
                                <tr class="bg_table_header">
                                    <th>S.No</th>
                                    <th>Payment ID</th>
                                    <th>Bill ID</th>
                                    <th>Enquiry ID</th>
                                    <th>Bill Type</th>
                                    <th class="text-right" colspan="2">Amount</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (!empty($items)): ?>
                                    <?php $tot = 0; ?>

                                    <?php foreach ($items as $k => $item):
                                        $amount = $item['bill_amount'] ?? 0;
                                        $tot += $amount;
                                        ?>
                                        <tr>
                                            <td><?= $k + 1 ?></td>
                                            <td><?= $item['vendor_payment_id'] ?? '' ?></td>
                                            <td><?= $item['bill_id'] ?? '' ?></td>
                                            <td><?= $item['tender_enquiry_id'] ?? '' ?></td>
                                            <td><?= $item['bill_type'] ?? '' ?></td>
                                            <td colspan="2" class="text-right"><?= number_format($amount, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <!-- TOTAL -->
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Total</strong></td>
                                        <td class="text-right">
                                            <strong><?= number_format($tot, 2) ?></strong>
                                        </td>
                                    </tr>

                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No items available</td>
                                    </tr>
                                <?php endif; ?>

                                <tr>
                                    <td colspan="5" style="height:20px;border:none;"></td>
                                </tr>

                            </tbody>

                        <?php endforeach; ?>

                    <?php else: ?>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">No records found</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>

                </table>
            </div>
        </div>
    </div>



    <!-- Vendor Payment Delivery  Bill List -->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border clearfix">
            <h3 class="box-title">Vendor Payment Delivery Bill List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">

                    <?php if (!empty($vendor_payment_delivery_bill_list)): ?>

                        <?php foreach ($vendor_payment_delivery_bill_list as $bill_list):
                            $info = $bill_list['info'] ?? [];
                            $items = $bill_list['items'] ?? [];
                            ?>

                            <thead>
                                <tr class="bg_top_header">
                                    <th>Payment Mode</th>
                                    <td><?= htmlspecialchars($info['payment_mode'] ?? '') ?></td>

                                    <th>Vendor Name</th>
                                    <td><?= htmlspecialchars($info['vendor_name'] ?? '') ?></td>

                                    <th>Payment No</th>
                                    <td><?= htmlspecialchars($info['payment_no'] ?? '') ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th>Payment Date</th>
                                    <td><?= $info['payment_date'] ?? '' ?></td>
                                    <th>Payment Date</th>
                                    <td><?= $info['payment_date'] ?? '' ?></td>

                                    <th>Payment Type</th>
                                    <td><?= htmlspecialchars($info['payment_type'] ?? '') ?></td>
                                </tr>

                                <tr class="bg_table_header">
                                    <th>S.No</th>
                                    <th>Payment ID</th>
                                    <th>Bill ID</th>
                                    <th>Enquiry ID</th>
                                    <th>Bill Type</th>
                                    <th class="text-right" colspan="2">Amount</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (!empty($items)): ?>
                                    <?php $tot = 0; ?>

                                    <?php foreach ($items as $k => $item):
                                        $amount = $item['bill_amount'] ?? 0;
                                        $tot += $amount;
                                        ?>
                                        <tr>
                                            <td><?= $k + 1 ?></td>
                                            <td><?= $item['vendor_payment_id'] ?? '' ?></td>
                                            <td><?= $item['bill_id'] ?? '' ?></td>
                                            <td><?= $item['tender_enquiry_id'] ?? '' ?></td>
                                            <td><?= $item['bill_type'] ?? '' ?></td>
                                            <td colspan="2" class="text-right"><?= number_format($amount, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <!-- TOTAL -->
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Total</strong></td>
                                        <td class="text-right">
                                            <strong><?= number_format($tot, 2) ?></strong>
                                        </td>
                                    </tr>

                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No items available</td>
                                    </tr>
                                <?php endif; ?>

                                <tr>
                                    <td colspan="5" style="height:20px;border:none;"></td>
                                </tr>

                            </tbody>

                        <?php endforeach; ?>

                    <?php else: ?>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">No records found</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>

                </table>
            </div>
        </div>
    </div>


    <!-- Vendor Payment Customer  Bill List -->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border clearfix">
            <h3 class="box-title">Vendor Payment Customer Bill List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">

                    <?php if (!empty($vendor_payment_customer_bill_list)): ?>

                        <?php foreach ($vendor_payment_customer_bill_list as $bill_list):
                            $info = $bill_list['info'] ?? [];
                            $items = $bill_list['items'] ?? [];
                            ?>

                            <thead>
                                <tr class="bg_top_header">
                                    <th>Payment Mode</th>
                                    <td><?= htmlspecialchars($info['payment_mode'] ?? '') ?></td>

                                    <th>Vendor Name</th>
                                    <td><?= htmlspecialchars($info['vendor_name'] ?? '') ?></td>

                                    <th>Payment No</th>
                                    <td><?= htmlspecialchars($info['payment_no'] ?? '') ?></td>
                                </tr>

                                <tr class="bg_top_header">
                                    <th>Payment Date</th>
                                    <td><?= $info['payment_date'] ?? '' ?></td>
                                    <th>Payment Date</th>
                                    <td><?= $info['payment_date'] ?? '' ?></td>

                                    <th>Payment Type</th>
                                    <td><?= htmlspecialchars($info['payment_type'] ?? '') ?></td>
                                </tr>
                                <tr class="bg_table_header">
                                    <th>S.No</th>
                                    <th>Payment ID</th>
                                    <th>Bill ID</th>
                                    <th>Enquiry ID</th>
                                    <th>Bill Type</th>
                                    <th class="text-right" colspan="2">Amount</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (!empty($items)): ?>
                                    <?php $tot = 0; ?>

                                    <?php foreach ($items as $k => $item):
                                        $amount = $item['bill_amount'] ?? 0;
                                        $tot += $amount;
                                        ?>
                                        <tr>
                                            <td><?= $k + 1 ?></td>
                                            <td><?= $item['vendor_payment_id'] ?? '' ?></td>
                                            <td><?= $item['bill_id'] ?? '' ?></td>
                                            <td><?= $item['tender_enquiry_id'] ?? '' ?></td>
                                            <td><?= $item['bill_type'] ?? '' ?></td>
                                            <td colspan="2" class="text-right"><?= number_format($amount, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <!-- TOTAL -->
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Total</strong></td>
                                        <td class="text-right">
                                            <strong><?= number_format($tot, 2) ?></strong>
                                        </td>
                                    </tr>

                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No items available</td>
                                    </tr>
                                <?php endif; ?>

                                <tr>
                                    <td colspan="5" style="height:20px;border:none;"></td>
                                </tr>

                            </tbody>

                        <?php endforeach; ?>

                    <?php else: ?>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">No records found</td>
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

        function toggleBoxes() {

            var isChecked = $("#chk_open_all").is(":checked");

            $(".box").not("#search_filter_box").each(function () {

                if (isChecked) {
                    $(this)
                        .removeClass("collapsed-box")
                        .children(".box-body, .box-footer")
                        .slideDown();
                } else {
                    $(this)
                        .addClass("collapsed-box")
                        .children(".box-body, .box-footer")
                        .slideUp();
                }

            });
        }

        // Run on page load
        toggleBoxes();

        // Run on checkbox change
        $("#chk_open_all").on("change", toggleBoxes);

    });
</script>