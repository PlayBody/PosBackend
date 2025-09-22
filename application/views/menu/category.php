
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

<form method="POST" id="searchList" method="post" action="<?php echo base_url(); ?>menu/category">
    <div class="breadcome-area">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="breadcome-list">
                        <div class="row condition">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 form-group-obj">
                                <span class="header-title">カテゴリ名 </span>
                                <input name="search_word" class="form-control" value="<?php echo $search_word; ?>" />
                                <button class="btn btn-primary">検索</button>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <button class="btn btn-success">新しいカテゴリを追加</button>
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
                            <th>カテゴリ名</th>
                            <th>表示順序</th>
                        </tr>
                        <?php foreach($categories as $item){ ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><a href="<?php echo base_url(); ?>/menu/category?id=<?php echo $item['id']; ?>" ><?php echo $item['name']; ?></a></td>
                                <td><?php echo $item['order_no']; ?></td>
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
                    
                        <form method="POST" id="categoryEdit" method="post" action="<?php echo base_url(); ?>menu/category/save">
                            <table id="example" class="display table table-bordered" aria-describedby="example_info">
                                <tr>
                                    <th>ID</th>
                                    <td><input name="id" type="text" class="form-control" value="<?php echo $category['id']; ?>" readonly/></td>
                                    <th>管理コード</th>
                                    <td><input name="code" type="text" class="form-control"  value="<?php echo $category['code']; ?>"/></td>
                                </tr>
                                <tr>
                                    <th>カテゴリ名</th>
                                    <td><input  name="name" type="text" class="form-control" value="<?php echo $category['name']; ?>"/></td>
                                    <th>略名</th>
                                    <td><input  name="alias" type="text" class="form-control" value="<?php echo $category['alias']; ?>"/></td>
                                </tr>
                                <tr>
                                    <th>説明</th>
                                    <td colspan="3"><textarea name="description" class="form-control" ><?php echo $category['description']; ?></textarea></td>
                                </tr>
                                <tr>
                                    <th>表示順序</th>
                                    <td colspan="3"><input name="order_no" type="text" class="form-control" value="<?php echo $category['order_no']; ?>"/></td>
                                </tr>
                                <tr>
                                    <th>ボタン色</th>
                                    <td colspan="3">
                                        <div class="col-md-4">
                                            <input style="padding:0; height:34px;" type="color" name=color class="form-control" id="exampleColorInput" value="<?php echo $category['color']; ?>" title="Choose your color">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
                <button id="btn_save" type="button" class="btn btn-primary">保存</button>
                <?php if ($category['id']){ ?>
                    <button id="btn_delete" type="button" class="btn btn-danger">削除</button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        $('#btn_save').on('click', function(e){
            $('#categoryEdit').submit();
        });
        $('#btn_delete').on('click', function(e){
            if (confirm('カテゴリを削除しますか？')){
                $('#categoryEdit').attr('action', '<?php echo base_url(); ?>menu/category/delete');
                $('#categoryEdit').submit();
            }
        });
    });
</script>
