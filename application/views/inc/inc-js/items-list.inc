 <script>
	jQuery(function ($) {  
		$(document).on("click", ".edit_record", function () {
			let itemId = $(this).val();

			$.ajax({
				url: "<?= site_url('get-data'); ?>",
				type: "POST",
				data: { tbl: "item_info", id: itemId },
				dataType: "json",
				success: function (d) {
					// Populate modal fields
					$("#edit_modal #item_id").val(d.item_id);
					$("#edit_modal #item_name").val(d.item_name);
					//$("#edit_modal #category_id").val(d.category_id).trigger("change");
					//$("#edit_modal #brand_id").val(d.brand_id).trigger("change");
					$("#edit_modal #item_description").val(d.item_description);
					$("#edit_modal #uom").val(d.uom);
					$("#edit_modal #item_code").val(d.item_code);
					$("#edit_modal #hsn_code").val(d.hsn_code);
					$("#edit_modal #gst").val(d.gst);

					// Display image if exists
					if (d.item_image) {
						let imgUrl = "<?= base_url(); ?>" + d.item_image;
						$("#edit_modal #item_image_preview").attr("src", imgUrl).show();
					} else {
						$("#edit_modal #item_image_preview").hide();
					}

					// Set status radio
					$(
						'#edit_modal input:radio[name="status"][value="' + d.status + '"]'
					).prop("checked", true);

					// Show modal
					$("#edit_modal").modal("show");
				},
				error: function (xhr, status, error) {
					alert("Error fetching data: " + error);
				},
			});
		});

		// --------------------------
		// DELETE RECORD
		// --------------------------
		$(document).on("click", ".del_record", function () {
			let id = $(this).val();
			let row = $(this).closest("tr");

			if (confirm("Are you sure you want to delete this item?")) {
				$.ajax({
					url: "<?= site_url('delete-record'); ?>",
					type: "POST",
					data: { tbl: "item_info_list", id: id },
					success: function (d) {
						alert(d);

						// If using DataTable
						if ($.fn.DataTable.isDataTable("#item_list")) {
							let table = $("#item_list").DataTable();
							table.row(row).remove().draw(false);
						} else {
							// fallback for non-DataTable
							row.fadeOut(500, function () {
								$(this).remove();
							});
						}
					},
					error: function (xhr, status, error) {
						alert("Error deleting: " + error);
					},
				});
			}
		});

		// --------------------------
		// IMAGE PREVIEW ON CHANGE
		// --------------------------
		function previewImage(input, previewSelector) {
			let file = input.files[0];
			if (file) {
				let url = URL.createObjectURL(file);
				$(previewSelector).attr("src", url).show();
			} else {
				$(previewSelector).hide();
			}
		}

		// New image in add form
		$("#item_image").on("change", function () {
			previewImage(this, "#item_image_preview");
		});

		// New image in edit modal
		$("#edit_modal #item_image").on("change", function () {
			previewImage(this, "#item_image_preview");
		});
	});
</script>
<script>
    $("#item_image").on("change", function () {
        var simgFile = this.files[0]; // get file object
        var simgURL = simgFile ? URL.createObjectURL(simgFile) : '';

        if (simgURL) {
            $("#item_image_preview").attr("src", simgURL).show();
        } else {
            $("#item_image_preview").hide();
        }
    });

</script>
<script>
    $("#edit_modal #item_image").on("change", function () {
        var simgFile = this.files[0]; // get file object
        var simgURL = simgFile ? URL.createObjectURL(simgFile) : '';

        if (simgURL) {
            $("#edit_modal #item_image_preview").attr("src", simgURL).show();
        } else {
            $("#edit_modal #item_image_preview").hide();
        }
    });

</script>