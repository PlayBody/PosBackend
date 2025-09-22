
<div class="container-fluid">
    <div class="row">
        <form id="frmConfig" name="frmConfig" method="POST" action="<?php echo base_url(); ?>/epark/config/update">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="sparkline10-list mg-tb-30 responsive-mg-t-0 table-mg-t-pro-n dk-res-t-pro-0 nk-ds-n-pro-t-0">
                    <div class="sparkline10-hd">
                        <div class="main-sparkline10-hd">
                            <h1>Epark接続設定</h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">API BASE URL</div>
                        <div class="col-md-9">
                            <input type="text" name="base_url" class="form-control" value="<?php echo empty($config['base_url']) ? '' : $config['base_url']; ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">auth_id</div>
                        <div class="col-md-9">
                            <input type="text" name="auth_id" class="form-control" value="<?php echo empty($config['auth_id']) ? '' : $config['auth_id']; ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">auth_pass</div>
                        <div class="col-md-9">
                            <input type="text" name="auth_pass" class="form-control" value="<?php echo empty($config['auth_pass']) ? '' : $config['auth_pass']; ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">company_id</div>
                        <div class="col-md-9">
                            <input type="text" name="epark_company_id" class="form-control" value="<?php echo empty($config['epark_company_id']) ? '' : $config['epark_company_id']; ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">同期間隔</div>
                        <div class="col-md-9">
                            <select class="form-control" name="duration">
                                <option <?php echo (empty($config['duration']) || $config['duration']==0) ? 'selected' : ''; ?> value="0">同期なし</option>
                                <option <?php echo (!empty($config['duration']) && $config['duration']==1) ? 'selected' : ''; ?> value="1">1分</option>
                                <option <?php echo (!empty($config['duration']) && $config['duration']==3) ? 'selected' : ''; ?> value="3">3分</option>
                                <option <?php echo (!empty($config['duration']) && $config['duration']==5) ? 'selected' : ''; ?> value="5">5分</option>
                                <option <?php echo (!empty($config['duration']) && $config['duration']==10) ? 'selected' : ''; ?> value="10">10分</option>
                                <option <?php echo (!empty($config['duration']) && $config['duration']==20) ? 'selected' : ''; ?> value="20">20分</option>
                                <option <?php echo (!empty($config['duration']) && $config['duration']==30) ? 'selected' : ''; ?> value="30">30分</option>
                                <option <?php echo (!empty($config['duration']) && $config['duration']==60) ? 'selected' : ''; ?> value="60">60分</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <button type="submit" class="btn btn-primary">保管する</button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="config_id" value="<?php echo empty($config['id']) ? '' : $config['id']; ?>" />
        </form>
    </div>
    <div class="row">
        <form id="frmConfigTable" name="frmConfigTable" method="POST" action="<?php echo base_url(); ?>/epark/config/updateTable">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="sparkline10-list mg-tb-30 responsive-mg-t-0 table-mg-t-pro-n dk-res-t-pro-0 nk-ds-n-pro-t-0">
                    <div class="sparkline10-hd">
                        <div class="main-sparkline10-hd">
                            <h1>同期データ設定</h1>
                        </div>
                    </div>
                    <?php foreach ($tables as $table){ ?>
                        <div class="row">
                            <input type="hidden" name="table[<?php echo $table['id']; ?>][id]" value="<?php echo $table['id']; ?>" />
                            <div class="col-md-2">API BASE URL</div>
                            <div class="col-md-2"><?php echo $table['table_alias']; ?></div>
                            <div class="col-md-2"><?php echo $table['table_name']; ?></div>
                            <div class="col-md-2">
                                <select name="table[<?php echo $table['id']; ?>][is_real_update]" class="form-control">
                                    <option <?php echo $table['is_real_update']==0 ? 'selected' : ''; ?> value="0">更新しない</option>
                                    <option <?php echo $table['is_real_update']==1 ? 'selected' : ''; ?> value="1">更新する</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="table[<?php echo $table['id']; ?>][from_type]" class="form-control">
                                    <option <?php echo $table['is_real_update']==0 ? 'selected' : ''; ?> value="0">すべて</option>
                                    <option <?php echo $table['from_type']==1 ? 'selected' : ''; ?> value="1">開始日の指定</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input class="form-control" type="text" name="table[<?php echo $table['id']; ?>][from_date]" value="<?php echo empty($table['from_date']) ? '' :$table['from_date']; ?>" />
                            </div>
                        </div>
                    <?php } ?>

                    <div class="row">
                        <button type="submit" class="btn btn-primary">保管する</button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="config_id" value="<?php echo empty($config['id']) ? '' : $config['id']; ?>" />
        </form>
    </div>
</div>

<script>
    $('#btn_shift').onclick(function(){
        if (confirm('初期化いますか。')){

            loadMask();
            $.ajax({
                url: base_url + "epark/initsync/shift",
                type: 'post',
                data: {
                    'select_date' : $('#select_date').val(),
                },
                context: document.body
            }).done(function(res) {

                console.log(res);
                hideMask();
            });


        }
    })
</script>