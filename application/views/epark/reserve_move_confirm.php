
<div id="widget_reserve_move_confirm" class="widget_dialog" style="display: none;z-index: 99991; ">
    <div class="title">
        <div style="float: left;">
            予約変更
        </div>
        <div style="float: right;">
            <button class="btn_reserve_move_confirm_close"><i class="fa fa-close"></i></button>
        </div>
    </div>

    <div class="row">
        <div id="reserve_confirm_message" class="label">開始時間</div>
    </div>
    <div class="row" style="float: right; margin-bottom: 12px; margin-top: 12px;">
        <button id="reserve_move_ok_btn" class="btn_red">OK</button>
        <button id="reserve_move_cancel" class="btn_grey">キャンセル</button>
    </div>
</div>

<script>
    $('.btn_reserve_move_confirm_close').click(function (e){
        $('#widget_back').hide();
        $('.widget_dialog').hide();
        resetReserveObject();
    });
    $('#reserve_move_cancel').click(function (e){
        $('#widget_back').hide();
        $('.widget_dialog').hide();
        resetReserveObject();
    });

    $('#reserve_move_ok_btn').click(function (e) {
        $.ajax({
            url: base_url + "epark/receipt/ajaxUpdateReserveTime",
            type: 'post',
            data : {
                'staff_id' : select_reserve_staff_id,
                'reserve_time' : select_reserve_from,
                'reserve_id' : select_reserve_id,
                'time_length' : select_reserve_time_length
            },
        }).done(function(res) {
            data = JSON.parse(res);
            if(data['isSave']){
                refresh_load();
            }else{
                $('#reserve_confirm_message').html('選択した時間には予約できません。');
                $('#reserve_move_ok_btn').hide();
                $(this).css('left', ui.originalPosition.left);
                $(this).css('top', ui.originalPosition.top);
            }
        });

    });

    function resetReserveObject(){
        select_reserve_obj.css('left', select_reserve_obj_original_left);
        select_reserve_obj.css('top', select_reserve_obj_original_top);
        
        $('#reserve_move_ok_btn').show();
    }

</script>
