

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
          <b>Visit-POS</b>管理画面
        </div>
        <strong>Copyright &copy; 2021 <a href="<?php echo base_url(); ?>"></a>.</strong> All rights reserved.
    </footer>
    
    <script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/dist/js/adminlte.min.js" type="text/javascript"></script>
<!--     <script src="--><?php //echo base_url(); ?><!--assets/dist/js/pages/dashboard.js" type="text/javascript"></script>-->
    <script src="<?php echo base_url(); ?>assets/js/jquery.validate.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/js/validation.js" type="text/javascript"></script>
    
    <script src="<?php echo base_url(); ?>assets/plugins/select2/js/select2.full.min.js"></script>
<!--    <script type="text/javascript">-->
<!--        var windowURL = window.location.href;-->
<!--        pageURL = windowURL.substring(0, windowURL.lastIndexOf('/'));-->
<!--        var x= $('a[href="'+pageURL+'"]');-->
<!--            x.addClass('active');-->
<!--            x.parent().addClass('active');-->
<!--        var y= $('a[href="'+windowURL+'"]');-->
<!--            y.addClass('active');-->
<!--            y.parent().addClass('active');-->
<!--    </script>-->
    <script>
    
      $(function () {
          //Initialize Select2 Elements
          $('.select2').select2();
      });
    </script>
  </body>
</html>