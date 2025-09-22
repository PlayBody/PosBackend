<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> 企業管理
            <small>作成, 編集, 削除</small>
        </h1>
    </section>

    <section class="content">

        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
                <!-- general form elements -->


                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">企業編集</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->

                    <form role="form" method="post" id="editUser" role="form">
                        <input type="hidden" value="save" name='mode'>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">企業名：</label>
                                        <input type="text" class="form-control" id="company_name" placeholder=""
                                               name="company_name" value="<?php echo $company['company_name']; ?>"
                                               maxlength="128">
                                        <input type="hidden" value="<?php echo $company['company_id']; ?>"
                                               name="company_id" id="company_id"/>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_email">メールアドレス：</label>
                                        <input type="email" class="form-control" id="company_email" placeholder=""
                                               name="company_email" value="<?php echo $company['company_email']; ?>"
                                               maxlength="128">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="work_start">営業開始時間：</label>
                                        <input type="text" class="form-control required" id="work_start"
                                               name="work_start"
                                               value="<?php if (isset($company['work_start'])) echo $company['work_start']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="work_end">営業終了時間：</label>
                                        <input type="text" class="form-control required" id="work_end"
                                               name="work_end"
                                               value="<?php if (isset($company['work_end'])) echo $company['work_end']; ?>">
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="company_wix_domain">WixAnswerURL：</label>
                                        <div class="input-group field col-12 col-sm-8 col-md-5">
                                            <input type="text" class="form-control input-group-addon"
                                                   id="company_wix_domain" name="company_wix_domain"
                                                   value="<?php if (isset($company['company_wix_domain'])) echo $company['company_wix_domain']; ?>"><span
                                                    class="input-group-addon"
                                                    id="basic-addon2">.wixanswers.com</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_wix_key">WixAnswerAPIキー：</label>
                                        <input type="text" class="form-control" id="company_wix_key"
                                               name="company_wix_key"
                                               value="<?php if (isset($company['company_wix_key'])) echo $company['company_wix_key']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_wix_secret">WixAnswerAPIシークレット：</label>
                                        <input type="text" class="form-control" id="company_wix_secret"
                                               name="company_wix_secret"
                                               value="<?php if (isset($company['company_wix_secret'])) echo $company['company_wix_secret']; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="company_wix_widget">WixAnswerウィジェット：</label>
                                        <textarea style="width: 100%;outline:none !important" rows="5" id="company_wix_widget"
                                                  name="company_wix_widget"><?php if (isset($company['company_wix_widget'])) echo $company['company_wix_widget']; ?></textarea>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">埋め込みコード：</label>
                                        <div id="bot_script">
                                            <div class="code-copy"
                                                 style="display: flex;position: absolute;right: 15px;">
                                                <div class="code-copy-message" style="display: none;">コピーしました！</div>
                                                <button class="code-copy-button"
                                                        onclick="CopyToClipboard('div1');return false;"><span
                                                            class="fa fa-fw fa-copy"></span></button>
                                            </div>
                                            <pre id="div1" style="min-height: 60px"><?php echo (isset($bot_script))? $bot_script:''; ?></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_password">パスワード：</label>
                                        <input type="password" class="form-control" id="company_password" placeholder=""
                                               name="company_password" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_password_confirm">パスワード（確認）：</label>
                                        <input type="password" class="form-control" id="company_password_confirm"
                                               placeholder="" name="company_password_confirm" maxlength="20">
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="保存"/>
                            <a class="btn btn-default" href="<?php echo admin_url() . 'company/' ?>">戻る</a>
                            <!--                            <input type="reset" class="btn btn-default" value="クリアー" />-->
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
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
    </section>
</div>

<script src="<?php echo base_url(); ?>assets/js/common.js" type="text/javascript"></script>