<link rel="stylesheet"
      href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css"/>

<style type="text/css">
    .mb-3 {
        margin-bottom: 10px;
    }
</style>

<script type="text/javascript">
    const base_url = "<?php echo base_url(); ?>";

    function onClear() {
        $('#start_date').val("");
        $('#end_date').val("");
    }
    function anal_sync() {

        var message = "WixAnswerでFAQの閲覧数を取得しますか？";
        if (confirm(message)) {
            $("#loading").show();
            $.ajax({
                url: base_url + 'faq/anal_sync',
                type: 'post',
                dataType: 'json',
            }).done(function (res) {
                $("#loading").hide();
                if (res.ok == true) {
                    alert(res.cnt + '件のデータを取得しました。');
                    location.href = base_url + "faq";
                } else {
                    alert(res.result);
                }
            }).fail(function () {
                $("#loading").hide();
                alert("エラーが発生しました。");
            });
        }
    }

    function faq_sync() {

        var message = "WixAnswerでFAQを取得しますか？";
        if (confirm(message)) {
            $("#loading").show();
            $.ajax({
                url: base_url + 'faq/faq_sync',
                type: 'post',
                dataType: 'json',
            }).done(function (res) {
                $("#loading").hide();
                if (res.ok == true) {
                    alert(res.cnt + '件のFAQを取得しました。');
                    location.href = base_url + "faq";
                } else {
                    alert(res.result);
                }
            }).fail(function () {
                $("#loading").hide();
                alert("エラーが発生しました。");
            });
        }
    }

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
                    </div><!-- /.box-header -->
                    <div class="box-footer clearfix" style="padding: 0px;"></div>
                    <div class="box-body" style="margin-left: 20px;">
                        <form action="<?php echo base_url() ?>faq" method="POST" id="form1" name="form1"
                              class="form-inline">
                            <div class="row mb-3">
                                <label for="wix_url" class="col-xs-2 col-form-label">WixAnswer URL：</label>
                                <div class="col-xs-10">
                                    <?php if ($company['company_wix_domain']) { ?>
                                        <a href="https://<?php echo $company['company_wix_domain']; ?>.wixanswers.com/"
                                           target="_blank">https://<?php echo $company['company_wix_domain']; ?>
                                            .wixanswers.com/</a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="wix_api_key" class="col-xs-2 col-form-label">WixAnswer APIキー：</label>
                                <div class="col-xs-10">
                                    <?php echo $company['company_wix_key']; ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="wix_api_secret" class="col-xs-2 col-form-label">WixAnswer APIシークレット：</label>
                                <div class="col-xs-10">
                                    <?php echo $company['company_wix_secret']; ?>
                                </div>
                            </div>
                            <!--                            <div class="row mb-3">-->
                            <!--                                <label for="wix_api_secret" class="col-xs-2 col-form-label">FAQの閲覧数更新日：</label>-->
                            <!--                                <div class="col-xs-10">--><?php //echo $anal_date; ?>
                            <!--                                </div>-->
                            <!--                            </div>-->
                            <div class="row mb-3 text-red text-bold">
                                <div id="loading" style="display: none;">同期しています。
                                    <img alt="同期" src="<?php echo base_url() ?>assets/images/loading.gif">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <?php if (!empty($company['company_wix_domain'])) { ?>
                                    <input type="reset" class="btn btn-info" onclick="anal_sync();return false;"
                                           value="FAQの閲覧数を取得">
                                    <input type="reset" class="btn btn-info" onclick="faq_sync();return false;"
                                           value="WixAnswerでFAQを取得">
                                    <input type="submit" class="btn btn-primary" onclick="download();return false;"
                                           value="CSVダウンロード">
                                <?php } else { ?>
                                    <a class="btn btn-danger"
                                       href="<?php echo base_url() . 'setting'; ?>">WixAnswer設定</a>
                                <?php } ?>
                            </div>
                            <hr/>

                            <div class="row mb-3">
                                <label for="start_date" class="col-xs-2 col-form-label">閲覧日付範囲：</label>
                                <div class="col-xs-10">
                                    <input type="text" id="start_date" name="start_date" class="datepicker form-control"
                                           value="<?php echo $start_date; ?>">
                                    ~<input type="text" id= "end_date" name="end_date" class="datepicker form-control"
                                            value="<?php echo $end_date; ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-xs-12 center-block">
                                    <input type="submit" class="btn btn-primary" value="検索">
<!--                                    <input type="reset" class="btn btn-danger" onclick="onClear();return false;" value="クリア">-->
                                </div>
                            </div>
                            <div class="row mb-3">
                                <?php if ($list_cnt) {
                                    echo $list_cnt; ?>件中（<?php echo $start_page; ?>件～<?php echo $end_page; ?>件）<?php } else {
                                    echo '0件';
                                } ?>
                            </div>

                        </form>

                        <table class="table table-hover">
                            <tr>
                                <th>№</th>
                                <th>ID</th>
                                <th>タイトル</th>
                                <th>内容</th>
                                <th width="50px">公開<br/>状態</th>
                                <th width="50px">閲覧<br/>数</th>
                            </tr>
                            <?php
                            if (!empty($list)) {
                                foreach ($list as $key => $record) {
                                    ?>
                                    <tr>
                                        <td><?php echo $key + $start_page ?></td>
                                        <td><?php echo $record->id ?></td>
                                        <td><a href="<?php echo $record->url ?>"
                                               target="_blank"><?php echo $record->title ?></a></td>
                                        <td style="word-wrap: anywhere"><?php echo html_escape($record->content) ?></td>
                                        <td><?php echo ($record->status == 10) ? '公開済み' : 'ドラフト' ?></td>
                                        <td align="center"><?php echo ($record->view) ? $record->view : 0 ?></td>
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
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.ja.min.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('.datepicker').datepicker({
            language: "ja",
            autoclose: true,
            format: "yyyy-mm-dd",
            zIndexOffset: 1000,
        });
    });
</script>
