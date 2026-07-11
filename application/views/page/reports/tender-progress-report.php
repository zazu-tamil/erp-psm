<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header no-print">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Reports</a></li>
        <li class="active"><?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">
    <!-- Search Filter -->
    <div class="box box-info no-print">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('tender-progress-report'); ?>" id="frmsearch">
                <input type="hidden" name="mode" value="Search">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="srch_from_date">From Date</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="date" name="srch_from_date" id="srch_from_date" class="form-control"
                                value="<?php echo set_value('srch_from_date', $srch_from_date); ?>" required="true">
                        </div>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_to_date">To Date</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="date" name="srch_to_date" id="srch_to_date" class="form-control"
                                value="<?php echo set_value('srch_to_date', $srch_to_date); ?>" required="true">
                        </div>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_customer_id">Customer</label>
                        <select name="srch_customer_id" id="srch_customer_id" class="form-control select2">
                            <option value="">-- All Customers --</option>
                            <?php if (!empty($customer_list)): ?>
                                <?php foreach ($customer_list as $cust): ?>
                                    <option value="<?php echo $cust['customer_id']; ?>"
                                        <?php echo ($srch_customer_id == $cust['customer_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cust['customer_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_text">PO / RFQ / Item Code</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-search"></i></div>
                            <input type="text" name="srch_text" id="srch_text" class="form-control"
                                placeholder="Search PO, RFQ, items..." value="<?php echo htmlspecialchars($srch_text); ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show Progress</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Stepper Custom Styles -->
    <style>
        .po-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .po-card:hover {
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .po-card-header {
            background: linear-gradient(135deg, #1e293b, #334155);
            color: #fff;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .po-card-title {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .po-card-title span {
            color: #38bdf8;
            font-weight: 800;
        }

        .po-card-meta {
            font-size: 12px;
            color: #cbd5e1;
            margin-top: 4px;
        }

        .po-card-metrics {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .metric-badge {
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            color: #f8fafc;
        }

        .metric-badge span {
            color: #facc15;
            font-size: 13px;
        }

        /* Stepper Styles */
        .stepper-row {
            padding: 40px 25px 25px;
            position: relative;
        }

        .stepper-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            z-index: 2;
        }

        /* Stepper Connecting Line */
        .stepper-progress-line {
            position: absolute;
            top: 60px;
            left: 8.33%;
            right: 8.33%;
            height: 4px;
            background: #e2e8f0;
            z-index: 1;
        }

        .stepper-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #3b82f6);
            width: 0%;
            transition: width 0.4s ease;
        }

        .stepper-node {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .node-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 3px solid #fff;
            transition: all 0.3s ease;
            position: relative;
        }

        /* Colors for Stepper Nodes */
        .node-success {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .node-warning {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .node-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        .node-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            font-size: 10px;
            font-weight: bold;
        }

        .node-success .node-badge i { color: #10b981; }
        .node-warning .node-badge i { color: #3b82f6; }
        .node-danger .node-badge i { color: #ef4444; }

        .node-label {
            font-size: 12px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .node-desc {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
            line-height: 1.3;
        }

        .node-desc strong {
            color: #0f172a;
            font-weight: 700;
        }

        /* Detail Panel Toggle */
        .card-toggle-btn {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 12px 25px;
            text-align: center;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            transition: background 0.2s ease;
        }

        .card-toggle-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .details-table-panel {
            border-top: 1px solid #e2e8f0;
            padding: 20px 25px;
            background: #fafafa;
        }

        .details-table-panel table {
            margin-bottom: 0;
            background-color: #fff;
        }

        .details-table-panel th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            text-align: center;
        }

        .details-table-panel td {
            font-size: 12px;
            text-align: center;
            vertical-align: middle !important;
        }

        .details-table-panel .text-left { text-align: left; }

        /* Progress Fill Percent helper classes */
        @media (max-width: 992px) {
            .stepper-container {
                flex-direction: column;
                gap: 30px;
                align-items: center;
            }
            .stepper-node {
                width: 100%;
            }
            .stepper-progress-line {
                display: none;
            }
        }
    </style>

    <!-- PO Progress List -->
    <div class="row">
        <div class="col-md-12">
            <?php if (!empty($po_records)): ?>
                <?php foreach ($po_records as $po): ?>
                    <?php
                    // Compute overall percentages
                    $v_po_pct = $po['total_po_qty'] > 0 ? ($po['total_vendor_po_qty'] / $po['total_po_qty']) * 100 : 0;
                    $dc_pct = $po['total_po_qty'] > 0 ? ($po['total_delivered_qty'] / $po['total_po_qty']) * 100 : 0;
                    $inv_pct = $po['total_po_qty'] > 0 ? ($po['total_invoiced_qty'] / $po['total_po_qty']) * 100 : 0;

                    // Stepper Line Fill (each completed node moves line progress)
                    // 6 nodes = 5 segments.
                    // Completed stages:
                    // 1. Enquiry: always (1/5)
                    // 2. Quotation: always if quotation_no exists (2/5)
                    // 3. Customer PO: always (3/5)
                    // 4. Vendor PO: completed proportionally (up to 4/5)
                    // 5. Customer DC: completed proportionally (up to 5/5)
                    // 6. Customer Invoice: completed proportionally (up to 100%)
                    $completed_stages = 3;
                    if ($v_po_pct > 0) $completed_stages += ($v_po_pct / 100);
                    if ($dc_pct > 0) $completed_stages += ($dc_pct / 100);
                    if ($inv_pct > 0) $completed_stages += ($inv_pct / 100);

                    $fill_width = min(100, ($completed_stages / 5) * 100);
                    ?>

                    <div class="po-card">
                        <!-- Card Header -->
                        <div class="po-card-header">
                            <div>
                                <h4 class="po-card-title">
                                    PO: <span><?php echo htmlspecialchars($po['our_po_no']); ?></span> 
                                    (Cust PO: <?php echo htmlspecialchars($po['customer_po_no'] ?: 'N/A'); ?>)
                                </h4>
                                <div class="po-card-meta">
                                    <strong>Tender Enquiry ID:</strong> <?php echo htmlspecialchars($po['tender_order_id'] ?: 'N/A'); ?> &nbsp;|&nbsp;
                                    <strong>Customer:</strong> <?php echo htmlspecialchars($po['customer_name']); ?> &nbsp;|&nbsp;
                                    <strong>PO Date:</strong> <?php echo date('d-m-Y', strtotime($po['po_date'])); ?>
                                </div>
                            </div>
                            <div class="po-card-metrics">
                                <div class="metric-badge">PO Qty: <span><?php echo $po['total_po_qty']; ?></span></div>
                                <div class="metric-badge">Vendor PO Qty: <span><?php echo $po['total_vendor_po_qty']; ?></span></div>
                                <div class="metric-badge">Delivered: <span><?php echo $po['total_delivered_qty']; ?></span></div>
                                <div class="metric-badge">Invoiced: <span><?php echo $po['total_invoiced_qty']; ?></span></div>
                            </div>
                        </div>

                        <!-- Card Body (Stepper) -->
                        <div class="stepper-row">
                            <div class="stepper-progress-line">
                                <div class="stepper-progress-fill" style="width: <?php echo $fill_width; ?>%;"></div>
                            </div>
                            <div class="stepper-container">
                                
                                <!-- Step 1: Customer Enquiry -->
                                <div class="stepper-node">
                                    <div class="node-circle node-success">
                                        <i class="fa fa-file-text-o"></i>
                                        <div class="node-badge"><i class="fa fa-check"></i></div>
                                    </div>
                                    <div class="node-label">Customer Enquiry</div>
                                    <div class="node-desc">
                                        No: <strong><?php echo htmlspecialchars($po['enquiry_no']); ?></strong><br>
                                        Date: <?php echo date('d-m-Y', strtotime($po['enquiry_date'])); ?>
                                    </div>
                                </div>

                                <!-- Step 2: Customer Quotation -->
                                <div class="stepper-node">
                                    <?php 
                                    $has_quote = !empty($po['quotation_no']);
                                    $quote_class = $has_quote ? 'node-success' : 'node-danger';
                                    $quote_icon = $has_quote ? 'fa-check' : 'fa-times';
                                    ?>
                                    <div class="node-circle <?php echo $quote_class; ?>">
                                        <i class="fa fa-calculator"></i>
                                        <div class="node-badge"><i class="fa <?php echo $quote_icon; ?>"></i></div>
                                    </div>
                                    <div class="node-label">Customer Quote</div>
                                    <div class="node-desc">
                                        <?php if ($has_quote): ?>
                                            No: <strong><?php echo htmlspecialchars($po['quotation_no']); ?></strong><br>
                                            Date: <?php echo date('d-m-Y', strtotime($po['quote_date'])); ?>
                                        <?php else: ?>
                                            Not Created
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Step 3: Customer PO -->
                                <div class="stepper-node">
                                    <div class="node-circle node-success">
                                        <i class="fa fa-shopping-cart"></i>
                                        <div class="node-badge"><i class="fa fa-check"></i></div>
                                    </div>
                                    <div class="node-label">Customer PO</div>
                                    <div class="node-desc">
                                        Date: <?php echo date('d-m-Y', strtotime($po['po_date'])); ?><br>
                                        Qty: <strong><?php echo $po['total_po_qty']; ?></strong>
                                    </div>
                                </div>

                                <!-- Step 4: Vendor PO -->
                                <div class="stepper-node">
                                    <?php
                                    if ($v_po_pct >= 100) {
                                        $v_node_class = 'node-success';
                                        $v_node_icon = 'fa-check';
                                    } elseif ($v_po_pct > 0) {
                                        $v_node_class = 'node-warning';
                                        $v_node_icon = 'fa-clock-o';
                                    } else {
                                        $v_node_class = 'node-danger';
                                        $v_node_icon = 'fa-times';
                                    }
                                    ?>
                                    <div class="node-circle <?php echo $v_node_class; ?>">
                                        <i class="fa fa-shopping-bag"></i>
                                        <div class="node-badge"><i class="fa <?php echo $v_node_icon; ?>"></i></div>
                                    </div>
                                    <div class="node-label">Vendor PO</div>
                                    <div class="node-desc">
                                        Qty: <strong><?php echo $po['total_vendor_po_qty']; ?> / <?php echo $po['total_po_qty']; ?></strong><br>
                                        Progress: <strong><?php echo round($v_po_pct, 1); ?>%</strong>
                                    </div>
                                </div>

                                <!-- Step 5: Customer DC -->
                                <div class="stepper-node">
                                    <?php
                                    if ($dc_pct >= 100) {
                                        $dc_node_class = 'node-success';
                                        $dc_node_icon = 'fa-check';
                                    } elseif ($dc_pct > 0) {
                                        $dc_node_class = 'node-warning';
                                        $dc_node_icon = 'fa-clock-o';
                                    } else {
                                        $dc_node_class = 'node-danger';
                                        $dc_node_icon = 'fa-times';
                                    }
                                    ?>
                                    <div class="node-circle <?php echo $dc_node_class; ?>">
                                        <i class="fa fa-truck"></i>
                                        <div class="node-badge"><i class="fa <?php echo $dc_node_icon; ?>"></i></div>
                                    </div>
                                    <div class="node-label">Delivery DC</div>
                                    <div class="node-desc">
                                        Qty: <strong><?php echo $po['total_delivered_qty']; ?> / <?php echo $po['total_po_qty']; ?></strong><br>
                                        Progress: <strong><?php echo round($dc_pct, 1); ?>%</strong>
                                    </div>
                                </div>

                                <!-- Step 6: Customer Invoice -->
                                <div class="stepper-node">
                                    <?php
                                    if ($inv_pct >= 100) {
                                        $inv_node_class = 'node-success';
                                        $inv_node_icon = 'fa-check';
                                    } elseif ($inv_pct > 0) {
                                        $inv_node_class = 'node-warning';
                                        $inv_node_icon = 'fa-clock-o';
                                    } else {
                                        $inv_node_class = 'node-danger';
                                        $inv_node_icon = 'fa-times';
                                    }
                                    ?>
                                    <div class="node-circle <?php echo $inv_node_class; ?>">
                                        <i class="fa fa-money"></i>
                                        <div class="node-badge"><i class="fa <?php echo $inv_node_icon; ?>"></i></div>
                                    </div>
                                    <div class="node-label">Customer Invoice</div>
                                    <div class="node-desc">
                                        Qty: <strong><?php echo $po['total_invoiced_qty']; ?> / <?php echo $po['total_po_qty']; ?></strong><br>
                                        Progress: <strong><?php echo round($inv_pct, 1); ?>%</strong>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Expandable Detail Toggle -->
                        <div class="card-toggle-btn" data-toggle="collapse" data-target="#details-<?php echo $po['tender_po_id']; ?>">
                            <i class="fa fa-list"></i> View Item Quantities Breakdown &nbsp;<i class="fa fa-angle-down"></i>
                        </div>

                        <!-- Detailed Table (Collapsed by default) -->
                        <div id="details-<?php echo $po['tender_po_id']; ?>" class="collapse details-table-panel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Item Code</th>
                                            <th class="text-left">Item Description</th>
                                            <th>UOM</th>
                                            <th>Customer PO Qty</th>
                                            <th>Vendor PO Qty</th>
                                            <th>Delivered Qty</th>
                                            <th>Invoiced Qty</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sno = 1;
                                        foreach ($po['items'] as $item): 
                                            // Compute item level delivery status label
                                            if ($item['delivered_qty'] >= $item['po_qty']) {
                                                $status_label = '<span class="label label-success">Fully Delivered</span>';
                                            } elseif ($item['delivered_qty'] > 0) {
                                                $status_label = '<span class="label label-warning">Partially Delivered</span>';
                                            } else {
                                                $status_label = '<span class="label label-danger">Not Delivered</span>';
                                            }
                                        ?>
                                            <tr>
                                                <td><?php echo $sno++; ?></td>
                                                <td><strong><?php echo htmlspecialchars($item['item_code']); ?></strong></td>
                                                <td class="text-left"><?php echo htmlspecialchars($item['item_desc']); ?></td>
                                                <td><?php echo htmlspecialchars($item['uom']); ?></td>
                                                <td><strong><?php echo $item['po_qty']; ?></strong></td>
                                                <td><?php echo $item['vendor_po_qty']; ?></td>
                                                <td><?php echo $item['delivered_qty']; ?></td>
                                                <td><?php echo $item['invoiced_qty']; ?></td>
                                                <td><?php echo $status_label; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="box box-solid">
                    <div class="box-body text-center text-muted" style="padding: 50px;">
                        <i class="fa fa-line-chart" style="font-size: 60px; color: #cbd5e1; margin-bottom: 15px;"></i>
                        <h4>No purchase order records found for the selected filter criteria.</h4>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
