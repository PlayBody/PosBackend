<style>
    .div_reserve_table_row{
        display: flex;
        width: 100%;
        margin: 6px 0;
    }
    .div_reserve_table_row .th{
        width: 30%;
        background-color: #cccccc;
        margin-right: 12px;
    }
</style>

<div class="inner">
    <div style="display: table; width: 100%; height: 40px;">
        <div style="float:left;">
            <?php echo date("Y-m-d H:i", strtotime($reserve['create_date'])); ?> <b>受付</b>
        </div>
        <?php if($reserve['status']==ORDER_STATUS_TABLE_END){ ?>
        <div style="float:right;">
            <button data="<?php echo $reserve['id']; ?>" pay-method="<?php echo empty($reserve['pay_method'])?'' : $reserve['pay_method']; ?>" id="btn_complete_order" class="btn_red">完了</button>
        </div>
        <?php } ?>
    </div>
    <div style="border-bottom: solid #aaaaaa 1px; width: 100%; display: table; padding-left:8px;margin-bottom:2px;">
        <div style="float: left; border: solid #aaaaaa 1px; padding: 4px 8px; border-bottom:none;">
            <?php echo $reserve['user_name']; ?>
        </div>
    </div>
    <div style="display: table; width:100%; margin-bottom:24px;">
        <?php if($reserve['status']==ORDER_STATUS_TABLE_START || $reserve['status']==ORDER_STATUS_RESERVE_APPLY){ ?>
        <div style="float:left;">
            <button data="<?php echo $reserve['id']; ?>" id="btn_exit_order" class="btn_grey">精算</button>
        </div>
        <?php } ?>
        <div style="float:right;">
            <button data-user_id="<?php echo $reserve['id']; ?>" id="btn_delete_order"  class="btn_red">削除</button>
        </div>
    </div>
    <div class="div_reserve_table_row">
        <div class="th">日時</div>
        <div><?php echo date("Y-m-d H:i", strtotime($reserve['from_time'])) . '~'; ?></div>
    </div>

       <div>
        <div class="div_reserve_table_row">
            <div class="th">時間</div>
            <div><?php echo date("H:i", strtotime($reserve['from_time'])) . '~'; ?></div>
        </div>
        <div class="div_reserve_table_row">
            <div class="th">ブース</div>
            <div><?php echo $reserve['table_name']; ?></div>
        </div>
        <div class="div_reserve_table_row">
            <div class="th">スタッフ</div>
            <div><?php if(!empty($reserve['staff_name'])){ ?><span style="color: red">★</span><?php } ?><?php echo $reserve['staff_name']; ?></div>
        </div>
        <?php $ii=0; foreach ($menus as $menu){ $ii++; ?>
            <div class="div_reserve_table_row">
                <div class="th">コース<?php echo $ii; ?></div>
                <div><?php echo $menu['menu_title']; ?></div>
            </div>
        <?php } ?>
        <div class="div_reserve_table_row">
            <div class="th">オプション</div>
            <div></div>
        </div>
        <div class="div_reserve_table_row">
            <div class="th">こだわり</div>
            <div></div>
        </div>
        <div class="div_reserve_table_row">
            <div class="th">予約タグ</div>
            <div></div>
        </div>
        <div class="div_reserve_table_row">
            <div class="th">施術タグ</div>
            <div></div>
        </div>
        <div class="div_reserve_table_row">
            <div class="th">施術コメント</div>
            <div></div>
        </div>
    </div>
</div>

<div id="widget_reset_order" class="widget_dialog" style="display: none;">
    <div class="title">
        <div style="float: left;">
            完了しますか？
        </div>
        <div style="float: right;">
            <button class="btn_close_widget"><i class="fa fa-close"></i></button>
        </div>
    </div>
    <div class="row" style="margin-top: 24px;">
        <div class="col-md-6 label">お支払い方法を選択してください。</div>
        <div class="col-md-6">
            <select class="form-control" id="complete_pay_method">
                <option value="<?php echo PAY_METHOD_CASH; ?>">現金</option>
                <option value="<?php echo PAY_METHOD_OTHER; ?>">その他電子マネー</option>
                <option value="<?php echo PAY_METHOD_CREDIT; ?>">クレジットカード</option>
            </select>
        </div>
    </div>
    <div class="row" style="float: right; margin-bottom: 12px; margin-top: 12px;">
        <button id="btn_dlg_complete_order" data="<?php echo $reserve['id']; ?>" class="btn_grey">完了</button>
    </div>
</div>

<script>

    $('#btn_delete_order').click(function () {
        if(!confirm('削除しますか？')) return;
        order_id = $(this).attr('data-user_id');
        $.ajax({
            url: base_url + "apiorders/deleteOrder",
            type: 'post',
            data : {'order_id' : order_id},
            context: document.body
        }).done(function(res) {
            data = JSON.parse(res);
            if(data['isDelete']){
                refresh_load();
            }else{
                alert('エラーが発生しました。');
            }
        });
    });

    $('#btn_exit_order').click(function () {
        order_id = $(this).attr('data');
        $.ajax({
            url: base_url + "apiorders/updateStatus",
            type: 'post',
            data : {'order_id' : order_id, 'status' : '<?php echo ORDER_STATUS_TABLE_END; ?>'},
            context: document.body
        }).done(function(res) {
            data = JSON.parse(res);
            if(data['isUpdate']){
                refresh_load();
            }else{
                alert('完了できません。');
            }
        });
    });

    $('#btn_complete_order').click(function () {
        order_id = $(this).attr('data');
        pay_method = $(this).attr('pay-method');
        if(pay_method!='')
            reset_order(order_id, pay_method);
        else
            showDlgCompletOrder();
    });

    function showDlgCompletOrder(){
        $('#widget_back').show();
        $('#widget_reset_order').show();
    }

    $('#btn_dlg_complete_order').click(function () {
        order_id = $(this).attr('data');
        pay_method = $('#complete_pay_method').val();
        reset_order(order_id, pay_method);
    });

    function reset_order(order_id, pay_method){
        $.ajax({
            url: base_url + "apiorders/resetOrder",
            type: 'post',
            data : {'order_id' : order_id, 'pay_method':pay_method},
            context: document.body
        }).done(function(res) {
            data = JSON.parse(res);
            if(data['isUpdate']){
                refresh_load();
            }else{
                alert('完了できません。');
            }
        });
    }
</script>
