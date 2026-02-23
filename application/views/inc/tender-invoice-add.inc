<div class="modal-header">
    <h5 class="modal-title">Add Tender Invoice</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form id="tender-invoice-add-form" method="post" enctype="multipart/form-data" action="<?php echo base_url('tender/save_invoice'); ?>">
    <div class="modal-body">
        <input type="hidden" name="tender_id" value="<?php echo isset($tender_id) ? $tender_id : ''; ?>">

        <div class="form-group">
            <label for="invoice_number">Invoice Number</label>
            <input type="text" class="form-control" id="invoice_number" name="invoice_number" required>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="invoice_date">Invoice Date</label>
                <input type="text" class="form-control datepicker" id="invoice_date" name="invoice_date" required>
            </div>
            <div class="form-group col-md-6">
                <label for="vendor_name">Vendor</label>
                <input type="text" class="form-control" id="vendor_name" name="vendor_name" placeholder="Vendor name or code">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="amount">Amount</label>
                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
            </div>
            <div class="form-group col-md-4">
                <label for="tax">Tax</label>
                <input type="number" step="0.01" class="form-control" id="tax" name="tax">
            </div>
            <div class="form-group col-md-4">
                <label for="total">Total</label>
                <input type="number" step="0.01" class="form-control" id="total" name="total" readonly>
            </div>
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="invoice_file">Attachment (optional)</label>
            <input type="file" class="form-control-file" id="invoice_file" name="invoice_file">
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save Invoice</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var amount = document.getElementById('amount');
    var tax = document.getElementById('tax');
    var total = document.getElementById('total');

    function updateTotal(){
        var a = parseFloat(amount.value) || 0;
        var t = parseFloat(tax.value) || 0;
        total.value = (a + t).toFixed(2);
    }

    if(amount) amount.addEventListener('input', updateTotal);
    if(tax) tax.addEventListener('input', updateTotal);

    if(typeof jQuery !== 'undefined' && jQuery().datepicker){
        jQuery('.datepicker').datepicker({format: 'yyyy-mm-dd', autoclose: true});
    }
});
</script>
