<!--<link rel="stylesheet" type="text/css" href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css">-->
<script type="text/javascript" charset="utf8"
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js"></script>

<style type="text/css">
    .tree {
        min-height: 100px;
        background-color: #fbfbfb;
        border: 1px solid #999;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
        -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05)
    }

    .tree li {
        list-style-type: none;
        margin: 0;
        padding: 10px 5px 0 5px;
        position: relative
    }

    .tree li::before, .tree li::after {
        content: '';
        left: -20px;
        position: absolute;
        right: auto
    }

    .tree li::before {
        border-left: 1px solid #999;
        bottom: 50px;
        height: 100%;
        top: 0;
        width: 1px
    }

    .tree li::after {
        border-top: 1px solid #999;
        height: 20px;
        top: 25px;
        width: 25px
    }

    .tree li span {
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border: 1px solid #999;
        border-radius: 5px;
        display: inline-block;
        padding: 5px 8px;
        text-decoration: none;
        max-width: 50%;
    }

    .tree li.parent_li > span {
        cursor: pointer
    }

    .tree > ul > li::before, .tree > ul > li::after {
        border: 0
    }

    .tree li:last-child::before {
        height: 25px
    }

    .tree li.parent_li > span:hover, .tree li.parent_li > span:hover + ul li span {
        background: #eee;
        border: 1px solid #94a0b4;
        color: #000
    }

    .end-margin {
        margin-right: 30px;
    }
</style>

<script type="text/javascript">
    const base_url = "<?php echo base_url(); ?>";
    $(function () {
        // $('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
        $('.tree li:has(ul)').addClass('parent_li').find(' > span');
        $('.tree li.parent_li > span').on('click', function (e) {
            var children = $(this).parent('li.parent_li').find(' > ul > li');
            if (children.is(":visible")) {
                children.hide('fast');
                // $(this).attr('title', 'Expand this branch').find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
            } else {
                children.show('fast');
                // $(this).attr('title', 'Collapse this branch!!!').find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
            }
            e.stopPropagation();
        });
    });

    function scenarioAdd(id) {
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/scenario/get',
            dateType: 'json',
            data: {id: id},
            success: function (data) {
                if (data) {
                    $("#edit_val").val("add");
                    $("#parent_id").val(id);
                    $("#parent_title").val(data.title);
                    $("#level").val(data.level);
                    $("#scenario_title").val("");
                    $("#scenario_content").val("");
                    $("#scenario_title").focus();
                }
            }
        });

    }

    function scenarioEdit(edit_id) {
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/scenario/get',
            dateType: 'json',
            data: {id: edit_id},
            success: function (data) {
                if (data) {
                    $("#edit_val").val("edit");
                    $("#parent_id").val(data.id);
                    $("#parent_title").val(data.parent_title);
                    $("#scenario_title").val(data.title);
                    $("#scenario_content").val(data.content);
                    $("#scenario_title").focus();
                }
            }
        });
    }

    function scenarioDelete(delete_id, title) {
        var message = "「" + title + "」を削除しますか？";
        if (confirm(message)) {
            $.ajax({
                url: base_url + 'admin/scenario/delete/' + delete_id,
                // type: 'post',
                dataType: 'json',
            });
            location.href = base_url + 'admin/scenario/';
        }
    }

    function scenarioRegist() {
        if ($("#scenario_title").val() == "") {
            alert("タイトルを入力してください。");
            return;
        }
        if ($("#scenario_content").val() == "") {
            alert("内容を入力してください。");
            return;
        }
        $("#scenarioRegistForm").submit();
    }

    function dataReset() {
        $("#scenario_title").val("");
        $("#scenario_content").val("");
        $("#edit_val").val("");
        $("#parent_id").val("");
        $("#parent_title").val("");
        $("#level").val("");
    }
