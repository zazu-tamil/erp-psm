<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo PG_HEAD;?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url() ?>/asset/images/Classwall-Logo-SM.png">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url() ?>asset/dist/css/AdminLTE.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition login-page">
    <div class="login-box">

        <!-- /.login-logo -->
        <div class="login-box-body" style="border-radius: 20px;">
            <div class="login-logo">
                <!-- <h2 class="text-blue"><?php //echo PG_HEAD;?></h2> -->
                <img src="asset/images/the_aalam_logo.png" class="img-rounded img-responsive"
                    alt="<?php echo PG_HEAD;?>">
            </div>
            <div class="login-logo1 text-center">
                <!-- <h2 class="text-blue"><?php //echo PG_HEAD;?></h2> -->
                <img src="asset/images/Classwall-VS.png" class="img-rounded img-thumbnail no-border" width="40%"
                    alt="<?php echo PG_HEAD;?>">
            </div>

            <p class="login-box-msg">
                <i>&nbsp;</i>
                <!-- <i class="fa fa-user-circle-o fa-3x bg-orange-active img-rounded pad"></i> -->

                <?php if ($this->session->flashdata('session_expired')): ?>
            <div class="alert alert-warning"><?= $this->session->flashdata('session_expired'); ?></div>
            <?php elseif ($this->session->flashdata('logout_msg')): ?>
            <div class="alert alert-success"><?= $this->session->flashdata('logout_msg'); ?></div>
            <?php endif; ?>


            <?php  
                if($login === false) 
                    echo  '<div class="alert alert-danger"><span class="close" data-dismiss="alert">x</span>'. $msg.'</div>';
                echo validation_errors('<div class="alert alert-danger"><p class="close" data-dismiss="alert">x</p>','</div>'); 
                ?>
            </p>

            <form action="<?php echo site_url('login') ?>" method="post">
                <div class="form-group has-feedback">
                    <input type="text" name="user_name" class="form-control" placeholder="User Name">
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="user_pwd" placeholder="Password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8 hide">
                        <a href="#">I forgot my password</a><br>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-12">
                        <button type="submit" class="btn bg-orange-active btn-block btn-flat">Log In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->

    <!-- jQuery 3 -->
    <script src="<?php echo base_url() ?>asset/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo base_url() ?>asset/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

</body>

</html>