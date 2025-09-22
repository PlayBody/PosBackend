
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

<form method="POST" id="frmAdd" method="post" action="<?php echo base_url(); ?>admin/shift_status">
    <div class="breadcome-area">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="breadcome-list">
                        <div class="row condition">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <button class="btn btn-success">新しい状態を追加</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="product-status-wrap">
                <table>
                    <tbody>
                        <tr>
                            <th>ID</th>
                            <th>状態タイトル</th>
                            <th>ドメイン</th>
                            <th>EPARK使用</th>
                        </tr>
                        <?php foreach($companies as $item){ ?>
                            <tr>
                                <td><?php echo $item['company_id']; ?></td>
                                <td>
                                    <a href="#" ><?php echo $item['company_name']; ?></a>
                                </td>
                                <td><?= $item['company_domain'] ?></td>
                                <td>
                                    <?php echo (!empty($item['is_sync_epark']) && $item['is_sync_epark']==1) ? 'YES' : 'NO'; ?>&nbsp;
                                    <a href="<?php echo base_url(); ?>/admin/company_epark?id=<?php echo $item['company_id']; ?>"><i class="educate-icon educate-settings "></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        $('#btn_save').on('click', function(e){
            $('#frmEdit').submit();
        });
        $('#btn_delete').on('click', function(e){
            if (confirm('削除しますか？')){
                $('#frmEdit').attr('action', '<?php echo base_url(); ?>admin/shift_status_delete');
                $('#frmEdit').submit();
            }
        });
    });
</script>
