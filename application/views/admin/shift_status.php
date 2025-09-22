
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
        <div class="col-md-6">
            <div class="product-status-wrap">
                <table>
                    <tbody>
                        <tr>
                            <th>ID</th>
                            <th>状態タイトル</th>
                            <th>背景色</th>
                        </tr>
                        <?php foreach($statuses as $item){ ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td>
                                    <?php if (!empty($item['is_fixable']) && $item['is_fixable'] ==1) { ?>
                                        <?php echo $item['title']; ?>
                                    <?php }else{ ?>
                                        <a href="<?php echo base_url(); ?>/admin/shift_status?id=<?php echo $item['id']; ?>" ><?php echo $item['title']; ?></a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <div style="border:solid #3333 1px;height:24px; <?php if (!empty($item['color'])) echo 'background-color:' . $item['color'] . ';' ?>"></div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="white-box form-horizontal">
                <div class="table-responsive">
                    <div id="example_wrapper" class="dataTables_wrapper">
                        <form method="POST" id="frmEdit" method="post" action="<?php echo base_url(); ?>admin/shift_status_save">
                            <table id="example" class="display table table-bordered" aria-describedby="example_info">
                                <tr>
                                    <th>ID</th>
                                    <td><input name="id" type="text" class="form-control" value="<?php if (!empty($status['id'])) echo $status['id']; ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <th>タイトル</th>
                                    <td><input  name="title" type="text" class="form-control" value="<?php if (!empty($status['title'])) echo $status['title']; ?>"/></td>
                                </tr>
                                <tr>
                                    <th>背景色</th>
                                    <td>
                                        <input name=color style="padding:0; height:34px;" type="color"  class="form-control" value="<?php echo empty($status['color']) ? '' : $status['color']; ?>" title="Choose your color">
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
                <?php if (empty($status['is_fixable']) || $status['is_fixable'] != 1){ ?>
                    <button id="btn_save" type="button" class="btn btn-primary">保存</button>
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
