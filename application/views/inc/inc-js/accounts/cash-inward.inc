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
<link
  rel="stylesheet"
  href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"
/>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<style>
  .select2-container {
    width: 100% !important;
  }
  .select2-container--default .select2-selection--single {
    height: 34px !important;
    padding: 6px 12px !important;
    border: 1px solid #d2d6de !important;
    border-radius: 0 !important;
    background-color: #fff !important;
  }
  .select2-selection__rendered {
    line-height: 22px !important;
  }
  .select2-selection__arrow {
    height: 32px !important;
  }
  .select2-container .select2-selection--single .select2-selection__rendered {
    padding-left: 0 !important;
    padding-right: 0 !important;
    height: auto !important;
  }
</style>
<script>
  jQuery(function ($) {
    $("#add_modal .select2").select2({
      dropdownParent: $("#add_modal"),
      width: "100%",
    });
    $("#edit_modal .select2").select2({
      dropdownParent: $("#edit_modal"),
      width: "100%",
    });

    $("#srch_enquiry_no").autocomplete({
      source: function (request, response) {
        $.ajax({
          url: "<?php echo base_url('tender/tender_enquiry_id_search'); ?>",
          type: "POST",
          data: { search: request.term },
          dataType: "json",
          success: function (data) {
            console.log(data);
            response(data);
          },
        });
      },
      minLength: 1,
      select: function (event, ui) {
        console.log(ui);
      },
    });

    $("#add_modal").on("shown.bs.modal", function () {
      $("#tender_enquiry_id").autocomplete({
        source: function (request, response) {
          $.ajax({
            url: "<?php echo base_url('accounts/tender_enquiry_id_search'); ?>",
            type: "POST",
            data: {
              search: request.term,
              company_id: $("#add_modal #company_id").val()
            },
            dataType: "json",
            success: function (data) {
              response(data);
            },
          });
        },
        minLength: 1,
        appendTo: "#add_modal",
        select: function (event, ui) {
          console.log(ui.item);

          $("#tender_enquiry_id").val(ui.item.label);

          // Example hidden field
          $("#tender_enquiry_hidden").val(ui.item.value);

          return false;
        },
      });
    });

    $("#edit_modal").on("shown.bs.modal", function () {
      $("#edit_tender_enquiry_id").autocomplete({
        source: function (request, response) {
          $.ajax({
            url: "<?php echo base_url('accounts/tender_enquiry_id_search'); ?>",
            type: "POST",
            data: {
              search: request.term,
              company_id: $("#edit_modal #company_id").val()
            },
            dataType: "json",
            success: function (data) {
              response(data);
            },
          });
        },
        minLength: 1,
        appendTo: "#edit_modal",
        select: function (event, ui) {
          console.log(ui.item);

          $("#edit_tender_enquiry_id").val(ui.item.label);
          $("#edit_tender_enquiry_hidden").val(ui.item.value);

          return false;
        },
      });
    });

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
          opt1.text("Select Inward From");
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
          opt1.text("Select Inward From");
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
        data: { tbl: "cash_inward_info", id: $(this).val() },
        success: function (d1) {
          //  alert(d.franchise_type_id);
          $("#edit_modal #cash_inward_id").val(d1.cash_inward_id);
          //$('#edit_modal #agent_id').val(d1.agent_id) ;
          $("#edit_modal #company_id")
            .val(d1.company_id)
            .trigger("change.select2");
          $("#edit_modal #edit_tender_enquiry_id").val(d1.enquiry_name);
          $("#edit_modal #edit_tender_enquiry_hidden").val(d1.tender_enquiry_id);
          $("#edit_modal #voucher_type_id").val(d1.voucher_type_id);
          $("#edit_modal #inward_date").val(d1.inward_date);
          $("#edit_modal #vno").val(d1.vno);
          $("#edit_modal #ac_type").val(d1.ac_type);
          $("#edit_modal #account_head_id").val(d1.account_head_id);
          //$('#edit_modal #sub_account_head_id').val(d1.sub_account_head_id);
          $("#edit_modal #amount").val(d1.amount);
          $("#edit_modal #remarks").val(d1.remarks);
          $("#edit_modal input:radio[value=" + d1.status + "]").prop(
            "checked",
            true,
          );

          $("#edit_modal #company_id").trigger("change");

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
            success: function (q) {
              var opt1 = $("<option />");
              opt1.val("");
              opt1.text("Select Inward From");
              $("#edit_modal #sub_account_headlvl3_id").append(opt1);

              $.each(q, function (key, info) {
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
          data: { tbl: "cash_intward_info_delet", id: $(this).val() },
          success: function (d) {
            alert(d);
            location.reload();
          },
        });
      }
    });
  });
</script>
