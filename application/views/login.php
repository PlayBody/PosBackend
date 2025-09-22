<!DOCTYPE html>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>ログイン | POSアプリ管理</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon
		============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <!-- Google Fonts
		============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Play:400,700" rel="stylesheet">
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
    <!-- main CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/main.css">
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
    <!-- forms CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/form/all-type-forms.css">
    <!-- style CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/style.css">
    <!-- responsive CSS
		============================================ -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/kiaalap/css/responsive.css">
    <!-- modernizr JS
		============================================ -->
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<div class="error-pagewrap">
    <div class="error-page-int">
        <div class="text-center m-b-md custom-login">
            <h3>Reservment管理ページ</h3>
            <p></p>
        </div>
        <div class="content-error">
            <div class="hpanel">
                <div class="panel-body">
                    <?php
                    $error = $this->session->flashdata('error');
                    if($error)
                    {
                        ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo $error; ?>
                        </div>
                    <?php } ?>
                    <form action="<?php echo base_url(); ?>login" method="post">
                        <div class="form-group">
                            <label class="control-label" for="username">Eメールアドレス</label>
                            <input type="text"  type="email" class="form-control" placeholder="メールアドレス" name="email" required >
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="password">パスワード</label>
                            <input type="password" class="form-control" placeholder="パスワード" name="password" required />
                        </div>
                        <div class="checkbox login-checkbox">
                            <label>
                                <input type="checkbox" class="i-checks"> Remember me </label>
                        </div>
                        <button type="submit" class="btn btn-success btn-block loginbtn">ログイン</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="text-center login-footer">
            <p>Copyright © 2023. All rights reserved. Cloud Devotion</p>
        </div>
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
<!-- mCustomScrollbar JS
    ============================================ -->
<script src="<?php echo base_url(); ?>assets/kiaalap/js/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="<?php echo base_url(); ?>assets/kiaalap/js/scrollbar/mCustomScrollbar-active.js"></script>
<!-- metisMenu JS
    ============================================ -->
<script src="<?php echo base_url(); ?>assets/kiaalap/js/metisMenu/metisMenu.min.js"></script>
<script src="<?php echo base_url(); ?>assets/kiaalap/js/metisMenu/metisMenu-active.js"></script>
<!-- tab JS
    ============================================ -->
<script src="<?php echo base_url(); ?>assets/kiaalap/js/tab.js"></script>
<!-- icheck JS
    ============================================ -->
<script src="<?php echo base_url(); ?>assets/kiaalap/js/icheck/icheck.min.js"></script>
<script src="<?php echo base_url(); ?>assets/kiaalap/js/icheck/icheck-active.js"></script>
<!-- plugins JS
    ============================================ -->
<script src="<?php echo base_url(); ?>assets/kiaalap/js/plugins.js"></script>
<!-- main JS
    ============================================ -->
<script src="<?php echo base_url(); ?>assets/kiaalap/js/main.js"></script>
<!-- tawk chat JS
    ============================================ -->
<script src="<?php echo base_url(); ?>assets/kiaalap/js/tawk-chat.js"></script>
</body>

</html>

