</div>
<!-- /.content-wrapper -->
<footer class="main-footer no-print">
    <div class="row">
        <div class="col-md-4">
            Copyright &copy; <?php echo date('Y'); ?> <a href="">Zazu Tech</a>.</strong> All rights
            reserved.
        </div>
        <div class="col-md-4 hidden-xs text-sm text-center">Since Login Time :
            <?php echo date('h:i a', $this->session->userdata(SESS_HD . 'login_time')) ?>
        </div>
        <div class="col-md-4 pull-right hidden-xs text-sm">
            Page rendered in <strong>{elapsed_time}</strong> seconds. Memory Usage in <strong>{memory_usage}
        </div>
    </div>

</footer>
</div>

<style>
    /* FULL RESPONSIVE WIDTH */
    .select2-container {
        width: 100% !important;
    }

    /* ADMINLTE INPUT MATCH */
    .select2-container--default .select2-selection--single {
        height: 34px !important;
        padding: 6px 12px !important;
        border: 1px solid #d2d6de !important;
        border-radius: 0 !important;
        background-color: #fff !important;
    }

    /* TEXT ALIGN FIX */
    /* .select2-selection__rendered {
        line-height: 22px !important;
    } */

    /* DROPDOWN ARROW */
    .select2-selection__arrow {
        height: 32px !important;
    }

    /* MOBILE TOUCH FIX */
    @media (max-width: 768px) {
        .select2-container--default .select2-selection--single {
            height: 37px !important;
            padding: 8px 12px !important;
        }
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 0 !important;
        padding-right: 0 !important;
        height: auto !important;
        /* margin-top: -4px; */
    }
</style>



<!-- jQuery 3 -->
<script src="<?php echo base_url() ?>asset/bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<!-- <script src="<?php echo base_url() ?>asset/bower_components/jquery-ui/jquery-ui.min.js"></script> -->
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url() ?>asset/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Sparkline -->
<script src="<?php echo base_url() ?>asset/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- daterangepicker -->
<script src="<?php echo base_url() ?>asset/bower_components/moment/min/moment.min.js"></script>
<script src="<?php echo base_url() ?>asset/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script
    src="<?php echo base_url() ?>asset/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo base_url() ?>asset/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="<?php echo base_url() ?>asset/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo base_url() ?>asset/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url() ?>asset/dist/js/adminlte.min.js"></script>

<?php
//if(isset($js) && (!empty($js))) {
include_once('inc-js/' . $js);
//}
?>
<script>
    $(document).ready(function () {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        setTimeout(function () {
            $(".auto-hide").fadeOut("slow");
        }, 2000);

    });
</script>
</body>

</html>