<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header no-print">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Reports</a></li>
        <li><a href="#">Supplier Report</a></li>
        <li class="active"><?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<style>
    /* Metric KPI Cards */
    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 18px 20px;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
        border-left: 5px solid #3c8dbc;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
    }

    .stat-card.invoices {
        border-left-color: #3c8dbc;
    }

    .stat-card.taxable {
        border-left-color: #00c0ef;
    }

    .stat-card.vat {
        border-left-color: #f39c12;
    }

    .stat-card.grand-total {
        border-left-color: #00a65a;
    }

    .stat-card .label-text {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        color: #777d8c;
        margin-bottom: 6px;
        display: block;
        letter-spacing: 0.5px;
    }

    .stat-card .value-text {
        font-size: 22px;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1.2;
    }

    .stat-card .icon-bg {
        position: absolute;
        right: 15px;
        bottom: 8px;
        font-size: 38px;
        color: rgba(0, 0, 0, 0.05);
        pointer-events: none;
    }

    /* Filter Box */
    .box-premium {
        border-radius: 10px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        background: #fff;
        border: none;
        margin-bottom: 20px;
    }

    .box-premium .box-header {
        border-bottom: 1px solid #f0f3f6;
        padding: 14px 20px;
        background: #3c8dbc;
        color: #fff;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .box-premium .box-header .box-title {
        font-size: 15px;
        font-weight: 600;
        color: #fff !important;
    }

    .box-premium .box-body {
        padding: 18px 20px;
    }

    /* Table styling */
    #tbl_supplier_invoice th {
        background-color: #f4f6f9;
        color: #334155;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        vertical-align: middle;
        border-bottom: 2px solid #dde2e8;
    }

    #tbl_supplier_invoice td {
        vertical-align: middle;
        font-size: 13px;
    }

    #tbl_supplier_invoice tfoot th {
        background-color: #edf2f7;
        font-weight: 700;
        font-size: 13px;
        border-top: 2px solid #cbd5e1;
    }

    /* Bill Type Badges */
    .badge-bill-type {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-supp-bill {
        background-color: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .badge-local-bill {
        background-color: #f3e8ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
    }

    .badge-dp-bill {
        background-color: #ccfbf1;
        color: #0f766e;
        border: 1px solid #99f6e4;
    }

    .badge-customs-bill {
        background-color: #ffedd5;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }

    .select2-container .select2-selection--single {
        height: 34px !important;
        border-radius: 4px !important;
        border: 1px solid #d2d6de !important;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
        padding-left: 10px !important;
    }

    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }

    /* Print header */
    .print-header {
        display: none;
    }

    @media print {

        .no-print,
        .main-header,
        .main-sidebar,
        .main-footer,
        .dataTables_filter,
        .dataTables_length,
        .dataTables_paginate,
        .dataTables_info {
            display: none !important;
        }

        .print-header {
            display: block !important;
            margin-bottom: 15px;
        }

        .content-wrapper {
            margin-left: 0 !important;
            padding-top: 0 !important;
            background: #fff !important;
        }

        .box-premium {
            box-shadow: none !important;
            border: 1px solid #ccc !important;
        }

        #tbl_supplier_invoice {
            width: 100% !important;
            font-size: 11px !important;
        }

        #tbl_supplier_invoice th:last-child,
        #tbl_supplier_invoice td:last-child {
            display: none !important;
        }
    }
</style>

