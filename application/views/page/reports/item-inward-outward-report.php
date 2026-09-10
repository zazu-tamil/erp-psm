<?php include_once(VIEWPATH . '/inc/header.php'); ?>

<style>
    .kpi-row {
        margin-bottom: 20px;
    }
    .kpi-tile {
        background: #fff;
        border-radius: 8px;
        padding: 16px 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        border-left: 4px solid #3b82f6;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        position: relative;
        overflow: hidden;
    }
    .kpi-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    .kpi-tile.kpi-opening { border-left-color: #64748b; }
    .kpi-tile.kpi-inward  { border-left-color: #10b981; }
    .kpi-tile.kpi-outward { border-left-color: #ef4444; }
    .kpi-tile.kpi-net     { border-left-color: #8b5cf6; }
    .kpi-tile.kpi-closing { border-left-color: #2563eb; }
    .kpi-tile.kpi-items   { border-left-color: #0ea5e9; }

    .kpi-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 6px;
    }
    .kpi-value {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }
    .kpi-sub {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 4px;
    }
    .kpi-icon {
        position: absolute;
        right: 14px;
        bottom: 12px;
        font-size: 34px;
        opacity: 0.12;
    }

    .badge-inward {
        background-color: #d1fae5;
        color: #065f46;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-block;
    }
    .badge-outward {
        background-color: #fee2e2;
        color: #991b1b;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-block;
    }
    .badge-opening {
        background-color: #f1f5f9;
        color: #334155;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-block;
    }
    .badge-closing {
        background-color: #dbeafe;
        color: #1e40af;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-block;
    }
    .net-positive {
        color: #059669;
        font-weight: 700;
    }
    .net-negative {
        color: #dc2626;
        font-weight: 700;
    }
    .net-neutral {
        color: #64748b;
        font-weight: 600;
    }

    .month-nav-btn {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }
    .view-tx-btn {
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .view-tx-btn:hover {
        opacity: 0.8;
        text-decoration: underline;
    }
    .table-report th {
        background-color: #1e293b !important;
        color: #f8fafc !important;
        vertical-align: middle !important;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .table-report tfoot th {
        background-color: #0f172a !important;
        color: #f8fafc !important;
        font-size: 13px;
        font-weight: 800;
    }
    .nav-tabs-custom > .nav-tabs > li.active {
        border-top-color: #2563eb;
    }
    .filter-card {
        background: #ffffff;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
</style>

<section class="content-header">
    <h1>
        <i class="fa fa-exchange text-primary"></i> <?php echo htmlspecialchars($title); ?>
        <small class="text-muted" style="font-size: 14px; font-weight: 600;">[ <?php echo htmlspecialchars($month_label); ?> ]</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo site_url('dash'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo site_url('in-stock-item-report'); ?>">In Stock Items</a></li>
        <li class="active"><?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <!-- Search & Filter Card -->
    <div class="filter-card">
        <div class="box-header with-border" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 12px 18px;">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="box-title" style="font-size: 15px; font-weight: 700; color: #1e293b; margin-top: 4px;">
                        <i class="fa fa-sliders text-primary"></i> Month & Filter Options
                    </h3>
                </div>
                <div class="col-sm-6 text-right">
                    <form method="post" action="<?php echo site_url('item-inward-outward-report'); ?>" style="display: inline-block;">
                        <input type="hidden" name="srch_month" value="<?php echo htmlspecialchars($srch_month); ?>">
                        <input type="hidden" name="srch_movement" value="<?php echo htmlspecialchars($srch_movement); ?>">
                        <input type="hidden" name="srch_keyword" value="<?php echo htmlspecialchars($srch_keyword); ?>">
                        <input type="hidden" name="srch_view_mode" value="<?php echo htmlspecialchars($srch_view_mode); ?>">

                        <div class="btn-group">
                            <button type="submit" name="month_action" value="prev" class="btn btn-default btn-sm month-nav-btn" title="Previous Month">
                                <i class="fa fa-chevron-left"></i> Prev
                            </button>
                            <button type="submit" name="month_action" value="current" class="btn btn-default btn-sm month-nav-btn" title="Current Month">
                                <i class="fa fa-calendar"></i> Current
                            </button>
                            <button type="submit" name="month_action" value="next" class="btn btn-default btn-sm month-nav-btn" title="Next Month">
                                Next <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="box-body" style="padding: 18px 20px;">
            <form method="post" action="<?php echo site_url('item-inward-outward-report'); ?>" id="frmReportSearch">
                <input type="hidden" name="srch_view_mode" id="srch_view_mode" value="<?php echo htmlspecialchars($srch_view_mode); ?>">

                <div class="row">
                    <!-- Month Selector -->
                    <div class="col-md-3 col-sm-6 form-group">
                        <label for="srch_month" style="font-weight: 600; color: #334155;">
                            <i class="fa fa-calendar-check-o text-primary"></i> Select Month <span class="text-danger">*</span>
                        </label>
                        <input type="month" name="srch_month" id="srch_month" class="form-control"
                               value="<?php echo htmlspecialchars($srch_month); ?>" required onchange="this.form.submit()">
                        <span class="help-block text-muted" style="font-size: 11px; margin-top: 2px;">
                            Period: <?php echo date('d-M-Y', strtotime($start_date)); ?> to <?php echo date('d-M-Y', strtotime($end_date)); ?>
                        </span>
                    </div>

                    <!-- Movement Filter (Inward & Outward based) -->
                    <div class="col-md-2 col-sm-6 form-group">
                        <label for="srch_movement" style="font-weight: 600; color: #334155;">
                            <i class="fa fa-exchange text-primary"></i> Movement
                        </label>
                        <select name="srch_movement" id="srch_movement" class="form-control" onchange="this.form.submit()">
                            <option value="all" <?php echo ($srch_movement === 'all') ? 'selected' : ''; ?>>All (In &amp; Out)</option>
                            <option value="inward_only" <?php echo ($srch_movement === 'inward_only') ? 'selected' : ''; ?>>Inward Only</option>
                            <option value="outward_only" <?php echo ($srch_movement === 'outward_only') ? 'selected' : ''; ?>>Outward Only</option>
                        </select>
                    </div>

                    <!-- Keyword Search (LIKE Filter) -->
                    <div class="col-md-4 col-sm-6 form-group">
                        <label for="srch_keyword" style="font-weight: 600; color: #334155;">
                            <i class="fa fa-search text-primary"></i> Search Item Code / Description
                        </label>
                        <div class="input-group">
                            <input type="text" name="srch_keyword" id="srch_keyword" class="form-control"
                                   placeholder="Type item code or keywords..." value="<?php echo htmlspecialchars($srch_keyword); ?>">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default" onclick="$('#srch_keyword').val(''); $('#frmReportSearch').submit();" title="Clear search">
                                    <i class="fa fa-times"></i>
                                </button>
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-3 col-sm-12 form-group text-right" style="margin-top: 24px;">
                        <button type="submit" class="btn btn-primary" style="font-weight: 600; padding: 6px 16px;">
                            <i class="fa fa-search"></i> Apply Filter
                        </button>

                        <a href="<?php echo site_url('item-inward-outward-report?export=excel'); ?>" class="btn btn-success" style="font-weight: 600; padding: 6px 16px; margin-left: 6px;" id="btnExportExcel">
                            <i class="fa fa-file-excel-o"></i> Export Excel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Summary Metrics -->
    <div class="row kpi-row">
        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="kpi-tile kpi-opening">
                <div class="kpi-label">Opening Stock</div>
                <div class="kpi-value"><?php echo number_format($kpi['total_opening_qty'], 2); ?></div>
                <div class="kpi-sub">Before <?php echo date('d-M', strtotime($start_date)); ?></div>
                <i class="fa fa-history kpi-icon text-muted"></i>
            </div>
        </div>

        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="kpi-tile kpi-inward">
                <div class="kpi-label" style="color: #059669;">Total Inward</div>
                <div class="kpi-value" style="color: #059669;"><?php echo number_format($kpi['total_inward_qty'], 2); ?></div>
                <div class="kpi-sub"><?php echo $kpi['total_inward_tx']; ?> Inward Bills</div>
                <i class="fa fa-arrow-circle-down kpi-icon text-success"></i>
            </div>
        </div>

        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="kpi-tile kpi-outward">
                <div class="kpi-label" style="color: #dc2626;">Total Outward (DC)</div>
                <div class="kpi-value" style="color: #dc2626;"><?php echo number_format($kpi['total_outward_qty'], 2); ?></div>
                <div class="kpi-sub"><?php echo $kpi['total_outward_tx']; ?> Delivery Challans</div>
                <i class="fa fa-arrow-circle-up kpi-icon text-danger"></i>
            </div>
        </div>

        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="kpi-tile kpi-net">
                <div class="kpi-label" style="color: #7c3aed;">Net Movement</div>
                <div class="kpi-value" style="color: #7c3aed;">
                    <?php 
                    $net_kpi = $kpi['total_net_movement_qty'];
                    echo ($net_kpi > 0 ? '+' : '') . number_format($net_kpi, 2); 
                    ?>
                </div>
                <div class="kpi-sub">Inward &minus; Outward</div>
                <i class="fa fa-exchange kpi-icon text-purple"></i>
            </div>
        </div>

        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="kpi-tile kpi-closing">
                <div class="kpi-label" style="color: #2563eb;">Closing Stock</div>
                <div class="kpi-value" style="color: #2563eb;"><?php echo number_format($kpi['total_closing_qty'], 2); ?></div>
                <div class="kpi-sub">As of <?php echo date('d-M-Y', strtotime($end_date)); ?></div>
                <i class="fa fa-cubes kpi-icon text-primary"></i>
            </div>
        </div>

        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="kpi-tile kpi-items">
                <div class="kpi-label" style="color: #0284c7;">Total Items</div>
                <div class="kpi-value" style="color: #0284c7;"><?php echo number_format($kpi['total_items_count']); ?></div>
                <div class="kpi-sub">In Report Scope</div>
                <i class="fa fa-tags kpi-icon text-info"></i>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs (Monthly View vs Yearly Trend) -->
    <div class="nav-tabs-custom" style="border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); overflow: hidden;">
        <ul class="nav nav-tabs">
            <li class="<?php echo ($srch_view_mode !== 'yearly_trend') ? 'active' : ''; ?>">
                <a href="#tab_monthly" data-toggle="tab" onclick="$('#srch_view_mode').val('monthly');">
                    <i class="fa fa-calendar-check-o text-primary"></i> <strong>Monthly Stock Movement [ <?php echo htmlspecialchars($month_label); ?> ]</strong>
                </a>
            </li>
            <li class="<?php echo ($srch_view_mode === 'yearly_trend') ? 'active' : ''; ?>">
                <a href="#tab_yearly" data-toggle="tab" onclick="$('#srch_view_mode').val('yearly_trend'); if(!$('#yearly_loaded').val()) { $('#frmReportSearch').submit(); }">
                    <i class="fa fa-line-chart text-purple"></i> <strong>12-Month Annual Trend [ <?php echo substr($srch_month, 0, 4); ?> ]</strong>
                </a>
            </li>
        </ul>

        <input type="hidden" id="yearly_loaded" value="<?php echo isset($yearly_matrix) ? '1' : ''; ?>">

        <div class="tab-content" style="padding: 16px;">
            <!-- Tab 1: Monthly Statement -->
            <div class="tab-pane <?php echo ($srch_view_mode !== 'yearly_trend') ? 'active' : ''; ?>" id="tab_monthly">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped table-report" id="tblItemInwardOutward" style="width:100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 40px;">#</th>
                                <th style="min-width: 140px;">Item Code</th>
                                <th style="min-width: 250px;">Item Description</th>
                                <th class="text-center" style="width: 60px;">UOM</th>
                                <th class="text-right" style="min-width: 100px;">Opening Qty</th>
                                <th class="text-right" style="min-width: 110px;">Inward Qty</th>
                                <th class="text-right" style="min-width: 110px;">Outward (DC)</th>
                                <th class="text-right" style="min-width: 100px;">Net Change</th>
                                <th class="text-right" style="min-width: 110px;">Closing Qty</th>
                                <th class="text-center" style="width: 70px;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($records)): ?>
                                <?php foreach ($records as $idx => $r): ?>
                                    <tr>
                                        <td class="text-center text-muted" style="font-size: 11px;"><?php echo ($idx + 1); ?></td>
                                        <td style="font-weight: 700; color: #1e293b;">
                                            <code><?php echo htmlspecialchars($r['item_code']); ?></code>
                                        </td>
                                        <td><?php echo htmlspecialchars($r['item_desc']); ?></td>
                                        <td class="text-center"><span class="label label-default"><?php echo htmlspecialchars($r['uom']); ?></span></td>
                                        
                                        <!-- Opening Qty -->
                                        <td class="text-right">
                                            <span class="badge-opening"><?php echo number_format($r['opening_qty'], 2); ?></span>
                                        </td>

                                        <!-- Inward Qty -->
                                        <td class="text-right">
                                            <?php if ($r['inward_qty'] > 0): ?>
                                                <a href="javascript:void(0);" class="badge-inward view-tx-btn" 
                                                   onclick="showItemVouchers('<?php echo htmlspecialchars(addslashes($r['item_code'])); ?>', 'inward');"
                                                   title="Click to view Inward vouchers">
                                                    <i class="fa fa-arrow-down"></i> <?php echo number_format($r['inward_qty'], 2); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted" style="font-size: 12px;">0.00</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Outward Qty -->
                                        <td class="text-right">
                                            <?php if ($r['outward_qty'] > 0): ?>
                                                <a href="javascript:void(0);" class="badge-outward view-tx-btn"
                                                   onclick="showItemVouchers('<?php echo htmlspecialchars(addslashes($r['item_code'])); ?>', 'outward');"
                                                   title="Click to view Delivery Challans">
                                                    <i class="fa fa-arrow-up"></i> <?php echo number_format($r['outward_qty'], 2); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted" style="font-size: 12px;">0.00</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Net Movement -->
                                        <td class="text-right">
                                            <?php 
                                             $net = $r['net_movement_qty'];
                                            if ($net > 0) {
                                                echo '<span class="net-positive">+' . number_format($net, 2) . '</span>';
                                            } elseif ($net < 0) {
                                                echo '<span class="net-negative">' . number_format($net, 2) . '</span>';
                                            } else {
                                                echo '<span class="net-neutral">0.00</span>';
                                            }
                                            ?>
                                        </td>

                                        <!-- Closing Qty -->
                                        <td class="text-right">
                                            <span class="badge-closing"><?php echo number_format($r['closing_qty'], 2); ?></span>
                                        </td>

                                        <!-- Actions -->
                                        <td class="text-center">
                                            <button type="button" class="btn btn-xs btn-default" 
                                                    onclick="showItemVouchers('<?php echo htmlspecialchars(addslashes($r['item_code'])); ?>', 'all');" 
                                                    title="View all transactions for this item">
                                                <i class="fa fa-list-alt text-primary"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted" style="padding: 30px;">
                                        <i class="fa fa-info-circle fa-2x" style="color: #94a3b8;"></i><br><br>
                                        No item movement transactions found for <strong><?php echo htmlspecialchars($month_label); ?></strong> matching the current filter.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">TOTALS:</th>
                                <th class="text-right"><?php echo number_format($kpi['total_opening_qty'], 2); ?></th>
                                <th class="text-right" style="color: #34d399 !important;"><?php echo number_format($kpi['total_inward_qty'], 2); ?></th>
                                <th class="text-right" style="color: #f87171 !important;"><?php echo number_format($kpi['total_outward_qty'], 2); ?></th>
                                <th class="text-right" style="color: #c084fc !important;">
                                    <?php echo ($kpi['total_net_movement_qty'] > 0 ? '+' : '') . number_format($kpi['total_net_movement_qty'], 2); ?>
                                </th>
                                <th class="text-right" style="color: #60a5fa !important;"><?php echo number_format($kpi['total_closing_qty'], 2); ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Yearly Matrix Trend -->
            <div class="tab-pane <?php echo ($srch_view_mode === 'yearly_trend') ? 'active' : ''; ?>" id="tab_yearly">
                <?php if (isset($yearly_matrix) && !empty($yearly_matrix)): ?>
                    <div class="alert alert-info" style="border-radius: 6px;">
                        <i class="fa fa-info-circle"></i> Showing 12-month inward and outward (Delivery Challan) movement for Year <strong><?php echo htmlspecialchars($trend_year); ?></strong>.
                        Each month column displays: <span class="badge-inward" style="padding: 2px 6px; font-size: 11px;">Inward</span> / <span class="badge-outward" style="padding: 2px 6px; font-size: 11px;">Outward</span>.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-report" id="tblYearlyTrend" style="width: 100%; font-size: 11px;">
                            <thead>
                                <tr>
                                    <th style="min-width: 110px;">Item Code</th>
                                    <th style="min-width: 160px;">Description</th>
                                    <th style="width: 45px;">UOM</th>
                                    <?php 
                                    $m_names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                    foreach ($m_names as $mn): ?>
                                        <th class="text-center" style="min-width: 80px;"><?php echo $mn; ?><br><small style="color: #94a3b8;">In / Out</small></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($yearly_matrix as $y_code => $y_row): ?>
                                    <tr>
                                        <td style="font-weight: 700;"><code><?php echo htmlspecialchars($y_code); ?></code></td>
                                        <td><?php echo htmlspecialchars($y_row['item_desc']); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($y_row['uom']); ?></td>
                                        <?php for ($m = 1; $m <= 12; $m++): 
                                            $in_val = $y_row['months'][$m]['in'];
                                            $out_val = $y_row['months'][$m]['out'];
                                        ?>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <?php if ($in_val > 0 || $out_val > 0): ?>
                                                    <span style="color: #059669; font-weight: 700;"><?php echo ($in_val > 0) ? number_format($in_val, 0) : '0'; ?></span>
                                                    /
                                                    <span style="color: #dc2626; font-weight: 700;"><?php echo ($out_val > 0) ? number_format($out_val, 0) : '0'; ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endfor; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center" style="padding: 40px;">
                        <p class="text-muted"><i class="fa fa-spinner fa-spin fa-2x"></i><br><br>Click the button below to load the 12-month trend matrix for <?php echo substr($srch_month, 0, 4); ?>.</p>
                        <button type="button" class="btn btn-primary" onclick="$('#srch_view_mode').val('yearly_trend'); $('#frmReportSearch').submit();">
                            <i class="fa fa-line-chart"></i> Load <?php echo substr($srch_month, 0, 4); ?> Trend Data
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</section>

<!-- Item Voucher Details Modal -->
<div class="modal fade" id="modalItemDetails" tabindex="-1" role="dialog" aria-labelledby="modalItemDetailsTitle">
    <div class="modal-dialog modal-lg" role="document" style="width: 85%;">
        <div class="modal-content" style="border-radius: 8px; overflow: hidden;">
            <div class="modal-header" style="background: #1e293b; color: #fff;">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8; color: #fff;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalItemDetailsTitle">
                    <i class="fa fa-list-alt text-primary"></i> Item Transaction Drilldown: 
                    <span id="modalItemCode" class="label label-primary" style="font-size: 14px;"></span>
                    <small id="modalMonth" style="color: #94a3b8; margin-left: 10px;"></small>
                </h4>
            </div>
            <div class="modal-body" style="background: #f8fafc; padding: 20px;">
                <!-- Loading indicator -->
                <div id="modalLoading" class="text-center" style="padding: 30px;">
                    <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="text-muted" style="margin-top: 10px;">Fetching vouchers for this month...</p>
                </div>

                <!-- Content area -->
                <div id="modalContent" style="display: none;">
                    <div class="row">
                        <!-- Inward Vouchers Card -->
                        <div class="col-md-6" id="cardInwards">
                            <div class="box box-success" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                                <div class="box-header with-border" style="background: #ecfdf5;">
                                    <h4 class="box-title text-success" style="font-size: 14px; font-weight: 700;">
                                        <i class="fa fa-arrow-circle-down"></i> Purchase Inward Entries (<span id="inwardCount">0</span>)
                                    </h4>
                                </div>
                                <div class="box-body table-responsive" style="padding: 0; max-height: 380px; overflow-y: auto;">
                                    <table class="table table-bordered table-striped table-condensed" style="font-size: 12px; margin-bottom: 0;">
                                        <thead style="background: #f1f5f9;">
                                            <tr>
                                                <th>Date</th>
                                                <th>Inward No</th>
                                                <th>Supplier</th>
                                                <th class="text-right">Qty</th>
                                                <th class="text-right">Rate</th>
                                                <th class="text-right">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody id="inwardRows"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Outward Delivery Challans Card -->
                        <div class="col-md-6" id="cardOutwards">
                            <div class="box box-danger" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                                <div class="box-header with-border" style="background: #fef2f2;">
                                    <h4 class="box-title text-danger" style="font-size: 14px; font-weight: 700;">
                                        <i class="fa fa-arrow-circle-up"></i> Delivery Challans (Outward) (<span id="outwardCount">0</span>)
                                    </h4>
                                </div>
                                <div class="box-body table-responsive" style="padding: 0; max-height: 380px; overflow-y: auto;">
                                    <table class="table table-bordered table-striped table-condensed" style="font-size: 12px; margin-bottom: 0;">
                                        <thead style="background: #f1f5f9;">
                                            <tr>
                                                <th>Date</th>
                                                <th>DC No</th>
                                                <th>Customer</th>
                                                <th class="text-right">Qty</th>
                                                <th class="text-center">UOM</th>
                                            </tr>
                                        </thead>
                                        <tbody id="outwardRows"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background: #fff; border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>
