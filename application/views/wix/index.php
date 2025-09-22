<!--<link rel="stylesheet" type="text/css" href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css">-->
<script type="text/javascript" charset="utf8"
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js"></script>

<style type="text/css">
    .mb-3{
        margin-bottom: 10px;
    }
</style>

<script type="text/javascript">
    const base_url = "<?php echo base_url(); ?>";

    function download() {
        if ($("#wix_api_key").val() == "") {
            alert("APIを入力してください。");
            return;
        }
        if ($("#wix_api_secret").val() == "") {
            alert("API内容を入力してください。");
            return;
        }
        $("#form1").attr('action', base_url + "faq/download");
        $("#form1").submit();
    }
</script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> FAQ一式をダウンロード
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
                        <h3 class="box-title">FAQ一覧</h3>
                        <div class="box-tools">
                            <!--                            <form action="-->
                            <?php //echo base_url() ?><!--scenario" method="POST" id="searchList">-->
                            <!--                                <div class="input-group">-->
                            <!--                                    <input type="text" name="searchText" value="-->
                            <?php //echo isset($searchText)?$searchText:''; ?><!--" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search"/>-->
                            <!--                                    <div class="input-group-btn">-->
                            <!--                                        <button class="btn btn-sm btn-default searchList"><i class="fa fa-search"></i></button>-->
                            <!--                                    </div>-->
                            <!--                                </div>-->
                            <!--                            </form>-->
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-footer clearfix" style="padding: 0px;"></div>
                    <div class="box-body" style="margin-left: 20px;">
                        <form action="<?php echo base_url() ?>faq" method="POST" id="form1" name="form1">
                            <div class="row mb-3">
                                <label for="wix_url" class="col-xs-2 col-form-label">WIX URL</label>
                                <div class="col-xs-10">
                                    <input type="text" class="form-control" id="wix_url" name="wix_url"
                                           value="<?php echo $wix_url; ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="wix_api_key" class="col-xs-2 col-form-label">WIX API KEY</label>
                                <div class="col-xs-10">
                                    <input type="text" class="form-control" id="wix_api_key" name="wix_api_key"
                                           value="<?php echo $wix_api_key; ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="wix_api_secret" class="col-xs-2 col-form-label">WIX API SECRET</label>
                                <div class="col-xs-10">
                                    <input type="text" class="form-control" id="wix_api_secret" name="wix_api_secret"
                                           value="<?php echo $wix_api_secret; ?>">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <input type="reset" class="btn btn-primary" onclick="save();return false;" value="保存">
                                <input type="submit" class="btn btn-primary" onclick="download();return false;"
                                       value="CSVダウンロード">
                            </div>
                        </form>
                    </div>
                </div><!-- /.box -->
            </div>
        </div>
    </section>
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>