<section class="content">
    <!-- PRINT HEADER -->
    <div class="print-header text-center">
        <h2 style="margin: 0 0 5px 0; font-size: 20px; font-weight: bold; text-transform: uppercase;">SUPPLIER INVOICE
            REPORT</h2>
        <p style="margin: 0; font-size: 13px; color: #555;">
            <strong>Period:</strong>
            <?php echo !empty($srch_from_date) ? date('d-M-Y', strtotime($srch_from_date)) : 'Start'; ?>
            &nbsp;to&nbsp;
            <?php echo !empty($srch_to_date) ? date('d-M-Y', strtotime($srch_to_date)) : 'Today'; ?>
            &nbsp;|&nbsp;
            <strong>Supplier:</strong> <?php echo htmlspecialchars($selected_vendor_name); ?>
            &nbsp;|&nbsp;
            <strong>Bill Type:</strong> <?php echo htmlspecialchars($selected_bill_type_label); ?>
        </p>
        <hr style="margin: 10px 0 15px 0; border-top: 1px solid #333;">
    </div>

    <!-- SEARCH FILTER -->
    <div class="box box-premium no-print">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-filter"></i> Search Filter</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool text-white" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('dp-custom-invoice-report'); ?>" id="frmSupplierInvoice">
                <input type="hidden" name="export_excel" id="export_excel" value="0">
                <div class="row">
                    <div class="form-group col-md-2">
                        <label for="srch_from_date">From Date</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="date" name="srch_from_date" id="srch_from_date" class="form-control"
                                value="<?php echo htmlspecialchars($srch_from_date); ?>">
                        </div>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="srch_to_date">To Date</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="date" name="srch_to_date" id="srch_to_date" class="form-control"
                                value="<?php echo htmlspecialchars($srch_to_date); ?>">
                        </div>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_vendor_id">Supplier</label>
                        <select name="srch_vendor_id" id="srch_vendor_id" class="form-control select2">
                            <option value="">All Suppliers</option>
                            <?php foreach ($vendor_list as $v): ?>
                                <option value="<?php echo $v['vendor_id']; ?>" <?php echo ($srch_vendor_id == $v['vendor_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($v['vendor_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="srch_bill_type">Bill Type</label>
                        <select name="srch_bill_type" id="srch_bill_type" class="form-control select2">
                            <?php foreach ($bill_type_opt as $bKey => $bVal): ?>
                                <option value="<?php echo $bKey; ?>" <?php echo ($srch_bill_type === (string) $bKey) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($bVal); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-3 text-right" style="padding-top: 24px;">
                        <button type="submit" class="btn btn-primary" id="btnFilter" title="Search Report">
                            <i class="fa fa-search"></i> Show
                        </button>
                        <a href="<?php echo site_url('dp-custom-invoice-report/reset'); ?>" class="btn btn-default"
                            title="Reset Filters">
                            <i class="fa fa-refresh"></i> Reset
                        </a>
                        <!-- <button type="button" class="btn btn-success" id="btnExportExcel" title="Export to Excel">
                            <i class="fa fa-file-excel-o"></i> Excel
                        </button> -->
                        <button type="button" class="btn btn-info" onclick="window.print();" title="Print Report">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI METRIC CARDS -->
    <div class="row no-print">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card invoices">
                <span class="label-text">Total Bills / Invoices</span>
                <span class="value-text"><?php echo number_format($total_bills); ?></span>
                <i class="fa fa-files-o icon-bg"></i>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card taxable">
                <span class="label-text">Taxable (Excl. VAT)</span>
                <span class="value-text"><?php echo number_format($total_taxable, 3); ?></span>
                <i class="fa fa-calculator icon-bg"></i>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card vat">
                <span class="label-text">Total VAT Amount</span>
                <span class="value-text"><?php echo number_format($total_vat, 3); ?></span>
                <i class="fa fa-percent icon-bg"></i>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="stat-card grand-total">
                <span class="label-text">Total Amount (Incl. VAT)</span>
                <span class="value-text"><?php echo number_format($grand_total, 3); ?></span>
                <i class="fa fa-money icon-bg"></i>
            </div>
        </div>
    </div>

    <!-- REPORT DATA TABLE -->
    <div class="box box-premium">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-list"></i> Supplier Invoices List
                <span style="font-size: 13px; font-weight: normal; margin-left: 8px; opacity: 0.9;">
                    (<?php echo !empty($srch_from_date) ? date('d-m-Y', strtotime($srch_from_date)) : 'Start'; ?> to
                    <?php echo !empty($srch_to_date) ? date('d-m-Y', strtotime($srch_to_date)) : 'End'; ?>)
                    &mdash; <?php echo htmlspecialchars($selected_vendor_name); ?>
                    &mdash; <span
                        class="badge bg-navy"><?php echo htmlspecialchars($selected_bill_type_label); ?></span>
                </span>
            </h3>
            <div class="box-tools pull-right no-print">
                <button type="button" class="btn btn-box-tool text-white" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped table-hover" id="tbl_supplier_invoice"
                style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">S.No</th>
                        <th style="width: 85px;" class="text-center">Date</th>
                        <th style="width: 110px;" class="text-center">Bill / Inv No</th>
                        <th style="width: 120px;" class="text-center">Bill Type</th>
                        <th>Supplier Name</th>
                        <th style="width: 110px;" class="text-center">VAT / CR No</th>
                        <th>Tender / Order Ref</th>
                        <th style="width: 110px;" class="text-right">Amt W/O VAT</th>
                        <th style="width: 90px;" class="text-right">VAT Amt</th> 
                        <th style="width: 110px;" class="text-right">Total Amount</th>
                        <th style="width: 110px;" class="text-right">Grand Amount</th>
                        <!-- <th style="width: 55px;" class="text-center no-print">Action</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($records)): ?>
                        <?php $sno = 1;
                        foreach ($records as $r): ?>
                            <?php
                            $badgeClass = 'badge-supp-bill';
                            if ($r['bill_type'] === 'Local Supplier Bill') {
                                $badgeClass = 'badge-local-bill';
                            } elseif ($r['bill_type'] === 'Delivery Partner Bill') {
                                $badgeClass = 'badge-dp-bill';
                            } elseif ($r['bill_type'] === 'Customs Bill') {
                                $badgeClass = 'badge-customs-bill';
                            }
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $sno++; ?></td>
                                <td class="text-center" data-order="<?php echo $r['invoice_date']; ?>">
                                    <?php echo date('d/m/Y', strtotime($r['invoice_date'])); ?>
                                </td>
                                <td class="text-center font-weight-bold" style="font-weight: 600;">
                                    <?php echo htmlspecialchars($r['invoice_no']); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge-bill-type <?php echo $badgeClass; ?>">
                                        <?php echo htmlspecialchars($r['bill_type']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($r['vendor_name'] ?? '-'); ?></td>
                                <td class="text-center">
                                    <?php echo htmlspecialchars($r['vendor_vat_cr'] ? $r['vendor_vat_cr'] : '-'); ?></td>
                                <td><?php echo htmlspecialchars($r['tender_details'] ? $r['tender_details'] : '-'); ?></td>


                                <td class="text-right" data-order="<?php echo (float) $r['amt_wo_vat']; ?>">
                                    <?php echo number_format((float) $r['amt_wo_vat'], (int) $r['decimal_point']); ?>
                                </td>

                                <td class="text-right" data-order="<?php echo (float) $r['vat_amt']; ?>">
                                    <?php echo number_format((float) $r['vat_amt'], (int) $r['decimal_point']); ?>
                                </td>

                                <td class="text-right" data-order="<?php echo (float) $r['payable']; ?>"
                                    style="font-weight: 700; color: #00a65a;">
                                    <?php echo number_format((float) $r['payable'], (int) $r['decimal_point']); ?>
                                </td>

                                <td class="text-right" data-order="<?php echo (float) $r['grand_amount']; ?>"
                                    style="font-weight: 700; color: #00a65a;">
                                    <?php echo number_format((float) $r['grand_amount'], (int) $r['decimal_point']); ?>
                                </td>
                                <!-- <td class="text-center no-print">
                                    <?php if (!empty($r['edit_url'])): ?>
                                        <a href="<?php echo site_url($r['edit_url']); ?>"
                                           class="btn btn-default btn-xs" target="_blank" title="View / Edit Bill">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td> -->
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center" style="padding: 30px; color: #888;">
                                <i class="fa fa-info-circle"
                                    style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
                                No supplier bills found matching the selected filter criteria.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: 700; background-color: #edf2f7;">
                        <th colspan="7" class="text-right">
                            Total (Bills: <?php echo number_format($total_bills); ?>):
                        </th>
                        <th class="text-right"><?php echo number_format($total_taxable, 3); ?></th>
                        <th class="text-right"><?php echo number_format($total_vat, 3); ?></th>
                        <th class="text-right" style="color: #00a65a;"><?php echo number_format($total_payable, 3); ?></th>
                        <th class="text-right" style="color: #00a65a;"><?php echo number_format($grand_total, 3); ?>
                        </th>
                     </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>