<?php include_once(VIEWPATH . 'inc/header.php');
// echo '<pre>';
// print_r($main_record);
// echo '</pre>';
?>
<section class="content-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-file-text"></i> Tender</a></li>
        <li class="active">Edit Tender Enquiry</li>
    </ol>
</section>
<style>
.text-danger {
    color: red !important;
}
</style>
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Edit Tender Enquiry</h3>
            <a href="<?php echo site_url('tender-enquiry-list'); ?>" class="btn btn-warning pull-right"> <i
                    class="fa fa-arrow-left"></i> Back to list</a>
        </div>

        <form method="post" action="" id="frmadd" enctype="multipart/form-data">
            <div class="box-body">
                <input type="hidden" name="mode" value="Edit" />
                <input type="hidden" name="tender_enquiry_id" value="<?php echo (int) $tender_enquiry_id; ?>" />

                <!-- Tender Details -->
                <fieldset>
                    <legend class="text-light-blue">Tender Enquiry Details</legend>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Company <span class="text-danger">*</span></label>
                            <?php echo form_dropdown('company_id', $company_opt, set_value('company_id', $main_record['company_id']), 'id="srch_company_id" class="form-control select2" required'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Customer<span class="text-danger">*</span></label>
                            <?php
                            echo form_dropdown(
                                'customer_id',
                                $customer_opt,
                                set_value('customer_id', $main_record['customer_id']),
                                'id="srch_customer_id" class="form-control" required'
                            );
                            ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Customer Contact Name</label>
                            <?php
                            echo form_dropdown(
                                'customer_contact_id',
                                $customer_contact_opt,
                                set_value('customer_contact_id', $main_record['customer_contact_id']),
                                'id="customer_contact_id" class="form-control"'
                            );
                            ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Tender Name</label>
                            <input type="text" name="tender_name" class="form-control"
                                value="<?php echo htmlspecialchars(set_value('tender_name', $main_record['tender_name'])); ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label>RFQ No<span class="text-danger">*</span></label>
                            <input type="text" name="enquiry_no" class="form-control"
                                value="<?php echo htmlspecialchars(set_value('enquiry_no', $main_record['enquiry_no'])); ?>"
                                required>
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
                            <input type="date" name="enquiry_date" class="form-control"
                                value="<?php echo set_value('enquiry_date', $main_record['enquiry_date']); ?>" required>
                        </div>
                        <!-- <div class="form-group col-md-4">
                            <label>Opening Date & Time</label>
                            <input type="datetime-local" name="opening_date" class="form-control" value="<?php echo $main_record['opening_date'] ? date('Y-m-d\TH:i', strtotime($main_record['opening_date'])) : ''; ?>">
                        </div> -->
                        <div class="form-group col-md-4">
                            <label>Closing Date & Time</label>
                            <input type="datetime-local" name="closing_date" class="form-control"
                                value="<?php echo $main_record['closing_date'] ? date('Y-m-d\TH:i', strtotime($main_record['closing_date'])) : ''; ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Tender Enquiry Status<span class="text-danger">*</span></label>
                            <?php echo form_dropdown('tender_status', $tender_status_opt, set_value('tender_status', $main_record['tender_status']), 'class="form-control" required'); ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Tender Document</label>
                            <input type="file" name="tender_document" class="form-control" id="tender_document_input">
                            <small class="text-muted">All file types allowed (Max 10MB)</small>

                            <?php
                            $doc_path = $main_record['tender_document'];
                            if ($doc_path):
                                $file_url = site_url($doc_path); // Create URL path
                                $file_ext = pathinfo($doc_path, PATHINFO_EXTENSION);
                                ?>
                            <p class="mt-2">
                                <strong>Current File:</strong>
                                <?php if (in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                <a href="<?php echo $file_url; ?>" target="_blank">
                                    <img src="<?php echo $file_url; ?>" alt="Tender Document Image"
                                        style="max-height: 50px; max-width: 50px; border: 1px solid #ddd; margin-top: 5px;">
                                </a>
                                <?php else: ?>
                                <a href="<?php echo $file_url; ?>" target="_blank" class="text-blue">
                                    <i class="fa fa-file-text-o"></i> View Document
                                </a>
                                <?php endif; ?>
                            </p>
                            <?php endif; ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Status</label> <br>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="status" class="minimal" id="status_active" value="Active"
                                        <?php echo (set_value('status', $main_record['status']) == 'Active') ? 'checked' : ''; ?>>
                                    Active
                                </label>
                            </div>

                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="status" class="minimal" id="status_inactive"
                                        value="Inactive"
                                        <?php echo (set_value('status', $main_record['status']) == 'Inactive') ? 'checked' : ''; ?>>
                                    Inactive
                                </label>
                            </div>
                        </div>



                    </div>

                </fieldset>

                <!-- Item Details -->
                <fieldset class="mt-4">
                    <legend class="text-light-blue">Item Details</legend>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="">Search Item & Load Item</label> <br>
                            <button type="button" class="btn btn-info" data-toggle="modal"
                                data-target="#select_item_modal">
                                <i class="fa fa-search"></i> Search Item & Load Item
                            </button>
                        </div>
                        <?php if (!empty($item_list)) { ?>
                        <div class="col-md-5 form-group ">
                            <label for="">Download Sample Excel File To Import</label><br>
                            <a href="<?php echo base_url('asset/tender-enquiry-items-sample.xlsx'); ?>"
                                class="btn btn-warning" download>
                                <i class="fa fa-download"></i> Download Sample Excel File To Import
                            </a>
                        </div>
                        <div class="col-md-4 form-group ">
                            <label for="excelFile">Choose Excel File to Import</label>
                            <input type="file" class="form-control" id="excelFile" accept=".xls,.xlsx"
                                placeholder="Choose Excel File to Import">
                        </div>
                        <?php } ?>

                    </div>

                    <div class="item-details-container">

                        <div class="grid-header">
                            <div style="width:10%;">Item Code </div>
                            <div>Description</div>
                            <div>UOM</div>
                            <div>Qty</div>
                            <div class="text-center">Action</div>
                        </div>

                        <!-- All rows will come inside this wrapper -->
                        <div id="item_rows">
                            <?php if (!empty($item_list)) { ?>

                            <?php foreach ($item_list as $items) { ?>
                            <div class="item-row">
                                <div class="cat-item-block">
                                    <input type="text" name="item_code[]" class="form-control item_code"
                                        autocomplete="off" placeholder="Item Code"
                                        value="<?php echo $items['item_code'] ?>">
                                    <input type="hidden" name="tender_enquiry_item_id[]"
                                        class="form-control tender_enquiry_item_id"
                                        value="<?php echo $items['tender_enquiry_item_id'] ?>">
                                </div>

                                <div class="item-desc-block">
                                    <textarea name="item_desc[]" class="form-control" rows="5"
                                        placeholder="Description"><?php echo $items['item_desc']; ?></textarea>
                                </div>

                                <!-- <div class="uom-block">
                                    <?php
                                            echo form_dropdown(
                                                'uom[]',
                                                ['' => '—'] + $uom_opt,
                                                set_value('uom[]', $items['uom']),
                                                'class="form-control uom_select"'
                                            );
                                            ?>
                                </div> -->

                                
                                <div class="uom-block">  
                                    <input type="text" name="uom[]" id="uom[]" class="form-control" value="<?php echo $items['uom']; ?>">
                                </div> 
                                <div class="qty-block">
                                    <input type="number" step="any" name="qty[]" class="form-control" placeholder="Qty"
                                        value="<?php echo $items['qty']; ?>">
                                </div>

                                <div class="action-block">
                                    <button type="button" class="btn btn-danger remove-row">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>

                            </div>
                            <?php } ?>

                            <?php }   ?>
                        </div>
                         <div id="item_rows_add"></div>

                            <button type="button" class="btn btn-primary" id="add_more_added" style="margin-top:10px;">
                            <i class="fa fa-plus"></i> Add More Item
                        </button>



                    </div>


                </fieldset>
            </div>

            <div class="box-footer text-right">
                <a href="<?php echo site_url('tender-enquiry-list'); ?>" class="btn btn-warning pull-left"> <i
                        class="fa fa-arrow-left"></i> Back to list</a>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Update
                </button>
            </div>
        </form>
    </div>

    <div class="modal fade" id="select_item_modal" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="post" action="" id="frm_item_modal">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">Items List</h3>
                        <input type="hidden" name="mode" value="Select items" />
                    </div>
                    <div class="modal-body">
                        <div>
                            <label for="item_search_modal">Search Item</label>
                            <input type="text" id="item_search_modal" class="form-control" placeholder="Search Item">
                        </div>
                        <table class="table table-bordered  table-striped mt-3" id="item_search_table_modal">
                            <thead>
                                <tr>
                                    <th>Item Code</th>
                                    <th width="50%">Item Desc</th>
                                    <th>UOM</th>
                                    <th>Table</th>
                                    <th>Date</th>
                                    <th>Select</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn_cancel" data-dismiss="modal">Cancel</button>
                        <!-- <input type="button" name="btn_add_brand" value="Save" class="btn btn-primary" /> -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include_once(VIEWPATH . 'inc/footer.php'); ?>