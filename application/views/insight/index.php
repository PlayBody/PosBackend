<style type="text/css">
    .mb-3{
        margin-bottom: 10px;
    }
</style>

<script type="text/javascript">
    const base_url = "<?php echo base_url(); ?>";

    function faq_sync() {

        var message = "WixAnswerと同期しますか？";
        if (confirm(message)){
            $("#loading").show();
            $.ajax({
                url: base_url + 'insight/view_sync',
                type: 'post',
                dataType: 'json',
            }).done(function(res) {
                $("#loading").hide();
                if(res.ok==true){
                    alert(res.cnt+'件、WixAnswerの閲覧履歴と同期された。');
                }else{
                    alert(res.result);
                }
            });
        }
    }
    function download() {

        $("#form1").attr('action', base_url + "insight/download");
        $("#form1").submit();
    }
</script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> 記事の閲覧数
            <small>CSVダウンロード</small>
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12 text-right">
                <div class="form-group">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php
                $this->load->helper('form');
                $error = $this->session->flashdata('error');
                if ($error) {
                    ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php } ?>
                <?php
                $success = $this->session->flashdata('success');
                if ($success) {
                    ?>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php } ?>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-left: 0; margin-left:-10px; margin-right:-10px;">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">閲覧履歴</h3>
                    </div><!-- /.box-header -->
                    <div class="box-footer clearfix" style="padding: 0px;"></div>
                    <div class="box-body" style="margin-left: 20px;">
                        <form action="<?php echo base_url() ?>faq" method="POST" id="form1" name="form1">
                            <div class="row mb-3">
                                <label for="wix_url" class="col-xs-2 col-form-label">WIX URL</label>
                                <div class="col-xs-10">
                                    <?php echo $company['company_wix_domain']; ?>
<!--                                    <input type="text" class="form-control" id="wix_url" name="wix_url"-->
<!--                                           value="--><?php //echo $wix_url; ?><!--">-->
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="wix_api_key" class="col-xs-2 col-form-label">WIX APIキー</label>
                                <div class="col-xs-10">
                                    <?php echo $company['company_wix_key']; ?>
<!--                                    <input type="text" class="form-control" id="wix_api_key" name="wix_api_key"-->
<!--                                           value="--><?php //echo $wix_api_key; ?><!--">-->
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="wix_api_secret" class="col-xs-2 col-form-label">WIX APIシークレット</label>
                                <div class="col-xs-10">
                                    <?php echo $company['company_wix_secret']; ?>
<!--                                    <input type="text" class="form-control" id="wix_api_secret" name="wix_api_secret"-->
<!--                                           value="--><?php //echo $wix_api_secret; ?><!--">-->
                                </div>
                            </div>
                            <div class="row mb-3 text-red text-bold">
                                <div id="loading" style="display: none;">同期しています。
                                    <img alt="同期" src="<?php echo base_url()?>assets/images/loading.gif">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <input type="reset" class="btn btn-default" onclick="faq_sync();return false;" value="同期する">
                                <input type="submit" class="btn btn-primary" onclick="download();return false;"
                                       value="CSVダウンロード">
                            </div>
                        </form>
                        <table class="table table-hover">
                            <tr>
                                <th>ID</th>
                                <th>記事</th>
                                <th>日付</th>
                                <th>閲覧回数</th>
                            </tr>
                            <?php
                            if(!empty($list))
                            {
                                foreach($list as $record)
                                {
                                    ?>
                                    <tr>
                                        <td><?php echo $record->faq_id ?></td>
                                        <td><?php echo $record->title ?></td>
                                        <td><?php echo date('Y-m-d',strtotime($record->view_date)) ?></td>
                                        <td><?php echo $record->view_cnt ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </table>
                    </div>
                    <div class="box-footer clearfix">
                        <?php echo $this->pagination->create_links(); ?>
                    </div>
                </div><!-- /.box -->
            </div>
        </div>

    </section>
</div>
<!--<script type="text/javascript" src="--><?php //echo base_url(); ?><!--assets/js/common.js" charset="utf-8"></script>-->