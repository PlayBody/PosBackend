<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> 店舗編集
        </h1>
    </section>

    <section class="content">

        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
                <!-- general form elements -->


                <div class="box box-primary">
                    <!-- form start -->

                    <form role="form" method="post" id="editUser" role="form">
                        <input type="hidden" value="save" name='mode'>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title">店舗名：</label>
                                        <input type="text" class="form-control" id="title" placeholder=""
                                               name="title" value="<?php echo $member['title']; ?>"
                                               maxlength="128">
                                        <input type="hidden" value="<?php echo $member['member_id']; ?>"
                                               name="member_id" id="member_id"/>
                                    </div>

                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_address">メールアドレス：</label>
                                        <input type="email" class="form-control" id="mail_address" placeholder=""
                                               name="mail_address" value="<?php echo $member['mail_address']; ?>"
                                               maxlength="128">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="set_time">セット時間：</label>
                                        <input type="text" class="form-control required " id="set_time" value="<?php echo $member['set_time']; ?>" name="set_time" maxlength="128">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">セット料金(円)：</label>
                                        <input type="text" class="form-control required" id="set_amount" value="<?php echo $member['set_amount']; ?>" name="set_amount" maxlength="128">
                                    </div>
                                </div>
                            </div>                     
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">パスワード：</label>
                                        <input type="password" class="form-control" id="password" placeholder=""
                                               name="password" maxlength="20">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirm">パスワード（確認）：</label>
                                        <input type="password" class="form-control" id="password_confirm"
                                               placeholder="" name="password_confirm" maxlength="20">
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="保存"/>
                            <a class="btn btn-default" href="<?php echo admin_url() . 'member/' ?>">戻る</a>
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

<script src="<?php echo base_url(); ?>assets/js/admin/member.js" type="text/javascript"></script>