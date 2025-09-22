<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="assets/images/favicon.png" type="image/png">
    <title><?php echo ('スタッフPOSアプリ管理画面'); ?></title>

    <link href="<?php echo base_url(); ?>assets/plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url(); ?>assets/plugins/datatables/css/jquery.dataTables-custom.css" rel="stylesheet" type="text/css">

    <!--Begin  Page Level  CSS -->
    <link href="<?php echo base_url(); ?>assets/plugins/morris-chart/morris.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet"/>
    <!--End  Page Level  CSS -->
    
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <link href="<?php echo base_url(); ?>assets/css/icons.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/responsive.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/custom.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="<?php echo base_url(); ?>assets/js/html5shiv.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/respond.min.js"></script>
    <![endif]-->

    <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
</head>

<body class="sticky-header">


<!--Start left side Menu-->
<div class="left-side sticky-left-side">

    <!--logo-->
    <div class="logo">
        <a href="index.html">Visit-pos.Net</a>
    </div>

    <div class="logo-icon text-center">
        <a href="index.html">POS</a>
    </div>
    <!--logo-->

    <div class="left-side-inner">
        <!--Sidebar nav-->
        <ul class="nav nav-pills nav-stacked custom-nav">
            <li class="<?php if ($page == 'dashboard'){  echo 'active'; } ?>"><a href="<?php echo base_url(); ?>dashboard"><i class="icon-home"></i> <span>Dashboard</span></a>
            </li>
            <li class="menu-list <?php if ($page == 'application'){ echo 'active'; } ?>"><a href="#"><i class="icon-layers"></i> <span>アプリ管理</span></a>
                <ul class="sub-menu-list">
                    <?php if($staff['staff_auth']>3){ ?>
                        <!--<li class="<?php if ($sub_page == 'company'){ echo 'active'; } ?>"><a href="<?php echo base_url(); ?>company"> 企業管理</a></li>-->
                        <!--<li class="<?php if ($sub_page == 'home_menu'){ echo 'active'; } ?>"><a href="<?php echo base_url(); ?>homemenu"> お店アプリメニュー</a></li>-->
                        <li class="<?php if ($sub_page == 'user'){ echo 'active'; } ?>"><a href="<?php echo base_url(); ?>user"> ユーザー管理</a></li>
                        <li class="<?php if ($sub_page == 'mail_text'){ echo 'active'; } ?>"><a href="<?php echo base_url(); ?>mailtext"> メール本文管理</a></li>
                    <?php } ?>
                    <li class="<?php if ($sub_page == 'excel_export'){ echo 'active'; } ?>"><a href="<?php echo base_url(); ?>excelexport"> Excelエスポート</a></li>
                </ul>
            </li>
            <li class="menu-list <?php if ($page == 'epark'){ echo 'active'; } ?>"><a href="#"><i class="icon-calendar"></i> <span>予定管理</span></a>
                <ul class="sub-menu-list">
                    <li class="<?php if ($sub_page == 'receipt'){ echo 'active'; } ?>"><a href="<?php echo base_url(); ?>epark/receipt"> 予約受付</a></li>
                    <li class="<?php if ($sub_page == 'receipt'){ echo 'active'; } ?>"><a href="<?php echo base_url(); ?>epark/scheduler">予約受付(UpdateWorking)</a></li>
                </ul>
            </li>
            <li class="menu-list <?php if ($page == 'menu'){ echo 'active'; } ?>"><a href="#"><i class="icon-calendar"></i> <span>商品管理</span></a>
                <ul class="sub-menu-list">
                    <li class="<?php if ($sub_page == 'category'){ echo 'active'; } ?>"><a href="<?php echo base_url(); ?>menu/category"> カテゴリー</a></li>
                    <li class="<?php if ($sub_page == 'menu'){ echo 'active'; } ?>"><a href="<?php echo base_url(); ?>menu/menu"> メニュー</a></li>
                </ul>
            </li>
            <!-- <li class="menu-list <?php if ($page == 'system'){ echo 'active'; } ?>"><a href="#"><i class="icon-settings"></i> <span>システム管理</span></a>
                <ul class="sub-menu-list">
                    <li class="<?php if ($sub_page == 'shift'){ echo 'active'; } ?>"><a href="<?php echo base_url(); ?>system/shift"> シフトデータエラー</a></li>
                </ul>
            </li> -->
        </ul>
        <!--End sidebar nav-->

    </div>
</div>
<!--End left side menu-->


<!-- main content start-->
<div class="main-content" >

    <!-- header section start-->
    <div class="header-section">

        <a class="toggle-btn"><i class="fa fa-bars"></i></a>

        <!--notification menu start -->
        <div class="menu-right">
            <ul class="notification-menu">
                <li>
                    <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        John Doe
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-usermenu pull-right">
                        <li> <a href="#"> <i class="fa fa-wrench"></i> Settings </a> </li>
                        <li> <a href="#"> <i class="fa fa-user"></i> Profile </a> </li>
                        <li> <a href="#"> <i class="fa fa-info"></i> Help </a> </li>
                        <li> <a href="#"> <i class="fa fa-lock"></i> Logout </a> </li>
                    </ul>
                </li>

            </ul>
        </div>
        <!--notification menu end -->

    </div>
    <!-- header section end-->


    <!--body wrapper start-->
    <div class="wrapper">

        <!--Start Page Title-->
        <div class="page-title-box">
            <h4 class="page-title"><?php echo $title; ?></h4>
            <div class="clearfix"></div>
        </div>
        <!--End Page Title-->
        <?php echo ($contents); ?>
    </div>
    <!-- End Wrapper-->


    <!--Start  Footer -->
    <footer class="footer-main"> 2022 &copy; Devotion Co., Ltd.	</footer>
    <!--End footer -->

</div>
<!--End main content -->



<!--Begin core plugin -->
<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.js"></script>
<script  src="<?php echo base_url(); ?>assets/js/jquery.slimscroll.js "></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.nicescroll.js"></script>
<script src="<?php echo base_url(); ?>assets/js/functions.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/select2/js/select2.full.min.js"></script>
<!-- End core plugin -->

<!--Begin Page Level Plugin-->
<script src="<?php echo base_url(); ?>assets/plugins/morris-chart/morris.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/morris-chart/raphael-min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>

<script>
    
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2();
    });
  </script>
<!--End Page Level Plugin-->
</body>

</html>
