<?php
 /*	echo "<pre>";
    print_r($record_list);
	echo "</pre>";
  */
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Voucher</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url() ?>asset/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url() ?>asset/dist/css/AdminLTE.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    @media print{
       .noprint{
           display:none;
       }
    }
     
    .wrapper{ 
        border:0px solid black;
        padding: 0px;
    }
    .invoice { 
        border:2px solid black;
    }
    .border-right{
         border-right:1px solid black;
    }
    .border-left{
         border-left:1px solid black;
    }
    .border-top{
         border-top:1px solid black;
    }
    
    .border-bottom{
         border-bottom:1px solid black;
    }
     
    
  </style>  
</head>
<body onload="window.print();">
<div class="wrapper" > 
    <div class="container" style="padding-bottom: 30px;"> 
        <table class="table table-bordered invoice">
            <tr>
                 <td class="text-center" width="70%">
                    <div>
                        <div class="col-lg-3"><img src="<?php echo base_url() ?>/asset/images/user.jpg" class="img-circle img-md" width="50%" alt="User Image"></div>
                        <div class="col-lg-9"><h2>Voucher </h2></div>
                    </div>
                 </td>
                 <td class="text-center">
                    <b>Voucher No :- <?php echo $record_list['prefix'] . $record_list['vno']. "/" . $record_list['fyr'];?></b> <hr />
                    <b>Date :- <?php echo date('d-m-Y', strtotime($record_list['outward_date'])) ;?></b>
                 </td>
            </tr>
            <tr>
                <td colspan="2" class="text-left">Paid for <?php echo $record_list['account_head_name'];?> , <?php echo $record_list['sub_account_head_name'];?></td>
            </tr>
            <tr>
                <td colspan="2" class="text-left">Amount of <i class="fa fa-rupee"></i> <?php echo $record_list['amount'];?> <?php echo $record_list['remarks'];?></td>
            </tr>
            <tr>
                <td colspan="2" class="text-left">the sum of  <?php echo $this->cce_model->getIndianCurrency($record_list['amount']);?> </td>
            </tr>
            <tr>
                <td style="height: 80px;" class="text-center">Accounts Manager Signature</td>
                <td class="text-center">Receiver Signature</td>
            </tr> 
        </table> 
    </div> 
   <?php
	  /*
    <div class="container" style="padding-bottom: 30px;"> 
        <table class="table table-bordered invoice">
            <tr>
                 <td class="text-center" width="50%"><h2>Voucher  </h2></td>
                 <td class="text-center">
                    <b>Voucher No :- <?php echo $record_list['prefix'] . $record_list['vno']. "/" . $record_list['fyr'];?></b> <hr />
                    <b>Date :- <?php echo date('d-m-Y', strtotime($record_list['outward_date'])) ;?></b>
                 </td>
            </tr>
            <tr>
                <td colspan="2" class="text-left">Paid for <?php echo $record_list['account_head_name'];?> , <?php echo $record_list['sub_account_head_name'];?></td>
            </tr>
            <tr>
                <td colspan="2" class="text-left">Amount of <i class="fa fa-rupee"></i> <?php echo $record_list['amount'];?> <?php echo $record_list['remarks'];?></td>
            </tr>
            <tr>
                <td colspan="2" class="text-left">the sum of  <?php echo $this->cce_model->getIndianCurrency($record_list['amount']);?> </td>
            </tr>
            <tr>
                <td style="height: 80px;" class="text-center">Accounts Manager Signature</td>
                <td class="text-center">Receiver Signature</td>
            </tr> 
        </table> 
    </div> 
     
    <div class="container" style="padding-bottom: 30px;"> 
        <table class="table table-bordered invoice">
            <tr>
                 <td class="text-center" width="50%"><h2>Voucher  </h2></td>
                 <td class="text-center">
                    <b>Voucher No :- <?php echo $record_list['prefix'] . $record_list['vno']. "/" . $record_list['fyr'];?></b> <hr />
                    <b>Date :- <?php echo date('d-m-Y', strtotime($record_list['outward_date'])) ;?></b>
                 </td>
            </tr>
            <tr>
                <td colspan="2" class="text-left">Paid for <?php echo $record_list['account_head_name'];?> , <?php echo $record_list['sub_account_head_name'];?></td>
            </tr>
            <tr>
                <td colspan="2" class="text-left">Amount of <i class="fa fa-rupee"></i> <?php echo $record_list['amount'];?> <?php echo $record_list['remarks'];?></td>
            </tr>
            <tr>
                <td colspan="2" class="text-left">the sum of  <?php echo $this->cce_model->getIndianCurrency($record_list['amount']);?> </td>
            </tr>
            <tr>
                <td style="height: 80px;" class="text-center">Accounts Manager Signature</td>
                <td class="text-center">Receiver Signature</td>
            </tr> 
        </table> 
    </div> 
    
    <div class="container" style="padding-bottom: 30px;"> 
        <table class="table table-bordered invoice">
            <tr>
                 <td class="text-center" width="50%"><h2>Voucher  </h2></td>
                 <td class="text-center">
                    <b>Voucher No :- <?php echo $record_list['prefix'] . $record_list['vno']. "/" . $record_list['fyr'];?></b> <hr />
                    <b>Date :- <?php echo date('d-m-Y', strtotime($record_list['outward_date'])) ;?></b>
                 </td>
            </tr>
            <tr>
                <td colspan="2" class="text-left">Paid for <?php echo $record_list['account_head_name'];?> , <?php echo $record_list['sub_account_head_name'];?></td>
            </tr>
            <tr>
                <td colspan="2" class="text-left">Amount of <i class="fa fa-rupee"></i> <?php echo $record_list['amount'];?> <?php echo $record_list['remarks'];?></td>
            </tr>
            <tr>
                <td colspan="2" class="text-left">the sum of  <?php echo $this->cce_model->getIndianCurrency($record_list['amount']);?> </td>
            </tr>
            <tr>
                <td style="height: 80px;" class="text-center">Accounts Manager Signature</td>
                <td class="text-center">Receiver Signature</td>
            </tr> 
        </table> 
    </div> 
    */
    
    ?>
    
    
</div> 
</body>
</html>
