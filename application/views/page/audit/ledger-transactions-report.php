<?php include_once(VIEWPATH . '/inc/header.php'); ?>

<section class="content-header">
    <h1><?= html_escape($title) ?></h1>
</section>

<section class="content">
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Ledger Transactions Report</h3>
        </div>

        <div class="box-body">

            <!-- FILTER FORM -->
            <form method="post" class="form-inline mb-4">
                <div class="form-group mr-3">
                    <label class="mr-2">Ledger:</label>
                    <?= form_dropdown('srch_ledger_id', $ledger_opt, set_value('srch_ledger_id', $srch_ledger_id), 'class="form-control select2" style="width:280px;"'); ?>
                </div>

                <div class="form-group mr-3">
                    <label class="mr-2">From:</label>
                    <input type="date" name="srch_from_date" class="form-control" value="<?= html_escape($srch_from_date) ?>">
                </div>

                <div class="form-group mr-3">
                    <label class="mr-2">To:</label>
                    <input type="date" name="srch_to_date" class="form-control" value="<?= html_escape($srch_to_date) ?>">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> Generate
                </button>
            </form>

            <?php if (empty($srch_ledger_id)): ?>
                <div class="alert alert-info mt-3">
                    <i class="fa fa-info-circle"></i> Please select a ledger to view the report.
                </div>

            <?php else: ?>

                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="bg-primary">
                            <tr>
                                <th>Date</th>
                                <th>Voucher Type</th>
                                <th>Particulars</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                                <th class="text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Opening Balance -->
                            <tr class="bg-light font-weight-bold">
                                <td colspan="3" class="text-center">
                                    Opening Balance as on <?= date('d-m-Y', strtotime($srch_from_date)) ?>
                                </td>
                                <td colspan="2"></td>
                                <td class="text-right">
                                    <?= number_format(abs($opening_balance), 2) ?>
                                    <?= $opening_balance >= 0 ? '<span class="text-success">Dr</span>' : '<span class="text-danger">Cr</span>' ?>
                                </td>
                            </tr>

                            <!-- Transactions -->
                            <?php foreach ($ledger_rows as $row): ?>
                                <?php if ($row->voucher_type !== 'Opening Balance'): ?>
                                    <tr>
                                        <td><?= $row->voucher_date ? date('d-m-Y', strtotime($row->voucher_date)) : '-' ?></td>
                                        <td><?= html_escape($row->voucher_type) ?></td>
                                        <td><?= html_escape($row->ledger_name) ?></td>
                                        <td class="text-right"><?= $row->debit > 0 ? number_format($row->debit, 2) : '-' ?></td>
                                        <td class="text-right"><?= $row->credit > 0 ? number_format($row->credit, 2) : '-' ?></td>
                                        <td class="text-right <?= $row->balance >= 0 ? 'text-success' : 'text-danger' ?>">
                                            <strong><?= number_format(abs($row->balance), 2) ?></strong>
                                            <?= $row->balance >= 0 ? 'Dr' : 'Cr' ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <!-- Closing Balance -->
                            <tr class="bg-light font-weight-bold">
                                <td colspan="3" class="text-center">
                                    Closing Balance as on <?= date('d-m-Y', strtotime($srch_to_date)) ?>
                                </td>
                                <td colspan="2"></td>
                                <td class="text-right">
                                    <?= number_format(abs($closing_balance), 2) ?>
                                    <?= $closing_balance >= 0 ? '<span class="text-success">Dr</span>' : '<span class="text-danger">Cr</span>' ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </div>
    </div>
</section>

<?php include_once(VIEWPATH . '/inc/footer.php'); ?>