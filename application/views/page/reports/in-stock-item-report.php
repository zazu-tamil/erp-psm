<?php include_once(VIEWPATH . '/inc/header.php'); ?>

<section class="content-header">
    <h1><?php echo $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Report</a></li>
        <li class="active"><?php echo $title; ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <!-- Search Filter Box -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-filter"></i> Search Filter</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('in-stock-item-report'); ?>" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="srch_vendor_id">Filter by Vendor</label>
                        <?php echo form_dropdown('srch_vendor_id', ['' => 'All Vendors'] + $vendor_opt, $srch_vendor_id, 'id="srch_vendor_id" class="form-control select2" style="width: 100%;"'); ?>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="srch_keyword">Keyword Search</label>
                        <input type="text" name="srch_keyword" id="srch_keyword" class="form-control" 
                               placeholder="Item code, description, inward no, tender..." 
                               value="<?php echo htmlspecialchars($srch_keyword ?? ''); ?>">
                    </div>
                    <div class="form-group col-md-4" style="margin-top: 25px;">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
                        <a href="<?php echo site_url('in-stock-item-report/reset'); ?>" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</a>
                        <button type="submit" name="export_excel" value="1" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Export Excel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">In-Stock Line Items</span>
                    <span class="info-box-number"><?php echo number_format($tot_items_count ?? count($record_list)); ?></span>
                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                    <span class="progress-description text-sm">Distinct stock lines</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-blue">
                <span class="info-box-icon"><i class="fa fa-download"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Inward Qty</span>
                    <span class="info-box-number"><?php echo number_format($tot_inward_qty ?? 0, 2); ?></span>
                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                    <span class="progress-description text-sm">Total received quantity</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="fa fa-truck"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total DC Qty</span>
                    <span class="info-box-number"><?php echo number_format($tot_dc_qty ?? 0, 2); ?></span>
                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                    <span class="progress-description text-sm">Dispatched via Delivery Notes</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Available Stock Qty</span>
                    <span class="info-box-number"><?php echo number_format($tot_stock_qty ?? 0, 2); ?></span>
                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                    <span class="progress-description text-sm">Valuation: <?php echo number_format($tot_stock_value ?? 0, 2); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main List Box -->
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-list"></i> Available Stock Item Records</h3>
            <div class="box-tools pull-right">
                <span class="label label-success"><?php echo count($record_list); ?> In-Stock Items</span>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped" id="in_stock_item_list_table">
                <thead>
                    <tr class="bg-gray-active">
                        <th class="text-center" style="width: 40px;">S.No</th>
                        <th>Inward No &amp; Date</th>
                        <th>Vendor</th>
                        <th>Tender Info</th>
                        <th>Item Code</th>
                        <th>Item Description</th>
                        <th class="text-center">UOM</th>
                        <th class="text-right">Inward Qty</th>
                        <th class="text-right">DC Qty</th>
                        <th class="text-right">In-Stock Qty</th>
                        <th class="text-right">Rate</th>
                        <th class="text-right">Stock Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $t_inw = 0;
                    $t_dc = 0;
                    $t_stk = 0;
                    $t_val = 0;
                    foreach ($record_list as $j => $ls): 
                        $inw_qty = (float)($ls['inward_qty'] ?? 0);
                        $dc_qty = (float)($ls['dc_qty'] ?? 0);
                        $stk_qty = (float)($ls['instock_item_qty'] ?? 0);
                        $stk_val = (float)($ls['stock_value'] ?? 0);
                        $rate = (float)($ls['rate'] ?? 0);
                        $t_inw += $inw_qty;
                        $t_dc += $dc_qty;
                        $t_stk += $stk_qty;
                        $t_val += $stk_val;
                        $inw_date = (!empty($ls['inward_date']) && $ls['inward_date'] != '0000-00-00') ? date('d-m-Y', strtotime($ls['inward_date'])) : '-';
                        $source = $ls['source_type'] ?? $ls['stock_source'] ?? '';
                    ?>
                        <tr>
                            <td class="text-center"><?php echo ($j + 1); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($ls['inward_no'] ?? '-'); ?></strong>
                                <br><small class="text-muted"><i class="fa fa-calendar"></i> <?php echo $inw_date; ?></small>
                                <?php if ($source === 'Manual Stock'): ?>
                                    <br><span class="label label-info text-xs">Direct Entry</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($ls['vendor_name'] ?? '-'); ?></td>
                            <td><?php echo !empty($ls['tender_details']) ? $ls['tender_details'] : '<span class="text-muted">-</span>'; ?></td>
                            <td><strong><?php echo htmlspecialchars($ls['item_code'] ?? ''); ?></strong></td>
                            <td><?php echo htmlspecialchars($ls['item_desc'] ?? ''); ?></td>
                            <td class="text-center"><span class="badge bg-gray"><?php echo htmlspecialchars($ls['uom'] ?? '-'); ?></span></td>
                            <td class="text-right"><?php echo number_format($inw_qty, 2); ?></td>
                            <td class="text-right text-orange"><?php echo number_format($dc_qty, 2); ?></td>
                            <td class="text-right">
                                <span class="badge bg-green" style="font-size: 13px; font-weight: 600;">
                                    <?php echo number_format($stk_qty, 2); ?>
                                </span>
                            </td>
                            <td class="text-right"><?php echo number_format($rate, 2); ?></td>
                            <td class="text-right"><strong><?php echo number_format($stk_val, 2); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($record_list)): ?>
                        <tr>
                            <td colspan="12" class="text-center text-muted" style="padding: 20px;">
                                <i class="fa fa-info-circle fa-2x"></i><br>No in-stock items found for the selected criteria.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($record_list)): ?>
                <tfoot>
                    <tr class="bg-gray" style="font-weight: bold;">
                        <td colspan="7" class="text-right">Total:</td>
                        <td class="text-right"><?php echo number_format($t_inw, 2); ?></td>
                        <td class="text-right text-orange"><?php echo number_format($t_dc, 2); ?></td>
                        <td class="text-right text-green" style="font-size: 14px;"><?php echo number_format($t_stk, 2); ?></td>
                        <td class="text-right">-</td>
                        <td class="text-right text-green" style="font-size: 14px;"><?php echo number_format($t_val, 2); ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>

</section>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>

<link rel="stylesheet" href="<?= base_url() ?>asset/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<script src="<?= base_url() ?>asset/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>asset/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize Select2 if available
        if ($.fn.select2) {
            $('.select2').select2();
        }

        var tableId = "#in_stock_item_list_table";
        if ($(tableId).length) {
            if ($.fn.DataTable.isDataTable(tableId)) {
                $(tableId).DataTable().clear().destroy();
            }
            $(tableId).DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                pageLength: 25,
                order: [[4, 'asc']] // Order by Item Code
            });
        }
    });
</script>
