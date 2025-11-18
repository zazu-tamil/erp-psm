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
	jQuery(function ($) {
		// ✅ Initialize DataTable safely
		if ($.fn.DataTable.isDataTable("#account_head_for_list")) {
			$("#account_head_for_list").DataTable().destroy();
		}

		var table = $("#account_head_for_list").DataTable({
			paging: true,
			lengthChange: false,
			searching: true,
			ordering: true,
			info: true,
			autoWidth: true,
		});

		$("#add_modal #type").change(function () {
			$("#add_modal #account_head_id > option").remove();
			$.ajax({
				url: "<?php echo site_url('get-data');?>",
				type: "post",
				data: { tbl: "get-account-head-type", id: $(this).val() },
				success: function (d) {
					var opt1 = $("<option />");
					opt1.val("");
					opt1.text("Select Account Head");
					$("#add_modal #account_head_id").append(opt1);

					$.each(d, function (key, info) {
						var opt1 = $("<option />");
						opt1.val(info.account_head_id);
						opt1.text(info.account_head_name);
						$("#add_modal #account_head_id").append(opt1);
					});
				},
			});
		});

		$("#add_modal #account_head_id").change(function () {
			$("#add_modal #sub_account_head_id > option").remove();
			$.ajax({
				url: "<?php echo site_url('get-data');?>",
				type: "post",
				data: { tbl: "get-sub-account-head", id: $(this).val() },
				success: function (d) {
					var opt1 = $("<option />");
					opt1.val("");
					opt1.text("Select Sub-Account Head");
					$("#add_modal #sub_account_head_id").append(opt1);

					$.each(d, function (key, info) {
						var opt1 = $("<option />");
						opt1.val(info.sub_account_head_id);
						opt1.text(info.sub_account_head_name);
						$("#add_modal #sub_account_head_id").append(opt1);
					});
				},
			});
		});

		$("#edit_modal #type").change(function () {
			$("#edit_modal #account_head_id > option").remove();
			$.ajax({
				url: "<?php echo site_url('get-data');?>",
				type: "post",
				data: { tbl: "get-account-head-type", id: $(this).val() },
				success: function (d) {
					var opt1 = $("<option />");
					opt1.val("");
					opt1.text("Select Account Head");
					$("#edit_modal #account_head_id").append(opt1);

					$.each(d, function (key, info) {
						var opt1 = $("<option />");
						opt1.val(info.account_head_id);
						opt1.text(info.account_head_name);
						$("#edit_modal #account_head_id").append(opt1);
					});
				},
			});
		});

		$("#edit_modal #account_head_id").change(function () {
			$("#edit_modal #sub_account_head_id > option").remove();
			$.ajax({
				url: "<?php echo site_url('get-data');?>",
				type: "post",
				data: { tbl: "get-sub-account-head", id: $(this).val() },
				success: function (d) {
					var opt1 = $("<option />");
					opt1.val("");
					opt1.text("Select Sub-Account Head");
					$("#edit_modal #sub_account_head_id").append(opt1);

					$.each(d, function (key, info) {
						var opt1 = $("<option />");
						opt1.val(info.sub_account_head_id);
						opt1.text(info.sub_account_head_name);
						$("#edit_modal #sub_account_head_id").append(opt1);
					});
				},
			});
		});

		$(document).on("click", ".edit_record", function () {
			let id = $(this).val();
			//alert($(this).val());

			$.ajax({
				url: "<?php echo site_url('get-data');?>",
				type: "post",
				data: { tbl: "sub_account_head_lvl3_info", id: id },
				success: function (d1) {
					//  alert(d.franchise_type_id);
					$("#edit_modal #sub_account_headlvl3_id").val(
						d1.sub_account_headlvl3_id
					);
					$("#edit_modal #type").val(d1.type);
					$("#edit_modal #sub_account_headlvl3_name").val(
						d1.sub_account_headlvl3_name
					);
					$("#edit_modal input:radio[value=" + d1.status + "]").prop(
						"checked",
						true
					);

					$("#edit_modal #account_head_id > option").remove();
					$.ajax({
						url: "<?php echo site_url('get-data');?>",
						type: "post",
						data: { tbl: "get-account-head-type", id: d1.type },
						success: function (d) {
							var opt1 = $("<option />");
							opt1.val("");
							opt1.text("Select Account Head");
							$("#edit_modal #account_head_id").append(opt1);

							$.each(d, function (key, info) {
								var opt1 = $("<option />");
								opt1.val(info.account_head_id);
								opt1.text(info.account_head_name);
								if (d1.account_head_id == info.account_head_id)
									opt1.attr("selected", "selected");
								$("#edit_modal #account_head_id").append(opt1);
							});
						},
					});

					$.ajax({
						url: "<?php echo site_url('get-data');?>",
						type: "post",
						data: { tbl: "get-sub-account-head", id: d1.account_head_id },
						success: function (d) {
							var opt1 = $("<option />");
							opt1.val("");
							opt1.text("Select Sub-Account Head");
							$("#edit_modal #sub_account_head_id").append(opt1);

							$.each(d, function (key, info) {
								var opt1 = $("<option />");
								opt1.val(info.sub_account_head_id);
								opt1.text(info.sub_account_head_name);
								if (d1.sub_account_head_id == info.sub_account_head_id)
									opt1.attr("selected", "selected");
								$("#edit_modal #sub_account_head_id").append(opt1);
							});
						},
					});
				},
			});
		});

		$(document).on("click", ".del_record", function () {
			let id = $(this).val();
			if (confirm("Are U sure want to delete ??")) {
				$.ajax({
					url: "<?php echo site_url('accounts/delete_record');?>",
					type: "post",
					data: { tbl: "cb_sub_account_head_lvl3_info", id: id },
					success: function (d) {
						alert(d);
						location.reload();
					},
				});
			}
		});
	});
</script>
