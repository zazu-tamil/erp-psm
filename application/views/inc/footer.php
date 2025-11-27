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

<!-- WhatsApp Floating Button 
<div id="whatsapp-fixed-button">
    <a href="https://api.whatsapp.com/send?phone=YOUR_NUMBER" target="_blank">
        <img src="<?php echo base_url('asset/images/mobile.png') ?>" alt="WhatsApp"
            style="width:48px;height:48px;" />
    </a>
</div>-->




<div id="whatsapp-fixed-button-right" class="hidden">
    <!-- Remove the <a> tag and use a clickable <img> with an id -->
    <img src="<?php echo base_url('asset/images/mobile.png') ?>" alt="WhatsApp"
        style="width:48px;height:48px;cursor:pointer;" id="openWhatsappModal" />
</div>


<!-- jQuery 3 -->
<script src="<?php echo base_url() ?>asset/bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo base_url() ?>asset/bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>
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
 
    });
</script>
</body>
</html>
  