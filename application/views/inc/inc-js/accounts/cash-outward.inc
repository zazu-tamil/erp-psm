<link
	rel="stylesheet"
	href="<?php echo base_url() ?>asset/bower_components/select2/dist/css/select2.min.css"
/>
<link
	rel="stylesheet"
	href="<?php echo base_url() ?>asset/bower_components/bootstrap-daterangepicker/daterangepicker.css"
/>
<!-- bootstrap datepicker -->
<link
	rel="stylesheet"
	href="<?php echo base_url() ?>asset/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css"
/>
<link
	rel="stylesheet"
	href="<?php echo base_url() ?>asset/plugins/timepicker/bootstrap-timepicker.min.css"
/>
<!-- bootstrap datepicker -->
<script src="<?php echo base_url() ?>asset/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo base_url() ?>asset/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script src="<?php echo base_url() ?>asset/bower_components/select2/dist/js/select2.full.min.js"></script>
<script>
	jQuery(function ($) {
		$(".datepicker").datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
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

		$("#add_modal #sub_account_head_id").change(function () {
			$("#add_modal #sub_account_headlvl3_id > option").remove();
			$.ajax({
				url: "<?php echo site_url('get-data');?>",
				type: "post",
				data: { tbl: "get_sub_account_headlvl3", id: $(this).val() },
				success: function (d) {
					var opt1 = $("<option />");
					opt1.val("");
					opt1.text("Select Outward For");
					$("#add_modal #sub_account_headlvl3_id").append(opt1);

					$.each(d, function (key, info) {
						var opt1 = $("<option />");
						opt1.val(info.id);
						opt1.text(info.val);
						$("#add_modal #sub_account_headlvl3_id").append(opt1);
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

		$("#edit_modal #sub_account_head_id").change(function () {
			$("#edit_modal #sub_account_headlvl3_id > option").remove();
			$.ajax({
				url: "<?php echo site_url('get-data');?>",
				type: "post",
				data: { tbl: "get_sub_account_headlvl3", id: $(this).val() },
				success: function (d) {
					var opt1 = $("<option />");
					opt1.val("");
					opt1.text("Select Outward For");
					$("#edit_modal #sub_account_headlvl3_id").append(opt1);

					$.each(d, function (key, info) {
						var opt1 = $("<option />");
						opt1.val(info.id);
						opt1.text(info.val);
						$("#edit_modal #sub_account_headlvl3_id").append(opt1);
					});
				},
			});
		});

		$(".edit_record").click(function () {
			$.ajax({
				url: "<?php echo site_url('get-data');?>",
				type: "post",
				data: { tbl: "cash_outward_info", id: $(this).val() },
				success: function (d1) {
					//  alert(d1.cash_outward_id);
					$("#edit_modal #cash_outward_id").val(d1.cash_outward_id);
					// $('#edit_modal #agent_id').val(d1.agent_id) ;
					$("#edit_modal #voucher_type_id").val(d1.voucher_type_id);
					$("#edit_modal #outward_date").val(d1.outward_date);
					$("#edit_modal #ac_type").val(d1.ac_type);
					$("#edit_modal #vno").val(d1.vno);
					$("#edit_modal #company_id").val(d1.company_id);
					$("#edit_modal #project_id").val(d1.project_id);
					$("#edit_modal #account_head_id").val(d1.account_head_id);
					//$('#edit_modal #sub_account_headlvl3_id').val(d1.sub_account_headlvl3_id);
					$("#edit_modal #amount").val(d1.amount);
					$("#edit_modal #remarks").val(d1.remarks);
					$("#edit_modal #project_id").val(d1.project_id);
					$("#edit_modal #bill_photo_path").val(d1.bill_photo);
					$("#edit_modal input:radio[value=" + d1.bill_type + "]").prop(
						"checked",
						true
					);
					$("#edit_modal input:radio[value=" + d1.status + "]").prop(
						"checked",
						true
					);

					$("#edit_modal #sub_account_head_id > option").remove();
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

					$("#edit_modal #sub_account_headlvl3_id > option").remove();
					$.ajax({
						url: "<?php echo site_url('get-data');?>",
						type: "post",
						data: {
							tbl: "get_sub_account_headlvl3",
							id: d1.sub_account_head_id,
						},
						success: function (w) {
							var opt1 = $("<option />");
							opt1.val("");
							opt1.text("Select Outward For");
							$("#edit_modal #sub_account_headlvl3_id").append(opt1);

							$.each(w, function (key, info) {
								var opt1 = $("<option />");
								opt1.val(info.id);
								opt1.text(info.val);
								if (d1.sub_account_headlvl3_id == info.id)
									opt1.attr("selected", "selected");
								$("#edit_modal #sub_account_headlvl3_id").append(opt1);
							});
						},
					});
				},
			});
		});

		$(".del_record").click(function () {
			if (confirm("Are U sure want to delete ??")) {
				$.ajax({
					url: "<?php echo site_url('delete-record');?>",
					type: "post",
					data: { tbl: "cash_outward_info_delet", id: $(this).val() },
					success: function (d) {
						alert(d);
						location.reload();
					},
				});
			}
		});

		$("#add_modal #company_id").change(function () {
			$("#add_modal #project_id").empty();

			$.ajax({
				url: "<?php echo site_url('get-data');?>",
				type: "post",
				dataType: "json",
				data: { tbl: "get_project_with_company", id: $(this).val() },
				success: function (d) {
					var opt1 = $("<option />");
					opt1.val("");
					opt1.text("Select Project");
					$("#add_modal #project_id").append(opt1);

					$.each(d, function (key, info) {
						var opt = $("<option />");
						opt.val(info.project_id);
						opt.text(
							info.project_id +
								" => " +
								info.customer +
								" => " +
								info.branch +
								" => " +
								info.work_name +
								" => " +
								info.work_place
						);
						$("#add_modal #project_id").append(opt);
					});
				},
			});
		});
		$("#edit_modal #company_id").change(function () {
			$("#edit_modal #project_id").empty();

			$.ajax({
				url: "<?php echo site_url('get-data');?>",
				type: "post",
				dataType: "json",
				data: { tbl: "get_project_with_company", id: $(this).val() },
				success: function (d) {
					var opt1 = $("<option />");
					opt1.val("");
					opt1.text("Select Project");
					$("#edit_modal #project_id").append(opt1);

					$.each(d, function (key, info) {
						var opt = $("<option />");
						opt.val(info.project_id);
						opt.text(
							info.project_id +
								" => " +
								info.customer +
								" => " +
								info.work_name +
								" => " +
								info.work_place
						);
						$("#edit_modal #project_id").append(opt);
					});
				},
			});
		});
	});
</script>
