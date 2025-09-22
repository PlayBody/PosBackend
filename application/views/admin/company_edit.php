
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
                        <form method="POST" id="frmEdit" method="post" action="<?php echo base_url(); ?>admin/shift_status_save">
                            <table id="example" class="display table table-bordered" aria-describedby="example_info">
                                <tr>
                                    <th>ID</th>
                                    <td><input name="id" type="text" class="form-control" value="<?php if (!empty($status['id'])) echo $status['id']; ?>" readonly/></td>
                                    <th>企業名</th>
                                    <td><input type="text" class="form-control" /></td>
                                    <th>ドメイン</th>
                                    <td><input type="text" class="form-control" /></td>
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
        // $('#btn_save').on('click', function(e){
        //     $('#frmEdit').submit();
        // });
        // $('#btn_delete').on('click', function(e){
        //     if (confirm('削除しますか？')){
        //         $('#frmEdit').attr('action', '<?php echo base_url(); ?>admin/shift_status_delete');
        //         $('#frmEdit').submit();
        //     }
        // });
    });
</script>