</script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> シナリオ管理
            <small>作成, 編集, 削除</small>
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
                        <h3 class="box-title">シナリオ一覧</h3>
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
                    <div class="box-body table-responsive" style="margin-left: 20px;">
                        <form action="<?php echo base_url() ?>admin/scenario/add" method="POST" id="scenarioRegistForm"
                              name="scenarioRegistForm">
                            <input type="hidden" id="edit_val" name="edit_val">
                            <input type="hidden" id="parent_id" name="parent_id">
                            <input type="hidden" id="level" name="level">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="parent_title">上位シナリオ：</label>
                                    <input type="text" class="form-control" id="parent_title" name="parent_title"
                                           readonly>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="scenario_title">タイトル：</label>
                                    <input type="text" class="form-control" id="scenario_title" name="scenario_title"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="scenario_content">内容：</label>
                                    <textarea class="form-control" rows="4" id="scenario_content"
                                              name="scenario_content" autocomplete="off"></textarea>
                                </div>
                            </div>
                            <input type="submit" class="btn btn-primary" onclick="scenarioRegist();return false;"
                                   value="保存">
                            <input type="reset" class="btn btn-default" onclick="dataReset();return false;"
                                   value="リセット">
                        </form>
                    </div>
                    <div class="box-footer clearfix" style="padding: 0px;"></div>
                    <div class="box-body table-responsive">
                        <div class="tree">
                            <ul>
                                <?php
                                $level = 0;
                                for ($i = 0;
                                $i < count($list);
                                $i++) {
                                if ($list[$i]->level == 1 && $i == 0) {
                                $level = $list[$i]->level; ?>
                                <li>
                                    <span><?= $list[$i]->title ?></span>
                                    <span class="badge badge-pill primary"><?= $list[$i]->select_cnt ?></span>
                                    <a href="javascript:scenarioAdd(<?= $list[$i]->scenario_id ?>)" class="fa fa-plus"
                                       style="margin-left: 10px;">追加</a>
                                    <a href="javascript:scenarioEdit(<?= $list[$i]->scenario_id ?>)" class="fa fa-edit"
                                       style="margin-left: 10px;">編集</a>
                                    <a href="javascript:scenarioDelete(<?= $list[$i]->scenario_id ?>, '<?= html_escape($list[$i]->title) ?>')"
                                       class="fa fa-remove" style="margin-left: 10px;">削除</a>
                                    <?php if ($list[$i]->child_flag == 0) { ?>
                                </li>
                                <?php } else { ?>
                                <ul>
                                    <?php } ?>
                                    <?php
                                    }else if ($list[$i]->level == $level) { ?>
                                    <li>
                                        <span><?= $list[$i]->title ?></span>
                                        <span class="badge"><?= $list[$i]->select_cnt ?></span>
                                        <a href="javascript:scenarioAdd(<?= $list[$i]->scenario_id ?>)" class="fa fa-plus"
                                           style="margin-left: 10px;">追加</a>
                                        <a href="javascript:scenarioEdit(<?= $list[$i]->scenario_id ?>)" class="fa fa-edit"
                                           style="margin-left: 10px;">編集</a>
                                        <a href="javascript:scenarioDelete(<?= $list[$i]->scenario_id ?>, '<?= html_escape($list[$i]->title) ?>')"
                                           class="fa fa-remove" style="margin-left: 10px;">削除</a>
                                        <?php if ($list[$i]->child_flag == 0) { ?>
                                    </li>
                                    <?php } else { ?>
                                    <ul>
                                        <?php } ?>
                                        <?php
                                        }else if ($list[$i]->level > $level){
                                        $level = $list[$i]->level;
                                        ?>
                                        <li>
                                        <span><?= $list[$i]->title ?></span>
                                        <span class="badge"><?= $list[$i]->select_cnt ?></span>
                                        <a href="javascript:scenarioAdd(<?= $list[$i]->scenario_id ?>)" class="fa fa-plus"
                                           style="margin-left: 10px;">追加</a>
                                        <a href="javascript:scenarioEdit(<?= $list[$i]->scenario_id ?>)" class="fa fa-edit"
                                           style="margin-left: 10px;">編集</a>
                                        <a href="javascript:scenarioDelete(<?= $list[$i]->scenario_id ?>, '<?= html_escape($list[$i]->title) ?>')"
                                           class="fa fa-remove" style="margin-left: 10px;">削除</a>
                                        <?php if ($list[$i]->child_flag == 0) { ?>
                                            </li>
                                        <?php } else { ?>
                                        <ul>
                                            <?php } ?>
                                            <?php
                                            }else if ($list[$i]->level < $level) {
                                            $levelDiff = $level - $list[$i]->level;
                                            $level = $list[$i]->level;
                                            for ($x = 0;
                                            $x < $levelDiff;
                                            $x++){
                                            ?>
                                        </ul>
                                        </li>
                                    <?php } ?>
                                        <li>
                                            <span><?= $list[$i]->title ?></span>
                                            <span class="badge"><?= $list[$i]->select_cnt ?></span>
                                            <a href="javascript:scenarioAdd(<?= $list[$i]->scenario_id ?>)" class="fa fa-plus"
                                               style="margin-left: 10px;">追加</a>
                                            <a href="javascript:scenarioEdit(<?= $list[$i]->scenario_id ?>)" class="fa fa-edit"
                                               style="margin-left: 10px;">編集</a>
                                            <a href="javascript:scenarioDelete(<?= $list[$i]->scenario_id ?>, '<?= html_escape($list[$i]->title) ?>')"
                                               class="fa fa-remove" style="margin-left: 10px;">削除</a>
                                            <?php if ($list[$i]->child_flag == 0) { ?>
                                        </li>
                                        <?php } else { ?>
                                        <ul>
                                            <?php } ?>
                                            <?php
                                            }
                                            }
                                            ?>
                                            </li>
                                        </ul>
                        </div>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>
    </section>
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>