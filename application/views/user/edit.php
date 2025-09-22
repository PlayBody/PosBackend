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


<form id="form1" name="form1" method="post">
    <input type="hidden" id="mode" name="mode" value="edit" />
    <div class="col-md-12">
        <div class="white-box form-horizontal">
            <div class="form-group">
                <label class="col-md-2 control-label">ユーザー名</label>
                <div class="col-md-10" style="display: flex;">
                    <input class="form-control" style="width: 120px;" type="text" name="user[user_first_name]" value="<?php echo $user['user_first_name']; ?>" required />
                    &nbsp;
                    <input class="form-control" style="width: 120px;" type="text" name="user[user_last_name]" value="<?php echo $user['user_last_name']; ?>" required />
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="example-email">ニックネーム</label>
                <div class="col-md-10">
                    <input class="form-control" type="text" name="user[user_nick]" value="<?php echo $user['user_nick']; ?>" required />
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="example-email">メールアドレス</label>
                <div class="col-md-10">
                    <input class="form-control" type="text" name="user[user_email]" value="<?php echo $user['user_email']; ?>" required />
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="example-email">電話番号</label>
                <div class="col-md-10">
                    <input class="form-control" type="text" name="user[user_tel]" value="<?php echo $user['user_tel']; ?>" required />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">性別</label>
                <div class="col-sm-10">
                    <select name="user[user_sex]" class="form-control" style="width: 120px;">
                        <option <?php if ($user['user_sex'] == 1) echo 'selected'; ?> value="1">男</option>
                        <option <?php if ($user['user_sex'] == 2) echo 'selected'; ?> value="2">女</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">パスウード</label>
                <div class="col-sm-10">
                    <input class="form-control" type="password" name="user[user_password]" value="" />
                </div>
            </div>
            <div style="padding:20px; text-align: center;">
                <input class="btn btn-primary" type="submit" value="保存する" />
                <input class="btn btn-danger" type="button" value="このユーザーを削除" onclick="
                                if (confirm('Is Delete')){
                                    $('#mode').val('delete'); 
                                    setTimeout(form.submit(), 100);
                                }

                            " />
                <input class="btn btn-default" type="button" value="戻る" onclick="location.href='<?php echo base_url(); ?>/user/index'" />
            </div>
        </div>
    </div>
    </div>
</form>

