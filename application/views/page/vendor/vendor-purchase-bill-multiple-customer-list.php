<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Vendor</a></li>
        <li class="active">Multiple Customer Supplier Purchase Invoice List</li>
    </ol>
</section>

<section class="content">
    <!-- Search Filter -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="srch_from_date">From Date</label>
                        <input type="date" name="srch_from_date" id="srch_from_date" class="form-control"
                            value="<?php echo set_value('srch_from_date', $srch_from_date); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_to_date">To Date</label>
                        <input type="date" name="srch_to_date" id="srch_to_date" class="form-control"
                            value="<?php echo set_value('srch_to_date', $srch_to_date); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Vendor</label>
                        <?php echo form_dropdown('srch_vendor_id', ['' => 'All'] + $vendor_opt, $srch_vendor_id, 'id="srch_vendor_id" class="form-control select2" style="width:100%"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <label for="">OR</label>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="srch_invoice_no">Invoice No </label>
                        <input type="text" name="srch_invoice_no" id="srch_invoice_no" class="form-control"
                            value="<?php echo set_value('srch_invoice_no', $srch_invoice_no); ?>"
                            placeholder="Search the Our Invoice No">
                    </div>

                    <div class="form-group col-md-6 text-left">
                        <br>
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- List Table -->
    <div class="box box-info">
        <div class="box-header with-border">
            <a href="<?php echo site_url('vendor-purchase-bill-multiple-customer-add'); ?>" class="btn btn-success">
                <i class="fa fa-plus-circle"></i> Add New
            </a>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered table-striped" id="vendor_multi_invoice_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 45px;">S.No</th>
                        <th style="width: 100px;">Invoice Date</th>
                        <th>Invoice No</th>
                        <th>Company</th>
                        <th>Supplier</th>
                        <th class="text-right">Amt Without Vat</th>
                        <th class="text-right">Vat Amt</th>
                        <th class="text-right">Total Amt With Vat</th>
                        <th class="text-center" style="width: 70px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($record_list)): ?>
                        <?php foreach ($record_list as $j => $row): ?>
                            <tr>
                                <td class="text-center"><?php echo ($j + 1); ?></td>
                                <td data-order="<?php echo $row['invoice_date']; ?>"><?php echo date('d-m-Y', strtotime($row['invoice_date'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['invoice_no']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['company_name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['vendor_name'] ?? '-'); ?></td>
                                <td class="text-right" data-order="<?php echo (float)($row['amt_without_vat'] ?? 0); ?>">
                                    <?php echo is_numeric($row['amt_without_vat']) ? number_format((float)$row['amt_without_vat'], 3) : htmlspecialchars($row['amt_without_vat'] ?? '-'); ?>
                                </td>
                                <td class="text-right" data-order="<?php echo (float)($row['vat_amt'] ?? 0); ?>">
                                    <?php echo is_numeric($row['vat_amt']) ? number_format((float)$row['vat_amt'], 3) : htmlspecialchars($row['vat_amt'] ?? '-'); ?>
                                </td>
                                <td class="text-right" data-order="<?php echo (float)($row['total_amt_with_vat'] ?? 0); ?>" style="font-weight: bold; color: #00a65a;">
                                    <?php echo is_numeric($row['total_amt_with_vat']) ? number_format((float)$row['total_amt_with_vat'], 3) : htmlspecialchars($row['total_amt_with_vat'] ?? '-'); ?>
                                </td>
                                <td class="text-center" style="white-space: nowrap;">
                                    <!-- EDIT -->
                                    <a href="<?php echo site_url('vendor-purchase-bill-multiple-customer-edit/' . $row['vendor_purchase_multiple_invoice_id']); ?>"
                                        class="btn btn-primary btn-xs" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <!-- DELETE -->
                                    <button value="<?php echo $row['vendor_purchase_multiple_invoice_id']; ?>"
                                        class="del_record btn btn-danger btn-xs" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="box-footer">
            <div class="form-group col-sm-6">
                <label>Total Records: <?php echo $total_records; ?></label>
            </div>
            <div class="form-group col-sm-6 text-right">
                <!-- Pagination handled by DataTables -->
            </div>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
