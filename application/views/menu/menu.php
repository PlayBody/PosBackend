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

<form method="POST" id="searchList" method="post" action="<?php echo base_url(); ?>menu/menu">
    <div class="breadcome-area">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="breadcome-list">
                        <div class="row condition">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 form-group-obj">
                                <span class="header-title">メニュー名 </span>
                                <input name="search_word" class="form-control" value="<?php echo $search_word; ?>" />
                                <button class="btn btn-warning">検索</button>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <button class="btn btn-success">新しいメニューを追加</button>
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
                        <th>メニュー名</th>
                        <th>表示順序</th>
                    </tr>
                    <?php foreach($menus as $item){ ?>
                        <tr>
                            <td><?php echo $item['menu_id']; ?></td>
                            <td><a href="<?php echo base_url(); ?>menu/menu?id=<?php echo $item['menu_id']; ?>" ><?php echo $item['menu_title']; ?></a></td>
                            <td><?php echo $item['sort_no']; ?></td>
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
                    
                        <form method="POST" id="categoryEdit" method="post" action="<?php echo base_url(); ?>menu/menu/save">
                            <table id="example" class="display table table-bordered" aria-describedby="example_info">
                                <tr>
                                    <th>ID</th>
                                    <td colspan="3"><input name="menu_id" type="text" class="form-control" value="<?php echo $menu['menu_id']; ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <th>メニュー名</th>
                                    <td colspan="3"><input  name="menu_title" type="text" class="form-control" value="<?php echo $menu['menu_title']; ?>" /></td>
                                </tr>
                                <tr>
                                    <th>価格</th>
                                    <td><input  name="menu_price" type="text" class="form-control" value="<?php echo $menu['menu_price']; ?>" /></td>
                                    <th>経過時間</th>
                                    <td>
                                        <select class="form-control" name="menu_time">
                                            <?php for ($i=5;$i<=90; $i+=5){ ?>
                                                <option <?php if($i==$menu['menu_time']) echo 'selected'; ?> value="<?php echo $i; ?>"><?php echo $i; ?>分</option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>説明</th>
                                    <td colspan="3"><textarea   rows="10" style="resize:none;" name="menu_detail" class="form-control" ><?php echo $menu['menu_detail']; ?></textarea></td>
                                </tr>
                                <tr>
                                    <th>表示順序</th>
                                    <td colspan="3"><input name="sort_no" type="text" class="form-control" value="<?php echo $menu['sort_no']; ?>" /></td>
                                </tr>
                                <tr>
                                    <th>カテゴリー</th>
                                    <td colspan="3">
                                        <select id="sel_category" class="form-control" name="category_id" style="background-color:<?php echo $menu['color']; ?>;">
                                            <option style="background-color:white;">▼ カテゴリー</option>
                                            <?php foreach ($categories as $category){ ?>
                                                <option <?php if($category['id']==$menu['category_id']) echo 'selected'; ?> value="<?php echo $category['id']; ?>" class="form-control" style="background-color:<?php echo $category['color']; ?>;"><?php echo $category['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
                <button id="btn_save" type="button" class="btn btn-primary">保存</button>
                <?php if ($menu['menu_id']){ ?>
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
        $('#sel_category').on('change', function(e){
            $val = $(this).val();
            if ($val==''){
                $(this).css('background-color', 'white');
                return;
            }
            $color = $('#sel_category option[value="'+$val+'"]').css('background-color');
            $(this).css('background-color', $color);

        });

        $('#btn_delete').on('click', function(e){
            if (confirm('メニューを削除しますか？')){
                $('#categoryEdit').attr('action', '<?php echo base_url(); ?>menu/menu/delete');
                $('#categoryEdit').submit();
            }
        });
    });
</script>
