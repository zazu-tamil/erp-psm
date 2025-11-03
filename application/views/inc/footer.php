<!-- /.sticky-notification -->
<!-- <div id="zazualert" class="alert alert-success sticky-notification" role="alert">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Note:</strong>
    <div class="note-content"></div>
</div> -->
<div id="zazualert"></div>
<!-- /.sticky-notification -->
<a href="#" id="goTopBtn" class="btn btn-warning btn-sm no-print"
    style="position:fixed; bottom:70px; right:20px; border-radius:50%; z-index:9999; display:none;">
    <i class="fa fa-arrow-up"></i>
</a>
<a href="#" id="goBottomBtn" class="btn btn-warning btn-sm no-print"
    style="position:fixed; bottom:20px; right:20px; border-radius:50%; z-index:9999;">
    <i class="fa fa-arrow-down"></i>
</a>
</div>
<!-- /.content-wrapper -->
<footer class="main-footer no-print">
    <div class="row">
        <div class="col-md-4">
            Copyright &copy; <?php echo date('Y');?> <a href="">Zazu Tech</a>.</strong> All rights
            reserved.
        </div>
        <div class="col-md-4 hidden-xs text-sm text-center">Since Login Time :
            <?php echo date('h:i a',$this->session->userdata(SESS_HD . 'login_time'))?></div>
        <div class="col-md-4 pull-right hidden-xs text-sm">
            Page rendered in <strong>{elapsed_time}</strong> seconds. Memory Usage in <strong>{memory_usage}
        </div>
    </div>

</footer>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="<?php echo base_url() ?>asset/bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo base_url() ?>asset/bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
$.widget.bridge('uibutton', $.ui.button);
window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function() {
        $(this).remove();
    });
}, 4000);
</script>
<script>
$(document).ready(function() {
    // Show/hide Top button
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('#goTopBtn').fadeIn();
        } else {
            $('#goTopBtn').fadeOut();
        }
    });

    // Scroll to Top
    $('#goTopBtn').click(function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, 600);
    });

    // Scroll to Bottom
    $('#goBottomBtn').click(function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $(document).height()
        }, 600);
    });
});
</script>

<style>
.skin-blue .main-header .navbar {
    background-color: #F37817;
    /* background-color: #0083AF;  #c6baae */
    /* background-color: rgb(106, 90, 56); #C6BAAE */
}

textarea.form-control {
    height: 40px;
}


.main-header .logo-lg {
    background-color: '#f4f4f4';
    border-radius: 5px;
}



.main-header .logo {
    padding: 0px;
    height: auto;

}

.main-header {
    position: relative;
    max-height: 220px;
    z-index: 1030;

}

.carousel-inner>.item>a>img,
.carousel-inner>.item>img,
.img-responsive,
.thumbnail a>img,
.thumbnail>img {
    display: inline;
    max-width: 100%;
    height: 60px;
}


@media (max-width: 767px) {

    .main-header .logo,
    .main-header .navbar {
        width: 100%;
        float: none;
        display: table;
    }

    .main-sidebar {
        top: 30px;
    }

    .main-header .logo {
        padding-top: 1px;
        height: auto;
    }


}

.sticky-notification {
    position: fixed;
    top: 20px;
    left: 50%;
    width: 300px;
    margin-left: -150px;
    /* center horizontally */
    z-index: 1050;
    text-align: center;
    padding: 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    border-radius: 4px;
}
</style>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url() ?>asset/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Sparkline -->
<script src="<?php echo base_url() ?>asset/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- daterangepicker -->
<script src="<?php echo base_url() ?>asset/bower_components/moment/min/moment.min.js"></script>
<script src="<?php echo base_url() ?>asset/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="<?php echo base_url() ?>asset/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js">
</script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo base_url() ?>asset/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="<?php echo base_url() ?>asset/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo base_url() ?>asset/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url() ?>asset/dist/js/adminlte.min.js"></script>
<script src="<?php echo base_url() ?>asset/dist/js/demo.js"></script>

<?php
//if(isset($js) && (!empty($js))) {
    include_once( 'inc-js/' . $js);
//}
?>
</body>

</html>