<?php include_once(VIEWPATH . '/inc/header.php');

// echo '<pre>';
// print_r($_POST);
// echo '</pre>'; 

?>

<section class="content-header">
    <h1><?php echo $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-cubes"></i> Report</a></li>
        <li class="active"><?php echo $title; ?></li>
    </ol>
</section>
<style>
    .pointer {
        cursor: pointer !important;
    }
</style>
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
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title text-white">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('in-stock-item-report'); ?>" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Select Vendor</label>
                        <?php echo form_dropdown('srch_vendor_id', ['' => 'All'] + $vendor_opt, $srch_vendor_id, 'id="srch_vendor_id" class="form-control select2"'); ?>
                    </div>
                    <div class="form-group col-md-3 text-left">
                        <br>
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Show</button>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main List Box -->
    <div class="box box-success">
        <div class="box-header with-border">
            <h5 class="box-title">In Stock Item Report</h5>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered" id="in_stock_item_list_table">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th>Vendor</th>
                        <th>Item Code</th>
                        <th>Item Desc</th>
                        <th>UOM</th>
                        <th>Inward Qty</th>
                        <th>Dc Qty</th>
                        <th>In Stock Qty</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($record_list as $j => $ls): ?>
                        <tr>
                            <td class="text-center"><?php echo ($j + 1); ?></td>
                            <td><?php echo $ls['vendor_name']; ?></td>
                            <td><?php echo $ls['item_code']; ?></td>
                            <td><?php echo $ls['item_desc']; ?></td>
                            <td><?php echo $ls['uom']; ?></td>
                            <td><?php echo $ls['inward_qty']; ?></td>
                            <td><?php echo $ls['dc_qty']; ?></td>
                            <td><?php echo $ls['instock_item_qty']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($record_list)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>


</section>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>

<link rel="stylesheet"
    href="<?= base_url() ?>asset/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

<script src="<?= base_url() ?>asset/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>asset/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script>
    $(document).ready(function () {

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
                autoWidth: false
            });
        }

    });
</script>