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

<form  id="form1" name="form1" method="post">
    <div class="breadcome-area">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="breadcome-list">
                        <div class="row condition">
                            <div class="col-md-9 form-group-obj">
                                <span class="header-title">企業名 </span>

                                <select name="cond[company_id]" class="form-control" onchange="form.submit();">
                                    <?php foreach($companies as $item){ ?>
                                        <option value="<?php echo $item['company_id']; ?>" <?php if ($cond['company_id']== $item['company_id']) echo 'selected'; ?> >
                                            <?php echo $item['company_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid" >
        <div class="row">
            <div class="col-md-12">
                <div class="white-box form-horizontal">
                    <div class="form-group">
                        <label class="col-md-2 control-label">タイプ</label>
                        <div class="col-md-10">
                            <select class="form-control" name="cond[mail_type]" onchange="form.submit();">
                                <?php foreach ($notices as $key => $txt){ ?>
                                    <option <?php if ($key==$cond['mail_type']) echo 'selected'; ?> value="<?php echo $key; ?>"><?php echo $txt; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="example-email">タイトル</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" name="data[title]" value="<?php echo empty($result['title']) ? '' : $result['title']; ?>" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="example-email">内容</label>
                        <div class="col-md-10">
                            <textarea name="data[content]" class="form-control" style="resize: vertical;min-height: 350px;"><?php echo empty($result['content']) ? '' : $result['content']; ?></textarea>
                        </div>
                    </div>
                    <center>
                        <button type="submit" class="btn btn-primary" name="mode" value="save">保存する</button>
                    </center>
                </div>
            </div>
        </div>
    </div>

</form>
