<?php include_once(VIEWPATH . 'inc/header.php'); ?>

<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-money"></i> Transactions</a></li>
        <li class="active"><?php echo htmlspecialchars($title); ?></li>
    </ol>
</section>

<section class="content">
    <!-- Search Filter -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Search Filter</h3>
        </div>
        <div class="box-body">
            <form method="post" action="<?php echo site_url('credit-debit-note-list'); ?>" id="frmsearch">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="srch_from_date">From Date</label>
                        <input type="date" name="srch_from_date" id="srch_from_date" class="form-control"
                            value="<?php echo set_value('srch_from_date', $filters['from_date'] ?? ''); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="srch_to_date">To Date</label>
                        <input type="date" name="srch_to_date" id="srch_to_date" class="form-control"
                            value="<?php echo set_value('srch_to_date', $filters['to_date'] ?? ''); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label>Party Type</label>
                        <select name="srch_party_type" class="form-control">
                            <option value="">All</option>
                            <option value="Customer" <?php echo (isset($filters['party_type']) && $filters['party_type'] == 'Customer') ? 'selected' : ''; ?>>Customer</option>
                            <option value="Supplier" <?php echo (isset($filters['party_type']) && $filters['party_type'] == 'Supplier') ? 'selected' : ''; ?>>Supplier</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Note Type</label>
                        <select name="srch_note_type" class="form-control">
                            <option value="">All</option>
                            <option value="Credit" <?php echo (isset($filters['note_type']) && $filters['note_type'] == 'Credit') ? 'selected' : ''; ?>>Credit</option>
                            <option value="Debit" <?php echo (isset($filters['note_type']) && $filters['note_type'] == 'Debit') ? 'selected' : ''; ?>>Debit</option>
                        </select>
                    </div>

                    <div class="form-group col-md-12 text-center">
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
                        <a href="<?php echo site_url('credit-debit-note-list'); ?>" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- List Table -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <a href="<?php echo site_url('credit-debit-note-add'); ?>" class="btn btn-primary">
                <i class="fa fa-plus-circle"></i> Add New Note
            </a>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable">
                <thead class="bg-gray">
                    <tr>
                        <th>S.No</th>
                        <th>Note No</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Party Type</th>
                        <th>Party Name</th>
                        <th>Tender Enquiry No</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($records)): ?>
                        <?php $sno = $this->uri->segment(2, 0) + 1;
                        foreach ($records as $row): ?>
                            <tr>
                                <td><?php echo $sno++; ?></td>
                                <td><?php echo htmlspecialchars($row['note_no']); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($row['note_date'])); ?></td>
                                <td>
                                    <?php if($row['note_type'] == 'Credit'): ?>
                                        <span class="label label-success">Credit</span>
                                    <?php else: ?>
                                        <span class="label label-danger">Debit</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['party_type']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($row['party_type'] == 'Customer' ? $row['customer_name'] : $row['vendor_name']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['tender_no']); ?></td>
                                <td class="text-right"><?php echo number_format($row['total_amount'], 2); ?></td>
                                <td>
                                    <?php 
                                        if($row['status'] == 'Draft') $lbl = 'label-warning';
                                        elseif($row['status'] == 'Approved') $lbl = 'label-primary';
                                        elseif($row['status'] == 'Posted') $lbl = 'label-success';
                                        else $lbl = 'label-default';
                                    ?>
                                    <span class="label <?php echo $lbl; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                                </td>
                                <td>
                                    <a href="<?php echo site_url('credit-debit-note-edit/' . $row['credit_debit_note_id']); ?>" class="btn btn-xs btn-primary" title="Edit">
                                        <i class="fa fa-pencil"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-xs btn-danger" title="Delete" onclick="deleteNote(<?php echo $row['credit_debit_note_id']; ?>)">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">No Records Found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="box-footer clearfix">
            <!-- DataTables pagination used instead -->
        </div>
    </div>
</section>

<!-- DataTables -->
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/datatables/dataTables.bootstrap.css'); ?>">
<script src="<?php echo base_url('assets/plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables/dataTables.bootstrap.min.js'); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('.dataTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false
    });
});

function deleteNote(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You will not be able to recover this Credit / Debit Note!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?php echo base_url(); ?>' + 'credit-debit-note-delete/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.status == 'success') {
                        Swal.fire('Deleted!', res.msg, 'success').then(function() {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error!', res.msg, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                }
            });
        }
    });
}
</script>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>
