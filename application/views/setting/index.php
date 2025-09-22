<style type="text/css">
    .mb-3 {
        margin-bottom: 10px;
    }
    .copy_area{
        height:100%;
        width: 100%;
    }
</style>
<script type="text/javascript">
    const base_url = "<?php echo base_url(); ?>";

    function onSubmit() {
        $('#mode').val('register');
        $('#form1').submit();
    }

</script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-cog"></i> 設定
        </h1>
    </section>
    <section class="content">

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
        <div class="row">
            <!-- left column -->
            <div class="col-xs-12">
                <!-- general form elements -->

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">設定</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <!-- form start -->

                        <?php $this->load->helper("form"); ?>
                        <form role="form" id='form1' action="<?php echo base_url() ?>setting" method="post">
                            <input type="hidden" id="mode" name="mode" value=""/>
<!--                            オペレーターの対応時間-->
                            <div class="row mb-3">
                                <label for="work_start" class="col-xs-2 col-form-label">営業開始時間：</label>
                                <div class="col-xs-10">
                                    <input type="text" class="form-control required" id="work_start" name="work_start"
                                           value="<?php if (isset($setting['work_start'])) echo $setting['work_start']; ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="work_end" class="col-xs-2 col-form-label">営業終了時間：</label>
                                <div class="col-xs-10">
                                    <input type="text" class="form-control required" id="work_end" name="work_end"
                                           value="<?php if (isset($setting['work_end'])) echo $setting['work_end']; ?>">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="company_wix_domain" class="col-xs-2 col-form-label">WixAnswerURL：</label>
                                <div class="col-xs-10">
                                    <div class="input-group field col-12 col-sm-8 col-md-5">
                                        <input type="text" class="form-control input-group-addon" id="company_wix_domain" name="company_wix_domain"
                                               value="<?php if (isset($setting['company_wix_domain'])) echo $setting['company_wix_domain']; ?>"><span class="input-group-addon" id="basic-addon2">.wixanswers.com</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="company_wix_key" class="col-xs-2 col-form-label">WixAnswerAPIキー：</label>
                                <div class="col-xs-10">
                                    <input type="text" class="form-control" id="company_wix_key" name="company_wix_key"
                                           value="<?php if (isset($setting['company_wix_key'])) echo $setting['company_wix_key']; ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="company_wix_secret" class="col-xs-2 col-form-label">WixAnswerAPIシークレット：</label>
                                <div class="col-xs-10">
                                    <input type="text" class="form-control" id="company_wix_secret" name="company_wix_secret"
                                           value="<?php if (isset($setting['company_wix_secret'])) echo $setting['company_wix_secret']; ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="company_wix_widget" class="col-xs-2 col-form-label">WixAnswerウィジェット：</label>
                                <div class="col-xs-10">
                                    <textarea style="width: 100%;outline:none !important" rows="5" id="company_wix_widget" name="company_wix_widget"><?php if (isset($setting['company_wix_widget'])) echo $setting['company_wix_widget']; ?></textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="" class="col-xs-2 col-form-label">埋め込みコード：</label>
                                <div class="col-xs-10" id="bot_script">
                                    <div class="code-copy" style="display: flex;position: absolute;right: 15px;"><div class="code-copy-message" style="display: none;">コピーしました！</div>
                                        <button class="code-copy-button" onclick="CopyToClipboard('div1');return false;"><span class="fa fa-fw fa-copy"></span></button>
                                    </div>
                                    <pre id="div1"><?php echo $bot_script;?></pre>
                                </div>
                            </div>

                            <div class="box-footer">
                                <input type="button" onclick="onSubmit();" class="btn btn-primary" value="保存"/>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>