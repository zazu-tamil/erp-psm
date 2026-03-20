<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Vendor</a></li>
        <li class="active"> <?php echo $title ?></li>
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

                    <div class="form-group col-md-3">
                        <label for="vendor_id">Vendor Name</label>
                        <?php echo form_dropdown('vendor_id', ['' => 'All'] + $vendor_opt, set_value('vendor_id', $vendor_id), 'id="vendor_id" class="form-control "'); ?>
                    </div>
                    <div class="form-group col-md-3 text-left">
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
            <div class="box-title">
                <h3 class="box-title text-white">InvoiceList</h3>
            </div>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <?php
        $grand_total = 0; // ✅ NEW
        
        $invoice_no_count = 0;
        ?>

        <?php
        $grand_total = 0;
        $invoice_no_count = 0;
        ?>

        <div class="box-body table-responsive">

            <table class="table table-bordered table-striped">
                <tr style="font-weight:bold;" class="bg-gray">
                    <th>S.No</th>
                    <th>Date</th>
                    <th>Invoice No</th>
                    <th>Vendor Name</th>
                    <th>Bill Type</th>
                    <th class="text-right">Amount</th>
                </tr>

                <?php foreach ($record_list as $i => $row) {

                    // ✅ Add inside loop
                    $grand_total += $row['total_amount'];
                    $invoice_no_count++;

                    ?>
                    <tr>
                        <td>
                            <?php echo  $i + 1;  ?>
                        </td>
                        <td>
                            <?php echo date('d/m/Y', strtotime($row['invoice_date'])); ?>
                        </td>
                        <td>
                            <?php echo $row['invoice_no']; ?>
                        </td>
                        <td>
                            <?php echo $row['vendor_name']; ?>
                        </td>
                        <td>
                            <?php echo $row['bill_type']; ?>
                        </td>
                        <td align="right">
                            <?php echo number_format($row['total_amount'], 3); ?>
                        </td>
                    </tr>
                <?php } ?>

                <!-- ✅ GRAND TOTAL -->
                <tr style="font-weight:bold; background:green; font-size:16px; color:#ffffff;">
                    <td colspan="2" class="text-right"> Total Invoice :</td>
                    <td>
                        <?= $invoice_no_count; ?>
                    </td>

                    <td class="text-right" colspan="4">
                        Grand Total : <?= number_format($grand_total, 3); ?>
                    </td>
                </tr>

            </table>

        </div>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>