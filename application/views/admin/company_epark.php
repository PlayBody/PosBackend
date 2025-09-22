
<div class="row">
            <div class="col-md-12">
                <?php
                    $this->load->helper('form');
                    $error = $this->session->flashdata('error');
                    if($error)
                    {
                ?>
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $this->session->flashdata('error'); ?>                    
                </div>
                <?php } ?>
                <?php  
                    $success = $this->session->flashdata('success');
                    if($success)
                    {
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

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="white-box form-horizontal">
                <div class="table-responsive">
                    <div id="example_wrapper" class="dataTables_wrapper">
                        <form method="POST" id="frmEdit" method="post" action="<?php echo base_url(); ?>admin/company_epark_update">
                            <table id="example" class="display table table-bordered" aria-describedby="example_info">
                                <tr>
                                    <th>ID</th>
                                    <td><?= $company['company_id']; ?></td>
                                    <th>企業名</th>
                                    <td><?= $company['company_name']; ?></td>
                                    <th>ドメイン</th>
                                    <td><?= $company['company_domain']; ?></td>
                                </tr>
                                <tr>
                                    <th>eparkを使用する</th>
                                    <td colspan="5">
                                        <select id="is_sync_epark" name="is_sync_epark" class="form-control" style="width: 120px;">
                                            <option <?php if (empty($company['is_sync_epark']) || $company['is_sync_epark'] == 0) echo 'selected'; ?> value="0">使用なし</option>
                                            <option <?php if (!empty($company['is_sync_epark']) && $company['is_sync_epark'] == 1) echo 'selected'; ?> value="1">使用する</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>BASE_URL</th>
                                    <td colspan="5">
                                        <input id="txt_epark_base_url" name="epark_base_url" type="text" class="form-control" <?php if (empty($company['is_sync_epark']) || $company['is_sync_epark'] == 0) echo 'readonly'; ?> value="<?= $company['epark_base_url'] ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th>ログインID</th>
                                    <td colspan="5">
                                        <input id="txt_epark_login_id" name="epark_login_id" type="text" class="form-control" <?php if (empty($company['is_sync_epark']) || $company['is_sync_epark'] == 0) echo 'readonly'; ?> value="<?= $company['epark_login_id'] ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th>パスーウド</th>
                                    <td colspan="5">
                                        <input id="txt_epark_login_pwd" name="epark_login_pwd" type="text" class="form-control" <?php if (empty($company['is_sync_epark']) || $company['is_sync_epark'] == 0) echo 'readonly'; ?> value="<?= $company['epark_login_pwd'] ?>"/>
                                    </td>
                                </tr>
                            </table>
                            <input type="hidden" name="company_id" value="<?= $company['company_id'] ?>" />
                        </form>
                    </div>
                </div>
                <?php if (empty($status['is_fixable']) || $status['is_fixable'] != 1){ ?>
                    <button id="btn_save" type="button" class="btn btn-primary">保存</button>
                    <button id="btn_back" type="button" class="btn">戻る</button>
                    <?php if (!empty($status['id'])){ ?>
                        <button id="btn_delete" type="button" class="btn btn-danger">削除</button>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        $('#is_sync_epark').on('change', function(e){
            var is_sync = $(this).val();
            $('#txt_epark_base_url').attr('readonly', is_sync !=1 );
            $('#txt_epark_login_id').attr('readonly', is_sync !=1 );
            $('#txt_epark_login_pwd').attr('readonly', is_sync !=1 );
        });
        $('#btn_save').on('click', function(e){
            $('#frmEdit').submit();
        });
        $('#btn_back').on('click', function(e){
            $('#frmEdit').attr('action', '<?php echo base_url(); ?>admin/company_epark');
            $('#frmEdit').submit();
        });
        // $('#btn_delete').on('click', function(e){
        //     if (confirm('削除しますか？')){
        //         $('#frmEdit').attr('action', '<?php echo base_url(); ?>admin/shift_status_delete');
        //         $('#frmEdit').submit();
        //     }
        // });
    });
</script>
