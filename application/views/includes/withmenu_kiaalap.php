<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo empty($title) ? '' : $title." | "; ?>POSアプリ管理</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon
		============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <!-- Google Fonts
		============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/bootstrap.min.css">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/font-awesome.min.css">
    <!-- owl.carousel CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/owl.carousel.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/owl.theme.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/owl.transitions.css">
    <!-- animate CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/animate.css">
    <!-- normalize CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/normalize.css">
    <!-- meanmenu icon CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/meanmenu.min.css">
    <!-- main CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/main.css">
    <!-- educate icon CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/educate-custon-icon.css">
    <!-- morrisjs CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/morrisjs/morris.css">
    <!-- mCustomScrollbar CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/scrollbar/jquery.mCustomScrollbar.min.css">
    <!-- metisMenu CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/metisMenu/metisMenu.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/metisMenu/metisMenu-vertical.css">
    <!-- calendar CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/calendar/fullcalendar.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/calendar/fullcalendar.print.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/datapicker/datepicker3.css">
    <!-- style CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/style.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/buttons.css">
    <!-- responsive CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/responsive.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/modals.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/select2/select2.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/chosen/bootstrap-chosen.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/notifications/Lobibox.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/notifications/notifications.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/custom.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <!-- modernizr JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/vendor/modernizr-2.8.3.min.js"></script>
    <script>
        var base_url = "<?php echo base_url(); ?>";
    </script>
</head>

<body>
    <!-- Start Welcome area -->
    <div class="all-content-wrapper">
        <div class="header-advance-area">
            <div class="header-top-area">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="header-top-wraper">
                                <div class="row">
                                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12">
                                        <div class="header-top-menu tabl-d-n">
                                            <ul class="nav navbar-nav mai-top-nav">
                                                <li class="nav-item"><a href="<?php echo base_url(); ?>dashboard" class="nav-link">Resevement</a></li>
                                                <li class="nav-item dropdown res-dis-nn">
                                                    <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle">アプリ管理 <span class="angle-down-topmenu"><i class="fa fa-angle-down"></i></span></a>
                                                    <div role="menu" class="dropdown-menu animated zoomIn">
                                                        <?php if($staff['staff_auth']>3){ ?>
                                                            <!--<a href="<?php echo base_url(); ?>homemenu" class="dropdown-item">アプリメニュー</a>-->
                                                            <a href="<?php echo base_url(); ?>user" class="dropdown-item">ユーザー管理</a>
                                                            <!--<a href="<?php echo base_url(); ?>mailtext" class="dropdown-item">メール本文管理</a>-->
                                                        <?php } ?>
                                                        <!--<a href="<?php echo base_url(); ?>excelexport" class="dropdown-item">Excelエスポート</a>-->
                                                    </div>
                                                </li>
                                                <li class="nav-item dropdown res-dis-nn">
                                                    <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle">予定管理 <span class="angle-down-topmenu"><i class="fa fa-angle-down"></i></span></a>
                                                    <div role="menu" class="dropdown-menu animated zoomIn">
                                                        <a href="<?php echo base_url(); ?>epark/scheduler" class="dropdown-item">予約受付</a>
                                                    </div>
                                                </li>
                                                <li class="nav-item dropdown res-dis-nn">
                                                    <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle">商品管理 <span class="angle-down-topmenu"><i class="fa fa-angle-down"></i></span></a>
                                                    <div role="menu" class="dropdown-menu animated zoomIn">
                                                        <a href="<?php echo base_url(); ?>menu/category" class="dropdown-item">カテゴリー</a>
                                                        <a href="<?php echo base_url(); ?>menu/menu" class="dropdown-item">メニュー</a>
                                                    </div>
                                                </li>
                                                <?php if($staff['staff_auth'] >= 5){ ?>
                                                  <li class="nav-item dropdown res-dis-nn">
                                                      <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle">システム管理 <span class="angle-down-topmenu"><i class="fa fa-angle-down"></i></span></a>
                                                      <div role="menu" class="dropdown-menu animated zoomIn">
                                                          <a href="<?php echo base_url(); ?>admin/shift_status" class="dropdown-item">シフト状態管理</a>
                                                          <a href="<?php echo base_url(); ?>admin/company" class="dropdown-item">企業管理</a>
                                                      </div>
                                                  </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="header-right-info">
                                            <ul class="nav navbar-nav mai-top-nav header-right-menu">
                                                <li class="nav-item">
                                                    <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle">
                                                        <span class="admin-name"><?php echo $staff['staff_first_name'] . " " . $staff['staff_last_name']; ?></span>
                                                        <i class="fa fa-angle-down edu-icon edu-down-arrow"></i>
                                                    </a>
                                                    <ul role="menu" class="dropdown-header-top author-log dropdown-menu animated zoomIn">
                                                        <li>
                                                            <a href="<?php echo base_url(); ?>/logout">
                                                                <span class="edu-icon edu-locked author-log-ic"></span>
                                                                ログアウト
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php echo ($contents); ?>

        <div class="footer-copyright-area">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="footer-copy-right">
                            <p>Copyright © 2023. All rights reserved. Cloud Devotion</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="load-mask" style="display: none;">
        <div class="spinner-border" role="status">
        </div>
    </div>

    <!-- jquery
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/vendor/jquery-1.12.4.min.js"></script>
    <!-- bootstrap JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/bootstrap.min.js"></script>
    <!-- wow JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/wow.min.js"></script>
    <!-- price-slider JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/jquery-price-slider.js"></script>
    <!-- meanmenu JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/jquery.meanmenu.js"></script>
    <!-- owl.carousel JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/owl.carousel.min.js"></script>
    <!-- sticky JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/jquery.sticky.js"></script>
    <!-- scrollUp JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/jquery.scrollUp.min.js"></script>
    <!-- counterup JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/counterup/jquery.counterup.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/counterup/waypoints.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/counterup/counterup-active.js"></script>
    <!-- mCustomScrollbar JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/scrollbar/mCustomScrollbar-active.js"></script>
    <!-- metisMenu JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/metisMenu/metisMenu.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/metisMenu/metisMenu-active.js"></script>
    <!-- morrisjs JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/morrisjs/raphael-min.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/morrisjs/morris.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/morrisjs/morris-active.js"></script>
    <!-- morrisjs JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/sparkline/jquery.sparkline.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/sparkline/jquery.charts-sparkline.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/sparkline/sparkline-active.js"></script>
    <!-- calendar JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/calendar/moment.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/calendar/fullcalendar.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/calendar/fullcalendar-active.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/datapicker/bootstrap-datepicker.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/datapicker/datepicker-active.js"></script>
    <!-- plugins JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/plugins.js"></script>

    <script src="<?php echo base_url(); ?>assets/kiaalap/js/chosen/chosen.jquery.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/chosen/chosen-active.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/notifications/Lobibox.js"></script>
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/notifications/notification-active.js"></script>
    <!-- main JS
		============================================ -->
    <script src="<?php echo base_url(); ?>assets/kiaalap/js/main.js"></script>
</body>

</html>