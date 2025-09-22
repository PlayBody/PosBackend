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
    .shift_info_time_btn{
        cursor: pointer;
        list-style: none;
        margin-right: 4px;
        margin-bottom: 12px;
        padding: 8px;
        border: solid #d5b790 1px;
        border-radius: 3px;
    }

    .shift_info_time_btn.active{
        border: solid #eb0909 1px;
        background-color: #fff6a0;
    }
</style>

<div class="inner" style="color: black;">
    <div class="comment_content" style="display: table; width: 100%;">
        <?php echo date("Y-m-d H:i", strtotime($shift['create_date'])); ?> <b>登録</b>
    </div>
    <div class="comment_label">スタッフ名:</div>
    <div class="comment_content"><?php echo $shift['staff_name']; ?></div>
    <div class="comment_label">開始時間:</div>
    <div class="comment_content">
        <?php echo date('Y-m-d', strtotime($shift['from_time'])); ?>&nbsp;
        <select id="shift_edit_from_hour">
            <?php for($i=$organ_from_hour; $i<=$organ_to_hour; $i++){ ?>
                <option <?php if(date('H', strtotime($shift['from_time']))==($i<10 ? '0'.$i : $i)) echo 'selected'; ?>><?php echo $i<10 ? '0'.$i : $i; ?></option>
            <?php } ?>
        </select>:
        <select  id="shift_edit_from_min">
            <?php for($i=0; $i<60; $i+=5){ ?>
                <option <?php if(date('i', strtotime($shift['from_time']))==($i<10 ? '0'.$i : $i)) echo 'selected'; ?>><?php echo $i<10 ? '0'.$i : $i; ?></option>
            <?php } ?>
        </select>:00
    </div>
    <div class="comment_label">業務時間:</div>
    <?php
        $from = date_create($shift['from_time']);
        $to = date_create($shift['to_time']);
        $interval = date_diff($from, $to);
        $diff = $interval->h*60 + $interval->i;
    ?>
    <div class="comment_content">
        <ul style="display: flex; flex-wrap: wrap;">
            <?php for($i=5; $i<=60; $i+=5){ ?>
            <li id="time_btn_<?php echo $i; ?>" class="<?php if($diff==$i) echo 'active'; ?> shift_info_time_btn"><?php echo $i; ?></li>
            <?php } ?>
            <li id="time_btn_90" class="<?php if($diff==90) echo 'active'; ?> shift_info_time_btn">90</li>
            <li id="time_btn_120" class="<?php if($diff==120) echo 'active'; ?> shift_info_time_btn">120</li>
        </ul>
        <div style="display: flex; align-items: center;">
            <input id="edit_shift_length" type="text" class="form-control" value="<?php echo $diff; ?>" />
            <div style="width: 30%; padding-left: 12px;">分</div>
        </div>
        <p style="font-size: 14px;">※手入力の場合は、5分単位で四捨五入されます</p>
    </div>
    <div class="comment_label">シフト状態:</div>
    <div class="comment_content">
        <select class="form-control" id="edit_shift_type">
            <option <?php if ($shift['shift_type']==SHIFT_STATUS_SUBMIT) echo 'selected'; ?> value="<?php echo SHIFT_STATUS_SUBMIT; ?>">申請中</option>
            <option <?php if ($shift['shift_type']==SHIFT_STATUS_REJECT) echo 'selected'; ?> value="<?php echo SHIFT_STATUS_REJECT; ?>">拒否</option>
            <option <?php if ($shift['shift_type']==SHIFT_STATUS_OUT) echo 'selected'; ?> value="<?php echo SHIFT_STATUS_OUT; ?>">店外待機</option>
            <option <?php if ($shift['shift_type']==SHIFT_STATUS_REST) echo 'selected'; ?> value="<?php echo SHIFT_STATUS_REST; ?>">休み</option>
            <option <?php if ($shift['shift_type']==SHIFT_STATUS_REQUEST) echo 'selected'; ?> value="<?php echo SHIFT_STATUS_REQUEST; ?>">出勤依頼</option>
            <option <?php if ($shift['shift_type']==SHIFT_STATUS_ME_REPLY) echo 'selected'; ?> value="<?php echo SHIFT_STATUS_ME_REPLY; ?>">回答済み</option>
            <option <?php if ($shift['shift_type']==SHIFT_STATUS_APPLY) echo 'selected'; ?> value="<?php echo SHIFT_STATUS_APPLY; ?>">承認</option>
        </select>
    </div>
    <div class="comment_content" style="display: table; width: 100%;">
        <button data="<?php echo $shift['shift_id']; ?>" id="shift_delete_btn" class="btn_grey" style="float: left;">削除</button>
        <button data="<?php echo $shift['shift_id']; ?>" id="shift_update_btn" class="btn_red" style="float: right;">登録</button>
    </div>
</div>
<script>
    staff_id = "<?php echo $shift['staff_id']; ?>";
    from_time = "<?php echo $shift['from_time']; ?>";
    $('.shift_info_time_btn').click(function (e){
       time = $(this).html();
        $('.shift_info_time_btn').removeClass('active');
        $('#time_btn_'+time).addClass('active');

        $('#edit_shift_length').val(time);
    });
    $('#shift_delete_btn').click(function (e){
        shift_id = $(this).attr('data');
        if (shift_id==null || shift_id=='') return;
        if(confirm('選択したシフトを削除しますか？')){
            $.ajax({
                url: base_url + "epark/receipt/ajaxDeleteShift",
                type: 'post',
                data : {'shift_id' : shift_id},
            }).done(function(res) {
                if(res){
                    refresh_load();
                }
            });
        }
    });


    $('#shift_update_btn').click(function (e){
        from_time = "<?php echo date('Y-m-d', strtotime($shift['from_time'])); ?>";
        from_time += ' ' + $('#shift_edit_from_hour').val() + ':' + $('#shift_edit_from_min').val()+':00';
        shift_id = $(this).attr('data');
        if (shift_id==null || shift_id=='') return;
        time_length = $('#edit_shift_length').val();

        time_length = parseInt(time_length/5)*5;
           _from = new Date(from_time);

           _to =  new Date(_from.getTime() + time_length*60000);
           year = _to.getFullYear();
           month = _to.getMonth()+1;
           date = _to.getDate();
            to_time = year+'-'+(month<10?'0'+month:month)+'-'+(date<10?'0'+date:date);
            to_time += ' ' + (_to.getHours()<10?'0'+ _to.getHours(): _to.getHours());
            to_time += ':' + (_to.getMinutes()<10?'0'+ _to.getMinutes(): _to.getMinutes());
            to_time += ':00';

            $.ajax({
                url: base_url + "apishifts/saveShift",
                type: 'post',
                data : {
                    'shift_id' : shift_id,
                    'organ_id' : '<?php echo $shift['organ_id']; ?>',
                    'staff_id' : staff_id,
                    'from_time' : from_time,
                    'to_time' : to_time,
                    'shift_type' : $('#edit_shift_type').val(),
                },
            }).done(function(res) {
                data = JSON.parse(res);
                if(data['isSave']){
                    refresh_load();
                }else{
                    alert(data['message']);
                }
            });
    });
    function addDays(date, days) {

        return result;
    }
</script>
