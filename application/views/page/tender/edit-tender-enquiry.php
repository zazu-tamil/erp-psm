<?php include_once(VIEWPATH . 'inc/header.php'); ?>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Tender</a></li>
        <li class="active">Edit Tender Enquiry</li>
    </ol>
</section>

<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Edit Tender Enquiry</h3>
        </div>

        <form method="post" action="" id="frmadd">
            <div class="box-body">
                <input type="hidden" name="mode" value="Edit" />
                <input type="hidden" name="tender_enquiry_id" value="<?php echo (int)$tender_enquiry_id; ?>" />

                <!-- Tender Details -->
                <fieldset>
                    <legend class="text-light-blue">Tender Enquiry Details</legend>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Company <span class="text-danger">*</span></label>
                            <?php echo form_dropdown('company_id', $company_opt, set_value('company_id', $main_record['company_id']), 'id="srch_company_id" class="form-control select2" required'); ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Customer</label>
                            <?php echo form_dropdown('customer_id', $customer_opt, set_value('customer_id', $main_record['customer_id']), 'id="srch_customer_id" class="form-control select2"'); ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Enquiry No</label>
                            <input type="text" name="enquiry_no" class="form-control" value="<?php echo htmlspecialchars(set_value('enquiry_no', $main_record['enquiry_no'])); ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Company S.No</label>
                            <input type="text" name="company_sno" class="form-control" placeholder="e.g., 001"
                                value="<?php echo htmlspecialchars(set_value('company_sno', $main_record['company_sno'])); ?>">
                        </div>
                     
                        <div class="form-group col-md-4">
                            <label>Customer S.No</label>
                            <input type="text" name="customer_sno" class="form-control" placeholder="e.g., 001"
                                value="<?php echo htmlspecialchars(set_value('customer_sno', $main_record['customer_sno'])); ?>">
                        </div>
                     
                        <div class="form-group col-md-4">
                            <label>Enquiry Date</label>
                            <input type="date" name="enquiry_date" class="form-control" value="<?php echo set_value('enquiry_date', $main_record['enquiry_date']); ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Opening Date & Time</label>
                            <input type="datetime-local" name="opening_date" class="form-control" value="<?php echo $main_record['opening_date'] ? date('Y-m-d\TH:i', strtotime($main_record['opening_date'])) : ''; ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Closing Date & Time</label>
                            <input type="datetime-local" name="closing_date" class="form-control" value="<?php echo $main_record['closing_date'] ? date('Y-m-d\TH:i', strtotime($main_record['closing_date'])) : ''; ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Tender Enquiry Status</label>
                            <?php echo form_dropdown('tender_status',  $tender_status_opt, set_value('tender_status', $main_record['tender_status']), 'class="form-control"'); ?>
                        </div>
                    
                        <div class="form-group col-md-4">
                            <label>Status</label>
                            <?php echo form_dropdown('status', $status_opt, set_value('status', $main_record['status']), 'class="form-control select2"'); ?>
                        </div>
                    </div>
                </fieldset>

                <!-- Item Details -->
                <fieldset class="mt-4">
                    <legend class="text-light-blue">Item Details</legend>
                    <div class="item-details-container">
                        <div class="grid-header">
                            <div>Category / Item</div>
                            <div>Description</div>
                            <div>UOM</div>
                            <div>Qty</div>
                            <div class="text-center">Action</div>
                        </div>
                        <div id="item_rows">
                            <?php if (!empty($item_list)): ?>
                                <?php foreach ($item_list as $idx => $item): ?>
                                    <div class="item-row" 
                                         data-rec-id="<?php echo (int)$item['tender_enquiry_item_id']; ?>"
                                         data-category-id="<?php echo (int)$item['category_id']; ?>"
                                         data-item-id="<?php echo (int)$item['item_id']; ?>"
                                         data-item-name="<?php echo htmlspecialchars($item['item_name'] ?? ''); ?>"
                                         data-item-desc="<?php echo htmlspecialchars($item['item_desc'] ?? ''); ?>"
                                         data-item-uom="<?php echo htmlspecialchars($item['uom'] ?? ''); ?>">
                                        
                                        <div class="cat-item-block">
                                            <select name="category_id[]" class="form-control category-select">
                                                <option value="">Select Category</option>
                                                <?php foreach ($category_opt as $cat_id => $cat_name): ?>
                                                    <?php if ($cat_id === '') continue; ?>
                                                    <option value="<?php echo $cat_id; ?>" 
                                                            <?php echo ($cat_id == $item['category_id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($cat_name); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <select name="item_id[]" class="form-control item-select mt-2">
                                                <option value="">Select Item</option>
                                                <?php if (!empty($item['item_id'])): ?>
                                                    <option value="<?php echo (int)$item['item_id']; ?>" selected>
                                                        <?php echo htmlspecialchars($item['item_name']); ?>
                                                    </option>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="item-desc-block">
                                            <textarea name="item_desc[]" class="form-control" rows="3"><?php echo htmlspecialchars($item['item_desc'] ?? ''); ?></textarea>
                                        </div>

                                        <div class="uom-block">
                                            <select name="uom[]" class="form-control">
                                                <option value="">—</option>
                                                <?php foreach ($uom_opt as $uom_val => $uom_name): ?>
                                                    <option value="<?php echo htmlspecialchars($uom_val); ?>" 
                                                            <?php echo ($uom_val == $item['uom']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($uom_name); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="qty-block">
                                            <input type="number" step="0.01" name="qty[]" class="form-control" 
                                                   value="<?php echo (float)$item['qty']; ?>" min="0">
                                        </div>

                                        <div class="action-block">
                                            <button type="button" class="btn-remove-row">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>

                                        <input type="hidden" name="tender_enquiry_item_id[]" 
                                               value="<?php echo (int)$item['tender_enquiry_item_id']; ?>">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="item-row" data-rec-id="0">
                                    <div class="cat-item-block">
                                        <select name="category_id[]" class="form-control category-select">
                                            <option value="">Select Category</option>
                                            <?php foreach ($category_opt as $cat_id => $cat_name): ?>
                                                <?php if ($cat_id === '') continue; ?>
                                                <option value="<?php echo $cat_id; ?>">
                                                    <?php echo htmlspecialchars($cat_name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <select name="item_id[]" class="form-control item-select mt-2">
                                            <option value="">Select Item</option>
                                        </select>
                                    </div>
                                    <div class="item-desc-block">
                                        <textarea name="item_desc[]" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="uom-block">
                                        <select name="uom[]" class="form-control">
                                            <option value="">—</option>
                                            <?php foreach ($uom_opt as $uom_val => $uom_name): ?>
                                                <option value="<?php echo htmlspecialchars($uom_val); ?>">
                                                    <?php echo htmlspecialchars($uom_name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="qty-block">
                                        <input type="number" step="0.01" name="qty[]" class="form-control" 
                                               placeholder="0.00" min="0" value="0">
                                    </div>
                                    <div class="action-block">
                                        <button type="button" class="btn-remove-row">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                    <input type="hidden" name="tender_enquiry_item_id[]" value="0">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" id="add_more" style="margin-top: 10px;">
                        <i class="fa fa-plus"></i> Add More Item
                    </button>
                </fieldset>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('tender-enquiry-list'); ?>" class="btn btn-default">Back</a>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>

