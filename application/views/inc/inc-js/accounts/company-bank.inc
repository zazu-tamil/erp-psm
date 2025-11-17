<link
	rel="stylesheet"
	href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"
/>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link
	href="https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
/>
<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<script>
jQuery(function($) {
    // Initialize DataTable
    if ($.fn.DataTable.isDataTable("#company_bank_list")) {
        $("#company_bank_list").DataTable().destroy();
    }

    var table = $("#company_bank_list").DataTable({
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: true,
        language: {
            emptyTable: "No records found"
        }
    });

    // Edit Record
    $(document).on("click", ".edit_record", function() {
        let bankId = $(this).val();
        let $editModal = $("#edit_modal");

        console.log("Bank ID:", bankId); // Debug: Log the bank ID

        $.ajax({
            url: "<?= site_url('get-data'); ?>",
            type: "POST",
            data: { tbl: "company_bank_info", id: bankId },
            dataType: "json",
            success: function(data) {
                console.log("AJAX Response:", data); // Debug: Log the response

                if (data && Object.keys(data).length > 0) {
                    $editModal.find("#bank_id").val(data.bank_id || '');
                    $editModal.find("#company_edit").val(data.company || '').trigger("change");
                    $editModal.find("#bank_name_edit").val(data.bank_name || '');
                    $editModal.find("#branch_edit").val(data.branch || '');
                    $editModal.find("#account_type_edit").val(data.account_type || '').trigger("change");
                    $editModal.find("#account_no_edit").val(data.account_no || '');
                    $editModal.find("#IFSC_code_edit").val(data.IFSC_code || '');
                    $editModal.find("#remarks_edit").val(data.remarks || '');
                    $editModal.find('input:radio[name="status"][value="' + (data.status || 'Active') + '"]').prop("checked", true);

                    if (data.qr_code) {
                        let qrCodeUrl = '<?php echo base_url('bank_qr_code/'); ?>' + data.qr_code + '?t=' + new Date().getTime();
                        console.log("QR Code URL:", qrCodeUrl);
                        $editModal.find("#qr_code_img_preview")
                            .attr("src", qrCodeUrl)
                            .show();
		                } else {
		                    console.log('No footer image found');
		                    $('#edit_modal #footer_img').hide();
		                }

                    $editModal.modal('show');

                } else {
                    alert("No data found for the selected bank record.");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", xhr.status, status, error); // Debug: Log error details
                alert("Error fetching bank info: " + error);
            }
        });
    });



           $("#add_modal #qr_code").change(function() {
		        readImage(this, '#add_modal #qr_code_img_preview');
		    });

            	 $("#edit_modal #qr_code").change(function() {
		        readImage(this, '#edit_modal #qr_code_img_preview');
		    });
              function readImage(input, targetImg) {
		        if (input.files && input.files[0]) {
		            var reader = new FileReader();
		            reader.onload = function(e) {
		                $(targetImg).attr('src', e.target.result).show();
		            }
		            reader.readAsDataURL(input.files[0]);
		        }
		    }



    // Delete Record
    $(document).on("click", ".del_record", function() {
        let id = $(this).val();
        let row = $(this).closest("tr");

        if (confirm("Are you sure you want to delete this bank record?")) {
            $.ajax({
                url: "<?= site_url('delete-record'); ?>",
                type: "POST",
                data: { tbl: "company_bank_info", id: id },
                success: function(response) {
                    alert(response);
                    if ($.fn.DataTable.isDataTable("#company_bank_list")) {
                        let table = $("#company_bank_list").DataTable();
                        table.row(row).remove().draw(false);
                    } else {
                        row.fadeOut(500, function() {
                            $(this).remove();
                        });
                    }
                },
                error: function(xhr, status, error) {
                    alert("Error deleting record: " + error);
                }
            });
        }
    });
});
</script>